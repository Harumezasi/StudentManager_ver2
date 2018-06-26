<template>
  <div class = "StudentAnalyticPredictionSetting">


    <!-- Header -->
    <v-parallax src="/images/analyticPredition.jpg" height="300">
      <h1 class="categoryAnalyticSetting">Student Analysis Settings</h1>
    </v-parallax>

    <!-- 학생 분류 기준 설정 타이틀 카드 ( 출석과 학업 ) -->
    <v-flex xs12>
      <v-container class = "titleCard" grid-list-xl>
        <v-layout row wrap align-center>

          <!-- 출석 타이틀 카드 -->
          <v-flex xs12 md5>
            <v-card class = "attendanceSettingTitleBox">
              <v-card-text style="padding-bottom: 5px;">
                <h2 style="color: white">출석</h2>
                <p>지각, 조퇴, 결석 학생</p>
              </v-card-text>
            </v-card>
          </v-flex>

          <!-- 학업 타이틀 카드 -->
          <v-flex xs12 md5>
            <v-card class = "gradeSettingTitleBox">
              <v-card-text style="padding-bottom: 5px;">
                <h2 style="color: white">학업</h2>
                <p>중간, 기말, 과제, 쪽지시험 등</p>
              </v-card-text>
            </v-card>
          </v-flex>

        </v-layout>
      </v-container>
    </v-flex>



    <!-- 학생 분류 기준 설정 컨텐츠 카드 (출석,학업) -->
    <v-flex xs12>
      <v-container class = "settingCard" grid-list-xl>
        <v-layout row wrap align-center>

          <!-- 출석 분류 기준 설정 영역 -->
          <v-flex xs12 md6>
            <v-card class="attendanceSettingCard">

              <!-- 출석 : 기간 설정 영역 -->
              <v-card-text>
                <v-container class = "periodBox" fluid>
                  <v-layout row>
                    <v-flex xs3>
                      <h2 class = "period">기간</h2>
                    </v-flex>
                    <v-flex xs9>
                      <v-text-field
                       style="margin-top: 40px"
                       label="일"
                       hint="* 시스템이 학생의 최근 출석 현황을 분석하기 위한 기간 입니다."
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
                      <h2 class = "frequency">지각</h2>
                    </v-flex>
                    <v-flex xs5>
                      <v-text-field
                       style="margin-left: 20px"
                       label="회"
                       type="number"
                       v-model="settingData['lateness_count']"
                     ></v-text-field>
                     <h2 class = "frequency_2">이상 일 때</h2>
                    </v-flex>
                  </v-layout>
                  <!-- 조퇴 횟수 기입 영역-->
                  <v-layout row>
                    <v-flex xs3>
                      <v-btn depressed fab dark color="cyan accent-4" >
                        <v-icon dark>local_hotel</v-icon>
                      </v-btn>
                      <h2 class = "frequency">조퇴</h2>
                    </v-flex>
                    <v-flex xs5>
                      <v-text-field
                       style="margin-left: 20px"
                       label="회"
                       type="number"
                       v-model="settingData['early_leave_count']"
                     ></v-text-field>
                     <h2 class = "frequency_2">이상 일 때</h2>
                    </v-flex>
                  </v-layout>
                  <!-- 결석 횟수 기입 영역 -->
                  <v-layout row>
                    <v-flex xs3>
                      <v-btn depressed fab dark color="red" >
                        <v-icon dark>close</v-icon>
                      </v-btn>
                      <h2 class = "frequency">결석</h2>
                    </v-flex>
                    <v-flex xs5>
                      <v-text-field
                       style="margin-left: 20px"
                       label="회"
                       type="number"
                       v-model="settingData['absence_count']"
                     ></v-text-field>
                     <h2 class = "frequency_2">이상 일 때</h2>
                    </v-flex>
                  </v-layout>
                </v-container>
                <!-- 취소 / 저장 버튼 -->
                <v-card-actions>
                  <v-spacer></v-spacer>
                  <v-btn color="warning" @click="setResetDatas()">되돌리기</v-btn>
                  <v-btn color="primary" @click="checkSettingDatas()">저장</v-btn>
                </v-card-actions>
              </v-card-text>
            </v-card>
          </v-flex>


          <!-- 학업 분류 기준 설정 영역 -->
          <v-flex xs12 md6>
            <v-card class="gradeSettingCard">

              <!-- 학업 : 분석 기간 설정 영역 -->
              <v-card-text>
                <v-container class = "standardBox" fluid>
                  <v-layout row>

                    <!-- 학생의 평소 학업 성취 현황 판단을 위한 기간 입력 영역 -->
                    <v-flex xs2>
                      <h2 class = "standard">평소</h2>
                    </v-flex>
                    <v-flex xs4>
                      <v-text-field
                       style="margin-top: 40px"
                       label="회"
                       type="number"
                       hint="시스템이 학생의 평소 학업 성취 현황을 판단하기 위해 분석하는 기간 입니다."
                       persistent-hint
                       v-model="settingData['study_usual']"
                     ></v-text-field>
                    </v-flex>

                    <!-- 학생의 최근 학업 성취 현황 판단을 위한 기간 입력 영역 -->
                    <v-flex xs2>
                      <h2 class = "standard">최근</h2>
                    </v-flex>
                    <v-flex xs4>
                      <v-text-field
                       style="margin-top: 40px"
                       label="회"
                       type="number"
                       hint="시스템이 학생의 최근 학업 성취 상태를 판단하기 위해 분석하는 기간 입니다."
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
                        <h1 class = "lowRankTitle">하위권</h1>
                      </v-flex>

                      <!-- 백분율 입력 영역 -->
                      <v-flex xs12 md6>
                        <h2 class = "lowRankText">석차 백분율이 하위</h2>
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
                        <h2 class = "lowRankText">미만</h2>
                      </v-flex>

                      <!-- 반 평균 대비 점수 입력 영역 -->
                      <v-flex xs12 md5>
                        <h2 class = "lowRankText">반 평균 대비</h2>
                      </v-flex>
                      <v-flex xs12 md3>
                        <v-text-field
                          label="점"
                          type="number"
                          v-model="settingData['low_score']"
                        ></v-text-field>
                      </v-flex>
                      <v-flex xs12 md4>
                        <h2 class = "lowRankText">이상 차이</h2>
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
                        <h1 class = "recentProblemsTitle">최근 문제 발생</h1>
                      </v-flex>

                      <!-- 석차 백분율 하락치 입력 영역 -->
                      <v-flex xs12 md6>
                        <h2 class = "recentProblemsText">석차 백분율이</h2>
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
                        <h2 class = "recentProblemsText_2">하락</h2>
                      </v-flex>

                      <!-- 평소 본인의 평균 대비 하락한 점수 입력 영역 -->
                      <v-flex xs12 md6>
                        <h2 class = "recentProblemsText">본인 평균 대비</h2>
                      </v-flex>
                      <v-flex xs12 md4>
                        <v-text-field
                          label="점"
                          type="number"
                          v-model="settingData['recent_score']"
                        ></v-text-field>
                      </v-flex>
                      <v-flex xs12 md2>
                        <h2 class = "recentProblemsText_2">하락</h2>
                      </v-flex>
                    </v-layout>
                  </v-container>
                </v-flex>
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
  position: relative;
  bottom: 380px;
  border-radius: 0.2975rem;
  margin: 20px 0 0 0;
  box-shadow: 0 4px 12px 0 rgba(161, 161, 161, 0.36);
}
.attendanceSettingTitleBox {
  border-radius: 0.3975rem;
  position: relative;
  z-index: 2;
  left: 43px;
  bottom: 130px;
  box-shadow:  0 4px 12px 0 rgba(97, 97, 97, 0.36);
  background: linear-gradient(-30deg, rgb(70, 90, 145), rgb(154, 173, 249));

}
.periodBox {
  border-bottom: 1px solid;
  border-color: rgba(187, 187, 187, 0.73);
}
  .period {
    font-family: "Nanum Gothic Coding";
    font-weight: lighter;
    color: black;
    margin: 65px 0 0 20px;
  }
