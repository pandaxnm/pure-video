# Pure-Video

> 一个简洁(简陋)的视频网站，Vue + PHP(Yii2)，影片数据采集自网络。

声明：本项目仅供个人学习(娱乐😀)使用。

## 截图

![](http://images.mokeee.com/blog/20190809223028.jpeg)

## 安装配置
### Clone项目 

```
git clone https://github.com/pandaxnm/pure-video.git
```
### 安装前端依赖

``` bash
cd web

npm install
```

### 安装后端依赖

``` bash
cd server

composer install
```

### 后端配置

#### 数据库配置

重命名 `server/common/config/db.default.php` 为 `db.php` 并修改

``` php
return [
    //...
    'dsn' => 'mysql:host=localhost;dbname=YOUR_DATABASE_NAME',
    'username' => 'YOUR_USERNAME',
    'password' => 'YOUR_PASSWORD',
    //...
];
```

#### NGINX

将 root 指向 `server/frontend/web/` 目录

```
    #...
    
	listen 80;
	server_name xxx.com;
	index index.html index.htm index.php;

	root  /path/server/frontend/web/
	
	location / {
        index    index.html index.htm index.php;
        try_files $uri $uri/ /index.php?$args;
    }
    
    #...
```

### 前端配置

#### 域名配置

重命名 `web/src/config/index.default.js` 为 `index.js` 并修改 

``` javascript
        proxyTable: {
            '/api': {
                target: 'http://xxx.com/',//你的后端dev域名
                changeOrigin: true,
                pathRewrite: {
                    '^/api': '/'
                }
            },
        },
```

重命名 `web/src/utils/api.default.js` 为 `api.js` 并修改

``` javascript
const dev = 'http://xxx.com'; //后端测试域名
const prod = 'http://xxx.com'; //后端正式域名
```

#### 跨域（Nginx配置）

```
    location /api  {
        proxy_pass http://xxx.com/; #后端域名
    }
```

### 资源采集

#### 采集脚本

``` bash
cd server

//只获取需要更新的影片
php yii worker/get-videos

//强制更新所有影片
php yii worker/get-videos true
```

#### 图片本地化脚本

``` bash
cd server

php yii worker/get-images
```