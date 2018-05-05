<?php

namespace App\Http\Controllers;

use App\Exceptions\NotValidatedException;
use App\GainedScore;
use App\Subject;
use Dotenv\Exception\ValidationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Validator;
use App\User;
use App\Professor;
use App\Student;
use App\Score;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\UploadScoresFormExport;
use PhpOffice\PhpSpreadsheet\IOFactory;

/**
 *  클래스명:               ProfessorController
 *  설명:                   교수 회원에게 제공하는 관련 기능들을 정의하는 클래스
 *  만든이:                 3-WDJ 春目指し 1401213 이승민
 *  만든날:                 2018년 4월 26일
 *
 *  함수 목록
 *      - 메인
 *          = index:                            교수 회원의 메인 페이지를 출력
 *
 *          = isTutor:                          해당 교수의 지도반 소유 여부를 조회
 *
 *
 *
 *      - 내 정보 관리
 *          = getMyInfo:                        사용자 정보를 획득
 *
 *          = updateMyInfo:                     사용자 정보를 갱신
 *
 *
 *
 *      - 강의 관리
 *          = getMySubjectList:                 내가 담당하는 강의 목록을 획득
 *
 *          = getJoinListOfSubject:             해당 과목의 수강학생 목록을 조회
 *
 *          = downloadScoreForm:                성적 등록을 위한 엑셀 양식을 다운로드
 *
 *          = uploadScoresAtExcel:              엑셀 파일을 분석하여 성적을 등록
 *
 *          = uploadScores:                     프론트엔드 인터페이스를 이용해 직접 성적 등록
 *
 *          = getScoresList:                    해당 과목에서 제출된 성적 목록 조회
 *
 *          = getGainedScoreList:               해당 성적 유형에서 학생들이 취득한 성적 확인
 *
 *          = updateGainedScore:                해당 학생의 성적 갱신
 *
 *          = detailScoresOfStudent:            지정한 학생이 해당 과목에서 취득한 성적 목록 조회
 *
 *          = getAchievementReflections:        해당 강의의 성적별 학업성취도 반영비율 조회
 *
 *          = updateAchievementReflections:     해당 강의의 성적별 학업성취도 반영비율 수정
 */
class ProfessorController extends Controller
{
    // 01. 멤버 변수 선언

    // 02. 멤버 메서드 정의

    // 메인
    /**
     *  함수명:                         index
     *  함수 설명:                      학생 회원의 메인 페이지를 출력
     *  만든날:                         2018년 4월 26일
     *
     *  매개변수 목록
     *  null
     *
     *  지역변수 목록
     *  null
     *
     *  반환값
     *  @return                         \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index() {
        return view('index');
    }

    /**
     *  함수명:                         isTutor
     *  함수 설명:                      해당 교수의 지도반 소유 여부를 조회
     *  만든날:                         2018년 5월 02일
     *
     *  매개변수 목록
     *  null
     *
     *  지역변수 목록
     *  null
     *
     *  반환값
     *  @return                         \Illuminate\Http\JsonResponse
     */
    public function isTutor() {
        if(!is_null(session()->get('user')->study_class)) {
            return response()->json(true, 200);
        } else {
            return response()->json(false, 200);
        }
    }



    // 내 정보 관리
    /**
     *  함수명:                         getMyInfo
     *  함수 설명:                      사용자 정보를 획득
     *  만든날:                         2018년 5월 05일
     *
     *  매개변수 목록
     *  null
     *
     *  지역변수 목록
     *  null
     *
     *  반환값
     *  @return \Illuminate\Http\JsonResponse
     */
    public function getMyInfo() {
        return response()->json(new ResponseObject(
            true, [
                'id'        => session()->get('user')->id,
                'name'      => session()->get('user')->name,
                'phone'     => session()->get('user')->phone,
                'email'     => session()->get('user')->email,
                'office'    => session()->get('user')->office,
                'photo'     => session()->get('user')->photo_url
            ]
        ), 200);
    }

