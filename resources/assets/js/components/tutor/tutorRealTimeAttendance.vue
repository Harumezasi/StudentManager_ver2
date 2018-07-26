<template>
<div class="realTimeAttendanceCheck">

  <!-- Header -->
  <v-parallax class="mainImage" src="/images/studentManagement.png" height="300">
    <h1 class="categoryAttendance">Student Attendance</h1>
  </v-parallax>

  <!-- 출결 현황 ( 결석, 지각, 등/하교, 관심학생 ) -->
  <v-container grid-list-xl>
    <v-layout row wrap align-center>

      <div class="realTimeTopDiv">

        <!-- 결석 -->
        <div class="absenceCard">
          <v-card class="firstLineCards white">
            <v-card-text>
              <v-flex xs12 class="cardsTitleUnderLine">
                <v-layout row wrap align-center>
                  <v-flex xs12 md2>
                    <v-icon color="light-blue darken-2" large>highlight_off</v-icon>
                  </v-flex>
                  <v-flex xs12 md6>
                    <h1 class="cardsTitle">欠席した学生</h1>
                  </v-flex>
                  <!-- 학생 수 -->
                  <v-flex xs12 md4>
                    <h1 class="currentStudentNum">{{ absenceCount }}/48</h1>
                  </v-flex>
                </v-layout>
              </v-flex>
            </v-card-text>
            <!-- 결석 학생 목록 -->
            <v-card-text class="absenceAndLateInfoArea">
              <v-list three-line>
                <template v-for="absence in absenceData">
                 <v-list-tile :key="absence.name" avatar @click="">
                   <!-- 결석 학생 사진 -->
                   <v-list-tile-avatar size = "40" style="position: relative; top: 8  px;">
                     <img :src="absence.photo_url">
                   </v-list-tile-avatar>
                   <v-list-tile-content>
                     <!-- 결석 학생의 이름과 학번 -->
                     <v-list-tile-title v-html="absence.id"></v-list-tile-title>
                     <v-list-tile-title v-html="absence.name"></v-list-tile-title>
                   </v-list-tile-content>
                 </v-list-tile>
               </template>
              </v-list>
            </v-card-text>
          </v-card>
        </div>


        <!-- 지각 -->
        <div class="lateCard">
          <v-card class="firstLineCards white">
            <v-card-text>
              <v-flex xs12 class="cardsTitleUnderLine">
                <v-layout row wrap align-center>
                  <v-flex xs12 md2>
                    <v-icon color="light-blue darken-2" large>directions_run</v-icon>
                  </v-flex>
                  <v-flex xs12 md6>
                    <h1 class="cardsTitle">遅刻した学生</h1>
                  </v-flex>
                  <v-flex xs12 md4>
                    <h1 class="currentStudentNum">{{ lateCount }}/48</h1>
                  </v-flex>
                </v-layout>
              </v-flex>
            </v-card-text>
            <!-- 지각 학생 목록 -->
            <v-card-text class="absenceAndLateInfoArea">
              <v-list three-line>
                <template v-for="late in lateData">
               <v-list-tile :key="late.name" avatar @click="">
                 <!-- 지각 학생 사진 -->
                 <v-list-tile-avatar size = "40" style="position: relative; top: 8  px;">
                   <img :src="late.photo_url">
                 </v-list-tile-avatar>
                 <v-list-tile-content>
                   <!-- 지각 학생의 이름과 학번 -->
                   <v-list-tile-title v-html="late.id"></v-list-tile-title>
                   <v-list-tile-title v-html="late.name"></v-list-tile-title>
                 </v-list-tile-content>
                 <v-list-tile-content>
                   <!-- 지각 시간-->
                   <v-list-tile-title v-html="late.sign_in_time"></v-list-tile-title>
                 </v-list-tile-content>
               </v-list-tile>
             </template>
              </v-list>
            </v-card-text>
          </v-card>
        </div>

        <!-- 등/하교 & 관심학생 영역 -->
        <!-- 등/하교 -->
        <div class="realTimeTopDiv_2">
          <div class="attendanceCard">
          <v-card class="attendanceCardBox white">
            <v-card-text>
              <v-flex xs12 class="cardsTitleUnderLine">
                <v-layout row wrap align-center>
                  <v-flex xs12 md2>
                    <v-icon color="light-blue darken-2" large>alarm</v-icon>
                  </v-flex>
                  <v-flex xs12 md6>
                    <h1 class="cardsTitle">出席した学生</h1>
                  </v-flex>
                  <v-flex xs12 md4>
                    <h1 class="currentStudentNum">{{ signData.length }}/48</h1>
                  </v-flex>
                </v-layout>
              </v-flex>
            </v-card-text>
            <!-- 등/하교 학생 목록 -->
            <v-card-text class="attendanceInfoArea">
              <v-list three-line>
                <template v-for="datas in signData">
                 <v-list-tile :key="datas.name" avatar @click="">
                   <!-- 등/하교 학생 사진 -->
                   <v-list-tile-avatar size = "40" style="position: relative; top: 8px;">
                     <img :src="datas.photo_url">
                   </v-list-tile-avatar>
                   <v-list-tile-content>
                     <!-- 등/하교 학생의 이름과 학번 -->
                     <v-list-tile-title v-html="datas.id"></v-list-tile-title>
                     <v-list-tile-title v-html="datas.name"></v-list-tile-title>
                   </v-list-tile-content>
                   <!-- 등교 완료시, 초록색 / 하교 완료시, 주황색 / 둘 다 X일 때, 회색-->
                   <v-list-tile-action>
                     <v-flex xs12>
                         <v-layout row wrap align-center>
                           <v-flex xs12 md6 v-if="!datas.sign_out">
                             <v-chip :color="datas.sign_in ? 'light-green' : 'grey lighten-1'">登校</v-chip>
                           </v-flex>
                           <v-flex xs12 md6 v-else>
                             <v-chip :color="datas.sign_out ? 'amber' : 'grey lighten-1'">下校</v-chip>
                           </v-flex>
                         </v-layout>
                      </v-flex>
                   </v-list-tile-action>
                 </v-list-tile>
               </template>
              </v-list>
            </v-card-text>
          </v-card>
        </div>

        <!-- 관심 학생 -->
        <div class="interestCard">
          <v-card class="interestStudentBox">
            <v-card-text>
              <v-flex xs12 class="cardsTitleUnderLine">
                <v-layout row wrap align-center>
                  <v-flex xs12 md2>
                    <v-icon color="red darken-3" large>error</v-icon>
                  </v-flex>
                  <v-flex xs12 md7>
                    <h1 class="cardsTitle">注意が必要な学生</h1>
                  </v-flex>
                  <v-flex xs12 md3>
                    <h1 class="currentStudentNum" style="color: white">{{ loveStudentCount }}/48</h1>
                  </v-flex>
                </v-layout>
              </v-flex>
            </v-card-text>
            <v-card-text class="interestInfoArea">
              <v-flex xs12>
                <v-layout row wrap align-center>
                  <div v-for="loveStudent in loveStudentData">
                    <v-flex xs12 md4>
                      <v-tooltip top>
                        <v-btn slot="activator" icon :onclick="loveStudent.infoLink">
                          <v-avatar style="box-shadow:  0px 4px 10px 0 rgba(33, 33, 33, 0.36);" size="70"><img :src="loveStudent.photo_url" /></v-avatar>
                        </v-btn>
                        <span><h3>名前 : {{ loveStudent.name }}<br>学生番号 : {{ loveStudent.id }}<br>理由 : {{ loveStudent.reason }} </h3></span>
                      </v-tooltip>
                    </v-flex>
                  </div>
                </v-layout>
              </v-flex>
            </v-card-text>
          </v-card>
        </div>
      </div>


