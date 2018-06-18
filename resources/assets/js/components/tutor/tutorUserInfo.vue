<template>
  <div class="tutorInformation">

    <v-container grid-list-xl>
      <v-flex xs11>
        <v-card
        class = "profileTitleBox"
        color = "amber darken-2"
        style = "box-shadow:  0 4px 12px 0 rgba(244, 149, 24, 0.36)"
        >
          <v-card-text style="padding-bottom: 5px;">
            <h1 style="color: white">회원정보</h1>
            <p style="font-size:20px">정보확인 및 수정</p>
          </v-card-text>
        </v-card>
      </v-flex>
    </v-container>

    <v-container grid-list-xl>
     <v-flex xs12>
       <v-card class="profileBox elevation-4" color = "white" style="min-width:370px">
       <v-container grid-list-xl>
         <v-layout row wrap align-center>
           <div class="userImageArea">
             <v-flex xs3 text-xs-center>
               <!-- 프로필 사진 -->
               <v-card-text class = "profilePic text-xs-center">
                <v-avatar size = "125px">
                  <img
                  class = "img-circle elevation-3"
                  :src = "userInfoDatas.photo"
                  >
                </v-avatar>
              </v-card-text>

              <!-- 프로필 사진 업데이트 버튼 -->
              <v-card-text class = "uploadBtn text-xs-center">
                <input type="file" value="프로필 사진 변경" id="file" ref='photo' v-on:change="handleFileUpload()">
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
                   v-model="userInfoDatas.phone"
                   label="Phone"
                   mask="phone"
                   id="fontSetting"
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
               <v-btn color = "amber darken-2" dark v-on:click="setUserInfo()" class="fontSetting">회원정보 변경</v-btn>
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
  font-size: 30px;
  font-style: 'Gothic A1';
}

.profileTitleBox {
  border-radius: 0.2975rem;
  position: relative;
  z-index: 2;
  left: 35px;
  top: 60px;
}
.profileBox {
  padding: 60px 10px 30px 20px;
  border-radius: 0.2975rem;
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
     photoData : null
   }),
   methods : {
     getUserInfo(){
       axios.get('/professor/info/select')
       .then((response) => {
         this.userInfoDatas = response.data.message;
         console.log(this.userInfoDatas);
       })
       .catch((error) => {
         console.log('getUserInfo Error : ' + error);
       })
     },
     setUserInfo(){
       /* 이미지 파일의 유무를 판단. */
       if(this.photoData != null){
         console.log('접근1');
         let photo = new FormData();
         console.log(this.photoData);
         photo.append('photo', this.photoData);
         console.log(photo);
       } else {
         console.log('접근2');
         let photo = null;
       }
       axios.post('/professor/info/update',{
         'password'       : this.userPassword,
         'password_check' : this.userPasswordCheck,
         'phone'          : this.userInfoDatas.phone,
         'email'          : this.userInfoDatas.email,
         'office'         : this.userInfoDatas.office,
         'photo'          : this.photoData
       },
       {
           headers: {
               'Content-Type': 'multipart/json'
           }
       })
       .then((response) => {
         /* 통신 테스트 */
         console.log("update success" + response.data);
         /* 통신 완료 후, 입력한 비밀번호 초기화 */
         this.userPassword      = null;
         this.userPasswordCheck = null;
         /* 업데이트 후, 변경된 데이터를 보여주기 위해서 데이터를 다시 받아온다. */
         this.getUserInfo();
       })
       .catch((error) => {
         console.log('setUserInfo Error : ' + error);
       })
     },
     handleFileUpload(){
       /* 업로드 할 이미지 파일 변경*/
      this.photoData = this.$refs.photo.files[0];
     }
   },
   created(){
     this.getUserInfo();
   }
 }
</script>
