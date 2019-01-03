<template>
  <div class="app-content-details">
    <div
      id="app-navigation-toggle-back"
      class="details-back icon-download"
      v-on:click="hideDetails()"
    ></div>
    <div v-if="!domainGroupsLoading" style="margin: 20px;">
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
    <div v-if="domainGroupsLoading" class="icon-loading" style="width: 200px; height: 200px;"></div>
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
      domainGroupsLoading: false
    };
  },
  props: ["domain"],
  beforeMount: function() {
    this.loadGroups();
    this.loadDomainGroup(this.domain);
  },
  watch: {
    domain: function(newVal, oldVal) {
      this.loadDomainGroup(newVal);
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
    }
  }
};
</script>

<style>
</style>
