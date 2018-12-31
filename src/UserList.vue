<template>
  <div class="app-content-list">
    <!-- showdetails class to show detail -->
    <div v-if="!userListLoading">
      <a
        class="app-content-list-item"
        v-bind:class="{active: !selectedUser}"
        href="#"
        v-on:click="activateUser(null)"
      >
        <div class="app-content-list-item-icon contact__icon icon-category-tools"></div>
        <div class="app-content-list-item-line-one">Domain settings</div>
      </a>
    </div>
    <div v-if="userListLoading" class="icon-loading" style="height:100%"></div>
    <a
      v-for="user in userList"
      :key="user"
      class="app-content-list-item"
      v-bind:class="{active: user===selectedUser}"
      href="#"
      v-on:click="activateUser(user)"
    >
      <div class="app-content-list-item-icon contact__icon icon-user"></div>
      <div class="app-content-list-item-line-one">{{user}}</div>
    </a>
  </div>
</template>

<script>
import {} from "nextcloud-vue";
import axios from "nextcloud-axios";

export default {
  data: function() {
    return {
      selectedUser: null,
      userListLoading: false,
      userList: []
    };
  },
  props: ["domain"],
  methods: {
    activateUser(user) {
      if (user === this.selectedUser) return;
      this.selectedUser = user;
      this.$emit("selected-user", user);
    },
    reloadUsers() {
      this.userListLoading = true;
      this.userList = [];
      this.activateUser(null);

      axios
        .get(OC.generateUrl("/apps/mailadmin/users/" + this.domain))
        .then(response => {
          this.userList = response.data.map(x => x.uid);
          this.userListLoading = false;
        })
        .catch(error => {
          this.userListLoading = false;
          OC.Notification.showTemporary(`Failed to load groups`);
          console.error(error);
        });
    }
  }
};
</script>

<style>
</style>
