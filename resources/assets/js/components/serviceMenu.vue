<template>
  <v-app id="inspire">

  <!-- class를 동적으로 설정할 수 있도록 구현 -->
  <!--- :class는 vuejs 객체와 연동되어 동적으로 변경, 변경될 필요가 없는 것은 따로 선언 -->
  <v-navigation-drawer :class="menuClassType" fixed v-model="drawer" app dark width="280">
    <v-list dense>
      <!-- LOGO IMAGE -->
      <v-list-tile-content>
         <router-link :to="link"><img class="logo-box" src="/images/logo.png" /></router-link>
      </v-list-tile-content>

      <!-- 현재 접속 된 유저 정보 -->
      <v-list>
        <!-- 유저 이미지 -->
        <v-list-tile avatar>
          <v-list-tile-avatar class = "userBox" size="70px">
            <img :src="userInfoData.photo" style="border:3px solid white;" />
          </v-list-tile-avatar>
        </v-list-tile>
        <!-- 유저 이름 -->
        <div class="userName text-xs-center">
          <span>
            {{ userInfoData.name }} 님<br>
          </span>
        </div>
        <!-- 유저 정보 버튼 -->
        <div class = "userButton">
          <v-btn flat icon color="white" small :to="infoLink">
            <v-icon class = "userIcon" small>
              info_outline
            </v-icon>
          </v-btn>
          <!-- 로그아웃 버튼 -->
          <a href="/logout">
            <v-btn flat icon color="white" small>
              <v-icon class = "userIcon" small>
                power_settings_new
              </v-icon>
            </v-btn>
          </a>
        </div>
      </v-list>

      <!-- 메뉴 -->
      <v-list class="menuBox">
        <div
          v-for="MenuData in MenuDatas"
          :key="MenuData.title"
          >
        <!-- 접고 펴야하는지를 판단 = true 리스트일 경우 -->
        <div v-if="MenuData.listSet == true">
         <!-- 메뉴 리스트를 만듬 -->
          <!-- 리스트 아이콘-->
          <v-list-group
            :prepend-icon="MenuData.action"
            v-model="MenuData.active"
            style="margin: 15px 0 15px 0;"
          >
            <!-- 리스트 타이틀 -->
            <v-list-tile slot="activator">
              <v-list-tile-content>
                <v-list-tile-title>{{ MenuData.title }}</v-list-tile-title>
              </v-list-tile-content>
            </v-list-tile>
            <!-- 실제 클릭하여 작동하는 메뉴 -->
            <router-link
            tag="v-list-tile"
            v-for = "subMunuData in MenuData.subMenu"
            :key="subMunuData.title"
            :to="subMunuData.path"
            >
              <!-- 메뉴 아이콘 -->
               <v-list-tile-action>
                <v-icon>{{ subMunuData.action }}</v-icon>
              </v-list-tile-action>
              <!-- 메뉴 타이틀 -->
              <v-list-tile-content>
                <v-list-tile-title>{{ subMunuData.title }}</v-list-tile-title>
              </v-list-tile-content>
            </router-link>
          </v-list-group>
        </div>
        <!-- 접고 펴야하는지를 판단 = false 리스트가 아닐 경우 -->
        <div v-else-if="MenuData.listSet == false">
          <router-link
          :to="MenuData.path"
          tag="v-list-tile"
          >

             <v-list-tile-action>
              <v-icon>{{ MenuData.action }}</v-icon>
            </v-list-tile-action>

            <v-list-tile-content>
              <v-list-tile-title>{{ MenuData.title }}</v-list-tile-title>
            </v-list-tile-content>

          </router-link>
        </div>
        <!-- 에러일 경우 -->
        <div v-else>
          ListSet 타입이 존재하지 않습니다.
        </div>
      </div>
      </v-list>
    </v-list>
  </v-navigation-drawer>

  <!-- Toolbar - 유저 버튼, 메뉴 여닫기 버튼, 알림 버튼  -->
  <v-toolbar color="transparent" flat fixed app>
    <v-toolbar-side-icon @click.stop="drawer = !drawer"></v-toolbar-side-icon>
    <v-spacer></v-spacer>
      <v-btn icon>
        <v-icon color="white">notifications_none</v-icon>
      </v-btn>
      <v-btn icon color="white">
        <v-icon>person_outline</v-icon>
      </v-btn>
  </v-toolbar>

  <!-- 각 페이지 별, Contents -->
  <v-content style="padding-top:0px;">
    <router-view name = "body"></router-view>
  </v-content>

  </v-app>
</template>

<style>

/*-- 로고 이미지 --*/
.logo-box {
  width: 130px;
  height: auto;
  margin: 35px 0 40px 65px;
}
/*-- 메뉴 - 유저 박스 --*/
.userBox {
  margin: auto;
  padding: auto;
}
/*-- 메뉴 - 유저 이름 --*/
.userName {
  margin: 30px 0 0 0;
}
/*-- userInfo, Logout 버튼 --*/
.userButton {
  margin: 0 0 0 92px;
}

/*-- 메뉴 부분 ( 출결관리, 학생 관리 등 )--*/
.menuBox {
  margin: 30px 0 0 0;
}
</style>

