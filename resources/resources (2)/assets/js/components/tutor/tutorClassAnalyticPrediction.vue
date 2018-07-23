<template>
  <div class = "classAnalyticPrediction">
    <!-- Header -->
    <v-parallax class = "mainImage" src="/images/analyticPredition.jpg" height="300">
      <h1 class="categoryClassAnalytic">Class Analytic Predition</h1>
    </v-parallax>

    <!-- 기간 설정 영역 -->
        <v-dialog v-model="dialog" width="750px">
          <v-card>
            <v-card-title class="grey lighten-4 py-4 title" style="font-family:Nanum Gothic Coding;">
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
                     <h2>最近（10週）は期間の変更ができません。</h2>
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

    <!-- 메인 -->
    <v-container grid-list-xl>
      <v-layout row wrap align-center>

        <div class="classAnalyticTopDiv">

          <div class = "classAttendanceCartCard">
            <v-card class = "classAttendanceCartBox">
              <v-card-text>
                <v-flex xs12>
                  <v-layout row wrap align-center>
                    <v-flex xs12 md1>
                      <v-icon color = "light-blue darken-2" large>pie_chart</v-icon>
                    </v-flex>
                    <v-flex xs12 md11>
                      <h2 class = "chartTitle">出席の情報分析
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
              <v-flex xs12>
                <v-container grid-list-xl>
                  <v-layout row wrap align-center>
                      <v-flex xs5>
                        <v-select
                          :items="attendance"
                          v-model="attSelect"
                          :label="attendance[0].text"
                          single-line
                        ></v-select>
                      </v-flex>
                      <v-flex xs7 v-if="attSelect.selected != 3">
                        <v-flex xs12>
                          <v-container style="position:relative; right: 30px;">
                            <v-layout row wrap align-center>
                              <v-flex xs12 md4>
                                  <v-btn depressed small round color="blue accent-3" dark v-on:click="adaChartController('lateness')">遅刻</v-btn>
                              </v-flex>
                              <v-flex xs12 md4>
                                <v-btn depressed small round color="blue accent-3" dark v-on:click="adaChartController('absence')">欠席</v-btn>
                              </v-flex>
                              <v-flex xs12 md4>
                                  <v-btn depressed small round color="blue accent-3" dark v-on:click="adaChartController('early_leave')">早退</v-btn>
                              </v-flex>
                            </v-layout>
                          </v-container>
                        </v-flex>
                      </v-flex>
                  </v-layout>
                  <v-flex xs12>
                    <v-container>
                      <v-layout row wrap align-center>
                        <v-flex xs12 md12>
                            <div v-if="attSelect.selected != 3"><h2 style="font-family: Gothic A1;font-weight: lighter;"> {{ this.attSelect.text }} ( {{ this.attendanceChartStat }} )</h2></div>
                            <pie-chart v-if="attSelect.selected == 1" :width="500" :data="attendanceData" :backgroundColor="attendanceColor" :labels="attendanceLabelData" :options="{ responsive: true, maintainAspectRatio: false }"></pie-chart>
                            <line-chart-lateness v-if="attSelect.selected == 2" :width="310" :data="attendanceLineData" :borderColor="attendanceLineColor" :labels="attendanceLineLabelData" :options="{ responsive: true, maintainAspectRatio: false }"></line-chart-lateness>
                            <line-chart-holiday v-if="attSelect.selected == 3" :width="300" :data="holidayData" :labels="holidayLabelData" :options="{ responsive: true, maintainAspectRatio: false }"></line-chart-holiday>
                        </v-flex>
                      </v-layout>
                    </v-container>
                  </v-flex>
                </v-container>
              </v-flex>
            </v-card>
          </div>

          <!-- 학업 관련 차트 영역 -->
          <div class = "classGradeCartCard">
            <v-card class = "classGradeCartBox">
              <v-card-text>
                <v-flex xs12>
                  <v-layout row wrap align-center>
                    <v-flex xs12 md1>
                      <v-icon color = "light-blue darken-2" large>bar_chart</v-icon>
                    </v-flex>
                    <v-flex xs12 md11>
                      <h2 class = "chartTitle">学業情報分析
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
              <v-flex xs12>
                <v-container grid-list-xl>
                  <v-layout row wrap align-center>
                      <v-flex xs6>
                        <v-select
                          :items="subjectList"
                          v-model="subjectSelect"
                          :label="subjectSelect.text"
                          single-line
                        ></v-select>
                      </v-flex>
                      <v-flex xs6>
                          <v-select
                            :items="subjectsList"
                            v-model="subjectsSelect"
                            :label="subjectsSelect.text"
                            single-line
                          ></v-select>
                      </v-flex>
                  </v-layout>
                  <v-flex xs12>
                    <v-container>
                      <v-layout row wrap align-center>
                        <v-flex xs12 md6>
                          <div><h2 style="font-family: Gothic A1;font-weight: lighter;">取得点数分布範囲 ( {{ this.studyChartStat }} )</h2></div>
                          <bar-plot-chart :datasets="plotDataSets" :labels="plotLabelData" :options="{ responsive: true, maintainAspectRatio: false }"></bar-plot-chart>
                        </v-flex>
                        <v-flex xs12 md6>
                          <div><h2 style="font-family: Gothic A1;font-weight: lighter;">取得点数分布図 ( {{ this.studysChartStat }} )</h2></div>
                          <bar-chart :data="gradeData" :labels="gradeLabelData" :options="{ responsive: true, maintainAspectRatio: false }"></bar-chart>
                        </v-flex>
                      </v-layout>
                    </v-container>
                  </v-flex>
                </v-container>
              </v-flex>
            </v-card>
          </div>
        </div>
      </v-layout>
    </v-container>
  </v-flex>