</div>
</v-layout>
</v-container>

</div>
</template>

<style>
.realTimeTopDiv {
  position: relative;
  bottom: 200px;
  margin-left: 20px;
}

.realTimeTopDiv_2 {
  float: left;
  margin: 10px;
  width: 34%;
}

.absenceCard {
  float: left;
  margin: 10px;
  width: 30%;
}

.lateCard {
  float: left;
  margin: 10px;
  width: 30%;
}

.studentListArea {
  overflow-y: scroll;
  height: 500px;
}

.categoryAttendance {
  color: #FFFFFF;
  font-size: 30px;
  position: relative;
  font-family: "Montserrat";
  font-weight: Bold;
  position: relative;
  left: 65px;
  bottom: 60px;
}

/* 공통 css */

.cardsTitle {
  font-family: "Mplus 1p";
  font-size: 19px;
  font-weight: bold;
  position: relative;
  right: 15px;
}

.currentStudentNum {
  font-family: "Montserrat";
  font-weight: lighter;
  font-size: 31px;
  position: relative;
  right: 15px;
  bottom: -3px;
}

.cardsTitleUnderLine {
  border-bottom: 1px solid;
  border-color: rgba(187, 187, 187, 0.73);
}

/* 결석 & 지각 */

.firstLineCards {
  border-radius: 0.6975rem;
  min-height: 680px;
  box-shadow: 0px 4px 10px 0 rgba(33, 33, 33, 0.36);
}
.absenceAndLateInfoArea {
  height: 600px;
  overflow-y: scroll;
  position: relative;
}

