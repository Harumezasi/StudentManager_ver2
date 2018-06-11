<template>

  <div>
    <!-- 상단 이미지 -->
      <div class="panel-header">
        <div class="header text-center">
          <v-layout class = "imgTitle" column align-center justify-center>
            <h2 class="title">
              강의 관리 페이지
            </h2>
          </v-layout>
        </div>
      </div>

      <!-- 내용들어갈 영역 -->
      <v-flex xs12>
        <v-container grid-list-xl>
          <v-layout row wrap align-center>

            <div class="headline text-xs-center" style="width:100%"> <h2> 수강자 정보</h2></div>
            <!-- 학생 정보 및 목록 -->
            <v-flex xs12 md12>

                      <v-card class = "elevation-0">
                        <v-card-title>
                          <v-text-field
                            append-icon="search"
                            label="Search"
                            single-line
                            hide-details
                            v-model="search"
                          ></v-text-field>
                        </v-card-title>
                        <v-data-table
                         :headers="headers"
                         :items="student_lists"
                         :search="search"
                         :pagination.sync="pagination"
                         >
                       <template slot="items" slot-scope="props">
                         <td class="text-xs-center">{{ props.item.id }}</td>
                         <td class="text-xs-center">{{ props.item.name }}</td>
                         <td class="text-xs-center">{{ props.item.achievement}}</td>
                         <td class="text-xs-center">
                             <v-btn color="light-green" slot="activator" normal :onclick="props.item.infoLink">
                               상세보기
                             </v-btn>
                         </td>
                       </template>
                      </v-data-table>
                       <div class="text-xs-center pt-2">
                         <v-pagination v-model="pagination.page" :length="pages"></v-pagination>
                       </div>
                        <v-alert slot="no-results" :value="true" color="error" icon="warning">
                          Your search for "{{ search }}" found no results.
                        </v-alert>
                      </v-card>
              </v-flex>

          </v-layout>
        </v-container>
      </v-flex>

      <div class="contents">
        <v-layout column class="my-5" align-center>
          <v-flex xs12 sm4 class="my-3">
            <div class="text-xs-center">
              <h2 class="headline"> 성적 등록 및 확인 </h2>
            </div>
          </v-flex>

          <v-container grid-list-xl>
            <v-layout row wrap align-center>
              <v-flex xs12 md7>
                <v-card class="elevation-0 transparent">
                  <!-- 엑셀 파일 -->
                  <v-card-text class="text-xs-center">
                    <img src = "../images/excel.png" style="width:70px; height:auto;"/>
                  </v-card-text>
                  <!-- 엑셀 파일 설명 -->
                  <v-card-title primary-title class="layout justify-center">
                    <div class="headline text-xs-center">엑셀 양식 다운로드 후, 파일 업로드</div>
                  </v-card-title>
                  <!-- 다운로드 버튼 -->
                  <v-dialog v-model="dialog1" persistent max-width="600px">
                    <v-btn color="green darken-4" class="white--text" style="height: 100px;" slot="activator" normal>
                      엑셀 양식 다운로드
                      <v-icon right dark>cloud_download</v-icon>
                    </v-btn>
                    <!-- 모달창 메인 -->
                    <v-card>
                      <v-card-title>
                        <span class="headline">엑셀 양식 다운로드</span>
                      </v-card-title>
                      <v-flex d-flex xs12 sm6 md4>
                        <!-- form 양식 -->
                        <v-form action='/professor/subject/score/excel/download' method='post'>
                          <input type="hidden" :value="paramsData" name="subject_id">
                          <table>
                            <tr>
                              <td>
                                <!-- 파일 이름 입력 -->
                                <v-chip style="width:80px" color="secondary" text-color="white">파일 이름</v-chip>
                              </td>
                              <td>
                                <v-text-field style="float:left" type="text" name="file_name" maxlength="30" required=""></v-text-field>
                              </td>
                            </tr>
                            <tr>
                              <!-- 실시일자 선택 -->
                              <td>
                                <v-chip color="secondary" text-color="white">실시 일자</v-chip>
                              </td>
                              <td>
                                <input type="date" name="execute_date" required="">
                              </td>
                            </tr>
                            <tr>
                              <td>
                                <!-- 성적 유형 -->
                                <v-chip style="width:80px" color="secondary" text-color="white"> 분 류 </v-chip>
                              </td>
                              <td>
                                <select name="score_type" id="score_type">
                                  <option value="midterm">중간</option>
                                  <option value="final">기말</option>
                                  <option value="homework" selected="">과제</option>
                                  <option value="quiz">쪽지</option>
                                </select>
                              </td>
                            </tr>
                            <tr>
                              <td>
                                <!-- 만점 설정 -->
                                <v-chip style="width:80px" color="secondary" text-color="white"> 만 점 </v-chip>
                              </td>
                              <td>
                                <v-text-field type="number" name="perfect_score" min="1" max="999" maxlength="3" required=""></v-text-field>
                              </td>
                            </tr>
                            <tr>
                              <td>
                                <!-- 성적 상세 내용 -->
                                <v-chip style="width:80px" color="secondary" text-color="white">성적 내용</v-chip>
                              </td>
                              <td>
                                <v-text-field type="text" name="content" minlength="2" maxlength="30" required=""></v-text-field>
                              </td>
                            </tr>
                            <tr>
                              <td>
                                <!-- 출력 파일 유형 -->
                                <v-chip style="width:80px" color="secondary" text-color="white"> 확 장 자 </v-chip>
                              </td>
                              <td>
                                <select name="file_type" id="file_type">
                                  <option value="xlsx">xlsx</option>
                                  <option value="xls">xls</option>
                                  <option value="csv">csv</option>
                                </select>
                              </td>
                            </tr>
                            <tr>
                              <td colspan="2">
                                <!-- SUBMIT 실행 버튼 영역 -->
                                <v-btn color="blue" type='submit'>양식 다운로드</v-btn>
                              </td>
                            </tr>
                          </table>
                          <!-- END -->
                        </v-form>
                        <!-- form End-->
                      </v-flex>
                      <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn color="blue darken-1" flat @click.native="dialog1 = false">Close</v-btn>
                      </v-card-actions>
                    </v-card>
                  </v-dialog>
                  <!-- 업로드 버튼 -->
                  <v-dialog v-model="dialog2" persistent max-width="600px">
                    <v-btn color="green darken-4" class="white--text" style="height: 100px;" slot="activator" normal>
                      엑셀 파일 업로드
                      <v-icon right dark>cloud_upload</v-icon>
                    </v-btn>
                    <!-- 모달창 메인 -->
                    <v-card>
                      <v-card-title>
                        <span class="headline">엑셀로 성적 업로드</span>
                      </v-card-title>
                      <div>
                        <!-- form 양식 -->
                        <!-- 파일 등록 -->
                        <v-chip color="secondary" text-color="white">파일등록</v-chip>
                        <input type="file" id="file" ref='upload_file' required="" accept=".xlsx, .xls, .csv" v-on:change="handleFileUpload()" class="upload_input">
                        <button v-on:click="submitFile()" class="upload_button">성적 업로드</button>
                      </div>
                      <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn color="blue darken-1" flat @click="dialog2=false">Close</v-btn>
                      </v-card-actions>
                    </v-card>
                  </v-dialog>
                  <!-- 버튼 영역 끝 -->
                </v-card>
              </v-flex>
              <!-- 완료 메시지 모달창 -->
              <v-dialog v-model="dialog3" persistent max-width="600px">
                <!-- 모달창 메인 -->
                <v-card>
                  <v-card-title>
                    <span class="headline">알림</span>
                  </v-card-title>
                  <v-flex d-flex xs12 sm6 md4>
                    <!-- form 양식 -->
                    <div v-if="reData">
                      성적 업로드에 성공하였습니다.
                    </div>
                    <div v-else>
                      성적 업로드에 실패하였습니다.
                    </div>
                    <!-- form End-->
                  </v-flex>
                  <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="blue darken-1" flat @click="dialog3=false, dialog2=false">Close</v-btn>
                  </v-card-actions>
                </v-card>
              </v-dialog>
              <!-- 등록된 성적 확인 -->
              <v-flex xs12 md3>
                <v-card class="elevation-0 transparent">
                  <v-card-text class="text-xs-center">
                    <img src = "../images/registration.png" style="width:70px; height:auto;"/>
                  </v-card-text>
                  <v-card-title primary-title class="layout justify-center">
                    <div class="headline text-xs-center">업로드 성적 확인</div>
                  </v-card-title>
                  <v-btn outline large color="primary" class="white--text" style="height: 100px;" :onclick="checkGradePageUrl">
                    등록된 성적 확인
                    <v-icon right dark>check</v-icon>
                  </v-btn>
                </v-card>
              </v-flex>
              <!-- End -->
            </v-layout>
          </v-container>
          </v-flex>
        </v-layout>
        </v-container>
      </div>
  </div>
