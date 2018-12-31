<template>
  <fragment>
    <div class="app-navigation-new">
      <button type="button" class="icon-add" v-on:click="openNewDomain()">Add domain</button>
    </div>
    <ul>
      <li v-bind:class="{ editing: addingDomain }">
        <a href="#" v-if="addingDomain"></a>
        <div class="app-navigation-entry-edit">
          <form
            v-bind:class="{ 'icon-loading-small': domainCreatingWait}"
            @submit.prevent="createNewDomain"
          >
            <fieldset style="display: contents" v-bind:disabled="domainCreatingWait">
              <input type="text" v-model="newDomainModel" placeholder="domain name" autofocus>
              <input type="button" value class="icon-close" v-on:click="closeNewDomain()">
              <input type="submit" value class="icon-checkmark">
            </fieldset>
          </form>
        </div>
      </li>
      <li
        v-for="domainObj in domainlist"
        :key="domainObj.domain"
        v-bind:class="{deleted:domainObj.deleting}"
      >
        <a
          v-bind:class="{hidden:domainObj.deleting, active: domainObj.domain==selectedDomain}"
          href="#"
          v-on:click="activateDomain(domainObj.domain)"
        >{{domainObj.domain}}</a>
        <div class="app-navigation-entry-utils">
          <ul>
            <li
              class="app-navigation-entry-utils-menu-button svg"
              v-on:click="domainMenuId=domainObj.domain"
            >
              <button></button>
            </li>
          </ul>
        </div>

        <div
          class="app-navigation-entry-menu"
          v-bind:class="{open: domainMenuId===domainObj.domain}"
        >
          <ul>
            <li>
              <a href="#" v-on:click="deleteDomain(domainObj)">
                <span class="icon-delete"></span>
                <span>Remove</span>
              </a>
            </li>
          </ul>
        </div>
        <div class="app-navigation-entry-deleted">
          <div class="app-navigation-entry-deleted-description">Deleted {{domainObj.domain}}</div>
          <button
            class="app-navigation-entry-deleted-button icon-history"
            title="Undo"
            v-on:click="deleteDomainCancel(domainObj)"
          ></button>
        </div>
      </li>
    </ul>
  </fragment>
</template>


<style scoped>
</style>

<script>
import {} from "nextcloud-vue";
import axios from "nextcloud-axios";
import { Fragment } from "vue-fragment";

export default {
  //name: 'domain',
  components: {
    Fragment
  },
  mixins: [],
  data: function() {
    return {
      addingDomain: false,
      newDomainModel: "",
      domainCreatingWait: false,
      domainMenuId: null,
      domainsLoading: false,
      domainlist: [],
      selectedDomain: null
    };
  },
  computed: {},
  watch: {},
  beforeMount: function() {
    this.loadDomains();
  },
  created() {
    document.addEventListener("click", this.documentClick);
  },
  destroyed() {
    document.removeEventListener("click", this.documentClick);
  },
  methods: {
    openNewDomain() {
      this.addingDomain = true;
    },
    closeNewDomain() {
      this.addingDomain = false;
      this.newDomainModel = "";
    },
    createNewDomain() {
      if (
        !this.newDomainModel ||
        !this.newDomainModel.match(
          /^(?!:\/\/)([a-zA-Z0-9-_]+\.)*[a-zA-Z0-9][a-zA-Z0-9-_]+\.[a-zA-Z]{2,11}?$/gim
        )
      ) {
        OC.Notification.showTemporary("invalid domain", {
          type: "error",
          timeout: 700
        });
      } else {
        this.domainCreatingWait = true;
        axios
          .post(
            OC.generateUrl("/apps/mailadmin/domain"),
            { domain: this.newDomainModel },
            { headers: {} }
          )
          .then(response => {
            if (response.data.status === -1) {
              throw new FollowException();
            }
          })
          .then(() => {
            this.domainCreatingWait = false;
            this.closeNewDomain();
            this.loadDomains();
          })
          .catch(e => {
            console.error(e);
            this.domainCreatingWait = false;
            OC.Notification.showTemporary("error creating domain");
          });
      }
    },
    loadDomains() {
      this.domainsLoading = true;
      return axios
        .get(OC.generateUrl("/apps/mailadmin/domains"))
        .then(response => {
          this.domainsLoading = false;
          if (response.data.status === -1) {
            throw new FollowException();
          }
          this.domainlist = response.data.map(x => {
            x.deleting = false;
            x.deletetimeout = null;
            return x;
          });
        })
        .catch(error => {
          this.domainsLoading = false;
          OC.Notification.showTemporary(`Failed to load domains`);
          console.error(`Failed to load domains`);
        });
    },
    deleteDomainCancel(domain) {
      domain.deleting = false;
      clearTimeout(domain.deletetimeout);
    },
    documentClick(e) {
      for (var elm of e.path) {
        if (
          elm &&
          elm.classList &&
          elm.classList.contains("app-navigation-entry-utils-menu-button")
        ) {
          return;
        }
      }

      this.domainMenuId = null;
    },
    deleteDomain(domain) {
	  domain.deleting = true;
	  if(this.selectedDomain === domain.domain) {
		  this.activateDomain(null);
	  }
      domain.deletetimeout = setTimeout(() => {
        axios
          .delete(
            OC.generateUrl("/apps/mailadmin/domains/" + domain.domain),
            {},
            { headers: {} }
          )
          .then(
            () => {
              this.domainlist = this.domainlist.filter(x => {
                return x.domain != domain.domain;
              });
            },
            () => {
              domain.deleting = false;
              OC.Notification.showTemporary(`Failed to delete domain`);
            }
          );
      }, 7000);
    },
    activateDomain(domain) {
      if(this.selectedDomain === domain) return;
      this.selectedDomain = domain;
      this.$emit('select-domain',this.selectedDomain);
    }
  }
};
</script>
