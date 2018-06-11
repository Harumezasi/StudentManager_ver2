<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use GuzzleHttp\Client;
use App\Schedule;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Carbon;

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
        $client = new Client(['base_uri' => 'http://apis.data.go.kr/B090041/openapi/service/SpcdeInfoService/']);

        // 02. 휴일 데이터를 데이터베이스에 등록
        for($iCount = 1; $iCount <= 12; $iCount++) {
            try {
                // 02-01. 요청 데이터 설정
                $month = sprintf("%02d", $iCount);

                $reqUrl = 'getRestDeInfo';
                $reqUrl .= '?' . 'solYear=' . today()->year;
                $reqUrl .= '&' . 'solMonth=' . $month;
                $reqUrl .= '&' . 'ServiceKey=' . config()->get('app.holiday_data_key');

                $req = $client->request('GET', $reqUrl);

                $body = $req->getBody();

                // 02-02. XML 파일 파싱
                $xmlObj = simplexml_load_string($body);

                // 02-03. 일정을 데이터베이스에 등록
                // 정보의 성공적인 수신 여부를 확인
                if(isset($xmlObj->body->items->item)) {
                    $items = $xmlObj->body->items->item;        // 일정 객체를 추출
                    foreach ($items as $item) {
                        $date = Carbon::Parse($item->locdate)->format('Y-m-d');
                        $setData = [
                            'start_date' => $date,
                            'end_date' => $date,
                            'name' => $item->dateName,
                            'type' => Schedule::TYPE['holidays'],
                            'holiday_flag' => TRUE,
                            'contents' => ''
                        ];

                        if(Schedule::insert($setData)) {
                            // 데이터 저장 성공
                            echo "{$item->dateName} is added.\n";
                            continue;
                        } else {
                            // 데이터 저장 실패
                            $this->warn('adding data is failed!');
                            return false;
                        }
                    }
                } else {
                    // 정보를 받지 못했을 때 -> 관리자에게 알림

                }
            } catch (GuzzleException $e) {
                // 요청 도중 예외 발생
            }
        }

        $this->info('adding holiday data is completed.');
        return true;
    }
}
