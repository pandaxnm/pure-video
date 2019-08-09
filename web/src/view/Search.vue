<template>
    <div>
        <form action="/">
            <van-search
                    v-model="keyword"
                    placeholder="请输入搜索关键词"
                    show-action
                    @search="onSearch"
                    @cancel="onCancel"
            />
        </form>
        <my-loading v-if="isLoading"></my-loading>
        <div v-else class="search-container">
            <div v-if="emptyData" class="empty">
                <span>暂无数据</span>
            </div>
            <div id="list" v-if="videos.length > 0">
                <div class="res">
                    <span>共搜索到{{totalCount}}个结果</span>
                </div>
                <movie-card v-for="(video, index) in videos" :video="video" :key="index" from="search">
                </movie-card>
            </div>
            <div class="more" v-if="totalPage > 1">
                <div class="empty" v-if="noMoreData">
                    <span>没有更多了</span>
                </div>
                <van-button v-else size="small" :loading="isLoadingMore" @click="changePage" :disabled="isLoadingMore">
                    {{isLoadingMore ? '正在加载' : '加载更多'}}
                </van-button>
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
            this.setHeader()
        },
        activated() {
            this.setHeader()
        },
        data() {
            return {
                keyword: '',
                isLoading: false,
                isLoadingMore: false,
                currentPage: 1,
                totalPage: 0,
                totalCount: 0,
                videos: [],
                emptyData: false,
                noMoreData: false,
            }
        },
        methods: {
            onSearch() {
                this.isLoading = true;
                this.emptyData = false;
                this.noMoreData = false;
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