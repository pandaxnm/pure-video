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
              v-if="isShowHeader"
              :showBack="isShowBack"
              :title="getTitle"
              :leftText="getLeftText"
              :rightText="getRightText"
              :clickLeft="getClickLeft"
              :clickRight="getClickRight"
              style="display: block"
      />

      <div id="main">
        <keep-alive>
          <router-view v-if="this.$route.meta.keepAlive"></router-view>
        </keep-alive>
        <router-view v-if="!this.$route.meta.keepAlive"></router-view>

        <back-to-top bottom="50px" right="20px">
          <van-button size="mini" round plain type="danger" style="width: 2rem">
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
            }
        },
        mounted() {
            this.currentUrl = window.location.href;
            if(common.isPc()){
                this.isPc = true;
            }
            this.getCategories();
        },
        methods: {
            getCategories() {
                this.$get(this.API.categories, {})
                    .then((res) => {
                        if(res.retCode === 0){
                            common.setCategories(res.data)
                        }
                    })
            }
        },
        computed: {
            isShowHeader(){
                return this.$store.getters.isShowHeader;
            },
            isShowFooter(){
                return this.$store.getters.isShowFooter;
            },
            isShowBack(){
                return this.$store.getters.isShowBack;
            },
            getLeftText(){
                return this.$store.getters.getLeftText;
            },
            getRightText(){
                return this.$store.getters.getRightText;
            },
            getTitle(){
                return this.$store.getters.getTitle;
            },
            getClickLeft(){
                return this.$store.getters.getClickLeft;
            },
            getClickRight(){
                return this.$store.getters.getClickRight;
            },
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
