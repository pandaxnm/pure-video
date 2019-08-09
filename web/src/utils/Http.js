import { Encrypt, Decrypt } from './aes';
import axios from 'axios'
import qs from 'qs';
import common from '../utils/common';

function timestamp() {
    let tmp = Date.parse( new Date() ).toString();
    return tmp.substr(0,10);
}

function getLoginToken() {
    return  window.localStorage.getItem('loginToken');
}

const dev = (process.env.NODE_ENV === 'development');

const service = axios.create({
    baseURL: '/api',
    // withCredentials: true,
    timeout: 20000
});


service.interceptors.request.use(
    config => {
        config.headers['token'] = getLoginToken();

        if(config.method === 'post'){
            config.data.timestamp = timestamp();
            dev && console.log(config.data);
            let encrypt = {encryptedData: Encrypt(config.data)};
            config.data = qs.stringify(encrypt)
        }else{
            config.params.timestamp = timestamp();
            dev && console.log(config.params);
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
        const res = JSON.parse(Decrypt(response.data));
        dev && console.log(response.config.url);
        dev && console.log(res);
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
