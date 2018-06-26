<template>
  <div class = "main">

    <div>
      <div class="header text-center">
        <v-parallax class="mainImage" src="/images/mainImage.jpg" height="420">
          <h2 class = "mainTitle">GRIT</h2>
          <h2 class = "mainSubTitle">
            <span>Student Management Service</span>
          </h2>
        </v-parallax>
      </div>
    </div>

    <v-flex xs12>
      <v-container grid-list-xl>
        <v-layout row wrap align-center v-if="isTutor">

          <v-flex xs12 md4>
            <v-card class = "firstLineCard">
              <v-flex xs12>
                <v-container grid-list-xl>
                  <v-layout row wrap align-center>

                    <v-flex xs12 md3>
                      <v-icon color = "amber" x-large>alarm</v-icon>
                    </v-flex>
                    <v-flex xs12 md6>
                      <h1 class = "firstCard">금일 지각 학생</h1>
                    </v-flex>
                    <v-flex xs12 md3>
                      <h2>{{ latenessStudentCount }} 명 </h2>
                    </v-flex>
                  </v-layout>
                </v-container>
              </v-flex>

              <v-list three-line>
                <div class="studentLists">
                  <!-- 반복문 시작 -->
                  <template v-for="datas in latenessStudent">
                    <v-divider :inset="true" :key="index"></v-divider>
                    <v-list-tile :key="datas.index" avatar @click="">
                      <!-- 학생 사진 -->
                      <v-list-tile-avatar>
                        <img :src="datas.photo_url">
                      </v-list-tile-avatar>
                      <v-list-tile-content>
                        <!-- 학생의 이름과 학번 -->
                        <v-list-tile-title v-html="datas.name"></v-list-tile-title>
                        <v-list-tile-sub-title v-html="datas.id"></v-list-tile-sub-title>
                      </v-list-tile-content>
                    </v-list-tile>
                  </template>
                </div>
              </v-list>
            </v-card>
          </v-flex>

          <v-flex xs12 md4>
            <v-card class = "firstLineCard">
              <v-flex xs12>
                <v-container grid-list-xl>
                  <v-layout row wrap align-center>
                    <v-flex xs12 md3>
                      <v-icon color = "red" x-large>highlight_off</v-icon>
                    </v-flex>
                    <v-flex xs12 md6>
                      <h1 class = "firstCard">금일 결석 학생</h1>
                    </v-flex>
                    <v-flex xs12 md3>
                      <h2>{{ absenceStudentCount }} 명 </h2>
                    </v-flex>
                  </v-layout>
                </v-container>
              </v-flex>
              <!--  학생 목록 -->
              <v-list three-line>
                <div class="studentLists">
                 <!-- 반복문 시작 -->
                 <template v-for="datas in absenceStudent">
                   <v-divider :inset="true" :key="index"></v-divider>
                   <v-list-tile :key="datas.index" avatar @click="">
                     <!-- 학생 사진 -->
                     <v-list-tile-avatar>
                       <img :src="datas.photo">
                     </v-list-tile-avatar>
                     <v-list-tile-content>
                       <!-- 학생의 이름과 학번 -->
                       <v-list-tile-title v-html="datas.name"></v-list-tile-title>
                       <v-list-tile-sub-title v-html="datas.id"></v-list-tile-sub-title>
                     </v-list-tile-content>
                   </v-list-tile>
                 </template>
                </div>
              </v-list>
            </v-card>
          </v-flex>

          <v-flex xs12 md4>
            <v-card class = "firstLineCard">

              <v-flex xs12>
                <v-container grid-list-xl>
                  <v-layout row wrap align-center>

                    <v-flex xs12 md3>
                      <v-icon color = "light-green " x-large>notifications_none</v-icon>
                    </v-flex>
                    <v-flex xs12 md9>
                      <h1 class = "firstCard">최근 알림</h1>
                    </v-flex>
                  </v-layout>
                </v-container>
              </v-flex>
              <v-card-text>
                <v-chip color="blue lighten-1" text-color="white">
                  '박효동' 학생이 관심학생으로 지정되었습니다.
                </v-chip>
                <v-chip color="yellow darken-3" text-color="white">
                   '이승민' 학생이 관심학생으로 지정되었습니다.
                </v-chip>
              </v-card-text>
            </v-card>
          </v-flex>

        </v-layout>
      </v-container>
    </v-flex>


  </div>
</template>
<script>
export default {
  data() {
    return {
      /* 지도 교수 권한 */
      isTutor : false,
      /* 결석 학생 */
      absenceStudent : [],
      absenceStudentCount : 0,
      /* 지각 학생 */
      latenessStudent : [],
      latenessStudentCount : 0,
      }
    },
    methods : {
        getAttendanceData(){
          axios.get('/tutor/attendance/today')
          .then((response) => {
            /* 금일 결석 학생 */
            this.absenceStudent = response.data.message.absence;
            this.absenceStudentCount = response.data.message.absence.length;
            /* 금일 지각 학생 */
            this.latenessStudent = response.data.message.lateness;
            this.latenessStudentCount = response.data.message.lateness.length;
          }).catch((error) => {
            console.log("getAttStuData Err :"+ error);
          })
        },
        /* 지도 교수 권한 확인 */
        checkIsTutor(){
          axios.post('/professor/is_tutor')
          .then((response) => {
            this.isTutor = response.data
          }).catch((error) => {
            console.log("checkTutro Err :" + error);
          })
        }
    },
    mounted(){
      this.checkIsTutor();
      this.getAttendanceData();
    }
};

</script>
<style>
.studentLists {
  overflow-y: scroll;
  max-height: 240px;
}


/*-- 메인 이미지 --*/
.mainImage {
  max-width: 100%;
  height: auto;
}
.firstLineCard {
  position: relative;
  bottom: 45px;
  border-radius: 0.6975rem;
  box-shadow: 0 3px 10px 0 rgba(161, 161, 161, 0.36);
  min-height: 370px;
  max-height: 370px;
}
.firstCard {
  font-family: "Nanum Gothic Coding";
  font-weight: lighter;
  color: rgb(61, 61, 61);
  position: relative;
  right: 30px;
  bottom: 3px;
}

.mainTitle {
  display:inline-block;
  white-space:nowrap;
  font-family: "Montserrat";
  font-size: 70px;
  font-style: italic;
  font-weight: lighter;
  position: relative;
  left: 50px;
  bottom: 10px;
  position: relative;
}
.mainSubTitle {
  display:inline-block;
  white-space:nowrap;
  font-family: "Montserrat";
  font-size: 50px;
  font-weight: lighter;
  position: relative;
  left: 50px;
  bottom: 10px;
  position: relative;
}

.mainTitle:first-of-type {
  animation: showup 10s infinite;
}

.mainSubTitle:last-of-type {
  width:0px;
  animation: reveal 10s infinite;
}

.mainSubTitle:last-of-type span {
  margin-left:-355px;
  animation: slidein 10s infinite;
}

@keyframes showup {
    0% {opacity:0;}
    20% {opacity:1;}
    80% {opacity:1;}
    100% {opacity:0;}
}

@keyframes slidein {
    0% { margin-left:-1000px; }
    20% { margin-left:-1000px; }
    35% { margin-left:0px; }
    100% { margin-left:0px; }
}

@keyframes reveal {
    0% {opacity:0;width:0px;}
    20% {opacity:1;width:0px;}
    30% {width:355px;}
    80% {opacity:1;}
    100% {opacity:0;width:355px;}
}


</style>
