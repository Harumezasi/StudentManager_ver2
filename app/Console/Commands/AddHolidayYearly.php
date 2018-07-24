<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use GuzzleHttp\Client;
use App\Schedule;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Carbon;

/**
 *  클래스명:               AddHolidayYearly
 *  설명:                   매년 연휴를 조회하여 데이터베이스에 추가하는 기능을 정의한 클래스
 *  만든이:                 3-WDJ 春目指し 1401213 이승민
 *  만든날:                 2018년 6월 09일　
 */
/**
 *  クラス名：              AddHolidayYearly
 *  説明:                   毎年の祝日を照会してデータベースに登録するタスクを定義するクラス
 *  作った人：              3-WDJ 春目指し 1401213 イ・スンミン
 *  作った日：              2018年6月09日　
 */
class AddHolidayYearly extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'holiday:add';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'add national holiday data for this year to database.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //
        // 01. HTTP Request 클라이언트 설정
        // 01．HTTP Request クライエントの設定
        $client = new Client(
            ['base_uri' => 'http://apis.data.go.kr/B090041/openapi/service/SpcdeInfoService/']
        );

        // 02. 휴일 데이터를 데이터베이스에 등록
        // 02. 祝日のデータをデータベースに登録
        for($iCount = 1; $iCount <= 12; $iCount++) {
            try {
                // 02-01. 요청 데이터 설정
                // 02-01. リクエストのデータを設定
                $month = sprintf("%02d", $iCount);

                $reqUrl = 'getRestDeInfo';
                $reqUrl .= '?' . 'solYear=' . today()->year;
                $reqUrl .= '&' . 'solMonth=' . $month;
                $reqUrl .= '&' . 'ServiceKey=' . config()->get('app.holiday_data_key');

                $req = $client->request('GET', $reqUrl);

                $body = $req->getBody();

                // 02-02. XML 파일 파싱
                // 02-02. XML パイルを構文分析
                $xmlObj = simplexml_load_string($body);

                // 02-03. 일정을 데이터베이스에 등록
                // 02-03. 日程をデータベースに登録

                // 정보의 성공적인 수신 여부를 확인
                // 情報を成功的に受信したかどうかを確認
                if(isset($xmlObj->body->items->item)) {
                    // 일정 객체를 추출
                    // 日程のオブジェクトを得る
                    $items = $xmlObj->body->items->item;

                    foreach ($items as $item) {
                        $date = Carbon::Parse($item->locdate)->format('Y-m-d');
                        $setData = [
                            'start_date' => $date,
                            'end_date' => $date,
                            'name' => $item->dateName,
                            'type' => Schedule::TYPE['holidays'],
                            'holiday_flag' => TRUE,
                            'include_flag' => TRUE,
                            'contents' => ''
                        ];

                        if(Schedule::insert($setData)) {
                            // 데이터 저장 성공
                            //　データのセーブを成功した場合
                            echo "{$item->dateName} is added.\n";
                            continue;
                        } else {
                            // 데이터 저장 실패
                            //　データのセーブを失敗した場合
                            $this->warn('adding data is failed!');
                            return false;
                        }
                    }
                } else {
                    // 정보를 받지 못했을 때 -> 관리자에게 알림
                    //　情報が得られなかった場合　→　管理者に知らせる

                }
            } catch (GuzzleException $e) {
                // 요청 도중 예외 발생
                // リクエストしている途中、例外が発生した場合
            }
        }

        $this->info('adding holiday data is completed.');
        return true;
    }
}
