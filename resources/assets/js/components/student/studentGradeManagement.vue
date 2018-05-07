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
                          {{ days.year }}년도 {{ days.term }}
                        </div>
                      </v-flex>
                      <!-- 성적 -->
                      <v-flex xs12>
                        <!-- 반복문 -->
                        <div v-for="datas in gradeData" :key="datas.key" class="studentInfoArea">
                          <table class="gradeDataTable">
                            <tr>
                              <!-- 이미지 들어갈 자리-->
                              <td rowspan="5"><img :src="datas.prof_info.face_photo"></img></td>
                              <td v-for="types in gradeType">{{types}}</td>
                            </tr>
                            <tr>
                              <td v-for="scores1 in datas.score[1]">{{scores1}}</td>
                            </tr>
                            <tr>
                              <td v-for="scores1 in datas.score[2]">{{scores1}}</td>
                            </tr>
                            <tr>
                              <td v-for="scores1 in datas.score[3]">{{scores1}}</td>
                            </tr>
                            <tr>
                              <td v-for="scores1 in datas.score[4]">{{scores1}}</td>
                            </tr>
                            <tr>
                            <!-- 강의명 -->
                              <td> {{ datas.title }} </td>
                              <td colspan="6"> 학업성취도 : {{ datas.achievement }} %</td>
                            </tr>
                          </table>
                          <!-- 상세보기 -->
                          <button>상세보기</button>
                          <div>
                            <table class="gradeDataTablePlus">
                              <tr>
                                <td v-for="type in plusType">{{type}}</td>
                              </tr>
                              <tr v-for="gainedData in datas.gained_score">
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
/*-- 학기 ㄷㅔ이터 --*/
export default {
  data() {
    return {
      e1: null,
      days : {
        year : null,
        term : null
      },
      fields: {
        type :  'test '
      },
      gradeType: {
        t1 : "",
        t2 : "횟수",
        t3 : "만점",
        t4 : "득점",
        t5 : "평균",
        t6 : "반영비율"
      },
      plusType: {
        t1 : "날짜",
        t2 : "타입",
        t3 : "상세",
        t4 : "득점",
        t5 : "만점",
      },
      gradeData: null,
      /*-- 테이블 선 나오게 함 --*/
      bordered: true,
    }
  },
  mounted(){
    this.getData();
  },
  methods: {
    getData(){
      axios.get('/student/getData/gradeManagement')
      .then((response)=>{
        /* 년도, 학기 */
        this.days.year = response.data.year;
        this.days.term = response.data.term;
        this.gradeData = response.data.lecture_list;
        console.log(this.gradeData);
      })
    }
  }
}
</script>
