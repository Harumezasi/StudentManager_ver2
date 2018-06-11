<template>
  <div class = "StudentAnalyticPrediction">

    <!-- Header -->
    <div class="panel-header">
      <div class="header text-center"></div>
    </div>

    <!-- 학생 목록 영역-->
    <v-flex xs12>
      <v-container grid-list-xl>
        <v-layout row wrap align-center>
          <v-flex xs12 md4>
            <v-card class = "studentListCard">
              <v-card-text>
                <h2 class = "studentListTitle">학생목록</h2>
              </v-card-text>
              <!-- 학생 분류 버튼 -->
              <v-card-text class = "buttonBox">
                <v-btn round outline color="blue accent-2">전체</v-btn>
                <v-btn round outline color="blue accent-2">주목</v-btn>
                <v-btn round outline color="blue accent-2">사랑</v-btn>
              </v-card-text>
              <!-- 학생 목록 -->
              <v-list three-line>
               <template v-for="(item, index) in items">
                 <!-- 전체, 주목, 사랑 버튼 중 한가지 선택 시, 선택한 버튼 텍스트 띄우는 영역 -->
                 <v-subheader v-if="item.header" :key="item.header">{{ item.header }}</v-subheader>
                 <!-- 구분선 -->
                 <v-divider v-else-if="item.divider" :inset="item.inset" :key="index"></v-divider>
                 <!-- 학생 사진, 이름과 학번, 사랑도, 주목된 이유 의 정보가 뜨게 함 -->
                 <v-list-tile v-else :key="item.name" avatar @click="">
                   <!-- 학생 사진 -->
                   <v-list-tile-avatar>
                     <img :src="item.avatar">
                   </v-list-tile-avatar>
                   <v-list-tile-content>
                     <!-- 학생의 이름과 학번 -->
                     <v-list-tile-title v-html="item.name"></v-list-tile-title>
                     <!-- 사랑 -->
                     <v-icon small v-html="item.interest" color = "red"></v-icon>
                     <!-- 주목된 이유 -->
                     <v-btn small depressed v-html="item.attention" round color="light-green lighten-1" ></v-btn>
                   </v-list-tile-content>
                 </v-list-tile>
               </template>
             </v-list>
            </v-card>
          </v-flex>
        </v-layout>
      </v-container>
    </v-flex>

    <!-- 각종 그래프가 들어올 영역 -->
    <v-flex xs12>
      <v-container grid-list-xl>
        <v-layout row wrap align-center>
          <v-flex xs12 md4>
            <v-card>

              그래프가 들어올 영역
              1. 출석정보
               1-1. 기간단위 : 일 , 범위 : 주
               1-2. 지각 조퇴 결석
               1-3. 지각 결석
               1-4. 지각 결석 + 조퇴
              2. 학업정보
               2-1. 일본어, 전공
               2-2. 각 강의
               2-3. 과제, 쪽지, 기말, 중간

            </v-card>
          </v-flex>
        </v-layout>
      </v-container>
    </v-flex>









    <!-- 분석 기준 설정 영역 -->
    <v-btn
     fab
     bottom
     right
     color="primary"
     dark
     fixed
     @click.stop="dialog = !dialog"
     >
      <v-icon>settings</v-icon>
    </v-btn>
    <v-dialog v-model="dialog" width="500px">
      <v-card>
        <v-card-title class="grey lighten-4 py-4 title">
          분석 조건 설정
        </v-card-title>
        <!-- 분석 조건 설정 : 출석 -->
         <v-container grid-list-sm class="pa-4">
           <v-layout row wrap>
             <v-flex xs2>
               <v-btn small depressed fab dark color="green" >
                 <v-icon>check</v-icon>
               </v-btn>
             </v-flex>
             <v-flex xs10>
               <h2 class = "attendanceSettingTitle">출석</h2>
             </v-flex>
             <v-flex xs12 sm4 md4>
               <!-- 전체 선택 -->
               <v-checkbox
                 v-model="attendanceSelected"
                 label="전체"
                 value="allCheck"
                 color = "primary"
                 hide-details
               ></v-checkbox>
               <!-- 지각 -->
               <v-checkbox
                 v-model="attendanceSelected"
                 label="지각"
                 value="checkLate"
                 color = "primary"
                 hide-details
               ></v-checkbox>
             </v-flex>
             <v-flex xs12 sm4 md4>
               <!-- 조퇴 -->
               <v-checkbox
                 v-model="attendanceSelected"
                 label="조퇴"
                 value="checkEarlyLeave"
                 color = "primary"
                 hide-details
               ></v-checkbox>
               <!-- 결석 -->
               <v-checkbox
                 v-model="attendanceSelected"
                 label="결석"
                 value="checkAbsence"
                 color = "primary"
                 hide-details
               ></v-checkbox>
             </v-flex>
             </v-flex>
           </v-layout>
         </v-container>
         <hr />
         <!-- 분석 조건 설정 : 학업 -->
         <v-container grid-list-sm class="pa-4">
           <v-layout row wrap>
             <v-flex xs2>
               <v-btn small depressed fab dark color="green" >
                 <v-icon>school</v-icon>
               </v-btn>
             </v-flex>
             <v-flex xs10>
               <h2 class = "attendanceSettingTitle">학업</h2>
             </v-flex>
             <v-flex xs12 sm6 md6>
              <!-- 전체 선택 -->
              <v-checkbox
                v-model="studySelected"
                label="전체"
                color="primary"
                value="allCheck"
                hide-details
              ></v-checkbox>
              <!-- 일본어 -->
              <v-checkbox
                v-model="studySelected"
                label="일본어"
                color="primary"
                value="checkJapanese"
                hide-details
              ></v-checkbox>
              <!-- 전공 전체 선택 -->
              <v-checkbox
                v-model="studySelected"
                label="전공"
                color="primary"
                value="checkAllMajor"
                hide-details
              ></v-checkbox>
              <!-- 전공 별 체크 -->
              <v-card-text>
                <v-checkbox
                  v-model="studySelected"
                  label="객체지향언어"
                  color="primary"
                  value="chekJava"
                  hide-details
                ></v-checkbox>
                <v-checkbox
                  v-model="studySelected"
                  label="데이터베이스"
                  color="primary"
                  value="checkDB"
                  hide-details
                ></v-checkbox>
                <v-checkbox
                  v-model="studySelected"
                  label="네트워크개론"
                  color="primary"
                  value="checkNetwork"
                  hide-details
                ></v-checkbox>
              </v-card-text>
            </v-flex>
           </v-layout>
         </v-container>
         <!-- 취소 / 저장 버튼 -->
         <v-card-actions>
           <v-spacer></v-spacer>
           <v-btn flat color="primary" @click="dialog = false">Cancel</v-btn>
           <v-btn flat @click="dialog = false">Save</v-btn>
         </v-card-actions>
       </v-card>
     </v-dialog>

  </div>
