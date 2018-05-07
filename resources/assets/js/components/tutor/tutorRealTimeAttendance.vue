<template>

  <div>
    <!-- 상단 이미지 -->
    <div class="panel-header">
      <div class="header text-center">
        <v-layout class = "imgTitle" column align-center justify-center>
          <h2 class="title">Attendance Check</h2>
          <p class="category">Handcrafted by our friend</p>
        </v-layout>
      </div>
    </div>

    <!-- 상단 학생 구분 컬러 영역 -->
    <v-flex xs10>
      <v-container grid-list-xl>
        <v-layout row wrap align-center>
          <v-flex xs10 offset-xs1>
            <div class="attendanceType">
              <v-chip color="primary" text-color="white">등교</v-chip>
              <v-chip color="primary" text-color="white">{{attendanceCount}}</v-chip>
            </div>
            <v-chip color="green" text-color="white">하교</v-chip>
            <v-chip color="green" text-color="white">{{returnHomeCount}}</v-chip>
            <div class="attendanceType">
              <v-chip color="pink lighten-3" text-color="white">관심학생</v-chip>
              <v-chip color="pink lighten-3" text-color="white">{{loveStudentCount}}</v-chip>
            </div>
            <div class="attendanceType">
              <v-chip color="amber" text-color="white">지각</v-chip>
              <v-chip color="amber" text-color="white">{{lateCount}}</v-chip>
            </div>
            <div class="attendanceType">
              <v-chip color="red" text-color="white">결석</v-chip>
              <v-chip color="red" text-color="white">{{absenceCount}}</v-chip>
            </div>
          </v-flex>
        </v-layout>
      </v-container>
    </v-flex>

    <!-- 출결 현황 보기 영역 -->
    <v-flex xs12>
      <v-container grid-list-xl>
        <v-layout row wrap align-center>

          <!-- 지각 -->
          <v-flex xs12 md4>
            <v-card class="elevation-4 transparent">
              <v-card-text class="text-xs-center">
                <v-btn color="primary" depressed block class="attendanceTitle">지각</v-btn>
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
              <v-card-text class="text-xs-center">
                <v-btn color="primary" depressed block class="attendanceTitle">결석</v-btn>
              </v-card-text>
              <v-card-text class="studentInfoScreen attendanceInfoArea">
                <v-chip color = "red" v-for="absence in absenceData" :key="absence.name" class="studentInfoArea">
                  <v-card-title>
                    <div>
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
              <v-card-text class="text-xs-center">
                <v-btn color="primary" depressed block class="attendanceTitle">관심학생</v-btn>
              </v-card-text>
              <v-card-text class="studentInfoScreen attendanceInfoArea">
                <v-chip color = "pink lighten-3" v-for="loveStudent in loveStudentData" :key="loveStudent.name" class="studentInfoArea">
                  <v-card-title>
                    <div>
                      <span>{{ loveStudent.name }}</span><br>
                      <span>{{ loveStudent.reason }}</span><br>
                      <span>{{ loveStudent.sign_in_time }}</span>
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
      <v-container grid-list-xl>
        <v-layout row wrap align-center>

          <!-- 등교 -->
          <v-flex xs12 md6>
            <v-card class="elevation-4 transparent">
              <v-card-text class="text-xs-center">
                <v-btn color="primary" depressed block class="attendanceTitle">등교</v-btn>
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
              <v-card-text class="text-xs-center">
                <v-btn color="primary" depressed block class="attendanceTitle">하교</v-btn>
              </v-card-text>
              <v-card-text class="studentInfoScreen attendanceInfoArea">
                <v-chip color = "green" v-for="returnHome in returnHomeData" :key="returnHome.name" class="studentInfoArea">
                  <v-card-title>
                    <div>
                      <span>{{ returnHome.name }}</span><br>
                      <span>{{ returnHome.sign_in_time }}</span>
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

  .attendanceType {
    display: inline-block;
  }

  .attendanceTitle {
    font-size: 20px;
    font-weight: bold;
    height: 60px;
  }

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
                        if(response.data.status === true) {
                            // 서버와 통신 성공하고, 성공적으로 데이터를 획득했을 때
                            console.log(response);

                            // 지각자
                            this.lateData = response.data.message.lateness;
                            this.lateCount = response.data.message.lateness.length;

                            // 결석자
                            this.absenceData = response.data.message.absence;
                            this.absenceCount = response.data.message.absence.length;

                            // 관심
                            this.loveStudentData = response.data.message.need_care;
                            this.loveStudentCount = response.data.message.need_care.length;

                            // 등교
                            this.attendanceData = response.data.message.sign_in;
                            this.attendanceCount = response.data.message.lateness.length;

                            // 하교
                            this.returnHomeData = response.data.message.sign_out;
                            this.returnHomeCount = response.data.message.sign_out.length;

                            console.log('is new Data');
                        } else {
                            // 서버와 통신하였지만, 데이터를 못얻었을때
                        }
                    }).catch((error) => {
                    console.log(error);
                });
            }
        }
    }
</script>
