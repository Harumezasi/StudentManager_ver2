<?php

namespace App;

use App\Exceptions\NotValidatedException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Carbon;

/**
 *  클래스명:               Student
 *  설명:                   학생 테이블에 대한 모델 속성을 정의
 *  만든이:                 3-WDJ 春目指し 1401213 이승민
 *  만든날:                 2018년 4월 26일
 */
class Student extends Model
{
    // 01. 모델 속성 설정
    protected   $table = 'students';
    protected   $keyType = 'string';
    protected   $fillable = [
        'id', 'study_class',
        'attention_level', 'attention_reason', 'stop_flag'
    ];

    public      $timestamps = false;
    public      $incrementing = false;



    // 02. 테이블 관계도 설정
    /**
     *  함수명:                         attendances
     *  함수 설명:                      학생 테이블의 출결 테이블에 대한 1:* 소유 관계를 정의
     *  만든날:                         2018년 4월 26일
     */
    public function attendances() {
        return $this->hasMany('App\Attendance', 'std_id', 'id');
    }

    /**
     *  함수명:                         user
     *  함수 설명:                      학생 테이블의 사용자 테이블에 대한 1:1 역관계를 정의
     *  만든날:                         2018년 4월 26일
     */
    public function user() {
        return $this->belongsTo('App\User', 'id', 'id');
    }

    /**
     *  함수명:                         studyClass
     *  함수 설명:                      학생 테이블의 반 테이블에 대한 1:* 역관계를 정의
     *  만든날:                         2018년 4월 26일
     */
    public function studyClass() {
        return $this->belongsTo('App\StudyClass', 'study_class', 'id');
    }

    /**
     *  함수명:                         comments
     *  함수 설명:                      학생 테이블의 코멘트 테이블에 대한 1:* 소유 관계를 정의
     *  만든날:                         2018년 4월 26일
     */
    public function comments() {
        return $this->hasMany('App\Comment', 'std_id', 'id');
    }

    /**
     *  함수명:                         joinLists
     *  함수 설명:                      학생 테이블의 수강목록 테이블에 대한 1:* 소유 관계를 정의
     *  만든날:                         2018년 4월 26일
     */
    public function joinLists() {
        return $this->hasMany('App\JoinList', 'std_id', 'id');
    }

    /**
     *  함수명:                         gainedScores
     *  함수 설명:                      학생 테이블의 취득성적 테이블에 대한 1:* 소유 관계를 정의
     *  만든날:                         2018년 4월 26일
     */
    public function gainedScores() {
        return $this->hasMany('App\GainedScore', 'std_id', 'id');
    }

    /**
     *  함수명:                         subjects
     *  함수 설명:                      학생 테이블의 강의 테이블에 대한 1:* 소유 관계를 정의
     *  만든날:                         2018년 5월 26일
     */
    public function subjects() {
        return $this->hasManyThrough(
            'App\Subject',
            'App\JoinList',
            'std_id',
            'id',
            'id',
            'subject_id'
        );
    }



    // 03. 스코프 정의
    public function scopeStop($query, $flag) {
        return $query->where('stop_flag', $flag);
    }

    public function scopeAttention($query) {
        return $query->where('attention_level', '>', 0);
    }

    // 04. 클래스 메서드 정의

