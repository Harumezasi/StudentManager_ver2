<template>
  <div class = "StudentAnalyticPredictionSetting">


    <!-- Header -->
    <v-parallax src="/images/analyticPredition.jpg" height="300">
      <h1 class="categoryAnalyticSetting">Student Analysis Settings</h1>
    </v-parallax>

    <!-- 학생 분류 기준 설정 컨텐츠 카드 (출석,학업) -->
    <v-container grid-list-xl>
      <v-layout row wrap align-center>

        <div class="topDiv_SettingArea">

          <!-- 출석 분류 기준 설정 영역 -->
          <div class="leftDiv_SettingArea">

            <!-- 출석 타이틀 카드 -->
            <div class="leftDiv_titleBox">
              <v-card class = "attendanceSettingTitleBox">
                <v-card-text style="padding-bottom: 5px;">
                  <h2 style="color: white">出席</h2>
                  <p>遅刻、早退、欠席の学生</p>
                </v-card-text>
              </v-card>
            </div>

            <v-card class="attendanceSettingCard">

              <!-- 출석 : 기간 설정 영역 -->
              <v-card-text>
                <v-container class="periodBox" fluid>
                  <v-layout row>
                    <v-flex xs3>
                      <h2 class = "period">期間</h2>
                    </v-flex>
                    <v-flex xs9>
                      <v-text-field
                       style="margin-top: 40px"
                       label="일"
                       hint="* システムが学生の最近の出席の状況を分析するための期間です。"
                       type="number"
                       min=0
                       max=365
                       persistent-hint
                       v-model="settingData['ada_search_period']"
                     ></v-text-field>
                    </v-flex>
                  </v-layout>
                </v-container>
              </v-card-text>

              <!-- 출석 : 각 항목 별 (지각, 조퇴, 결석) 빈도 수 기입 영역 -->
              <v-card-text>
                <v-container class = "frequencyBox" fluid>
                  <!-- 지각 횟수 기입 영역 -->
                  <v-layout row>
                    <v-flex xs3>
                      <v-btn depressed fab dark color="lime accent-4" >
                        <v-icon dark>directions_run</v-icon>
                      </v-btn>
                      <h2 class = "frequency">遅刻</h2>
                    </v-flex>
                    <v-flex xs5>
                      <v-text-field
                       style="margin-left: 20px"
                       label="回"
                       type="number"
                       v-model="settingData['lateness_count']"
                     ></v-text-field>
                     <h2 class = "frequency_2">以上の場合</h2>
                    </v-flex>
                  </v-layout>
                  <!-- 조퇴 횟수 기입 영역-->
                  <v-layout row>
                    <v-flex xs3>
                      <v-btn depressed fab dark color="cyan accent-4" >
                        <v-icon dark>local_hotel</v-icon>
                      </v-btn>
                      <h2 class = "frequency">早退</h2>
                    </v-flex>
                    <v-flex xs5>
                      <v-text-field
                       style="margin-left: 20px"
                       label="回"
                       type="number"
                       v-model="settingData['early_leave_count']"
                     ></v-text-field>
                     <h2 class = "frequency_2">以上の場合</h2>
                    </v-flex>
                  </v-layout>
                  <!-- 결석 횟수 기입 영역 -->
                  <v-layout row>
                    <v-flex xs3>
                      <v-btn depressed fab dark color="red" >
                        <v-icon dark>close</v-icon>
                      </v-btn>
                      <h2 class = "frequency">欠席</h2>
                    </v-flex>
                    <v-flex xs5>
                      <v-text-field
                       style="margin-left: 20px"
                       label="回"
                       type="number"
                       v-model="settingData['absence_count']"
                     ></v-text-field>
                     <h2 class = "frequency_2">以上の場合</h2>
                    </v-flex>
                  </v-layout>
                </v-container>
                <!-- 취소 / 저장 버튼 -->
                <v-card-actions>
                  <v-spacer></v-spacer>
                  <v-btn color="warning" @click="setResetDatas()">リセット</v-btn>
                  <v-btn color="primary" @click="checkSettingDatas()">セーブ</v-btn>
                </v-card-actions>
              </v-card-text>
            </v-card>
          </div>


          <!-- 학업 분류 기준 설정 영역 -->
          <div class="rightDiv_SettingArea">

            <!-- 학업 타이틀 카드 -->
            <div class="rightDiv_titleBox">
              <v-card class = "gradeSettingTitleBox">
                <v-card-text style="padding-bottom: 5px;">
                  <h2 style="color: white">学業</h2>
                  <p>中間、期末、課題、テストなど</p>
                </v-card-text>
              </v-card>
            </div>

            <v-card class="gradeSettingCard">
              <!-- 학업 : 분석 기간 설정 영역 -->
              <v-card-text>
                <v-container class = "standardBox" fluid>
                  <v-layout row>

                    <!-- 학생의 평소 학업 성취 현황 판단을 위한 기간 입력 영역 -->
                    <v-flex xs2>
                      <h2 class = "standard">普段</h2>
                    </v-flex>
                    <v-flex xs4>
                      <v-text-field
                       style="margin-top: 40px"
                       label="回"
                       type="number"
                       hint="システムが学生の普段の学業成就を分析するための期間です。"
                       persistent-hint
                       v-model="settingData['study_usual']"
                     ></v-text-field>
                    </v-flex>

                    <!-- 학생의 최근 학업 성취 현황 판단을 위한 기간 입력 영역 -->
                    <v-flex xs2>
                      <h2 class = "standard">最近</h2>
                    </v-flex>
                    <v-flex xs4>
                      <v-text-field
                       style="margin-top: 40px"
                       label="回"
                       type="number"
                       hint="システムが学生の最近の学業成就を分析するための期間です。"
                       persistent-hint
                       v-model="settingData['study_recent']"
                     ></v-text-field>
                    </v-flex>
                  </v-layout>
                </v-container>
              </v-card-text>

              <!-- 학업 : 하위권 판단을 위한 데이터 입력 영역 -->
              <v-card-text>
                <v-flex xs12>
                  <v-container class = "lowRankBox" grid-list-xl>
                    <v-layout row wrap align-center>

                      <!-- 하위권 표시 아이콘, 타이틀 영역 -->
                      <v-flex xs12 md3>
                        <v-btn depressed fab dark color="yellow accent-4" >
                          <v-icon dark>trending_down</v-icon>
                        </v-btn>
                      </v-flex>
                      <v-flex xs12 md9>
                        <h1 class = "lowRankTitle">下位圏</h1>
                      </v-flex>

                      <!-- 백분율 입력 영역 -->
                      <v-flex xs12 md6>
                        <h2 class = "lowRankText">席次パーセンテージが下位</h2>
                      </v-flex>
                      <v-flex xs12 md4>
                        <v-text-field
                          label="%"
                          type="number"
                          min=0
                          max=100
                          v-model="settingData['low_reflection']"
                        ></v-text-field>
                      </v-flex>
                      <v-flex xs12 md2>
                        <h2 class = "lowRankText">未満</h2>
                      </v-flex>

                      <!-- 반 평균 대비 점수 입력 영역 -->
                      <v-flex xs12 md5>
                        <h2 class = "lowRankText">クラスの平均に比べ</h2>
                      </v-flex>
                      <v-flex xs12 md3>
                        <v-text-field
                          label="点"
                          type="number"
                          v-model="settingData['low_score']"
                        ></v-text-field>
                      </v-flex>
                      <v-flex xs12 md4>
                        <h2 class = "lowRankText">以上の差</h2>
                      </v-flex>
                    </v-layout>
                  </v-container>
                </v-flex>
              </v-card-text>

              <!-- 학업 : 최근 문제 발생 학생 수집을 위한 데이터 입력 영역 -->
              <v-card-text>
                <v-flex xs12>
                  <v-container class = "recentProblemsBox" grid-list-xl>
                    <v-layout row wrap align-center>

                      <!-- 최근 문제 발생 표시 아이콘, 타이틀 영역 -->
                      <v-flex xs12 md3>
                        <v-btn depressed fab dark color="deep-orange accent-3" >
                          <v-icon dark>warning</v-icon>
                        </v-btn>
                      </v-flex>
                      <v-flex xs12 md9>
                        <h1 class = "recentProblemsTitle">最近問題発生</h1>
                      </v-flex>

                      <!-- 석차 백분율 하락치 입력 영역 -->
                      <v-flex xs12 md6>
                        <h2 class = "recentProblemsText">席次パーセンテージが</h2>
                      </v-flex>
                      <v-flex xs12 md4>
                        <v-text-field
                          label="%"
                          type="number"
                          min=0
                          max=100
                          v-model="settingData['recent_reflection']"
                        ></v-text-field>
                      </v-flex>
                      <v-flex xs12 md2>
                        <h2 class = "recentProblemsText_2">下落</h2>
                      </v-flex>

                      <!-- 평소 본인의 평균 대비 하락한 점수 입력 영역 -->
                      <v-flex xs12 md6>
                        <h2 class = "recentProblemsText">学生自信の平均に比べ</h2>
                      </v-flex>
                      <v-flex xs12 md4>
                        <v-text-field
                          label="点"
                          type="number"
                          v-model="settingData['recent_score']"
                        ></v-text-field>
                      </v-flex>
                      <v-flex xs12 md2>
                        <h2 class = "recentProblemsText_2">下落</h2>
                      </v-flex>
                    </v-layout>
                  </v-container>
                </v-flex>
              </v-card-text>
            </v-card>
          </div>
        </div>
      </v-layout>
    </v-container>
  </div>
