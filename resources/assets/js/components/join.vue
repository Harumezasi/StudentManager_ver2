<template>
  <div class="joinArea">
    <!-- 회원가입 체크 -->
    <div>
      <v-flex xs12>
        <v-container grid-list-xl>
          <v-layout row wrap align-center>
            <v-card class="formArea">
              <v-card-title>
                <h2>新規登録　（ID チェック）</h2>
                <v-spacer></v-spacer>
                <div class="backBtnPositionDiv">
                  <v-btn block outline color="red" v-on:click="pageOut()">戻る</v-btn>
                </div>
              </v-card-title>
              <!-- 학번 or 아이디 입력 영역 -->
              <v-card-actions>

                <div>
                  <v-radio-group v-model="join_Type" class="radioDiv">
                    <v-radio label="学生" value="student" v-if="!joinCheck"></v-radio>
                    <v-radio label="学生" value="student" v-else disabled></v-radio>
                    <v-radio label="教授" value="professor" v-if="!joinCheck"></v-radio>
                    <v-radio label="教授" value="professor" v-else disabled></v-radio>
                  </v-radio-group>
                </div>

                <div>
                  <v-text-field solo-inverted name="id" :label="message" type="text" v-model="id_Number" class="inputArea" v-if="!joinCheck"></v-text-field>
                  <v-text-field solo-inverted name="id" :label="message" type="text" v-model="id_Number" class="inputArea" v-else disabled></v-text-field>
                </div>

                <v-btn block outline color="green" v-on:click="checkJoin()" v-if="!joinCheck">ID チェック</v-btn>
                <v-btn block outline color="green" v-on:click="checkJoin()" v-else disabled>ID チェック</v-btn>

              </v-card-actions>
            </v-card>
          </v-layout row wrap align-center>
        </v-container grid-list-xl>
      </v-flex xs12>
    </div>

    <!-- 회원가입 정보 입력  -->
    <div v-if="joinCheck">
      <v-flex xs12>
        <v-container grid-list-xl>
          <v-layout row wrap align-center>
            <v-card class="formArea">
              <v-card-title>
                <h2>会員情報入力</h2>
              </v-card-title>
              <!-- 학번 or 아이디 입력 영역 -->
              <v-card-actions>
                <div class="formAreaDiv">
                    <div class="labelArea"> </div>
                    <img :src="photoUrl" height="150" width="150" v-if="photoUrl"/>
                  <br>
                    <div class="labelArea"> 写真 </div>
                    <v-text-field solo-inverted label="Select Image" @click='pickFile' v-model='photoName' class="inputArea" prepend-icon='attach_file'></v-text-field>
          					<input
          						type="file"
          						style="display: none"
          						ref="image"
          						accept="image/*"
          						@change="onFilePicked"
          					>
                  <br><br>
                    <div class="labelArea"> 名前 </div>
                    <v-text-field solo-inverted type="text" v-model="name" class="inputArea" v-if="!professorCheck" disabled></v-text-field>
                    <v-text-field solo-inverted type="text" v-model="name" class="inputArea" v-else></v-text-field>
                  <br><br>
                    <div class="labelArea"> ID </div>
                    <v-text-field solo-inverted type="text" v-model="id" class="inputArea"></v-text-field>
                  <br><br>
                    <div class="labelArea"> PW </div>
                    <v-text-field solo-inverted type="password" v-model="pw" class="inputArea"></v-text-field>
                  <br><br>
                    <div class="labelArea"> PW 確認 </div>
                    <v-text-field solo-inverted type="password" v-model="pw_check" class="inputArea"></v-text-field>
                  <br><br>
                    <div class="labelArea"> メール </div>
                    <v-text-field solo-inverted type="text" v-model="mail" class="inputArea"></v-text-field>
                  <br><br>
                    <div class="labelArea"> 電話番号 </div>
                    <v-text-field solo-inverted type="text" v-model="phone" class="inputArea"></v-text-field>
                  <br><br>
                  <div v-if="professorCheck">
                      <div class="labelArea"> 研究室 </div>
                      <v-text-field solo-inverted type="text" v-model="office" class="inputArea"></v-text-field>
                    <br><br>
                  </div>
                  <div class="btnSizeDiv">
                    <v-btn block outline color="blue" v-on:click="reset()">リセット</v-btn>
                  </div>
                  <div class="btnSizeDiv">
                    <v-btn block outline color="red" v-on:click="join()">会員登録</v-btn>
                  </div>
                </div>


              </v-card-actions>
            </v-card>
          </v-layout row wrap align-center>
        </v-container grid-list-xl>
      </v-flex xs12>
    </div>
  </div>
</template>

