<template>
	<view class="content">
		<mixLoading class="mix-loading" v-if="loading"></mixLoading>
		<common-error v-else-if="error" ref="error" :errorMsg="errorMsg" @tryAgain="getDetail"></common-error>
		
		<view class="video-wrapper" v-if="!loading && !error">
			<video 
				id="video"
				class="video"
				:src="videoUrl" 
				controls
				objectFit="contain"
				:autoplay="false"
				:show-center-play-btn="true"
				@error="videoError"
			></video>
		</view>
		<scroll-view class="scroll" scroll-y v-show="!loading && !error">
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
						<view class="introduce"  v-show="showDesc">
							<view class="introduce-text"><text class="introduce-title">导演：</text>{{videoInfo.director ? videoInfo.director : '未知'}}</view>
							<view class="introduce-text"><text class="introduce-title">演员：</text>{{videoInfo.actors ? videoInfo.actors : '未知'}}</view>
							<view class="introduce-text"><text class="introduce-title">简介：</text>{{videoInfo.desc ? videoInfo.desc : '未知'}}</view>
							<view style="padding:10upx;display: flex;justify-content: flex-start;">
								<button @click="setRate(0.5)" size="mini">0.5</button>
								<button @click="setRate(1)" size="mini">1</button>
								<button @click="setRate(1.5)" size="mini">1.5</button>
							</view>
						</view>
					</view>
					
					
					<!-- 推荐 -->
					<view class="s-header">
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
			</view>
		</scroll-view>
		
	</view>
</template>

<script>
	import mixLoading from '@/components/mix-loading/mix-loading';
	import CommonError from '@/components/common-error/common-error.vue'
	
	export default {
		components: {
			mixLoading,
			CommonError,
		},
		data() {
			return {
				loading: false,
				detailData: {},
				newsList: [],
				videoInfo: [],
				videoList: [],
				videoUrl: '',
				lastWatchNum: '',
				showDesc: false,
				error: false,
				errorMsg: '',
				
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
				this.error = false;
				this.loading = true;
				let data =  {
					id: this.detailData.id,
					from: this.detailData.from
				}
				this.$get('/video/detail', data)
				.then((res) => {
					if(res.retCode === 0){
						this.videoInfo = res.data.detail;
						this.videoList = res.data.list;
						this.setVideoUrl();
						// setTimeout(()=>{
							this.loading = false;
						// }, 1000)
					}else{
						this.setError(res.retMsg)
					}
				}).catch(err => {
					this.setError(err)
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
			setRate(rate) {
				this.videoContext.playbackRate(rate);
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
			videoError() {
				uni.showToast({
					title: "视频加载出错",
					position:'bottom',
					duration: 3000,
				})
			},
			//错误提示
			setError(msg) {
				this.loading = false;
				this.error = true;
				this.errorMsg = msg;
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
			margin-bottom: 20upx;
			display: flex;
			justify-content: space-between;
			.shortcut-text {
				margin-bottom: 10upx;
				font-size: 26upx;
				color: #909399;
			}
		}
		.introduce{
			display: column;
			flex-direction: row;
			.introduce-text{
				font-size: 26upx;
				color: #909399;
			}
			.introduce-title{
				font-weight: bold;
			}
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
		// display: flex;
		// flex-wrap: wrap;
		// justify-content: flex-start;
		padding: 10upx 2% 30upx 2%;
		background-color: #fff;
		.list-item {
			display: inline-block;
			margin:15upx 1.6%;
			width: 30%;
			text-align: center;
		}
	}

	
</style>
