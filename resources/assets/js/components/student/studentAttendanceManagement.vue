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

                    <v-flex xs12 sm4>
                        <div class="text-xs-center">
                            <h2 class="headline">등/하교 출결</h2>
                        </div>

                        <!-- 날짜 선택 (월 선택만 가능하게 함) -->
                        <v-dialog
                                ref="dialog"
                                persistent
                                v-model="modal"
                                lazy
                                full-width
                                width="290px"
                                :return-value.sync="date"
                        >
                            <v-text-field
                                    slot="activator"
                                    label="Picker in dialog"
                                    v-model="date"
                                    prepend-icon="event"
                                    readonly
                            ></v-text-field>
                            <v-date-picker type="month" v-model="date" scrollable>
                                <v-spacer></v-spacer>
                                <v-btn flat color="primary" @click="modal = false">Cancel</v-btn>
                                <v-btn flat color="primary" @click="$refs.dialog.save(date)">OK</v-btn>
                            </v-date-picker>
                        </v-dialog>
                    </v-flex>

                    <!-- 그래프 부분 -->
                    <v-container grid-list-xl>
                        <v-layout row wrap>
                            <v-flex xs12 md3>
                                <!-- 출석률 ( 도넛그래프 ) -->
                                <v-chip color="green" text-color="white">출석률</v-chip>
                                <doughnut-chart></doughnut-chart>
                            </v-flex>
                            <!-- 한 달 동안 출,지,결,조 를 몇번 했나에 대한 그래프 -->
                            <v-flex xs12 md9>
                                <bar-chart></bar-chart>
                            </v-flex>
                        </v-layout>
                    </v-container>

                    <!-- 출석 관련 표로 보기 영역 -->
                    <v-flex xs12>
                        <v-data-table
                                :headers="headers"
                                :items="attendanceData"
                                hide-actions
                                class="elevation-0"
                        >
                            <template slot="items" slot-scope="props">
                                <!-- 출석과 최근 출석 -->
                                <td class="text-xs-center">{{ props.item.sign_in }}</td>
                                <td class="text-xs-center">{{ props.item.recent_sign_in }}</td>
                                <!-- 지각과 최근 지각-->
                                <td class="text-xs-center">{{ props.item.lateness }}</td>
                                <td class="text-xs-center">{{ props.item.recent_lateness }}</td>
                                <!-- 결석과 최근 결석 -->
                                <td class="text-xs-center">{{ props.item.absence }}</td>
                                <td class="text-xs-center">{{ props.item.recent_absence }}</td>
                                <!-- 조퇴와 최근 조퇴 -->
                                <td class="text-xs-center">{{ props.item.early_leave }}</td>
                                <td class="text-xs-center">{{ props.item.recent_early_leave }}</td>
                            </template>
                        </v-data-table>
                    </v-flex>

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
    /**/

    .contents {
        text-align: center;
        background-color: rgb(255, 255, 255);
    }

    .attendanceCheckTitleEng {
        color: white;
        font-family: inherit;
    }

    .attendanceCheckTitleJap {
        color: rgb(0, 0, 0);
        font-size: 20px;
        font-family: MS Gothic;
    }
</style>

<script>

    import Vue from 'vue'
    import VueChartJs from 'vue-chartjs'
    import {Bar} from 'vue-chartjs'
    import {Doughnut} from 'vue-chartjs'

    /*-- bar --*/
    Vue.component('bar-chart', {
        extends: VueChartJs.HorizontalBar,
        data() {
            return {
                // 출석
                attendance : 0,
                // 지각
                late : 0,
                // 결석
                absence : 0,
                // 조퇴
                early : 0
            }
        },
        mounted () {
            this.attendance,
                this.late,
                this.absence,
                this.early,
                this.getData()
        },
        methods : {
            // axios start
            getData() {
                axios.get('/student/attendance')
                    .then((response) => {
                    if(response.data.status === true) {
                        // 서버와의 통신이 정상적으로 성공했을 경우
                        this.attendance = response.data.message['sign_in'];
                        this.late = response.data.message['lateness'];
                        this.absence = response.data.message['absence'];
                        this.early = response.data.message['early_leave'];
                        /* 그래프를 그린다. */
                        this.renderChart({
                                labels: ['출석', '지각', '결석', '조퇴'],
                                datasets: [
                                    {
                                        backgroundColor: ['#009a92', '#f6c202', '#f53e3e', '#787878'],
                                        pointBackgroundColor: 'white',
                                        borderWidth: 1,
                                        pointBorderColor: '#249EBF',
                                        data: [this.attendance, this.late, this.absence, this.early]
                                    }
                                ],
                                responsive: true,
                                maintainAspectRatio: false,
                                height: 100
                            },
                            {responsive: true, maintainAspectRatio: false});
                    } else{
                        // 조회된 기록이 없을 경우
                        alert('조회된 출석기록이 없습니다.');
                    }
                    }).catch((error) => {
                    console.log(error);
                });
            }
            // axios function End
        }
    })

    /* 출석률 - 도넛 그래프 */
    Vue.component('doughnut-chart', {
        extends: VueChartJs.Doughnut,
        data() {
            return {
                attendanceData : 0,
            }
        },
        mounted () {
            this.attendanceData,
                this.getData()
        },
        methods : {
            // axios start
            getData() {
                axios.get('/student/attendance')
                    .then((response) => {
                    if(response.data.status === true) {
                        // 서버와의 통신이 정상적으로 성공했을 경우
                        /* 받아온 값 중 필요한 데이터인 출석률 값만 취한다. */
                        this.attendanceData = response.data.message['attendance_rate'];
                        /* 그래프를 그린다. */
                        this.renderChart({
                                datasets: [
                                    {
                                        label: '출석률',
                                        backgroundColor: ['#6bb9ff', 'rgb(200, 200, 200)'],
                                        pointBackgroundColor: 'white',
                                        borderWidth: 1,
                                        pointBorderColor: '#249EBF',
                                        data: [this.attendanceData, 100 - this.attendanceData]
                                    }
                                ],
                                height: 200
                            },
                            {responsive: true, maintainAspectRatio: false})
                    } else {
                        // 조회된 기록이 없을 경우
                    }
                    }).catch((error) => {
                    console.log(error);
                });
            }
            // axios function End
        }
    })

    /*-- 표 데이터 --*/
    export default {
        data () {
            return {
                headers: [
                    { text: '출석', value: 'sign_in' },
                    { text: '최근출석', value: 'recent_sign_in' },
                    { text: '지각', value: 'lateness' },
                    { text: '최근지각', value: 'recent_lateness' },
                    { text: '결석', value: 'absence' },
                    { text: '최근결석', value: 'recent_absence' },
                    { text: '조퇴', value: 'early_leave' },
                    { text: '최근조퇴', value: 'recent_early_leave' }
                ],
                attendanceData: [],
                /*-- 날짜 선택 관련 --*/
                date: null,
                menu: false,
                modal: false
            }
        },
        mounted() {
            this.attendanceData = [];
            this.getData();
        },
        methods: {
            getData() {
                axios.get('/student/attendance')
                    .then((response) => {
                        if(response.data.status === true) {
                            // 서버와의 통신에 정상적으로 성공했을 경우
                            this.attendanceData.push(response.data.message);

                        } else {
                            // 조회된 기록이 없을 경우
                        }
                    }).catch((error) => {
                    console.log(error);
                });
            }
        }
    }
</script>
