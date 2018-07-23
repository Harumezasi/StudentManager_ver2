<template>
  <div class = "StudentAnalyticPrediction">

    <!-- Header -->
    <v-parallax src="/images/analyticPredition.jpg" height="300">
      <h1 class="categoryStudentAnalytic">Student Analytic Predition</h1>
    </v-parallax>

    <!-- 기간 설정 영역 -->
    <v-dialog v-model="dialog" width="750px">
      <v-card>
        <v-card-title class="grey lighten-4 py-4 title" style="font-family:Mplus 1p">
         分析の期間設定
        </v-card-title>
        <!-- 분석 조건 설정 : 출석 -->
        <v-container grid-list-sm class="pa-4">
            <v-btn color="info" depressed round v-on:click="selectPeriod('recently')">最近</v-btn>
            <v-btn color="info" depressed round v-on:click="selectPeriod('weekly')">週間</v-btn>
            <v-btn color="info" depressed round v-on:click="selectPeriod('monthly')">月間</v-btn>
          <br>
          <v-layout row wrap>
           <v-flex xs10>
               <!-- 월간 -->
               <v-date-picker
                v-if="setPeriod_type == 'monthly'"
                v-model="fDate"
                type="month"
                min="2018-01"
               ></v-date-picker>
               <v-date-picker
                v-if="setPeriod_type == 'monthly'"
                v-model="sDate"
                type="month"
                min="2018-01"
               ></v-date-picker>
               <!-- 주간 -->
               <v-date-picker
                v-if="setPeriod_type == 'weekly'"
                v-model="fDate"
                min="2018-01"
               ></v-date-picker>
               <v-date-picker
                v-if="setPeriod_type == 'weekly'"
                v-model="sDate"
                min="2018-01"
               ></v-date-picker>
               <!-- 최근 일 경우 -->
               <div v-if="setPeriod_type == 'recently'">
                 <h2 style="font-family: Mplus 1p">最近（10週）は期間の変更ができません。</h2>
               </div>
           </v-flex>
         </v-layout>
        </v-container>
        <!-- 취소 / 저장 버튼 -->
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn color="primary" @click="selectPeriod('save'), dialog = false">期間変更</v-btn>
        </v-card-actions>
     </v-card>
   </v-dialog>

    <!-- 학생 목록 영역-->
    <v-flex xs12>
      <v-container grid-list-xl>
        <v-layout row wrap align-center>

          <div class="studentPageTopDiv">

            <div class = "studentListCard chartLeft">
            <v-card class = "studentListCard chatCardLeft">
              <v-card-text>
                <v-flex xs12>
                    <v-layout row wrap align-center>
                      <v-flex xs12 md2>
                        <v-icon color = "light-blue darken-2" large>format_list_bulleted</v-icon>
                      </v-flex>
                      <v-flex xs12 md10>
                        <h2 class = "studentListTitle">学生リスト
                          <v-btn round color = "indigo accent-1" v-if="!dateCheck" @click.stop="dialog = !dialog">{{ this.periodSelected }}</v-btn>
                          <v-btn class = "elevation-0" color = "transparent" v-else @click.stop="dialog = !dialog">
                            {{ this.periodSelected }}
                            ( {{ this.startDate }} ~ {{ this.endDate }})
                          </v-btn>
                        </h2>
                      </v-flex>
                    </v-layout>
                </v-flex>
              </v-card-text>
              <!-- 학생 분류 버튼 -->
              <v-card-text class = "buttonBox">
                <v-btn style="font-family:Mplus 1p"round outline color="blue accent-2" @click="getStudentInfo('total'), studentSelected='全て'">全て</v-btn>
                <v-btn round outline color="blue accent-2" @click="getStudentInfo('filter'), studentSelected='注目'">注目</v-btn>
                <v-btn round outline color="blue accent-2" @click="getStudentInfo('attention'), studentSelected='チェック'">チェック</v-btn>
              </v-card-text>
              <!-- 학생 목록 -->
              <v-list three-line>
                <v-subheader><h2> {{ studentSelected }} </h2></v-subheader>
                <v-progress-linear :indeterminate="progressStudent" height="3" ></v-progress-linear>
                  <div class="studentInfoDiv">
                    <template v-for="datas in studentInfo">
                       <!-- 전체, 주목, 사랑 버튼 중 한가지 선택 시, 선택한 버튼 텍스트 띄우는 영역 -->
                         <!-- 학생 사진, 이름과 학번, 사랑도, 주목된 이유 의 정보가 뜨게 함 -->
                         <v-list-tile avatar @click="selectStudent(datas)">
                           <!-- 학생 사진 -->
                           <v-list-tile-avatar>
                             <img :src="datas.photo_url">
                           </v-list-tile-avatar>
                           <!-- 학생 정보 -->
                           <v-list-tile-content>
                             <!-- 학생의 이름과 학번 -->
                             <v-list-tile-title> {{ datas.id }} {{ datas.name }}
                               <v-icon v-if="datas.attention_level > 0" small color = "red">favorite</v-icon>
                               <v-icon v-if="datas.attention_level > 1" small color = "red">favorite</v-icon>
                               <v-icon v-if="datas.attention_level > 2" small color = "red">favorite</v-icon>
                             </v-list-tile-title>
                             <!-- 주목된 이유 -->
                             <div>
                               <v-tooltip top v-if="datas.recent_trouble_Check">
                                 <v-btn slot="activator" small dark depressed  round color="light-green lighten-1" >最近、成績が下落</v-btn>
                                 <span><div v-for="data in datas.trouble.recent_trouble"><h3>{{ data }}</h3></div></span>
                               </v-tooltip>
                               <v-tooltip top v-if="datas.ada_Check">
                                 <v-btn slot="activator" small dark depressed round color="light-green lighten-1" >出席率の低調</v-btn>
                                 <span><div v-for="data in datas.trouble.ada"><h3>{{ data }}</h3></div></span>
                               </v-tooltip>
                               <v-tooltip top v-if="datas.low_level_Check">
                                 <v-btn slot="activator" small dark depressed round color="light-green lighten-1" >成績が下位</v-btn>
                                 <span><div v-for="data in datas.trouble.low_level"><h3>{{ data }}</h3></div></span>
                               </v-tooltip>
                             </div>
                           </v-list-tile-content>
                         </v-list-tile>
                         <!-- 구분선 -->
                         <v-divider :inset="true" ></v-divider>
                       </template>
                     </div>
                   </v-list>
                 </v-card>
              </div>

               <!-- 출결 관려 차트 영역 -->
                <div class = "chatRight">
                 <v-card class = "chartCard chatCardRight">
                    <v-card-text>
                      <v-flex xs12>
                        <v-layout row wrap align-center>
                          <v-flex xs12 md1>
                            <v-icon  color = "light-blue darken-2" large>show_chart</v-icon>
                          </v-flex>
                          <v-flex xs12 md10>
                            <h2 class="chartTitle">学生の分析</h2>
                          </v-flex>
                        </v-layout>
                      </v-flex>
                    </v-card-text>

                    <div class = "studentInfoBox">
                      <v-flex xs12>
                        <v-container grid-list-xl>
                          <v-layout row wrap align-center v-if="btnLock">
                              <v-flex xs1>
                                <v-card-text>
                                  <v-avatar class = "elevation-3" size = "120px">
                                    <img :src="selectStudentData.photo_url" />
                                  </v-avatar>
                                </v-card-text>
                              </v-flex>

                              <!-- 학생 정보 -->
                              <v-flex xs8>
                                <v-card-text class = "studentInfo">
                                  <div class="textBox"><span>{{ selectStudentData.id }}</span>{{ selectStudentData.name }}</div>
                                  <!-- 사랑 -->
                                  <div>
                                  <v-icon v-if="selectStudentData.attention_level > 0" color="red" @click="setAttentionLevel(selectStudentData, 1, 'red')">favorite</v-icon>
                                  <v-icon v-if="selectStudentData.attention_level > 1" color="red" @click="setAttentionLevel(selectStudentData, 2, 'red')">favorite</v-icon>
                                  <v-icon v-if="selectStudentData.attention_level > 2" color="red">favorite</v-icon>
                                  <v-icon v-if="selectStudentData.attention_level <= 2" color="gray" @click="setAttentionLevel(selectStudentData, 1, 'gray')">favorite</v-icon>
                                  <v-icon v-if="selectStudentData.attention_level <= 1" color="gray" @click="setAttentionLevel(selectStudentData, 2, 'gray')">favorite</v-icon>
                                  <v-icon v-if="selectStudentData.attention_level <= 0" color="gray" @click="setAttentionLevel(selectStudentData, 3, 'gray')">favorite</v-icon>
                                </div>
                                <div>
                                  <!-- 주목된 이유 -->
                                  <v-tooltip top v-if="selectStudentData.recent_trouble_Check">
                                    <v-btn slot="activator" small dark depressed  round color="light-green lighten-1" >最近、成績が下落</v-btn>
                                    <span><div v-for="data in selectStudentData.trouble.recent_trouble"><h3>{{ data }}</h3></div></span>
                                  </v-tooltip>
                                  <v-tooltip top v-if="selectStudentData.ada_Check">
                                    <v-btn slot="activator" small dark depressed round color="light-green lighten-1" >出席率の低調</v-btn>
                                    <span><div v-for="data in selectStudentData.trouble.ada"><h3>{{ data }}</h3></div></span>
                                  </v-tooltip>
                                  <v-tooltip top v-if="selectStudentData.low_level_Check">
                                    <v-btn slot="activator" small dark depressed round color="light-green lighten-1" >成績が下位</v-btn>
                                    <span><div v-for="data in selectStudentData.trouble.low_level"><h3>{{ data }}</h3></div></span>
                                  </v-tooltip>
                                </div>
                                </v-card-text>
                              </v-flex>
                          </v-layout>
                          <!-- 학생 미선택시 -->
                          <v-layout row wrap align-center v-else>
                              <v-flex xs1>
                                <v-card-text>
                                  <v-avatar class = "elevation-3" size = "120px">
                                    <img src="#" />
                                  </v-avatar>
                                </v-card-text>
                              </v-flex>
                              <!-- 학생 정보 -->
                              <v-flex xs8>
                                <v-card-text class = "studentInfo">
                                  <div class="textBox"><span>学生を選んでください。</span></div>
                                </v-card-text>
                              </v-flex>
                          </v-layout>

                        </v-container>
                      </v-flex>
                    </div>

                    <!-- 학업 관련 차트 영역-->
                    <div class = "attendanceChartTypeSelect">
                      <v-flex xs12>
                        <v-container grid-list-xl>
                          <v-layout row wrap align-center>
                              <v-flex xs4>
                                <v-select
                                  :items="attendance"
                                  v-model="attendanceSelected"
                                  :label="attendanceSelected.text"
                                  single-line
                                ></v-select>
                              </v-flex>
                              <v-flex xs6 v-if="attendanceSelected.select == 1">
                                <!-- 등하교 버튼 -->
                                <v-btn depressed small dark round color="blue accent-3" v-if="btnLock" v-on:click="selectMinorType('sign_in', 'sign', '登校')">登校</v-btn>
                                <v-btn depressed small round color="blue accent-3" v-else disabled>登校</v-btn>
                                <v-btn depressed small dark round color="blue accent-3" v-if="btnLock" v-on:click="selectMinorType('sign_out', 'sign', '下校')">下校</v-btn>
                                <v-btn depressed small round color="blue accent-3" v-else disabled>下校</v-btn>
                              </v-flex>
                              <v-flex xs6 v-else-if="attendanceSelected.select == 2">
                                <!-- 출결 버튼 -->
                                <v-btn depressed small dark round color="blue accent-3" v-if="btnLock" v-on:click="selectMinorType('lateness', 'attendance', '遅刻')">遅刻</v-btn>
                                <v-btn depressed small round color="blue accent-3" v-else disabled>遅刻</v-btn>
                                <v-btn depressed small dark round color="blue accent-3" v-if="btnLock" v-on:click="selectMinorType('absence', 'attendance', '欠席')">欠席</v-btn>
                                <v-btn depressed small round color="blue accent-3" v-else disabled>欠席</v-btn>
                                <v-btn depressed small dark round color="blue accent-3" v-if="btnLock" v-on:click="selectMinorType('early_leave', 'attendance', '早引げ')">早引げ</v-btn>
                                <v-btn depressed small round color="blue accent-3" v-else disabled>早引げ</v-btn>
                              </v-flex>
                          </v-layout>
                          <v-flex xs12>
                            <!-- 등교 하교 시간 변화량 그래프 -->
                            <div v-if="attendanceSelected.select == 1">
                              <div><h2 style="font-family: Gothic A1;font-weight: lighter;">登校＆下校の時間の変化 ( {{ setSign }} ) </h2></div>
                              <attendance-time-lineChart :width="1000" :data="timeLineData" :labels="timeLineLabelData" :options="{ responsive: true, maintainAspectRatio: false }"></attendance-time-lineChart>
                            </div>
                            <!-- 지각 조퇴 결석 ~ 횟수 변화량 그래프 (3 중 1 선택) -->
                            <div v-else-if="attendanceSelected.select == 2">
                              <div><h2 style="font-family: Gothic A1;font-weight: lighter;">出席の回数変化 ( {{ setAtt }} )</h2></div>
                              <attendance-count-lineChart :width="1000" :data="countLineData" :labels="countLineLabelData" :options="{ responsive: true, maintainAspectRatio: false }"></attendance-count-lineChart>
                            </div>
                            <!-- 출결 비율 그래프-->
                            <div v-else-if="attendanceSelected.select == 3">
                              <attendance-count-pieChart :width="1000" :data="countPieData" :options="{ responsive: true, maintainAspectRatio: false }"></attendance-count-pieChart>
                            </div>
                          </v-flex>
                        </v-container>
                      </v-flex>
                    </div>

                    <div class = "attendanceChartTypeSelect">
                      <v-flex xs12>
                        <v-container grid-list-xl>
                          <v-layout row wrap align-center>
                              <!-- 강의 선택 -->
                              <v-flex xs6>
                                <v-select
                                  :items="subjectList"
                                  v-model="subjectSelect"
                                  :label="subjectSelect.text"
                                  single-line
                                ></v-select>
                              </v-flex>
                              <!-- 그래프 선택 -->
                              <v-flex xs6>
                                <v-select
                                  :items="grade"
                                  v-model="greadeSelected"
                                  :label="greadeSelected.text"
                                  single-line
                                ></v-select>
                              </v-flex>
                              <!-- 종목 선택 -->
                              <div v-if="greadeSelected.select == 3 || greadeSelected.select == 4">
                                <div class="btnFloatSettingCss">
                                  <v-btn depressed round dark color="blue accent-3" v-if="btnLock" v-on:click="selectMinorType('_quiz', 'detailCode', 'テスト')">テスト</v-btn>
                                  <v-btn depressed round color="blue accent-3" v-else disabled>テスト</v-btn>
                                </div>
                                <div class="btnFloatSettingCss">
                                  <v-btn depressed round dark color="blue accent-3" v-if="btnLock" v-on:click="selectMinorType('_homework', 'detailCode', '課題')">課題</v-btn>
                                  <v-btn depressed round color="blue accent-3" v-else disabled>課題</v-btn>
                                </div>
                                <div class="btnFloatSettingCss">
                                  <v-btn depressed round dark color="blue accent-3" v-if="btnLock" v-on:click="selectMinorType('_midterm', 'detailCode', '中間')">中間</v-btn>
                                  <v-btn depressed round color="blue accent-3" v-else disabled>中間</v-btn>
                                </div>
                                <div class="btnFloatSettingCss">
                                  <v-btn depressed round dark color="blue accent-3" v-if="btnLock" v-on:click="selectMinorType('_final', 'detailCode', '期末')">期末</v-btn>
                                  <v-btn depressed round color="blue accent-3" v-else disabled>期末</v-btn>
                                </div>
                              </div>
                          </v-layout>
                            <v-flex xs12>
                          <v-layout row wrap align-center>
                            <v-flex xs12 md6>
                            <div v-if="greadeSelected.select == 1">
                              <div><h2 style="font-family: Gothic A1;font-weight: lighter;">講義の取得点数 ( {{ setLec }} )</h2></div>
                              <!-- 강의 취득점수 비교 그래프 (강의 중 1택) -->
                              <study-lecture-score-lineChart :datasets="lectureScoreDataSets" :labels="lectureScoreLabelData" :options="{ responsive: true, maintainAspectRatio: false }"></study-lecture-score-lineChart>
                            </div>
                            <div v-if="greadeSelected.select == 2">
                              <div><h2 style="font-family: Gothic A1;font-weight: lighter;">講義席次のパーセンテージ ( {{ setLec }} )</h2></div>
                              <study-lecture-ranking-lineChart :data="lectureRankingData" :labels="lectureRankingLabelData" :options="{ responsive: true, maintainAspectRatio: false }"></study-lecture-ranking-lineChart>
                            </div>
                            <div v-if="greadeSelected.select == 3">
                              <!-- 강의의 항목(중간 기말 쪽지 과제) 별 취득점수 비교 그래프 (4중 1택) -->
                              <div><h2 style="font-family: Gothic A1;font-weight: lighter;">種目別の取得点数 ( {{ setSub }} )</h2></div>
                              <study-subject-score-lineChart :datasets="subjectScoreDataSets" :labels="subjectScoreLabelData" :options="{ responsive: true, maintainAspectRatio: false }"></study-subject-score-lineChart>
                            </div>
                            <div v-if="greadeSelected.select == 4">
                              <div><h2 style="font-family: Gothic A1;font-weight: lighter;">種目別のパーセンテージ ( {{ setSub }} )</h2></div>
                              <study-subject-ranking-lineChart :data="subjectRankingData" :labels="subjectRankingLabelData" :options="{ responsive: true, maintainAspectRatio: false }"></study-subject-ranking-lineChart>
                            </div>
                            </v-flex>
                            <!-- 전공 & 일본어 수준 비교 그래프 -->
                            <v-flex xs12 md6>
                              <div><h2 style="font-family: Gothic A1;font-weight: lighter;">専攻&日本語の実力</h2></div>
                              <study-japenese-Major-lineChart :datasets="jmLineDataSets" :labels="jmLineLabelData" :options="{ responsive: true, maintainAspectRatio: false }"></study-japenese-Major-lineChart>
                            </v-flex>
                          </v-layout>
                       </v-flex>
                        </v-container>
                      </v-flex>
                    </div>
                 </v-card>
                </div>

             </div>

        </v-layout>
      </v-container>
    </v-flex>
  </div>