.frequency {
  font-family: "Nanum Gothic Coding";
  font-weight: lighter;
  color: black;
  left: 85px;
  bottom: 56px;
  position: relative;
}
.frequency_2 {
  font-family: "Nanum Gothic Coding";
  font-weight: lighter;
  color: black;
  left: 230px;
  bottom: 55px;
  position: relative;
}

/*-- 학업 분류 기준 설정 카드 --*/
.gradeSettingCard {
  position: relative;
  bottom: 225px;
  border-radius: 0.2975rem;
  box-shadow: 0 4px 12px 0 rgba(161, 161, 161, 0.36);
  position: relative;
  z-index: 1;

}
.gradeSettingTitleBox {
  border-radius: 0.3975rem;
  position: relative;
  z-index: 2;
  left: 143px;
  bottom: 130px;
  box-shadow:  0 4px 12px 0 rgba(97, 97, 97, 0.36);
  background: linear-gradient(-60deg, rgb(70, 90, 145), rgb(154, 173, 249));
}
.standardBox {
  border-bottom: 1px solid;
  border-color: rgba(187, 187, 187, 0.73);
}
  .standard {
    font-family: "Nanum Gothic Coding";
    font-weight: lighter;
    color: black;
    margin: 65px 0 0 20px;
  }

.lowRankBox {
  position: relative;
  bottom: 20px;
  border-bottom: 1px solid;
  border-color: rgba(187, 187, 187, 0.73);
}
 .lowRankTitle {
   font-family: "Nanum Gothic Coding";
   font-weight: lighter;
   position: relative;
   right: 35px;
 }
 .lowRankText {
   font-family: "Nanum Gothic Coding";
   font-weight: lighter;
 }


.recentProblemsBox {
  position: relative;
  bottom: 40px;
}
  .recentProblemsTitle {
    font-family: "Nanum Gothic Coding";
    font-weight: lighter;
    position: relative;
    right: 35px;
  }
  .recentProblemsText {
    font-family: "Nanum Gothic Coding";
    font-weight: lighter;
  }
  .recentProblemsText_2 {
    font-family: "Nanum Gothic Coding";
    font-weight: lighter;
  }


</style>
