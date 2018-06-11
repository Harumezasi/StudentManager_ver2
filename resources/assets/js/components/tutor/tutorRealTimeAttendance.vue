<template>
  <div class = "realTimeAttendanceCheck">

    <!-- Header -->
    <div class="panel-header">
      <div class="header text-center">
        <v-layout class = "imgTitle" column align-center justify-center>
          <h1 class="category1">Attendance Management</h1>
          <p class="category">Attendance Check</p>
        </v-layout>
      </div>
    </div>

    <!-- 실시간 등/하교, 지각, 결석, 관심학생 수-->
    <v-container grid-list-xl>
      <v-flex xs12>
        <v-card class="attendanceNumBox elevation-2" color = "white">
        <v-container grid-list-xl>
          <v-layout row wrap align-center>

            <!-- 정상 등/하교 학생 수 -->
            <v-flex xs3 text-xs-center>
              <div class="leftLine">
                <v-icon color="green" x-large style="margin: 5px 0 10px 0;">check</v-icon>
                <h2>
                  {{ attendanceCount }} / {{ returnHomeCount }}
                </h2>
                <h3>정상 등/하교</h3>
              </div>
            </v-flex>

            <!-- 지각 학생 수 -->
            <v-flex xs3 text-xs-center>
              <div class="leftLine">
                <v-icon color="yellow darken-3" x-large style="margin: 5px 0 10px 0;">directions_run</v-icon>
                <h2>
                  {{ lateCount }}
                </h2>
                <h3>지각</h3>
              </div>
            </v-flex>

            <!-- 결석 학생 수 -->
            <v-flex xs3 text-xs-center>
              <div class="leftLine">
                <v-icon color="red" x-large style="margin: 5px 0 10px 0;">clear</v-icon>
                <h2>
                  {{ absenceCount }}
                </h2>
                <h3>결석</h3>
              </div>
            </v-flex>

            <!-- 관심학생 수 -->
            <v-flex xs3 text-xs-center>
              <div>
                <v-icon color="pink lighten-3" x-large style="margin: 5px 0 10px 0;">favorite_border</v-icon>
                <h2>
                  {{ loveStudentCount }}
                </h2>
                <h3>관심학생</h3>
              </div>
            </v-flex>

          </v-layout>
        </v-container>
        </v-card>
      </v-flex>
    </v-container>

    <!-- 출결 현황 보기 영역 -->
    <v-flex xs12>
      <v-container class="firstLineCards" grid-list-xl>
        <v-layout row wrap align-center>
          <!-- 지각 -->
          <v-flex xs12 md4>
            <v-card class="elevation-4 transparent">
              <v-card-text>
                <h1 class = "attendanceTitle">지각</h1>
              </v-card-text>
              <v-card-text class="studentInfoScreen attendanceInfoArea">
                <v-chip color = "amber" v-for="late in lateData" :key="late.name" class="studentInfoArea">
                    <v-card-title>
                      <div>
                        <span>{{ late.name }}</span><br>
                        <span>{{ late.sign_in_time }}</span>
                      </div>
                    </v-card-title>
                  </v-chip>
              </v-card-text>
            </v-card>
          </v-flex>

          <!-- 결석 -->
          <v-flex xs12 md4>
            <v-card class="elevation-4 transparent">
              <v-card-text>
                <h1 class = "attendanceTitle">결석</h1>
              </v-card-text>
              <v-card-text class="studentInfoScreen attendanceInfoArea">
                <v-chip color = "red" v-for="absence in absenceData" :key="absence.name" class="studentInfoArea">
                    <v-card-title>
                      <div>
                        <span>{{ absence.id }}</span>
                        <span>{{ absence.name }}</span>
                      </div>
                    </v-card-title>
                  </v-chip>
              </v-card-text>
            </v-card>
          </v-flex>

          <!-- 관심학생 -->
          <v-flex xs12 md4>
            <v-card class="elevation-4 transparent">
              <v-card-text>
                <h1 class = "attendanceTitle">관심학생</h1>
              </v-card-text>
              <v-card-text class="studentInfoScreen attendanceInfoArea">
                <v-chip color = "pink lighten-3" v-for="loveStudent in loveStudentData" :key="loveStudent.name" class="studentInfoArea">
                    <v-card-title>
                      <div>
                        <span>{{ loveStudent.name }}</span><br>
                        <span>{{ loveStudent.come }}</span>
                      </div>
                    </v-card-title>
                  </v-chip>
              </v-card-text>
            </v-card>
          </v-flex>
        </v-layout>
      </v-container>
    </v-flex>

    <v-flex xs12>
      <v-container class = "secondLineCards" grid-list-xl>
        <v-layout row wrap align-center>

          <!-- 등교 -->
          <v-flex xs12 md6>
            <v-card class="elevation-4 transparent">
              <v-card-text>
                <h1 class = "attendanceTitle">등교</h1>
              </v-card-text>
              <v-card-text class="studentInfoScreen attendanceInfoArea">
                <v-chip color = "green" v-for="attendance in attendanceData" :key="attendance.name" class="studentInfoArea">
                    <v-card-title>
                      <div>
                        <span>{{ attendance.name }}</span><br>
                        <span>{{ attendance.sign_in_time }}</span>
                      </div>
                    </v-card-title>
                  </v-chip>
              </v-card-text>
            </v-card>
          </v-flex>

          <!-- 하교 -->
          <v-flex xs12 md6>
            <v-card class="elevation-4 transparent">
              <v-card-text>
                <h1 class = "attendanceTitle">하교</h1>
              </v-card-text>
              <v-card-text class="studentInfoScreen attendanceInfoArea">
                <v-chip color = "green" v-for="returnHome in returnHomeData" :key="returnHome.name" class="studentInfoArea">
                    <v-card-title>
                      <div>
                        <span>{{ returnHome.name }}</span><br>
                        <span>{{ returnHome.sign_out_time }}</span>
                      </div>
                    </v-card-title>
                  </v-chip>
              </v-card-text>
            </v-card>
          </v-flex>

        </v-layout>
      </v-container>
    </v-flex>
  </div>