    /**
     *  함수명:                         updateMyInfo
     *  함수 설명:                      사용자 정보를 수정
     *  만든날:                         2018년 5월 05일
     *
     *  매개변수 목록
     *  @param Request $request:        요청 메시지
     *
     *  지역변수 목록
     *  null
     *
     *  반환값
     *  @return \Illuminate\Http\JsonResponse
     *
     *  예외
     *  @throws NotValidatedException
     */
    public function updateMyInfo(Request $request) {
        // 01. 유효성 검사
        $validator = Validator::make($request->all(), [
            'password'          => 'required_with:password_check|same:password_check',
            'password_check'    => 'required_with:password|same:password',
            'phone'             => 'required',
            'email'             => 'required|email',
            'office'            => 'required|string',
            'photo'             => 'image'
        ]);

        if($validator->fails()) {
            throw new NotValidatedException($validator->errors());
        }

        // 02. 데이터 획득
        $professor  = Professor::findOrFail(session()->get('user')->id);
        $password   = $request->post('password');
        $phone      = $request->post('phone');
        $email      = $request->post('email');
        $office     = $request->post('office');
        $photo      = $request->hasFile('photo') ? $request->file('photo') : null;

        // 03. 데이터 수정
        $updateInfo['password'] = $password;
        $updateInfo['phone']    = $phone;
        $updateInfo['email']    = $email;
        $updateInfo['office']   = $office;

        if(!is_null($photo)) {
            // 기존 이미지가 존재한다면 => 기존 이미지 삭제
            $original_photo = session()->get('user')->photo;
            // 파일의 존재여부를 확인
            if(Storage::disk('prof_photo')->exists($original_photo)) {
                // 삭제
                Storage::disk('prof_photo')->delete($original_photo);
            }

            // 새 이미지 저장 => DB 사용자 정보에 새로운 이미지 경로를 지정
            $fileName = $photo->store('/','prof_photo');
            $updateInfo['photo'] = $fileName;
        }

        // 04. 데이터 갱신
        if($professor->updateMyInfo($updateInfo)) {
            session()->put('user', $professor->user->selectUserInfo());

            return response()->json(new ResponseObject(
                true, "정보 갱신을 완료했습니다."
            ), 200);
        } else {
            return response()->json(new ResponseObject(
                false, "정보 갱신을 실패했습니다."
            ), 200);
        }
    }


    // 강의 관리
    /**
     *  함수명:                        getMySubjectList
     *  함수 설명:                     내가 담당하는 강의 목록을 획득
     *  만든 날:                       2018년 4월 30일
     *
     *  매개변수 목록
     *  @param Request $request :       요청 데이터
     *
     *  지역변수 목록
     *  $validator:                     유효성 검증 객체
     *  $professor:                     현재 접속한 교수의 정보
     *  $argPeriod:                     매개변수 설정
     *  $periodValue:                   자체 함수로 획득한 학기 값
     *  $subjects:                      해당 학기에 담당하고 있는 교과목 목록
     *  $pagination:                    페이지네이션 값
     *  $data:                          View 단에 전송할 데이터
     *
     *  반환값
     *  @return \Illuminate\Http\JsonResponse 예외
     *
     * 예외
     *  @throws NotValidatedException
     */
    public function getMySubjectList(Request $request) {
        // 01. 유효성 검증
        $validator = Validator::make($request->all(), [
            'period'  => 'regex:/^[1-2]\d{3}-[1-2]?[a-zA-Z_]+$/'
        ]);

        if($validator->fails()) {
            throw new NotValidatedException($validator->errors());
        }

        // 02. 데이터 획득
        $professor      = Professor::findOrFail(session()->get('user')->id);
        $argPeriod      = $request->exists('period') ? $request->get('period') : null;
        $periodValue    = $this->getTermValue($argPeriod);
        $subjects       = $professor->subjects()->term($periodValue['this'])->get(['id', 'name'])->all();

        // ###### 조회된 과목 리스트가 없을 경우 ######
        if(sizeof($subjects) <= 0) {
            return response()->json(new ResponseObject(
                false, "해당 학기에 조회된 강의가 없습니다."
            ), 200);
        }

        // 03. 페이지네이션 데이터 지정
        $pagination     = [
            'prev'          => $periodValue['prev'],
            'this_page'     => $periodValue['this_format'],
            'next'          => $periodValue['next']
        ];

        // 04. 전송할 데이터 지정
        $data = [
            'subjects'      => $subjects,
            'pagination'    => $pagination
        ];

        return response()->json(new ResponseObject(
            true, $data
        ));
    }

