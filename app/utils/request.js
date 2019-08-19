import request from '../components/request/js/index'
import config from '../config'

const dev = (process.env.NODE_ENV === 'development');

function timestamp() {
	let tmp = Date.parse( new Date() ).toString();
	return tmp.substr(0,10);
}

// 设置全局配置
request.prototype.setConfig({
	url: config.baseUrl,
    header: {
        isApp: '1'
    }
});

// 全局拦截器
request.prototype.addGlobalInterce({
    // 请求拦截器 (例如配置token)
    // return false或者不return值, 都不会发送请求
    request (config) {
		
		if(config.data === undefined){
			config.data = {};
		}
		config.data.timestamp = timestamp();//给请求带上时间戳
        dev && console.log('url:', config.url);
        dev && console.log('data:', config.data);

        return config;
        // return false;
    },

    // 响应拦截器 (例如根据状态码拦截数据)
    // return false或者不return值 则都不会返回值
    // return Promise.reject('xxxxx')，主动抛出错误
    response (res) {
        let firstCodeNum = String(res.statusCode).substr(0, 1);
        // dev && console.log('📫 is global response interceptors', res)

        // 2xx
        if (firstCodeNum === '2') {
            // do something
            // res.data.data.text = 'addGlobalInterce response'

            return res;
        }

        // 3xx
        if (firstCodeNum === '3') {
            // do something
            return res;
        }

        // 4xx or 5xx
        if (firstCodeNum === '4' || firstCodeNum === '5') {
            // do something
            console.log('is 4xx or 5xx')
            return Promise.reject('nooooo')
        }

        // 停止发送请求 request.stop()
        if (JSON.stringify(res) === '{"errMsg":"request:fail abort"}') {
            // do something
            // return Promise.reject('xxxxxxxxx');
            return false;
        }

        // return Promise.reject(res)
        return res;
    }
});

export default request;