    // 05. 멤버 메서드 정의
    /**
     *  함수명:                         selectSubjectsList
     *  함수 설명:                      해당 학생이 수강하고 있는 교과목의 상세정보 목록을 조회
     *  만든날:                         2018년 4월 29일
     *
     *  매개변수 목록
     *  @param $when :                  조회기간을 지정 (연도-학기)
     *
     *  지역변수 목록
     *  $period(array):                 지정된 조회기간
     *
     *  반환값
     *  @return                          $this
     */
    public function selectSubjectsList($when) {
        $period = explode('-', $when);

        $subjects = $this->joinLists()
            ->join('subjects', function($join) use($period) {
                $join->on('subjects.id', 'join_lists.subject_id')
                    ->where([['subjects.year', $period[0]], ['subjects.term', $period[1]]]);
            })->join('users', function($join) {
                $join->on('users.id', 'subjects.professor');
            })->select([
                'subjects.id', 'subjects.name', 'users.name as prof_name', 'users.photo',
            ])->get()->all();

        foreach($subjects as $data) {
            // 사용자 사진이 등록되어 있다면
            if (Storage::disk('prof_photo')->exists($data->photo)) {
                $data->photo = Storage::url('source/prof_face/') . $data->photo;
            } else {
                $data->photo = Storage::url('source/prof_face/').'default.png';
            }
        }

        return $subjects;
    }

    // 해당 과목이 사용자가 수강하는 강의인지 확인
    public function isMySubject($subjectId) {
        $subjects = $this->joinLists()->where("subject_id", $subjectId)->get()->all();

        if(sizeof($subjects) > 0) {
            return Subject::findOrFail($subjectId);
        } else {
            throw new NotValidatedException(__('response.no_authority', ['contents' => __('study.subject')]));
        }
    }

    /**
     *  함수명:                         selectScoresList
     *  함수 설명:                      해당 학생이 해당 과목에서 취득한 성적 목록을 출력
     *  만든날:                         2018년 4월 29일
     *
     *  매개변수 목록
     *  @param $subjectId:              강의 코드
     *
     *  지역변수 목록
     *  $period(array):                 지정된 조회기간
     *
     *  반환값
     *  @return                          $this
     */
    public function selectScoresList($subjectId = null) {
        $final = __('study.final');
        $midterm = __('study.midterm');
        $homework = __('study.homework');
        $quiz = __('study.quiz');

        return $this->gainedScores()
            ->rightJoin('scores', function($join) use ($subjectId){
                $join->on('gained_scores.score_type', 'scores.id')->where('subject_id', $subjectId);
            })->select([
                'scores.execute_date', 'scores.detail',
                'scores.perfect_score', 'gained_scores.score AS gained_score',
                DB::raw("(CASE scores.type WHEN 'final' THEN '{$final}' WHEN 'midterm' THEN '{$midterm}'
                    WHEN 'homework' THEN '{$homework}' WHEN 'quiz' THEN '{$quiz}' END) AS type")
            ])->orderBy('scores.execute_date', 'desc');
    }

    /**
     *  함수명:                         selectStatsOfType
     *  함수 설명:                      해당 학생이 해당 과목에서 성적 유형별로 취득한 성적을 조회
     *  만든날:                         2018년 4월 29일
     *
     *  매개변수 목록
     *  @param $subjectId:              강의 코드
     *
     *  지역변수 목록
     *  $period(array):                 지정된 조회기간
     *
     *  반환값
     *  @return                          $this
     */
    public function selectStatsOfType($subjectId) {
        return $this->selectScoresList($subjectId)->groupBy('type')
            ->select([
                'type', DB::raw('count(score) AS count'),
                DB::raw('sum(perfect_score) AS perfect_score'),
                DB::raw('sum(score) AS gained_score'),
                DB::raw('format((sum(score) / sum(perfect_score)) * 100, 0) AS average')
            ]);
    }