    /**
     *  함수명:                        getJoinListOfSubject
     *  함수 설명:                     해당 과목의 수강학생 목록을 조회
     *  만든날:                        2018년 4월 30일
     *
     *  매개변수 목록
     *  @param Request $request :       요청 메시지
     *
     *  지역변수 목록
     *
     *  반환값
     *  @return \Illuminate\Http\JsonResponse 예외
     *
     * 예외
     *  @throws NotValidatedException
     */
    public function getJoinListOfSubject(Request $request) {
        // 01. 유효성 검사
        $validator = Validator::make($request->all(), [
            'subject_id'        => 'required|exists:subjects,id'
        ]);

        if($validator->fails()) {
            throw new NotValidatedException($validator->errors());
        }

        // 02. 강의 데이터 조회
        $professor  = Professor::findOrFail(session()->get('user')->id);
        $subject    = $professor->isMySubject($request->get('subject_id'));


        // 03. 수강학생 목록 조회
        $joinList = $subject->joinLists()->join('users', 'users.id', 'join_lists.std_id')
            ->get(['users.id', 'users.name', 'users.photo', 'join_lists.subject_id', 'join_lists.achievement'])->all();

        // 학업 성취도 삽입
        foreach($joinList as $item) {
            $item->achievement = number_format($item->achievement * 100, 0);
        }

        // 04. 데이터 반환
        return response()->json(new ResponseObject(
            true, $joinList
        ), 200);
    }

    /**
     *  함수명:                         downloadScoreForm
     *  함수 설명:                      성적 등록을 위한 엑셀 양식을 다운로드
     *  만든날:                         2018년 4월 30일
     *
     *  매개변수 목록
     *  @param $request:                요청 객체
     *
     *  지역변수 목록
     *  $professor:                     성적을 등록하는 교수의 아이디
     *  $lecture:                       성적이 등록되는 과목 정보
     *  $fileName:                      파일 이름
     *  $scoreType:                     성적 유형
     *  $content:                       이 성적에 대한 설명
     *  $perfectScore:                  만점
     *  $fileType:                      파일 확장자
     *  $studentList:                   학생 목록
     *  $data:                          엑셀 파일에 등록할 데이터
     *
     *  반환값
     *  @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     *
     *  예외
     *  @throws NotValidatedException
     */
    public function downloadScoreForm(Request $request) {
        // 01. 입력값 검증
        $valid_today     = today()->format('Y-m-d');
        $valid_scoreType = implode(',', self::SCORE_TYPE);
        $validator = Validator::make($request->all(), [
            'subject_id'        => 'required|exists:subjects,id',
            'file_name'         => 'required|string|min:2',
            'execute_date'      => "required|date|before_or_equal:{$valid_today}",
            'score_type'        => "required|in:{$valid_scoreType}",
            'content'           => 'required|string|min:2',
            'perfect_score'     => 'required|between:1,999',
            'file_type'         => 'required|string|in:xlsx,xls,csv',
        ]);

        if($validator->fails()) {
            throw new NotValidatedException($validator->errors());
        }

        // 02. 데이터 획득
        $professor  = Professor::findOrFail(session()->get('user')->id);
        $subject    = $professor->isMySubject($request->post('subject_id'));


        $fileName       = $request->post('file_name');
        $executeDate    = $request->post('execute_date');
        $scoreType      = $request->post('score_type');
        $content        = $request->post('content');
        $perfectScore   = $request->post('perfect_score');
        $fileType       = $request->post('file_type');
        $studentList    = $subject->joinLists()->join('users', 'users.id', 'join_lists.std_id')
                            ->get(['users.id', 'users.name'])->all();

        // 03. 엑셀에 삽입할 데이터 구성
        $data = [
            ['강의 코드', $subject->id],
            ['등록일자', $executeDate],
            ['유형', $scoreType],
            ['만점', $perfectScore],
            ['설명', $content],
            ['학번', '이름', '취득점수'],
        ];
        $data = array_merge_recursive($data, $studentList);

        // 04. 확장자 지정
        switch(strtolower($fileType)) {
            case 'xlsx':
                $fileType = \Maatwebsite\Excel\Excel::XLSX;
                break;
            case 'xls':
                $fileType = \Maatwebsite\Excel\Excel::XLS;
                break;
            case 'csv':
                $fileType = \Maatwebsite\Excel\Excel::CSV;
                break;
        }

        // 05. 엑셀 파일 생성
        return Excel::download(new UploadScoresFormExport($data), $fileName.'.'.$fileType, $fileType);
    }