/* 등/하교 */

.attendanceCardBox {
  border-radius: 0.6975rem;
  min-height: 330px;
  box-shadow: 0px 4px 10px 0 rgba(33, 33, 33, 0.36);
  margin-bottom: 18px;
}

.attendanceInfoArea {
  height: 305px;
  overflow-y: scroll;
  position: relative;
}


/* 관심 학생 */

.interestStudentBox {
  border-radius: 0.6975rem;
  min-height: 200px;
  box-shadow: 0px 4px 10px 0 rgba(33, 33, 33, 0.36);
  background: linear-gradient(to bottom right, rgb(87, 140, 247), rgb(2, 90, 224));
}

.interestInfoArea {
  height: 175px;
  overflow-y: scroll;
  position: relative;
}

/* 스크롤 */

/* width */

::-webkit-scrollbar {
  width: 9px;
}

/* Track */

::-webkit-scrollbar-track {
  background-color: rgb(208, 208, 208);
  border-radius: 10px;
}

/* Handle */

::-webkit-scrollbar-thumb {
  background: rgb(185, 199, 250);
  border-radius: 10px;
}

/* Handle on hover */

::-webkit-scrollbar-thumb:hover {
  background: #5491f7;
}
</style>

<script>
export default {
  data() {
    return {
      /*-- 지각 --*/
      lateData: [],
      lateCount: 0,
      lateStudentNum: 50,
      /*-- 결석 --*/
      absenceData: [],
      absenceCount: 0,
      /*-- 관심학생 --*/
      loveStudentData: [],
      loveStudentCount: 0,
      /*-- 등교 --*/
      attendanceData: [],
      attendanceCount: 0,
      /*-- 하교 --*/
      returnHomeData: [],
      returnHomeCount: 0,
      /* 등/하교 */
      signData: []
    }
  },

  mounted() {
    this.getData();
    setInterval(this.getData, 2500);
  },
  methods: {
    sortFun() {
      function sortData(a, b) {
        if (a.sign_in_time > b.sign_in_time) {
          return -1;
        }
        if (a.sign_in_time < b.sign_in_time) {
          return 1;
        }
      }
      return this.lateData.sort(sortData);
    },
    getData() {
      axios.get('/tutor/attendance/today')
        .then((response) => {
          console.log(response.data);
          // 지각자
          this.lateData = response.data.message.lateness;
          if (this.lateData == null) {
            this.lateCount = 0;
          } else {
            this.lateCount = this.lateData.length;
          }
          this.sortFun();

          // 결석자
          this.absenceData = response.data.message.absence;
          if (this.absenceData == null) {
            this.absenceCount = 0;
          } else {
            this.absenceCount = this.absenceData.length;
          }

          // 관심
          this.loveStudentData = response.data.message.need_care;
          if (this.loveStudentData == null) {
            this.loveStudentCount = 0;
          } else {
            this.loveStudentCount = this.loveStudentData.length;
            /* 관심학생 정보 바로가기 링크 생성 */
            for (let data in this.loveStudentData) {
              this.$set(this.loveStudentData[data], 'infoLink', "window.open('/studentManagement/main?getInfoIdType=" + this.loveStudentData[data].id + "', 'newwindow', 'width=1000,height=700'); return false;");
            }
          }

          // 1. sign 변수 생성
          // 2. 등/하교 합치기
          let sign = [];
          // 등교 정보 등록
          sign.push(response.data.message.sign_in)
          // 하교 정보 등록
          for (let start in response.data.message.sign_out) {
            sign[0].push(response.data.message.sign_out[start])
          }
          // 등, 하교 boolean 값 생성
          for (let start in sign[0]) {
            if (sign[0][start].sign_in_time != null)
              this.$set(sign[0][start], 'sign_in', true)
            else
              this.$set(sign[0][start], 'sign_in', false)

            if (sign[0][start].sign_out_time != null)
              this.$set(sign[0][start], 'sign_out', true)
            else
              this.$set(sign[0][start], 'sign_out', false)
          }
          this.signData = sign[0];
          // 기존 사용 삭제 보류
          // 등교
          this.attendanceData = response.data.message.sign_in;
          if (this.attendaceData == null) {
            this.attendanceCount = 0;
          } else {
            this.attendanceCount = this.attendanceData.length;
          }

          // 하교
          this.returnHomeData = response.data.message.sign_out;
          if (this.returnHomeData == null) {
            this.returnHomeCount = 0;
          } else {
            this.returnHomeCount = this.returnHomeData.length;
          }

        }).catch((error) => {
          console.log(error);
        });
    }
  }
}
</script>
