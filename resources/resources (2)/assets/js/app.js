/* import vue's */
import Vue from 'vue'
import Vuetify from 'vuetify'
import 'vuetify/dist/vuetify.min.css'
Vue.use(Vuetify)

import VueRouter from 'vue-router'
Vue.use(VueRouter)
window.axios = require('axios')

import VueAxios from 'vue-axios'
import axios from 'axios'
Vue.use(VueAxios, axios)

import moment from 'moment'
Vue.use(moment)

import VModal from 'vue-js-modal'
Vue.use(VModal)

import VueChart from 'vue-chart-js'
Vue.use(VueChart)

/* 중계 라우터 */
import App from './App.vue';

/* 로그인 */
import Login from './components/Login.vue'

/* 회원가입 */
import join from './components/join.vue'

/* 비밀번호 찾기 */
import help from './components/help.vue'

/* 메뉴 */
import serviceMenu from './components/serviceMenu.vue'

/* 관리자 */
import adminMain from './components/admin/adminMain.vue'

/* 학생 */
import studentAttendanceManagement    from './components/student/studentAttendanceManagement.vue'
import studentGradeManagement         from './components/student/studentGradeManagement.vue'
import studentUserInfo                from './components/student/studentUserInfo.vue'

/* 교과목 교수 */
import professorMainBody              from './components/professor/professorMainBody.vue'
import professorStudentManagement     from './components/professor/professorStudentManagement.vue'
import professorGradeList             from './components/professor/professorGradeList.vue'

/* 지도교수 */
import tutorRealTimeAttendance               from './components/tutor/tutorRealTimeAttendance.vue'
import tutorStudentManagement                from './components/tutor/tutorStudentManagement.vue'
import tutorAlertStudentSetting              from './components/tutor/tutorAlertStudentSetting.vue'
import tutorUserInfo                         from './components/tutor/tutorUserInfo.vue'
import tutorClassAnalyticPrediction          from './components/tutor/tutorClassAnalyticPrediction.vue'
import tutorStudentAnalyticPrediction        from './components/tutor/tutorStudentAnalyticPrediction.vue'
import tutorStudentAnalyticPredictionSetting from './components/tutor/tutorStudentAnalyticPredictionSetting.vue'


/* 학생 정보 페이지 */
import managementMenu       from './components/management/managementMenu.vue'
import managementMain       from './components/management/managementMain.vue'
import managementGrade      from './components/management/managementGrade.vue'
import managementComment    from './components/management/managementComment.vue'


/* 테스트 페이지 */
import Test from './components/test.vue'

/* 경로 설정 */
const routes = [
  /* 테스트 페이지 */
  {
    path : '/test',
    component : Test
  },
  /* 로그인 페이지 */
  {
    path: '/',
    component: Login
  },
  /* 회원가입 페이지 */
  {
    path: '/join',
    component:join
  },
  /* 비밀번호 찾기 페이지 */
  {
    path: '/help',
    component:help
  },
  /* 관리자 페이지 */
  {
    path: '/admin',
    component: adminMain
  },
  {
    path: '/admin/main',
    component: adminMain
  },
  /* 학생 페이지 */
  {
    path: '/student',
    component: serviceMenu,
    children: [
      {
        path: '/student',
        components : {
          body : studentAttendanceManagement
        }
      },
      {
        path: '/student/main',
        components : {
          body : studentAttendanceManagement
        }
      },
      {
        path: '/student/attendanceManagement',
        components : {
          body : studentAttendanceManagement
        }
      },
      {
        path: '/student/gradeManagement',
        components : {
          body : studentGradeManagement
        }
      },
      {
        path: '/student/userinfo',
        components : {
          body : studentUserInfo
        }
      },
    ]
  },
  /* 교과목 교수 페이지 */
  {
    path: '/professor',
    component: serviceMenu,
    children: [
      {
        path: '/professor',
        components : {
          body : professorMainBody
        }
      },
      {
        path: '/professor/main',
        components : {
          body : professorMainBody
        }
      },
      {
        path: '/professor/studentManagement',
        components : {
          body : professorStudentManagement
        }
      },
      {
        path: '/professor/userInfo',
        components : {
          body : tutorUserInfo
        }
      }
    ]
  },
  /* 등록된 성적 확인 (새창)*/
  {
    path: '/professor/gradeCheck',
    component: professorGradeList
  },
  /* 지도 교수 페이지 */
  {
    path: '/tutor',
    component: serviceMenu,
    children: [
      {
        path: '/tutor/attendance',
        components : {
          body : tutorRealTimeAttendance
        }
      },
      {
        path: '/tutor/alertStudentSetting',
        components : {
          body : tutorAlertStudentSetting
        }
      },
      {
        path: '/tutor/studentManagement',
        components : {
          body : tutorStudentManagement
        }
      },
      {
        path: '/tutor/userInfo',
        components : {
          body : tutorUserInfo
        }
      },
      {
        path: '/tutor/classAnalyticPrediction',
        components : {
          body : tutorClassAnalyticPrediction
        }
      },
      {
        path: '/tutor/studentAnalyticPrediction',
        components : {
          body : tutorStudentAnalyticPrediction
        }
      },
      {
        path: '/tutor/studentAnalyticPredictionSetting',
        components : {
          body : tutorStudentAnalyticPredictionSetting
        }
      }
    ]
  },
  /* 학생 정보 페이지 (새창) */
  {
    path: '/studentManagement',
    component: managementMenu,
    children: [
      {
        path: '/studentManagement/main',
        components : {
          body : managementMain
        }
      },
      {
        path: '/studentManagement/grade',
        components : {
          body : managementGrade
        }
      },
      {
        path: '/studentManagement/comment',
        components : {
          body : managementComment
        }
      }
    ]
  }
];

const router = new VueRouter({mode: 'history', routes : routes});
new Vue(Vue.util.extend({ router }, App)).$mount('#main_div');