</template>

<script>

export default {
  data(){
    return {
      settingData : []
    }
  },
  methods : {
    /* 리셋 */
    setResetDatas(){
      /* 다시 불러온다 */
      this.getSettingDatas()
    },
    /* 설정 값 가져오기 */
    getSettingDatas(){
      axios.get('/tutor/analyse/select_criteria')
      .then((response)=>{
        this.settingData = response.data.message;
      })
      .catch((error)=>{
        console.log("getSetting Err :" + error);
      })
    },
    /* 값의 유효성 확인 */
    checkSettingDatas(){
      let checked = true;

      /* 기간 <= 365 */
      if(this.settingData['ada_search_period'] > 365 && this.settingData['ada_search_period'] < 1){
        alert('기간 : 기간은 [1]일~[365]일 내로 설정하셔야합니다.');
        checked = false;
      }
      /* 학업 - 평균 >= 최근 */
      else if(this.settingData['study_usual'] < this.settingData['study_recent']){
        alert('학업 : [최근 기간]이 [평소 기간]보다 클 수 없습니다.');
        checked = false;
      }else if(this.settingData['study_usual'] < 1 ){
        alert('학업 : [평소 기간]의 횟수는 최소 [1]회 이상이여야 합니다.');
        checked = false;
      }else if(this.settingData['study_recent'] < 1){
        alert('학업 : [최근 기간]의 횟수는 최소 [1]회 이상이여야 합니다.');
        checked = false;
      }
      /* 석차백분율 <= 100% */
      else if(this.settingData['low_reflection'] > 100){
        alert('하위권 : 석차백분율이 [100%]를 초과할 수 없습니다.');
        checked = false;
      }
      else if(this.settingData['recent_reflection'] > 100){
        alert('최근 : 석차백분율이 [100%]를 초과할 수 없습니다.');
        checked = false;
      }

      /* 최종확인 */
      if(checked){
        this.setSaveDatas();
      }
    },
    /* 변경한 설정 값 저장 */
    setSaveDatas(){
      axios.post('/tutor/analyse/update_criteria', this.settingData)
      .then((response) => {
        alert(response.data.message)
      })
      .catch((error) => {
        console.log("setSave Err :" + error);
      })
    }
  },
  mounted(){
    this.getSettingDatas();
  }
}

