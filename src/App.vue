<template>
	<div>
		<div id="app">
			<div id="app-navigation">
				<div class="app-navigation-new">
					<button type="button" class="icon-add" v-on:click="openNewDomain()">
						Add domain
					</button>
				</div>
				<ul>
					<li v-bind:class="{ editing: addingDomain }">
						<a href="#" v-if="addingDomain"></a>
						<div class="app-navigation-entry-edit">
							<form v-bind:class="{ 'icon-loading-small': domainCreatingWait}" @submit.prevent="createNewDomain">
								<fieldset style="display: contents" v-bind:disabled="domainCreatingWait">
									<input type="text" v-model="newDomainModel" placeholder="domain name" autofocus>
									<input type="button" value="" class="icon-close" v-on:click="closeNewDomain()">
									<input type="submit" value="" class="icon-checkmark">
								</fieldset>
							</form>
						</div>
					</li>
					<li v-for="domain in domainlist" :key="domain.id" v-bind:class="{deleted:domain.deleting}">
						<a v-bind:class="{hidden:domain.deleting, active: domain.id==selectedDomainId}" href="#" v-on:click="activateDomain(domain)">{{domain.title}}</a>
						<div class="app-navigation-entry-utils">
							<ul>
								<li class="app-navigation-entry-utils-menu-button svg" v-on:click="domainMenuId=domain.id"><button></button></li>
							</ul>
						</div>

						<div class="app-navigation-entry-menu" v-bind:class="{open: domainMenuId===domain.id}">
							<ul>
								<li>
									<a href="#" v-on:click="deleteDomain(domain)">
										<span class="icon-delete"></span>
										<span>Remove</span>
									</a>
								</li>
							</ul>
						</div>
						 <div class="app-navigation-entry-deleted">
							<div class="app-navigation-entry-deleted-description">Deleted {{domain.title}}</div>
							<button class="app-navigation-entry-deleted-button icon-history" title="Undo" v-on:click="deleteDomainCancel(domain)"></button>
						</div>
					</li>
				</ul>
				<div id="app-settings">
					<div id="app-settings-header">
						<button class="settings-button"
								data-apps-slide-toggle="#app-settings-content"
						></button>
					</div>
					<div id="app-settings-content">
						<!-- Your settings in here -->
					</div>
				</div>
			</div>

			<div id="app-content">
				<div id="app-content-wrapper" v-if="existsActiveDomain">
					<div class="app-content-list">
						<a class="app-content-list-item" v-bind:class="{active: 'settings'==selectedUserId}" href="#" v-on:click="activateUser({id:'settings'})">
							<div class="app-content-list-item-icon contact__icon icon-category-tools"></div>
							<div class="app-content-list-item-line-one">Nastavenie domeny</div>
						</a>
						<a class="app-content-list-item" v-bind:class="{active: '1'==selectedUserId}" href="#" v-on:click="activateUser({id:'1'})">
							<div class="app-content-list-item-icon contact__icon icon-password" title="User from nextcloud group"></div>
							<div class="app-content-list-item-line-one">group user</div>
						</a>
						<a class="app-content-list-item" v-bind:class="{active: '2'==selectedUserId}" href="#" v-on:click="activateUser({id:'2'})">
							<div class="app-content-list-item-icon contact__icon icon-user" title="Own mail user"></div>
							<div class="app-content-list-item-line-one">own user</div>
						</a>
					</div>
					<div class="app-content-details" v-if="'settings'==selectedUserId">
						<select multiple style="min-width: 200px; width: 100%; min-height: 500px; margin-left: 20px;" v-model="domainGroupsSelected">
							<option v-for="domaingroup in domainGroups" v-bind:key="domaingroup.gid" v-bind:value="domaingroup.gid">{{domaingroup.gid}}</option>
						</select>
						<button style="float: right" v-on:click="saveDomainGroupChange()">save</button>
					</div>
					<div class="aapp-content-details" v-if="'settings'!=selectedUserId">
						user detail
					</div>
				</div>
			</div>
		</div>
	</div>
</template>

<style scoped>
	
</style>

<script>
import {
	PopoverMenu,
	AppNavigation,
	Multiselect,
	Avatar
} from 'nextcloud-vue'
import axios from 'nextcloud-axios'