</template>

<style>

.btnFloatSettingCss {
  float : left;
}

.studentPageTopDiv {
  position : relative;
  width: 100%;
  bottom : 180px;
}

.chartLeft {
  float:left;
  width : 37%;
  margin: 10px;
}

.chatCardLeft {
  width : 100%;
}

.chatRight {
  float:left;
  width : 58%;
  margin: 10px;
}

.chatCardRight {
  width : 100%;
}

/*-- 헤더 영역 --*/
.categoryStudentAnalytic {
  color: #FFFFFF;
  font-size: 30px;
  position: relative;
  font-family: "Montserrat";
  font-weight: Bold;
  position: relative;
  left: 40px;
  bottom: 30px;
}

/*--- 학생 분류 카드 ---*/
.studentListCard {
  border-radius: 0.7975rem;
  box-shadow:  0px 4px 10px 0 rgba(33, 33, 33, 0.36);
}

.studentListTitle {
  font-family: "Mplus 1p";
  font-weight: normal;
  font-size: 30px;
}
.buttonBox {
  margin: 0 0 0 3px;
}
.StudentAnalyticPrediction{
  width: 100%;
  height: 100%;
}
/*-- 차트 --*/
.chartCard {
  position: relative;
  left: 10px;
  border-radius: 0.7975rem;
  box-shadow:  0px 4px 10px 0 rgba(33, 33, 33, 0.36);
}
.chartTitle {
  font-family: "Mplus 1p";
  font-weight: normal;
  font-size: 30px;
}
.studentInfoBox {
  position: relative;
  bottom: 40px;
}
.studentInfo {
  position: relative;
  left: 87px;
}
  .textBox {
    font-family: "Mplus 1p";
    font-weight: normal;
    font-size: 25px;
    margin: 0 0 10px 0;
  }
  .textBox span {
    font-size: 25px;
    font-family: "Montserrat";
    font-weight: bold;
    color: rgb(125, 125, 125);
    margin: 0 10px 0 0px;
  }
