<template>
  <div class = "notificationsPage">

      <v-parallax src="/images/studentManagement.png" height="300">
        <h1 class="category1">Notification Settings</h1>
      </v-parallax>


    <!-- 알림추가 영역 -->

    <v-container grid-list-xl>
      <v-flex xs12>
        <v-card class="notificationsAddBox elevation-2" color = "white">
        <v-container grid-list-xl>
          <v-layout row wrap align-center>

            <v-card-text>
              <h1 class = "cardInsideTitle">お知らせの条件追加</h1>
            </v-card-text>

              <!-- 기간 설정 (ex. 일주일, 한달) 부분 -->
              <div class="settingBoxDivArea">
                <v-select class="selectDivWidth" :items="period" v-model="set_days" label="Select" single-line></v-select>
              </div>

              <div class="settingBoxDivArea tutorAlertFontSetting">
                間
              </div>

              <!-- 상태 (ex. 출석, 결석 등) -->
              <div class="settingBoxDivArea">
                <v-select class="selectDivWidth" :items="attendance" v-model="set_ada" label="Select" single-line></v-select>
              </div>

              <div class="settingBoxDivArea tutorAlertFontSetting">
                を
              </div>

              <!-- 빈도 (ex. 연속, 누적) -->
              <div class="settingBoxDivArea">
                <v-select class="selectDivWidth" :items="frequency" v-model="set_continuative" label="Select" single-line></v-select>
              </div>

              <!-- 횟수 (ex. 3회, 4회) -->
              <div class="settingBoxDivArea">
                <v-text-field class="selectDivWidth_shot" label="「数字」" v-model="set_count" maxlength="2"　id="num"></v-text-field>
              </div>

              <div class="settingBoxDivArea tutorAlertFontSetting">
                回以上した場合
              </div>

              <!-- 알림 보낼 대상 설정 (ex. 교수, 학생) -->
              <div class="settingBoxDivArea">
                <v-select class="selectDivWidth_long" :items="noticeTarget" v-model="set_alert_std" label="Select" single-line></v-select>
              </div>

              <div class="settingBoxDivArea tutorAlertFontSetting">
                にお知らせ
              </div>

              <!-- 알림 추가 버튼 -->
              <div class="settingBoxDivArea">
                <v-btn color = "blue accent-2" style="color:white;" v-on:click="setAlert()" class="tutorAlertFontSetting">追加</v-btn>
              </div>

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
                <h1 class = "cardInsideTitle">お知らせの条件管理</h1>
              </v-card-text>

              <div
                class = "setAlertDataArea"
                v-for = "setAlertData in settingAlertData"
              >
                <v-flex xs12 md8 class="tutorAlertFontSetting">
                  {{ setAlertData.alert_condition }}
                  <v-btn color = "red" v-on:click="delAlert(setAlertData.alert_id)" style="color:white" class="tutorAlertFontSetting">消す</v-btn>
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

.selectDivWidth_shot {
  width : 5em;
}

.selectDivWidth {
  width : 8em;
}

.selectDivWidth_long {
  width : 13em;
}

.settingBoxDivArea {
  float:left;
  margin: 5px;
}

.tutorAlertFontSetting {
  font-size: 20px;
  font-weight: normal;
  font-family: "Mplus 1p";
}

#tutorAlertFontSetting td {
  font-size: 18px;
  font-weight: normal;
  font-family: "Mplus 1p";
}

.setAlertDataArea {
  width: 100%
}

.category1 {
    color: #FFFFFF;
    font-size: 40px;
    font-family: "Montserrat";
    font-weight: Bold;
    position: relative;
    left: 50px;
    top: 40px;
}

.notificationsAddBox {
  border-radius: 0.2975rem;
  position: relative;
  z-index: 1;
  bottom: 100px;
}
.notificationsConfirmBox {
  border-radius: 0.2975rem;
  position: relative;
  z-index: 2;
  bottom: 120px;
}
.cardInsideTitle {
  font-family: "Mplus 1p";
  font-weight: bold;
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
          text: '一週',
          value : 7
        },
        {
          text: '１ヶ月',
          value : 30
        },
        {
          text: '２ヶ月',
          value : 60
        }
      ],
      /*-- 상태 --*/
      attendance: [
        {
          text: '遅刻',
          value : 'lateness'
        },
        {
          text: '早退',
          value : 'early_leave'
        },
        {
          text: '欠席',
          value : 'absence'
        }
      ],
      /*-- 빈도 --*/
      frequency: [{
          text: '連続',
          value : true
        },
        {
          text: '累積',
          value : false
        }
      ],
      /*-- 알림대상 --*/
      noticeTarget: [{
          text: '教授(自分)',
          value : false
        },
        {
          text: '教授(自分)と学生',
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
              setMessageData[start] = start+1 +". 一週間、";
              break;
            case 30 :
              setMessageData[start] = start+1 +". １ヶ月間、";
              break;
            case 60 :
              setMessageData[start] = start+1 +". ２ヶ月間、";
              break;
          }
          /* 필터링 타입 */
          switch (response.data.message[start].ada_type) {
            case "lateness":
              setMessageData[start] += "遅刻を";
              break;
            case "absence":
              setMessageData[start] += "欠席を";
              break;
            case "early_leave":
              setMessageData[start] += "早退を";
              break;
          }
          /* 2차 필터링 타입 */
          if(response.data.message[start].continuative_flag){
            setMessageData[start] += "連続　";
          }else if(!response.data.message[start].continuative_flag){
            setMessageData[start] += "累積　";
          }
          /* 횟수 */
          setMessageData[start] += response.data.message[start].count + "　回以上した場合、";
          /* 알림 대상 */
          if(response.data.message[start].alert_std_flag){
            setMessageData[start] += "教授(自分)と学生にお知らせ。";
          }else if(!response.data.message[start].alert_std_flag){
            setMessageData[start] += "教授(自分)にお知らせ。";
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
