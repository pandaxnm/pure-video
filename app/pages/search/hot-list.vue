<template>
	<view class="hot" v-if="list.length > 0">
		<text class="hot-text">热门搜索</text>
		<view class="hot-list">
			<view
				class="hot-list-item"
				v-for="(item, index) in list"
				:key="index"
				@click="navToDetails(item)"
			>
				<text class="rank">{{index+1}}</text>
				<text class="title">{{item.title}}</text>
			</view>
		</view>
	</view>
</template>

<script>
	
	export default {
		props: {
			list: {
				type: Array,
				default() {
					return []
				}
			},
		},
		methods: {
			navToDetails(item){
				uni.hideKeyboard();//关闭软键盘
				let data = {
					id: item.id,
					title: item.title,
					from: 'list',
				}
			
				uni.navigateTo({
					url: `/pages/details/videoDetails?data=${JSON.stringify(data)}`
				})
			},
		}
	}
</script>

<style lang="scss">
	.hot {
		.hot-list {
			/* margin: 10upx 0; */
		}
		.hot-text {
			color: #666;
			padding: 10upx;
			font-size: 26upx;
			margin-left: 20upx;
		}
		.hot-list-item {
			padding: 20upx 0;
			margin-left: 30upx;
			border-bottom: 1px #EEEEEE solid;
			font-size: 28upx;
		}
		.rank {
			color: #ec706b;
			font-size:26upx;
		}
		.title {
			color: #666;
			font-size: 28upx;
			margin-left: 32upx;
		}
	}
</style>