.attendanceChartTypeSelect {
  position: relative;
  bottom: 100px;
}

/*-- 분류 조건 설정 --*/
.attendanceSettingTitle{
  position: relative;
  top: 5px;
  left: -10px;
  font-family: "Mplus 1p";
  font-weight: normal;
}

/* */
.studentInfoDiv {
  overflow-y : scroll;
  height : 1100px;
}
</style>
<script>

import Vue from 'vue'
import VueChartJs from 'vue-chartjs'

Vue.component('attendance-time-lineChart', {
  extends : VueChartJs.Line,
  props: ['data', 'labels', 'options'],
  mounted(){
    this.renderLineChart();
  },
  /* 선언된 chart 의 data 속성에 값을 반환한다. */
  computed: {
    timeLineData : function(){
      return this.data
    },
    timeLineLabelData : function(){
      return this.labels
    }
  },
  methods : {
    renderLineChart : function(){
      this.renderChart({
        labels: this.timeLineLabelData,
        datasets:[{
            label : '時間(00:00)',
            borderColor: '#4451fb',
            fill : false,
            data: this.timeLineData
          }],
        options:{
          responsive: true,
          maintainAspectRatio: false,
          height: 100,
        }
      },
      {
        scales: {
             yAxes: [{
               ticks: {
                min : 0,
                max : 2400
               }
             }]
           }
      }
    )
  }
},
watch: {
  /* 값 변경을 감지하면 실행 */
  /* 지정 변수가 바뀌어야한다. 하위의 값이 바뀌는 것은 해당하지 않는다. */
  timeLineData : function(){
    this.$data._chart.destroy();
    this.renderLineChart();
  }
}
});

