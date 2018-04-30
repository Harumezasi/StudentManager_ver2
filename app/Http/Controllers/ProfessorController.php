<?php

namespace App\Http\Controllers;

use App\Exceptions\NotValidatedException;
use Illuminate\Http\Request;
use Validator;
use App\Professor;
use App\Student;
use App\Score;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\UploadScoresFormExport;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;

/**
 *  클래스명:               ProfessorController
 *  설명:                   교수 회원에게 제공하는 관련 기능들을 정의하는 클래스
 *  만든이:                 3-WDJ 春目指し 1401213 이승민
 *  만든날:                 2018년 4월 26일
 *
 *  함수 목록
 *      - 메인
 *          = index():                          교수 회원의 메인 페이지를 출력
 *
 *
 *
 *      - 내 정보 관리
 *          = downloadScoreForm
 *
 *          = uploadScore
 *
 *      - 강의 관리
 *
 *      - 지도반 관리
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



    // 내 정보 관리



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
            'prev'      => $periodValue['prev'],
            'this'      => $periodValue['this_format'],
            'next'      => $periodValue['next']
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
     *  매개 변수
     *  @param Request $request :       요청 메시지
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
            'subject_id'        => 'required|numeric'
        ]);

        if($validator->fails()) {
            throw new NotValidatedException($validator->errors());
        }

        // 02. 강의 데이터 조회
        $professor  = Professor::find(session()->get('user')->id);
        $subjects   = $professor->subjects->where('id', $request->get('subject_id'));

        // ##### 클라이언트가 요청한 강의가 해당 교수가 담당하는 강의가 아닐 때 #####
        if(sizeof($subjects) <= 0) {
            return response()->json(new ResponseObject(
                false, "잘못된 강의코드입니다."
            ), 200);
        }

        // 03. 수강학생 목록 조회
        $joinList = $subjects[0]->joinLists()->join('users', 'users.id', 'join_lists.std_id')
            ->get(['users.id', 'users.name', 'users.photo', 'join_lists.subject_id'])->all();

        // 학업 성취도 삽입
        foreach($joinList as $item) {
            $achievement = Student::find($item->id)->selectStatList($item->subject_id)['achievement'];
            $item->achievement = $achievement;
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
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     *
     *  예외
     *  @throws NotValidatedException
     */
    public function downloadScoreForm(Request $request) {
        // 01. 입력값 검증
        $valid_scoreType = implode(',', self::SCORE_TYPE);
        $validator = Validator::make($request->all(), [
            'subject_id'        => 'required|numeric',
            'file_name'         => 'required|string|min:2',
            'execute_date'      => 'required|date',
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
        $subjects   = $professor->subjects()->where('id', $request->post('subject_id'))->get()->all();

        // ##### 내 강의 목록에서 해당 강의가 없을 때 #####
        if(sizeof($subjects) <= 0) {
            return redirect(route('professor.index'))->with('alert', '해당 강의에 대한 접근 권한이 없습니다.');
        }

        $fileName       = $request->post('file_name');
        $executeDate    = $request->post('execute_date');
        $scoreType      = $request->post('score_type');
        $content        = $request->post('content');
        $perfectScore   = $request->post('perfect_score');
        $fileType       = $request->post('file_type');
        $studentList    = $subjects[0]->joinLists()->join('users', 'users.id', 'join_lists.std_id')
                            ->get(['users.id', 'users.name'])->all();

        // 03. 엑셀에 삽입할 데이터 구성
        $data = [
            ['강의 코드', $subjects[0]->id],
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

    public function uploadScores(Request $request) {
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
        $extractData['score']  = NULL;
        if (sizeof($subjects = $professor->subjects()->where('id', $sheetData[1]['B'])->get()->all()) > 0) {
            $extractData['subject_id'] = $sheetData[1]['B'];
        } else {
            return response()->json(new ResponseObject(
                false, "강의 코드가 잘못되었습니다."
            ), 200);
        }

        // 실시 일자
        $extractData['execute_date'] = $sheetData[2]['B'];

        // 분류
        $extractData['score_type'] = $sheetData[3]['B'];

        // 만점
        $extractData['perfect_score'] = $sheetData[4]['B'];

        // 성적 내용
        $extractData['content'] = $sheetData[5]['B'];

        // 04. 유효성 검증 & 데이터 추출
        $valid_scoreType = implode(',', self::SCORE_TYPE);
        $validator = Validator::make($extractData, [
            'subject_id'        => 'required|numeric',
            'execute_date'      => 'required|date',
            'score_type'        => "required|in:{$valid_scoreType}",
            'content'           => 'required|string|min:2',
            'perfect_score'     => 'required|between:1,999',
        ]);

        if($validator->fails()) {
            throw new NotValidatedException($validator->errors());
        }

        // 학생-점수 리스트
        $extractData['std_list'] = [];
        $signUpList = $subjects[0]->joinLists()->get(['std_id'])->pluck('std_id')->all();
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
                        continue;
                    case 'C':
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
        $score->subject_id      = $extractData['subject_ide'];
        $score->execute_date    = $extractData['execute_date'];
        $score->type            = $extractData['score_type'];
        $score->detail          = $extractData['content'];
        $score->perfect_score   = $extractData['perfect_score'];

        // 각 학생별로 취득 점수 등록
        if($score->insertScoreList($score, $extractData['std_list'])) {
            return response()->json(new ResponseObject(
                true, "성적 등록에 성공하였습니다."
            ), 200);
        } else {
            return response()->json(new ResponseObject(
                false, "성적 등록에 실패하였습니다."
            ), 200);
        }
    }



    // 지도반 관리
}