<template>
  <div class = "main">

    <!-- Main Image -->
    <v-carousel hide-controls hide-delimiters style="height:570px;">
      <v-carousel-item
        v-for="(item,i) in items"
        :key="i"
        :src="item.src"
        transition="fade"
        reverse-transition="fade">
      </v-carousel-item>
    </v-carousel>
    <!-- Main Image Inside Text -->
    <div class = "imgTextBox">
      <p>Growth, Resilience, Intrinsicmotivation, Tenacity</p>
      <p>成長、回復力、内在的モチベーション, 粘り</p>
    </div>

    <!-- Notice Box -->
    <v-layout column wrap align-center>
      <v-flex xs12 sm4 >
        <div class="text-xs-center" style="margin:35px 0 20px 0;">
          <h2 class="headline">NOTICE</h2>
          <span class="subheading"> 최신 알림을 확인해보세요.</span>
        </div>
      </v-flex>

      <v-flex xs12>
        <v-container grid-list-xl>
          <v-layout row wrap align-center>
            <!-- 최근 알림 -->
            <v-flex xs12 md6>
              <v-card hover>
                <v-card-title primary-title class="layout justify-center">
                  <div class="headline">
                    <v-icon>notifications</v-icon>&nbsp;최근 알림</div>
                  <v-btn flat class="blue--text">Read More</v-btn>
                </v-card-title>
                <v-card-text>
                  Cras facilisis mi vitae nunc lobortis pharetra. Nulla volutpat tincidunt ornare. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Nullam in aliquet odio. Aliquam eu est vitae tellus bibendum tincidunt. Suspendisse
                  potenti.
                </v-card-text>
              </v-card>
            </v-flex>
            <!-- 금일 지각자 -->
            <v-flex xs12 md6>
              <v-card hover>
                <v-card-title primary-title class="layout justify-center">
                  <div class="headline">
                    <v-icon>directions_run</v-icon>&nbsp;금일 지각자</div>
                  <v-btn flat class="blue--text">Read More</v-btn>
                </v-card-title>
                <v-card-text>
                  Cras facilisis mi vitae nunc lobortis pharetra. Nulla volutpat tincidunt ornare. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Nullam in aliquet odio. Aliquam eu est vitae tellus bibendum tincidunt. Suspendisse
                  potenti.
                </v-card-text>
              </v-card>
            </v-flex>
            <!-- 상담 요청 -->
            <v-flex xs12 md6>
              <v-card hover>
                <v-card-title primary-title class="layout justify-center">
                  <div class="headline text-xs-center">
                    <v-icon>face</v-icon>&nbsp;상담요청</div>
                  <v-btn flat class="blue--text">Read More</v-btn>
                </v-card-title>
                <v-card-text>
                  Cras facilisis mi vitae nunc lobortis pharetra. Nulla volutpat tincidunt ornare. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Nullam in aliquet odio. Aliquam eu est vitae tellus bibendum tincidunt. Suspendisse
                  potenti.
                </v-card-text>
              </v-card>
            </v-flex>
            <!-- 사랑이 필요한 학생 -->
            <v-flex xs12 md6>
              <v-card hover>
                <v-card-title primary-title class="layout justify-center">
                  <div class="headline text-xs-center">
                    <v-icon>favorite</v-icon>&nbsp;사랑이 필요한 학생</div>
                  <v-btn flat class="blue--text">Read More</v-btn>
                </v-card-title>
                <v-card-text>
                  Cras facilisis mi vitae nunc lobortis pharetra. Nulla volutpat tincidunt ornare. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Nullam in aliquet odio. Aliquam eu est vitae tellus bibendum tincidunt. Suspendisse
                  potenti.
                </v-card-text>
              </v-card>
            </v-flex>

          </v-layout>
        </v-container>
      </v-flex>
    </v-layout>
  </div>
</template>

<style>
/*-- 메인 이미지 --*/
img {
  max-width: 100%;
  height: auto;
}
/*-- 메인 이미지 안 텍스트 --*/
.imgTextBox {
  font-size: 30px;
  position: absolute;
  top:20%;
  right:15%;
  color: white;
  z-index: 2;
  text-align: center;
  font-family: "Mplus 1p";
  font-weight: lighter;
}
</style>

<script>
export default {
  data () {
      return {
        items: [
          {
            src: '/images/1.jpg'
          },
          {
            src: '/images/2.jpg'
          },
          {
            src: '/images/3.jpg'
          },
          {
            src: '/images/4.jpg'
          }
        ]
      }
    }
}
</script>