/* 출결 횟수 그래프 */
Vue.component('attendance-count-lineChart', {
  extends : VueChartJs.Line,
  props: ['data', 'labels', 'options'],
  mounted(){
    this.renderLineChart();
  },
  /* 선언된 chart 의 data 속성에 값을 반환한다. */
  computed: {
    countLineData : function(){
      return this.data
    },
    countLineLabelData : function(){
      return this.labels
    }
  },
  methods : {
    renderLineChart : function(){
      this.renderChart({
        labels: this.countLineLabelData,
        datasets:[{
            label : '回数',
            borderColor: '#4657fa',
            fill : false,
            data: this.countLineData
          }],
        options:{
          responsive: true,
          maintainAspectRatio: false,
          height: 100
        }
      },
      {
        scales: {
             yAxes: [{
               ticks: {
                min : 0
               }
             }]
           }
      }
    )
  }
},
watch: {
  /* 값 변경을 감지하면 실행 */
  /* 지정 변수가 바뀌어야한다. 하위의 값이 바뀌는 것은 해당하지 않는다. */
  countLineData : function(){
    this.$data._chart.destroy();
    this.renderLineChart();
  }
}
});

/* 등교 유형 및 조퇴 횟수 */
/* 출결 횟수 그래프 */
Vue.component('attendance-count-pieChart', {
  extends : VueChartJs.Pie,
  props: ['data', 'labels', 'options'],
  mounted(){
    this.renderPieChart();
  },
  /* 선언된 chart 의 data 속성에 값을 반환한다. */
  computed: {
    countPieData : function(){
      return this.data
    }
  },
  methods : {
    renderPieChart : function(){
      this.renderChart({
        labels : ['出席','遅行','欠席','早退'],
        datasets:[{
            backgroundColor: ['#4657fa','#9f98ed','#ff3333','#808080'],
            fill : false,
            data: this.countPieData
          }],
        options:{
          responsive: true,
          maintainAspectRatio: false,
          height: 100
        }
      }
    )
  }
},
watch: {
  /* 값 변경을 감지하면 실행 */
  /* 지정 변수가 바뀌어야한다. 하위의 값이 바뀌는 것은 해당하지 않는다. */
  countPieData : function(){
    this.$data._chart.destroy();
    this.renderPieChart();
  }
}
});

/* 전공 일본어 비교 */
Vue.component('study-japenese-Major-lineChart', {
  extends : VueChartJs.Line,
  props: ['datasets', 'labels', 'options'],
  mounted(){
    this.renderLineChart();
  },
  /* 선언된 chart 의 data 속성에 값을 반환한다. */
  computed: {
    jmLineDataSets : function(){
      return this.datasets
    },
    jmLineLabelData : function(){
      return this.labels
    }
  },
  methods : {
    renderLineChart : function(){
      this.renderChart({
        labels: this.jmLineLabelData,
        datasets: this.jmLineDataSets,
        options:{
          responsive: true,
          maintainAspectRatio: false,
          height: 100
        }
      },
      {
        scales: {
             yAxes: [{
               ticks: {
                min : 0,
                max : 100
               }
             }]
           }
      }
    )
  }
},
watch: {
  /* 값 변경을 감지하면 실행 */
  /* 지정 변수가 바뀌어야한다. 하위의 값이 바뀌는 것은 해당하지 않는다. */
  jmLineLabelData : function(){
    this.$data._chart.destroy();
    this.renderLineChart();
  }
}
});

/* 과목 득점 비교 */
Vue.component('study-lecture-score-lineChart', {
  extends : VueChartJs.Line,
  props: ['datasets', 'labels', 'options'],
  mounted(){
    this.renderLineChart();
  },
  /* 선언된 chart 의 data 속성에 값을 반환한다. */
  computed: {
    lectureScoreDataSets : function(){
      return this.datasets
    },
    lectureScoreLabelData : function(){
      return this.labels
    }
  },
  methods : {
    renderLineChart : function(){
      this.renderChart({
        labels: this.lectureScoreLabelData,
        datasets: this.lectureScoreDataSets,
        options:{
          responsive: true,
          maintainAspectRatio: false,
          height: 100
        }
      },
      {
        scales: {
             yAxes: [{
               ticks: {
                min : 0
               }
             }]
           }
      }
    )
  }
},
watch: {
  /* 값 변경을 감지하면 실행 */
  /* 지정 변수가 바뀌어야한다. 하위의 값이 바뀌는 것은 해당하지 않는다. */
  lectureScoreLabelData : function(){
    this.$data._chart.destroy();
    this.renderLineChart();
  }
}
});

