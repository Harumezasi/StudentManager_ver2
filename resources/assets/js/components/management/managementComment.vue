<template>
<div class="tutorComment">

  <!-- 코멘트용 말풍선 (왼쪽 오른쪽)-->
  <!-- <div class="box3 sb13">I'm speech bubble</div>
    <div class="box3 sb14">I'm speech bubble</div> -->

  <!-- 코멘트 영역 -->
  <v-flex xs12>
    <v-container grid-list-xl>
      <v-layout row wrap align-center>
        <v-flex d-flex xs12 order-xs5>
          <v-layout column>

                <!-- 코멘트 데이터 -->
                <v-card-text
                class="professor text-xs-left"
                v-for = "comment in commentData"
                :key = "comment.key"
                >
                  <v-avatar class="professorPic" size="70px">
                    <img :src="comment.photo_url" />
                  </v-avatar>
                  <div>
                    <span style="color: black">{{ comment.name }}<br>교수님</span>
                  </div>
                  <!-- 보여지는 코멘트 -->
                  <div class="box3 sb14" v-if="!comment.textView"> {{ comment.content }} </div>

                  <!-- 코멘트 수정시 보여지는 영역 -->
                  <div class="box3 sb14" v-if="comment.textView">
                    <v-text-field
                    textarea
                    v-model="comment.content"
                    >
                    </v-text-field>
                  </div>

                  <!-- 본인 코멘트 일 경우 표시 및 동작 -->
                  <!-- 수정 -->
                  <v-btn v-if="comment.isOwner&&!comment.textView" v-on:click="comment.textView=true, reWriteStart(comment.address)" class = "editDelete" flat icon color = "indigo">
                    <v-icon>edit</v-icon>
                  </v-btn>
                  <!-- 삭제 -->
                  <v-btn v-if="comment.isOwner&&!comment.textView" v-on:click="delComment(comment.id)" class = "editDelete" flat icon color = "indigo">
                    <v-icon>delete_forever</v-icon>
                  </v-btn>

                  <!-- 코멘트 수정 작업시 전환되는 버튼 -->
                  <!-- 수정한 코멘트 저장 -->
                  <v-btn v-if="comment.isOwner&&comment.textView" v-on:click="reWriteComment(comment.id, comment.address)" class = "editDelete" flat icon color = "indigo">
                    <v-icon>save</v-icon>
                  </v-btn>
                  <!-- 수정작업 취소 -->
                  <v-btn v-if="comment.isOwner&&comment.textView" v-on:click="comment.textView=false, reWriteCancel()" class = "editDelete" flat icon color = "indigo">
                    <v-icon>cancel</v-icon>
                  </v-btn>

                </v-card-text>

          </v-layout>
        </v-flex>
      </v-layout>
    </v-container>
  </v-flex>

  <v-container class="line" grid-list-xl>
    <v-flex xs12>
      <v-card class="studentProfileBox" color="transparent" flat>
        <v-container grid-list-xl>

          <v-layout row wrap align-center>
            <!-- 코멘트 입력 -->
            <v-flex xs9>
              <v-text-field
                name="input-1"
                label="코멘트를 입력하세요."
                textarea
                v-model="setData"
              ></v-text-field>
            </v-flex>
            <v-flex xs3>
              <v-btn outline large fab color="indigo"
               v-on:click="setComment()"
              >
                <v-icon>send</v-icon>
              </v-btn>
            </v-flex>
          </v-layout>

        </v-container>
      </v-card>
    </v-flex>
  </v-container>


</div>
</template>

<style>
.professor {}

.professorPic {
  margin: 0 0 10px 7px;
}
.fontSetting {
  font-size: 30px;
  font-style: 'Gothic A1';
}
/*-- 코멘트용 말풍선 --*/
.box3 {
  width: 300px;
  bottom: 80px;
  left: 110px;
  border-radius: 15px;
  background: #00bfb6;
  color: #fff;
  padding: 20px;
  text-align: center;
  font-weight: 900;
  font-family: "Nanum Gothic Coding";
  position: relative;
}
/*-- 왼쪽 말풍선 --*/
.sb13:before {
  content: "";
  width: 0px;
  height: 0px;
  position: absolute;
  border-left: 15px solid #00bfb6;
  border-right: 15px solid transparent;
  border-top: 15px solid #00bfb6;
  border-bottom: 15px solid transparent;
  right: -16px;
  top: 0px;
}
/*-- 오른쪽 말풍선 --*/
.sb14:before {
  content: "";
  width: 0px;
  height: 0px;
  position: absolute;
  border-left: 15px solid transparent;
  border-right: 15px solid #00bfb6;
  border-top: 15px solid #00bfb6;
  border-bottom: 15px solid transparent;
  left: -16px;
  top: 0px;
}
.editDelete {
  position: relative;
  bottom: 80px;
  left: 300px;
}

</style>

