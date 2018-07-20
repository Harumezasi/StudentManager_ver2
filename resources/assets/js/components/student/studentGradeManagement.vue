<template>
<div class="studnetGradeManagement">

  <!-- Header -->
  <v-parallax class="mainImage" src="/images/studentGrade2.jpg" height="300">
    <h1 class="categoryGrade">Grade Management</h1>
  </v-parallax>

  <div class = "studentPageTopDiv">
    <v-flex xs12>
      <v-container grid-list-xl>
        <v-layout row wrap align-center>

          <!-- 강의 목록 영역 -->
          <v-flex xs12 md5>
            <v-card class = "lectureListCard">
              <v-card-text style="position:relative; bottom:15px;">
                <v-flex xs12>
                  <v-layout row wrap align-center>
                    <v-flex xs12 md2>
                      <v-icon color = "light-green darken-2" large>format_list_bulleted</v-icon>
                    </v-flex>
                    <v-flex xs12 md4>
                      <h2 style="font-family:Mplus 1p;font-weight:bold">講義リスト</h2>
                    </v-flex>
                    <v-flex xs12 md6>
                      <!-- <v-select :items="semester" v-model="e1" label="Select" color = "light-green darken-2" single-line></v-select> -->
                    </v-flex>
                  </v-layout>
                </v-flex>
              </v-card-text>

              <!-- 강의 목록 list-->
              <v-list two-line>
                <div class = "lectureListScroll">

                  <v-subheader>
                    {{ this.dayData}}
                  </v-subheader>

                  <template v-for="datas in gradeData">

                    <v-divider></v-divider>

                    <v-list-tile
                      :key="datas"
                      avatar
                      @click="viewDataChange(datas.number)"
                      style="margin: 20px 0 20px 0"
                    >
                      <v-list-tile-content>
                        <v-list-tile-title v-html="datas.name"></v-list-tile-title>
                        <v-list-tile-sub-title v-html="datas.prof_name"></v-list-tile-sub-title>
                      </v-list-tile-content>
                    </v-list-tile>

                  </template>
                </div>
             </v-list>

            </v-card>
          </v-flex>



          <!-- 해당 강의 성적 정보 영역 -->
          <v-flex xs12 md7>
            <v-card class = "gradeCheckCard">

              <v-card-text>
                <v-flex xs12>
                  <v-layout row wrap align-center>
                    <v-flex xs12 md1>
                      <v-icon color = "light-green darken-2" large>bubble_chart</v-icon>
                    </v-flex>
                    <!-- 해당 강의명 -->
                    <v-flex xs12 md7>
                      <h2 style="font-family:Mplus 1p;font-weight:bold">{{ this.viewGradeData.name }}</h2>
                    </v-flex>
                  </v-layout>
                </v-flex>
              </v-card-text>

              <!-- 강의 정보 -->
              <v-flex xs12>
                <v-container>
                  <v-layout row wrap align-center>

                    <!-- 교수님 정보 -->
                    <v-flex xs12 md3>
                      <v-avatar class = "elevation-5" size = "120px">
                        <img :src="this.viewGradeData.photo">
                      </v-avatar>
                      <div class="headline" style="margin:25px 0 0 0">{{ this.viewGradeData.prof_name }}
                        <span v-if="this.viewGradeData.stats"　style="font-weight:lighter;font-size:18px;font-family:Mplus 1p">教授</span>
                      </div>
                    </v-flex>

                    <!-- 성적 간단 테이블 -->
                    <v-flex xs12 md9>
                      <table class="type03">
                        <thead>
                         <tr>
                           <th scope="cols">分類</th>
                           <th scope="cols">回数</th>
                           <th scope="cols">得点</th>
                           <th scope="cols">満点</th>
                           <th scope="cols">平均</th>
                         </tr>
                        </thead>
                        <tbody v-if="this.viewGradeData.stats">
                          <tr>
                              <th scope="row">中間</th>
                              <td>{{ this.viewGradeData.stats.midterm.count }}</td>
                              <td>{{ this.viewGradeData.stats.midterm.gained_score }}</td>
                              <td>{{ this.viewGradeData.stats.midterm.perfect_score }}</td>
                              <td>{{ this.viewGradeData.stats.midterm.average }}</td>
                          </tr>
                          <tr>
                              <th scope="row">期末</th>
                              <td>{{ this.viewGradeData.stats.final.count }}</td>
                              <td>{{ this.viewGradeData.stats.final.gained_score }}</td>
                              <td>{{ this.viewGradeData.stats.final.perfect_score }}</td>
                              <td>{{ this.viewGradeData.stats.final.average }}</td>
                          </tr>
                          <tr>
                              <th scope="row">テスト</th>
                              <td>{{ this.viewGradeData.stats.quiz.count }}</td>
                              <td>{{ this.viewGradeData.stats.quiz.gained_score }}</td>
                              <td>{{ this.viewGradeData.stats.quiz.perfect_score }}</td>
                              <td>{{ this.viewGradeData.stats.quiz.average }}</td>
                          </tr>
                          <tr>
                              <th scope="row">課題</th>
                              <td>{{ this.viewGradeData.stats.homework.count }}</td>
                              <td>{{ this.viewGradeData.stats.homework.gained_score }}</td>
                              <td>{{ this.viewGradeData.stats.homework.perfect_score }}</td>
                              <td>{{ this.viewGradeData.stats.homework.average }}</td>
                          </tr>
                        </tbody>
                        <tbody v-else>
                          <tr>
                            <td rowspan="4" colspan="4" style="height:270px">
                              <br><br>
                              <h2>講義を選択してください。</h2>
                            </td>
                          </tr>
                        </tbody>
                      </table>
                    </v-flex>
                  </v-layout>
                </v-container>
              </v-flex>

              <!-- 상세 성적 조회 -->
              <v-flex xs12>
                <v-container>
                  <v-layout row wrap align-center>
                    <v-card-text>
                      <div
                      style="font-size:23px;
                             border-bottom: 2px solid;
                             border-color: rgb(80, 154, 56);"
                      >詳しし成績</div>
                    </v-card-text>
                    <v-data-table
                      :headers="detailTableHeaders"
                      :items="viewGradeData.scores"
                      :pagination.sync="pagination"
                      hide-actions
                      class="elevation-0"
                      style="position: relative;left:15px;"
                    >
                      <template slot="headerCell" slot-scope="props">
                        <v-tooltip bottom>
                          <span slot="activator">
                            {{ props.header.text }}
                          </span>
                          <span>
                            {{ props.header.text }}
                          </span>
                        </v-tooltip>
                      </template>
                      <template slot="items" slot-scope="props">
                        <td class="text-xs-center">{{ props.item.execute_date }}</td>
                        <td class="text-xs-center">{{ props.item.detail }}</td>
                        <td class="text-xs-center">{{ props.item.gained_score }}</td>
                        <td class="text-xs-center">{{ props.item.perfect_score }}</td>
                        <td class="text-xs-center">{{ props.item.type }}</td>
                      </template>
                    </v-data-table>
                    <div class="text-xs-center pt-3" style="margin:auto; padding:auto">
                      <v-pagination v-model="pagination.page" :length="pages" color="light-green darken-2" circle></v-pagination>
                    </div>
                  </v-layout>
                </v-container>
              </v-flex>
            </v-card>
          </v-flex>

        </v-layout>
      </v-container>
    </v-flex>
  </div>


