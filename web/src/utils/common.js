export default class Common {

    /**
     * 判断当前设备是否是PC
     * @returns {boolean}
     */
    static isPc(){
        return !navigator.userAgent.match(/(phone|pad|pod|iPhone|iPod|ios|iPad|Android|Mobile|BlackBerry|IEMobile|MQQBrowser|JUC|Fennec|wOSBrowser|BrowserNG|WebOS|Symbian|Windows Phone)/i);
    }

    /**
     * 设置本次观看的集数
     * @param id
     * @param list_num
     */
    static setWatchNum(id, list_num) {
        window.localStorage.setItem('video_'+id+'_last_watch_num', list_num);
    }

    /**
     * 获取上次观看的集数
     * @param id
     * @returns {string | null}
     */
    static getLastWatchNum(id) {
        return window.localStorage.getItem('video_'+id+'_last_watch_num');
    }

    static setCategories(categories) {
        return window.localStorage.setItem('categories', JSON.stringify(categories));
    }

    static getCategories() {
        return JSON.parse(window.localStorage.getItem('categories'));
    }





}