    /**
     *  함수명:                         selectStatsOfType
     *  함수 설명:                      해당 학생이 해당 과목에서 성적 유형별로 취득한 성적을 조회
     *  만든날:                         2018년 4월 29일
     *
     *  매개변수 목록
     *  @param $subjectId :             강의 코드
     *
     *  지역변수 목록
     *  $finalStats:                    기말고사 성적표
     *  $midtermStats:                  중간고사 성적표
     *  $homeworkStats:                 과제 성적표
     *  $quizStats:                     쪽지시험 성적표
     *  $subject:                       강의 데이터
     *
     *  반환값
     *  @return                         array
     */
    public function selectStatList($subjectId) {
        // 데이터 획득
        $finalStats     = $this->selectStatsOfType($subjectId)->where('type', 'final')->get()->all();
        $midtermStats   = $this->selectStatsOfType($subjectId)->where('type', 'midterm')->get()->all();
        $homeworkStats  = $this->selectStatsOfType($subjectId)->where('type', 'homework')->get()->all();
        $quizStats      = $this->selectStatsOfType($subjectId)->where('type', 'quiz')->get()->all();
        $subject        = Subject::findOrFail($subjectId);

        // 성적 통계표
        $stats = [
            'final'     => [
//                'type'          => '기말',
                'count'         => sizeof($finalStats) <= 0 ? 0 : $finalStats[0]->count,
                'perfect_score' => sizeof($finalStats) <= 0 ? 0 : $finalStats[0]->perfect_score,
                'gained_score'  => sizeof($finalStats) <= 0 ? 0 : $finalStats[0]->gained_score,
                'average'       => sizeof($finalStats) <= 0 ? 0 : $finalStats[0]->average,
                'reflection'    => number_format($subject->final_reflection * 100, 0)
            ],
            'midterm'   => [
//                'type'          => '중간',
                'count'         => sizeof($midtermStats) <= 0 ? 0 : $midtermStats[0]->count,
                'perfect_score' => sizeof($midtermStats) <= 0 ? 0 : $midtermStats[0]->perfect_score,
                'gained_score'  => sizeof($midtermStats) <= 0 ? 0 : $midtermStats[0]->gained_score,
                'average'       => sizeof($midtermStats) <= 0 ? 0 : $midtermStats[0]->average,
                'reflection'    => number_format($subject->midterm_reflection * 100, 0)
            ],
            'homework'  => [
//                'type'          => '과제',
                'count'         => sizeof($homeworkStats) <= 0 ? 0 : $homeworkStats[0]->count,
                'perfect_score' => sizeof($homeworkStats) <= 0 ? 0 : $homeworkStats[0]->perfect_score,
                'gained_score'  => sizeof($homeworkStats) <= 0 ? 0 : $homeworkStats[0]->gained_score,
                'average'       => sizeof($homeworkStats) <= 0 ? 0 : $homeworkStats[0]->average,
                'reflection'    => number_format($subject->homework_reflection * 100, 0)
            ],
            'quiz'      => [
//                'type'          => '쪽지',
                'count'         => sizeof($quizStats) <= 0 ? 0 : $quizStats[0]->count,
                'perfect_score' => sizeof($quizStats) <= 0 ? 0 : $quizStats[0]->perfect_score,
                'gained_score'  => sizeof($quizStats) <= 0 ? 0 : $quizStats[0]->gained_score,
                'average'       => sizeof($quizStats) <= 0 ? 0 : $quizStats[0]->average,
                'reflection'    => number_format($subject->quiz_reflection * 100, 0)
            ]
        ];
        foreach($stats as $key => $value) {
            $stats[$key]['type'] = __("study.{$key}");
        }

        return $stats;
    }

    // 학생 정보 갱신 메서드
    public function updateMyInfo(Array $dataArray) {
        // 01. 사용자의 데이터 갱신
        $user = $this->user;

        if(isset($dataArray['password']))
            $user->password = password_hash($dataArray['password'], PASSWORD_DEFAULT);
        if(isset($dataArray['email']))      $user->email    = $dataArray['email'];
        if(isset($dataArray['phone']))      $user->phone    = $dataArray['phone'];
        if(isset($dataArray['photo']))      $user->photo    = $dataArray['photo'];

        if($user->save() !== true) return false;

        return true;
    }