</template>

<style>
.leftLine {
  border-right: 1px solid;
  border-color: #d2d2d2;

}

.category1 {
    color: #FFFFFF;
    font-size: 30px;
    font-family: "Montserrat";
    font-weight: Bold;
}
.category {
    max-width: 600px;
    color: rgba(255, 255, 255, 0.5);
    margin: 0 auto;
    font-size: 17px;
    font-family: "Montserrat"
}
.panel-header {
  height: 200px;
  padding-top: 70px;
  padding-bottom: 45px;
  background: #141E30;
  /* fallback for old browsers */
  background: -webkit-gradient(linear, left top, right top, from(#0c2646), color-stop(60%, #204065), to(#2a5788));
  background: linear-gradient(to right, #0c2646 0%, #204065 60%, #2a5788 100%);
  position: relative;
  overflow: hidden;
}

.panel-header-sm {
  height: 135px;
}

.panel-header-lg {
  height: 380px;
}

.cardLineOne {
  height: 90px;
  width: 0px;
  margin-left: 0;

}
.attendanceNumBox {
  border-radius: 0.2975rem;
  position: relative;
  z-index: 1;
  bottom: 50px;
}

h2 {
  font-family: "Montserrat";
  font-weight: "Extra-Bold";
}
h3 {
  font-family: "Nanum Gothic Coding";
  font-weight: lighter;
  color: rgb(136, 136, 136);
  margin-bottom: 5px;
}

.firstLineCards {
  border-radius: 0.2975rem;
  position: relative;
  z-index: 2;
  bottom: 70px;
}
.secondLineCards {
  border-radius: 0.2975rem;
  position: relative;
  z-index: 3;
  bottom: 80px;
}
.attendanceTitle {
  font-family: "Nanum Gothic Coding";
  font-weight: lighter;
  border-bottom: 1px solid;
  padding-bottom: 6px;
  border-color: rgba(187, 187, 187, 0.73);
}
/**/
.attendanceInfoArea {
  min-height: 500px;
  max-height: 500px;
  overflow-y: scroll;
}

.studentInfoArea {
  width : 200px;
  height : 100px;
  font-size: 15px;
  font-weight: bold;
  text-align: center;
  overflow: hidden;
}
.studentInfoScreen {
  text-align:center;
}


</style>

<script>
export default {
  data () {
    return {
      /*-- 지각 --*/
      lateData : [],
      lateCount : 0,
      /*-- 결석 --*/
      absenceData : [],
      absenceCount : 0,
      /*-- 관심학생 --*/
      loveStudentData : [],
      loveStudentCount : 0,
      /*-- 등교 --*/
      attendanceData : [],
      attendanceCount : 0,
      /*-- 하교 --*/
      returnHomeData : [],
      returnHomeCount : 0,
    }
  },
  mounted() {
      this.getData();
      setInterval(this.getData, 2500);
  },
  methods: {
    getData() {
      axios.get('/tutor/attendance/today')
      .then((response) => {
        // 지각자
        this.lateData = response.data.message.lateness;
        if(this.lateData == null){
          this.lateCount        = 0;
        } else {
          this.lateCount        = this.lateData.length;
        }

        // 결석자
        this.absenceData        = response.data.message.absence;
        if(this.absenceData == null){
          this.absenceCount     = 0;
        }else{
          this.absenceCount     = this.absenceData.length;
        }

        // 관심
        this.loveStudentData    = response.data.message.need_care;
        if(this.loveStudentData == null){
          this.loveStudentCount = 0;
        } else {
          this.loveStudentCount = this.loveStudentData.length;
        }

        // 등교
        this.attendanceData     = response.data.message.sign_in;
        if(this.attendaceData == null){
          this.attendanceCount  = 0;
        } else {
          this.attendanceCount  = this.attendanceData.length;
        }

        // 하교
        this.returnHomeData     = response.data.message.sign_out;
        if(this.returnHomeData == null){
          this.returnHomeCount  = 0;
        } else {
          this.returnHomeCount  = this.returnHomeData.length;
        }

      }).catch((error) => {
        console.log(error);
      });
    }
  }
}
</script>