<script>
export default {
  data: () => ({
    /* 메인 로고 링크 */
    link : null,
    /* 회원정보 버튼 링크 */
    infoLink : null,
    /*-- Left Menu --*/
    drawer: null,
    /* 메뉴 색상 조정 */
    menuClassType : null ,
    menuClassTypeList : [{
      student : 'light-green',
      professor : 'deep-purple accent-3',
      tutor : 'light-blue darken-4 elevation-10'
    }],

    /* 출력될 메뉴 내용 조정 */
    /* listSet = 리스트인지 아닌지 표기, 필수 값*/
    MenuDatas : null,
    MenuDataList : [{
      /* 학생 */
      student : [
              {
                action: 'face',
                title: '출결관리',
                path: '/student/attendanceManagement',
                listSet : false
              },
              {
                action: 'settings',
                title: '학업관리',
                path: '/student/gradeManagement',
                listSet : false
              }
            ],
      /* 교과목 교수 */
      /* 교과목 교수는 강의 데이터를 함수로부터 받아와야한다. */
      professor : [{
            action: 'subject',
            title: '강의 관리',
            active: true,
            listSet : true,
            subMenu: []
      }],
      /* 지도 교수 */
      tutor : [{
            action: 'check',
            title: '출결 관리',
            active: false,
            listSet : true,
            subMenu: [
              {
                action: 'check',
                title: '등/하교 출결',
                path: '/tutor/attendance'
              },
              {
                action: 'alarm',
                title: '알림 설정',
                path: '/tutor/alertStudentSetting'
              }
            ]
          },
          {
            listSet : false,
            action: 'face',
            title: '지도 학생 정보',
            path: '/tutor/studentManagement'
          },
          {
            action: 'bar_chart',
            title: '학생 분석 예측',
            active: false,
            listSet : true,
            subMenu: [
              {
                action: 'person',
                title: '개인별 분석 예측',
                path: '/tutor/studentAnalyticPrediction'
              },
              {
                action: 'group',
                title: '지도반 분석 예측',
                path: '/tutor/classAnalyticPrediction'
              },
              {
                action: 'settings',
                title: '설정',
                path: '/tutor/studentAnalyticPredictionSetting'
              }
            ]
          }
      ]
    }],
    /* 사용자 정보 */
    userInfoData : [],
    paramsData : null
  }),
  methods : {
      getUserInfo() {
        axios.get('/info')
        .then((response) => {
          /* 유저 정보를 저장 (이름, 이미지) */
          this.userInfoData = response.data.message;

          /* 학생, 교수 판단*/
          switch (response.data.message.type) {
            case 'student' :
              /* 학생일 경우, 바로 메뉴 타입을 설정한다.*/
              this.MenuDatas = this.MenuDataList[0].student;
              this.menuClassType = this.menuClassTypeList[0].student;
              /* 메인 로고 링크 설정 */
              this.link = "/student/main";
              /* 회원정보 링크 설정 */
              this.infoLink = "/student/userInfo";
              break;
            case 'professor' :
              /* 교수권한을 확인 후, 해당 함수에서 메뉴타입을 설정한다. 함수는 바로 아래에 존재 */
              this.checkTutor();
              /* 메인 로고 링크 설정 */
              this.link = "/professor/main";
              break;
          }
        })
        .catch((error) => {
          console.log('getInfo Error : ' + error);
        })
      },
      checkTutor(){
        console.log(this.$router);
        axios.post('/professor/is_tutor')
        .then((response) => {
          /* 지도교수 권한이 있는지에 대한 boolean값이 반환된다. */
          /* true 이면 지도교수의 메뉴를 활성화, false이면 비활성화한다. */
          if(response.data){
            /* 메뉴 정보 */
            this.MenuDatas = this.MenuDataList[0].professor;
            /* 지도교수 메뉴를 추가 */
            for(let start = 0; start < this.MenuDataList[0].tutor.length; start++){
              this.MenuDatas.push(this.MenuDataList[0].tutor[start]);
            }
            /* 메뉴 색상 */
            this.menuClassType = this.menuClassTypeList[0].tutor;
            /* 회원정보 링크 설정 */
            this.infoLink = "/tutor/userInfo";
          }else{
            /* 교과목 교수는 강의 리스트를 받아와서 제작한다. */
            /* 메뉴 정보 */
            this.MenuDatas = this.MenuDataList[0].professor;
            /* 메뉴 색상 */
            this.menuClassType = this.menuClassTypeList[0].professor;
            /* 회원정보 링크 설정 */
            this.infoLink = "/professor/userInfo";
          }
        })
        .catch((error) => {
          console.log("tutorCheck Error : " + error);
        })
      },
      getSubjectList(){
        axios.get('/professor/subject/list')
        .then((response) => {
          let subjects = response.data.message.subjects;
          /* 강의 메뉴 생성 */
          for(let start = 0; start < subjects.length; start++){
            this.MenuDataList[0].professor[0].subMenu
            .push({
              title: subjects[start].name,
              path: '/professor/studentManagement?subjectName=' + subjects[start].id
            })
          }
        })
        .catch((error) => { console.log('subError : ' + error);})
      }
  },
  created(){
    this.getUserInfo();
    this.getSubjectList();
  }
}
</script>
