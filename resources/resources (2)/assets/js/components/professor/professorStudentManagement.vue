<template>
<div>

  <v-parallax src="/images/lectureManagement.jpg" height="300">
    <h2 class="category1">
      Lecture Management
    </h2>
  </v-parallax>


  <!-- 내용들어갈 영역 -->
    <v-container grid-list-xl>
      <v-layout row wrap align-center>

        <!-- 학생 정보 및 목록 -->
        <v-flex xs12 md12>
          <v-card class="studentListBox">
            <v-card-title>
              <v-flex xs12>
                <v-layout row wrap align-center>
                  <v-flex xs12 md3>
                    <v-dialog v-model="dialog1" persistent max-width="600px">
                      <v-btn depressed round color="green" class="white--text" slot="activator" normal style="position:relative;right:15px;">
                        エクセルパイルをダウンロード
                        <v-icon right dark>cloud_download</v-icon>
                      </v-btn>
                      <!-- 모달창 메인 -->
                       <v-card>
                         <v-card-title
                           class="grey lighten-4 py-4 title"
                         >
                           成績様式ダウンロード
                         </v-card-title>
                         <v-container grid-list-sm class="pa-4">
                           <v-layout row wrap>
                             <v-flex xs12 align-center justify-space-between>
                               <v-layout align-center>
                                 <v-text-field
                                    id="fileName"
                                    name="file_name"
                                    label="パイル名"
                                    v-model="filename"
                                  ></v-text-field>
                               </v-layout>
                             </v-flex>
                             <!-- 달력 -->
                             <v-flex xs12>
                               <v-menu
                                ref="menu"
                                :close-on-content-click="false"
                                v-model="menu"
                                :nudge-right="40"
                                :return-value.sync="date"
                                lazy
                                transition="scale-transition"
                                offset-y
                                full-width
                                min-width="290px"
                              >
                              <v-text-field
                                slot="activator"
                                v-model="date"
                                label="Picker in menu"
                                prepend-icon="event"
                                readonly
                              ></v-text-field>
                              <v-date-picker v-model="date" no-title scrollable>
                               <v-spacer></v-spacer>
                               <v-btn flat color="primary" @click="menu = false">Cancel</v-btn>
                               <v-btn flat color="primary" @click="$refs.menu.save(date)">OK</v-btn>
                             </v-date-picker>
                            </v-menu>
                             </v-flex>
                             <!-- 종목 -->
                             <v-flex xs12>
                               <v-select
                                 :items="types"
                                 v-model="subType"
                                 label="分類"
                                 class="input-group--focused"
                               ></v-select>
                             </v-flex>
                             <v-flex xs12>
                               <v-text-field
                                 id="perfectScore"
                                 name="perfect_score"
                                 label="満点"
                                 v-model="perfectScore"
                               ></v-text-field>
                             </v-flex>
                             <v-flex xs12>
                               <v-text-field
                                 id="content"
                                 name="content"
                                 label="試験の説明"
                                 v-model="content"
                               ></v-text-field>
                             </v-flex>
                             <v-flex xs12>
                               <v-select
                                 :items="fileTypes"
                                 v-model="fileType"
                                 label="拡張子"
                                 class="input-group--focused"
                                 item-value="text"
                               ></v-select>
                             </v-flex>
                           </v-layout>
                         </v-container>
                         <v-card-actions>
                           <v-spacer></v-spacer>
                           <v-btn flat color="primary" @click="dialog1 = false">Cancel</v-btn>
                           <v-btn flat @click="getDownloadFile()">download</v-btn>
                         </v-card-actions>
                       </v-card>
                     </v-dialog>
                    </v-dialog>
                  </v-flex>

                  <v-flex xs12 md3>
                    <v-dialog v-model="dialog2" persistent max-width="600px">
                      <v-btn depressed round color="green" class="white--text" slot="activator" normal>
                        成績のパイルアップロード
                        <v-icon right dark>cloud_upload</v-icon>
                      </v-btn>
                      <!-- 모달창 메인 -->
                      <v-card style="padding: 20px 20px 20px 20px;">
                        <v-card-title>
                          <span class="headline">エクセルで成績アップロード</span>
                        </v-card-title>
                        <div>

                          <!-- form 양식 -->
                          <!-- 파일 등록 -->
                          {{ this.fileName }}
                          <v-btn
                          v-on:click='pickFile'
                          style="width:120px"
                          color="green"
                          > パイルを選択 </v-btn>
                          <input type="file" style="display:none;" id="file" ref='upload_file' required="" accept=".xlsx, .xls, .csv" v-on:change="handleFileUpload()" class="upload_input">
                          <v-btn color = "blue accent-2" v-on:click="submitFile()" class="upload_button">成績アップロード</v-btn>
                        </div>
                        <v-card-actions>
                          <v-spacer></v-spacer>
                          <v-btn color="blue darken-1" flat @click="dialog2=false">Close</v-btn>
                        </v-card-actions>
                      </v-card>
                    </v-dialog>
                    <!-- 완료 메시지 모달창 -->
                    <v-dialog v-model="dialog3" persistent max-width="600px">
                      <!-- 모달창 메인 -->
                      <v-card>
                        <v-card-title>
                          <span class="headline">お知らせ</span>
                        </v-card-title>
                        <v-flex d-flex xs12 sm6 md4>
                          <!-- form 양식 -->
                          <div v-if="reData">
                            成績のアップロードに成功しました。
                          </div>
                          <div v-else>
                            成績のアップロードに失敗しました。
                          </div>
                          <!-- form End-->
                        </v-flex>
                        <v-card-actions>
                          <v-spacer></v-spacer>
                          <v-btn color="blue darken-1" flat @click="dialog3=false, dialog2=false">Close</v-btn>
                        </v-card-actions>
                      </v-card>
                    </v-dialog>
                  </v-flex>

                  <v-flex xs12 md3>
                    <v-btn depressed round color="green" class="white--text" :onclick="checkGradePageUrl" style="position:relative;right:-1px;">
                      登録した成績確認＆修正
                      <v-icon right dark>check</v-icon>
                    </v-btn>
                  </v-flex>
                  <v-flex xs12 md3>
                    <v-text-field append-icon="search" label="名前で探す。" single-line hide-details v-model="search"></v-text-field>
                  </v-flex>
              </v-layout>
            </v-flex>
            </v-card-title>
            <v-data-table
              :headers="headers"
              :items="student_lists"
              :search="search"
              :pagination.sync="pagination"
              hide-actions
            >
              <template slot="items" slot-scope="props">
                 <td class="text-xs-center" style="height: 70px; font-size: 20px;font-family: Gothic A1">{{ props.item.id }}</td>
                 <td class="text-xs-center" style="font-size: 20px;font-family: Gothic A1">{{ props.item.name }}</td>
                 <td class="text-xs-center" style="font-size: 20px;font-family: Gothic A1">
                     <v-btn color = "blue accent-2" style="color:white" slot="activator" normal :onclick="props.item.infoLink">
                       詳しく見る
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
</div>
</template>

