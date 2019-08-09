<template>
    <div>
        <!--播放器-->
        <d-player ref="player" :video="video" :contextmenu="contextmenu" class="player"></d-player>

        <!--信息-->
        <div class="info">
            <my-error v-if="error" :errorMsg="errorMsg" :tryAgain="getDetail"></my-error>
            <my-loading v-else-if="isLoading"></my-loading>
            <div v-else>
                <div class="video-name-list">
                    <span>正在播放 {{detail.title}} </span>
                    <span v-if="detail.type">{{$route.query.list_num}}</span>
                </div>
                <div id="lines">
                    <div>
                        <van-tag plain>播放源</van-tag>
                    </div>
                    <van-row style="margin-top:.5rem;margin-bottom: 1rem">
                        <van-col v-for="(item, index) in lines" span="6" class="lines-row" :key="index">
                            <van-button size="small" plain type="primary" v-if="index == currentLine" class="lines-btn" @click="toPlay(detail.id, item.list_num)"><span class="source-item">{{item.xianlu}}</span></van-button>
                            <van-button size="small" v-else class="lines-btn" @click="changeLine(item.play_url, index)"><span class="source-item">{{item.xianlu}}</span></van-button>
                        </van-col>
                    </van-row>
                </div>

                <div style="margin: 1rem 0;height:0.01rem;background-color: #ddd"></div>

                <div>
                    <van-tag plain>剧集</van-tag>
                    <div class="list-container">
                        <div v-for="(item, index) in list" class="list-row" :key="index">
                            <van-button size="small" plain type="primary" v-if="item.list_num == lastWatchNum" class="list-btn" @click="toPlay(detail.id, item.list_num)">{{item.list_num}}</van-button>
                            <van-button size="small" v-else class="list-btn" @click="changeListNum(item.list_num,item.play_url)">{{item.list_num}}</van-button>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>
</template>

<script>
    import VueDPlayer from '../components/VueDPlayerHls';
    import MyLoading from '../components/Loading';
    import MyError from '../components/Error';
    import Common from '../utils/common'

    export default {
        name: 'Video',
        components: {
            'd-player': VueDPlayer,
            MyLoading,
            MyError,
        },
        mounted() {
            this.player = this.$refs.player.dp;
            this.videoId = this.$route.query.id;
            this.lastWatchNum = Common.getLastWatchNum(this.$route.query.id)
            this.setHeader();
            this.getPlayInfo();
        },
        updated() {
            this.setHeader();
        },
        destroyed() {
            //销毁播放器
            this.player.destroy();
        },
        data() {
            return {
                videoId: 0,
                isLoading: false,
                error: false,
                errorMsg: '',
                detail: [],
                list: [],
                lines: [],
                lastWatchNum: 0,
                currentLine: 0,
                // showChangeLineModal: false,

                msg: 'Welcome',
                video: {
                    url: '',
                    pic: '',
                    type: 'auto'
                },
                defaultQuality: 0,
                autoplay: true,
                player: null,
                contextmenu: [

                ]
            }
        },
        methods: {
            play() {
                console.log('play callback')
            },
            getPlayInfo() {
                this.isLoading = true;
                this.error = false;
                this.$get(this.API.playInfo, { id: this.$route.query.id, list_num: this.$route.query.list_num})
                    .then((res) => {
                        this.isLoading = false;
                        if(res.retCode === 0){
                            this.detail = res.data.info.detail;
                            this.list = res.data.info.list;
                            this.lines = res.data.lines;
                            this.changeLine()
                        }
                    }).catch((e) => {
                        this.error = true;
                        this.errorMsg = res.retMsg;
                })
            },
            //更换线路
            changeLine(url = '', index = 0) {
                console.log(url)
                if(!url){
                     url = this.lines[0].play_url;
                }
                this.player.switchVideo({
                    url: url,
                });
                this.currentLine = index;
                this.player.play();
            },
            //更换剧集
            changeListNum(list_num, play_url) {
                this.lastWatchNum = list_num;
                Common.setWatchNum(this.videoId,list_num);
                this.$router.replace({name: 'Video',query: {id:this.videoId, list_num: list_num}});
                this.changeLine(play_url)
            },
            setHeader() {
                this.$store.commit('showHeader');
                this.$store.commit('showBack');
                this.$store.commit('setTitle','视频播放-'+this.lastWatchNum);
                this.$store.commit('setLeftText','');
                this.$store.commit('setRightText','');
                this.$store.commit('setClickRight','');
            },
        }
    }


</script>

<style scoped>
    .info {
        border-radius: .3rem;
        margin: .5rem .3rem;
        background-color: #fff;
        padding: 1rem;
    }
    .lines-row {
        text-align: center;
    }
    .lines-btn {
        margin-top: .5rem;
    }
    .player {
        height: 13.2rem
    }
    .list-container {
        margin-top: .5rem;
        display: flex;
        flex-direction: row;
        flex-wrap: wrap;
        justify-content: space-between
    }
    .list-row {
        text-align: center;
    }
    .list-btn {
        margin-top: .5rem;
    }
    .video-name-list {
        color: #666;
        font-size: .8rem;
        margin-bottom: .5rem;
    }
    .source-item {
        overflow: hidden;
        white-space: nowrap;
        text-overflow: ellipsis;
    }
</style>