</template>

<style>
  .panel-header {
    height: 200px;
    padding-top: 80px;
    padding-bottom: 45px;
    background: #141E30;
    /* fallback for old browsers */
    background: -webkit-gradient(linear, left top, right top, from(#0c2646), color-stop(60%, #204065), to(#2a5788));
    background: linear-gradient(to right, #0c2646 0%, #204065 60%, #2a5788 100%);
    position: relative;
    overflow: hidden;
  }
  .panel-header .header .title {
    color: #FFFFFF;
  }
  .panel-header .header .category {
    max-width: 600px;
    color: rgba(255, 255, 255, 0.5);
    margin: 0 auto;
    font-size: 13px;
  }
  .panel-header .header .category a {
    color: #FFFFFF;
   }

  .panel-header-sm {
    height: 135px;
  }

  .panel-header-lg {
    height: 380px;
  }
  /* 성적 업로드 부분 css */
  .upload_input {
    border: 1px solid black;
    width : 300px;
  }

  .upload_button {
    border: 1px solid black;
    width: 100px;
    height: 50px;
    border-radius: 10px;
    background-color: gray;
    font-weight: bold;
    font-size: 15px;
  }

  .contents {
    text-align: center;
  }

  .attendanceCheckTitleEng {
    color: white;
    font-family: inherit;
  }

  .attendanceCheckTitleJap {
    color: rgb(0, 0, 0);
    font-size: 20px;
    font-family: MS Gothic;
  }

  .custom-loader {
    animation: loader 1s infinite;
    display: flex;
  }
</style>

<script>
export default {
  data () {
    return {
      /* 업로드 */
      file: null,
      dialog1: false,
      dialog2: false,
      dialog3: false,
      reData : true,
      /* 학생정보 */
      search: '',
      pagination: {
        /* 테이블에 표시될 데이터 수, 기본 값*/
        rowsPerPage: 10
      },
      headers: [
        {
          text: '학번',
          value: 'studentNum',
          sortable: true,
          align: 'center'
        },
        {
          text: '이름',
          sortable: true,
          value: 'name',
          align: 'center'
        },
        {
         text: '학업성취도',
         value: 'gradePersent',
         sortable: true,
         align: 'center'
       },
       {
         text: '',
         value: 'detailInfo',
         align: 'center'
       }
     ],
     student_lists: [],
     paramsData : this.$router.history.current.query.subjectName,
     /* 성적 반영 비율 */
     reflection : [],
     /* 성적 확인 페이지 이동 url */
     checkGradePageUrl : null
    }
  },
  computed: {
   paramsData : function (newParams){
     console.log("success!!!" + this.$router.history.current.query.subjectName);
   }
  },
  methods : {
    getSubjectData() {
      axios.get('/professor/subject/join_list', {
        params : {
            subject_id : this.$router.history.current.query.subjectName
        }
      }).then((response)=>{
        /* 같은 페이지의 parameter 가 바뀔 경우 다시 값을 가져와야하기 때문에 저장한다.*/
        this.paramsData = this.$router.history.current.query.subjectName;
        /* 학생 정보를 저장 */
        this.student_lists = response.data.message;
        /* 학생정보페이지 작업 / url 생성 및 연결 */
        for(var start = 0; start < this.student_lists.length; start++){
            this.$set(this.student_lists[start], 'infoLink', "window.open('/studentManagement/main?getInfoIdType="+
            this.student_lists[start].id +"', 'newwindow', 'width=1000,height=700'); return false;");
        }
      })
      .catch((error)=>{
        console.log("getSubject Error!! : " + error );
      })
    },
    submitFile(){
        let formData = new FormData();
        formData.append('upload_file', this.file);
        axios.post( '/professor/subject/score/excel/upload',
            formData,
            {
                headers: {
                    'Content-Type': 'multipart/form-data'
                }
            }).then((response)=>{
            if(response.data.status === true) {
                this.reData = true;
                this.openWindow();
                console.log(response.data);
            } else {
                this.reData = false;
                this.openWindow();
                console.log(response.data)
            }})
            .catch((error)=>{
                console.log('FAILURE!!' + error);
                this.reData = false;
                this.openWindow();
                console.log(error.data);
            });
    },
    handleFileUpload(){
        this.file = this.$refs.upload_file.files[0];
    },
    openWindow(){
        this.dialog3 = true;
    },
    setGradeCheckUrl(){
      this.checkGradePageUrl = "window.open('/professor/gradeCheck?subject_id="+ this.$router.history.current.query.subjectName +"', 'newwindow', 'width=1000,height=700'); return false;"
    },
    getReflection(){
      axios.get('/professor/subject/manage/reflection_select',{
        params : {
          subject_id : this.$router.history.current.query.subjectName
        }
      }).then((response)=>{
        this.reflection = response.data.message;
      }).catch((error)=>{
        console.log("getRef Error :"  + error);
      })
    },
    setReflection(){
      /* 모든 값의 합이 100인지 확인 */
      let refSum =
      parseInt(this.reflection.midterm) +
      parseInt(this.reflection.final) +
      parseInt(this.reflection.homework) +
      parseInt(this.reflection.quiz);

      if(refSum == 100){
        axios.post('/professor/subject/manage/reflection_update', {
          subject_id : this.$router.history.current.query.subjectName,
          midterm : this.reflection.midterm,
          final : this.reflection.final,
          homework : this.reflection.homework,
          quiz : this.reflection.quiz
        }).then((response)=>{
            /* 성공알림출력 */
            alert('저장완료');
            /* 설정 값 새로 불러오기 */
            this.getReflection();
            /* 설정 모달창 닫기 */
            this.setModal = false;
        }).catch((error)=>{
          console.log('setRef Error :' + error);
          alert('입력 값을 확인해주세요.');
        })
      }else{
        /* 경고 */
        alert('반영비율의 합은 [ 100 ]이어야 합니다.');
      }
    }
  },
  mounted(){
    /* 학생들의 성적 데이터 받아오기 */
    this.getSubjectData();
    /* 성적확인 url 변경 */
    this.setGradeCheckUrl();
    /* 성적 반영 비율 값 받아오기 */
    this.getReflection();
  },
  watch: {
        '$route' (to, from) {
          if (this.paramsData != this.$router.history.current.query.subjectName) {
            /* 학생 정보를 초기화 */
            this.student_lists = [];
            /* 정보를 다시 받아온다, 함수 실행 */
            this.getSubjectData();
            /* 성적확인 url 변경 */
            this.setGradeCheckUrl();
            /* 성적 반영 비율 값 받아오기 */
            this.getReflection();
          }
        }
  },
  computed: {
    pages () {
      if (this.pagination.rowsPerPage == null ||
        this.pagination.totalItems == null
      ) return 0

      return Math.ceil(this.pagination.totalItems / this.pagination.rowsPerPage)
    }
  }
}
</script>
