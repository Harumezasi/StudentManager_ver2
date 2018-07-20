<template>
  <div>
    <div class="helpArea">
      <v-card class="formArea">

        <v-card-title>
          <h2> PWを探す </h2>
          <v-spacer></v-spacer>
          <div class="backBtnPositionDiv">
            <v-btn block outline color="red" v-if="!lock" v-on:click="pageOut()">戻る</v-btn>
            <v-btn block outline color="red" v-else disabled>戻る</v-btn>
          </div>
        </v-card-title>

        <v-card-actions>
          <div class="labelArea"> ID </div>

          <v-text-field solo-inverted type="text" v-if="!lock" v-model="id" class="inputArea"></v-text-field>
          <v-text-field solo-inverted type="text" v-else       v-model="id" class="inputArea" disabled></v-text-field>

          <div class="btnSizeDiv">
            <v-btn class="btnSize" block outline v-if="!lock" color="blue" v-on:click="pwResetRequest()">PW 変更</v-btn>
            <v-progress-circular
            :size="50"
            :width="7"
            color="green"
            indeterminate
            v-else
          ></v-progress-circular>
          </div>

        </v-card-actions>

      </v-card>
    </div>
  </div>
</template>

<script>
    export default {
        data () {
            return {
              id : null,
              lock : false
            }
        },
        methods: {
          check(){
            alert('有効なIDを入力してください。')
            this.pwResetRequest();
          },
          pwResetRequest(){
            this.lock = true;
            axios.post('/password_change/verify',{
                id : this.id
            }).then((response) => {
              this.lock = false;
              alert('PW 変更案内のメールが送信されました。')
              this.$router.push('/');
            }).catch((error) => {
              console.log('pwRs Err :' + error);
              this.lock = false;
            })

          },
          pageOut(){
            this.$router.push('/');
          }
        }
    }
</script>

<style>
.formArea {
  width: 500px;
  padding: 10px;
}
.labelArea {
  min-width : 30px;
  font-size: 20px;
}
.btnSizeDiv {
  min-width: 100px;
}
.btnSize {
  width: 100px;
  height: 50px;
}
.helpArea {
  min-width: 40%;
  min-height: 30%;

  position: absolute;
  left : 30%;
}


</style>
