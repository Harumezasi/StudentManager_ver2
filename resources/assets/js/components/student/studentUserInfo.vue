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
            <h2 style="color: white">회원정보</h2>
            <p>정보확인 및 수정</p>
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
                   required
                 ></v-text-field>

                 <!-- 이메일 -->
                 <v-text-field
                   v-model="userInfoDatas.email"
                   label="E-mail"
                   required
                 ></v-text-field>

                 <!-- 연락처 -->
                 <v-text-field
                   v-model="userInfoDatas.phone"
                   label="Phone"
                   mask="phone"
                   required
                 ></v-text-field>

                 <!-- 비밀번호 -->
                 <v-text-field
                   v-model="userPassword"
                   label="Password"
                   type="password"
                   required
                 ></v-text-field>

                 <!-- 비밀번호 확인 -->
                 <v-text-field
                   v-model="userPasswordCheck"
                   label="Password Check"
                   type="password"
                   required
                 ></v-text-field>

               </v-form>
             </v-card-text>
             <v-card-text class = "updateBtn text-xs-right">
               <v-btn color = "amber darken-2" dark v-on:click="setUserInfo()">회원정보 변경</v-btn>
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
       axios.get('/student/info/select')
       .then((response) => {
         this.userInfoDatas = response.data.message;
         console.log(this.userInfoDatas);
       })
       .catch((error) => {
         console.log('getUserInfo Error : ' + error);
       })
     },
     /* 회원정보 변경 */
     setUserInfo(){
      console.log('미구현');
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
