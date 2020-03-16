require('./bootstrap');


import Vue from 'vue';

import VueAxios from 'vue-axios';
import axios from 'axios';
Vue.use(VueAxios, axios);
import App from './App.vue';
import router from './router';
import store from './store';

window.$ = jQuery;

var base = new URL(location.href);

axios.defaults.baseURL = base.origin + '/api/';

new Vue({
    store,
    router,
    render: h => h(App),
    
}).$mount('#app')

// history.pushState(null, null, location.href);
// window.onpopstate = function () {
//     history.go(1);
// };