    /**
     *  함수명:                         uploadScoresAtExcel
     *  함수 설명:                      엑셀 파일을 분석하여 성적을 등록
     *  만든날:                         2018년 4월 30일
     *
     *  매개변수 목록
     *  @param Request $request :        요청 메시지
     *
     *  지역변수 목록
     *  null
     *
     *  반환값
     *  @return \Illuminate\Http\JsonResponse
     *
     *  예외
     *  @throws NotValidatedException
     */
    public function uploadScoresAtExcel(Request $request) {
        // 01. 전송 데이터 유효성 검사
        $validator = Validator::make($request->all(), [
            'upload_file'       => 'required|file|mimes:xlsx,xls,csv'
        ]);

        if($validator->fails()) {
            throw new NotValidatedException($validator->errors());
        }

        // 02. 변수 설정
        $professor  = Professor::findOrFail(session()->get('user')->id);
        $file       = $request->file('upload_file');
        $fileType   = ($array = explode('.', $file->getClientOriginalName()))[sizeof($array) - 1];

        // 03. 전송받은 파일 해석
        $reader = IOFactory::createReader($this->getType($fileType));
        $reader->setReadDataOnly(true);
        $sheetData = $reader->load($file->path())->getActiveSheet()->toArray(
            null, true, true, true
        );

        // 04. 데이터 추출

        // 추출 데이터 - 강의
        $extractData['subject_id']  = $sheetData[1]['B'];
        $subject = $professor->isMySubject($extractData['subject_id']);

        // 실시 일자
        $extractData['execute_date'] = $sheetData[2]['B'];

        // 분류
        $extractData['score_type'] = $sheetData[3]['B'];

        // 만점
        $extractData['perfect_score'] = $sheetData[4]['B'];

        // 성적 내용
        $extractData['content'] = $sheetData[5]['B'];

        // 04. 유효성 검증 & 데이터 추출
        $valid_today     = today()->format('Y-m-d');
        $valid_scoreType = implode(',', self::SCORE_TYPE);
        $validator = Validator::make($extractData, [
            'subject_id'        => 'required|exists:subjects,id',
            'execute_date'      => "required|date|before_or_equal:{$valid_today}",
            'score_type'        => "required|in:{$valid_scoreType}",
            'content'           => 'required|string|min:2',
            'perfect_score'     => 'required|between:1,999',
        ]);

        if($validator->fails()) {
            throw new NotValidatedException($validator->errors());
        }

        // 학생-점수 리스트
        $extractData['std_list'] = [];
        $signUpList = $subject->joinLists()->get(['std_id'])->pluck('std_id')->all();
        foreach($sheetData as $key => $row) {
            // 키값이 7보다 작으면(학생 리스트 등장 이전) 다음 행 추출
            if($key < 7) {
                continue;
            }

            $stdId = NULL;
            $score = NULL;
            // 행에서 셀을 추출하여 순환
            foreach($row as $deepKey => $cell) {
                // 각 셀에 대해 데이터 무결성 확인
                switch($deepKey) {
                    case 'A':
                        // 학번의 자료형이 수 이고, 강의를 수강하고 있는 학생일 때
                        if(is_numeric($cell)) {
                            if (in_array($cell, $signUpList)) {
                                $stdId = $cell;
                                break;
                            }
                        }
                        throw new NotValidatedException("등록되지 않은 학생이 존재합니다.");
                    case 'B':
                        // 학생의 이름 칸 => 건너뛰기
                        continue;
                    case 'C':
                        // 학생이 취득한 점수 => 0점 이상 만점 이하일 것
                        if(is_numeric($cell)) {
                            if ($cell <= $extractData['perfect_score'] && $cell >= 0) {
                                $score = $cell;
                                break;
                            }
                        }
                        throw new NotValidatedException('형식에 맞지 않게 입력된 점수가 존재합니다.');
                }
            }

            // 데이터 삽입
            $extractData['std_list'][$stdId] = $score;
        }
        // 새로운 점수 유형 생성
        $score = new Score();
        $score->subject_id      = $extractData['subject_id'];
        $score->execute_date    = $extractData['execute_date'];
        $score->type            = $extractData['score_type'];
        $score->detail          = $extractData['content'];
        $score->perfect_score   = $extractData['perfect_score'];

        // 각 학생별로 취득 점수 등록
        if($score->insertScoreList($extractData['std_list'])) {
            return response()->json(new ResponseObject(
                true, "성적 등록에 성공하였습니다."
            ), 200);
        } else {
            return response()->json(new ResponseObject(
                false, "성적 등록에 실패하였습니다."
            ), 200);
        }
    }