</script>

<style>

.topDiv_SettingArea {
  width : 100%;
  position:relative;
  bottom : 190px;
  left : 30px;
}

.leftDiv_SettingArea {
  width : 45%;
  float:left;
  margin: 0px 15px 0px 15px;
}

.leftDiv_titleBox {
  position: relative;

  top : 60px;
  left : 50px;
}

.rightDiv_SettingArea {
  width : 45%;
  float:left;
  margin: 0px 15px 0px 15px;
}

.rightDiv_titleBox {
  position: relative;

  top : 60px;
  left : 50px;
}

/*-- 헤더 영역 --*/
.categoryAnalyticSetting {
    color: #FFFFFF;
    font-size: 40px;
    font-family: "Montserrat";
    font-weight: Bold;
    position: relative;
    left: 90px;
}

/*-- 출석 분류 기준 설정 카드 --*/
.attendanceSettingCard {
  border-radius: 0.2975rem;
  box-shadow: 0 4px 12px 0 rgba(161, 161, 161, 0.36);
  width: 100%;
}

.attendanceSettingTitleBox {
  border-radius: 0.3975rem;
  z-index: 2;
  box-shadow:  0 4px 12px 0 rgba(97, 97, 97, 0.36);
  background: linear-gradient(to bottom right, rgb(132, 159, 213), rgb(61, 96, 241));
  width: 80%;
}

