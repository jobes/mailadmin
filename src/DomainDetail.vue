<template>
  <div class="app-content-details">
    <div
      id="app-navigation-toggle-back"
      class="details-back icon-download"
      v-on:click="hideDetails()"
    ></div>
    <div style="margin-left: 10px;" v-if="!aliasLoading && !domainGroupsLoading">Alias:
      <ul>
        <li v-for="aliasObj in aliasList" :key="aliasObj.alias">
          <div class="aliasline" v-if="!aliasObj.deleting">
            <div class="litext">{{aliasObj.alias}}</div>
            <span class="ulicon icon-delete" v-on:click="deleteAlias(aliasObj)"></span>
          </div>
          <div class="aliasline" v-if="aliasObj.deleting">
            <div class="litext deleted">deleted {{aliasObj.alias}}</div>
            <span class="ulicon icon-history" v-on:click="revertAlias(aliasObj)"></span>
          </div>
        </li>
      </ul>
      <form class="aliasform" @submit.prevent="createNewAlias(newAlias)">
        <input type="text" v-model="newAlias" placeholder="new alias">
        <button>save</button>
      </form>
    </div>
    <div v-if="!aliasLoading && !domainGroupsLoading" style="margin: 20px;">
      Mail domain for user in groups:
      <select
        multiple
        style="min-width: 200px; width: 100%; min-height: 500px;"
        v-model="domainGroupsSelected"
      >
        <option
          v-for="domaingroup in domainGroups"
          v-bind:key="domaingroup.gid"
          v-bind:value="domaingroup.gid"
        >{{domaingroup.gid}}</option>
      </select>
      <button style="float: right" v-on:click="saveDomainGroupChange()">save</button>
    </div>
    <div
      v-if="domainGroupsLoading || aliasLoading"
      class="icon-loading"
      style="width: 200px; height: 200px;"
    ></div>
  </div>
</template>

<script>
import {} from "nextcloud-vue";
import axios from "nextcloud-axios";

export default {
  data: function() {
    return {
      domainGroupsSelected: [],
      domainGroupsSelectedOriginal: [],
      domainGroups: [],
      domainGroupsLoading: false,
      aliasList: [],
      newAlias: "",
      aliasLoading: false
    };
  },
  props: ["domain"],
  beforeMount: function() {
    this.loadGroups();
    this.loadDomainGroup(this.domain);
    this.loadAlias(this.domain);
  },
  watch: {
    domain: function(newVal, oldVal) {
      this.loadDomainGroup(newVal);
      this.loadAlias(newVal);
    }
  },
  methods: {
    loadGroups() {
      axios
        .get(OC.generateUrl("/apps/mailadmin/groups"))
        .then(response => {
          this.domainGroups = response.data;
        })
        .catch(error => {
          OC.Notification.showTemporary(`Failed to load groups`);
          console.error(error);
        });
    },
    loadDomainGroup(domain) {
      this.domainGroupsLoading = true;
      axios
        .get(OC.generateUrl("/apps/mailadmin/domaingroups/" + domain))
        .then(response => {
          this.domainGroupsSelected = response.data.map(x => x.gid);
          this.domainGroupsSelectedOriginal = response.data.map(x => x.gid);
          this.domainGroupsLoading = false;
        })
        .catch(error => {
          this.domainGroupsLoading = false;
          OC.Notification.showTemporary(`Failed to load groups`);
          console.error(error);
        });
    },
    saveDomainGroupChange() {
      let removed = [],
        added = [];
      this.domainGroupsSelectedOriginal.forEach(x => {
        if (!this.domainGroupsSelected.includes(x)) {
          removed.push(x);
        }
      });

      this.domainGroupsSelected.forEach(x => {
        if (!this.domainGroupsSelectedOriginal.includes(x)) {
          added.push(x);
        }
      });

      axios
        .post(OC.generateUrl("/apps/mailadmin/domaingroups"), {
          domain: this.domain,
          added: added,
          removed: removed
        })
        .then(response => {
          this.domainGroupsSelectedOriginal = this.domainGroupsSelected.slice(
            0
          );
          this.$emit("user-reload-needed");
        })
        .catch(error => {
          OC.Notification.showTemporary(`Failed to load groups`);
          console.error(error);
        });
    },
    hideDetails() {
      $("#app-navigation-toggle").removeClass("showdetails");
      $(".app-content-list").removeClass("showdetails");
    },
    loadAlias(domain) {
      this.aliasLoading = true;
      this.aliasList = [];
      axios
        .get(OC.generateUrl("/apps/mailadmin/domainalias/" + domain))
        .then(response => {
          if (response.data.status === -1) {
            throw new FollowException();
          }
          this.aliasList = response.data.map(x => {
            x.deleting = false;
            x.deleteTimer = null;
            return x;
          });
          this.aliasLoading = false;
        })
        .catch(error => {
          this.aliasLoading = false;
          OC.Notification.showTemporary(`Failed to load domain alias`);
        });
    },
    createNewAlias(alias) {
      if (
        !alias ||
        !alias.match(
          /^(?!:\/\/)([a-zA-Z0-9-_]+\.)*[a-zA-Z0-9][a-zA-Z0-9-_]+\.[a-zA-Z]{2,11}?$/gim
        )
      ) {
        OC.Notification.showTemporary("invalid domain", {
          type: "error",
          timeout: 700
        });
      } else {
        axios
          .post(OC.generateUrl("/apps/mailadmin/domainalias"), {
            domain: this.domain,
            alias: alias
          })
          .then(response => {
            this.loadAlias(this.domain);
            this.newAlias = "";
          })
          .catch(error => {
            OC.Notification.showTemporary(`Failed to create alias`);
          });
      }
    },
    deleteAlias(aliasObj) {
      aliasObj.deleting = true;
      aliasObj.deleteTimer = setTimeout(() => {
        axios
          .delete(
            OC.generateUrl(
              "/apps/mailadmin/domainalias/" + aliasObj.alias
            )
          )
          .then(response => {
            this.aliasList = this.aliasList.filter(
              x => x.alias != aliasObj.alias
            );
          })
          .catch(error => {
            OC.Notification.showTemporary(`Failed to load domains`);
          });
      }, 7000);
    },
    revertAlias(aliasObj) {
      aliasObj.deleting = false;
      clearTimeout(aliasObj.deleteTimer);
      aliasObj.deleteTimer = null;
    },
  }
};
</script>

<style lang="scss" scoped>
.mx-datepicker {
  width: 120px;
}

ul {
  list-style: disc;
  list-style-position: inside;
}

li {
  &:hover {
    background-color: WhiteSmoke;
  }
  padding-left: 10px;
  width: calc(100% - 20px);
  max-width: 500px;
}

.floatleft {
  float: left;
}

.ulicon {
  @extend .floatleft;
  cursor: pointer;
  margin: 2px;
}

.aliasline {
  width: calc(100% - 20px);
  float: right;
}

.deleted {
  color: grey;
}

.litext {
  @extend .floatleft;
  width: calc(100% - 20px);
}

.aliasform {
  margin-top: 0;
}
</style>