</div>

  </div>
</template>

<style>

.classAnalyticTopDiv {
  position: relative;
  width: 2000px;
  bottom: 200px;
  margin-left: 5px;
}

.classAttendanceCartCard {
  float: left;
  margin: 5px;
  width: 40%;
}

.classAttendanceCartBox {
  border-radius: 0.7975rem;
  box-shadow: 0 4px 10px 0 rgba(161, 161, 161, 0.36);
}

.classGradeCartCard {
  float: left;
  margin: 5px;
  width: 58%;
}
.classGradeCartBox {
  border-radius: 0.7975rem;
  box-shadow: 0 4px 10px 0 rgba(161, 161, 161, 0.36);
}


/*-- 헤더 영역 --*/
.categoryClassAnalytic {
  color: #FFFFFF;
  font-size: 30px;
  position: relative;
  font-family: "Montserrat";
  font-weight: Bold;
  position: relative;
  left: 38px;
  bottom: 60px;
}

.chartTitle {
  font-family: "Mplus 1p";
  font-weight: normal;
  font-size: 30px;
  position: relative;
  left: 10px;
}


</style>
<script>

import Vue from 'vue'
import VueChartJs from 'vue-chartjs'
import {Pie, Line} from 'vue-chartjs'

/* 출결 현황 비교 그래프 */
Vue.component('pie-chart', {
  extends : VueChartJs.Pie,
  props: ['data', 'backgroundColor', 'labels', 'options'],
  mounted(){
    this.renderPieChart();
  },
  /* 선언된 chart 의 data 속성에 값을 반환한다. */
  /* 지정된 변수명 : 실행할 함수 */
  /* props 에 선언된 속성에 지정된 변수가 공유된다.? */
  computed: {
    attendanceData : function(){
      return this.data
    },
    attendanceColor : function(){
      return this.backgroundColor
    },
    attendanceLabelData : function(){
      return this.labels
    }
  },
  methods : {
    renderPieChart : function(){
      this.renderChart({
        labels: this.attendanceLabelData,
        datasets:[{
            backgroundColor: this.attendanceColor,
            pointBackgroundColor: 'white',
            pointBorderColor: '#249EBF',
            data: this.attendanceData
          }],
        options:{
          responsive: true,
          maintainAspectRatio: false,
          height: 100
        }
      },
      { responsive: true, maintainAspectRatio: false }
    )
  }
},
watch: {
  /* 값 변경을 감지하면 실행 */
  /* 지정 변수가 바뀌어야한다. 하위의 값이 바뀌는 것은 해당하지 않는다. */
  attendanceData : function(){
    this.$data._chart.destroy();
    this.renderPieChart();
  }
}
});