.periodBox {
  border-bottom: 1px solid;
  border-color: rgba(187, 187, 187, 0.73);
}
  .period {
    font-family: "Mplus 1p";
    font-weight: normal;
    color: black;
    margin: 65px 0 0 20px;
  }

.frequency {
  font-family: "Mplus 1p";
  font-weight: normal;
  color: black;
  left: 85px;
  bottom: 56px;
  position: relative;
}
.frequency_2 {
  font-family: "Mplus 1p";
  font-weight: normal;
  color: black;
  left: 230px;
  bottom: 55px;
  position: relative;
}

/*-- 학업 분류 기준 설정 카드 --*/
.gradeSettingCard {
  border-radius: 0.2975rem;
  box-shadow: 0 4px 12px 0 rgba(161, 161, 161, 0.36);
  z-index: 1;
  width: 100%;
}

.gradeSettingTitleBox {
  border-radius: 0.3975rem;
  z-index: 2;
  box-shadow:  0 4px 12px 0 rgba(97, 97, 97, 0.36);
  background: linear-gradient(to bottom right, rgb(132, 159, 213), rgb(61, 96, 241));
  width: 80%;
}

.standardBox {
  border-bottom: 1px solid;
  border-color: rgba(187, 187, 187, 0.73);
}
  .standard {
    font-family: "Mplus 1p";
    font-weight: normal;
    color: black;
    margin: 57px 0 0 20px;
  }

.lowRankBox {
  position: relative;
  bottom: 20px;
  border-bottom: 1px solid;
  border-color: rgba(187, 187, 187, 0.73);
}

 .lowRankTitle {
   font-family: "Mplus 1p";
   font-weight: normal;
   position: relative;
   right: 35px;
 }
 .lowRankText {
   font-family: "Mplus 1p";
   font-weight: normal;
   font-size: 17px;
 }


.recentProblemsBox {
  position: relative;
  bottom: 40px;
}
  .recentProblemsTitle {
    font-family: "Mplus 1p";
    font-weight: normal;
    position: relative;
    right: 35px;
  }
  .recentProblemsText {
    font-family: "Mplus 1p";
    font-weight: normal;
    font-size: 17px;
  }
  .recentProblemsText_2 {
    font-family: "Mplus 1p";
    font-weight: normal;
    font-size: 17px;
  }


</style>
