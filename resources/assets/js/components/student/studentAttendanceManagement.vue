<template>
<div class="studentAttendanceManagement">

  <!-- Header -->
  <v-parallax class="mainImage" src="/images/studentAttendance.jpg" height="300">
    <h1 class="categoryStdAttend">Attendance Management</h1>
  </v-parallax>

  <!-- 내용들어갈 영역 -->
  <v-flex xs12>
    <v-container grid-list-xl>
      <v-layout row wrap align-center>

        <v-card class = "attendaceCard">
          <v-flex xs12 sm4>
            <!-- 조회 기준 설정 -->
            <div>

              <v-btn color="light-green darken-2" dark v-on:click="weeklyClick()">週間</v-btn>
              <v-btn color="light-green darken-2" dark v-on:click="monthlyClick()">月間</v-btn>
            </div>

            <div v-if="period == 'monthly'">
              <v-btn color="light-green accent-4" v-if="prevDate != null" v-on:click="prevClick()">先月</v-btn>
              <v-btn color="light-green accent-4" v-else disabled>先月</v-btn>
              {{ pagiNationInfo.this }}
              <v-btn color="light-green accent-4" v-if="nextDate != null" v-on:click="nextClick()">来月</v-btn>
              <v-btn color="light-green accent-4" v-else disabled>来月</v-btn>
            </div>
            <div v-else-if="period == 'weekly'">
              <v-btn color="light-green accent-4" v-if="prevDate != null" v-on:click="prevClick()">先週</v-btn>
              <v-btn color="light-green accent-4" v-else disabled>先週</v-btn>
              {{ pagiNationInfo.this }}
              <v-btn color="light-green accent-4" v-if="nextDate != null" v-on:click="nextClick()">来週</v-btn>
              <v-btn color="light-green accent-4" v-else disabled>来週</v-btn>

            </div>
          </v-flex>

          <!-- 그래프 부분 -->
          <v-container grid-list-xl>
            <v-layout row wrap>
              <v-flex xs12 md3>
                <!-- 출석률 ( 도넛그래프 ) -->
                <div class="attendance_content">
                  <v-chip color="green" text-color="white">出席率</v-chip>
                </div>
                <div class="attendance_content">
                  <!-- attendace doughnut-Grahp start -->
                  <v-progress-circular :size="300" :width="30" :rotate="270" :value="value" color="teal">
                    {{ value }} %
                  </v-progress-circular>
                </div>
                <!-- attendace doughnut-Grahp end -->
              </v-flex>
              <!-- 한 달 동안 출,지,결,조 를 몇번 했나에 대한 그래프 -->
              <v-flex xs12 md9>
                <!-- 막대 그래프 -->
                <student-Attendance-bar-chart :data="dataChart" :options="{ responsive: true, maintainAspectRatio: false }"></student-Attendance-bar-chart>

              </v-flex>
            </v-layout>
          </v-container>

          <!-- 출석 관련 표로 보기 영역 -->
          <v-flex xs12>
            <v-data-table :headers="headers" :items="attendanceData" hide-actions class="elevation-0">
              <template slot="items" slot-scope="props">
                <!-- 출석과 최근 출석 -->
                <td class="text-xs-center">{{ props.item.sign_in }}</td>
                <td class="text-xs-center">{{ props.item.recent_sign_in }}</td>
                <!-- 지각과 최근 지각-->
                <td class="text-xs-center">{{ props.item.lateness }}</td>
                <td class="text-xs-center">{{ props.item.recent_lateness }}</td>
                <!-- 결석과 최근 결석 -->
                <td class="text-xs-center">{{ props.item.absence }}</td>
                <td class="text-xs-center">{{ props.item.recent_absence }}</td>
                <!-- 조퇴와 최근 조퇴 -->
                <td class="text-xs-center">{{ props.item.early_leave }}</td>
                <td class="text-xs-center">{{ props.item.recent_early_leave }}</td>
            </template>
            </v-data-table>
          </v-flex>
        </v-card>


      </v-layout>
    </v-container>
  </v-flex>

</div>
</template>

<script>
import Vue from 'vue'
import VueChartJs from 'vue-chartjs'

