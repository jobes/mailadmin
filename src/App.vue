<template>
  <div>
    <div id="app">
      <div id="app-navigation">
        <domain v-on:select-domain="selectDomain($event)" v-on:domain-list="domainList=$event"/>
        <!--<div id="app-settings">
					<div id="app-settings-header">
						<button class="settings-button"
								data-apps-slide-toggle="#app-settings-content"
						></button>
					</div>
					<div id="app-settings-content">
						this
					</div>
        </div>-->
      </div>

      <div id="app-content">
        <div id="app-content-wrapper" v-if="selectedDomain">
          <user-list
            v-bind:domain="selectedDomain"
            v-on:selected-user="selectUser($event)"
            ref="userList"
          />
          <domain-detail
            v-bind:domain="selectedDomain"
            v-if="!selectedUser"
            v-on:user-reload-needed="reloadUsers()"
          />
          <user-detail
            v-bind:user="selectedUser"
            v-bind:domain-list="domainList"
            v-if="selectedUser"
          />
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
</style>

<script>
import { PopoverMenu, AppNavigation, Multiselect, Avatar } from "nextcloud-vue";
import axios from "nextcloud-axios";
import Domain from "./Domain.vue";
import DomainDetail from "./DomainDetail.vue";
import UserList from "./UserList.vue";
import UserDetail from "./UserDetail.vue";

export default {
  name: "App",
  components: {
    PopoverMenu,
    AppNavigation,
    Multiselect,
    Avatar,
    Domain,
    DomainDetail,
    UserList,
    UserDetail
  },
  mixins: [],
  data: function() {
    return {
      isAdmin: false,
      selectedDomain: null,
      selectedUser: null,
      domainList: []
    };
  },
  computed: {},
  watch: {},
  beforeMount: function() {
    // importing server data into the store
    const serverDataElmt = document.getElementById("serverData");
    if (serverDataElmt !== null) {
      this.isAdmin = JSON.parse(
        document.getElementById("serverData").dataset.server
      ).isAdmin;
    }
  },
  created() {},
  destroyed() {},
  methods: {
    selectDomain(domain) {
      this.selectedDomain = domain;
      this.reloadUsers();
    },
    selectUser(user) {
      this.selectedUser = user;
    },
    reloadUsers() {
      setTimeout(() => {
        if (this.$refs.userList) {
          this.$refs.userList.reloadUsers();
        }
      }, 0);
    }
  }
};
</script>
