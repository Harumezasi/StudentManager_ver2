<template>
  <div class="tutorInformation">

    <v-container grid-list-xl>
      <v-flex xs7>
        <v-card
        class = "profileTitleBox"
        color = "light-blue darken-4"
        style = "box-shadow:  0 4px 12px 0 rgba(24, 111, 244, 0.36)"
        >
          <v-card-text style="padding-bottom: 5px;">
            <h1 style="color: white">会員情報</h1>
            <p style="font-size:20px">情報確認＆修正</p>
          </v-card-text>
        </v-card>
      </v-flex>
    </v-container>

    <v-container grid-list-xl>
     <v-flex xs8>
       <v-card class="profileBox elevation-4" color = "white" style="min-width:370px">
       <v-container grid-list-xl>
         <v-layout row wrap align-center>
           <div class="userImageArea">
             <v-flex xs3 text-xs-center>
               <!-- 프로필 사진 -->
               <v-card-text class = "profilePic text-xs-center">
                <v-avatar size = "125px">
                  <img
                  v-if="photoUrl"
                  class = "img-circle elevation-3"
                  :src = "photoUrl"
                  >
                  <img
                  v-else
                  class = "img-circle elevation-3"
                  :src = "userInfoDatas.photo"
                  >
                </v-avatar>
              </v-card-text>
              <!-- 프로필 사진 업데이트 버튼 -->
              <v-card-text class = "uploadBtn text-xs-center">
                <!-- 보여지는 버튼 -->

                <v-btn
                v-if="photoUrl"
                v-on:click='imageDataReset'
                style="width:120px"
                color="red"
                > リセット </v-btn>

                <v-btn
                v-else
                v-on:click='pickFile'
                style="width:120px"
                color="blue"
                > 写真変更 </v-btn>


                <!-- 이미지 -->
                <input
                  type="file"
                  id="file"
                  ref="myFiles"
                  style="display:none;"
                  v-on:change="handleFileUpload"
                  accept="image/*"
                />
              </v-card-text>
             </v-flex>
           </div>
           <div class="userInfoDataArea">
           <!-- 회원정보 -->
           <v-flex xs9 text-xs-center>
             <v-card-text class = "text-xs-left">
               <v-form v-model="valid">

                 <!-- 이름 -->
                 <v-text-field
                   v-model="userInfoDatas.name"
                   label="Name"
                   id="fontSetting"
                   required
                 ></v-text-field>

                 <!-- 이메일 -->
                 <v-text-field
                   v-model="userInfoDatas.email"
                   label="E-mail"
                   id="fontSetting"
                   required
                 ></v-text-field>

                 <!-- 연락처 -->
                 <v-text-field
                   :mask="phoneType"
                   label="Phone"
                   id="fontSetting"
                   v-model="phone"
                   required
                 ></v-text-field>

                 <!-- 연구실 위치 -->
                 <v-text-field
                   v-model="userInfoDatas.office"
                   label="Office"
                   id="fontSetting"
                   required
                 ></v-text-field>

                 <!-- 비밀번호 -->
                 <v-text-field
                   v-model="userPassword"
                   label="Password"
                   type="password"
                   id="fontSetting"
                   required
                 ></v-text-field>

                 <!-- 비밀번호 확인 -->
                 <v-text-field
                   v-model="userPasswordCheck"
                   label="Password Check"
                   type="password"
                   id="fontSetting"
                   required
                 ></v-text-field>

               </v-form>
             </v-card-text>
             <v-card-text class = "updateBtn text-xs-right">
               <v-btn color = "light-blue darken-4" dark v-on:click="setUserInfo()" class="fontSetting">会員情報セーブ</v-btn>
             </v-card-text>
           </v-flex>
         </div>
         </v-layout>
       </v-container>
       </v-card>
    </v-flex>
  </v-container>

  </div>
</template>

<style>

.fontSetting {
  font-size: 25px;
  font-style: 'Gothic A1';
}