/*-- HorizontalBar --*/
Vue.component('student-Attendance-bar-chart', {
  extends: VueChartJs.HorizontalBar,
  props: ['data', 'options'],
  mounted() {
    this.renderBarChart();
  },
  /* 선언된 chart 의 data 속성에 값을 반환한다. */
  computed: {
    dataChart: function() {
      return this.data
    }
  },
  methods: {
    renderBarChart: function() {
      this.renderChart({
        labels: ['出席', '遅刻', '欠席', '早退'],
        datasets: [{
          backgroundColor: ['#009a92', '#f6c202', '#f53e3e', '#787878'],
          data: this.dataChart
        }]
      },
        {
          scales: {
            xAxes: [{
              ticks: {
                min: 0,
                max: 25,
              }
            }]
          },
         legend: {
             display: false
         },
          responsive: true,
          maintainAspectRatio: false,
          height: 100
      })
    }
  },
  watch: {
    /* 값 변경을 감지하면 실행 */
    /* 지정 변수가 바뀌어야한다. 하위의 값이 바뀌는 것은 해당하지 않는다. */
    data: function() {
      this.$data._chart.destroy();
      this.renderBarChart();
    }
  }
});

/*-- 표 데이터 --*/
export default {
  data() {
    return {
      headers: [{
          text: '出席',
          value: 'sign_in',
          align: 'center',
          sortable: false
        },
        {
          text: '最近の出席',
          value: 'recent_sign_in',
          align: 'center',
          sortable: false
        },
        {
          text: '遅刻',
          value: 'lateness',
          align: 'center',
          sortable: false
        },
        {
          text: '最近の遅刻',
          value: 'recent_lateness',
          align: 'center',
          sortable: false
        },
        {
          text: '欠席',
          value: 'absence',
          align: 'center',
          sortable: false
        },
        {
          text: '最近の欠席',
          value: 'recent_absence',
          align: 'center',
          sortable: false
        },
        {
          text: '早退',
          value: 'early_leave',
          align: 'center',
          sortable: false
        },
        {
          text: '最近の早退',
          value: 'recent_early_leave',
          align: 'center',
          sortable: false
        }
      ],
      attendanceData: [],
      // 출석률
      value: 0,
      // 조회기간
      pagiNationInfo: [],
      // 조회단위, 상세
      period: 'weekly',
      date: null, // 쿼리를 실행할 때, 클릭 이벤트에 맞춰서 값을 넣어준다.
      nextDate: null,
      prevDate: null,
      dataChart: []
    }
  },
  mounted() {
    this.attendanceData = [];
    this.getData();
  },
  methods: {
    weeklyClick() {
      this.period = 'weekly';
      this.date = null;
      this.getData();
    },
    monthlyClick() {
      this.period = 'monthly';
      this.date = null;
      this.getData();
    },
    prevClick() {
      this.date = this.prevDate;
      this.getData();
    },
    nextClick() {
      this.date = this.nextDate;
      this.getData();
    },
    getData() {
      axios.get('/student/attendance', {
          params: {
            period: this.period,
            date: this.date
          }
        })
        .then((response) => {
          console.log(response.data);
          if (response.data.status === true) {
            // 서버와의 통신에 정상적으로 성공했을 경우
            // 출결 데이터 저장
            this.attendanceData = [];
            this.attendanceData.push(response.data.message);

            // 출석률 데이터 저장
            this.value = response.data.message.attendance_rate;
            // 페이지네이션 정보 저장
            this.pagiNationInfo = response.data.message.pagination;

            this.period = response.data.message.pagination.period;
            this.nextDate = response.data.message.pagination.next;
            this.prevDate = response.data.message.pagination.prev;

            // /* 막대 그래프 값 */
            this.dataChart = [response.data.message['sign_in'], response.data.message['lateness'], response.data.message['absence'], response.data.message['early_leave']];

          } else {
            // 조회된 기록이 없을 경우
            alert('情報がありません。')
          }
        }).catch((error) => {
          console.log('getDataErr :' + error);
        });
    }
  }
}
</script>
<style>

/* Header */
.categoryStdAttend {
  color: #FFFFFF;
  font-size: 38px;
  position: relative;
  font-family: "Montserrat";
  font-weight: Bold;
  position: relative;
  left: 40px;
  top: 20px;
}


.attendaceCard {
  border-radius: 0.6975rem;
  position: relative;
  bottom: 110px;
  min-height: 845px;
  box-shadow:  0px 4px 10px 0 rgba(33, 33, 33, 0.36);
}


.contents {
  text-align: center;
  background-color: rgb(255, 255, 255);
}

.attendanceCheckTitleEng {
  color: white;
  font-family: inherit;
}

.attendanceCheckTitleJap {
  color: rgb(0, 0, 0);
  font-size: 20px;
  font-family: MS Gothic;
}

.attendance_content {
  align-content: center;
  text-align: center;
}
</style>