</template>

<style>

/*-- 헤더 영역 --*/
.panel-header {
  height: 100px;
  padding-top: 70px;
  padding-bottom: 45px;
  background: #141E30;
  /* fallback for old browsers */
  background: -webkit-gradient(linear, left top, right top, from(#0c2646), color-stop(60%, #204065), to(#2a5788));
  background: linear-gradient(to right, #0c2646 0%, #204065 60%, #2a5788 100%);
  position: relative;
  overflow: hidden;
}
.panel-header-sm {
  height: 135px;
}
.panel-header-lg {
  height: 380px;
}

/*--- 학생 분류 카드 ---*/
.studentListCard {
  position: relative;
  bottom: 57px;
  left: -30px;
  border-radius: 0.2975rem;
  box-shadow: 0 2px 3px 0 rgba(161, 161, 161, 0.36);
  width: 250px;
}

.studentListTitle {
  font-family: "Nanum Gothic Coding";
  font-weight: lighter;
  font-size: 30px;
}
.buttonBox {
  margin: 0 0 0 3px;
}

/*-- 분류 조건 설정 --*/
.attendanceSettingTitle{
  position: relative;
  top: 5px;
  left: -10px;
  font-family: "Nanum Gothic Coding";
  font-weight: lighter;
}

</style>

<script>

export default {
    data () {
      return {
        /*-- 분류 조건 설정 --*/
        dialog: false,
        /*-- 학생 목록 --*/
        items: [
          { header: '전체' },
          {
            avatar: '/images/studentSample.jpg',
            name: '1601231 이승민',
            interest: 'favorite',
            attention: '하위권'
          },
          { divider: true, inset: true },
          {
            avatar: '/images/studentSample2.jpg',
            name: '1601342 박주용',
            interest: 'favorite',
            attention: '하위권'
          },
          { divider: true, inset: true },
          {
            avatar: '/images/studentSample3.jpg',
            name: '1602345 장세원',
            interest: 'favorite',
            attention: '하위권'
          },
          { divider: true, inset: true },
          {
            avatar: '/images/studentSample4.jpg',
            name: '1601121 염세환',
            interest: 'favorite',
            attention: '하위권'
          },
          { divider: true, inset: true },
          {
            avatar: '/images/studentSample5.png',
            name: '1602211 박효동',
            interest: 'favorite',
            attention: '하위권'
          },
      ],
      /*-- 분류 조건 설정 : 출석 --*/
      attendanceSelected: ['allCheck', 'checkLate', 'checkEarlyLeave', 'checkAbsence'],
      /*-- 분류 조건 설정 : 학업 --*/
      studySelected: ['allCheck', 'checkJapanese', 'checkAllMajor', 'checkJava', 'checkDB', 'checkNetwork'],
    }
  }
}
</script>