    /**
     *  함수명:                         uploadScores
     *  함수 설명:                      프론트엔드 인터페이스를 이용해 직접 성적 등록
     *  만든날:                         2018년 5월 03일
     *
     *  매개변수 목록
     *  @param Request $request :        요청 메시지
     *      # subject_id(강의 코드):            필수|강의 테이블에 존재하는 코드일 것
     *      # execute_date(실시 일자):          필수|날짜 자료형|오늘보다 미래시점은 금지
     *      # score_type(성적 유형):            필수|시스템에서 지정한 타입일 것
     *      # detail(상세 내용):                문자열|최소:2
     *      # perfect_score(만점):              필수|숫자형|최소:1|최대:999
     *      # gained_score(취득 점수):          필수|배열형일 것
     *
     *
     *  지역변수 목록
     *  null
     *
     *  반환값
     *  @return \Illuminate\Http\JsonResponse
     *
     *  예외
     *  @throws NotValidatedException
     */
    public function uploadScores(Request $request) {
        // 01. 요청 유효성 검사
        $valid_today        = today()->format('Y-m-d');
        $valid_scoreType    = implode(',', self::SCORE_TYPE);
        $validator = Validator::make($request->all(), [
            'subject_id'        => 'required|exists:subjects,id',
            'execute_date'      => "required|date|before:{$valid_today}",
            'score_type'        => "required|in:{$valid_scoreType}",
            'detail'            => 'string|min:2',
            'perfect_score'     => 'required|numeric|min:1|max:999',
            'gained_score'      => 'required|array',
        ]);

        if($validator->fails()) {
            throw new NotValidatedException($validator->errors());
        }

        // 02. 데이터 획득
        $professor  = Professor::findOrFail(session()->get('user')->id);
        $subject    = $professor->isMySubject($request->post('subject_id'));
        $signUpList = $subject->joinLists()->get(['std_id'])->pluck('std_id')->all();

        // 각 학생별 취득 성적 획득
        $gainedScoreList = [];
        foreach($request->post('gained_score') as $stdId => $gainedScore) {
            if($gainedScore < 0 || $gainedScore > $request->post('perfect_score')) {
                // 입력된 점수가 형식에 맞지 않을 때 => 알고리즘 종료
                throw new NotValidatedException("형식에 맞지 않게 입력된 점수가 존재합니다.");
            }

            if(in_array($stdId, $signUpList)) {
                // 해당 학생의 취득 점수를 등록
                $gainedScoreList[$stdId] = $gainedScore;
            } else {
                // 입력된 학생 목록 중 해당 강의의 수강생이 아닐 경우
                throw new NotValidatedException("등록되지 않은 학생이 존재합니다.");
            }
        }


        // 03. 새로운 성적 유형 생성
        $score = new Score();
        $score->subject_id      = $subject->id;
        $score->execute_date    = $request->post('execute_date');
        $score->type            = $request->post('score_type');
        $score->detail          = $request->post('detail');
        $score->perfect_score   = $request->post('perfect_score');

        // 각 학생별로 취득 점수 등록
        if($score->insertScoreList($score, $gainedScoreList)) {
            return response()->json(new ResponseObject(
                true, "성적 등록에 성공하였습니다."
            ), 200);
        } else {
            return response()->json(new ResponseObject(
                false, "성적 등록에 실패하였습니다."
            ), 200);
        }
    }

