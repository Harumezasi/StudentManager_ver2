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

      <!-- 내용들어갈 영역 -->
      <v-flex xs12>
        <v-container grid-list-xl>
          <v-layout row wrap align-center>

            <!-- 출석 관련 표로 보기 영역 -->
                      <!-- 기간 -->
                      <v-flex xs12>
                        <div class="termArea">
                          {{ days.this }}
                        </div>
                      </v-flex>
                      <!-- 성적 -->
                      <v-flex xs12>
                        <!-- 반복문 -->
                        <div v-for="datas in gradeData" :key="datas.key" class="studentInfoArea">
                          <table class="gradeDataTable">
                            <tr>
                              <!-- 이미지 들어갈 자리-->
                              <td rowspan="5"><img :src="datas.photo"></img></td>
                              <!-- 성적테이블 메뉴 -->
                              <td v-for="types in gradeType">{{types}}</td>
                            </tr>
                            <!-- 분류별 종합 성적 / 퀴즈, 과제, 중간, 기말 순 -->
                            <tr>
                              <td v-for="quiz in datas.stats.quiz">{{quiz}}</td>
                            </tr>
                            <tr>
                              <td v-for="homework in datas.stats.homework">{{homework}}</td>
                            </tr>
                            <tr>
                              <td v-for="midterm in datas.stats.midterm">{{midterm}}</td>
                            </tr>
                            <tr>
                              <td v-for="final in datas.stats.final">{{final}}</td>
                            </tr>
                            <tr>
                            <!-- 강의명 -->
                              <td> {{ datas.title }} </td>
                            <!-- 학업성취도 -->
                              <td colspan="6"> 학업성취도 : {{ datas.achievement }} %</td>
                            </tr>
                          </table>
                          <!-- 상세보기 / 하단의 div와 pageOpen제어 click 이벤트 연결 -->
                          <button v-on:click="datas.pageOpen = !datas.pageOpen">상세보기</button>
                          <!-- 상세보기 영역 div / pageOpen으로 제어-->
                          <div v-if="datas.pageOpen == true">
                            <table class="gradeDataTablePlus">
                              <tr>
                                <!-- 상세보기 테이블 메뉴 -->
                                <td v-for="type in plusType">{{type}}</td>
                              </tr>
                              <!-- 상세데이터 출력 -->
                              <tr v-for="gainedData in datas.scores">
                                <td v-for="datas in gainedData">{{ datas }}</td>
                              </tr>
                            </table>
                          </div>
                        </div>
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
  /**/

  .studentInfoArea{
    display: inline-block;
    margin : 10px;
  }
  .studentInfoArea button {
    width : 100px;
    height: 50px;
    background-color: cyan;
    border: 1px solid black;
    margin : 10px;
    font-size: 15px;
    font-weight: bold;
  }
  .gradeDataTable {
      width : 600px;
      height: 300px;
  }
  .gradeDataTablePlus {
      width : 600px;
      height: 200px;
  }
  table {
    text-align: center;
    border: 1px solid black;
    border-radius: 10px;
    font-size: 15px;
    font-weight: bold;
  }
  td {
    border: 1px solid black;
  }
</style>

<script>
export default {
  data() {
    return {
      /* 페이지네이션 정보*/
      days : {
        this : null,
        prevDate : null,
        nextDate : null
      },
      /* 성적테이블 상단 메뉴 */
      gradeType: {
        t1 : "",
        t2 : "횟수",
        t3 : "만점",
        t4 : "득점",
        t5 : "평균",
        t6 : "반영비율"
      },
      /* 상세보기 테이블 메뉴 */
      plusType: {
        t1 : "날짜",
        t2 : "타입",
        t3 : "상세",
        t4 : "득점",
        t5 : "만점",
      },
      /* 테이블 선 나오게 함 */
      bordered: true,
      /* 성적데이터 */
      gradeData: []
    }
  },
  mounted(){
    this.getData();
  },
  methods: {
    getData(){
      axios.get('/student/subject')
      .then((response)=>{
        /* 년도, 학기 */
        this.days.this = response.data.message.pagination.this;
        /* 과목 */
        this.gradeData = response.data.message.subjects;
        /* 각 과목에 상세보기 펴고접기위한 제어 변수 boolean을 추가 */
        /* pageOpen 이라는 변수로 상세보기에 해당하는 div 제어 */
        for(var start = 0; start < this.gradeData.length; start++){
            this.$set(this.gradeData[start], 'pageOpen', false);
        }
      })
    }
  }
}
</script>
