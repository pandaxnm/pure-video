<template>
    <my-error v-if="error" :errorMsg="errorMsg" :tryAgain="getVideos"></my-error>
    <my-loading v-else-if="isLoading"></my-loading>
    <div v-else>
        <div v-if="emptyData" class="empty">
            <span>暂无数据</span>
            <van-button @click="getVideos" size="small">刷新</van-button>
        </div>
        <div v-else class="home-container">
            <van-pull-refresh v-model="isRefreshing" @refresh="onRefresh" class="list">
                <van-list
                        v-model="isLoadingMore"
                        :finished="noMoreData"
                        finished-text="没有更多了"
                        @load="changePage"
                        :offset="50"
                >
                <movie-card v-for="(video, index) in videos" :video="video" :key="index">
                </movie-card>
                </van-list>
            </van-pull-refresh>
        </div>

    </div>
</template>

<script>
    import MovieCard from "../components/MovieCard";
    import MyLoading from '../components/Loading';
    import MyError from '../components/Error';

    export default {
        name: "Home",
        components: {
            MovieCard,
            MyLoading,
            MyError
        },
        mounted() {
            this.setHeader();
            this.getVideos();
        },
        updated() {
            this.setHeader();
        },
        activated() {
            this.setHeader();
        },
        data() {
            return {
                videos: [],
                currentPage: 1,
                totalPage: 0,
                totalCount: 0,
                isLoading: false,
                isRefreshing: false,
                isLoadingMore: false,
                emptyData: false,
                noMoreData: false,
                error: false,
                errorMsg: '',
            }
        },
        methods: {
            //获取列表数据
            getVideos() {
                this.emptyData = false;
                this.noMoreData = false;
                if(!this.isRefreshing){
                    this.isLoading = true;
                    this.videos = [];
                }
                this.$get(this.API.home, {}, this.currentPage)
                    .then((res) => {
                        this.isLoading = false;
                        this.isRefreshing = false;
                        if(res.retCode === 0){
                            if(res.data.list.length === 0){
                                this.emptyData = true;
                            }
                            this.videos = res.data.list;
                            this.totalPage = res.data.totalPage;
                            this.totalCount = res.data.totalPage;
                        }
                    })
            },
            //下拉刷新
            onRefresh() {
                this.currentPage = 1;
                this.getVideos();
            },
            //加载更多
            changePage() {
                this.currentPage += 1;
                this.isLoadingMore = true;
                this.$get(this.API.home, {}, this.currentPage)
                    .then((res) => {
                        this.isLoadingMore = false;
                        if(res.retCode === 0){
                            if(res.data.list.length === 0){
                                this.noMoreData = true;
                                return;
                            }
                            res.data.list.map((item, index) => {
                                this.videos.push(item);
                            })
                            this.totalPage = res.data.totalPage;
                            this.totalCount = res.data.totalPage;
                        }
                    })
            },
            setHeader() {
                this.$store.commit('showHeader');
                this.$store.commit('hideBack');
                this.$store.commit('setTitle','最近更新');
                this.$store.commit('setLeftText','');
                this.$store.commit('setRightText','搜索');
                this.$store.commit('setClickRight',() => { this.$router.push('/search') });
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