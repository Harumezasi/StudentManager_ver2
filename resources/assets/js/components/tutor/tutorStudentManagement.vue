<template>

    <div>
      <!-- 상단 이미지 -->
        <div class="panel-header">
          <div class="header text-center">
            <v-layout class = "imgTitle" column align-center justify-center>
              <h2 class="title">Student Management</h2>
              <p class="category">empty.</p>
            </v-layout>
          </div>
        </div>

        <v-layout column align-center>
          <v-flex xs12 sm4>
            <!-- 학기 선택 하는 곳 -->
            <v-select :items="semester" v-model="e1" label="Select" single-line></v-select>
          </v-flex>
        </v-layout>


              <!-- 내용들어갈 영역 -->
              <v-flex xs12>
                <v-container grid-list-xl>
                  <v-layout row wrap align-center>
                    <!-- 학생 정보 및 목록 -->
                    <v-flex xs12 md12>

                              <v-card class = "elevation-0">
                                <v-card-title>
                                  <v-spacer></v-spacer>
                                  <v-text-field
                                    append-icon="search"
                                    label="Search"
                                    single-line
                                    hide-details
                                    v-model="search"
                                  ></v-text-field>
                                </v-card-title>
                                <v-data-table
                                 id="fontSetting"
                                 :headers="headers"
                                 :items="student_lists"
                                 :search="search"
                                 :pagination.sync="pagination"
                                 >
                               <template slot="items" slot-scope="props">
                                 <td class="text-xs-center">{{ props.item.id }}</td>
                                 <td class="text-xs-center">{{ props.item.name }}</td>
                                 <td class="text-xs-center">{{ props.item.minimum_achievement}}</td>
                                 <td class="text-xs-center">
                                     <v-btn color="light-green" slot="activator" normal :onclick="props.item.infoLink">
                                       상세보기
                                     </v-btn>
                                 </td>
                               </template>
                              </v-data-table>
                               <div class="text-xs-center pt-2">
                                 <v-pagination v-model="pagination.page" :length="pages"></v-pagination>
                               </div>
                                <v-alert slot="no-results" :value="true" color="error" icon="warning">
                                  Your search for "{{ search }}" found no results.
                                </v-alert>
                              </v-card>

                      </v-flex>
                  </v-layout>
                </v-container>
              </v-flex>

    </div>
</template>

<style>
#fontSetting td {
  font-size: 30px;
  font-style: 'Gothic A1';
}

.fontSetting {
  font-size: 30px;
  font-style: 'Gothic A1';
}
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
</style>

<script>
export default {
   data () {
     return {
       search: '',
       pagination: {},
       selected: [],
       e1: null,
       pagination: {
         /* 테이블에 표시될 데이터 수, 기본 값*/
         rowsPerPage: 10
       },
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
       headers: [
         {
          class : 'display-1',
          text: '학번',
          sortable: true,
          value: 'studentNum',
          align: 'center'
         },
         {
           class : 'display-1',
           text: '이름',
           sortable: true,
           value: 'name',
           align: 'center'
         },
        {
          class : 'display-1',
          text: '',
          value: 'detailInfo',
          align: 'center'
        }
       ],
       student_lists: [],
     }
   },
   created() {
       this.getData();
   },
   methods: {
     getData() {
       axios.get('/tutor/class/student_list')
       .then((response) => {
        this.student_lists = response.data.message.students;
        /* 학생정보페이지 작업 / url 생성 및 연결 */
        for(var start = 0; start < this.student_lists.length; start++){
            this.$set(this.student_lists[start], 'infoLink', "window.open('/studentManagement/main?getInfoIdType="+ this.student_lists[start].id +"', 'newwindow', 'width=1000,height=700'); return false;");
        }
        }).catch((error) => {
         console.log(error);
        });
      }
   },
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
