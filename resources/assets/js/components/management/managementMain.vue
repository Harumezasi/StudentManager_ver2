<template>
  <div class = "tutorStudentAttendance fontSetting">

    <!-- 출결 그래프 영역 -->
    <v-flex xs12>
      <v-container grid-list-xl>
        <v-layout row wrap align-center>
          <v-flex xs12 md12>
            <v-card class="elevation-1" color= "white">
              <v-card-text>
                <h2 class = "cardInsideTitle">출결 그래프</h2>
                <v-container>
                  <v-layout>
                    <v-flex xs6>
                      <!-- 출결 그래프 -->
                      <attendance-pie-chart :data="attendanceData" :width="2" :height="1"></attendance-pie-chart>
                    </v-flex>
                    <v-flex xs6>
                      <!-- 등교 하교시간 비교 그래프 -->
                      <checkInOut-doubleLine-chart :datasets="attendanceDatasets" :labels="attendanceLabelData" :width="2" :height="1"></checkInOut-doubleLine-chart>
                    </v-flex>
                  </v-layout>
                </v-container>
              </v-card-text>
            </v-card>
          </v-flex>
        </v-layout>
      </v-container>
    </v-flex>

    <!--출결 상세정보 영역 = 누적데이터 정리 -->
    <v-flex xs12>
      <v-container grid-list-xl>
        <v-layout row wrap align-center>

          <v-flex xs12 md12>
            <v-card class="elevation-1" color = "white">
              <v-card-text>
                <h2 class = "cardInsideTitle">상세보기</h2>
              </v-card-text>
              <v-container fluid grid-list-md>
                <v-data-iterator
                  :items="attendanceStats"
                  content-tag="v-layout"
                  row
                  wrap
                >
                  <v-flex slot="item" slot-scope="props" xs6 md6>
                    <v-card class = "elevation-0">
                      <v-card-title><h4>{{ props.item.name }}</h4></v-card-title>
                      <v-divider></v-divider>
                      <v-list dense>
                        <v-list-tile>
                          <v-list-tile-content class="fontSetting">{{ props.item.countType }} </v-list-tile-content>
                          <v-list-tile-content class="align-end fontSetting">{{ props.item.count }}</v-list-tile-content>
                        </v-list-tile>
                        <v-list-tile>
                          <v-list-tile-content class="fontSetting">{{ props.item.continuityType }}</v-list-tile-content>
                          <v-list-tile-content class="align-end fontSetting">{{ props.item.continuityNum }}</v-list-tile-content>
                        </v-list-tile>
                        <v-list-tile>
                          <v-list-tile-content class="fontSetting">{{ props.item.recentlyType }}</v-list-tile-content>
                          <v-list-tile-content class="align-end fontSetting">{{ props.item.recentlyDate }}</v-list-tile-content>
                        </v-list-tile>
                      </v-list>
                    </v-card>
                  </v-flex>
                </v-data-iterator>
              </v-container>
            </v-card>
          </v-flex>

        </v-layout>
      </v-container>
    </v-flex>

    <!--출결 상세정보 영역 = 순차적 데이터 -->
    <v-flex xs12>
      <v-container grid-list-xl>
        <v-layout row wrap align-center>
          <v-flex xs12 md12>
              <v-card class = "elevation-0">
                <v-data-table
                 :headers="attendanceHeaders"
                 :items="attendanceDatas"
                 :pagination.sync="attendancePagination"
                 class="elevation-1"
                 id='fontSetting'
                 >
               <template slot="items" slot-scope="props">
                 <td class="text-xs-center">{{ props.item.reg_date }}</td>
                 <td class="text-xs-center">{{ props.item.sign_in }}</td>
                 <td class="text-xs-center">{{ props.item.sign_in_time }}</td>
                 <td class="text-xs-center">{{ props.item.sign_in_message }}</td>
                 <td class="text-xs-center">{{ props.item.sign_out }}</td>
                 <td class="text-xs-center">{{ props.item.sign_out_time }}</td>
                 <td class="text-xs-center">{{ props.item.sign_out_message }}</td>
               </template>

              </v-data-table>
               <div class="text-xs-center pt-2">
                 <v-pagination v-model="attendancePagination.page" :length="attendancePages"></v-pagination>
               </div>
              </v-card>
          </v-flex>
        </v-layout>
      </v-container>
    </v-flex>
    <!--출결 분석표 영역 -->
    <v-flex xs12>
      <v-container grid-list-xl>
        <v-layout row wrap align-center>

          <v-flex xs12 md12>
            <v-card class="elevation-1" color = "white">
              <v-card-text>
                <h2 class = "cardInsideTitle">출결 분석</h2>
              </v-card-text>
              <v-data-table
                :headers="headers"
                :items="attendanceAnalysis"
                hide-actions
                class="elevation-0"
                id="fontSetting"
              >
                <template slot="items" slot-scope="props">
                  <td>{{ props.item.frequent_data.lateness }}</td>
                  <td>{{ props.item.frequent_data.early_leave }}</td>
                  <td>{{ props.item.frequent_data.absence }}</td>
                  <td>{{ props.item.lateness_average }}</td>
                </template>
              </v-data-table>
              <v-data-table
                :headers="headers2"
                :items="attendanceAnalysisMonth"
                hide-actions
                class="elevation-0"
                id='fontSetting'
              >
                <template slot="items" slot-scope="props">
                  <td>{{ props.item.average_data.lateness }}</td>
                  <td>{{ props.item.average_data.early_leave }}</td>
                  <td>{{ props.item.average_data.absence }}</td>
                  <td>{{ props.item.reason.lateness }}</td>
                </template>
              </v-data-table>
            </v-card>
          </v-flex>

        </v-layout>
      </v-container>
    </v-flex>


  </div>
