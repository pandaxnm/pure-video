<template>
	<view style="padding: 10px;">
		<view class="list">
			<view class="list-item" v-for="(item, index) in list" :key="index" @click="navToDetails(item)">
				<view class="poster">
					<image :src="item.poster" class="img" lazy-load mode="aspectFill"></image>
					<view class="note" v-if="item.note.length > 1">
						<text class="note-text">{{item.note | note}}</text>
					</view>
				</view>
				<text class="title">{{item.title}}</text>
			</view>
		</view>
	</view>
</template>

<script>
	export default {
		name: 'VideoList',
		props: {
			list: {
				type: Array,
				default() {
					
					return []
				}
			},
		},
		filters: {
			note: function (value) {
				if(value.indexOf('更新')!= -1 || value.indexOf('集') != -1 || value.indexOf('期') != -1){
					return value;
				}
				var reg =/[\u4e00-\u9fa5]/g;
				return value.replace(reg, "");
			}
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
			parseNote(note) {
				
			}
		}
	}
</script>

<style lang="scss">
	.list {
		display:flex;
		flex-wrap: wrap;
		flex-direction: row;
		justify-content: space-between;
	}
	.list-item {
		width:31.5%
	}
	.poster {
		position: relative;
	}
	.img {
		width: 100%;
		max-height:320upx;
		background: #ddd;
	}
	.title {
		color:#303133;
		font-size: 26upx;
		padding-top: 10upx;
		padding-bottom: 10upx;
		overflow: hidden;
	}
	
	.note {
		text-align: right;
		display: flex;
		flex-direction: column;
		justify-content: flex-end;
		position: absolute;
		left: 0;
		bottom: 0;
		background: linear-gradient(transparent, #444);
		height: 100upx;
		width: 100%;
		overflow: hidden;
	}
	.note-text {
		color: #fff;
		font-size: 23upx;
		padding-right: 10upx;
		padding-bottom: 5upx;
	}
</style>
