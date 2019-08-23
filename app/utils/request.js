import { Encrypt, Decrypt } from './aes';
import axios from '@/js_sdk/gangdiedao-uni-axios'
import qs from 'qs';
import config from '../config'

function timestamp() {
    let tmp = Date.parse( new Date() ).toString();
    return tmp.substr(0,10);
}

const dev = (process.env.NODE_ENV === 'development');

const service = axios.create({
    baseURL: config.baseUrl,
    // withCredentials: true,
    timeout: 10000
});

service.interceptors.request.use(
    config => {

        if(config.method === 'post'){
            config.data.timestamp = timestamp();
            dev && console.log('未加密参数：',config.data);
            let encrypt = {encryptedData: Encrypt(config.data)};
            config.data = qs.stringify(encrypt)
        }else{
            config.params.timestamp = timestamp();
            dev && console.log('未加密参数：',config.params);
            let encrypt = {encryptedData:Encrypt(config.params)};
            if(config.params.p) {
                encrypt.p = config.params.p;
            }
            config.params = encrypt;
        }
        return config
    }
);

service.interceptors.response.use(

    response => {
		dev && console.log('请求地址：',response.config.url);
        const res = JSON.parse(Decrypt(response.data));
        dev && console.log('返回值解密：',res);
        return res
    },
    error => {
        return Promise.reject(error)
    }
);

export function get(url, data = {}, p = null) {
    if(p) {
        data.p = p
    }

    return new Promise((resolve,reject) => {
        service.get(url, {
            params: data,
        })
            .then((response) => {
                return response;
            })
            .then(response => {
                resolve(response);
            },err => {
                reject(err)
            })
    })
}


export function post(url,data = {}){
    return new Promise((resolve,reject) => {
        service.post(url, data)
            .then((response) => {
                return response;
            })
            .then(response => {
                resolve(response);
            },err => {
                reject(err)
            })
    })
}
