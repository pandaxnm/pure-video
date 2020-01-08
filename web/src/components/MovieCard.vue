<template>
    <div style="width: 7.5rem;margin-top: 10px;">
        <div style="position: relative;">
            <img :src="video.poster" style="width: 100%;height:10rem;object-fit:cover"  @click="toVideoDetail"/>
            <span v-if="video.note" class="note">
                {{video.note}}
            </span>

        </div>
        <span style="font-size: .8rem;overflow:hidden;">{{video.title}}</span>
    </div>

</template>

<script>
    export default {
        name: "MovieCard",
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
    .note {
        text-align: right;
        display: flex;
        flex-direction: column;
        justify-content: flex-end;
        position: absolute;
        left: 0;
        bottom: 0;
        background: linear-gradient(transparent, #444);
        height: 2rem;
        width: 100%;
        overflow: hidden;
        color: #ddd;
        font-size: .7rem;
        /*padding-right: .6rem;*/
        margin-bottom: .2rem;
    }
    .list-count span{
        padding-right: .6em;
        border-radius: 0 .8em .8em 0;
    }
</style>