    /**
     *  함수명:                         getScoresList
     *  함수 설명:                      해당 과목에서 제출된 성적 목록 조회
     *  만든날:                         2018년 5월 02일
     *
     *  매개변수 목록
     *  @param Request $request :       요청 메시지
     *      # subject_id(강의 코드):            필수|강의 테이블에 존재하는 코드일 것
     *
     *  지역변수 목록
     *  $validator:                     요청 유효성 검사용 객체
     *  $professor:                     교수회원 정보
     *  $subject:                       강의 정보
     *  $scores:                        해당 과목에서 제출된 성적 목록
     *
     *  반환값
     *  @return \Illuminate\Http\JsonResponse 예외
     *
     * 예외
     *  @throws NotValidatedException
     */
    public function getScoresList(Request $request) {
        // 01. 데이터 유효성 검사
        $validator = Validator::make($request->all(), [
            'subject_id'    => 'required|exists:scores,id'
        ]);

        if($validator->fails()) {
            throw new NotValidatedException($validator->errors());
        }

        // 02. 데이터 획득
        $professor  = Professor::findOrFail(session()->get('user')->id);
        $subject    = $professor->isMySubject($request->get('subject_id'));
        $scores     = $subject->scores()->orderBy('execute_date', 'desc')
                        ->get(['id', 'execute_date', 'type', 'detail'])->all();

        // 03. 데이터 반환
        return response()->json(new ResponseObject(
            true, $scores
        ), 200);
    }

    // 해당 성적 유형에서 학생들이 취득한 성적 확인
    public function getGainedScoreList(Request $request) {
        // 01. 요청 유효성 검사
        $validator = Validator::make($request->all(), [
            'score_type'    => 'required|exists:scores,id',
        ]);

        if($validator->fails()) {
            throw new NotValidatedException($validator->errors());
        }

        // 02. 데이터 획득
        $professor  = Professor::findOrFail(session()->get('user')->id);
        $score      = Score::findOrFail($request->post('score_type'));

        // 현재 접속한 회원이 이 성적에 접근할 권한이 있는지 확인
        $subject    = $professor->isMySubject($score->subject->id);

        $gainedScores   = $subject->selectGainedScoreList($score->id)->get()->all();

        // 03. 반환 데이터 설정
        $data = [
            'score_info'    => $score,
            'gained_scores' => $gainedScores
        ];

        return response()->json(new ResponseObject(
            true, $data
        ), 200);
    }

