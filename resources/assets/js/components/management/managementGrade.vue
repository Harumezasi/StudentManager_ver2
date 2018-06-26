<template>
  <div class = "tutorStudentGrade fontSetting">

    <!-- 성적조회 간략히 영역 -->
    <v-flex xs12>
      <v-container grid-list-xl>
        <v-layout row wrap align-center>

          <v-flex xs12 md12>
            <v-card class="elevation-1" color = "white">
              <v-card-text>
                <h2 class = "cardInsideTitle">성적조회 (간략히 보기)</h2>
              </v-card-text>

              <v-card-text>
                <v-container grid-list-xl>
                  <v-layout justify-space-between>
                    <!-- 학기 설정 -->
                    <v-flex xs3 md3>
                      <v-select :items="semester" v-model="e1" label="Select" single-line></v-select>
                    </v-flex>
                    <!-- 과목 -->
                    <v-flex xs12 md12>
                      <v-btn
                        v-for = "examSort in examSortData"
                        :key  = "examSort.key"
                        depressed
                        color = "primary"
                        v-on:click="getSubjectStats(examSort.id, examSort.name)"
                      >
                        {{ examSort.name }}
                      </v-btn>
                    </v-flex>
                  </v-layout>
                </v-container>
              </v-card-text>

              <v-card-text>
                <h2 class = "cardInsideTitle">{{ subjectStstsName }}</h2>
              </v-card-text>

              <v-container fluid grid-list-md>
                <v-data-iterator
                  :items="subjectStats"
                  content-tag="v-layout"
                  hide-actions
                >
                  <v-flex slot="item" slot-scope="props">
                    <v-card>
                      <v-card-title><h3>{{ props.item.type }}</h3></v-card-title>
                      <v-divider></v-divider>
                      <v-list dense>
                        <v-list-tile>
                          <v-list-tile-content class="fontSetting">횟수</v-list-tile-content>
                          <v-list-tile-content class="align-end fontSetting">{{ props.item.count }}</v-list-tile-content>
                        </v-list-tile>
                        <v-list-tile>
                          <v-list-tile-content class="fontSetting">총 점수</v-list-tile-content>
                          <v-list-tile-content class="align-end fontSetting">{{ props.item.perfect_score }}</v-list-tile-content>
                        </v-list-tile>
                        <v-list-tile>
                          <v-list-tile-content class="fontSetting">취득 점수</v-list-tile-content>
                          <v-list-tile-content class="align-end fontSetting">{{ props.item.gained_score }}</v-list-tile-content>
                        </v-list-tile>
                        <v-list-tile>
                          <v-list-tile-content class="fontSetting">평균</v-list-tile-content>
                          <v-list-tile-content class="align-end fontSetting">{{ props.item.average }}</v-list-tile-content>
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

    <!-- 성적조회 상세보기 영역 -->
    <v-flex xs12>
      <v-container grid-list-xl>
        <v-layout row wrap align-center>

          <v-flex xs12 md12>
            <v-card class="elevation-1" color = "white">
              <v-card-text>
                <h2 class = "cardInsideTitle">성적조회 (상세보기)</h2>
              </v-card-text>
              <v-card-text>
                <v-container grid-list-xl>
                  <v-layout justify-space-between>
                    <!-- 학기 설정 -->
                    <v-flex xs3 md3>
                      <v-select :items="semester" v-model="e1" label="Select" single-line></v-select>
                    </v-flex>
                    <!-- 과목 -->
                    <v-flex xs12 md12>
                      <v-btn
                        v-for = "examSort in examSortData"
                        :key  = "examSort.key"
                        depressed
                        color = "primary"
                        v-on:click="getSubjectScores(examSort.id, examSort.name)"
                      >
                        {{ examSort.name }}
                      </v-btn>
                    </v-flex>
                  </v-layout>
                </v-container>
              </v-card-text>

              <v-card-text>
                <h2 class = "cardInsideTitle">{{ subjectScoreName }}</h2>
              </v-card-text>

              <!-- 테이블 -->
              <v-data-table
                 :headers="headers"
                 :items="subjectScore"
                 :search="search"
                 page
                 id="fontSetting"
               >
                 <template slot="items" slot-scope="props">
                   <td>{{ props.item.execute_date }}</td>
                   <td>{{ props.item.type }}</td>
                   <td>{{ props.item.detail }}</td>
                   <td>{{ props.item.gained_score }}/{{ props.item.perfect_score }}</td>
                 </template>
              </v-data-table>
             </v-card-title>
            </v-card>
          </v-flex>

        </v-layout>
      </v-container>
    </v-flex>

  </div>
