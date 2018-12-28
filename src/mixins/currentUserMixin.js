import serverData from './serverData'
export default {
	mixins: [
		serverData
	],
	computed: {
		currentUser() {
			return OC.getCurrentUser()
		},
		mailadminId() {
			return '@' + this.cloudId
		},
		cloudId() {
			const url = document.createElement('a')
			url.setAttribute('href', this.serverData.cloudAddress)
			return this.currentUser.uid + '@' + url.hostname
		}
	}
}