    /**
     *  함수명:                         updateGainedScore
     *  함수 설명:                      해당 학생의 성적 갱신
     *  만든날:                         2018년 4월 26일
     *
     *  매개변수 목록
     *  @param Request $request:        요청 메시지
     *      # gained_score_id(취득점수 코드):         필수|취득점수 테이블에 존재하는 코드
     *      # std_id(학번):                           필수|학생 테이블에 존재하는 학번
     *      # score(취득점수):                        필수|숫자|최소:0|최대:999 => 이후에 만점을 초과하지 않는지 추가로 검사
     *
     *  지역변수 목록
     *  $validator:                     요청 유효성 검사용 객체

     *  $professor:                     교수 정보
     *  $gainedScore:                   취득점수 정보
     *  $scoreType:                     성적 유형
     *  $subject:                       강의 정보
     *  $student:                       학생 정보
     *
     *  반환값
     *  @return                         \Illuminate\Http\JsonResponse
     *
     *  예외
     *  @throws                         NotValidatedException
     */
    public function updateGainedScore(Request $request) {
        // 01. 유효성 검사
        $validator = Validator::make($request->all(), [
            'gained_score_id'   => 'required|exists:gained_scores,id',
            'std_id'            => 'required|exists:students,id',
            'score'             => 'required|numeric|min:0|max:999'
        ]);

        if($validator->fails()) {
            throw new NotValidatedException($validator->errors());
        }

        // 02. 데이터 획득
        $professor      = Professor::findOrFail(session()->get('user')->id);
        $gainedScore    = GainedScore::findOrFail($request->post('gained_score_id'));
        $scoreType      = $gainedScore->scoreType;
        $subject        = $professor->isMySubject($scoreType->subject_id);
        $student        = Student::findOrFail($request->post('std_id'));

        if(!in_array($student->id, $subject->joinLists()->get(['std_id'])->pluck('std_id')->all())) {
            // ###### 성적 수정을 요청한 학생이 해당 강의의 수강생이 아닐 때 ######
            throw new NotValidatedException("해당 학생은 이 강의의 수강생이 아닙니다.");
        }

        $score          = $request->post('score');

        if($score > $scoreType->perfect_score) {
            // ##### 입력된 성적이 만점을 초과할 경우 #####
            throw new NotValidatedException("입력한 성적이 만점을 초과합니다.");
        }


        // 03. 데이터 수정
        $gainedScore->score = $score;
        if($gainedScore->save() === true) {
            return response()->json(new ResponseObject(
                true, "성적 수정이 완료되었습니다."
            ), 200);
        } else {
            return response()->json(new ResponseObject(
                false, '성적 수정이 실패하였습니다.'
            ), 200);
        }
    }

    /**
     *  함수명:                         getScoresOfStudents
     *  함수 설명:                      지정한 학생이 해당 과목에서 취득한 성적 목록 조회
     *  만든날:                         2018년 5월 02일
     *
     *  매개변수 목록
     *  @param Request $request :       요청 메시지
     *
     *  지역변수 목록
     *  $validator:                     요청 유효성 검사용 객체
     *  $professor:                     교수회원 정보
     *  $subject:                       강의 정보
     *  $student:                       조회하고자 하는 학생 정보
     *  $scores:                        해당 학생의 성적 목록
     *
     *  반환값
     *  @return \Illuminate\Http\JsonResponse 예외
     *
     * 예외
     *  @throws NotValidatedException
     */
    public function detailScoresOfStudent(Request $request) {
        // 01. 유효성 검사
        $validator = Validator::make($request->all(), [
            'std_id'        => 'required|exists:students,id',
            'subject_id'    => 'required|exists:subjects,id'
        ]);

        if($validator->fails()) {
            throw new NotValidatedException($validator->errors());
        }

        // 02. 데이터 획득
        $professor  = Professor::findOrFail(session()->get('user')->id);
        $subject    = $professor->isMySubject($request->get('subject_id'));
        $student    = in_array($request->get('std_id'), $subject->joinLists->pluck('std_id')->all())
                            ? Student::findOrFail($request->get('std_id')) : null;

        // ##### 해당 과목을 수강하는 학생이 아닐 때 ######
        if(is_null($student)) {
            throw new NotValidatedException("잘못된 학번입니다.");
        }

        // 03. View 단에 반환할 성적 목록 획득
        $scores = $student->selectScoresList($subject->id)->get()->all();


        // 03. 데이터 반환
        return response()->json(new ResponseObject(
            true, $scores
        ), 200);
    }

