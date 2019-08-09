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
            }
        },
        {
            path: '/search',
            name: 'Search',
            component: Search,
            meta: {
                keepAlive: true,
            }
        },
        {
            path: '/detail',
            name: 'Detail',
            component: Detail,
            meta: {
                keepAlive: false,
            }
        },
        {
            path: '/video',
            name: 'Video',
            component: Video,
            meta: {
                keepAlive: false,
            }
        },
    ]
})

router.beforeEach((to, from, next) => {
    const title = to.meta && to.meta.title;
    if (title) {
        document.title = 'Pure Video - ' + title;
    }

    next();
});

export default router;