<script>
export default {
   data: () => ({
     /* 코멘트 데이터 */
     commentData : [],
     /* 신규 등록한 코멘트 데이터*/
     setData : null,
     /* 임시저장될 코멘트 데이터 변수 */
     backupCom : null,
     /* 수정 모드가 진행 중인지 체크용 변수 */
     reWriteModeCheck : [{
       mode : false,
       address : null
     }]
   }),
   methods : {
     /* 수정 모드 시작 */
     reWriteStart(com_addr){
       /* 수정 모드로 중복 진입할 경우, 기존에 열린 수정 모드가 있는지를 확인하고
       있으면 조회모드로 되돌린다. */
       if(this.reWriteModeCheck[0].mode){
         /* 열린 수정 창을 닫는다. */
         this.commentData[this.reWriteModeCheck[0].address].textView = false;

         /* 기존 수정 작업을 취소 */
         this.reWriteCancel();
       }
       /* 수정 모드로 진입 전, 수정 도중 취소시, 되돌릴 수 있도록 임시 변수에 기존 코멘트를 저장한다. */
       /* 값을 다시 받아와도 되지만, 서버 통신 시간상 시각적 효과가 좋지 않기 때문에 이렇게 구현 */
       this.backupCom = this.commentData[com_addr].content;
       /* 수정 정보를 저장 */
       this.reWriteModeCheck[0].mode = true;
       this.reWriteModeCheck[0].address = com_addr;
     },
     /* 수정 모드 취소 */
     reWriteCancel(){
       /* 수정을 취소할 경우, 임시 저장한 코멘트를 다시 받아온다. */
       this.commentData[this.reWriteModeCheck[0].address].content = this.backupCom;
       /* 변수 초가화 */
       this.backupCom = null;
       this.reWriteModeCheck[0].mode = false;
       this.reWriteModeCheck[0].address = null;
     },
     /* 조회 */
     getComment(){
       axios.get('/professor/detail/comment/select', {
         params : {
           std_id : this.$router.history.current.query.getInfoIdType
         }
       }).then((response) => {
         /* 코멘트 데이터를 초기화 */
         this.commentData = [];
         /* 조회된 코멘트가 있는지 확인 */
         if(response.data.message.comments.length == 14 &&
           response.data.message.comments == '조회된 코멘트가 없습니다.'){
             /* 조회된 코멘트가 없을 경우 */
             this.commentData = [{
               'name' : '안녕하세요!',
               'content' : '등록된 코멘트가 없습니다. 학생에 대해 알려주세요!',
               'isOwner' : false
             }]
           }else{
             /* 코멘트 데이터를 저장 */
             this.commentData = response.data.message.comments
             /* 필요한 데이터를 추가 */
             for( let start = 0; start < this.commentData.length; start++ ){
               /* 코멘트에 각각 제어용 boolean을 추가 */
               this.$set(this.commentData[start], 'textView', false);
               /* 배열을 뒤적이지 않고 바로 접근 가능하도록 주소 값을 추가 */
               this.$set(this.commentData[start], 'address', start);
             }
             console.log(this.commentData);
           }
       }).catch((error) => {
         console.log('getCom Error :' + error);
       })
     },
     /* 등록 */
     setComment(){
       axios.post('/professor/detail/comment/insert', {
        std_id : this.$router.history.current.query.getInfoIdType,
        content : this.setData
       }).then((response) => {
         console.log('set');
         console.log(response);
         /* 코멘트 등록 후, 입력 값을 초기화한다. */
         this.setData = null
         /* 변수 초기화 : 수정작업중, 신규 코멘트를 등록 할 수 있기 때문 */
         this.backupCom = null;
         this.reWriteModeCheck[0].mode = false;
         this.reWriteModeCheck[0].address = null;
         /* 코멘트 등록 후, 새로 업데이트한다. */
         this.getComment();
       }).catch((error) => {
         console.log('setCom Error :' + error);
       })
     },
     /* 수정 */
     reWriteComment(com_id, com_addr){
       /* 코멘트 수정을 시작 */
       axios.post('/professor/detail/comment/update', {
         comment_id : com_id,
         content : this.commentData[com_addr].content
       }).then((response) => {
         /* 변수 초가화 */
         this.backupCom = null;
         this.reWriteModeCheck[0].mode = false;
         this.reWriteModeCheck[0].address = null;
         /* 코멘트 수정 후, 새로 업데이트한다. */
         this.getComment();
       }).catch((error) => {
         console.log('reWriteCom Error :' + error);
       })
     },
     /* 삭제 */
     delComment(delCommentId){
       axios.post('/professor/detail/comment/delete', {
         comment_id : delCommentId
       }).then((response) => {
         /* 코멘트 삭제 후, 새로 업데이트한다. */
         this.getComment();
         /* 삭제 알림 */
         alert('삭제완료');
       }).catch((error) => {
         console.log('delCom Error :' + error);
       })
     }
   },
   mounted() {
     /* 코멘트를 불러온다. */
     this.getComment();
   }

 }

</script>
