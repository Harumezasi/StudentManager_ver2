<template>
  <div class = "tutorStudentGrade fontSetting">

    <!-- 성적조회 간략히 영역 -->
    <v-flex xs12 v-if="profType">
      <v-container grid-list-xl>
        <v-layout row wrap align-center>

          <v-flex xs12 md12>
            <v-card class="elevation-1" color = "white">
              <v-card-text>
                <h2>成績照会 (簡単に)</h2>
              </v-card-text>

              <v-card-text>
                <v-container grid-list-xl>
                  <v-layout justify-space-between>
                    <!-- 학기 설정 -->
                    <!-- <v-flex xs3 md3>
                      <v-select :items="semester" v-model="e1" label="Select" single-line></v-select>
                    </v-flex> -->
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
                <h2>{{ subjectStstsName }}</h2>
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
                          <v-list-tile-content class="fontSetting">回数</v-list-tile-content>
                          <v-list-tile-content class="align-end fontSetting">{{ props.item.count }}</v-list-tile-content>
                        </v-list-tile>
                        <v-list-tile>
                          <v-list-tile-content class="fontSetting">取得可能点数</v-list-tile-content>
                          <v-list-tile-content class="align-end fontSetting">{{ props.item.perfect_score }}</v-list-tile-content>
                        </v-list-tile>
                        <v-list-tile>
                          <v-list-tile-content class="fontSetting">取得点数</v-list-tile-content>
                          <v-list-tile-content class="align-end fontSetting">{{ props.item.gained_score }}</v-list-tile-content>
                        </v-list-tile>
                        <v-list-tile>
                          <v-list-tile-content class="fontSetting">平均</v-list-tile-content>
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
                <h2>成績照会 (詳しく)</h2>
              </v-card-text>
              <v-card-text>
                <v-container grid-list-xl>
                  <v-layout justify-space-between>
                    <!-- 학기 설정 -->
                    <!-- <v-flex xs3 md3>
                      <v-select :items="semester" v-model="e1" label="Select" single-line></v-select>
                    </v-flex> -->
                    <!-- 과목 -->
                    <v-flex xs12 md12>
                      <v-btn
                        v-for = "examSort in examSortData"
                        :key  = "examSort.key"
                        depressed
                        color = "primary"
                        v-on:click="getSubjectScores(examSort.id, examSort.name, this.profType)"
                      >
                        {{ examSort.name }}
                      </v-btn>
                    </v-flex>
                  </v-layout>
                </v-container>
              </v-card-text>

              <v-card-text>
                <h2>{{ subjectScoreName }}</h2>
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
     profType : false,
     search: null,
     selected: [],
     e1: null,
     semester: [{
         text: '準備中です。'
       }
     ],
     /* 성적조회 (간략) */
     subjectStats     : [],
     subjectStstsName : '講義を選んでください。',
     /* 성적조회 (상세) */
     subjectScore     : [],
     subjectScoreName : '講義を選んでください。',
     headers: [
         { text: '日子', value: 'date' },
         { text: '分類', value: 'sort' },
         { text: '説明＆備考', value: 'detailData' },
         { text: '得点/満点', value: 'score' }
       ],
       /* 강의 메뉴 데이터 */
       examSortData: []
   }),
   methods : {
     /* 교과목, 지도교수를 확인하여 수강강의 조회의 url을 조작한다. */
     /* 지도교수인지 교과목 교수인지 판단. */
     checkTutor(){
       axios.post('/professor/is_tutor')
       .then((response) => {
         /* 지도교수 권한이 있는지에 대한 boolean값이 반환된다. */
         /* true 이면 지도교수의 메뉴를 활성화, false이면 비활성화한다. (학생 출결 및 담당과목 외 성적) */
         if(response.data){
          /* 지도교수 일 경우 */
          this.profType = true;
          this.getSubjectList();
          console.log('지도교수');
         }else{
          /* 교과목 교수 일 경우 */
          this.profType = false;
          this.getSubjectListProfessor();
         }
       })
       .catch((error) => {
         console.log("tutorCheck(manGrade) Error : " + error);
       })
     },
     /* 교과목 교수 - 수강 강의를 조회 */
     getSubjectListProfessor(){
       /* 학번을 통해 자신의 강의를 듣는지 조회 */
       axios.get('/professor/detail/join_list',{
         params : {
           std_id : this.$router.history.current.query.getInfoIdType,
           term : "2018-1st_term"
         }
       }).then((response)=>{
         /* 강의목록 저장 */
         this.examSortData = response.data;
         /* 강의가 존재하면 가장 앞에 있는 강의의 정보를 기본 값으로 불러온다. (간략, 상세) */
         if(this.examSortData[0].id != null){
           console.log(this.examSortData[0]);
           this.getSubjectScores(this.examSortData[0].id, this.examSortData[0].name, false);
         }
       }).catch((error)=>{
         console.log("getSubProf Error :" + error);
       })

     },
     /* 지도교수 - 수강 강의를 조회 */
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
           this.getSubjectScores(this.examSortData[0].id, this.examSortData[0].name, true);
         }
       }).catch((error)=>{
         console.log("getSub Error :" + error);
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
     getSubjectScores(subjectNum, subjectName, type){

       let urlData = '/professor/detail/score'

       if(type || this.profType){
         urlData = '/tutor/detail/subject_scores'
       }

       axios.get(urlData,{
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
     this.checkTutor();
   }
 }
</script>
