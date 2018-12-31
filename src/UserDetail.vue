<template>
  <div class="app-content-details">
    <div v-if="!loading">Alias:
      <ul>
        <li v-for="aliasObj in aliasList" :key="aliasObj.alias">
          <div class="aliasline" v-if="!aliasObj.deleting">
            <span class="litext">{{aliasObj.alias}}</span>
            <span class="ulicon icon-delete" v-on:click="deleteAlias(aliasObj)"></span>
          </div>
          <div class="aliasline" v-if="aliasObj.deleting">
            <span class="litext deleted">deleted {{aliasObj.alias}}</span>
            <span class="ulicon icon-history" v-on:click="revertAlias(aliasObj)"></span>
          </div>
        </li>
      </ul>
      <form class="aliasform" @submit.prevent="createNewAlias(newAliasFirstPartModel, newAliasSecondPartModel)">
        <input type="text" v-model="newAliasFirstPartModel" placeholder="new alias">@
        <select v-model="newAliasSecondPartModel"></select>
        <button>save</button>
      </form>
    </div>
    <div v-if="loading" class="icon-loading" style="width: 200px; height: 200px;"></div>
  </div>
</template>

<script>
///TODO delete alias, add alias, alias list
export default {
  data: function() {
    return {
      loading: false,
      newAliasFirstPartModel: "",
      newAliasSecondPartModel:"",
      aliasList: [
        { alias: "first@domain.sk", deleting: false, deleteTimer: null },
        { alias: "second@domain.sk", deleting: false, deleteTimer: null },
        { alias: "third@domain.sk", deleting: false, deleteTimer: null }
      ]
    };
  },
  props: ["user"],
  methods: {
    deleteAlias(aliasObj) {
      aliasObj.deleting = true;
      aliasObj.deleteTimer = setTimeout(() => {
        this.aliasList = this.aliasList.filter(x => x.alias != aliasObj.alias);
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
        console.log(firstpart+"@"+secondpart, this.validateEmail(firstpart+"@"+secondpart));
    }
  }
};
</script>

<style lang="scss" scoped>
ul {
  list-style: disc;
  margin-left: 20px;
}

.floatleft {
  float: left;
}

.ulicon {
  @extend .floatleft;
  cursor: pointer;
}

.aliasline {
  @extend .floatleft;
  &:hover {
    background-color: WhiteSmoke;
  }
}

.app-content-details {
  margin-left: 10px;
}

.deleted {
  color: grey;
}

.litext {
  @extend .floatleft;
  width: 200px;
}

.aliasform {
    margin-top: 30px;
}
</style>
