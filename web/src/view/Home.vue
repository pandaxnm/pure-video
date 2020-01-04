<template>
    <div>
        <my-error v-if="error" :errorMsg="errorMsg" :tryAgain="getCategories"></my-error>
        <my-loading v-else-if="isLoading"></my-loading>
        <div v-else>
            <van-swipe v-if="banners.length > 0" :autoplay="5000" indicator-color="white">
                <van-swipe-item v-for="item in banners">
                    <img :src="item.url" style="width: 100%;height:150px;object-fit: cover;" @click="toVideoDetail(item.video_id)"/>
                </van-swipe-item>
            </van-swipe>
            <van-tabs  @click="getVideos" swipeable v-model="tabBarIndex" animated>
                <van-tab v-for="(category, index) in categories" :title="category.name" :key="index">
                    <my-error v-if="category.error" :errorMsg="category.errorMsg" :tryAgain="getVideos"></my-error>
                    <my-loading v-else-if="category.isLoading"></my-loading>
                    <div v-else-if="category.emptyData" class="empty">
                        <span>暂无数据</span>
                        <van-button @click="getVideos" size="small">刷新</van-button>
                    </div>
                    <div v-else class="home-container">
                        <van-pull-refresh v-model="category.isRefreshing" @refresh="onRefresh" class="list">
                            <van-list
                                    v-model="category.isLoadingMore"
                                    :finished="category.noMoreData"
                                    finished-text="没有更多了"
                                    @load="loadMore"
                            >
                                <movie-card v-for="(video, index) in category.list" :video="video" :key="index">
                                </movie-card>
                            </van-list>
                        </van-pull-refresh>
                    </div>
                </van-tab>
            </van-tabs>
        </div>

    </div>

</template>

<script>
    import MovieCard from "../components/MovieCard";
    import MyLoading from '../components/Loading';
    import MyError from '../components/Error';
    import common from '../utils/common';
    import { Swipe, SwipeItem } from 'vant';


    export default {
        name: "Home",
        components: {
            MovieCard,
            MyLoading,
            MyError,
            'van-swipe': Swipe,
            'van-swipe-item': SwipeItem,
        },
        mounted() {
            this.init();
        },
        updated() {
            this.setHeader();
        },
        activated() {
            this.setHeader();
        },
        data() {
            return {
                tabBarIndex: 0,
                categories: [],
                category: '',
                isLoading: false,
                error: false,
                errorMsg: '',
                banners: [],
            }
        },
        methods: {
            toVideoDetail(id) {
                this.$router.push({name: 'Detail', query: {id: id}})
            },
            init() {
                this.setHeader();
                this.getCategories();
            },
            getCategories() {
                this.error = false;
                this.errorMsg = '';
                this.isLoading = true;
                this.getBanners();
                this.$get(this.API.categories, {})
                .then((res) => {
                    if(res.retCode === 0){
                        let tabList = res.data;
                        tabList.forEach(item=>{
                            item.list = [];
                            item.emptyData = false;
                            item.noMoreData = false;
                            item.isLoading = false;
                            item.isRefreshing = false;
                            item.isLoadingMore = false;
                            item.currentPage = 1;
                            item.totalPage = 0;
                            item.totalCount = 0;
                            item.error = false;
                            item.errorMsg = '';
                        })
                        this.categories = tabList;
                        this.isLoading = false;
                        this.getVideos()
                    }else{
                        this.error = true;
                        this.errorMsg = res.retMsg;
                    }
                }).catch(e => {
                    this.error = true;
                    this.errorMsg = e;
                })
            },
            //获取列表数据
            getVideos(type = 'init') {
                let tabItem = this.categories[this.tabBarIndex];
                if(type === 'init' && tabItem.list.length > 0){
                    return;
                }
                tabItem.error = false;
                tabItem.errorMsg = '';
                tabItem.emptyData = false;
                tabItem.noMoreData = false;
                if(type === 'init'){
                    tabItem.isLoading = true;
                    tabItem.currentPage = 1;
                }
                if(type === 'refresh'){
                    tabItem.isRefreshing = true;
                    tabItem.currentPage = 1;
                }
                if(type === 'add'){
                    tabItem.isLoadingMore = true;
                    tabItem.currentPage += 1;
                }

                let params = {
                    category: tabItem.name
                };
                this.$get(this.API.home, params, tabItem.currentPage)
                    .then((res) => {
                        if(res.retCode === 0){
                            if(res.data.list.length === 0){
                                if(type === 'add'){
                                    tabItem.noMoreData = true;
                                }else{
                                    tabItem.emptyData = true;
                                }
                            }
                            res.data.list.forEach(item => {
                                tabItem.list.push(item);
                            });

                            tabItem.totalPage = res.data.totalPage;
                            tabItem.totalCount = res.data.totalPage;

                            if(type === 'init'){
                                tabItem.isLoading = false;
                            }
                            if(type === 'refresh'){
                                tabItem.isRefreshing = false;
                                this.$toast({message: '刷新成功', position: 'bottom'});
                            }
                            if(type === 'add'){
                                tabItem.isLoadingMore = false;
                            }

                        }else{
                            tabItem.error = true;
                            tabItem.errorMsg = res.retMsg;
                        }
                    })
                    .catch(e => {
                      tabItem.error = true;
                      tabItem.errorMsg = e;
                })
            },
            getBanners() {
                this.$get(this.API.banner)
                    .then(res =>{
                        if(res.retCode === 0){
                            this.banners = res.data;
                        }
                    })
            },
            //下拉刷新
            onRefresh() {
                this.getVideos('refresh');
            },
            //加载更多
            loadMore() {
                this.getVideos('add')
            },
            setHeader() {
                let data = {
                    title: '最近更新',
                    showBack: false,
                    leftText: '',
                    clickLeft: '',
                    rightText: '搜索',
                    clickRight: () => { this.$router.push('/search') },
                }
                this.$emit('changeHeader', data)
                // this.$store.commit('showHeader');
                // this.$store.commit('hideBack');
                // this.$store.commit('setTitle','最近更新');
                // this.$store.commit('setLeftText','');
                // this.$store.commit('setRightText','搜索');
                // this.$store.commit('setClickRight',() => { this.$router.push('/search') });
            },
        },

    }
</script>

<style scoped>
    .home-container {
        background-color: #f5f5f5;
    }
    .list {
        margin: 0;
        padding: 0;
    }
    .more {
        margin: 1rem 0 3rem 0;
        text-align: center;
    }
    .empty {
        margin-top: 5rem;
        text-align: center;
    }
    .empty span{
        color: #666;
        font-size: .8rem;
        display: block;
    }
    .empty button {
        margin-top: 1rem;
    }
</style>