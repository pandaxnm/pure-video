const dev = 'http://api.mokeee.com';
const prod = 'http://api.mokeee.com';

export default {

    domain: process.env.NODE_ENV === 'development' ? dev : prod,
    home: '/video/index',
    detail: '/video/detail',
    playInfo: '/video/play-info',
    search: '/video/search',

}