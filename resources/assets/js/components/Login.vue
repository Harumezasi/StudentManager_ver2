<template>
  <div>
    <!-- 로고 -->
    <v-toolbar app flat fixed>
      <v-toolbar-title>
        <img class="logo-box" src="/images/logo.png" />
      </v-toolbar-title>
    </v-toolbar>

    <!-- Parallax Scroll -->
    <v-parallax :src="images[numbers].link" height="870" class="bgi">
        <div class="shadow">
          <!-- 흐림 효과 -->
          <div class="loginArea">
            <!-- 프로젝트 명 -->
            <div class="loginText">J-Class Project, Start。</div>
            <br>
            <!-- 로그인 버튼-->
            <div class="loginButton">
              <v-btn v-on:click="loginPage=true" round outline color="white--text" class="loginVButton">LOGIN & JOIN</v-btn>
            </div>
          </div>
        </div>
    </v-parallax>

    <!-- Main Contents Section -->
    <div>
      <v-layout column wrap class="my-5" align-center>
        <v-flex xs12 sm4 class="my-3">
          <div class="text-xs-center">
            <!-- NOTICE -->
            <h2 class="headline">Service</h2>
          </div>
        </v-flex>
        <v-flex xs12>
          <!-- 서비스 소개 영역 -->
          <v-container grid-list-xl class="serviceArea">
            <v-layout row wrap align-center>
              <!-- 서비스 소개 시작 -->
              <v-flex xs12 md6>

              </v-flex>
              <!-- 서비스 소개 끝 -->
            </v-layout>
          </v-container>
        </v-flex>
      </v-layout>

      <v-footer height="auto" class="blue lighten-2">
        <v-layout row wrap justify-center>
          <!-- 마무리 -->
          <v-flex xs12 py-3 text-xs-center white--text>
            &copy;2018 — <strong>GRIT</strong>
          </v-flex>
        </v-layout>
      </v-footer>
    </div>
    <!-- 로그인 창 -->
    <transition name="fade">
      <div v-if="loginPage == true" class="loginDivArea">

        <!-- 반투명 영역 -->
        <div class="loginShadowArea" v-on:click="loginPage = false">
        </div>

        <!-- 로그인 폼 -->
        <div class="loginFormArea">
          <!-- 수직 정렬을 위한 div -->
          <div class="loginFormDiv">
            <!-- 실제 form 이 들어가는 영역 -->
            <div class="loginForm">
              <h1> Login & Join </h1>
              <v-form v-on:submit.prevent="login">
                <!-- ID / PW 입력 영역 -->
                <v-text-field prepend-icon="person" solo-inverted name="id" label="ID" type="text" v-model="id"></v-text-field>
                <v-text-field prepend-icon="lock" solo-inverted name="password" label="Password" type="password" v-model="pw"></v-text-field>
                <!-- SUBMIT 실행 버튼 영역 -->
                <v-card-actions>
                  <v-btn block outline color="cyan" type='submit' class="loginButton">ログイン</v-btn>
                </v-card-actions>
              </v-form>
              <!-- form End -->
              <!-- join start -->
              <v-card-actions>
                <v-btn block outline color="orange" type='submit' class="loginButton">新規登録</v-btn>
                <v-btn block outline color="red" type='submit' class="loginButton">PWを探す</v-btn>
              </v-card-actions>
              <!-- join End -->
            </div>
          </div>
        </div>

      </div>
    </transition>
  </div>
</template>

<script>
    export default {
        data () {
            return {
                loginPage : false,
                images : [ { link :'https://jmagazine.joins.com/_data/photo/2014/10/2039295025_9GUqx7BZ_img27.jpg', key : 0},
                    { link :'http://cfile30.uf.tistory.com/image/2357C235524E4CF606D433',key : 1},
                    { link :'http://www.tkdwon.kr/kr/images/content/doyak_top_04_01.jpg',key : 2},
                    { link :'http://www.eduinnews.co.kr/news/photo/201709/8700_4217_481.jpg',key : 3}
                ],
                numbers : 0,
                timer : null,
                id:null,
                pw:null
            }
        },
        mounted() {
            this.startRotation();
        },
        methods: {
            startRotation: function() {
                this.timer = setInterval(this.next, 5000);
            },
            next: function() {
                let delData = this.numbers;
                this.numbers += 1;
                if(this.numbers >= this.images.length){
                    this.numbers = 0;
                }
            },
            login: function() {
                axios.post('./login', {
                    id:this.id,
                    password:this.pw
                })
                    .then((result) => {
                            if(result.data.status === true) {
                                location.reload(true);
                            } else {
                                alert(result.data.message)
                            }
                        }
                    )
                    .catch()
            }
        }
    }
</script>

<style>
  .bgiFade-leave-active, .bgiFade-enter-active{
    transition: all 2.5s ease;
    overflow: hidden;
    visibility: visible;
    opacity: 1;
  }
  .bgiFade-enter, .bgiFade-leave {
    transition: all 2.5s;
    opacity: 0;
    visibility: hidden;
  }
  /* */

  .fade-enter-active {
    transition: all .9s;
  }

  .fade-leave-active {
    transition: all .9s;
  }

  .fade-enter, .fade-leave-to {
    opacity: 0;
  }

  .logo-box {
    margin-top: 40px;
    margin-left: 15px;
    width: 200px;
    height: auto;
  }

  .bgi {
    overflow: hidden;
  }

  .shadow {
    width: 110%;
    height: 870px;
    margin-left: -6%;
    background-color: rgba( 000, 000, 000, 0.5 );

    text-align:center;
    vertical-align:middle;
    position:absolute;
    display: flex;
    align-items:center;
    justify-content:center;
  }

  .loginArea {
    margin-left: 30px;
  }

  .loginText {
    font-size: 40px;
    font-weight: bold;
  }

  .loginVButton {
    width : 300px;
    height:  60px;
    font-weight: bold;
    font-size: 30px;
  }

  .serviceArea img {
    width: 150px;
    height: 150px;
    margin-left: 10px;
    margin-right: 10px;
  }

  .imgArea {
    text-align: center;
  }

  .loginDivArea {
    width:100%;
    height:100%;
    position:fixed;
    top:0px;
    right:0px;
  }

  .loginShadowArea {
    width:60%;
    height:100%;
    background-color: rgba( 000, 000, 000, 0.5 );
    float:left;
  }


  .loginFormArea {
    width:40%;
    height:100%;
    background-color:white;
    float:left;

    display: table;
  }

  .loginButton {
    font-size: 15px;
    font-weight: bold;
    padding-bottom: 40px;
  }

  .loginFormDiv {
    width: 100%;
    height: 100%;

    display: table-cell;
    text-align: center;
    vertical-align: middle;
    align-content: center;
  }

  .loginForm {
    position: relative;
    display: inline-block;
  }

  /*-- Responsive Web Settings --*/
  @media ( max-width: 480px) {
    .container {
      width: auto;
    }
    .content {
      float: none;
      width: auto;
    }
  }
</style>
