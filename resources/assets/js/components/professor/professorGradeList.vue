<template>

  <div>
    <!-- 상단 이미지 -->
      <div class="panel-header">
        <div class="header text-center">
          <v-layout class = "imgTitle" column align-center justify-center>
            <h2 class="title">Attendance Check</h2>
            <p class="category">Handcrafted by our friend</p>
          </v-layout>
        </div>
      </div>

      <!-- 내용들어갈 영역 -->
      <v-flex xs12>
        <v-container grid-list-xl>
          <v-layout row wrap align-center>



          </v-layout>
        </v-container>
      </v-flex>

  </div>
</template>

<style>
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

    }
  }
}
</script>
