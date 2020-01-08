import { get, post } from '../utils/Http';


const Api =  {

    homeList(params, p = 1) {
        return get('/video/index', params, p)
    },

    hotList() {
        return get('/video/hot')
    },

    search() {
        return get('/video/search')
    },

    videoDetail() {
        return get('/video/detail')
    },

    playInfo(videoId, ListNum) {
        return get('/video/play-info', {id: videoId, list_num: ListNum})
    },

    categories() {
        return get('/video/categories')
    },



};

export default Api;

