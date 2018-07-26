<?php

namespace App\Http\Controllers;

use App\Exceptions\NotValidatedException;
use App\Exports\ExportArrayToExcel;
use App\Http\Controllers\ResponseObject;
use App\JoinList;
use App\Professor;
use App\Rules\NotExists;
use App\Student;
use App\StudyClass;
use App\Subject;
use App\Timetable;
use App\User;
use http\Env\Response;
use Illuminate\Http\Request;
use App\Schedule;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Validator;

/**
 *  클래스명:               AdminController
 *  설명:                   관리자에게 제공하는 관련 기능들을 정의하는 클래스
 *  만든이:                 3-WDJ 春目指し 1401213 이승민
 *  만든날:                 2018년 4월 26일
 *
 *  함수 목록
 *      - 메인
 *          = index():                          관리자의 메인 페이지를 출력
 *
 *      - 학생관리
 *          = selectStudentsList():             학생 목록 조회
 *          = selectStudentInfo():              학생별 상세 정보 조회
 *          = insertStudent():                  form 요청을 이용한 학생 정보 등록
 *          = getStudentRegisterExcel():
 */
class AdminController extends Controller
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

    // 회원 관리
    // 학생 관리

    // 학생 목록 조회
    public function selectStudentsList(Request $request)
    {
        // 01. 요청 유효성 검사
        $validator = Validator::make($request->all(), [
            'class_id'      => 'exists:study_classes,id'
        ]);

        if($validator->fails()) {
            throw new NotValidatedException($validator->errors());
        }

        // 02. 데이터 획득
        $classId    = $request->exists('class_id') ? $request->get('class_id') : null;
        $studyClass = is_null($classId) ? null : StudyClass::findOrFail($classId);

        $studentList = is_null($studyClass) ? Student::all()->all() : $studyClass->students->all();

        // 03. 정보 획득
        $result = array();
        foreach($studentList as $student) {
            $stdInfo = $student->user->selectUserInfo();
            $result[] = new class($student->id, $student->study_class, $stdInfo->study_class, $stdInfo->name, $stdInfo->photo_url) {
                public $id, $class_id, $class_name, $name, $photo_url;

                public function __construct($argId, $argClassId, $argClassName, $argName, $argPhotoUrl)
                {
                    $this->id           = $argId;
                    $this->class_id     = $argClassId;
                    $this->class_name   = $argClassName;
                    $this->name         = $argName;
                    $this->photo_url    = $argPhotoUrl;
                }
            };
        }

        return response()->json(new ResponseObject(
            true, $result
        ), 200);
    }

    // 학생별 상세정보 조회
    public function selectStudentInfo(Request $request)
    {
        // 01. 요청 유효성 검사
        $validator = Validator::make($request->all(), [
            'std_id'    => 'required|exists:students,id'
        ]);

        if($validator->fails()) {
            throw new NotValidatedException();
        }

        // 02. 사용자 정보 획득
        $student = Student::findOrFail($request->get('std_id'));

        return response()->json(new ResponseObject(
            true, $student->user->selectUserInfo()
        ), 200);
    }

    // form 요청을 이용한 학생 등록
    public function insertStudent(Request $request)
    {
        // 01. 요청 유효성 검사
        $validator = Validator::make($request->all(), [
            'student_list'                  => 'required|array',
            'student_list.*.id'             => ['required', 'regex:#\d{7}#', new NotExists('users', 'id')],
            'student_list.*.name'           => 'required|string|min:2',
            'student_list.*.class_id'       => 'required|exists:study_classes,id'
        ]);

        if ($validator->fails()) {
            throw new NotValidatedException($validator->errors());
        }

        // 02. 데이터 획득
        $studentList = $request->post('student_list');

        foreach ($studentList as $student) {
            if (!Student::insertInfo($student['id'], $student['name'], $student['class_id'])) {
                return response()->json(new ResponseObject(
                    false, __('response.insert_failed', ['element' => __('interface.student')])
                ), 200);
            }
        }

        return response()->json(new ResponseObject(
            true, __('response.insert_success', ['element' => __('interface.student')])
        ), 200);
    }

    // 내 지도반 학생 등록을 위한 엑셀 양식 출력
    public function getStudentRegisterExcel(Request $request)
    {
        // 01. 요청 유효성 검사
        $validator = Validator::make($request->all(), [
            'file_name' => 'required|string|min:2',
            'file_type' => 'required|string|in:xlsx,xls,csv',
        ]);

        if ($validator->fails()) {
            throw new NotValidatedException($validator->errors());
        }

        // 02. 데이터 획득
        $fileType = $request->post('file_type');
        $fileName = $request->post('file_name');
        $data = [
            [__('interface.class_id'), __('interface.std_id'), __('interface.name'), null, null, __('interface.class_list'), null, null],
            [null, null, null, null, null, __('interface.class_id'), __('interface.name'), __('interface.tutor_name')]
        ];


        // 03. 지도반 목록 획득
        $classList  = StudyClass::all()->all();
        $counter    = 2;
        foreach($classList as $class) {
            if(!isset($data[$counter])) {
                $data[$counter] = [null, null, null, null, null, null, null, null];
            }

            $data[$counter][5]  = $class->id;
            $data[$counter][6]  = $class->name;
            $data[$counter][7]  = User::findOrFail($class->tutor)->name;
        }

        // 03. 확장자 지정
        $fileType = $this->getType($fileType);

        // 04. 엑셀 파일 생성
        return Excel::download(new ExportArrayToExcel($data), $fileName . '.' . $fileType, $fileType);
    }

    // 엑셀 파일을 통한 내 지도반 학생 등록
    public function importStudentRegisterExcel(Request $request)
    {
        // 01. 요청 유효성 검사
        $validator = Validator::make($request->all(), [
            'upload_file' => 'required|file|mimes:xlsx,xls,csv'
        ]);

        if ($validator->fails()) {
            throw new NotValidatedException($validator->errors());
        }

        // 02. 데이터 획득
        $file = $request->file('upload_file');
        $fileType = ($array = explode('.', $file->getClientOriginalName()))[sizeof($array) - 1];

        // 03. 전송받은 파일 해석
        $reader = IOFactory::createReader($this->getType($fileType));
        $reader->setReadDataOnly(true);
        $sheetData = $reader->load($file->path())->getActiveSheet()->toArray(
            null, true, true, true
        );

        // 04. 엑셀 데이터 획득
        $extractData = array();
        $extractData['class_id'] = $sheetData[1]['B'];

        // 학생 리스트 추출
        $studentList = array_slice($sheetData, 2);
        $studentList = array_filter($studentList, function ($value) {
            return !(is_null($value['A']) || is_null($value['B']) || is_null($value['C']));
        });

        $validator = Validator::make(['std_list' => $studentList], [
            'std_list.*.A' => 'required|exists:study_classes,id',
            'std_list.*.B' => ['required', 'regex:#\d{6,7}#', new NotExists('users', 'id')],
            'std_list.*.C' => 'required|string|min:2'
        ]);

        if ($validator->fails()) {
            throw new NotValidatedException($validator->errors());
        }

        // 05. 학생 등록
        foreach ($studentList as $student) {
            if (!Student::insertfo(sprintf('%07d', $student['B']), $student['C'], $student['A'])) {
                return response()->json(new ResponseObject(
                    false, __('response.insert_failed', ['element' => __('interface.student')])
                ), 200);
            }
        }

        return response()->json(new ResponseObject(
            true, __('response.insert_success', ['element' => __('interface.student')])
        ), 200);
    }

    // 학생 수정
    public function updateStudent(Request $request) {
        // 01. 요청 유효성 검증
        $validator = Validator::make($request->all(), [
            'std_id'        => 'required|exists:students,id',
            'name'          => 'required|string|min:2',
            'phone'         => 'required',
            'email'         => 'required|email',
            'class_id'      => 'required|exists:study_classes,id'
        ]);

        if($validator->fails()) {
            throw new NotValidatedException($validator->errors());
        }

        // 02. 데이터 획득
        $student    = Student::findOrFail($request->post('std_id'));
        $studyClass = StudyClass::findOrFail($request->post('class_id'));

        // 03. 데이터 갱신
        if($student->updateMyInfo([
            'name'          => $request->post('name'),
            'phone'         => $request->post('phone'),
            'email'         => $request->post('email'),
            'study_class'   => $studyClass->id
        ])) {
            return response()->json(new ResponseObject(
                true, __('response.update_success', ['element' => __('interface.student')])
            ), 200);
        } else {
            return response()->json(new ResponseObject(
                true, __('response.update_failed', ['element' => __('interface.student')])
            ), 200);
        }
    }


    // 학생 삭제
    public function deleteStudent(Request $request) {
        // 01. 요청 메시지 검증
        $validator = Validator::make($request->all(), [
            'student_list'      => 'required|array',
            'student_list.*'    => 'required|exists:students,id'
        ]);

        if($validator->fails()) {
            throw new NotValidatedException($validator->errors());
        }

        // 02. 데이터 획득
        $studentList = $request->post('student_list');
        foreach($studentList as $student) {
            if(!Student::findOrFail($student)->delete()) {
                return response()->json(new ResponseObject(
                    false, __('response.delete_failed', ['element' => __('interface.student')])
                ), 200);
            }
        }

        return response()->json(new ResponseObject(
            true, __('response.delete_success', ['element' => __('interface.student')])
        ), 200);
    }


    // 교수 회원 관리

    // 교수 목록 획득
    public function selectProfessorsList()
    {
        // 01. 데이터 획득
        $result = array();
        $professors = Professor::all()->all();

        foreach ($professors as $professor) {
            $pInfo = $professor->user->selectUserInfo();

            $result[] = new class($pInfo->id, $pInfo->name, $pInfo->photo_url)
            {
                public $id, $name, $photo_url;

                public function __construct($argId, $argName, $argPhotoUrl)
                {
                    $this->id = $argId;
                    $this->name = $argName;
                    $this->photo_url = $argPhotoUrl;
                }
            };
        }

        return response()->json(new ResponseObject(
            true, $result
        ), 200);
    }

    // 해당 교수에 대한 상세 정보 획득
    public function selectProfessorInfo(Request $request) {
        // 01. 요청 메시지 유효성 검사
        $validator = Validator::make($request->all(), [
            'prof_id'       => 'required|exists:professors,id'
        ]);

        if($validator->fails()) {
            throw new NotValidatedException($validator->errors());
        }

        // 02. 데이터 획득
        $professor  = Professor::findOrFail($request->get('prof_id'));
        $profInfo   = $professor->user->selectUserInfo();

        return response()->json(new ResponseObject(
            true, $profInfo
        ), 200);
    }

    // 교수 정보 수정
    public function updateProfessor(Request $request) {
        // 01. 요청 메시지 유효성 검사
        $validator = Validator::make($request->all(), [
            'prof_id'   => 'required|exists:professors,id',
            'name'      => 'required|string|min:2',
            'phone'     => 'required',
            'email'     => 'required|email',
            'office'    => 'required|string|min:2',
        ]);

        if($validator->fails()) {
            throw new NotValidatedException($validator->errors());
        }

        // 02. 데이터 획득
        $professor = Professor::findOrFail($request->post('prof_id'));

        // 03. 데이터 갱신
        if($professor->updateMyInfo([
            'name'      => $request->post('name'),
            'phone'     => $request->post('phone'),
            'email'     => $request->post('email'),
            'office'    => $request->post('office')
        ])) {
            return response()->json(new ResponseObject(
                true, __('response.update_success', ['element' => __('interface.professor')])
            ), 200);
        } else {
            return response()->json(new ResponseObject(
                true, __('response.update_failed', ['element' => __('interface.professor')])
            ), 200);
        }
    }

    // 교수 삭제
    public function deleteProfessor(Request $request) {
        // 01. 요청 메시지 유효성 검사
        $validator = Validator::make($request->all(), [
            'prof_id'   => 'required|exists:professors,id'
        ]);

        if($validator->fails()) {
            throw new NotValidatedException($validator->errors());
        }

        // 02. 데이터 획득
        $professor = Professor::findOrFail($request->post('prof_id'));

        if($professor->delete()) {
            return response()->json(new ResponseObject(
                true, __('response.delete_success', ['element' => __('interface.professor')])
            ), 200);
        } else {
            return response()->json(new ResponseObject(
                true, __('response.delete_failed', ['element' => __('interface.professor')])
            ), 200);
        }
    }


    // 지도반 관리
    // 지도반 목록 조회
    public function selectClassesList() {
        // 01. 데이터 획득
        $classList = StudyClass::all()->all();

        $result = array();
        foreach($classList as $class) {
            $tutorName = $class->professor->user->name;

            $result[] = new class($class->id, $class->name, $tutorName) {
                public $id, $name, $tutor;

                public function __construct($argId, $argName, $argTutor)
                {
                    $this->id       = $argId;
                    $this->name     = $argName;
                    $this->tutor    = $argTutor;
                }
            };
        }

        return response()->json(new ResponseObject(
            true, $result
        ), 200);
    }

    // 등록
    public function insertClass(Request $request) {
        // 01. 요청 유효성 검사
        $validProfList = implode(',', Professor::isTutor(false)->pluck('id')->all());

        $validator = Validator::make($request->all(), [
            'tutor_id'      => "required|in:{$validProfList}",
            'class_name'    => 'required:string:min:2'
        ]);

        if($validator->fails()) {
            throw new NotValidatedException($validator->errors());
        }

        // 02. 데이터 획득
        $tutor      = Professor::findOrFail($request->post('tutor_id'));
        $className  = $request->post('class_name');

        // 03. 학반 등록
        if($tutor->studyClass()->create(['name' => $className])) {
            return response()->json(new ResponseObject(
                true, __('response.insert_success', ['element' => __('interface.class')])
            ), 200);

        } else {
            return response()->json(new ResponseObject(
                true, __('response.insert_failed', ['element' => __('interface.class')])
            ), 200);

        }
    }

    // 삭제
    public function deleteClass(Request $request) {
        // 01. 요청 메시지 유효성 검사
        $validator = Validator::make($request->all(), [
            'class_id'  => 'required|exists:study_classes,id'
        ]);

        if($validator->fails()) {
            throw new NotValidatedException($validator->errors());
        }

        // 02. 데이터 획득
        $class = StudyClass::findOrFail($request->post('class_id'));

        // 03. 데이터 삭제
        if($class->delete()) {
            return response()->json(new ResponseObject(
                true, __('response.delete_success', ['element' => __('interface.class')])
            ), 200);
        } else {
            return response()->json(new ResponseObject(
                true, __('response.delete_failed', ['element' => __('interface.class')])
            ), 200);
        }
    }


    // 지도반 수강강의 관리
    // 지도반별 강의목록 조회
    public function selectSubjects(Request $request) {
        // 01. 요청 메시지 유효성 검증
        $validator = Validator::make($request->all(), [
            'term'      => ['required', 'regex:/(19|20)\d{2}-((1st|2nd)_term|(summer|winter)_vacation)/'],
            'class_id'  => 'required|exists:study_classes,id',
        ]);

        if($validator->fails()) {
            throw new NotValidatedException($validator->errors());
        }

        // 02. 데이터 획득
        $studyClass = StudyClass::findOrFail($request->get('class_id'));
        $termValue  = explode('-', $this->getTermValue($request->post('term'))['this']);
        $year       = $termValue[0];
        $term       = $termValue[1];

        $subjects   = Subject::where(['join_class' => $studyClass->id, 'year' => $year, 'term' => $term]);

        if($subjects->exists()) {
            return response()->json(new ResponseObject(
                true, $subjects->get()->all()
            ), 200);
        } else {
            return response()->json(new ResponseObject(
                false, __('response.select_failed', ['element' => __('study.subject')])
            ), 200);
        }
    }

    // 강의 등록
    // 자체 인터페이스를 이용한 강의 등록
    public function insertSubjects(Request $request)
    {
        // 01. 요청 메시지 유효성 검즘
        $validator = Validator::make($request->all(), [
            'term'                      => ['required', 'exists:/(19|20)\d{2}-((1st|2nd)_term|(summer|winter)_vacation)/'],
            'subject_list'              => 'required|array',
            'subject_list.*.join_class' => 'required|in:study_classes,id',
            'subject_list.*.professor'  => 'required|exists:professors,id',
            'subject_list.*.name'       => 'required|string|min:2',
            'subject_list.*.type'       => 'required|in:japanese,major',
        ]);

        if ($validator->fails()) {
            throw new NotValidatedException($validator->errors());
        }

        // 02. 데이터 획득
        $subjectList = $request->post('subject_list');
        $termValue = explode('-', $this->getTermValue($request->post('term'))['this']);
        $year = $request->exists('year') ? $request->post('year') : $termValue[0];
        $term = $request->exists('term') ? $request->post('term') : $termValue[1];

        // 03. 데이터 등록
        foreach ($subjectList as $subject) {
            $model = new Subject();

            $model->year = $year;
            $model->term = $term;
            $model->join_class = $subject['join_class'];
            $model->professor = $subject['professor'];
            $model->name = $subject['name'];
            $model->type = $subject['type'];

            $model->save();
        }

        // 04. 결과 반환
        return response()->json(new ResponseObject(
            true, __('response.insert_success', ['element' => __('study.subject')])
        ), 200);
    }

    // 강의 등록을 위한 엑셀 양식 출력
    public function getSubjectRegisterExcel(Request $request)
    {
        // 01. 요청 유효성 검증
        $validator = Validator::make($request->all(), [
            'term'      => ["required|", "exists:/(19|20)\d{2}-((1st|2nd)_term|(summer|winter)_vacation)/"],
            'file_name' => 'required|string|min:2',
            'file_type' => 'required|in:xlsx,xls,csv'
        ]);

        if ($validator->fails()) {
            throw new NotValidatedException($validator->errors());
        }

        // 02. 데이터 설정
        $fileType = $request->post('file_type');
        $fileName = $request->post('file_name');
        $termValue = explode('-', $this->getTermValue($request->post('term'))['this']);
        $year = $termValue[0];
        $term = $termValue[1];
        $data = [
            [__('interface.year'), $year, __('interface.term'), $term, null,
                null, null, null, null, null],


            [__('interface.type_explain'), null, null, null, null,
                null, __('interface.prof_list'), null, __('interface.class_list'), null],

            [__('interface.class_id'), __('interface.prof_id'), __('interface.name'), __('interface.type'), null,
                null, __('interface.prof_id'), __('interface.name'), __('interface.class_id'), __('interface.class_name')],
        ];

        // 교수 목록 & 지도반 목록 추가 추가
        $profList   = Professor::all();
        $classList  = StudyClass::all();
        for($iCount = 0; $iCount < max($profList->count(), $classList->count()); $iCount++) {
            $counter = $iCount + 3;
            if (!isset($data[$counter])) {
                $data[$counter] = [null, null, null, null, null, null, null, null, null];
            }

            // 교수 정보 추가
            if(isset($profList[$iCount])) {
                $professor  = $profList[$iCount];
                $profInfo   = $professor->user->selectUserInfo();
                $data[$counter][6] = $professor->id;
                $data[$counter][7] = $profInfo->name;
            }

            // 반 정보 추가
            if(isset($classList[$iCount])) {
                $class = $classList[$iCount];

                $data[$counter][8] = $class->id;
                $data[$counter][9] = $class->name;
            }
        }

        // 03. 확장자 지정
        $fileType = $this->getType($fileType);

        // 05. 엑셀 파일 생성
        return Excel::download(new ExportArrayToExcel($data), $fileName . '.' . $fileType, $fileType);

    }

    // 엑셀 파일을 이용한 강의 등록
    public function importSubjects(Request $request)
    {
        // 01. 요청 유효성 검사
        $validator = Validator::make($request->all(), [
            'upload_file' => 'required|file|mimes:xlsx,xls,csv'
        ]);

        if ($validator->fails()) {
            throw new NotValidatedException($validator->errors());
        }

        // 02. 데이터 획득
        $file = $request->file('upload_file');
        $fileType = ($array = explode('.', $file->getClientOriginalName()))[sizeof($array) - 1];

        // 03. 전송받은 파일 해석
        $reader = IOFactory::createReader($this->getType($fileType));
        $reader->setReadDataOnly(true);
        $sheetData = $reader->load($file->path())->getActiveSheet()->toArray(
            null, true, true, true
        );

        // 04. 엑셀 데이터 획득
        // 등록 학기 추출
        $year = $sheetData[1]['B'];
        $term = $sheetData[1]['D'];

        // 강의 리스트 추출
        $subjectList = array_slice($sheetData, 3);
        $subjectList = array_filter($subjectList, function ($value) {
            return !(is_null($value['A']) || is_null($value['B']) || is_null($value['C'] || is_null($value['D'])));
        });


        // 유효성 검사
        $validTermList = implode(',', self::TERM_TYPE);
        $validator = Validator::make(['subject_list' => $subjectList, 'year' => $year, 'term' => $term], [
            'year'              => ['required_with:term', 'regex:/(19|20)\d{2}/'],
            'term'              => "required_with:year|in:{$validTermList}",
            'subject_list.*.A'  => 'required|exists:study_classes,id',
            'subject_list.*.B'  => 'required|exists:professors,id',
            'subject_list.*.C'  => 'required|string|min:2',
            'subject_list.*.D'  => 'required|in:major,japanese'
        ]);

        if ($validator->fails()) {
            throw new NotValidatedException($validator->errors());
        }


        // 05. 강의 등록
        foreach ($subjectList as $subject) {
            $model = new Subject();

            $model->year = $year;
            $model->term = $term;
            $model->join_class = $subject['A'];
            $model->professor = $subject['B'];
            $model->name = $subject['C'];
            $model->type = $subject['D'];

            if (!$model->save()) {
                return response()->json(new ResponseObject(
                    false, __('response.insert_failed', ['element' => __('study.subject')])
                ), 200);
            }
        }

        return response()->json(new ResponseObject(
            true, __('response.insert_success', ['element' => __('study.subject')])
        ), 200);
    }

    // 강의 수정
    public function updateSubject(Request $request) {
        // 01. 요청 유효성 검사
        $validator = Validator::make($request->all(), [
            'subject_id'    => 'required|exists:subjects,id',
            'class_id'      => 'required|exists:study_classes,id',
            'prof_id'       => 'required|exists:professors,id',
            'name'          => 'required|string|min:2',
            'type'          => 'required|in:major,japanese'
        ]);

        if($validator->fails()) {
            throw new NotValidatedException($validator->errors());
        }

        // 02. 데이터 획득
        $subject = Subject::findOrFail($request->post('subject_id'));

        // 03. 데이터 수정
        if($subject->update([
            'join_class'    => $request->post('class_id'),
            'professor'     => $request->post('prof_id'),
            'name'          => $request->post('name'),
            'type'          => $request->post('type')
        ])) {
            return response()->json(new ResponseObject(
                true, __('response.update_success', ['element' => __('study.subject')])
            ), 200);
        } else {
            return response()->json(new ResponseObject(
                false, __('response.update_failed', ['element' => __('study.subject')])
            ), 200);
        }
    }


    // 강의 삭제
    public function deleteSubject(Request $request) {
        // 01. 요청 메시지 유효성 검사
        $validator = Validator::make($request->all(), [
            'subject_id'    => 'required|exists:subjects,id'
        ]);

        if($validator->fails()) {
            throw new NotValidatedException($validator->errors());
        }

        // 02. 데이터 획득
        $subject = Subject::findOrFail($request->post('subject_id'));

        // 03. 데이터 제거
        if($subject->delete()) {
            return response()->json(new ResponseObject(
                true, __('response.delete_success', ['element' => __('study.subject')])
            ), 200);
        } else {
            return response()->json(new ResponseObject(
                false, __('response.delete_failed', ['element' => __('study.subject')])
            ), 200);
        }
    }


    // 학생별 수강강의 목록 관리
    // 수강 목록 조회
    public function selectJoinList(Request $request) {
        // 01. 요청 유효성 검사
        $validator = Validator::make($request->all(), [
            'subject_id'    => 'required|exists:subjects,id'
        ]);

        if($validator->fails()) {
            throw new NotValidatedException($validator->errors());
        }

        // 02. 데이터 획득
        $subject = Subject::findOrFail($request->get('subject_id'));
        $joinLists = $subject->joinLists;

        foreach($joinLists as $key => $value) {
            $stdInfo = Student::findOrFail($value->std_id)->user->selectUserInfo();

            $joinLists[$key]->name      = $stdInfo->name;
            $joinLists[$key]->photo_url = $stdInfo->photo_url;
        }

        return response()->json(new ResponseObject(
            true, $joinLists
        ), 200);
    }

    // 수강생 등록
    public function insertJoinList(Request $request)
    {
        // 01. 요청 유효성 검사
        $validator = Validator::make($request->all(), [
            'subject_id'        => "required|exists:subjects,id",
            'student_list'      => 'required|array',
            'student_list.*'    => "required|exists:students,id"
        ]);

        if ($validator->fails()) {
            throw new NotValidatedException($validator->errors());
        }

        // 02. 데이터 획득
        $studentList = $request->post('student_list');
        $subject = Subject::findOrFail($request->post('subject_id'));

        // 해당 학생들의 소속 반과 해당 강의가 개설된 반이 동일한지 검사
        foreach($studentList as $stdId) {
            $student = Student::findOrFail($stdId);

            if($student->study_class != $subject->join_class) {
                throw new NotValidatedException(__('response.wrong_join'));
            }
        }

        // 반 추가
        foreach($studentList as $stdId) {
            $subject->joinLists()->create(['std_id' => $stdId]);
        }

        // 03. 결과 반환
        return response()->json(new ResponseObject(
            true, __('response.insert_success', ['element' => __('interface.join_list')])
        ), 200);
    }

    // 수강생 삭제
    public function deleteJoinList(Request $request)
    {
        // 01. 요청 메시지 유효성 검사
        $validator = Validator::make($request->all(), [
            'join_list'     => 'required|array',
            'join_list.*'   => 'required|exists:join_lists,id'
        ]);

        if($validator->fails()) {
            throw new NotValidatedException($validator->errors());
        }

        // 02. 데이터 획득
        $joinLists = $request->post('join_list');

        foreach($joinLists as $joinListId) {
            $joinList = JoinList::findOrFail($joinListId);

            if(!$joinList->delete()) {
                return response()->json(new ResponseObject(
                    false, __('response.delete_failed', ['element' => __('interface.join_list')])
                ), 200);
            }
        }

        return response()->json(new ResponseObject(
            true, __('response.delete_success', ['element' => __('interface.join_list')])
        ), 200);
    }

    // 내 지도반 시간표 관리
    // 시간표 조회
    public function selectTimetable(Request $request) {
        // 01. 요청 메시지 유효성 검사
        $validator = Validator::make($request->all(), [
            'class_id'  => 'required|exists:study_classes,id',
            'term'      => ['regex:/(19|20)\d{2}-((1st|2nd)_term|(summer|winter)_vacation)/']
        ]);

        if($validator->fails()) {
            throw new NotValidatedException($validator->errors());
        }

        // 02. 데이터 획득
        $studyClass = StudyClass::findOrFail($request->get('class_id'));
        $termValue  = $this->getTermValue($request->exists('term') ? $request->get('term') : null);
        $timeTables = $studyClass->selectTimetables($termValue['this'])->get()->all();

        $paginate = [
            'prev'  => $termValue['prev'],
            'this'  => $termValue['this_format'],
            'next'  => $termValue['next']
        ];

        return response()->json(new ResponseObject(
            true, ['timetables' => $timeTables, 'pagination' => $paginate]
        ), 200);
    }

    // 자체 인터페이스를 이용한 시간표 등록
    public function insertTimetables(Request $request) {
        // 01. 요청 메시지 유효성 검사
        $validator = Validator::make($request->all(), [
            'term'                      => ['required', "regex:/(19|20)\d{2}-((1st|2nd)_term|(summer|winter)_vacation)/"],
            'timetable'                 => 'required|array',
            'timetable.*'               => 'required|array',
            'timetable.*.subject_id'    => 'required|exists:subjects,id',
            'timetable.*.day_of_week'   => 'required|numeric|min:1|max:5',
            'timetable.*.period'        => 'required|numeric|min:1|max:9',
            'timetable.*.classroom'     => 'required|string|min:2'
        ]);

        if($validator->fails()) {
            throw new NotValidatedException($validator->errors());
        }

        // 02. 데이터 획득
        $termValue = $request->post('term');
        $timetableList = $request->post('timetable');

        if(Timetable::insert($timetableList, $termValue)) {
            return response()->json(new ResponseObject(
                true, __('response.insert_success', ['element' => __('interface.timetable')])
            ), 200);
        } else {
            return response()->json(new ResponseObject(
                true, __('response.insert_false', ['element' => __('interface.timetable')])
            ), 200);
        }
    }

    // 시간표 등록을 위한 엑셀 양식 받기
    public function getTimetableRegisterExcel(Request $request) {
        // 01. 요청 메시지 유효성 검증
        $validator = Validator::make($request->all(), [
            'class_id'  => 'required|exists:study_classes,id',
            'term'      => ["required", "regex:/(19|20)\d{2}-((1st|2nd)_term|(summer|winter)_vacation)/"],
            'file_name' => 'required|string|min:2',
            'file_type' => 'required|in:xlsx,xls,csv'
        ]);

        if($validator->fails()) {
            throw new NotValidatedException($validator->errors());
        }

        // 02. 데이터 획득
        $studyClass = StudyClass::findOrFail($request->post('class_id'));
        $term       = $request->post('term');
        $fileName   = $request->post('file_name');
        $fileType   = $request->post('file_type');

        $data = [
            [__('interface.term'), $term, __('interface.class_id'), $studyClass->id, null,
                null, null, null, null, __('interface.subject_list'), null],
            [null, null, __('interface.monday'), __('interface.tuesday'), __('interface.wednesday'),
                __('interface.thursday'), __('interface.friday'), null, null, __('interface.sub_num'), __('interface.name')],

            [__('interface.period', ['period' => 1]), __('interface.sub_num'), null, null, null,
                null, null, null, null, null, null],
            ["09:00~09:50", __('interface.classroom'), null, null, null,
                null, null, null, null, null, null],

            [__('interface.period', ['period' => 2]), __('interface.sub_num'), null, null, null,
                null, null, null, null, null, null],
            ["10:00~10:50", __('interface.classroom'), null, null, null,
                null, null, null, null, null, null],

            [__('interface.period', ['period' => 3]), __('interface.sub_num'), null, null, null,
                null, null, null, null, null, null],
            ["11:00~11:50", __('interface.classroom'), null, null, null,
                null, null, null, null, null, null],

            [__('interface.period', ['period' => 4]), __('interface.sub_num'), null, null, null,
                null, null, null, null, null, null],
            ["12:00~12:50", __('interface.classroom'), null, null, null,
                null, null, null, null, null, null],

            [__('interface.period', ['period' => 5]), __('interface.sub_num'), null, null, null,
                null, null, null, null, null, null],
            ["13:00~13:50", __('interface.classroom'), null, null, null,
                null, null, null, null, null, null],

            [__('interface.period', ['period' => 6]), __('interface.sub_num'), null, null, null,
                null, null, null, null, null, null],
            ["14:00~14:50", __('interface.classroom'), null, null, null,
                null, null, null, null, null, null],

            [__('interface.period', ['period' => 7]), __('interface.sub_num'), null, null, null,
                null, null, null, null, null, null],
            ["15:00~15:50", __('interface.classroom'), null, null, null,
                null, null, null, null, null, null],

            [__('interface.period', ['period' => 8]), __('interface.sub_num'), null, null, null,
                null, null, null, null, null, null],
            ["16:00~16:50", __('interface.classroom'), null, null, null,
                null, null, null, null, null, null],

            [__('interface.period', ['period' => 9]), __('interface.sub_num'), null, null, null,
                null, null, null, null, null, null],
            ["17:00~17:50", __('interface.classroom'), null, null, null,
                null, null, null, null, null, null],
        ];

        // 03. 강의 목록
        $subjectList    = $studyClass->subjects()->term($term)->get()->all();
        $counter        = 1;
        foreach($subjectList as $subject) {
            if(!isset($data[$counter])) {
                $data[$counter] = [null, null, null, null, null, null, null, null, null, null, null];
            }

            $data[$counter][9]  = $subject->id;
            $data[$counter][10]  = $subject->name;

            $counter++;
        }

        // 03. 확장자 지정
        $fileType = $this->getType($fileType);

        // 05. 엑셀 파일 생성
        return Excel::download(new ExportArrayToExcel($data), $fileName . '.' . $fileType, $fileType);
    }

    // 엑셀 양식을 이용해 시간표 등록
    public function importTimetablesRegister(Request $request) {
        // 01. 요청 유효성 검사
        $validator = Validator::make($request->all(), [
            'upload_file' => 'required|file|mimes:xlsx,xls,csv'
        ]);

        if ($validator->fails()) {
            throw new NotValidatedException($validator->errors());
        }

        // 02. 데이터 획득
        $file = $request->file('upload_file');
        $fileType = ($array = explode('.', $file->getClientOriginalName()))[sizeof($array) - 1];

        // 03. 전송받은 파일 해석
        $reader = IOFactory::createReader($this->getType($fileType));
        $reader->setReadDataOnly(true);
        $sheetData = $reader->load($file->path())->getActiveSheet()->toArray(
            null, true, true, true
        );

        // 04. 엑셀 데이터 획득
        // 등록 데이터 추출
        $term       = $sheetData[1]['B'];

        // 시간표 리스트 추출
        $timetableList = array_slice($sheetData, 2);

        // 05. 시간표 등록용 데이터 가공
        $data = array();
        for ($period = 0; $period < 9; $period++) {
            foreach(['C', 'D', 'E', 'F', 'G'] as $dayOfWeek) {
                if(is_null($timetableList[$period * 2][$dayOfWeek]) || is_null($timetableList[$period * 2 + 1][$dayOfWeek])) {
                    continue;
                }

                // 요일 코드 획득
                $dayCode = null;
                switch($dayOfWeek) {
                    case 'C':
                        $dayCode = 1;
                        break;
                    case 'D':
                        $dayCode = 2;
                        break;
                    case 'E':
                        $dayCode = 3;
                        break;
                    case 'F':
                        $dayCode = 4;
                        break;
                    case 'G':
                        $dayCode = 5;
                        break;
                }

                // 중복 강의 검정
                $subjectIds = explode(',', $timetableList[$period * 2][$dayOfWeek]);
                $classrooms = explode(',', $timetableList[$period * 2 + 1][$dayOfWeek]);

                for($sCount = 0; $sCount < sizeof($subjectIds); $sCount++) {
                    $data[] = [
                        'subject_id'    => $subjectIds[$sCount],
                        'period'        => $period + 1,
                        'day_of_week'   => $dayCode,
                        'classroom'     => $classrooms[$sCount]
                    ];
                }
            }
        }

        if(Timetable::insert($data, $term)) {
            return response()->json(new ResponseObject(
                true, __('response.insert_success', ['element' => __('interface.timetable')])
            ), 200);

        } else {
            return response()->json(new ResponseObject(
                false, __('response.insert_failed', ['element' => __('interface.timetable')])
            ), 200);
        }
    }



    // 일정 관리
    // 공통일정 조회
    public function selectCommonSchedule(Request $request)
    {
        // 01. 요청 메시지 유효성 검사
        $validator = Validator::make($request->all(), [
            'start_date'    => 'required|date',
            'end_date'      => 'required|date|after_or_equal:start_date'
        ]);

        if($validator->fails()) {
            throw new NotValidatedException($validator->errors());
        }

        // 02. 데이터 획득
        $startDate  = Carbon::parse($request->post('start_date'));
        $endDate    = Carbon::parse($request->post('end_date'));
        $schedules  = Schedule::selectBetweenDate($startDate->format("Y-m-d"), $endDate->format('Y-m-d'))
            ->whereNull('class_id')->orderBy('start_date')->get()->all();

        return response()->json(new ResponseObject(
            true, $schedules
        ), 200);
    }

    // 공통일정 추가

    /**
     *  함수명:                         insertCommonSchedule
     *  함수 설명:                      모든 지도반의 공통 일정을 등록
     *  만든날:                         2018년 6월 11일
     *
     *  매개변수 목록
     *  @param Request $request :        요청 메시지
     *
     *  지역변수 목록
     *
     *  반환값
     *  @return                          \Illuminate\Http\JsonResponse
     *
     *  예외
     *  @throws                          NotValidatedException
     */
    public function insertCommonSchedule(Request $request)
    {
        // 01. 요청 유효성 검증
        $validator = Validator::make($request->all(), [
            'start_date'        => 'required|date',
            'end_date'          => 'required|date|after_or_equal:start_date',
            'name'              => 'required|string|min:2',
            'holiday_flag'      => 'required|boolean',
            'include_flag'      => 'required_if:holiday_flag,0,false|boolean',
            'in_default_flag'   => 'required_if:holiday_flag,0,false|boolean',
            'out_default_flag'  => 'required_if:holiday_flag,0,false|boolean',
//            'sign_in_time'      => "required_if:holiday_flag,0,false|date_format:H:i:s",
//            'sign_out_time'     => "required_if:holiday_flag,0,false|date_format:H:i:s|after_or_equal:sign_in_time",
            'contents'          => 'string'
        ]);

        // 휴일 플래그가 false 이고 등교시각 기본값 플래그가 false 일 때 -> 관리자가 직접 등교시간을 지정
        $validator->sometimes('sign_in_time', 'required_if:in_default_flag,0,false|date_format:H:i:s', function($input) {
            return !$input->holiday_flag;
        });

        // 휴일 플래그가 false 이고 하교시각 기본값 플래그가 false 일 때 -> 관리자가 직접 하교 시간을 지정
        $validator->sometimes('sign_out_time', 'required_if:out_default_flag,0,false|date_format:H:i:s', function($input) {
            return !$input->holiday_flag;
        });


        if($validator->fails()) {
            throw new NotValidatedException($validator->errors());
        }

        // 02. 데이터 획득
        $startDate      = Carbon::parse($request->post('start_date'));
        $endDate        = Carbon::parse($request->post('end_date'));
        $name           = $request->post('name');
        $holidayFlag    = $request->post('holiday_flag');
        $includeFlag    = true;
        $signInTime     = null;
        $signOutTime    = null;
        $contents       = $request->has('contents') ? $request->post('contents') : '';

        // 시간 데이터 획득
        if(!$holidayFlag) {
            // 등교 시각
            if(!$request->post('in_default_flag')) {
                $signInTime = Carbon::parse($request->post("sign_in_time"));
            }

            // 하교 시각
            if(!$request->post('out_default_flag')) {
                $signOutTime = Carbon::parse($request->post('sign_out_time'));
            }

            $includeFlag = $request->post('include_flag');
        }

        // 지정된 기간동안 이미 정의된 일정이 있을 경우 => 삽입 거부
        if(Schedule::selectBetweenDate($startDate->format("Y-m-d"), $endDate->format('Y-m-d'))->common()->exists()) {
            throw new NotValidatedException(__('response.schedule_already_exists'));
        }

        // 03. 스케쥴 등록
        $setData = [
            'start_date'        => $startDate->format('Y-m-d'),
            'end_date'          => $endDate->format('Y-m-d'),
            'name'              => $name,
            'type'              => Schedule::TYPE['common'],
            'class_id'          => NULL,
            'holiday_flag'      => $holidayFlag,
            'include_flag'      => $includeFlag,
            'sign_in_time'      => is_null($signInTime) ? $signInTime : $signInTime->format('H:i:s'),
            'sign_out_time'     => is_null($signOutTime) ? $signOutTime : $signOutTime->format('H:i:s'),
            'contents'          => $contents
        ];

        if(Schedule::insert($setData)) {
            // 일정 등록 성공 => 성공 메시지 반환
            return response()->json(new ResponseObject(
                true, __('response.insert_success', ['element' => __('ada.schedule_common')])
            ), 200);
        } else {
            return response()->json(new ResponseObject(
                false, __('response_.insert_failed', ['element' => __('ada.schedule_common')])
            ), 200);
        }
    }

    // 공통일정 수정
    public function updateCommonSchedule(Request $request)
    {
        // 01. 요청 유효성 검증
        $validator = Validator::make($request->all(), [
            'id'                => 'required|exists:schedules,id',
            'start_date'        => 'required|date',
            'end_date'          => 'required|date|after_or_equal:start_date',
            'name'              => 'required|string|min:2',
            'holiday_flag'      => 'required|boolean',
            'include_flag'      => 'required_if:holiday_flag,0,false|boolean',
            'in_default_flag'   => 'required_if:holiday_flag,0,false|boolean',
            'out_default_flag'   => 'required_if:holiday_flag,0,false|boolean',
//            'sign_in_time'      => "required_if:holiday_flag,0,false|date_format:H:i:s",
//            'sign_out_time'     => "required_if:holiday_flag,0,false|date_format:H:i:s|after_or_equal:sign_in_time",
            'contents'          => 'string'
        ]);

        // 휴일 플래그가 false 이고 등교시각 기본값 플래그가 false 일 때 -> 관리자가 직접 등교시간을 지정
        $validator->sometimes('sign_in_time', 'required_if:in_default_flag,0,false|date_format:H:i:s', function($input) {
            return !$input->holiday_flag;
        });

        // 휴일 플래그가 false 이고 하교시각 기본값 플래그가 false 일 때 -> 관리자가 직접 하교 시간을 지정
        $validator->sometimes('sign_out_time', 'required_if:out_default_flag,0,false|date_format:H:i:s', function($input) {
            return !$input->holiday_flag;
        });

        if($validator->fails()) {
            throw new NotValidatedException($validator->errors());
        }

        // 02. 데이터 획득
        $schedule       = Schedule::findOrFail($request->post('id'));
        if(!$schedule->typeCheck(Schedule::TYPE['common'])) {
            // 일정 유형이 공통이 아닌 경우 => 데이터에 대한 접근 거부
            throw new NotValidatedException(__('response.no_authority', ['contents' => __('interface.schedule')]));
        }

        $startDate      = Carbon::parse($request->post('start_date'));
        $endDate        = Carbon::parse($request->post('end_date'));
        $name           = $request->post('name');
        $holidayFlag    = $request->post('holiday_flag');
        $includeFlag    = true;
        $signInTime     = null;
        $signOutTime    = null;
        $contents       = $request->has('contents') ? $request->post('contents') : '';

        // 지정된 기간동안 이미 정의된 일정이 있을 경우 => 수정 거부
        if(Schedule::selectBetweenDate($startDate->format("Y-m-d"), $endDate->format('Y-m-d'))->
        common()->where('id', '!=', $schedule->id)->exists()) {
            throw new NotValidatedException(__('response.schedule_already_exists'));
        }

        // 시간 데이터 획득
        if(!$holidayFlag) {
            // 등교 시각
            if(!$request->post('in_default_flag')) {
                $signInTime = Carbon::parse($request->post("sign_in_time"));
            }

            // 하교 시각
            if(!$request->post('out_default_flag')) {
                $signOutTime = Carbon::parse($request->post('sign_out_time'));
            }

            $includeFlag = $request->post('include_flag');
        }


        // 03. 일정 갱신
        $setData = [
            'start_date'        => $startDate->format('Y-m-d'),
            'end_date'          => $endDate->format('Y-m-d'),
            'name'              => $name,
            'holiday_flag'      => $holidayFlag,
            'include_flag'      => $includeFlag,
            'sign_in_time'      => is_null($signInTime) ? $signInTime : $signInTime->format('H:i:s'),
            'sign_out_time'     => is_null($signOutTime) ? $signOutTime : $signOutTime->format('H:i:s'),
            'contents'          => $contents
        ];

        if($schedule->update($setData)) {
            // 갱신 성공
            return response()->json(new ResponseObject(
                true, __('response.update_success', ['element' => __('ada.schedule_common')])
            ), 200);
        } else {
            // 갱신 실패
            return response()->json(new ResponseObject(
                false, __('response.update_failed', ['element' => __('ada.schedule_common')])
            ), 200);
        }
    }

    // 공통일정 삭제
    public function deleteCommonSchedule(Request $request)
    {
        // 01. 요청 유효성 검증
        $validator = Validator::make($request->all(), [
            'id'        => 'required|exists:schedules,id'
        ]);

        if($validator->fails()) {
            throw new NotValidatedException($validator->errors());
        }

        // 02. 데이터 획득
        $schedule = Schedule::findOrFail($request->post('id'));
        if(!$schedule->typeCheck(Schedule::TYPE['common'])) {
            throw new NotValidatedException(__('response.no_authority', ['contents' => __('interface.data')]));
        }

        // 03. 일정 삭제
        if($schedule->delete()) {
            // 갱신 성공
            return response()->json(new ResponseObject(
                true, __('response.delete_success', ['element' => __('ada.schedule_common')])
            ), 200);
        } else {
            // 갱신 실패
            return response()->json(new ResponseObject(
                false, __('response.delete_failed', ['element' => __('ada.schedule_common')])
            ), 200);
        }
    }
}
