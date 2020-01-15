<template>
  <div id="app">
    <div v-if="isPc" class="notice">
      <van-icon name="info" color="rgb(242, 130, 106)" size="5rem"/>
      <p>当前网站不支持在PC上浏览，请使用移动设备访问。</p>
      <p>扫一扫访问</p>
      <vue-qr :text="currentUrl" qid="testid" backgroundColor="#f5f5f5" :dotScale="1" colorDark="#666"></vue-qr>
    </div>
    <div v-else>
      <my-header
          :data="headerData"
          style="display: block"
      />

      <div id="main">
        <keep-alive>
          <router-view v-if="this.$route.meta.keepAlive" @changeHeader="changeHeader"></router-view>
        </keep-alive>
        <router-view v-if="!this.$route.meta.keepAlive" @changeHeader="changeHeader"></router-view>

        <back-to-top bottom="2rem" right="1rem">
          <van-button size="mini" round plain type="danger" style="width: 2rem;height:2rem;">
            <van-icon name="arrow-up"></van-icon>
          </van-button>
        </back-to-top>
      </div>

    </div>
  </div>
</template>

<script>
    import common from './utils/common';
    import Header from './components/Header';
    import VueQr from 'vue-qr'

    export default {
        name: 'App',
        components: {
            'my-header': Header,
            VueQr
        },
        data() {
            return {
                isPc: false,
                currentUrl: '',
                headerData: {}
            }
        },
        mounted() {
            this.currentUrl = window.location.href;
            if(common.isPc()){
                this.isPc = true;
            }
        },
        methods: {
            changeHeader(data) {
                this.headerData = data;
            }
        },
    }
</script>

<style>
  body{
    background-color: #f5f5f5;
  }
  #main {
    margin-top: 3rem;
  }
  .notice {
    display: flex;
    display: -webkit-flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    margin-top: 5rem;
  }
  .notice p {
    font-size: 1rem;
    color: #666;
  }
  .error {
    text-align: center;
    margin-top: 5rem;
    color:#666;
    font-size: 0.8rem;
  }
  .error p {
    line-height: 2rem;
  }
  .error button{
    margin-top: 1.2rem;
  }
</style>