</template>

<style>
.cardInsideTitle {
  font-family: "Nanum Gothic Coding";
  font-weight: lighter;
  border-bottom: 1px solid;
  padding-bottom: 6px;
  border-color: rgba(187, 187, 187, 0.73);
}

.fontSetting {
  font-size: 18px;
  font-weight: lighter;
  font-style: 'Gothic A1';
}

#fontSetting td {
  font-size: 14px;
  font-weight: lighter;
  font-style: 'Gothic A1';
}
</style>


<script>

import Vue from 'vue'
import VueChartJs from 'vue-chartjs'

/* 등교 하교 시간 비교 그래프  */
Vue.component('attendance-pie-chart', {
  extends : VueChartJs.Pie,
  props: ['data', 'options'],
  mounted(){
    this.renderPieChart();
  },
  /* 선언된 chart 의 data 속성에 값을 반환한다. */
  computed: {
    attendanceData : function(){
      return this.data
    }
  },
  methods : {
    renderPieChart : function(){
      this.renderChart({
        /* 기간 내의 날짜 */
        labels: ['출석','지각','결석','조퇴'],
        datasets: [{
            backgroundColor: ['#10a236', '#f9cd41', '#fe7272', '#5c7add'],
            pointBackgroundColor: 'white',
            pointBorderColor: '#249EBF',
            data: this.attendanceData
          }],
          options:{
            responsive: true,
            maintainAspectRatio: false
          }
        }
      )
    }
  },
  watch : {
    attendanceData : function(){
      this.$data._chart.destroy();
      this.renderPieChart();
    }
  }
});

