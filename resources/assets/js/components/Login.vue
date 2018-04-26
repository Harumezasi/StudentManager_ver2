<template>
    <v-app>
        <!-- 로그인 창 박스 -->
        <v-content class="login">
            <v-container fluid fill-height>
                <v-layout align-center justify-center>
                    <v-flex xs12 sm8 md4>
                        <form method="post" @submit.prevent="login">
                            <!-- 타이틀 이미지 출력 -->
                            <div class="text-xs-center">
                                <img class = "logo-box" src="/images/logoShadow.png" />
                            </div>
                            <!-- ID 입력 필드 -->
                            <v-text-field
                                    prepend-icon="person"
                                    name="id"
                                    label="ID"
                                    type="text"
                                    v-model="id"
                            ></v-text-field>
                            <!-- 패스워드 입력 필드 -->
                            <v-text-field
                                    prepend-icon="lock"
                                    name="password"
                                    label="Password"
                                    id="password"
                                    type="password"
                                    v-model="pw"
                            ></v-text-field>

                            <!-- 로그인 버튼 -->
                            <v-btn type="submit">로그인</v-btn>
                        </form>
                    </v-flex>
                </v-layout>
            </v-container>
        </v-content>
    </v-app>
</template>
<style>
    /* 로그인 박스 클래스 정의 */
    .login {
        background-color: rgb(255, 255, 255);
    }

    /* 로고 박스 정의 */
    .logo-box {
        width: 300px;
        height: auto;
        margin-bottom: 30px;
    }
</style>
<script>
    export default {
        data() {
            return{
                id : null,
                pw : null
            }
        },
        props: {
            source: String
        },
        methods: {
            /* 로그인 메서드 */
            login: function() {
                this.$http.post('/login', {
                    id: this.id,
                    password: this.pw
                })
                    .then((response) => {
                        if(response.data.status === true) {
                            // 로그인 성공
                            location.reload(true);
                        } else {
                            // 로그인 실패
                            alert(response.data.message)
                        }
                    })
                    .catch((error) => {
                        console.log(error.data);
                    })
            }
        }
    }
</script>