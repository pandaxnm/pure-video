<template>
    <div>
        <van-card
                :thumb="video.poster"
                class="card"
                :tag="video.type === 'tv' ? '第'+video.current_list_count+'集' : ''"
        >
            <div slot="thumb">
                <img class="thumb" v-lazy="video.poster_url ? API.domain + video.poster_url : video.poster"/>
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
                <van-tag plain type="danger" v-if="video.note" class="note">{{video.note}}</van-tag>
            </div>
        </van-card>
    </div>

</template>

<script>
    export default {
        name: "MovieCard",
        props: ["video"],
    }
</script>

<style scoped>
    .card {
        border-radius: .3rem;
        margin: .5rem .3rem;
        background-color: #fff;
        min-height: 10rem;
    }
    .thumb {
        width: 4.5rem;
        height: 6rem;
        margin-left: .5rem;
        margin-top: .25rem;
    }
    .title {
        font-size: 1rem;
        margin-top: .2rem;
        margin-bottom: .2rem;
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
        /*overflow: hidden;*/
        /*white-space: nowrap;*/
        /*text-overflow: ellipsis;*/
    }
    .note {
        margin: .7rem 0 .5rem 0;
    }
</style>