    // 연속/누적 출석데이터 획득
    public function selectAttendancesStats($argDaysUnit) {
        // 01. 데이터 획득
        $startDate  = today()->subDays($argDaysUnit - 1)->format("Y-m-d");
        $endDate    = today()->format('Y-m-d');

        $attendancesRecords = $this->attendances()->start($startDate)->end($endDate);

        // 02. 연속 데이터 추출
        $continuativeData = [
            'lateness'      => null,
            'absence'       => null,
            'early_leave'   => null
        ];
        $tempLateness   = 0;
        $tempAbsence    = 0;
        $tempEarlyLeave = 0;
        $tempArray = with(clone $attendancesRecords)->orderDesc()->get()->all();
        foreach($tempArray as $item) {
            // 모든 데이터를 추출했다면 => 반복문 종료
            if(!in_array(null, $continuativeData)) {
                break;
            }

            // 지각
            if(is_null($continuativeData['lateness'])) {
                if($item->lateness_flag != 'good') {
                    $tempLateness++;
                } else {
                    $continuativeData['lateness'] = $tempLateness;
                }
            }

            // 결석
            if(is_null($continuativeData['absence'])) {
                if($item->absence_flag != 'good') {
                    $tempAbsence++;
                } else {
                    $continuativeData['absence'] = $tempAbsence;
                }
            }

            // 조퇴
            if(is_null($continuativeData['early_leave'])) {
                if($item->early_leave_flag != 'good') {
                    $tempEarlyLeave++;
                } else {
                    $continuativeData['early_leave'] = $tempEarlyLeave;
                }
            }
        }

        // 03. 데이터 반환
        return [
            'total_lateness'            => with(clone $attendancesRecords)->lateness()->count(),
            'total_absence'             => with(clone $attendancesRecords)->absence()->count(),
            'total_early_leave'         => with(clone $attendancesRecords)->earlyLeave()->count(),
            'continuative_lateness'     => $continuativeData['lateness'],
            'continuative_absence'      => $continuativeData['absence'],
            'continuative_early_leave'  => $continuativeData['early_leave'],
        ];
    }

    // (결석|조퇴|지각)이 잦은 요일을 계산
    public function selectFrequentAttendances() {
        // 01. 출석 데이터 획득
        $attendances = $this->attendances()->recent()->select('reg_date');

        // 02. 자주 (지각|조퇴|결석)하는 요일 획득
        $frequentDay['lateness']    = with(clone $attendances)->lateness();
        $frequentDay['absence']     = with(clone $attendances)->absence();
        $frequentDay['early_leave'] = with(clone $attendances)->earlyLeave();
        $frequentResult = [
            'lateness'      => null,
            'absence'       => null,
            'early_leave'   => null
        ];
        $averageLateTime    = null;
        foreach($frequentDay as $adaType => $adaDataArray) {
            // ##### (지각|결석|조퇴) 비율이 일정 비율(기본값 : 0.2)을 넘기지 못할 경우 => 연산 중단 ######
            if(($adaDataArray->count() / $attendances->count()) < 0.2) {
                $type = __("ada.{$adaType}");
                $frequentResult[$adaType] = __('tutor.percent_not_enough', ['type' => $type, 'percent' => 20]);
                continue;
            }

            // 유형별 (지각|결석|조퇴)가 잦은 요일 획득
            $dailyCount = [
                'monday'   => 0,
                'tuesday'   => 0,
                "wednesday"   => 0,
                "thursday"   => 0,
                'friday'   => 0
            ];
            foreach($adaDataArray->get()->all() as $attendance) {
                $regDate = explode('-', $attendance->reg_date);
                $regDate = Carbon::createFromDate($regDate[0], $regDate[1], $regDate[2]);

                // 각 요일별 (지각|결석|조퇴) 횟수 획득
                switch ($regDate->dayOfWeek) {
                    case Carbon::MONDAY:
                        $dailyCount[__('interface.monday')]++;
                        break;
                    case Carbon::TUESDAY:
                        $dailyCount[__('interface.tuesday')]++;
                        break;
                    case Carbon::WEDNESDAY:
                        $dailyCount[__('interface.wednesday')]++;
                        break;
                    case Carbon::THURSDAY:
                        $dailyCount[__('interface.thursday')]++;
                        break;
                    case Carbon::FRIDAY:
                        $dailyCount[__('interface.friday')]++;
                        break;
                    default:
                        continue 2;
                }

                arsort($dailyCount);
                $frequentResult[$adaType] = array_search($max = max($dailyCount), $dailyCount).//" ({$max} 회)";\
                                            sprintf("(%s)", __('interface.count', ['count' => $max]));
            }
        }

        return $frequentResult;
    }

