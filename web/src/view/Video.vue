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
<!--                    <span v-if="detail.type">{{$route.query.list_num}}</span>-->
<!--                    <span v-if="lastWatchNum"> - {{list[currentLine][lastWatchNum-1]['list_num']}}</span>-->
                </div>
                <div id="lines">
                    <van-tabs v-if="list.length > 1" style="margin-top:.5rem;margin-bottom: 1rem"  swipeable v-model="currentLine" >
                        <van-tab v-for="(it, index) in list" :title="`播放源`+(index+1)" :key="index">
                            <div class="list-container">
                                <div v-for="(item, index2) in list[currentLine]" class="list-row" :key="index2">
                                    <van-button size="small" plain type="primary" v-if="parseInt(index2+1) === parseInt(lastWatchNum)" class="list-btn" @click="changeListNum(index2+1,item.play_url)">{{item.list_num}}</van-button>
                                    <van-button size="small" v-else class="list-btn" @click="changeListNum(index2+1,item.play_url)">{{item.list_num}}</van-button>
                                </div>
                            </div>
                        </van-tab>
                    </van-tabs>
                    <div v-else>
                        <div class="list-container">
                            <div v-for="(item, index2) in list[currentLine]" class="list-row" :key="index2">
                                <van-button size="small" plain type="primary" v-if="parseInt(index2+1) === parseInt(lastWatchNum)" class="list-btn" @click="toPlay(detail.id, index2+1)">{{item.list_num}}</van-button>
                                <van-button size="small" v-else class="list-btn" @click="changeListNum(index2+1,item.play_url)">{{item.list_num}}</van-button>
                            </div>
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
            this.setHeader();
            this.player = this.$refs.player.dp;
            this.videoId = this.$route.query.id;
            this.lastWatchNum = Common.getLastWatchNum(this.$route.query.id)
            this.getPlayInfo();
        },
        updated() {
            this.setHeader();
        },
        activated() {
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
                this.$get(this.API.detail, { id: this.$route.query.id, from: ''})
                    .then((res) => {
                        this.isLoading = false;
                        if(res.retCode === 0){
                            this.detail = res.data.detail;
                            this.list = res.data.list;
                            this.changeLine()
                        }
                    }).catch((e) => {
                        this.error = true;
                        this.errorMsg = e.toString();
                })
            },
            //更换线路
            changeLine(index = 0) {
                this.currentLine = index;
                let url = this.list[index][0].play_url;
                this.player.switchVideo({
                    url: url,
                });
                this.player.play();
            },
            //更换剧集
            changeListNum(list_num, play_url) {
                this.lastWatchNum = list_num;
                Common.setWatchNum(this.videoId,list_num);
                // this.$router.replace({name: 'Video',query: {id:this.videoId, list_num: list_num}});
                // this.changeLine(play_url)
                this.player.switchVideo({
                    url: play_url,
                });
                this.player.play();
            },
            setHeader() {
                let data = {
                    title: '视频播放',
                    showBack: true,
                    leftText: '',
                    clickLeft: () => {this.$router.go(-1)},
                    rightText: '',
                    clickRight: '',
                }
                this.$emit('changeHeader', data)
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