    /**
     *  함수명:                         getAchievementReflections
     *  함수 설명:                      해당 강의의 성적별 학업성취도 반영비율 조회
     *  만든날:                         2018년 5월 04일
     *
     *  매개변수 목록
     *  @param Request $request :       요청 메시지
     *      # subject_id(강의 코드):            필수|강의 테이블에 존재하는 코드일 것
     *
     *  지역변수 목록
     *  $validator:                     요청 유효성 검사용 객체
     *  $professor:                     교수회원 정보
     *  $subject:                       강의 정보
     *  $data:                          프론트엔드에 반환하는 값
     *
     *  반환값
     *  @return \Illuminate\Http\JsonResponse 예외
     *
     * 예외
     *  @throws NotValidatedException
     */
    public function getAchievementReflections(Request $request) {
        // 01. 데이터 유효성 검증
        $validator = Validator::make($request->all(), [
            'subject_id'        => 'required|exists:subjects,id'
        ]);

        if($validator->fails()) {
            throw new NotValidatedException($validator->errors());
        }

        // 02. 데이터 획득
        $professor  = Professor::findOrFail(session()->get('user')->id);
        $subject    = $professor->isMySubject($request->get('subject_id'));

        // 03. 반환 데이터 설정
        $data = [
            // 기말 반영비
            'final'         => number_format($subject->final_reflection * 100, 0),

            // 중간 반영비
            'midterm'       => number_format($subject->midterm_reflection * 100, 0),

            // 과제 반영비
            'homework'      => number_format($subject->homework_reflection * 100, 0),

            // 쪽지시험 반영비
            'quiz'          => number_format($subject->quiz_reflection * 100, 0),
        ];

        return response()->json(new ResponseObject(
            true, $data
        ), 200);
    }

    /**
     *  함수명:                         updateAchievementReflections
     *  함수 설명:                      해당 강의의 성적별 학업성취도 반영비율 수정
     *  만든날:                         2018년 5월 04일
     *
     *  매개변수 목록
     *  @param Request $request :       요청 메시지
     *      # subject_id(강의 코드):                필수|강의 테이블에 존재하는 코드일 것
     *      # final(기말 반영비):                   필수|숫자형|최소:0|최대:100
     *      # midterm(중간 반영비):                 필수|숫자형|최소:0|최대:100
     *      # homework(과제 반영비):                필수|숫자형|최소:0|최대:100
     *      # quiz(쪽지시험 반영비):                필수|숫자형|최소:0|최대:100
     *
     *  지역변수 목록
     *  $validator:                     요청 유효성 검사용 객체
     *  $professor:                     교수회원 정보
     *  $subject:                       강의 정보
     *  $reflections:                   갱신에 사용할 반영비 데이터
     *
     *  반환값
     *  @return \Illuminate\Http\JsonResponse 예외
     *
     * 예외
     *  @throws NotValidatedException
     */
    public function updateAchievementReflections(Request $request) {
        // 01. 요청 유효성 검사
        $validator = Validator::make($request->all(), [
            'subject_id'    => 'required|exists:subjects,id',
            'final'         => 'required|numeric|min:0|max:100',
            'midterm'       => 'required|numeric|min:0|max:100',
            'homework'      => 'required|numeric|min:0|max:100',
            'quiz'          => 'required|numeric|min:0|max:100',
        ]);

        if($validator->fails()) {
            throw new NotValidatedException($validator->errors());
        }

        // 02. 데이터 획득
        $professor  = Professor::findOrFail(session()->get('user')->id);
        $subject    = $professor->isMySubject($request->post('subject_id'));

        // 입력된 반영비 획득
        $reflections = [
            'final'     => $request->post('final'),
            'midterm'   => $request->post('midterm'),
            'homework'  => $request->post('homework'),
            'quiz'      => $request->post('quiz'),
        ];

        // ##### 반영비의 합이 100이 아닐 경우 => 갱신 중단 #####
        if(array_sum($reflections) != 100) {
            throw new NotValidatedException("입력된 반영비의 합이 100이 아닙니다.");
        }
        // 소수점으로 변환
        $reflections = [
            'final'     => number_format($reflections['final'] / 100, 2),
            'midterm'   => number_format($reflections['midterm'] / 100, 2),
            'homework'  => number_format($reflections['homework'] / 100, 2),
            'quiz'      => number_format($reflections['quiz'] / 100, 2),
        ];

        // 03. 갱신
        if($subject->updateReflections($reflections)) {
            return response()->json(new ResponseObject(
                true, "반영비를 갱신하였습니다."
            ), 200);
        } else {
            return response()->json(new ResponseObject(
                false, "반영비 갱신에 실패하였습니다."
            ), 200);
        }
    }
}