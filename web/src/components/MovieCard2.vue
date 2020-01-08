<template>
    <div>
        <van-card
                :thumb="video.poster"
                class="card"
                tag=""
                @click="toVideoDetail"
                @click-thumb="toVideoDetail"

        >
            <div slot="thumb">
                <!--<img class="thumb" v-lazy="video.poster_url ? API.domain + video.poster_url : video.poster"/>-->
                <img class="thumb" v-lazy="video.poster"/>
            </div>
            <div slot="title">
                <p class="title">{{video.title}}</p>
            </div>
            <div slot="desc">
                <p class="desc">
                    {{video.area ? video.area + ' / ' : ''}}
                    {{video.category ? video.category + ' / ' : ''}}
                    {{video.year > 0  ? video.year + ' / ' : ''}}
                </p>
                <p class="desc">导演：{{video.director ? video.director : '未知'}}</p>
                <p class="desc">主演：{{video.actors ? video.actors : '未知'}}</p>
            </div>
        </van-card>
    </div>

</template>

<script>
    export default {
        name: "MovieCard2",
        props: ["video","from"],
        data() {
            return {
                id: 0,
            }
        },
        created() {
            this.id = this.video.id
        },
        methods: {
            toVideoDetail() {
                this.$router.push({name: 'Detail', query: {id: this.id,from: this.from ? this.from : ''}})
            },
        }
    }
</script>

<style scoped>
    .card {
        border-radius: .3rem;
        margin: .5rem .3rem;
        background-color: #fff;
        min-height: 7rem;
    }
    .thumb {
        width: 4rem;
        height: 6rem;
        margin-left: .5rem;
        margin-top: .25rem;
    }
    .title {
        margin-top: .2rem;
        margin-bottom: .5rem;
        font-size: .9rem;
        max-height: 2rem;
        font-weight: bold;
        line-height: 1rem;
        overflow: hidden;
        white-space: nowrap;
        text-overflow: ellipsis;
    }
    .desc {
        margin: .3rem 0;
        margin-bottom: 0;
        color: #666;
        overflow: hidden;
        white-space: nowrap;
        text-overflow: ellipsis;
    }
    .list-count span{
        padding-right: .6em;
        border-radius: 0 .8em .8em 0;
    }
</style>