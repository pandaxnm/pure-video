<template>
    <div class="loading" style="margin-top: 20px">
        <hollow-dots-spinner
                :animation-duration="800"
                :size="50"
                color="gray"
                style="margin-top: 20px"
        />
        <p style="margin-top: 1rem;color:gray">正在加载...</p>
    </div>
</template>

<script>

    import {HollowDotsSpinner} from 'epic-spinners'

    export default {
        name: "Loading",
        components: {
            HollowDotsSpinner
        }
    }
</script>

<style scoped>
    .loading {
        display: -webkit-flex;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
    }
    .loading p {
        font-size: 0.8rem;
        color:#444444;
        margin-top: 0.35rem;
    }
</style>