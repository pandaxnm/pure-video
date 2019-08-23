<template>
<!-- 	<view style="padding: 10px;">
		<view  style="display:flex;flex-wrap: wrap;flex-direction: row;justify-content: space-between">
			<view v-for="(item, index) in list" :key="index" style="width:31.5%" @click="navToDetails(item)">
				<view>
					<image :src="item.poster" style="width: 100%;height:320upx" lazy-load mode="aspectFill"></image>
				</view>
				<text class="title">{{item.title}}</text>
			</view>
		</view>
	</view> -->
	<scroll-view class="wrapper" :scroll-y="true">
		<view class="list" v-for="(video, index) in list" :key="index" @click="navToDetails(video)">
			<view>
				<image :src="video.poster" style="width: 180upx;height:240upx;background: #ddd;" lazy-load mode="aspectFill"></image>
			</view>
			<view style="margin-left: 40upx;">
				<view style="margin-bottom: 20upx;">
					<text class="title">{{video.title}}</text>
				</view>
				<view>
					<text class="desc">
						{{video.area ? video.area + ' / ' : ''}}{{video.category ? video.category + ' / ' : ''}}{{video.year > 0  ? video.year + ' / ' : ''}}
					</text>
				</view>
				<view>
					<text class="director">
						导演：{{video.director ? video.director : '未知'}}
					</text>
				</view>
				<view>
					<text class="note">
						{{video.note}}
					</text>
				</view>
			</view>
			
			
		</view>
	</scroll-view>
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
	.wrapper {
		padding: 10px;
		background: #f8f8f8;
	}
	.list {
		display: flex;
		margin-bottom: 10upx;
		background: #fff;
		padding:20upx;
	}
	.title {
		color:#303133;
		font-size: 28upx;
		overflow: hidden;
		font-weight: bold;
	}
	.desc {
		font-size: 26upx;
		color: #666;
	}
	.director {
		font-size: 26upx;
		color: #666;
	}
	.note {
		font-size: 26upx;
		color: #ec706b;
	}
</style>
