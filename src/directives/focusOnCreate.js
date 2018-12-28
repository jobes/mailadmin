import Vue from 'vue'

export default {
	bind: function(el) {
		Vue.nextTick(() => {
			el.focus()
		})
	}
}