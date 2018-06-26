<template>
  <v-app id="inspire">

  <v-container class = "line" grid-list-xl>
    <v-flex xs12>
    <v-card class="studentProfileBox" color = "transparent" flat>
      <v-container grid-list-xl>
        <v-layout row wrap align-center>
          <v-flex xs1>
            <v-card-text v-if="updateCheck">
              <v-avatar size = "120px">
                <img :src="userInfoData[0].photo_url" />
              </v-avatar>
            </v-card-text>
          </v-flex>
          <!-- 학생 정보 -->
          <v-flex xs5>
            <v-card-text class = "studentName text-xs-left" v-if="updateCheck">
              <div class="className"><span> {{ userInfoData[0].id }} </span> {{ userInfoData[0].name }} </div>
              <div class="studentEmail"> {{ userInfoData[0].email }} </div>
              <!-- <div class="studentPhone"> {{ userInfoData[0].phone }} </div> -->
              <div class="studentPhone"> {{ userInfoData[0].study_class }} </div>
            </v-card-text>
          </v-flex>
          <!-- INFO1 END -->
          <!-- 학생 정보 접근 버튼 -->
          <v-flex xs6>
            <v-card-text class = "studentName text-xs-left">
              <!-- 출결 // 지도교수일 때만 표시되도록 처리한다.-->
              <v-btn depressed color="amber darken-2" fab large dark :to="menuData[0].studentAttendance" v-if="profType == 'tutor'">
                <v-icon>done</v-icon>
              </v-btn>
              <!-- 성적 -->
              <v-btn depressed color="amber darken-2" fab large dark :to="menuData[0].studentGrade">
                <v-icon>insert_chart</v-icon>
              </v-btn>
              <!-- 코멘트 -->
              <v-btn depressed color="amber darken-2" fab large dark :to="menuData[0].studentComment">
                <v-icon>chat_bubble</v-icon>
              </v-btn>
            </v-card-text>
          </v-flex>
          <!-- INFO2 END -->
        </v-layout>
      </v-container>
    </v-card>
  </v-flex>
</v-container>

  <!-- 각 페이지 별, Contents -->
  <v-content style="padding-top:0px;"  :v-if="profType != null">
    <router-view name = "body"></router-view>
  </v-content>

  </v-app>
</template>

<style>
.line {
  border-bottom: 1px solid rgb(218, 218, 218);
}
.studentProfileBox {
  position: relative;
  left: 20px;
  top: 5px;
}
.studentName {
  position: relative;
  left: 60px;
  bottom: 5px;
}
.className {
  font-size: 25px;
  font-family: "Nanum Gothic Coding";
  font-weight: bold;
  margin: 0 0 10px 0;
}
  .className span {
    font-size: 25px;
    font-family: "Montserrat";
    font-weight: lighter;
    color: rgb(147, 146, 146);
    margin: 0 10px 0 0px;
  }
.studentEmail {
  font-size: 15px;
  font-family: "Montserrat";
  font-weight: lighter;
  margin: 0 0 3px 0;
}
.studentPhone {
  font-size: 15px;
  font-family: "Montserrat";
  font-weight: lighter;
}
</style>

<script>
export default {
  data: () => ({

    /* 지됴교수인지 교과목교수인지 체크 prof, tutor */
    profType : null,
    /* 메뉴 링크 (학번을 get 타입으로 지정해놓는다.) */
    menuData : [{
      studentAttendance : 'main',
      studentGrade : 'grade',
      studentComment : 'comment',
    }],
    /* 학생 정보 */
    userInfoData : [{
      photo_url : '/images/default.jpg',
      study_class : 'not Data',
      id : '1234567',
      name : '홍길동',
      email : 'group8@grid.system',
      phone : '010-XXXX-XXXX'
    }],
    updateCheck : false
  }),
  methods : {
      /* 학생 정보를 가지고 온다. ~ 상단에 사진, 학번, 이름, 이메일, 연락처 표시~*/
      getUserInfo() {
        axios.get('/professor/detail/info', {
          params : {
            std_id : this.$router.history.current.query.getInfoIdType
          }
        })
        .then((response) => {
          this.userInfoData[0] = response.data.message;
          this.updateCheck = true
        })
        .catch((error) => {
          console.log('getInfo Error : ' + error);
        })
      },
      /* 지도교수인지 교과목 교수인지 판단. */
      checkTutor(){
        axios.post('/professor/is_tutor')
        .then((response) => {
          /* 지도교수 권한이 있는지에 대한 boolean값이 반환된다. */
          /* true 이면 지도교수의 메뉴를 활성화, false이면 비활성화한다. (학생 출결 및 담당과목 외 성적) */
          if(response.data){
            /* 지도교수 */
            this.profType = 'tutor';
            /* 메뉴 링크 부여 */
            this.menuData[0].studentAttendance += '?getInfoIdType='+this.$router.history.current.query.getInfoIdType;
            this.menuData[0].studentGrade      += '?getInfoIdType='+this.$router.history.current.query.getInfoIdType;
            this.menuData[0].studentComment    += '?getInfoIdType='+this.$router.history.current.query.getInfoIdType;
          }else{
            /* 교과목 교수 */
            /* 메인페이지인 출석 정보에 대한 권한이 없으므로 성적페이지로 이동한다. */
            /* 페이지를 이동했는지 체크한다.  */
            if(this.$router.history.current.query.prof != 'false'){
              /* 연속해서 이동되지 않도록 url을 건드려준다. */
              location.href = '/studentManagement/grade?getInfoIdType='+this.$router.history.current.query.getInfoIdType+'&prof=false';
            }else{
              /* 출석페이지를 띄우지 않기 위해서 뒤에서 처리한다. / 교과목 교수 권한 부여 */
              this.profType = 'prof';
              /* 메뉴 링크 부여 (교과목교수에 맞추어 변형) */
              this.menuData[0].studentAttendance = null;
              this.menuData[0].studentGrade      += '?getInfoIdType='+this.$router.history.current.query.getInfoIdType+'&prof=false';
              this.menuData[0].studentComment    += '?getInfoIdType='+this.$router.history.current.query.getInfoIdType+'&prof=false';
            }
          }
        })
        .catch((error) => {
          console.log("tutorCheck Error : " + error);
        })
      }

  },
  mounted(){
    this.getUserInfo();
    this.checkTutor();
  }
}
</script>