<script>
    export default {
        data () {
            return {
              /* 회원가입 체크 */
              joinCheck : false,
              id_check : 0,
              /* 입력 메세지 (학생, 교수)*/
              message : null,
              /* 체크 데이터 */
              id_Number : null,
              join_Type : null,

              professorCheck : false,

              /* 회원등록 정보 */
              name : null,
              id : null,
              pw : null,
              pw_check : null,
              phone : null,
              mail : null,
              office : null,

              /* 이미지 */
              photoUrl  : null,
              photoName : null,
              photoFile : null
            }
        },
        mounted() {
          this.join_Type = 'student';
        },
        methods: {
          /* 회원등록 여부 확인 */
          checkJoin(){

            if(this.id_Number == null)
              alert('情報を入力してください。')
            else

            axios.get('/join/check', {
              params : {
                id : this.id_Number,
                type : this.join_Type
              }
            }).then((response) => {
              console.log(response);

              if(response.data.status){
                this.joinCheck = true;
                this.id_check = 1;
                if(this.join_Type == 'professor'){
                  /* 기본 아이디를 추가 */
                  this.id = this.id_Number;
                  this.professorCheck = true;
                }else{
                  /* 이름을 추가 */
                  this.name = response.data.message;
                  this.professorCheck = false;
                }
              }else{
                this.joinCheck = false;
                this.professorCheck = false;
              }

              alert(response.data.message)

            }).catch((error) => {
              alert('入力した情報をもう一度確認してください。')
              console.log('joinCheck Err :' + error);
            })
          },
          /* 회원등록 요청 */
          join(){
            /* 입력한 비밀번호가 같은지 확인 */
            if(this.pw != this.pw_check){

              alert('パスワードが間違っています、もう一度確認してください。')

            }else if(this.pw == this.pw_check){
              let formData = new FormData();
              console.log(this.joinCheck);
              formData.append('type', this.join_Type)
              formData.append('id', this.id)
              formData.append('id_check', this.id_check)
              formData.append('password', this.pw)
              formData.append('password_check', this.pw_check)
              formData.append('name', this.name)
              formData.append('email', this.mail)
              formData.append('phone', this.phone)

              /* 교수 여부 확인 = 연구실 위치 추가 */
              if(this.professorCheck)
                formData.append('office', this.office)

              /* 이미지 등록 여부 확인 = 이미지 추가 */
              if(this.photoFile != null)
                formData.append('photo', this.photoFile)

              /* 회원가입 요청 시작 */
              axios.post('/join', formData)
              .then((response) => {

                if(response.data.status){
                  alert(response.data.message);
                  this.$router.push('/');
                }

              }).catch((error) => {
                console.log('joinErr :' + error);
              })
            }
          },
          pageOut(){
            this.$router.push('/');
          },
          /* 이미지 등록 함수들 */
          pickFile () {
            this.$refs.image.click()
          },
      		onFilePicked (e) {
      			const files = e.target.files
      			if(files[0] !== undefined) {
      				this.photoName = files[0].name
      				if(this.photoName.lastIndexOf('.') <= 0) {
      					return
      				}
      				const fr = new FileReader ()
      				fr.readAsDataURL(files[0])
      				fr.addEventListener('load', () => {
      					this.photoUrl = fr.result
      					this.photoFile = files[0]
      				})
      			} else {
      				this.photoName = ''
      				this.photoFile = ''
      				this.photoUrl = ''
      			}
      		},
          /* 초기화 */
          reset(){
            this.joinCheck = false
            this.professorCheck = false
            this.name = null
            this.id = null
            this.pw = null
            this.pw_check = null
            this.phone = null
            this.mail = null
            this.office = null
            this.photoUrl  = null
            this.photoName = null
            this.photoFile = null
            this.id_check = 0
          }
        },
        watch : {
          join_Type : function(){
            if(this.join_Type == 'student'){
              this.message = "学生番号を入力してください。"
            }else if(this.join_Type == 'professor'){
              this.message = "指定した ID を入力してください。"
            }
          }
        }

    }
</script>

<style>
.inputArea {
  width : 30em;
  display: inline-block;
}

.labelArea {
  width : 80px;
  display: inline-block;
}

.formArea {
  width : 650px;
}

.formAreaDiv {
  padding-left : 50px;
  padding-bottom : 25px;
}

.backBtnPositionDiv {
  height: 30px;
  width: 150px;
}

.btnSizeDiv {
  padding: 10px;
  float: left;
  width: 250px;
}

.joinArea {
  min-width: 40%;
  min-height: 30%;

  position: absolute;
  left : 30%;
}
.radioDiv {
  width: 100px;
}

</style>
