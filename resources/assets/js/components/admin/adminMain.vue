<template>
  <div>
    <v-container fluid grid-list-xl>
      <v-layout wrap align-center>
    관리자 페이지

      <!-- 1. 일정 관리 -->
      <!--
      1. 일정추가
      2. 일정조회
      3. 일정변경
      4. 일정삭제
      -->
        <div>
        <!-- 기간 설정 -->
          <v-dialog v-model="dialog" width="600px">
            <v-card>
              <v-date-picker
               v-model="startDate"
               min="2018-01"
              ></v-date-picker>
              <v-date-picker
               v-model="endDate"
               min="2018-01"
              ></v-date-picker>
              <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn color="primary" @click="dialog = false">期間変更</v-btn>
              </v-card-actions>
            </v-card>
          </v-dialog>
          <!-- End -->

          <v-card>
            <v-btn v-on:click="dialog = true">기간 설정</v-btn>
            <div v-if="startDate == null || endDate == null">
              기간을 지정해 주십시오.
            </div>
            <div v-else>
              기간 : {{ this.startDate }} ~ {{ this.endDate}}
            </div>
            <!--   -->
            <br>
            <div>
              상세  <v-text-field solo-inverted name="id" label="title" type="text" v-model="name" class="inputArea"></v-text-field>
            </div>
            <div>
              <v-radio-group v-model="dayType" class="radioDiv">
                <v-radio label="휴일 지정" value="true"></v-radio>
                <v-radio label="정상 등교" value="false"></v-radio>
              </v-radio-group>
            </div>
            <div>
              등교시간
            </div>
            <div>
              <v-text-field solo-inverted name="id" label="Hour" type="text" v-model="startTimeH" maxlength="2" class="timeArea"></v-text-field>
              :
              <v-text-field solo-inverted name="id" label="Minute" type="text" v-model="startTimeM" maxlength="2" class="timeArea"></v-text-field>
            </div>
            <br>
            <div>
              하교시간
            </div>
            <div>
              <v-text-field solo-inverted name="id" label="Hour" type="text" v-model="endTimeH" maxlength="2" class="timeArea"></v-text-field>
              :
              <v-text-field solo-inverted name="id" label="Minute" type="text" v-model="endTimeM" maxlength="2" class="timeArea"></v-text-field>
            </div>
            <v-card-actions>
              <v-spacer></v-spacer>
              <v-btn v-on:click="">등록버튼</v-btn>
              <v-btn v-on:click="setSchedule()">초기화버튼</v-btn>
            </v-card-actions>
          </v-card>
        </div>






      </v-layout>
    </v-container>
  </div>
</template>

<script>
    export default {
        data () {
            return {
              dialog : false,
              startDate : null,
              endDate : null,
              name : null,
              dayType : 'false',
              includeFlag : false,
              in_flag : true,
              out_flag : true,

              startTimeH : null,
              startTimeM : null,
              endTimeH : null,
              endTimeM : null,
            }
        },
        mounted() {

        },
        methods: {
          getSchedule(){
            axios.get('/admin/schedule/select', {
              params : {

              }
            })
          },
          setSchedule(){
            axios.post('/admin/schedule/insert', {
              'start_date'        : this.startDate,
              'end_date'          : this.endDate,
              'name'              : this.name,
              'holiday_flag'      : this.dayType,
              'include_flag'      : this.includeFlag,
              'in_default_flag'   : this.in_flag,
              'out_default_flag'  : this.out_flag
              // 'contents'          : 'string'
            }).then((response) => {
              console.log(response);
            }).catch((error) => {
              console.log("setSched Err :" + error);
            })
          },
          delSchedule(){

          },
          updateSchedule(){

          },
          timeSetting(time, position, type){
            switch (position) {
              case 'Hour':
                if(time < 0 || time >= 24){
                  alert("입력가능한 범위는 [ 0 ] ~ [ 23 ] 입니다.");
                  return 0;
                }
                break;
              case 'Minute' :
                if(time < 0 || time >= 59){
                  alert("입력가능한 범위는 [ 0 ] ~ [ 59 ] 입니다.");
                  return 0;
                }
                break;
            }

            switch (type) {
              case 'start':

                break;
              case 'end' :

                break;
            }



          }
        },
        watch : {
          startTimeH : function(){
            console.log('sh');
            this.in_flag = this.timeSetting(this.startTimeH, "Hour", "start");
          },
          startTimeM : function(){
            console.log('sm');
            this.in_flag = this.timeSetting(this.startTimeH, "Hour", "start");
          },
          endTimeH : function(){
            console.log('eh');
            this.out_flag = this.timeSetting(this.startTimeH, "Minute", "end");
          },
          endTimeM : function(){
            console.log('em');
            this.out_flag = this.timeSetting(this.startTimeH, "Minute", "end");
          },
        }
    }
</script>

<style>

.inputArea {
  width : 30em;
  display: inline-block;
}
.timeArea {
  width : 5em;
  display: inline-block;
}
</style>
