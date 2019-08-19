<template>
	<view class="content">
		<mixLoading class="mix-loading" v-if="loading"></mixLoading>
		<view class="video-wrapper" v-if="!loading">
			<video 
				id="video"
				class="video"
				:src="videoUrl" 
				controls
				objectFit="fill"
				:autoplay="false"
				:show-center-play-btn="true"
			></video>
		</view>
		<scroll-view class="scroll" scroll-y v-show="!loading">
			<view class="scroll-content">
				<view class="container">
					<view class="introduce-section">
						<text class="title">{{detailData.title}} - {{lastWatchNum}}</text>
						<view class="shortcut">
							<text class="shortcut-text">
								{{videoInfo.year}}・{{videoInfo.area}}・{{videoInfo.category}}
							</text>
							<text :class="showDesc ? 'yticon show-icon icon-shang':'yticon show-icon icon-xia'" @click="showDesc=!showDesc"></text>
						</view>
						<view class="introduce"  v-if="showDesc">
							<text class="introduce">{{videoInfo.desc}}</text>
						</view>
					</view>
					<!-- 推荐 -->
					<view class="s-header" >
						<text class="tit">剧集</text>
						<text class="note">{{videoInfo.note}}</text>
					</view>
					<view class="lists">
						<button 
							class="list-item"
							:style="item.list_num == lastWatchNum ? 'background-color: #ec706b;color:#fff' : ''" 
							size="mini" 
							v-for="(item, index) in videoList"
							@click="changeListNum(item.list_num,item.web_url)"
						>{{item.list_num}}</button>
					</view>
				</view>
				<!-- 加载图标 -->
			</view>
		</scroll-view>
	
	</view>
</template>

<script>
	import mixLoading from '@/components/mix-loading/mix-loading';
	import request from '@/utils/request';
	
	export default {
		components: {
			mixLoading,
		},
		data() {
			return {
				loading: true,
				detailData: {},
				newsList: [],
				videoInfo: [],
				videoList: [],
				videoUrl: '',
				lastWatchNum: '',
				showDesc: false,
			}
		},
		onLoad(options){
			this.detailData = JSON.parse(options.data);
			this.getLastWatch(this.detailData.id);
			this.videoContext = uni.createVideoContext('video')
			this.getDetail();
		},
		methods: {
			//获取视频播放信息
			getDetail() {
				const instance = new request();
				instance.get({
					url: '/video/detail',
					data: {
						id: this.detailData.id,
						from: this.detailData.from
					}
				}).then((res) => {
					if(res.data.retCode === 0){
						this.videoInfo = res.data.data.detail;
						this.videoList = res.data.data.list;
						this.setVideoUrl();
						setTimeout(()=>{
							// this.videoContext.play();
							this.loading = false;
						}, 1000)
					}
				})
			},
			//设置视频播放链接
			setVideoUrl(url = '') {
				if(!url){
					this.setLastWatch(this.videoInfo.id, this.videoList[0].list_num)
				}
				this.videoUrl = url ? url : this.videoList[0].web_url;
				this.videoContext.play();
				
			},
			//更换集数
			changeListNum(listNum, url) {
				console.log('更换集数: ' + listNum + url);
				this.setLastWatch(this.videoInfo.id, listNum)
				this.setVideoUrl(url);
			},
			//设置本次观看集数
			setLastWatch(id, listNum) {
				console.log('设置本次观看集数: ' + listNum)
				uni.setStorageSync(id + 'last_watch',listNum);
				this.lastWatchNum = listNum;
			},
			//获取上次观看集数
			getLastWatch(id) {
				this.lastWatchNum = uni.getStorageSync(id + 'last_watch');
				console.log('上次观看:' + this.lastWatchNum);
			},
		
		}
	}
</script>


