<?php

namespace App;

use App\Exceptions\NotValidatedException;
use Illuminate\Database\Eloquent\Model;
use Validator;

/**
 *  클래스명:               Timetable
 *  설명:                   시간표 테이블에 대한 모델 속성을 정의
 *  만든이:                 3-WDJ 春目指し 1401213 이승민
 *  만든날:                 2018년 4월 26일
 */
class Timetable extends Model
{
    // 01. 모델 속성 설정
    protected   $table = 'timetables';
    protected   $fillable = [
        'subject_id', 'day_of_week', 'period', 'classroom'
    ];

    public      $timestamps = false;



    // 02. 테이블 관계도 설정
    /**
     *  함수명:                         subject
     *  함수 설명:                      시간표 테이블의 강의 테이블에 대한 1:* 역관계를 정의
     *  만든날:                         2018년 4월 26일
     */
    public function subject() {
        return $this->belongsTo('App\Subject', 'subject_id', 'id');
    }



    // 03. 스코프 정의
    public function scopeDayOfWeek($query, $dayOfWeek) {
        return $query->where('day_of_week', $dayOfWeek);
    }


    // 04. 클래스 메서드 정의
    public static function insert(Array $dataList, $term) {
        // 01. 데이터 유효성 검사
        $validator = Validator::make(['timetable' => $dataList, 'term' => $term], [
            'term'                      => ['required', 'regex:/(19|20)\d{2}-((1st|2nd)_term|(summer|winter)_vacation)/'],
            'timetable.*'               => 'required|array',
            'timetable.*.subject_id'    => 'required|exists:subjects,id',
            'timetable.*.day_of_week'   => 'required|numeric|min:1|max:5',
            'timetable.*.period'        => 'required|numeric|min:1|max:9',
            'timetable.*.classroom'     => 'required|string|min:2'
        ]);

        if($validator->fails()) {
            throw new NotValidatedException($validator->errors());
        }

        // 데이터 유일성 검증
        $termValue  = explode('-', $term);
        $year       = $termValue[0];
        $term       = $termValue[1];

        // 동일 지도반의 과목인지 여부를 검증
        $joinClassTest      = array();
        foreach($dataList as $data) {
            $subject = Subject::findOrFail($data['subject_id']);
            if(!($subject->year == $year && $subject->term == $term)) {
                throw new NotValidatedException(__('response.timetable_wrong_subject', ['subject_id' => $subject->id]));
            }

            $joinClassTest[] = $subject->join_class;
        }
        $joinClassTest = array_unique($joinClassTest);

        if(sizeof($joinClassTest) > 1) {
            throw new NotValidatedException(__('response.timetable_wrong_class'));
        }

        // 해당 학기의 시간표 삭제
        $subjectsList = Subject::where(['year' => $year, 'term' => $term])->get()->all();
        foreach($subjectsList as $subject) {
            $subject->timetables()->delete();
        }

        // 데이터 저장
        foreach($dataList as $data) {
            $timetable = new self();

            $timetable->subject_id  = $data['subject_id'];
            $timetable->day_of_week = $data['day_of_week'];
            $timetable->period      = $data['period'];
            $timetable->classroom   = $data['classroom'];

            if(!$timetable->save()) {
                return false;
            }
        }

        return true;
    }


    // 05. 멤버 메서드 정의
}