export default {
	name: 'App',
	components: {
		PopoverMenu,
		AppNavigation,
		Multiselect,
		Avatar,
	},
	mixins: [],
	data: function() {
		return {
			isAdmin: false,
			addingDomain: false,
			newDomainModel: "",
			domainCreatingWait: false,
			domainMenuId: null,
			domainsLoading: false,
			domainlist: [],
			selectedDomainId: null,
			selectedUserId: null,
			domainGroups: [],
			domainGroupsSelected: [],
			domainGroupsSelectedOriginal: []
		}
	},
	computed: {
		existsActiveDomain: function() {
			let activedomain = this.domainlist.filter(x=>{return x.id===this.selectedDomainId});
			return activedomain.length>0 && !activedomain[0].deleting;
		},
		existsActiveUser: function() {
			return false;
		}
	},
	watch: {
	},
	beforeMount: function() {
		// importing server data into the store
		const serverDataElmt = document.getElementById('serverData')
		if (serverDataElmt !== null) {
			this.isAdmin = JSON.parse(document.getElementById('serverData').dataset.server).isAdmin;
		}
		
		this.loadDomains();
		
	},
	created() {
		document.addEventListener('click', this.documentClick)
	},
    destroyed () {
    // important to clean up!!
		document.removeEventListener('click', this.documentClick)
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
			
			if(!this.newDomainModel || !this.newDomainModel.match(/^(?!:\/\/)([a-zA-Z0-9-_]+\.)*[a-zA-Z0-9][a-zA-Z0-9-_]+\.[a-zA-Z]{2,11}?$/igm)) {
				OC.Notification.showTemporary("invalid domain", {type: 'error', timeout: 700});
			} else {
				this.domainCreatingWait = true;
				this.createDomain(this.newDomainModel).then(()=>{
					this.domainCreatingWait = false;
					this.closeNewDomain();
					this.loadDomains();
				}).catch((e)=>{
					console.error(e)
					this.domainCreatingWait = false;
					OC.Notification.showTemporary("error creating domain");
				});
			}
			
		},

		loadDomains() {
			this.domainsLoading = true
			return axios.get(OC.generateUrl('/apps/mailadmin/domains')).then((response) => {
				this.domainsLoading = false
				if (response.data.status === -1) {
					throw new FollowException()
				}
				this.domainlist = response.data.map(x=>{x.deleting=false; x.deletetimeout=null; return x;});

			}).catch((error) => {
				this.domainsLoading = false
				OC.Notification.showTemporary(`Failed to load domains`)
				console.error(`Failed to load domains`)
			})
		},
		createDomain(domain) {
			return axios.post(OC.generateUrl('/apps/mailadmin/domains'), {title:domain, note: ''}, {headers: {}}).then((response) => {
				if (response.data.status === -1) {
					throw new FollowException()
				}
			})
		},
		deleteDomain(domain){
			domain.deleting=true;
			
			domain.deletetimeout=setTimeout(() => {
				axios.delete(OC.generateUrl('/apps/mailadmin/domains/'+domain.id), {}, {headers: {}}).then(()=>{
					this.domainlist = this.domainlist.filter(x=>{return x.id!=domain.id});
				}, ()=>{
					domain.deleting=false;
					OC.Notification.showTemporary(`Failed to delete domain`)
				});
				
			}, 7000);
		},
		activateDomain(domain){
			this.selectedDomainId=domain.id;
			this.activateUser({id:'settings'});

			let domainTitle = this.domainlist.find(x=>{return x.id===this.selectedDomainId}).title;
			axios.get(OC.generateUrl('/apps/mailadmin/users/'+domainTitle), {}, {headers: {}}).then((result)=>{
				console.log(result.data)
			}, ()=>{
				OC.Notification.showTemporary(`Failed to load users`)
			});
		},
		activateUser(user){
			this.selectedUserId=user.id;

			if(user.id=='settings') {
				let domainTitle = this.domainlist.find(x=>{return x.id===this.selectedDomainId}).title;
				Promise.all([
					axios.get(OC.generateUrl('/apps/mailadmin/groups'), {}, {headers: {}}), 
					axios.get(OC.generateUrl('/apps/mailadmin/domain_group/'+domainTitle), {}, {headers: {}})
				]).then((result)=>{
					this.domainGroups = result[0].data;
					this.domainGroupsSelected = result[1].data.map(x=>x.gid);
					this.domainGroupsSelectedOriginal = result[1].data.map(x=>x.gid);
				}, (result)=>{
					OC.Notification.showTemporary(`Failed to load nextcloud groups`)
				});
			}
		},
		saveDomainGroupChange() {
			let removed = [], added = [];
			this.domainGroupsSelectedOriginal.forEach(x=>{
				if(!this.domainGroupsSelected.includes(x)){
					removed.push(x);
				}
			});

			this.domainGroupsSelected.forEach(x=>{
				if(!this.domainGroupsSelectedOriginal.includes(x)){
					added.push(x);
				}
			});

			let promises = [];
			for (let i = 0; i < added.length; i++) {
				promises.push(axios.post(OC.generateUrl('/apps/mailadmin/domain_group'), {domain:this.domainlist.find(x=>{return x.id===this.selectedDomainId}).title, gid: added[i]}, {headers: {}}).then((response) => {
					if (response.data.status === -1) {
						throw new FollowException()
					}
				}));			
			}

			for (let i = 0; i < removed.length; i++) {
				promises.push(axios.delete(OC.generateUrl('/apps/mailadmin/domain_group/'+removed[i]+'/'+ this.domainlist.find(x=>{return x.id===this.selectedDomainId}).title), {}, {headers: {}}).then((response) => {
					if (response.data.status === -1) {
						throw new FollowException()
					}
				}));			
			}

			Promise.all(promises).then(()=>{
				this.activateDomain({id:this.selectedDomainId});
			}, ()=>{
				OC.Notification.showTemporary(`Failed to update groups`)
				this.activateDomain({id:this.selectedDomainId});
			});

		},
		deleteDomainCancel(domain) {
			domain.deleting=false;
			clearTimeout(domain.deletetimeout);
		},
		documentClick(e){
			for(var elm of e.path){
				if(elm && elm.classList && elm.classList.contains('app-navigation-entry-utils-menu-button')){
					return;
				}
			}

			this.domainMenuId = null;
		}
	}//TODO list of users, domain remove note and id, prava pre admina v kontrolery
}
</script>
