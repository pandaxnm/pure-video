<template>
    <div>
        <form action="/">
            <van-search
                    v-model="keyword"
                    placeholder="请输入搜索关键词"
                    show-action
                    @search="onSearch"
                    @cancel="onCancel"
                    @focus="showList = false"
            />
        </form>
        <!--热门搜索-->
        <div v-if="!showList">
            <div v-if="hotList.length > 0">
                <van-cell-group title="热门搜索">
                    <van-cell clickable :to="'/detail?id='+item.id+'&from=search'" v-for="(item, index) in hotList" :key="index">
                        <template slot="title">
                            <van-tag type="danger">{{index+1}}</van-tag>
                            <span style="margin-left: .5rem">{{item.title}}</span>
                        </template>
                    </van-cell>
                </van-cell-group>
            </div>
        </div>
        <!--搜索结果-->
        <div v-else>
            <my-loading v-if="isLoading"></my-loading>
            <div v-else class="search-container">
                <!--搜索结果为空-->
                <div v-if="emptyData" class="empty">
                    <span>暂无数据</span>
                </div>
                <!--搜索结果列表-->
                <div id="list" v-if="videos.length > 0">
                    <div class="res">
                        <span>共搜索到{{totalCount}}个结果</span>
                    </div>
                    <van-list
                            v-model="isLoadingMore"
                            :finished="noMoreData"
                            finished-text="没有更多了"
                            @load="changePage"
                            :offset="50"
                    >
                        <movie-card v-for="(video, index) in videos" :video="video" :key="index" from="search">
                        </movie-card>
                    </van-list>
                </div>
            </div>
        </div>

    </div>
</template>

<script>
    import MyLoading from '../components/Loading';
    import MovieCard from "../components/MovieCard";

    export default {
        name: "Search",
        components: {
            MyLoading,
            MovieCard,
        },
        mounted() {
            this.setHeader();
            this.getHotSearch();
        },
        updated() {
            this.setHeader();
        },
        activated() {
            this.setHeader();
        },
        data() {
            return {
                keyword: '',
                isLoading: false,
                isLoadingMore: false,
                isLoadingHot: false,
                currentPage: 1,
                totalPage: 0,
                totalCount: 0,
                videos: [],
                emptyData: false,
                noMoreData: false,
                showList: false,
                hotList: [],
            }
        },
        methods: {
            getHotSearch() {
                this.showList = false;
                this.isLoadingHot = true;
                this.$get(this.API.hot, {})
                    .then((res) => {
                        this.isLoadingHot = false;
                        if(res.retCode === 0){
                            this.hotList = res.data.list;
                        }
                    })
            },
            onSearch() {
                this.isLoading = true;
                this.emptyData = false;
                this.noMoreData = false;
                this.showList = true;
                this.videos = [];
                this.$get(this.API.search, {keyword: this.keyword}, this.currentPage)
                    .then((res) => {
                        this.isLoading = false;
                        if(res.retCode === 0){
                            if(res.data.list.length === 0){
                                this.emptyData = true;
                                return;
                            }
                            this.videos = res.data.list;
                            this.totalPage = res.data.totalPage;
                            this.totalCount = res.data.totalCount;
                        }
                    })
            },
            //加载更多
            changePage() {
                this.currentPage += 1;
                this.isLoadingMore = true;
                this.$get(this.API.search, {keyword: this.keyword}, this.currentPage)
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
            onCancel() {
                this.showList = false;
                this.$router.replace({name: 'Home'})
            },
            setHeader() {
                this.$store.commit('showHeader');
                this.$store.commit('hideBack');
                this.$store.commit('setTitle','搜索');
                this.$store.commit('setLeftText','');
                this.$store.commit('setRightText','');
                this.$store.commit('setClickRight','');
            },
        }
    }
</script>

<style scoped>
    .search-container {
        background-color: #f5f5f5;
    }
    .more {
        margin: 1rem 0 3rem 0;
        text-align: center;
    }
    .empty {
        margin-top: 3rem;
        text-align: center;
    }
    .res {
        margin-top: .3rem;
        text-align: center;
    }
    .empty span, .res span{
        color: #666;
        font-size: .8rem;
    }

</style>