</div>
</template>

<script>
export default {
  data() {
    return {
      gradeData : null,

      viewGradeData : [{
        name : '',
        prof_name : '',
        photo : "/storage/source/prof_face/default.png",
      }],

      dayData : null,
      pagination: {},
      e1: null,
      /* 학기 선택 */
      semester: [{
          text: '2016년 1학기'
        },
        {
          text: '2016년 2학기'
        }
      ],
       /* 상세 성적 정보 */
       detailTableHeaders: [
          { text: '日時', value: 'date' },
          { text: '試験タイプ', value: 'detailType' },
          { text: '得点', value: 'myScore' },
          { text: '満点', value: 'totalScore' },
          { text: '分類', value: 'testType' }
        ]
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
        // this.days.this = response.data.message.pagination.this;
        this.dayData = response.data.message.pagination['this']
        /* 과목 */
        this.gradeData = response.data.message.subjects;

        let labels = Object.keys(this.gradeData);

        for(let count in this.gradeData){
          this.$set(this.gradeData[count], 'number', labels.shift());
        }

        this.viewDataChange(0)
      }).catch((error) => {
        alert('講義の情報がありません。')
      })
    },
    viewDataChange(num){
      this.viewGradeData = this.gradeData[num];
    }
  },
  /* 페이지네이션 */
  computed: {
     pages () {
       if (this.pagination.rowsPerPage == null ||
         this.pagination.totalItems == null
       ) return 0

       return Math.ceil(this.pagination.totalItems / this.pagination.rowsPerPage)
     }
   }
}
</script>

<style>

/* Header */
.categoryGrade {
  color: #FFFFFF;
  font-size: 38px;
  position: relative;
  font-family: "Montserrat";
  font-weight: Bold;
  position: relative;
  left: 48px;
  bottom: 50px;
}

/* scroll 관련 */
.lectureListScroll {
  overflow-y: scroll;
  height: 630px;
}

/* 강의 */
.lectureListCard {
  border-radius: 0.6975rem;
  position: relative;
  bottom: 200px;
  min-height: 845px;
  box-shadow:  0px 4px 10px 0 rgba(33, 33, 33, 0.36);
}

/* 해당 강의 성적 확인 */
.gradeCheckCard {
  border-radius: 0.6975rem;
  position: relative;
  bottom: 200px;
  min-height: 680px;
  box-shadow:  0px 4px 10px 0 rgba(33, 33, 33, 0.36);
}

/*-- 테이블 --*/
table.type03 {
    border-collapse: collapse;
    text-align: center;
    line-height: 1.5;
    border-top: 1px solid #ffffff;
    border-left: 3px solid #3ba204;
    margin : 20px 10px;
}
table.type03 th {
    width: 147px;
    padding: 9px;
    font-weight: bold;
    vertical-align: top;
    color: #525252;
    border-right: 1px solid #ffffff;
    border-bottom: 1px solid #c7c7c7;

}
table.type03 td {
    width: 349px;
    padding: 10px;
    color: #4e872b;
    font-style: italic;
    vertical-align: top;
    border-right: 1px solid #ffffff;
    border-bottom: 1px solid #ccc;
}

/* 스크롤 */
/* width */
::-webkit-scrollbar {
    width: 9px;
}

/* Track */
::-webkit-scrollbar-track {
    background-color: rgb(208, 208, 208);
    border-radius: 10px;
}

/* Handle */
::-webkit-scrollbar-thumb {
    background: rgb(195, 195, 195);
    border-radius: 10px;
}

/* Handle on hover */
::-webkit-scrollbar-thumb:hover {
    background: rgb(190, 190, 190);
}

</style>