<style lang="scss">
	page{
		height: 100%;
	}
	.content{
		display: flex;
		flex-direction: column;
		height: 100%;
		background: #fff;
	}
	.video-wrapper{
		height: 422upx;
		
		.video{
			width: 100%;
			height: 100%;
		}
	}
	.scroll{
		flex: 1;
		position: relative;
		background-color: #f8f8f8;
		height: 0;
	}
	.scroll-content{
		display: flex;
		flex-direction: column;
	}
	/* 简介 */
	.introduce-section{
		display: flex;
		flex-direction: column;
		padding: 20upx 30upx;
		background: #fff;
		line-height: 1.5;
		
		.title{
			font-size: 36upx;
			color: #303133;
			margin-bottom: 16upx;
		}
		.shortcut {
			margin-bottom: 32upx;
			display: flex;
			flex-direction: row;
			justify-content: space-between;
			.shortcut-text {
				font-size: 26upx;
				color: #909399;
			}
		}
		.introduce{
			display: flex;
			font-size: 26upx;
			color: #909399;
			
			.show-icon{
				font-size: 26upx;
				padding-left: 10upx;
			}
		}
	}
	.mix-loading{
		transform: translateY(140upx);
	}
	.s-header{
		padding: 20upx 30upx;
		font-size: 30upx;
		color: #303133;
		background: #fff;
		margin-top: 16upx;
		
		&:before{
			content: '';
			width: 0;
			height: 40upx;
			margin-right: 24upx;
			border-left: 6upx solid #ec706b;
		}
		.note {
			color:#ec706b;
			font-size: 26upx;
			margin-right: 10upx;
			float:right
		}
	}
	/* 剧集 */
	.lists {
		display: flex;
		flex-wrap: wrap;
		justify-content: flex-start;
		padding: 10upx;
		background-color: #fff;
		.list-item {
			width: 30%;
			margin-bottom: 10upx;
		}
	}
	/* 推荐列表 */
	.rec-section{
		background-color: #fff;
		.rec-item{
			display: flex;
			padding: 20upx 30upx;
			position: relative;
			&:after{
				content: '';
				position: absolute;
				left: 30upx;
				right: 0;
				bottom: 0;
				height: 0;
				border-bottom: 1px solid #eee;
				transform: scaleY(50%);
			}
		}
		.left{
			flex: 1;
			padding-right: 10upx;
			overflow: hidden;
			position: relative;
			.title{
				display: -webkit-box;
				-webkit-box-orient: vertical;
				-webkit-line-clamp: 2;
				overflow: hidden;
				font-size: 32upx;
				color: #303133;
				line-height: 44upx;
			}
			.bot{
				position: absolute;
				left: 0;
				bottom: 4upx;
				font-size: 26upx;
				color: #909399;
			}
			.time{
				margin-left: 20upx;
			}
		}
		.right{
			width: 220upx;
			height: 140upx;
			flex-shrink: 0;
			position: relative;
			.img{
				width: 100%;
				height: 100%;
			}
			.video-tip{
				position: absolute;
				left: 0;
				top: 0;
				display: flex;
				align-items: center;
				justify-content: center;
				width: 100%;
				height: 100%;
				background-color: rgba(0,0,0,.3);
				image{
					width: 60upx;
					height:60upx; 
				}
			}
		}
	}
	
	/* 底部 */
	.bottom{
		flex-shrink: 0;
		display: flex;
		align-items: center;
		height: 90upx;
		padding: 0 30upx;
		box-shadow: 0 -1px 3px rgba(0,0,0,.04); 
		position: relative;
		z-index: 1;
		
		.input-box{
			display: flex;
			align-items: center;
			flex: 1;
			height: 60upx;
			background: #f2f3f3;
			border-radius: 100px;
			padding-left: 30upx;
			
			.icon-huifu{
				font-size: 28upx;
				color: #909399;
			}
			.input{
				padding: 0 20upx;
				font-size: 28upx;
				color: #303133;
			}
		}
		.confirm-btn{
			font-size: 30upx;
			padding-left: 20upx;
			color: #0d9fff;
		}
	}
</style>
