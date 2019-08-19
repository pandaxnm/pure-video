<template>
    <view class="error">
		<text class="error-text">请求出错<text class="error-info" v-if="errorMsg.length > 0">: {{errorMsg}}</text></text>
		<button class="btn" type="default" @click="tryAgain">重试</button>
    </view>
</template>

<script>
    export default {
        
        name: "Error",
		data() {
			return {
				errorMsg: '',
				tryAgain: '',
			}
		},
		methods: {
			setError(errorMsg, tryAgain) {
				this.errorMsg = errorMsg;
				this.tryAgain = tryAgain;
			}
		}
	
    }
</script>

<style lang='scss'>
.error {
	display: flex;
	justify-content: center;
	flex-direction: column;
	text-align: center;
	padding-top: 300upx;
	
	.error-text {
		font-size: 35upx;
		color:#666;
	}
	
	.btn {
		margin-top: 60upx;
	}
}
</style>