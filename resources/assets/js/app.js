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

/* 학생 */
import studentMenu from './components/student/studentMenu.vue'
import studentMainBody from './components/student/studentMainBody.vue'
import studentAttendanceManagement from './components/student/studentAttendanceManagement.vue'
import studentGradeManagement from './components/student/studentGradeManagement.vue'

/* 교과목 교수 */
import professorMenu from './components/professor/professorMenu.vue'
import professorMainBody from './components/professor/professorMainBody.vue'
import professorAttendanceCheck from './components/professor/professorAttendanceCheck.vue'
import professorGradeRegister from './components/professor/professorGradeRegister.vue'
import professorGradeList from './components/professor/professorGradeList.vue'

/* 지도교수 */
import tutorMenu from './components/tutor/tutorMenu.vue'
import tutorMainBody from './components/tutor/tutorMainBody.vue'
import tutorRealTimeAttendance from './components/tutor/tutorRealTimeAttendance.vue'
import tutorStudentManagement from './components/tutor/tutorStudentManagement.vue'
import tutorAlertStudentSetting from './components/tutor/tutorAlertStudentSetting.vue'

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
  /* 학생 페이지 */
  {
    path: '/student',
    component: studentMenu,
    children: [
      {
        path: '/student',
        components : {
          body : studentMainBody
        }
      },,
      {
        path: '/student/main',
        components : {
          body : studentMainBody
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
    ]
  },
  /* 교과목 교수 페이지 */
  {
    path: '/professor',
    component: professorMenu,
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
        path: '/professor/gradeRegister',
        components : {
          body : professorGradeRegister
        }
      },
      /* 보류 및 제작예정*/
      {
        path: '/professor/gradeCheck',
        components : {
          body : professorGradeList
        }
      },
      {
        path: '/professor/studentManagement',
        components : {
          body : professorMainBody
        }
      }
    ]
  },
  /* 지도 교수 페이지 */
  {
    path: '/tutor',
    component: tutorMenu,
    children: [
      {
        path: '/tutor',
        components : {
          body : tutorMainBody
        }
      },
      {
        path: '/tutor/main',
        components : {
          body : tutorMainBody
        }
      },
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
      }
    ]
  }
];

const router = new VueRouter({mode: 'history', routes : routes});
new Vue(Vue.util.extend({ router }, App)).$mount('#main_div');