#fontSetting {
  font-size: 20px;
  font-style: 'Gothic A1';
}

.tutorInformation {
  position: relative;
  left: 180px;
}
.profileTitleBox {
  border-radius: 0.4975rem;
  position: relative;
  z-index: 2;
  left: 43px;
  top: 60px;
}
.profileBox {
  padding: 60px 10px 30px 20px;
  border-radius: 0.4475rem;
  position: relative;
  z-index: 1;
  bottom: 40px;
}
.profilePic {
  position: relative;
  bottom: 70px;
}
.uploadBtn {
  position: relative;
  bottom: 90px;
}
.updateBtn {
  position: relative;
  top: 30px;
}
/**/
.userImageArea {
  width:10%;
  min-width:250px;
}
.userInfoDataArea {
  width:60%;
  min-width:400px;
}
</style>

<script>
export default {
   data: () => ({
     valid: false,
     /* 회원정보 */
     userInfoDatas : [],
     userPassword : null,
     userPasswordCheck : null,
     photoData : null,
     photoName : null,
     photoUrl : null,
     /* */
     checkCount : 0,

     phone : null,
     phoneType : "###########"
   }),
   methods : {
     getUserInfo(){
       axios.get('/professor/info/select')
       .then((response) => {
         this.phone = response.data.message.phone;
         this.userInfoDatas = response.data.message;
         console.log(this.userInfoDatas);
       })
       .catch((error) => {
         console.log('getUserInfo Error : ' + error);
       })
     },
     /* 회원정보 수정 */
     setUserInfo(){
      this.checkCount = 0;
      let formData = new FormData();

      if(this.userPassword == null){
        this.checkCount += 1;
      }
      if(this.userPasswordCheck == null){
        this.checkCount += 1;
      }
      if(this.phone == null){
        this.checkCount += 1;
      }
      if(this.userInfoDatas.email == null){
        this.checkCount += 1;
      }
      if(this.userInfoDatas.office == null){
         this.checkCount += 1;
      }
      if(this.userPassword != this.userPasswordCheck){
        this.checkCount += 1;
      }

      /* 이미지 파일의 유무를 판단. */
      if(this.photoData != null){
         formData.append('photo', this.photoData);
      }

      formData.append('password', this.userPassword);
      formData.append('password_check', this.userPasswordCheck);
      formData.append('phone', this.phone);
      formData.append('email', this.userInfoDatas.email);
      formData.append('office', this.userInfoDatas.office);

      if(this.checkCount <= 0)
        axios.post('/professor/info/update',formData)
        .then((response) => {
          /* 통신 테스트 */
          console.log("update success");
          alert(response.data.message)
          /* 통신 완료 후, 입력한 비밀번호 초기화 */
          this.userPassword      = null;
          this.userPasswordCheck = null;
          /* 이미지 초기화 */
          this.photoData = null;
          /* 업데이트 후, 변경된 데이터를 보여주기 위해서 데이터를 다시 받아온다. */
          this.getUserInfo();

        }).catch((error) => {

        console.log('setUserInfo Error : ' + error);

        })
      else if(this.userPassword != this.userPasswordCheck)
        alert('パスワードが間違っています。')
      else
        alert('入力しない情報があります。')
     },
     /* 이미지 등록 */
     pickFile() {
       this.$refs.myFiles.click()
     },
     handleFileUpload (e) {
      console.log(e.target);
      console.log(e.target.files);

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
           this.photoData = files[0]
         })
       }
     },
     imageDataReset(){
        this.photoName = ''
        this.photoData = ''
        this.photoUrl = ''
     }
   },
   watch : {
     phone : function(){
       console.log('w');
       if(this.phone.length >= 11){
         this.phoneType = "###-####-####"
       }else{
         this.phoneType = "(###)###-#####"
       }

       console.log(this.phone);
       console.log(this.phoneType);
     }
   },
   created(){
     console.log('c');
     this.getUserInfo();
   },
   mounted(){
     console.log('m');
     this.getUserInfo();
   }
 }
</script>
