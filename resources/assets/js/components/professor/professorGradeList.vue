<template>
<v-app>

  <!-- 메뉴 영역 -->
  <!-- Left Menu -->
  <v-navigation-drawer class="black" fixed v-model="drawer" width="260" app dark>
    <v-list dense>
      <v-list>
        <router-link
        v-for="menuData in menuDatas"
        tag="v-list-tile"
        :key="menuData.key"
        :name="menuData.name"
        :to="subject_id_url"
        v-on:click.native="getGradeData(menuData.id), selectedGradeData = menuData.execute_date, drawer = false"
        >

          <v-list-tile-action>
            <v-icon>subject</v-icon>
          </v-list-tile-action>

          <v-list-tile-content>
            <v-list-tile-title>{{ menuData.execute_date }}</v-list-tile-title>
          </v-list-tile-content>

        </router-link>
      </v-list>
    </v-list>
  </v-navigation-drawer>

  <!-- Top Menu -->
  <v-toolbar color="transparent" flat fixed app>
    <v-toolbar-side-icon @click.stop="drawer = !drawer"></v-toolbar-side-icon>
    <v-spacer></v-spacer>
  </v-toolbar>

  <!-- 성적데이터가 나타날 영역-->
  <v-content style="padding-top: 0px;">
    <v-card class="gradeTable">
      <v-card-title>
        <h2>{{ selectedGradeData }}</h2>
        <v-spacer></v-spacer>
        <v-text-field v-model="search" append-icon="search" label="Search" single-line hide-details></v-text-field>
          <v-progress-linear :indeterminate="loadingValue"></v-progress-linear>
      </v-card-title>
      <v-data-table
      :headers="headers"
      :items="gradeDatas"
      :pagination.sync="pagination"
      :search="search"
      >
        <template slot="items" slot-scope="props">
          <td>{{ props.item.std_id }}</td>
          <td>{{ props.item.name }}</td>
          <td>
            <v-flex xs4 >
              <v-text-field
               id="testing"
               name="input-1"
               label="성적"
               v-model="props.item.score"
              ></v-text-field>
            </v-flex>
          </td>
          <td>
            {{ perfect_score }}
          </td>
          <td>
            <v-btn @click="updateGradeData(props.item.position)">성적 수정</v-btn>
          </td>
        </template>
        <v-alert slot="no-results" :value="true" color="error" icon="warning">
          Your search for "{{ search }}" found no results.
        </v-alert>
      </v-data-table>
    </v-card>
  </v-content>

</v-app>
</template>

<style>
/*-- 메뉴 부분 ( 출결관리, 학생 관리 등 )--*/

.menuBox {
  margin: 10px 0 0 0;
}

.gradeTable {
  margin: 70px 0 0 0;
}
</style>

<script>
export default {
  data: () => ({
    /*-- Left Menu --*/
    drawer: null,
    search: '',
    /* 페이지 */
    pagination: {
      rowsPerPage: 10
    },
    /* 성적정보 */
    headers: [{
        text: '학번',
        value: 'std_id',
        align: 'center'
      },
      {
        text: '이름',
        value: 'name',
        align: 'center'
      },
      {
        text: '성적',
        value: 'score',
        align: 'center'
      },
      {
        text: '만점',
        align: 'center'
      },
      {}
    ],
    gradeDatas: [],
    menuDatas : [],
    subject_id_url : null,
    perfect_score : null,

    selectedGradeData : '조회된 성적 데이터가 없습니다.',
    loadingValue : false,
  }),
  methods: {
    /* 성적 메뉴 데이터 */
    getGradeList() {
      axios.get('/professor/subject/score/list', {
        params: {
          subject_id: this.$router.history.current.query.subject_id
        }
      }).then((response) => {
        this.subject_id_url = "?subject_id=" + this.$router.history.current.query.subject_id;
        this.menuDatas = response.data.message;
        for(let start = 0; start < this.menuDatas.length; start++){
          /* 메뉴 타이틀 수정 */
          switch (this.menuDatas[start].type) {
            case 'quiz' :
              this.menuDatas[start].execute_date += " (퀴즈)";
              break;
            case 'homework' :
              this.menuDatas[start].execute_date += " (과제)";
              break;
            case 'midterm' :
              this.menuDatas[start].execute_date += " (중간고사)";
              break;
            case 'final' :
              this.menuDatas[start].execute_date += " (기말고사)";
              break;
          }
        }

        /* 데이터가 있는지 확인 */
        if(this.menuDatas[0].id != null){
          /* 최근 성적 기본 값으로 불러오기 */
          this.getGradeData(this.menuDatas[0].id);
          this.selectedGradeData = this.menuDatas[0].execute_date;
        }
      }).catch((error) => {
        console.log('getGradeList Error!!! : ' + error);
      })
    },
    /* 성적 데이터 */
    getGradeData(num) {
      /* 로딩바 활성화 */
      this.loadingValue = true;
      /* 통신 시작 */
      axios.get('/professor/subject/score/gained_scores', {
        params: {
          score_type : num
        }
      }).then((response) => {
        /* 초기화 단계 */
        this.gradeDatas = [];
        this.perfect_score = null;
        /* 값 저장 */
        this.gradeDatas = response.data.message.gained_scores;
        this.perfect_score = response.data.message.score_info.perfect_score;
        /* 값 변조 */
        for(let start = 0; start < this.gradeDatas.length; start++){
          /* 값 업데이트를 위한 위치 값 추가*/
          this.$set(this.gradeDatas[start], 'position', start);
        }
        /* 로딩바 비 활성화 */
        this.loadingValue = false;
      }).catch((error) => {
        console.log('getGradeList Error!!! : ' + error);
      })
    },
    /* 성적 수정 (개인) */
    updateGradeData(num){
      axios.post('/professor/subject/score/update',{
        score : this.gradeDatas[num].score,
        gained_score_id : this.gradeDatas[num].id
      }).then((response) => {
        alert(response.data.message);
      }).catch((error) => {
        console.log(error);
      })

    }
  },
  created() {
    this.getGradeList();
  }
}
</script>
