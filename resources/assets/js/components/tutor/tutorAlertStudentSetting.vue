<template>
  <div class = "notificationsPage">

      <div class="panel-header">
        <div class="header text-center">
          <v-layout class = "imgTitle" column align-center justify-center>
            <h1 class="category1">Attendance Management</h1>
            <p class="category">Notification Settings</p>
          </v-layout>
        </div>
      </div>

    <!-- 알림추가 영역 -->

    <v-container grid-list-xl>
      <v-flex xs12>
        <v-card class="notificationsAddBox elevation-2" color = "white">
        <v-container grid-list-xl>
          <v-layout row wrap align-center>

            <v-card-text>
              <h1 class = "cardInsideTitle">알림추가</h1>
            </v-card-text>

            <!-- 기간 설정 (ex. 일주일, 한달) 부분 -->
            <v-flex xs2 text-xs-center class="fontSetting">
              <v-select :items="period" v-model="set_days" label="Select" single-line></v-select>동안
            </v-flex>

            <!-- 상태 (ex. 출석, 결석 등) -->
            <v-flex xs2 text-xs-center class="fontSetting">
              <v-select :items="attendance" v-model="set_ada" label="Select" single-line></v-select>을
            </v-flex>

            <!-- 빈도 (ex. 연속, 누적) -->
            <v-flex xs2 text-xs-center>
              <v-select :items="frequency" v-model="set_continuative" label="Select" single-line></v-select>
            </v-flex>

            <!-- 횟수 (ex. 3회, 4회) -->
            <v-flex xs2 text-xs-center class="fontSetting">
              <v-text-field
                v-model="set_count"
                id="num"
                >
              </v-text-field>
              이상 했을 시,
            </v-flex>

            <!-- 알림 보낼 대상 설정 (ex. 교수, 학생) -->
            <v-flex xs2 text-xs-center class="fontSetting">
              <v-select :items="noticeTarget" v-model="set_alert_std" label="Select" single-line></v-select>
              에게 알림
            </v-flex>

            <!-- 알림 추가 버튼 -->
            <v-flex xs2 text-xs-center>
              <v-btn color="primary" v-on:click="setAlert()" class="fontSetting">추가</v-btn>
            </v-flex>
          </v-layout>
        </v-container>
      </v-card>
      </v-flex>
    </v-container>


    <!-- 알림 확인 부분은 위에 데이터가 전달되야 뜨는건데 어떻게 해야할지 몰라서
    일단 형태만 냅둠 -->
    <v-container grid-list-xl>
      <v-flex xs12>
        <v-card class="notificationsConfirmBox elevation-2" color = "white">
          <v-container grid-list-xl>
            <v-layout row wrap align-center>

              <v-card-text>
                <h1 class = "cardInsideTitle">알림확인</h1>
              </v-card-text>

              <div
                class = "setAlertDataArea"
                v-for = "setAlertData in settingAlertData"
              >
                <v-flex xs12 md8 class="fontSetting">
                  {{ setAlertData.alert_condition }}
                  <v-btn color = "red" v-on:click="delAlert(setAlertData.alert_id)" style="color:white" class="fontSetting">삭제</v-btn>
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
body {
  background-color: rgb(255, 255, 255);
}

.fontSetting {
  font-size: 25px;
  font-style: 'Gothic A1';
}

#fontSetting td {
  font-size: 25px;
  font-style: 'Gothic A1';
}

