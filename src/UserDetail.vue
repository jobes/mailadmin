<template>
  <div class="app-content-details">
    <div
      id="app-navigation-toggle-back"
      class="details-back icon-download"
      v-on:click="hideDetails()"
    ></div>
    <div v-if="!loading">Alias:
      <ul>
        <li v-for="aliasObj in permanentAliasList" :key="aliasObj.alias">
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
      <form
        class="aliasform"
        @submit.prevent="createNewAlias(newAliasFirstPartModel, newAliasSecondPartModel)"
      >
        <input type="text" v-model="newAliasFirstPartModel" placeholder="new alias">@
        <select v-model="newAliasSecondPartModel">
          <option
            v-for="domainObj in domainList"
            :key="domainObj.domain"
            v-bind:value="domainObj.domain"
          >{{domainObj.domain}}</option>
        </select>
        <button>save</button>
      </form>
      <div style="margin-top: 30px;">Temporary alias:</div>
      <ul>
        <li v-for="aliasObj in temporaryAliasList" :key="aliasObj.alias">
          <div class="aliasline" v-if="!aliasObj.deleting">
            <div
              class="litext"
            >{{aliasObj.alias}},&nbsp;&nbsp;&nbsp;{{(new Date(aliasObj.validto)).toDateString()}}</div>
            <span class="ulicon icon-delete" v-on:click="deleteAlias(aliasObj)"></span>
          </div>
          <div class="aliasline" v-if="aliasObj.deleting">
            <div class="litext deleted">deleted {{aliasObj.alias}}</div>
            <span class="ulicon icon-history" v-on:click="revertAlias(aliasObj)"></span>
          </div>
        </li>
      </ul>
      <form
        class="aliasform"
        @submit.prevent="createNewTempAlias(newTempAliasFirstPartModel, newTempAliasSecondPartModel, newTempAliasDate)"
      >
        <input type="text" v-model="newTempAliasFirstPartModel" placeholder="new alias" readonly>@
        <select v-model="newTempAliasSecondPartModel">
          <option
            v-for="domainObj in domainList"
            :key="domainObj.domain"
            v-bind:value="domainObj.domain"
          >{{domainObj.domain}}</option>
        </select>
        <datetime-picker :lang="'en'" v-model="newTempAliasDate"/>
        <button>save</button>
      </form>
    </div>
    <div v-if="loading" class="icon-loading" style="width: 200px; height: 200px;"></div>
  </div>
</template>

<script>
import axios from "nextcloud-axios";
import { DatetimePicker } from "nextcloud-vue";
export default {
  components: {
    DatetimePicker
  },
  data: function() {
    return {
      loading: false,
      newAliasFirstPartModel: "",
      newAliasSecondPartModel: "",
      newTempAliasFirstPartModel: "",
      newTempAliasSecondPartModel: "",
      aliasList: [],
      newTempAliasDate: ""
    };
  },
  props: ["user", "domainList", "domain"],
  watch: {
    user: function(user) {
      this.loadUserAlias(user);
    }
  },
  beforeMount: function() {
    this.loadUserAlias(this.user);
  },
  computed: {
    temporaryAliasList: function() {
      return this.aliasList.filter(x => {
        return x.validto;
      });
    },
    permanentAliasList: function() {
      return this.aliasList.filter(x => {
        return !x.validto;
      });
    }
  },
  methods: {
    deleteAlias(aliasObj) {
      aliasObj.deleting = true;
      let email = this.user + "@" + this.domain;
      aliasObj.deleteTimer = setTimeout(() => {
        axios
          .delete(
            OC.generateUrl(
              "/apps/mailadmin/useralias/" + email + "/" + aliasObj.alias
            )
          )
          .then(response => {
            this.aliasList = this.aliasList.filter(
              x => x.alias != aliasObj.alias
            );
          })
          .catch(error => {
            OC.Notification.showTemporary(`Failed to load domains`);
            console.error(`Failed to load domains`);
          });
      }, 7000);
    },
    revertAlias(aliasObj) {
      aliasObj.deleting = false;
      clearTimeout(aliasObj.deleteTimer);
      aliasObj.deleteTimer = null;
    },
    validateEmail(email) {
      var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
      return re.test(String(email).toLowerCase());
    },
    createNewAlias(firstpart, secondpart) {
      let aliasemail = firstpart + "@" + secondpart;
      if (this.validateEmail(aliasemail)) {
        axios
          .post(OC.generateUrl("/apps/mailadmin/useralias"), {
            email: this.user + "@" + this.domain,
            alias: aliasemail
          })
          .then(response => {
            this.loadUserAlias(this.user);
            this.newAliasFirstPartModel = "";
            this.newAliasSecondPartModel = "";
          })
          .catch(error => {
            OC.Notification.showTemporary(`Failed to create alias`);
          });
      } else {
        OC.Notification.showTemporary(`alias invalid`);
      }
    },
    loadUserAlias(user) {
      (this.newAliasFirstPartModel = ""),
        (this.newAliasSecondPartModel = ""),
        (this.newTempAliasFirstPartModel = this.generateRandomText()),
        (this.newTempAliasSecondPartModel = ""),
        (this.aliasList = []),
        (this.newTempAliasDate = "");
      let email = user + "@" + this.domain;
      this.loading = true;

      axios
        .get(OC.generateUrl("/apps/mailadmin/useralias/" + email))
        .then(response => {
          this.loading = false;
          if (response.data.status === -1) {
            throw new FollowException();
          }
          this.aliasList = response.data.map(x => {
            x.deleting = false;
            x.deleteTimer = null;
            return x;
          });
        })
        .catch(error => {
          this.loading = false;
          OC.Notification.showTemporary(`Failed to load domains`);
          console.error(`Failed to load domains`);
        });
    },
    generateRandomText() {
      var length = 15;
      var result = "";
      var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";

      for (var i = 0; i < length; i++)
        result += possible.charAt(Math.floor(Math.random() * possible.length));

      return result;
    },
    createNewTempAlias(firstpart, secondpart, todate) {
      let aliasemail = firstpart + "@" + secondpart;
      if (!todate || todate.getTime() < new Date().getTime()) {
        OC.Notification.showTemporary(`date invalid`);
      } else if (!this.validateEmail(aliasemail)) {
        OC.Notification.showTemporary(`alias invalid`);
      } else {
        axios
          .post(OC.generateUrl("/apps/mailadmin/useralias"), {
            email: this.user + "@" + this.domain,
            alias: aliasemail,
            validto: todate
          })
          .then(response => {
            this.loadUserAlias(this.user);
            this.newTempAliasFirstPartModel = this.generateRandomText();
            this.newTempAliasSecondPartModel = "";
          })
          .catch(error => {
            OC.Notification.showTemporary(`Failed to save alias`);
          });
      }
    },
    hideDetails() {
      $("#app-navigation-toggle").removeClass("showdetails");
      $(".app-content-list").removeClass("showdetails");
    }
  }
};
</script>

<style lang="scss" scoped>
input,
select {
  width: 150px;
}

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

.app-content-details {
  margin-left: 10px;
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
