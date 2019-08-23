<template>
	<view class="to-top">
		<image src="../../static/top.png" class="img"></image>
	</view>
</template>

<script>
	export default {
		methods: {
			toTop() {
				console.log('click to-top')
				this.$emit('toTop')
			}
		}
	}
</script>

<style>
	.to-top {
		position: fixed;
		bottom:20upx;
		right:20upx;
		width:60upx;
		height:60upx;
		z-index:99;
		padding:15upx;
		background: #fff;
		border: 1upx solid #ddd;
		border-radius: 30upx;
		opacity: 0.9;
	}
	.img {
		opacity: 1;
		width: 100%;
		height:100%;
	}
</style>
