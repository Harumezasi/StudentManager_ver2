<?php

namespace App;

use App\Exceptions\NotValidatedException;
use Validator;
use Awobaz\Compoships\Compoships;
use Illuminate\Database\Eloquent\Model;

/**
 *  클래스명:               Term
 *  설명:                   학기 테이블에 대한 모델 속성을 정의
 *  만든이:                 3-WDJ 春目指し 1401213 이승민
 *  만든날:                 2018년 5월 27일
 */
class Term extends Model
{
    use Compoships;

    // 01. 모델 속성 설정
    protected   $table = 'terms';
    protected   $primaryKey = ['year', 'term'];
    protected   $fillable = [
        'year', 'term', 'start', 'end'
    ];

    public      $timestamps     = false;
    public      $incrementing   = false;

    // 02. 관계도 정의
    /**
     *  함수명:                         comments
     *  함수 설명:                      학기 테이블의 코멘트 테이블에 대한 1:* 소유관계를 정의
     *  만든날:                         2018년 5월 27일
     */
    public function comments() {
        return $this->hasMany('App\Comment',  ['year', 'term'], ['year', 'term']);
    }

    /**
     *  함수명:                         subjects
     *  함수 설명:                      학기 테이블의 강의 테이블에 대한 1:* 소유관계를 정의
     *  만든날:                         2018년 5월 28일
     */
    public function subjects() {
        return $this->hasMany('App\Subject', ['year', 'term'], ['year', 'term']);
    }



    // 03. 스코프 정의



    // 04. 클래스 메서드 정의
    /**
     *  함수명:                         thisTerm
     *  함수 설명:                      이번 학기를 조회
     *  만든날:                         2018년 5월 28일
     *
     *  매개변수 목록
     *  null
     *
     *  반환값
     *  @return \Illuminate\Database\Query\Builder|static
     */
    public static function thisTerm() {
        $date = today()->format('Y-m-d');
        $model = self::where([
            ['start', '<=', $date],
            ['end', '>=', $date]
        ])->get()->all();

        if(sizeof($model) <= 0) {
            return null;
        } else {
            return $model[0];
        }
    }


    public static function termsIncludePeriod($startDate, $endDate) {
        // 01. 매개인자 유효성 검사
        $validator = Validator::make(['start' => $startDate, 'end' => $endDate], [
                'start'     => 'required|date',
                'end'       => 'required|date|after:start'
        ]);

        if($validator->fails()) {
            throw new NotValidatedException($validator->errors());
        }

        // 02. 기간 설정
        $recentStart    = Term::where('start', '<=', $startDate)->orderBy('start', 'desc')->first()->start;
        $recentEnd      = Term::where('end', '>=', $endDate)->orderBy('end')->first()->end;

        return Term::where([
            ['start', '>=', $recentStart],
            ['end', '<=', $recentEnd],
        ])->orderBy('start');
    }



    // 05. 멤버 메서드 정의
}