</template>

<style>
.fontSetting {
  font-size: 18px;
  font-weight: lighter;
  font-style: 'Gothic A1';
}

#fontSetting td {
  font-size: 18px;
  font-weight: lighter;
  font-style: 'Gothic A1';
}

</style>

<script>
export default {
   data: () => ({
     search: null,
     selected: [],
     e1: null,
     semester: [{
         text: '2016년 1학기'
       },
       {
         text: '2016년 2학기'
       },
       {
         text: '2017년 1학기'
       },
       {
         text: '2017년 2학기'
       },
       {
         text: '2018년 1학기'
       },
     ],
     /* 성적조회 (간략) */
     subjectStats     : [],
     subjectStstsName : '과목을 선택해주세요.',
     /* 성적조회 (상세) */
     subjectScore     : [],
     subjectScoreName : '과목을 선택해주세요.',
     headers: [
         { text: '날짜', value: 'date' },
         { text: '분류', value: 'sort' },
         { text: '상세', value: 'detailData' },
         { text: '득점/점수', value: 'score' }
       ],
       /* 강의 메뉴 데이터 */
       examSortData: []
   }),
   methods : {
     /* 교과목, 지도교수를 확인하여 수강강의 조회의 url을 조작한다. */
     /* 수강 강의를 조회 */
     getSubjectList(){
       axios.get('/tutor/detail/join_list',{
         params : {
           std_id : this.$router.history.current.query.getInfoIdType
         }
       }).then((response)=>{
         /* 강의목록 저장 */
         this.examSortData = response.data.message.subjects;
         /* 강의가 존재하면 가장 앞에 있는 강의의 정보를 기본 값으로 불러온다. (간략, 상세) */
         if(this.examSortData[0].id != null){
           this.getSubjectStats(this.examSortData[0].id, this.examSortData[0].name);
           this.getSubjectScores(this.examSortData[0].id, this.examSortData[0].name);
         }
       }).catch((error)=>{
         console.log("getSubError :" + error);
       })
     },
     /* 강의의 성적을 조회 (간략 데이터) */
     getSubjectStats(subjectNum, subjectName){
       axios.get('/tutor/detail/subject_stat',{
         params : {
           std_id     : this.$router.history.current.query.getInfoIdType,
           subject_id : subjectNum
         }
       }).then((response)=>{
         /* 변수 초기화 */
         this.subjectStats = [];
         /* 받아온 데이터를 저장 */
         this.subjectStats.push(response.data.message.stats.quiz);
         this.subjectStats.push(response.data.message.stats.homework);
         this.subjectStats.push(response.data.message.stats.midterm);
         this.subjectStats.push(response.data.message.stats.final);
         /* 과목명을 변경 */
         this.subjectStstsName = subjectName;
       }).catch((error)=>{
         console.log('getStatsError :' + error);
       })
     },
     /* 강의의 성적을 조회 (상세 데이터) */
     getSubjectScores(subjectNum, subjectName){
       axios.get('/tutor/detail/subject_scores',{
         params : {
           std_id     : this.$router.history.current.query.getInfoIdType,
           subject_id : subjectNum
         }
       }).then((response)=>{
         console.log(response.data);
         /* 데이터 저장 */
         this.subjectScore = response.data.message;
         /* 과목명을 변경 */
         this.subjectScoreName = subjectName;
       }).catch((error)=>{
         console.log('getScoreError :' + error);
       })
     }
   },
   created(){
     this.getSubjectList()
   }
 }
</script>
