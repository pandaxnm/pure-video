<template>
    <div class="error">
        <p>请求出错 <span v-if="errorMsg.length > 0">: {{errorMsg}}</span></p>
        <van-button type="primary" @click="tryAgain">重试</van-button>
    </div>
</template>

<script>
    export default {
        props: ['errorMsg', 'tryAgain', 'handleTry'],
        name: "Error",
    }
</script>

<style scoped>

</style>