/* 등교 하교 시간 비교 그래프  */
Vue.component('checkInOut-doubleLine-chart', {
  extends : VueChartJs.Line,
  props: ['datasets', 'labels', 'options'],
  mounted(){
    this.renderLineChart();
  },
  /* 선언된 chart 의 data 속성에 값을 반환한다. */
  computed: {
    attendanceLabelData : function(){
      return this.labels
    },
    attendanceDatasets : function(){
      return this.datasets
    }
  },
  methods : {
    renderLineChart : function(){
      this.renderChart({
        /* 기간 내의 날짜 */
        labels: this.attendanceLabelData,
        datasets:this.attendanceDatasets
      },
      /* 옵션이 들어갈 영역 */
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
  watch : {
    attendanceLabelData : function(){
      this.$data._chart.destroy();
      this.renderLineChart();
    }
  }
});




export default {
   data: () => ({
     /* 그래프 데이터 변수 */
     attendanceData : [],
     /* */
     attendanceLabelData : [],
     attendanceDatasets : [{
       label: '등교&지각',
       borderColor: '#18a62a',
       fill: false,
       data: []
     },
     {
       label: '하교&조퇴',
       borderColor: '#f48080',
       fill: false,
       data: []
     }],
     /*--- 출결 상세정보 테이블 --*/
     attendanceStats: [
       {
         name: '출석',
         countType : '횟수',
         count: null,
         continuityType : '등교 시간',
         continuityNum: null,
         recentlyType : '하교 시간',
         recentlyDate: null
       },
       {
         name: '지각',
         countType : '횟수',
         count : null,
         continuityType : '연속 횟수',
         continuityNum : null,
         recentlyType : '최근 일자',
         recentlyDate : null
       },
       {
         name: '결석',
         countType : '횟수',
         count: null,
         continuityType : '연속 횟수',
         continuityNum: null,
         recentlyType : '최근 일자',
         recentlyDate: null
       },
       {
         name: '조퇴',
         countType : '횟수',
         count: null,
         continuityType : '연속 횟수',
         continuityNum: null,
         recentlyType : '최근 일자',
         recentlyDate: null
       }
     ],
     /* 상세 데이터  순차적 */
     /* 학생정보 */
      attendancePagination: {
        /* 테이블에 표시될 데이터 수, 기본 값*/
        rowsPerPage: 10
      },
      attendanceDatas : [],
      attendanceHeaders: [
        {
          text: '날짜',
          sortable: false,
          align: 'center'
        },
        {
          text: '등교',
          sortable: false,
          align: 'center'
        },
        {
         text: '시간',
         sortable: false,
         align: 'center'
       },
       {
         text: '비고',
         sortable: false,
         align: 'center'
       },
       {
         text: '하교',
         sortable: false,
         align: 'center'
       },
       {
         text: '시간',
         sortable: false,
         align: 'center'
       },
       {
         text: '비고',
         sortable: false,
         align: 'center'
       }
     ],
     /*--- 출결 분석 테이블 --*/
     headers: [
       {text: '자주 지각하는 요일', value: 'lateWeek'},
       {text: '자주 조퇴하는 요일', value: 'leaveEarlyWeek'},
       {text: '자주 결석하는 요일', value: 'absenceWeek'},
       {text: '평균 지각 시간', value: 'averageLateTime'},
     ],
     attendanceAnalysis: [],
     headers2: [
       {text: '월 평균 지각 횟수', value: 'lateNum'},
       {text: '월 평균 조퇴 횟수', value: 'leaveEarlyNum'},
       {text: '월 평균 결석 횟수', value: 'absenceNum'},
       {text: '조퇴/지각 주요 사유', value: 'reason'},
     ],
     attendanceAnalysisMonth: [],
   }),
   methods: {
     /* 출결 분석 데이터 */
    getData() {
      axios.get('/tutor/detail/attendance/analyse',
      {
        params: {
          std_id : this.$router.history.current.query.getInfoIdType
      }}).then((response) => {
        /* 데이터 추출 */
        this.attendanceAnalysis.push(response.data.message);
        this.attendanceAnalysisMonth.push(response.data.message);
        /* 지각 시간 메세지 가공 */
        let str = Math.floor(parseInt(this.attendanceAnalysisMonth[0].lateness_average)/60)+"분 "+
        parseInt(this.attendanceAnalysisMonth[0].lateness_average)%60+"초";
        /* 저장 */
        this.attendanceAnalysisMonth[0].lateness_average=str;
      }).catch((error) => {
          console.log('getData Error : ' + error);
      });
    },
    /* 출결 상세 분석 정보 get & 가공 */
    getAttendanceStatData(){
      axios.get('/tutor/detail/attendance/stat',{
        params : {
          std_id : this.$router.history.current.query.getInfoIdType
        }
      }).then((response)=>{
        let getDatas = response.data.message;
        /* 그래프에 들어갈 정보 저장*/
        this.attendanceData.push(getDatas.total_sign_in);
        this.attendanceData.push(getDatas.total_lateness);
        this.attendanceData.push(getDatas.total_absence);
        this.attendanceData.push(getDatas.total_early_leave);
        /* 정보를 저장 */
        /* 출석 */
        this.attendanceStats[0].count = getDatas.total_sign_in;
        this.attendanceStats[0].continuityNum = getDatas.today_sign_in;
        this.attendanceStats[0].recentlyDate = getDatas.today_sign_out;

        /* 지각 */
        this.attendanceStats[1].count = getDatas.total_lateness;
        this.attendanceStats[1].continuityNum = getDatas.continuative_lateness;
        this.attendanceStats[1].recentlyDate = getDatas.recent_lateness;
        /* 결석 */
        this.attendanceStats[2].count = getDatas.total_absence;
        this.attendanceStats[2].continuityNum = getDatas.continuative_absence;
        this.attendanceStats[2].recentlyDate = getDatas.recent_absences;
        /* 조퇴 */
        this.attendanceStats[3].count = getDatas.total_early_leave;
        this.attendanceStats[3].continuityNum = getDatas.continuative_early_leave;
        this.attendanceStats[3].recentlyDate = getDatas.recent_early_leave;
        /* null 값을 식별할 수 있도록 하이폰(ㅡ) 처리 */
        for(let start = 0; start < this.attendanceStats.length; start++){
          if(this.attendanceStats[start].count == null){
            this.attendanceStats[start].count = "ㅡ";
          }
          if(this.attendanceStats[start].continuityNum == null){
            this.attendanceStats[start].continuityNum = "ㅡ";
          }
          if(this.attendanceStats[start].recentlyDate == null){
            this.attendanceStats[start].recentlyDate = "ㅡ";
          }
        }

      }).catch((error)=>{
        console.log('getStatError :' + error);
      })
    },
    /* 출결 상세 정보 리스트 get & 가공 */
    getAttendanceListData(){
      axios.get('/tutor/detail/attendance/list',{
        params : {
          std_id : this.$router.history.current.query.getInfoIdType
        }
      }).then((response) => {
        console.log(response.data.message);
        this.createAttendanceDoubleLineValue(response.data.message);

        let datas = [];
        for(let start = 0; start < response.data.message.length; start++){
          /* 날짜 등록 */
          datas.push({'reg_date' : response.data.message[start].reg_date});
          /* 등교타입 확인 */
          if(response.data.message[start].absence_flag != 'good'){
            /* 결석 */
            this.$set(datas[start], 'sign_in', '결석');
            this.$set(datas[start], 'sign_in_time', 'ㅡ');
            this.$set(datas[start], 'sign_in_message', 'ㅡ');
          }else if(response.data.message[start].lateness_flag != 'good'){
            /* 지각 */
            this.$set(datas[start], 'sign_in', '지각');
            this.$set(datas[start], 'sign_in_time', response.data.message[start].sign_in_time);
            this.$set(datas[start], 'sign_in_message', response.data.message[start].detail[0].sign_in_message);

          }else {
            /* 등교 */
            this.$set(datas[start], 'sign_in', '출석');
            this.$set(datas[start], 'sign_in_time', response.data.message[start].sign_in_time);
            this.$set(datas[start], 'sign_in_message', 'ㅡ');
          }

          /* 하교타입 확인 : 결석일 경우 미확인 */
          if(response.data.message[start].absence_flag == 'good'){
            switch (response.data.message[start].early_leave_flag) {
              case 'good':
                /* 하교 */
                this.$set(datas[start], 'sign_out', '하교');
                this.$set(datas[start], 'sign_out_time', response.data.message[start].sign_out_time);
                this.$set(datas[start], 'sign_out_message', 'ㅡ');
                break;
              case 'unreason':
                /* 조퇴 */
                this.$set(datas[start], 'sign_out', '조퇴');
                this.$set(datas[start], 'sign_out_time', response.data.message[start].sign_out_time);
                this.$set(datas[start], 'sign_out_message', response.data.message[start].detail[0].sign_out_message);
                break;
            }
          }
        }
        this.attendanceDatas = datas;
      }).catch((error) => {
        console.log("listError : " + error);
      })
    },
    /* 출결 그래프 값 생성 */
    createAttendanceDoubleLineValue(value){
      let max = 10;
      let datas = [];
      let labelData = [];
      for(let start = 0; start < value.length, start<max; start++){
        /* 날짜 등록 */
        labelData.push(value[start].reg_date);

        /* 결석은 패스 */
        if(value[start].lateness_flag != "good"){
          /* 지각 */
          this.attendanceDatasets[0].data.push(this.cutTime(value[start].sign_in_time, 'in'));
        }else if(value[start].absence_flag == "good"){
          /* 등교 */
          this.attendanceDatasets[0].data.push(this.cutTime(value[start].sign_in_time, 'in'));
        }

        /* 하교타입 확인 : 결석일 경우 미확인 */
        if(value[start].absence_flag == "good"){
          switch (value[start].early_leave_flag) {
            case 'good':
              /* 하교 */
              this.attendanceDatasets[1].data.push(this.cutTime(value[start].sign_out_time, 'out'));
              break;
            case 'unreason':
              /* 조퇴 */
              this.attendanceDatasets[1].data.push(this.cutTime(value[start].sign_out_time, 'out'));
              break;
          }
        }
      }
      /* 값 반대로 뒤집기 */
      let end = labelData.length;
      let labels = [];
      let inData = [];
      let outData = [];
      for(let start = 0; start < end; start++ ){
        labels.push(labelData.pop());
        inData.push(this.attendanceDatasets[0].data.pop());
        outData.push(this.attendanceDatasets[1].data.pop());
      }
      this.attendanceDatasets[0].data = inData;
      this.attendanceDatasets[1].data = outData;
      /* x축 값 변경 = 그래프 다시 그리기 */
      this.attendanceLabelData = labels;
    },
    cutTime(value, setting){

      /* 출석 시작 기준 값 */
      let checkInStart = '0830';
      /* 값 변경 시작 */
      let data = value.split(' ');
      let timeData = data[1].split(':');
      let time = timeData[0] + timeData[1];
      /* 24시 이후 새벽에 하교하였을 경우 24시로 고정한다.*/
      if(time < checkInStart && setting == 'out'){
        time = '2400';
      }
      return time;
    }
  },
  mounted(){
    this.getAttendanceStatData();
    this.getAttendanceListData();
    this.getData();
  },
  computed: {
      attendancePages () {
        if (this.attendancePagination.rowsPerPage == null ||
          this.attendancePagination.totalItems == null
        ) return 0

        return Math.ceil(this.attendancePagination.totalItems / this.attendancePagination.rowsPerPage)
      }
    }
  }

</script>
