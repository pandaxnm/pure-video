const dev = 'http://dev.com';
const prod = 'http://prod.com';

export default {

    domain: process.env.NODE_ENV === 'development' ? dev : prod,
    home: '/video/index',
    detail: '/video/detail',
    playInfo: '/video/play-info',
    search: '/video/search',

}