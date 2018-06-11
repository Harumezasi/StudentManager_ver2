<template>
  <v-app id="inspire">

    <!-- Left Menu -->
    <v-navigation-drawer class = "black" fixed v-model="drawer" app dark>
      <v-list dense>
        <v-list-tile-content>
          <router-link to="/professor/main"><img class="logo-box" src="/images/grit.png" /></router-link>
        </v-list-tile-content>

        <v-divider></v-divider>

        <v-list class="pa-0">
          <v-list-tile avatar>
            <v-list-tile-avatar>
              <img src="/images/sample.jpg" >
            </v-list-tile-avatar>
            <v-list-tile-content>
              <v-list-tile-title class="userInfoMenu">John Leider</v-list-tile-title>
            </v-list-tile-content>
          </v-list-tile>
        </v-list>

        <v-list>
          <v-list-group
                  v-model="item.active"
                  v-for="item in items"
                  :key="item.title"
                  :prepend-icon="item.action"
                  no-action
          >
            <v-list-tile slot="activator">
              <v-list-tile-content>
                <v-list-tile-title>{{ item.title }}</v-list-tile-title>
              </v-list-tile-content>
            </v-list-tile>
            <router-link
                    tag="v-list-tile"
                    v-for = "subItem in item.items"
                    :key="subItem.title"
                    :to="subItem.path"
                    @click="">
              <v-list-tile-content>
                <v-list-tile-title>{{ subItem.title }}</v-list-tile-title>
              </v-list-tile-content>
              <v-list-tile-action>
                <v-icon>{{ subItem.action }}</v-icon>
              </v-list-tile-action>
            </router-link>
          </v-list-group>
        </v-list>


        <router-link
                tag="v-list-tile"
                v-for="page in listItems"
                :prepend-icon="page.action"
                :key="page.key"
                :to="page.path"
        >
          <v-list-tile-action >
            <v-icon>{{page.action}}</v-icon>
          </v-list-tile-action>

          <v-list-tile-content>
            <v-list-tile-title class = "menu">{{page.text}}</v-list-tile-title>
          </v-list-tile-content>
        </router-link>

      </v-list>
    </v-navigation-drawer>

    <!-- Top Menu -->
    <v-toolbar color="transparent" flat fixed app>
      <v-toolbar-side-icon @click.stop="drawer = !drawer"></v-toolbar-side-icon>
      <v-spacer></v-spacer>
      <v-btn icon>
        <v-icon>notifications_none</v-icon>
      </v-btn>
      <v-btn icon>
        <v-icon>person_outline</v-icon>
      </v-btn>
    </v-toolbar>

    <v-content style="padding-top: 0px;">
      <router-view name = "body"></router-view>
    </v-content>

  </v-app>
</template>

<style>
  .logo-box {
    width: 220px;
    height: auto;
    margin: 30px;
  }
  .userInfoMenu {
    font-size: 15px;
    font-family:"Comfortaa";
  }
  .pa-0{
    margin: 10px 0 5px 0;
  }

</style>

<script>
    export default {
        data: () => ({
            drawer: null,
            items: [{
                action: 'check',
                title: '성적 관리',
                active: true,
                items: [
                    {
                        title: '성적등록',
                        path: '/professor/gradeRegister'
                    },
                    {
                        title: '성적확인',
                        path: '/professor/gradeCheck'
                    }
                ]
            },
            ],

            listItems: [{
                action: 'face',
                text: '학생 관리',
                path: '/professor/studentManagement'
            }
            ],
        }),
        props: {
            source: String
        },
        method: {

        }
    }
</script>
