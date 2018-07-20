<template>
<div>
  <!-- 상단 이미지 -->
  <v-parallax src="/images/tutorManagement.jpg" height="300">
    <h1 class="categoryManage">Student Management</h1>
  </v-parallax>

  <!-- 내용들어갈 영역 -->
  <v-flex xs12>
    <v-container grid-list-xl>
      <v-layout row wrap align-center>
        <!-- 학생 정보 및 목록 -->
        <v-flex xs12 md12>

          <v-card class="studentListBox" height="580">
            <v-card-title>
              <!-- 학기 선택 하는 곳 -->
              <v-select :items="semester" v-model="e1" label="Select" single-line></v-select>
              <v-spacer></v-spacer>
              <v-text-field append-icon="search" label="Search" single-line hide-details v-model="search"></v-text-field>
            </v-card-title>
            <v-data-table :headers="headers" :items="student_lists" :search="search" :pagination.sync="pagination" hide-actions>
              <template slot="items" slot-scope="props">
                <td class="text-xs-center" style="height: 70px; font-size: 20px;font-family: Mplus 1p" >{{ props.item.id }}</td>
                 <td class="text-xs-center" style="font-size: 20px;font-family: Mplus 1p">{{ props.item.name }}</td>
                 <td class="text-xs-center">
                   <v-btn color = "blue accent-2" style="color:white;font-family: Mplus 1p" slot="activator" normal :onclick="props.item.infoLink">
                     詳しく見る
                   </v-btn>
                 </td>
              </template>
            </v-data-table>
            <div class="text-xs-center pt-2">
              <v-pagination circle v-model="pagination.page" :length="pages"></v-pagination>
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
.categoryManage {
  color: #FFFFFF;
  font-size: 50px;
  position: relative;
  font-family: "Montserrat";
  font-weight: Bold;
  position: relative;
  left: 39px;
}

.studentListBox {
  box-shadow:  0 4px 12px 0 rgba(163, 163, 163, 0.36);
  border-radius: 0.4075rem;
  position: relative;
  bottom: 150px;
}

</style>

<script>
export default {
  data() {
    return {
      search: '',
      pagination: {},
      selected: [],
      e1: null,
      semester: [{
          text: '準備中です。'
        }
      ],
      headers: [{
          class: 'display-1',
          text: '学生番号',
          value: 'studentNum',
          align: 'center',
        },
        {
          class: 'display-1',
          text: '名前',
          sortable: true,
          value: 'name',
          align: 'center'
        },
        {
          class: 'display-1',
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
          for (var start = 0; start < this.student_lists.length; start++) {
            this.$set(this.student_lists[start], 'infoLink', "window.open('/studentManagement/main?getInfoIdType=" + this.student_lists[start].id + "', 'newwindow', 'width=1000,height=700'); return false;");
          }
        }).catch((error) => {
          console.log(error);
        });
    }
  },
  computed: {
    pages() {
      if (this.pagination.rowsPerPage == null ||
        this.pagination.totalItems == null
      ) return 0

      return Math.ceil(this.pagination.totalItems / this.pagination.rowsPerPage)
    }
  }
}
</script>
