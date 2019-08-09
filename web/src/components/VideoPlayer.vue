<template>
    <div class="hello">
        <d-player ref="player" @play="play" :video="video" :contextmenu="contextmenu"></d-player>
    </div>
</template>

<script>
    import VueDPlayer from '../components/VueDPlayerHls';

    export default {
        name: 'VueVideoPlayer',
        props: ['videoInfo'],
        mounted() {
            this.player = this.$refs.player.dp;
        },
        data () {
            return {
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
        components: {
            'd-player': VueDPlayer
        },
        methods: {
            play() {
                console.log('play callback')
            }
        },

    }
</script>

<style scoped>
</style>