/* 개인 석차 백분율 */
Vue.component('study-lecture-ranking-lineChart', {
  extends : VueChartJs.Line,
  props: ['data', 'labels', 'options'],
  mounted(){
    this.renderLineChart();
  },
  /* 선언된 chart 의 data 속성에 값을 반환한다. */
  computed: {
    lectureRankingData : function(){
      return this.data
    },
    lectureRankingLabelData : function(){
      return this.labels
    }
  },
  methods : {
    renderLineChart : function(){
      this.renderChart({
        labels: this.lectureRankingLabelData,
        datasets: [{
            label : "席次のパーセンテージ",
            borderColor : ['#33ff66'],
            fill : false,
            data: this.lectureRankingData
          }],
        options:{
          responsive: true,
          maintainAspectRatio: false,
          height: 100
        }
      },
      {
        scales: {
             yAxes: [{
               ticks: {
                min : 0,
                max : 100
               }
             }]
           }
      }
    )
  }
},
watch: {
  /* 값 변경을 감지하면 실행 */
  /* 지정 변수가 바뀌어야한다. 하위의 값이 바뀌는 것은 해당하지 않는다. */
  lectureRankingData : function(){
    this.$data._chart.destroy();
    this.renderLineChart();
  }
}
});

/* 강의 = 종류별 석차 백분율 비교 */
Vue.component('study-subject-score-lineChart', {
  extends : VueChartJs.Line,
  props: ['datasets', 'labels', 'options'],
  mounted(){
    this.renderLineChart();
  },
  /* 선언된 chart 의 data 속성에 값을 반환한다. */
  computed: {
    subjectScoreDataSets : function(){
      return this.datasets
    },
    subjectScoreLabelData : function(){
      return this.labels
    }
  },
  methods : {
    renderLineChart : function(){
      this.renderChart({
        labels: this.subjectScoreLabelData,
        datasets: this.subjectScoreDataSets,
        options:{
          responsive: true,
          maintainAspectRatio: false,
          height: 100
        }
      },
      {
        scales: {
             yAxes: [{
               ticks: {
                min : 0
               }
             }]
           }
      }
    )
  }
},
watch: {
  /* 값 변경을 감지하면 실행 */
  /* 지정 변수가 바뀌어야한다. 하위의 값이 바뀌는 것은 해당하지 않는다. */
  subjectScoreLabelData : function(){
    this.$data._chart.destroy();
    this.renderLineChart();
  }
}
});

/* 강의 = 개인 석차 백분율 */
Vue.component('study-subject-ranking-lineChart', {
  extends : VueChartJs.Line,
  props: ['data', 'labels', 'options'],
  mounted(){
    this.renderLineChart();
  },
  /* 선언된 chart 의 data 속성에 값을 반환한다. */
  computed: {
    subjectRankingData : function(){
      return this.data
    },
    subjectRankingLabelData : function(){
      return this.labels
    }
  },
  methods : {
    renderLineChart : function(){
      this.renderChart({
        labels: this.subjectRankingLabelData,
        datasets: [{
            label : "席次のパーセンテージ",
            borderColor : ['#33ff66'],
            fill : false,
            data: this.subjectRankingData
          }],
        options:{
          responsive: true,
          maintainAspectRatio: false,
          height: 100
        }
      },
      {
        scales: {
             yAxes: [{
               ticks: {
                min : 0,
                max : 100
               }
             }]
           }
      }
    )
  }
},
watch: {
  /* 값 변경을 감지하면 실행 */
  /* 지정 변수가 바뀌어야한다. 하위의 값이 바뀌는 것은 해당하지 않는다. */
  subjectRankingData : function(){
    this.$data._chart.destroy();
    this.renderLineChart();
  }
}
});