.setAlertDataArea {
  width: 100%
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
.notificationsAddBox {
  border-radius: 0.2975rem;
  position: relative;
  z-index: 1;
  bottom: 50px;
}
.notificationsConfirmBox {
  border-radius: 0.2975rem;
  position: relative;
  z-index: 2;
  bottom: 60px;
}
.cardInsideTitle {
  font-family: "Nanum Gothic Coding";
  font-weight: lighter;
  border-bottom: 1px solid;
  padding-bottom: 6px;
  border-color: rgba(187, 187, 187, 0.73);
}
</style>

<script>
export default {
  data() {
    return {
      /*-- 기간 --*/
      period: [{
          text: '일주일',
          value : 7
        },
        {
          text: '1개월',
          value : 30
        },
        {
          text: '2개월',
          value : 60
        }
      ],
      /*-- 상태 --*/
      attendance: [
        {
          text: '지각',
          value : 'lateness'
        },
        {
          text: '조퇴',
          value : 'early_leave'
        },
        {
          text: '결석',
          value : 'absence'
        }
      ],
      /*-- 빈도 --*/
      frequency: [{
          text: '연속',
          value : true
        },
        {
          text: '누적',
          value : false
        }
      ],
      /*-- 알림대상 --*/
      noticeTarget: [{
          text: '교수(나)',
          value : false
        },
        {
          text: '교수(나)와 학생',
          value : true
        }
      ],
      /* 설정할 알림 데이터 값 */
      set_days : null,
      set_ada : null,
      set_continuative : null,
      set_count : null,
      set_alert_std : null,
      /* 설정된 알림 데이터 */
      settingAlertData : []
    }
  },
  methods : {
    setAlert() {
      axios.post('/tutor/attendance/care/insert',{
          days_unit : this.set_days,
          ada_type : this.set_ada,
          continuative_flag : this.set_continuative,
          count : this.set_count,
          alert_std_flag : this.set_alert_std
      })
      .then((response) => {
        /* 추가 후 알림 내역 업데이트를 위해 getAlert를 호출 */
        this.getAlert();
      })
      .catch((error) => { console.log('setAlert Error!!! : ' + error);})
    },
    getAlert() {
      axios.get('/tutor/attendance/care/select')
      .then((response) => {
        /* 중복을 막기위해 초기화한다. */
        this.settingAlertData = [];
        /* 알맞은 메세지를 출력하도록 가공한다. */
        let setMessageData = [];
        for(let start = 0; start < response.data.message.length; start++){
          /* 기간 */
          switch (response.data.message[start].days_unit) {
            case 7 :
              setMessageData[start] = start+1 +". 일주일 간 ";
              break;
            case 30 :
              setMessageData[start] = start+1 +". 1개월 간 ";
              break;
            case 60 :
              setMessageData[start] = start+1 +". 2개월 간 ";
              break;
          }
          /* 필터링 타입 */
          switch (response.data.message[start].ada_type) {
            case "lateness":
              setMessageData[start] += "지각을 ";
              break;
            case "absence":
              setMessageData[start] += "결석을 ";
              break;
            case "early_leave":
              setMessageData[start] += "조퇴를 ";
              break;
          }
          /* 2차 필터링 타입 */
          if(response.data.message[start].continuative_flag){
            setMessageData[start] += "연속 ";
          }else if(!response.data.message[start].continuative_flag){
            setMessageData[start] += "누적 ";
          }
          /* 횟수 */
          setMessageData[start] += response.data.message[start].count + "회 이상 했을 시, ";
          /* 알림 대상 */
          if(response.data.message[start].alert_std_flag){
            setMessageData[start] += "교수(나)와 학생에게 알림.";
          }else if(!response.data.message[start].alert_std_flag){
            setMessageData[start] += "교수(나)에게 알림.";
          }
          /* 알림 목록 데이터에 추가 : 삭제를 위한 아이디 값도 추가 */
          /* this.$set 의 경우, 없는 주소를 참조할 수 없으므로 .push를 이용하여 array주소를 생성하는 편법을 사용한다. */
          this.settingAlertData.push({'alert_id' : response.data.message[start].id});
          this.$set(this.settingAlertData[start], 'alert_condition', setMessageData[start]);
        }
      })
      .catch((error) => { console.log('getAlert Error!!! : ' + error);})
    },
    delAlert(delAlert_id) {
      axios.post('/tutor/attendance/care/delete',{
          alert_id : delAlert_id
      })
      .then((response) => {
        /* 삭제 후 알림 내역 업데이트를 위해 getAlert를 호출 */
        this.getAlert();
      })
      .catch((error) => { console.log('delAlert Error!!! : ' + error);})
    }
  },
  mounted() {
    this.getAlert();
  }
}
</script>