/*  평균 출결 인원 */
Vue.component('line-chart-lateness', {
  extends : VueChartJs.Line,
  props: ['data', 'labels', 'borderColor', 'options'],
  mounted(){
    this.renderLineChart();
  },
  /* 선언된 chart 의 data 속성에 값을 반환한다. */
  computed: {
    attendanceLineData : function(){
      return this.data
    },
    attendanceLineLabelData : function(){
      return this.labels
    },
    attendanceLineColor : function(){
      return this.borderColor
    }
  },
  methods : {
    renderLineChart : function(){
      this.renderChart({
        /* 기간 내의 날짜 */
        labels:this.attendanceLineLabelData,
        datasets:[
          {
            label: '遅刻',
            backgroundColor : false,
            borderColor: '#f6c202',
            fill: false,
            data: this.attendanceLineData
          }
        ],
      },
      /* 옵션이 들어갈 영역 */
      {
        scales: {
             yAxes: [{
               ticks: {
                min : 0,
                max : 60
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
  attendanceLineData : function(){
    this.$data._chart.destroy();
    this.renderLineChart();
  }
  }
});

/* 지각 시간 비교 그래프  */
Vue.component('line-chart-holiday', {
  extends : VueChartJs.Line,
  props: ['data', 'labels', 'options'],
  mounted(){
    this.renderLineChart();
  },
  /* 선언된 chart 의 data 속성에 값을 반환한다. */
  computed: {
    holidayData : function(){
      return this.data
    },
    holidayLabelData : function(){
      return this.labels
    }
  },
  methods : {
    renderLineChart : function(){
      this.renderChart({
        /* 기간 내의 날짜 */
        labels:this.holidayLabelData,
        datasets:[
          {
            label: '週末の出席した学生の数',
            borderColor: '#f53e3e',
            fill: false,
            data: this.holidayData
          }
        ]
      },
      /* 옵션이 들어갈 영역 */
      {
        scales: {
             yAxes: [{
               ticks: {
                min: 0
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
  holidayData : function(){
    this.$data._chart.destroy();
    this.renderLineChart();
  }
  }
});

/* 취득점수 분포 범위 */
Vue.component('bar-plot-chart', {
  extends : VueChartJs.Bar,
  props: ['datasets', 'labels', 'options'],
  mounted(){
    this.renderBarPlotChart();
  },
  /* 선언된 chart 의 data 속성에 값을 반환한다. */
  computed: {
    plotDataSets : function(){
      return this.datasets
    },
    plotLabelData : function(){
      return this.labels
    }
  },
  methods : {
    renderBarPlotChart : function(){
      this.renderChart({
        /* 기간 내의 날짜 */
        labels: this.plotLabelData,
        datasets: this.plotDataSets
      },
      {
        scales: {
             yAxes: [{
               stacked: true,
               ticks: {
                min: 0
               }
             }],
             xAxes: [{
               stacked: true
             }]
           },
        legend: {
            display: false
        },
        responsive: true,
        maintainAspectRatio: false,
      }
    )
  }
  },
  watch: {
  /* 값 변경을 감지하면 실행 */
  /* 지정 변수가 바뀌어야한다. 하위의 값이 바뀌는 것은 해당하지 않는다. */
  plotDataSets : function(){
    this.$data._chart.destroy();
    this.renderBarPlotChart();
  },
  plotLabelData : function(){
    this.$data._chart.destroy();
    this.renderBarPlotChart();
  }
  }
});

/* 취득점수 분포도 */
Vue.component('bar-chart', {
  extends : VueChartJs.Bar,
  props: ['data', 'labels', 'options'],
  mounted(){
    this.renderBarChart();
  },
  /* 선언된 chart 의 data 속성에 값을 반환한다. */
  computed: {
    gradeData : function(){
      return this.data
    },
    gradeLabelData : function(){
      return this.labels
    }
  },
  methods : {
    renderBarChart : function(){
      this.renderChart({
        /* 기간 내의 날짜 */
        labels: this.gradeLabelData,
        datasets:[
          {
            label : "取得点数分布図",
            backgroundColor : '#4867fb',
            borderColor: '#3c0cc6',
            fill: false,
            data: this.gradeData
          },
        ]
      },
      {
        scales: {
             yAxes: [{
               ticks: {
                min: 0
               }
             }]
           },
        width:300,
        responsive: true,
        maintainAspectRatio: false
      }
    )
  }
  },
  watch: {
    /* 값 변경을 감지하면 실행 */
    /* 지정 변수가 바뀌어야한다. 하위의 값이 바뀌는 것은 해당하지 않는다. */
    gradeData : function(){
      this.$data._chart.destroy();
      this.renderBarChart();
    }
  }
});

export default {
    data () {
        return {
          /* 출석 차트 종류 */
          attSelect : { text: '出席回数比率', selected:'1' },
          subjectSelect : { text: '情報を探しています。'},
          subjectsSelect : { text: '情報を探しています。'},
          attendance: [
            { text : '出席回数比率', selected:'1' },
            { text : '平均出席した人の数', selected:'2' },
            { text : '週末に出席した学生の数', selected:'3' },
          ],
          /* 기간 설정 */
          dialog : false,
          periodSelected : '最近',
          fDate : null,
          sDate : null,
          startDate : null,
          endDate : null,
          dateCheck : false,
          setPeriod_type : 'recently',
          selectAtt : 'lateness',
          /* 상태 */
          attendanceChartStat : '',
          studyChartStat : '',
          studysChartStat : '',
          /* 과목 */
          subjectList : [{ id : '', name : '行っている講義がありません。' }],
          subjectsList: [{ id : '', name : '試験の情報がありません。' }],
          subjectCode : null,
          subjectsCode : null,
          /* 그래프 값 변수들*/

          attendanceData : [],
          attendanceLabelData : [],
          attendanceColor : [],

          attendanceLineData : [],
          attendanceLineLabelData : [],

          holidayData : [],
          holidayLabelData : [],

          plotDataSets : [],
          plotLabelData : [],

          gradeData : [],
          gradeLabelData : []
        }
    },
    methods : {
      adaChartController(select){
        /* 상태표시 */
        switch (select) {
          case 'lateness':
            this.attendanceChartStat = '遅行';
            this.selectAtt = 'lateness';
            break;
          case 'early_leave':
            this.attendanceChartStat = '早退';
            this.selectAtt = 'early_leave';
            break;
          case 'absence':
            this.attendanceChartStat = '欠席';
            this.selectAtt = 'absence';
            break;
        }
        this.getAttendancePieData();
        this.getAttendanceLineData();
        this.getHolidayLineData();
      },
      /* 강의 그래프 컨트롤러 */
      studyChartController(value, name, set){
        switch (set) {
          case 'subject':
            this.studyChartStat = name;
            this.subjectCode = value;
            this.getStudyScore();
            this.getStudeySubScore();
            break;
          case 'subjects' :
            this.studysChartStat = name;
            this.subjectsCode = value;
            this.getStudeySubScore();
            break;
        }

      },
      /* 날짜 */
      selectPeriod(value){
        switch (value) {
          case 'daily' :
            // 사용대기
            break;
          case 'weekly':
            this.sDate = null;
            this.fDate = null;
            this.dateCheck = false;
            this.setPeriod_type = value;
            this.periodSelected = '週間';
            break;
          case 'monthly':
            this.sDate = null;
            this.fDate = null;
            this.dateCheck = false;
            this.setPeriod_type = value;
            this.periodSelected = '月間';
            break;
          case 'recently':
            this.sDate = null;
            this.fDate = null;
            this.dateCheck = false;
            this.setPeriod_type = value;
            this.periodSelected = '最近';
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
              alert('(リセット)間違って入力です。')
            }else{
              this.adaChartController();
              this.getStudyScore();
              this.getStudeySubScore();
            }
        }
      },
      /* 수강 강의 목록 */
      getSubjectList(){
        axios.get('/tutor/class/subject_list', )
        .then((response) => {
          /* 초기화 */
          this.subjectList = [];
          for(let start = 0; start < response.data.message.subjects.length; start++){
            this.subjectList.push(response.data.message.subjects[start]);
          }
          /* select 리스트 용 text 추가 */
          for(let subData in this.subjectList){
            this.$set(this.subjectList[subData], 'text', this.subjectList[subData].name);
          }
          /* 과목 코드 기본 값 설정 */
          this.subjectCode = this.subjectList[0].id;
          this.studyChartStat = this.subjectList[0].name;
          this.subjectSelect = this.subjectList[0];
        }).catch((error) => {
          console.log('getSub Err :' + error);
        })
      },
      /* 출결 비율 pie 그래프 함수 */
      getAttendancePieData(select){

        let paramData = [{
          major_class : 'ada',
          graph_type  : 'pie',
          period_type : 'recently',
          minor_class : this.selectAtt
        }];
        /* 날짜 데이터 추가 */
        if(this.setPeriod_type != 'recently'){
          this.$set(paramData[0], 'start_date', this.startDate);
          this.$set(paramData[0], 'end_date', this.endDate);
        }

        axios.get('/tutor/analyse/result',{
          params : paramData[0]
        }).then((response)=>{
          /* label 데이터 */
          let labels = Object.keys(response.data.message.value);

          this.attendanceLabelData = [];

          for(let datas in response.data.message.value){
            this.attendanceLabelData.push(response.data.message.value[datas].name);
          }

          /* color 생성 */
          this.attendanceColor = this.createdColor(labels.length);
          /* 그래프의 값 생성 */
          /* 라벨의 갯수 만큼 하나하나 확인하여 값을 추가 (해당 인원수) */
          let tempValue = [];

          for(let datas in response.data.message.value){
            tempValue.push(response.data.message.value[datas].count);
          }

          this.attendanceData = tempValue;
        }).catch((error)=>{
          console.log("getAttPieErr :" + error);
        })
      },
      /* 평균 출결 인원 그래프 함수 */
      getAttendanceLineData(){

        let paramData = [{
          major_class : 'ada',
          graph_type  : 'single_line',
          period_type : 'recently',
          minor_class : this.selectAtt
        }];
        /* 날짜 데이터 추가 */
        if(this.setPeriod_type != 'recently'){
          this.$set(paramData[0], 'start_date', this.startDate);
          this.$set(paramData[0], 'end_date', this.endDate);
        }

        axios.get('/tutor/analyse/result',{
          params : paramData[0]
        }).then((response)=>{

          let label = [];
          let data = [];

          for(let start = 0; start < response.data.message.value.length; start++){
            if(response.data.message.value[start]['y-point'] == null){
              data.push(0);
            }else{
              data.push(response.data.message.value[start]['y-point'])
            }
            label.push(response.data.message.value[start]['x-point'])
          }

          this.attendanceLineData      = data;
          this.attendanceLineLabelData = label;

        }).catch((error)=>{
          console.log('attLine Error :' + error);
        })
      },
      /* 휴일 등교 인원 그래프 함수 */
      getHolidayLineData(){

        let paramData = [{
          major_class : 'ada',
          graph_type  : 'single_line',
          period_type : 'recently',
          minor_class : 'holiday'
        }];

        /* 날짜 데이터 추가 */
        if(this.setPeriod_type != 'recently'){
          this.$set(paramData[0], 'start_date', this.startDate);
          this.$set(paramData[0], 'end_date', this.endDate);
        }

        axios.get('/tutor/analyse/result',{
          params : paramData[0]
        }).then((response)=>{

        let label = [];
        let data = [];

        for(let start = 0; start < response.data.message.value.length; start++){
          if(response.data.message.value[start]['y-point'] == null){
            data.push(0);
          }else{
            data.push(response.data.message.value[start]['y-point'])
          }
          label.push(response.data.message.value[start]['x-point'])
        }

        this.holidayData      = data;
        this.holidayLabelData = label;


        }).catch((error)=>{
          console.log('holiday Error :' + error);
        })
      },
      /* 강의별 취득점수 분포 */
      getStudyScore(){

        let paramData = [{
          major_class : 'study',
          graph_type  : 'box_and_whisker',
          period_type : 'recently',
          minor_class : 'subject_' + this.subjectCode
        }];

        /* 날짜 데이터 추가 */
        if(this.setPeriod_type != 'recently'){
          this.$set(paramData[0], 'start_date', this.startDate);
          this.$set(paramData[0], 'end_date', this.endDate);
        }

        axios.get('/tutor/analyse/result', {
          params : paramData[0]
        }).then((response)=>{
          /* 기본 데이터 생성 */
          let data = [];
          let label = [];
          let setting = [{
            'red' : [2,4,6,8,10],
            'none': [1,3,7,9,11],
            'blue': [5,7]
          }];

          for(let start = 1; start <= 11; start++){
            if(setting[0]['red'].indexOf(start) != -1){
              data.push({ data : [], backgroundColor : "#ff2d2d" });
            }else if(setting[0]['blue'].indexOf(start) != -1){
              data.push({ data : [], backgroundColor : "#8f96ff" });
            }else if(setting[0]['none'].indexOf(start) != -1){
              data.push({ fill : false, data : []});
            }
          }

          /* 그래프 데이터 가공 */
          for(let start = 0; start < response.data.message.value.length; start++){
            /* 라벨 저장 */
            label.push(response.data.message.value[start]['x-point']);
            /* 데이터 연산 = 저장 */
            data[0]['data'].push(response.data.message.value[start]['y-point']['min']-1);
            data[1]['data'].push(3);
            data[2]['data'].push(response.data.message.value[start]['y-point']['75%']-response.data.message.value[start]['y-point']['min']-2);
            data[3]['data'].push(3);
            data[4]['data'].push(response.data.message.value[start]['y-point']['avg']-response.data.message.value[start]['y-point']['75%']-2);
            data[5]['data'].push(3);
            data[6]['data'].push(response.data.message.value[start]['y-point']['25%']-response.data.message.value[start]['y-point']['avg']-2);
            data[7]['data'].push(3);
            data[8]['data'].push(response.data.message.value[start]['y-point']['max']-response.data.message.value[start]['y-point']['25%']-2);
            data[9]['data'].push(3);
            data[10]['data'].push(
              250 -
              (data[0]['data'][start] + data[1]['data'][start] + data[2]['data'][start] + data[3]['data'][start] + data[4]['data'][start] +
               data[5]['data'][start] + data[6]['data'][start] + data[7]['data'][start] + data[8]['data'][start] + data[9]['data'][start])
            );
          }
          this.plotDataSets = data;
          this.plotLabelData = label;

          /* 종목별 메뉴 데이터 추출 */
          this.subjectsList = [];
          for(let start = 0; start < response.data.message.value.length; start++){
            this.subjectsList.push({
              'id' : response.data.message.value[start]['score_id'],
              'name' : response.data.message.value[start]['x-point'] +
                      '(' + response.data.message.value[start]['detail'].type + ')'
            })
          }
          /* select 리스트 용 text 추가 */
          for(let subs in this.subjectsList){
            this.$set(this.subjectsList[subs], 'text', this.subjectsList[subs].name);
          }
          /* 기본값 설정 */
          this.subjectsCode = this.subjectsList[0].id;
          this.studysChartStat = this.subjectsList[0].name;
          this.subjectsSelect = this.subjectsList[0];
        }).catch((error)=>{
          console.log('box Error :' + error);
        })
      },
      /* 성적별 취득점수 분포도 */
      getStudeySubScore(){

        let paramData = [{
          major_class : 'study',
          graph_type  : 'histogram',
          period_type : 'recently',
          minor_class : 'score_' + this.subjectsCode
        }];

        /* 날짜 데이터 추가 */
        if(this.setPeriod_type != 'recently'){
          this.$set(paramData[0], 'start_date', this.startDate);
          this.$set(paramData[0], 'end_date', this.endDate);
        }
        /* 예외처리 - 성적코드 확인 */
        if(this.subjectsCode != null)
        axios.get('/tutor/analyse/result', {
          params : paramData[0]
        }).then((response)=>{

          let label = [];
          let data = [];

          for(let start = 0; start < response.data.message.value.length; start++){
            label.push(response.data.message.value[start]['x-point']);
            data.push(response.data.message.value[start]['y-point']);
          }
          this.gradeData      = data;
          this.gradeLabelData = label;

        }).catch((error) => {
          console.log('subSubject Error :' + error);
        })
      },
      /* 색상 랜덤 함수 ( int 수 만큼 생성 )*/
      createdColor(count){
        // 색상 저장 변수
        let rgbColor = [];
        // 지정한 수 만큼 반복
        for(let start = 0; start < count; start++){
          // 변수 초기화
          rgbColor[start] = '#';
          // 1회당 2문자씩 총 6문자의 RGB 색상을 만든다.
          for(let rgb = 0; rgb < 6; rgb++){
            let ranValue = Math.ceil(Math.random()*16)-1;
            switch (ranValue) {
              case 10 :
                rgbColor[start] += 'a';
                break;
              case 11 :
                rgbColor[start] += 'b';
                break;
              case 12 :
                rgbColor[start] += 'c';
                break;
              case 13 :
                rgbColor[start] += 'd';
                break;
              case 14 :
                rgbColor[start] += 'e';
                break;
              case 15 :
                rgbColor[start] += 'f';
                break;
              default :
                rgbColor[start] += ranValue;
            }
            ranValue = null;
          }
          // 중복검사
          for(let dupCheck = 0; dupCheck < start-1; dupCheck++){
            // 지금 만들어 낸 것과 이전의 값을 비교
            if(rgbColor[dupCheck] == rgbColor[start]){
              //console.log('중복확인 :' + start + ':' + rgbColor[start]);
              // 중복 확인시, 초기화 -> 재생성
              rgbColor[start] = '';
              start--;
            }
          }
        }
        // 만들어진 색상을 반환
        return rgbColor;
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
      }
    },
    mounted(){
      this.adaChartController('lateness');
      this.getSubjectList();
    },
    watch : {
      fDate : function(){
        this.checkDate('start')
      },
      sDate : function(){
        this.checkDate('end')
      },
      subjectSelect : function (){
        this.studyChartController(this.subjectSelect.id, this.subjectSelect.name, 'subject');
      },
      subjectsSelect : function (){
        this.studyChartController(this.subjectsSelect.id, this.subjectsSelect.name, 'subjects')
      }
    }
}

</script>
