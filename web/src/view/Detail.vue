<template>
    <div id="detail">
        <my-error v-if="error" :errorMsg="errorMsg" :tryAgain="getDetail"></my-error>
        <my-loading v-else-if="isLoading"></my-loading>
        <div v-else id="detail-list">
            <movie-detail-card :video="detail"></movie-detail-card>
            <div class="list">
                <div v-if="detail.type === 'movie'">
                    <van-button block size="small" type="primary" class="list-btn" @click="toPlay(detail.id, list[0].list_num)">播放</van-button>
                </div>
                <div v-else>
                    <van-tabs v-model="active">
                        <van-tab title="剧集">
                            <div class="list-container">
                                <div v-for="(item, index) in list" class="list-row" :key="index">
                                    <van-button size="small" plain type="primary" v-if="item.list_num == lastWatchNum" class="list-btn" @click="toPlay(detail.id, item.list_num)">{{item.list_num}}</van-button>
                                    <van-button size="small" v-else class="list-btn" @click="toPlay(item.video_id,item.list_num)">{{item.list_num}}</van-button>
                                </div>
                            </div>
                        </van-tab>
                        <van-tab title="简介">
                            <div class="desc">{{detail.desc}}</div>
                        </van-tab>
                    </van-tabs>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import MovieDetailCard from "../components/MovieDetailCard";
    import MyLoading from '../components/Loading';
    import MyError from '../components/Error';
    import Common from '../utils/common'


    export default {
        name: "Detail",
        components: {
            MovieDetailCard,
            MyLoading,
            MyError,
        },
        created() {
            this.setHeader();
        },
        updated() {
            this.setHeader();
        },
        mounted() {
            // this.setHeader();
            this.getDetail();
            //获取上次播放的集数
            this.lastWatchNum = Common.getLastWatchNum(this.$route.query.id)
        },
        data() {
            return {
                active: 0,
                isLoading: false,
                error: false,
                errorMsg: '',
                detail: [],
                list: [],
                lastWatchNum: 0,
            }
        },
        methods: {
            //跳转播放页
            toPlay(id, list_num) {
                //设置本次点击的集数
                Common.setWatchNum(id, list_num);
                this.$router.push({name:'Video', query: {id: id, list_num: list_num}})
            },
            //获取影片详情
            getDetail() {
                this.isLoading = true;
                this.error = false;
                this.$get(this.API.detail, { id: this.$route.query.id, from: this.$route.query.from ? this.$route.query.from : ''})
                    .then((res) => {
                        this.isLoading = false;
                        if(res.retCode === 0){
                            this.detail = res.data.detail;
                            this.list = res.data.list;
                        }
                    }).catch((e) => {
                        this.error = true;
                        this.errorMsg = res.retMsg;
                })
            },
            setHeader() {
                this.$store.commit('showHeader');
                this.$store.commit('showBack');
                this.$store.commit('setTitle','影片详情');
                this.$store.commit('setLeftText','');
                this.$store.commit('setRightText','');
                this.$store.commit('setClickLeft',() => {this.$router.go(-1)});
            },
        }
    }
</script>

<style scoped>
    .list {
        border-radius: .3rem;
        margin: .5rem .3rem;
        background-color: #fff;
        padding: 1rem;
    }
    .list-container {
        margin-top: .5rem;
        display: flex;
        flex-direction: row;
        flex-wrap: wrap;
        justify-content: space-between;
    }
    .list-row {
        text-align: center;
    }
    .list-btn {
        margin-top: .5rem;
    }
    .desc {
        font-size: .8rem;
        margin: 1rem 0;
        margin-bottom: 0;
        color: #666;
        line-height: 1.5rem;
    }
</style>