    // 최근 평균 지각 시각 계산
    public function selectAverageLatenessTime() {
        // 01. 출석 데이터 획득
        $attendances = $this->attendances()->recent()->lateness()->select('detail');

        // 02. 평균 지각시간 계산
        $totalTime = 0;
        foreach($attendances->get()->pluck('detail')->all() as $detailObj) {
            $detail = json_decode($detailObj);

            $totalTime += $detail->lateness_time;
        }

        return number_format($attendances->count() > 0 ? $totalTime / $attendances->count() : 0, 0, '.', '');
    }

    // (지각|조퇴|결석) 주요 사유 조회
    public function selectAttendanceReason() {
        // 01. 출석 데이터 획득
        $attendances = $this->attendances()->recent();

        // 02. 각 유형별 데이터 획득
        $reasons['lateness'] = with(clone $attendances)->lateness()
            ->groupBy('lateness_flag')->having('lateness_flag', '!=', 'good')
            ->select(['lateness_flag as reason', DB::raw('count(*) as count')])->get()->all();
        $reasons['early_leave'] = with(clone $attendances)->earlyLeave()
            ->groupBy('early_leave_flag')->having('early_leave_flag', '!=', 'good')
            ->select(['early_leave_flag as reason', DB::raw('count(*) as count')])->get()->all();
        $reasons['absence'] = with(clone $attendances)->absence()
            ->groupBy('absence_flag')->having('absence_flag', '!=', 'good')
            ->select(['absence_flag as reason', DB::raw('count(*) as count')])->get()->all();
        $reasonResult = [
            'lateness'      => null,
            'absence'       => null,
            'early_leave'   => null
        ];

        foreach($reasons as $type => $reason) {
            $maxValue = 0;

            foreach($reason as $row) {
                if($row->count > $maxValue) {
                    $maxValue = $row->count;
                    $reasonResult[$type] = __("ada.{$row->reason}");
                }
            }
        }

        return $reasonResult;
    }

    // 월 평균 (지각|조퇴|결석) 횟수 획득
    public function selectMonthlyAverageAttendances() {
        // 01. 출석 데이터 획득
        $count = $this->attendances()->groupBy('m')->select([
            DB::raw("date_format(reg_date, '%Y-%m') AS m"),
            DB::raw("count(CASE lateness_flag when 'good' THEN NULL ELSE TRUE END) AS lateness_count"),
            DB::raw("count(CASE absence_flag when 'good' THEN NULL ELSE TRUE END) AS absence_count"),
            DB::raw("count(CASE early_leave_flag when 'good' THEN NULL ELSE TRUE END) AS early_leave_count"),
        ]);

        // 02. 평균 계산
        $total_lateness_count       = $count->get(['lateness_count'])->pluck('lateness_count')->all();
        $total_absence_count        = $count->get(['absence_count'])->pluck('absence_count')->all();
        $total_early_leave_count    = $count->get(['early_leave_count'])->pluck('early_leave_count')->all();
        $rowCount = sizeof($count->get()->all());

        return [
            'lateness'      => number_format(array_sum($total_lateness_count) / $rowCount, 0),
            'absence'       => number_format(array_sum($total_absence_count) / $rowCount, 0),
            'early_leave'   => number_format(array_sum($total_early_leave_count) / $rowCount, 0),
        ];
    }

    // 내가 수강하는 강의 목록 조회
    public function selectSubjectList() {
        return Subject::whereIn('id', $this->joinLists()->pluck('subject_id')->all());
    }
}