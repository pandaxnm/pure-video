import Vue from 'vue'
import Router from 'vue-router'
import Home from '@/view/Home'
import Detail from '@/view/Detail'
import Video from '@/view/Video'
import Search from '@/view/Search'

Vue.use(Router)

const router =  new Router({
    routes: [
        {
            path: '/',
            name: 'Home',
            component: Home,
            meta: {
                keepAlive: true,
                title: '最近更新'
            }
        },
        {
            path: '/search',
            name: 'Search',
            component: Search,
            meta: {
                keepAlive: true,
                title: '搜索'
            }
        },
        {
            path: '/detail',
            name: 'Detail',
            component: Detail,
            meta: {
                keepAlive: false,
                title: '影片详情'
            }
        },
        {
            path: '/video',
            name: 'Video',
            component: Video,
            meta: {
                keepAlive: false,
                title: '影片播放'
            }
        },
    ]
})

router.beforeEach((to, from, next) => {
    const title = to.meta && to.meta.title;
    if (title) {
        document.title = 'Pure Video - ' + title;
    }
    if(to.name === 'Detail'){
        to.meta.keepAlive = from.name === 'Video';
    }

    next();
});

export default router;