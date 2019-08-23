import Vue from 'vue'
import App from './App'
import {post, get} from './utils/request';

Vue.config.productionTip = false
Vue.prototype.$post = post;
Vue.prototype.$get = get;

App.mpType = 'app'

const app = new Vue({
    ...App
})
app.$mount()