<style>
.category1 {
  color: #FFFFFF;
  font-size: 50px;
  position: relative;
  font-family: "Montserrat";
  font-weight: Bold;
  position: relative;
  left: 39px;
}

.studentListBox {
  box-shadow:  0 4px 12px 0 rgba(163, 163, 163, 0.36);
  border-radius: 0.4075rem;
  position: relative;
  bottom: 148px;
}


/* 성적 업로드 부분 css */
.upload_input {
  border: 1px solid black;
  width: 300px;
}

.upload_button {
  font-family: "Gothic A1";
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
  data() {
    return {
      /* 다운로드 */
      filename : null,
      date: null,
      subType : null,
      perfectScore : null,
      content : null,
      fileType: null,
      /* 업로드 */
      file: null,
      fileName : "パイルを選択してください。",
      dialog1: false,
      dialog2: false,
      dialog3: false,
      reData: true,
      e2: null,
      menu: false,
      /* 학생정보 */
      search: '',
      pagination: {
        rowsPerPage: 10
      },
      types: [
        {text:'中間', select : 'midterm' },
        {text:'期末', select : 'final' },
        {text:'課題', select : 'homework' },
        {text:'テスト', select : 'quiz' }
      ],
      fileTypes: [
        {text:'xlsx'},
        {text:'xls'},
        {text:'csv'},
      ],
      headers: [{
          class: 'display-1',
          text: '学生番号',
          value: 'studentNum',
          sortable: true,
          align: 'center'
        },
        {
          class: 'display-1',
          text: '名前',
          sortable: true,
          value: 'name',
          align: 'center'
        },
        {
          class: 'display-1',
          text: '',
          value: 'detailInfo',
          align: 'center'
        }
      ],
      student_lists: [],
      paramsData: this.$router.history.current.query.subjectName,
      /* 성적 확인 페이지 이동 url */
      checkGradePageUrl: null
    }
  },
  computed: {
    paramsData: function(newParams) {
      console.log("success!!!" + this.$router.history.current.query.subjectName);
    }
  },
  methods: {
    getDownloadFile(){
      axios.post('/professor/subject/score/excel/download', {
        subject_id : this.$router.history.current.query.subjectName,
        file_name : this.filename,
        execute_date : this.date,
        score_type : this.subType.select,
        perfect_score : this.perfectScore,
        content : this.content,
        file_type : this.fileType
      },{responseType: 'arraybuffer'}).then((response)=> {
        let result = document.createElement('a');
        let blob = new Blob([response.data], {type: response.headers['content-type']})

        let fileName = this.filename + "." + this.fileType;

        let link = document.createElement('a');
        link.href = window.URL.createObjectURL(blob);
        link.target = '_self';
        link.download = fileName;
        link.click();

        this.dialog1 = false;
      }).catch((error)=>{
        console.log("download Err :"+error);
      })
    },
    getSubjectData() {
      axios.get('/professor/subject/join_list', {
          params: {
            subject_id: this.$router.history.current.query.subjectName
          }
        }).then((response) => {
          /* 같은 페이지의 parameter 가 바뀔 경우 다시 값을 가져와야하기 때문에 저장한다.*/
          this.paramsData = this.$router.history.current.query.subjectName;
          /* 학생 정보를 저장 */
          this.student_lists = response.data.message;
          /* 학생정보페이지 작업 / url 생성 및 연결 */
          for (var start = 0; start < this.student_lists.length; start++) {
            this.$set(this.student_lists[start], 'infoLink', "window.open('/studentManagement/main?getInfoIdType=" + this.student_lists[start].id + "', 'newwindow', 'width=1000,height=700'); return true;");
          }
        })
        .catch((error) => {
          console.log("getSubject Error!! : " + error);
        })
    },
    submitFile() {
      let formData = new FormData();
      formData.append('upload_file', this.file);
      axios.post('/professor/subject/score/excel/upload',
          formData, {
            headers: {
              'Content-Type': 'multipart/form-data'
            }
          }).then((response) => {
          if (response.data.status === true) {
            this.reData = true;
            this.openWindow();
            console.log(response.data);
          } else {
            this.reData = false;
            this.openWindow();
            console.log(response.data)
          }
        })
        .catch((error) => {
          console.log('FAILURE!!' + error);
          this.reData = false;
          this.openWindow();
          console.log(error.data);
        });
    },
    pickFile() {
      this.$refs.upload_file.click()
    },
    handleFileUpload() {
      this.file = this.$refs.upload_file.files[0];
      console.log(this.file);
      this.fileName = this.file.name;
    },
    openWindow() {
      this.dialog3 = true;
    },
    setGradeCheckUrl() {
      this.checkGradePageUrl = "window.open('/professor/gradeCheck?subject_id=" + this.$router.history.current.query.subjectName + "', 'newwindow', 'width=1000,height=700'); return false;"
    }
  },
  mounted() {
    this.getSubjectData();
    /* 성적확인 url 변경 */
    this.setGradeCheckUrl();
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
      }
    }
  },
  computed: {
    pages() {
      if (this.pagination.rowsPerPage == null ||
        this.pagination.totalItems == null
      ) return 0

      return Math.ceil(this.pagination.totalItems / this.pagination.rowsPerPage)
    }
  }
}
</script>