export default {
    data () {
      return {
        /* 출석 그래프 선택 */
        attendanceSelected : { text: '登校＆下校の時間変化', select : 1 },
        greadeSelected: { text : '講義の取得点数', select : 1 },
        attendance: [
          { text: '登校＆下校の時間変化', select : 1 },
          { text: '出席回数の変化', select : 2 },
          { text: '出席別の回数', select : 3 },
        ],

        grade: [
          { text : '講義の取得点数', select : 1 },
          { text : '講義の席次パーセンテージ', select : 2 },
          { text : '種目別の取得点数', select : 3 },
          { text : '種目別の席次パーセンテージ', select : 4 },
        ],
        /* 선택한 학생 정보 */
        selectStudentData : [],
        /* 선택 상태 */
        setSign : '登校',
        setAtt : '遅刻',
        setLec : null,
        setSub : 'テスト',
        /* 기간 */
        periodSelected : '最近',
        dialog : false,
        fDate : null,
        sDate : null,
        startDate : null,
        endDate : null,
        dateCheck : false,
        /* 우측 버튼 잠금 해제 */
        btnLock : false,
        /* 로딩바 , progress */
        progressStudent : false,
        /* 학생 목록 */
        studentInfo: [],
        studentsType : 'total',
        studentSelected : '全て',

        /* 강의 목록 */
        subjectList : [{ id : '', name : '', text : '講義の情報がありません.'}],
        subjectSelect : { text : '講義の情報がありません.'},

        /* 그래프 조회 기본 값*/
        setStd_id : null,
        setMinor_type : [{
          'sign' : 'sign_in',
          'attendance' : 'lateness',
          'code' : null,
          'detailCode' : '_homework'
        }],
        setPeriod_type : 'recently',

        /* 그래프에 사용될 데이터 변수 */
        /* 01. 등하교 시간 변화 */
        timeLineData : [],
        timeLineLabelData : [],
        /* 02. 출결 횟수 변화 */
        countLineData : [],
        countLineLabelData : [],
        /* 03. 출결별 횟수 */
        countPieData : [],
        /* 04. 전공 일본어 비교 */
        jmLineDataSets : [{
              label : '日本語',
              borderColor: '#0000ff',
              fill : false,
              data: []
            },
            {
              label : '専攻',
              borderColor: '#ff3333',
              fill : false,
              data: []
            }],
        jmLineLabelData : [],

        /* 05. 강의 득점 평균 비교 및 석차백분율 변화 */
        lectureScoreDataSets : [{
              label : '学生の平均',
              borderColor: '#ff3333',
              fill : false,
              data: []
            },
            {
              label : 'クラスの平均',
              borderColor: '#0000ff',
              fill : false,
              data: []
            }],
        lectureScoreLabelData : [],

        lectureRankingData : [],
        lectureRankingLabelData : [],
        /* 06. 강의 = 상세 종목별 득점 평균 비교 및 석차백분율 변화 */
        subjectScoreDataSets : [{
              label : '学生の平均',
              borderColor: '#ff3333',
              fill : false,
              data: []
            },
            {
              label : 'クラスの平均',
              borderColor: '#0000ff',
              fill : false,
              data: []
            }],
        subjectScoreLabelData : [],

        subjectRankingData : [],
        subjectRankingLabelData : [],
    }
  },
  methods : {
    /* 학생 선택 */
    selectStudent(value){
        /* 우측 버튼 잠금 해제 */
        this.btnLock = true;
        /* 학생 정보 저장 */
        this.selectStudentData = value;
        /* 학번 변경 */
        this.setStd_id = value.id;
        /* 과목 불러오기 : 첫번째 과목을 기본 값으로 설정 => 중계함수 호출 */
        this.getSubjectList();
        /* 그래프 중계함수 호출*/
        // this.getStudentInfoRenderGraph();
    },
    /* 하위 메뉴 조작 */
    selectMinorType(value, set, name){
        switch (set) {
          case 'sign' :
            this.setMinor_type[0]['sign'] = value;
            this.setSign = name;
            this.getStudentInfoRenderGraph('sign');
            break;
          case 'attendance' :
            this.setMinor_type[0]['attendance'] = value;
            this.setAtt = name;
            this.getStudentInfoRenderGraph('attendance');
            break;
          case 'code' :
            this.setMinor_type[0]['code'] = value;
            console.log(value);
            console.log(this.setLec);
            this.setLec = name;
            this.getStudentInfoRenderGraph('code');
            break;
          case 'detailCode' :
            this.setMinor_type[0]['detailCode'] = value;
            this.setSub = name;
            this.getStudentInfoRenderGraph('detailCode');
            break;
        }
    },
    selectPeriod(value){
      switch (value) {
        case 'daily' :
          // 사용대기
          break;
        case 'weekly':
          this.sDate = null;
          this.fDate = null;
          this.setPeriod_type = value;
          this.periodSelected = '週間';
          this.dateCheck = false;
          break;
        case 'monthly':
          this.sDate = null;
          this.fDate = null;
          this.setPeriod_type = value;
          this.periodSelected = '月間';
          this.dateCheck = false;
          break;
        case 'recently':
          this.sDate = null;
          this.fDate = null;
          this.setPeriod_type = value;
          this.periodSelected = '最近';
          this.dateCheck = false;
          break;
        case 'save' :
          /* 날짜 예외처리 = 정상 값 확인 */
          /* 기본 날짜는 watch 로 1차 확인 */
          if(!this.dateCheck && this.setPeriod_type != 'recently'){
            /* 잘못된 값 */
            this.sDate = null;
            this.fDate = null;
            this.setPeriod_type = 'recently';
            this.periodSelected = '最近';
            this.dateCheck = false;
            /* 알림 */
            alert('(リセット)間違った入力です。')
          }
          /* 학생이 선택된 상태라면 그래프를 그린다. */
          else if(this.btnLock){
            this.getStudentInfoRenderGraph()
          }
      }
    },
    getStudentInfoRenderGraph(set){

      switch (set) {
        case 'sign' :
          this.getSignData();
          break;
        case 'attendance' :
          this.getAttendanceData();
          break;
        case 'code' :
          this.getLectureData();
          this.getSubjectData();
          break;
        case 'detailCode' :
          this.getSubjectData();
          break;
        default :
          this.getSignData();
          this.getAttendanceData();
          this.getAttendanceDoughnutData();
          this.getJmLineData();
          this.getLectureData();
          this.getSubjectData();
      }
    },
    /* 학생 목록 */
    getStudentInfo(typeSelect){
      this.studentInfo = [];
      this.studentsType = typeSelect;
      this.progressStudent = true;
      axios.get('/tutor/analyse/student_list', {
        params : {
          type  : this.studentsType,
          order : 'id'
        }
      }).then((response) => {
        this.studentInfo = response.data.message;
        for(let data in this.studentInfo){
          if(this.studentInfo[data].attention_reason == null){
            this.studentInfo[data].attention_reason = '';
          }
          this.$set(this.studentInfo[data], 'number', data)
        }
        this.progressStudent = false;

        this.selectStudent(this.studentInfo[0]);
        console.log(this.studentInfo);
        console.log(Object.keys(this.studentInfo[0].trouble).length);

        /* 트러블 기본 정보 추가 */
        for(let troubleSetting in this.studentInfo){
          this.$set(this.studentInfo[troubleSetting], 'recent_trouble_Check', false);
          this.$set(this.studentInfo[troubleSetting], 'ada_Check', false);
          this.$set(this.studentInfo[troubleSetting], 'low_level_Check', false);
        }

        /* 트러블 정보 추가 */
        for(let trouble in this.studentInfo){
          /* 트러블 정보가 존재하는지 확인 */
          if(Object.keys(this.studentInfo[trouble].trouble).length > 0){
            /* 트러블 라벨 추출 */
            let data = Object.keys(this.studentInfo[trouble].trouble);
            /* 라벨을 확인하여 값 변경 */
            for(let num in data){
              switch (data[num]) {
                case 'recent_trouble':
                  this.studentInfo[trouble]['recent_trouble_Check'] = true;
                  break;
                case 'ada':
                  this.studentInfo[trouble]['ada_Check'] = true;
                  break;
                case 'low_level':
                  this.studentInfo[trouble]['low_level_Check'] = true;
                  break;
              }
            }
          }
        }

      }).catch((error) => {
        console.log("getStuInfo Err : " + error);
        alert('ロードに失敗しました。')
      })
    },
    /* 수강 강의 목록 */
    getSubjectList(){
      axios.get('/tutor/detail/join_list', {
        params : {
          std_id : this.setStd_id
        }
      }).then((response) => {
        /* 초기화 */
        this.subjectList = [];
        for(let start in response.data.message.subjects){
          this.subjectList.push(response.data.message.subjects[start]);
        }
        /* select 리스트 용 데이터 생성 */
        for(let datas in this.subjectList){
          this.$set(this.subjectList[datas], 'text', this.subjectList[datas].name)
        }
        console.log(this.subjectList);
        /* 과목 코드 기본 값 설정 */
        this.setMinor_type[0]['code'] = this.subjectList[0].id;
        this.setLec = this.subjectList[0].name;
        this.subjectSelect = this.subjectList[0];

        this.getStudentInfoRenderGraph();
      }).catch((error) => {
        console.log('getSub Err :' + error);
      })
    },
    /* 등하교 시간 변화량 */
    getSignData(std_id){

      let paramData = [{
        major_class : 'ada',
        graph_type  : 'compare',
        minor_class : this.setMinor_type[0]['sign'],
        period_type : this.setPeriod_type,
        std_id : this.setStd_id
      }];
      /* 날짜 데이터 추가 */
      if(this.setPeriod_type != 'recently'){
        this.$set(paramData[0], 'start_date', this.startDate);
        this.$set(paramData[0], 'end_date', this.endDate);
      }

      axios.get('/tutor/analyse/result',{
        params : paramData[0]
      }).then((response)=>{

        let data = [];
        let label = [];

        for(let start = 0; start < response.data.message.value.length; start++){
          label.push(response.data.message.value[start]['x-point']);
          data.push(this.cutTime(response.data.message.value[start]['y-point']));
        }
        this.timeLineLabelData = label;
        this.timeLineData = data;
      }).catch((error)=>{
        console.log("getSign Err :"+error);
      })

    },
    /* 출결 횟수 변화량 */
    getAttendanceData(){

      let paramData = [{
        major_class : 'ada',
        graph_type  : 'single_line',
        minor_class : this.setMinor_type[0]['attendance'],
        period_type : this.setPeriod_type,
        std_id : this.setStd_id
      }];
      /* 날짜 데이터 추가 */
      if(this.setPeriod_type != 'recently'){
        this.$set(paramData[0], 'start_date', this.startDate);
        this.$set(paramData[0], 'end_date', this.endDate);
      }

      axios.get('/tutor/analyse/result',{
        params : paramData[0]
      }).then((response)=>{

        let data = [];
        let label = [];

        for(let start = 0; start < response.data.message.value.length; start++){
          label.push(response.data.message.value[start]['x-point']);
          if(response.data.message.value[start]['y-point'] == null){
            data.push(0);
          }else{
            data.push(response.data.message.value[start]['y-point']);
          }
        }
        this.countLineLabelData = label;
        this.countLineData = data;

      }).catch((error)=>{
        console.log("getAtt Err :"+error);
      })
    },
    /* 출결 유형 비율 */
    getAttendanceDoughnutData(){

      let paramData = [{
        major_class : 'ada',
        graph_type  : 'donut',
        period_type : this.setPeriod_type,
        std_id : this.setStd_id
      }];
      /* 날짜 데이터 추가 */
      if(this.setPeriod_type != 'recently'){
        this.$set(paramData[0], 'start_date', this.startDate);
        this.$set(paramData[0], 'end_date', this.endDate);
      }

      axios.get('/tutor/analyse/result',{
        params : paramData[0]
      }).then((response)=>{

        let data = response.data.message.value.graph;

        this.countPieData = [data.good, data.lateness, data.absence, data.early_leave];

      }).catch((error)=>{
        console.log("getPie Err :"+error);
      })
    },
    /* 전공 일본어 비교 */
    getJmLineData(){

      let paramData = [{
        major_class : 'study',
        graph_type  : 'double_line',
        minor_class  : 'japanese',
        period_type : this.setPeriod_type,
        std_id : this.setStd_id
      }];
      /* 날짜 데이터 추가 */
      if(this.setPeriod_type != 'recently'){
        this.$set(paramData[0], 'start_date', this.startDate);
        this.$set(paramData[0], 'end_date', this.endDate);
      }

      axios.get('/tutor/analyse/result',{
        params : paramData[0]
      }).then((response) =>{

        let label = [];
        let japanese = [];
        let major = [];

        for(let start = 0; start < response.data.message.japanese.value.length; start++){
          label.push(response.data.message.japanese.value[start]['x-point']);
          /* 일본어 */
          /* 기록이 없을 경우 */
          if(response.data.message.japanese.value[start]['y-point'] == null && start != 0){
            /* 이전 기록을 가지고 온다.*/
            japanese.push(japanese[start-1]);
          }else if(response.data.message.japanese.value[start]['y-point'] == null){
            /* 기록에 완전 없을 경우, 0으로 등록한다.*/
            japanese.push(0);
          }else{
            /* 기록이 있을 경우 */
            japanese.push(response.data.message.japanese.value[start]['y-point']);
          }
          /* 전공 */
          /* 기록이 없을 경우 */
          if(response.data.message.japanese.value[start]['y-point'] == null && start != 0){
            /* 이전 기록을 가지고 온다.*/
            major.push(major[start-1]);
          }else if(response.data.message.japanese.value[start]['y-point'] == null){
            /* 기록에 완전 없을 경우, 0으로 등록한다.*/
            major.push(0);
          }else{
            /* 기록이 있을 경우 */
            major.push(response.data.message.major.value[start]['y-point']);
          }
        }
        this.jmLineDataSets[0]['data'] = japanese;
        this.jmLineDataSets[1]['data'] = major;
        /* watch */
        this.jmLineLabelData = label;

      }).catch((error) => {
        console.log('getJM Err :' + error);
      })
    },
    /* 강의 별 득점 비교 및 석차 변화 */
    getLectureData(){

      let paramDataFirst = [{
        major_class : 'study',
        graph_type  : 'double_line',
        minor_class  : 'subject_' + this.setMinor_type[0]['code'],
        period_type : this.setPeriod_type,
        std_id : this.setStd_id
      }];
      /* 날짜 데이터 추가 */
      if(this.setPeriod_type != 'recently'){
        this.$set(paramDataFirst[0], 'start_date', this.startDate);
        this.$set(paramDataFirst[0], 'end_date', this.endDate);
      }

      /* 득점 비교 */
      axios.get('/tutor/analyse/result',{
        params : paramDataFirst[0]
      }).then((response) => {

        let label = [];
        let gained = [];
        let classAve = [];

        for(let start = 0; start < response.data.message.value.gained_score.length; start++){

          /* 개인점수 */
          if(response.data.message.value.gained_score[start]['y-point'] != null){

            label.push(response.data.message.value.gained_score[start]['x-point']);

            gained.push(response.data.message.value.gained_score[start]['y-point']);
          }

          /* 반 점수 */
          if(response.data.message.value.class_average[start]['y-point'] != null){
            classAve.push(response.data.message.value.class_average[start]['y-point']);
          }
        }

        this.lectureScoreDataSets[0]['data'] = gained;
        this.lectureScoreDataSets[1]['data'] = classAve;
        /* watch */
        this.lectureScoreLabelData = label;

      }).catch((error) => {
        console.log('getLecSingle Err :' + error);
      })

      /* 석차 백분율 */
      let paramDataSecond = [{
        major_class : 'study',
        graph_type  : 'single_line',
        minor_class  : 'subject_' + this.setMinor_type[0]['code'],
        period_type : this.setPeriod_type,
        std_id : this.setStd_id
      }];
      /* 날짜 데이터 추가 */
      if(this.setPeriod_type != 'recently'){
        this.$set(paramDataSecond[0], 'start_date', this.startDate);
        this.$set(paramDataSecond[0], 'end_date', this.endDate);
      }

      axios.get('/tutor/analyse/result',{
        params : paramDataSecond[0]
      }).then((response) => {

        let label = [];
        let gained = [];

        for(let start = 0; start < response.data.message.value.length; start++){

          /* 석차 백분율 */
          if(response.data.message.value[start]['y-point'] != null){
            label.push(response.data.message.value[start]['x-point']);
            gained.push(response.data.message.value[start]['y-point']);
          }
        }
        this.lectureRankingLabelData = label;
        this.lectureRankingData = gained;

      }).catch((error) => {
        console.log('getLecDouble Err :' + error);
      })
    },
    /* 강의 = 종류별 득점 비교 및 석차 변화 */
    getSubjectData(){

      let paramDatafirst = [{
        major_class : 'study',
        graph_type  : 'double_line',
        minor_class  : this.setMinor_type[0]['code'] + this.setMinor_type[0]['detailCode'],
        period_type : this.setPeriod_type,
        std_id : this.setStd_id
      }];
      /* 날짜 데이터 추가 */
      if(this.setPeriod_type != 'recently'){
        this.$set(paramDatafirst[0], 'start_date', this.startDate);
        this.$set(paramDatafirst[0], 'end_date', this.endDate);
      }

      axios.get('/tutor/analyse/result',{
        params : paramDatafirst[0]
      }).then((response) => {

        let label = [];
        let gained = [];
        let classAve = [];

        for(let start = 0; start < response.data.message.value.gained_score.length; start++){
          /* 개인점수 */
          if(response.data.message.value.gained_score[start]['y-point'] != null){

            label.push(response.data.message.value.gained_score[start]['x-point']);

            gained.push(response.data.message.value.gained_score[start]['y-point']);
          }

          /* 반 점수 */
          if(response.data.message.value.class_average[start]['y-point'] != null){
            classAve.push(response.data.message.value.class_average[start]['y-point']);
          }
        }

        this.subjectScoreDataSets[0]['data'] = gained;
        this.subjectScoreDataSets[1]['data'] = classAve;
        /* watch */
        this.subjectScoreLabelData = label;

      }).catch((error) => {
        console.log('getSubSingle Err :' + error);
      })

      /* 종류 별 석차백분율 */
      let paramDataSecond = [{
        major_class : 'study',
        graph_type  : 'single_line',
        minor_class : this.setMinor_type[0]['code'] + this.setMinor_type[0]['detailCode'],
        period_type : this.setPeriod_type,
        std_id : this.setStd_id
      }];
      /* 날짜 데이터 추가 */
      if(this.setPeriod_type != 'recently'){
        this.$set(paramDataSecond[0], 'start_date', this.startDate);
        this.$set(paramDataSecond[0], 'end_date', this.endDate);
      }

      axios.get('/tutor/analyse/result',{
        params : paramDataSecond[0]
      }).then((response) => {

        let label = [];
        let gained = [];

        for(let start = 0; start < response.data.message.value.length; start++){
          /* 석차 백분율 */
          if(response.data.message.value[start]['y-point'] != null){
            label.push(response.data.message.value[start]['x-point']);
            gained.push(response.data.message.value[start]['y-point']);
          }
        }
        this.subjectRankingLabelData = label;
        this.subjectRankingData = gained;

      }).catch((error) => {
        console.log('getSubDouble Err :' + error);
      })
    },
    cutTime(value){
      /* 값 변경 시작 */
      if(value != null){
        let timeData = value.split(':');
        let time = timeData[0] + timeData[1];

        return time;
      }
        return [];
    },
    /* 주차 계산기 */
    createWeekTime(value){
      /* 시간 크기 비교 */
      /* 같은 값 허용 */
      /* 말라초로 변환 후 비교한다. */
      let minute = 1000 * 60;
      let hour = minute * 60;
      let day = hour * 24;

      /* 연도 획득 */
      let splitDate = value.split('-');
      /* 시작점 획득 */
      let start = new Date(splitDate[0] + "-01-01");
      start = start.getTime();
      /* 지정일 획득 */
      let date = new Date(value);
      date = date.getTime();
      /* 지정일 - 시작점 + 주차보정 = 현재까지의 시간 */
      /* 음수가 나올 수 없다. */
      let days = date - start + ((splitDate[0]-2017)*day);
      /* 양수가 나오면 주(7) 단위로 나누기 */
      let week = Math.ceil(days / (day*7)) +1;
      /* 주차 조합 */
      let setTime = splitDate[0] + "-" + week;

      return setTime;
    },
    /* 날짜 예외확인 */
    checkDate(value){
      /* 정상 값인지 비교 */
      if(this.fDate != null && this.sDate != null && value != 'save'){

        let sDate = new Date(this.fDate);
        sDate = sDate.getTime();
        let eDate = new Date(this.sDate);
        eDate = eDate.getTime();

        if(sDate > eDate){
          if(value == "start"){
            /* 경고 -> 값 초기화 */
            alert('始まりの日が終わりの日より遅いです。')
            console.log('始まりの日が終わりの日より遅いです。');
            this.fDate = null
          }else if(value == "end"){
            /* 경고 -> 값 초기화 */
            alert('終わりの日が始まりの日より早いです。')
            console.log('終わりの日が始まりの日より早いです。');
            this.sDate = null
          }
          this.dateCheck = false;
        }else{
          /* 값이 정상일 경우 */
          switch (this.setPeriod_type) {
            case 'daily':
              /* 사용보류 */
              break;
            case 'weekly':
              this.startDate = this.createWeekTime(this.fDate);
              this.endDate = this.createWeekTime(this.sDate);
              break;
            case 'monthly':
              this.startDate = this.fDate;
              this.endDate = this.sDate;
              break;
          }
          this.dateCheck = true;
        }
      }
    },
    /* 관심도 설정 */
    setAttentionLevel(value, count, color){

      let setCount = 0;

      if(color == 'red'){
        if(value.attention_level == 1){
          setCount = 0;
        }else{
          setCount = count;
        }
      }else if(color == 'gray'){
        setCount = value.attention_level + count;
      }

      axios.post('/tutor/detail/attention_level/update', {
        std_id : value.id,
        attention_level : setCount
      }).then((response) => {
        /* 하트 수정 */
        this.studentInfo[value.number].attention_level = setCount;
      }).catch((error) => {
        console.log("setAttention Err : "+ error);
      })
    }
  },
  mounted(){
    this.getStudentInfo(this.studentsType);
  },
  watch : {
    fDate : function(){
      this.checkDate('start')
    },
    sDate : function(){
      this.checkDate('end')
    },
    subjectSelect : function(){
      this.selectMinorType(this.subjectSelect.id, 'code', this.subjectSelect.name)
    }
  }
}
</script>
