<template>
	<view class="content">		
		<!-- 顶部选项卡 -->
		<scroll-view id="nav-bar" class="nav-bar" scroll-x scroll-with-animation :scroll-left="scrollLeft">
			<view 
				v-for="(item,index) in tabBars" :key="item.id"
				class="nav-item"
				:class="{current: index === tabCurrentIndex}"
				:id="'tab'+index"
				@click="changeTab(index)"
			>{{item.name}}</view>
		</scroll-view>
		
		<my-error v-if="error" ref="error"></my-error>
		<!-- 下拉刷新组件 -->
		<mix-pulldown-refresh v-else ref="mixPulldownRefresh" class="panel-content" :top="90" @refresh="onPulldownReresh" @setEnableScroll="setEnableScroll">
			<!-- 内容部分 -->
			<swiper 
				id="swiper"
				class="swiper-box" 
				:duration="300" 
				:current="tabCurrentIndex" 
				@change="changeTab"
			>
				<swiper-item v-for="tabItem in tabBars" :key="tabItem.id">
					<scroll-view 
						class="panel-scroll-box" 
						:scroll-y="enableScroll" 
						@scrolltolower="loadMore"
						>
						<!-- 
							* 列表 
						-->
						<view>
							<view style="padding: 10px;">
								<view  style="display:flex;flex-wrap: wrap;flex-direction: row;justify-content: space-between">
									<view v-for="(item, index) in tabItem.videoList" :key="index" style="width:31.5%" @click="navToDetails(item)">
										<view>
											<image :src="item.poster" style="width: 100%;height:320upx" lazy-load mode="aspectFill"></image>
										</view>
										<text style="color:#303133;font-size: 26upx;padding-top: 10upx;padding-bottom: 10upx;overflow: hidden;">{{item.title}}</text>
									</view>
								</view>
							</view>
							
							<!-- 上滑加载更多组件 -->
							<mix-load-more :status="tabItem.loadMoreStatus"></mix-load-more>
						</view>
						
					</scroll-view>
				</swiper-item>
			</swiper>
		</mix-pulldown-refresh>
		
	</view>
</template>

<script>
	import mixPulldownRefresh from '@/components/mix-pulldown-refresh/mix-pulldown-refresh';
	import mixLoadMore from '@/components/mix-load-more/mix-load-more';
	import request from '@/utils/request';
	import Error from '@/components/error/index.vue'
	
	
	let windowWidth = 0, scrollTimer = false, tabBar;
	export default {
		components: {
			mixPulldownRefresh,
			mixLoadMore,
			'my-error': Error,
		},
		data() {
			return {
				error: false,
				errorMsg: false,
				tabCurrentIndex: 0, //当前选项卡索引
				scrollLeft: 0, //顶部选项卡左滑距离
				enableScroll: true,
				tabBars: [],
				videos: [],
				currentPage: 1,
			}
		},
		//跳转到搜索
		onNavigationBarSearchInputClicked(e) {
			uni.navigateTo({
				url: '/pages/search/index'
			});
		},
		computed: {
			
		},
		async onLoad() {
			// 获取屏幕宽度
			windowWidth = uni.getSystemInfoSync().windowWidth;
			this.loadTabbars();
		},
		onReady(){
			
		},
		methods: {
			//错误提示
			setError(msg) {
				this.error = true;
				this.$refs.error.setError(msg, () => this.loadTabbars());
			},
			
			//获取tabbar
			loadTabbars(){
				this.error = false;
				const instance = new request();
				instance.get({
					url: '/video/categories', 
					data: {},
					})
				.then((res) => {
					if(res.data.retCode === 0){
						let tabList = res.data.data;
						tabList.forEach(item=>{
							item.videoList = [];
							item.loadMoreStatus = 0;  //加载更多 0加载前，1加载中，2没有更多了
							item.refreshing = 0;
							item.page = 0;
							item.totalPage = 0;
							item.totalCount = 0;
						})
						this.tabBars = tabList;
						this.getVideoList('add')
					}else{
						this.setError(res.data.retMsg)
					}
				}).catch(err => {
                    this.setError(err)
                })		
			},
			
			//获取影片列表
			getVideoList(type){
				this.error = false;
				let tabItem = this.tabBars[this.tabCurrentIndex];
				//type add 加载更多 refresh下拉刷新
				if(type === 'add'){
					if(tabItem.loadMoreStatus === 2){
						return;
					}
					tabItem.loadMoreStatus = 1;
					tabItem.page += 1;
				}else if(type === 'refresh'){
					tabItem.refreshing = true;
					tabItem.page = 1;
				}
				let url = '/video/index?category='+tabItem.name+'&p='+tabItem.page;
				let data = {}
				
				const instance = new request();
                instance.get({
					url: url, 
					data: data,
				})
				.then((res) => {
                    if(res.data.retCode === 0){
                        if(type === 'refresh'){
                            tabItem.videoList = []; //刷新前清空数组
                        }
                        tabItem.totalPage = res.data.data.totalPage;
                        tabItem.totalCount = res.data.data.totalCount;
                        res.data.data.list.forEach(item=>{
                            tabItem.videoList.push(item);
                        })
                        //上滑加载 处理状态
                        if(type === 'add'){
                            if(res.data.data.list.length === 0 || tabItem.totalPage == 1){
                                tabItem.loadMoreStatus = 2;
                                return;
                            }else{
                                tabItem.loadMoreStatus = 0;
                            }
                        }

                        //下拉刷新 关闭刷新动画
                        if(type === 'refresh'){
                            this.$refs.mixPulldownRefresh && this.$refs.mixPulldownRefresh.endPulldownRefresh();
                            // #ifdef APP-PLUS
                            tabItem.refreshing = false;
                            // #endif
                            tabItem.loadMoreStatus = 0;
                        }
                    }else{
						this.setError(res.data.retMsg)
					
					}
				}).catch(err => {
                    this.setError(err)
                })
				
			},
			//详情
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
			
			//下拉刷新
			onPulldownReresh(){
				this.getVideoList('refresh');
			},
			//上滑加载
			loadMore(){
				this.getVideoList('add');
			},
			//设置scroll-view是否允许滚动
			setEnableScroll(enable){
				if(this.enableScroll !== enable){
					this.enableScroll = enable;
				}
			},

			//tab切换
			async changeTab(e){
				if(scrollTimer){
					//多次切换只执行最后一次
					clearTimeout(scrollTimer);
					scrollTimer = false;
				}
				let index = e;
				//e=number为点击切换，e=object为swiper滑动切换
				if(typeof e === 'object'){
					index = e.detail.current
				}
				if(typeof tabBar !== 'object'){
					tabBar = await this.getElSize("nav-bar")
				}
				//计算宽度相关
				let tabBarScrollLeft = tabBar.scrollLeft;
				let width = 0; 
				let nowWidth = 0;
				//获取可滑动总宽度
				for (let i = 0; i <= index; i++) {
					let result = await this.getElSize('tab' + i);
					width += result.width;
					if(i === index){
						nowWidth = result.width;
					}
				}
				if(typeof e === 'number'){
					//点击切换时先切换再滚动tabbar，避免同时切换视觉错位
					this.tabCurrentIndex = index; 
				}
				//延迟300ms,等待swiper动画结束再修改tabbar
				scrollTimer = setTimeout(()=>{
					if (width - nowWidth/2 > windowWidth / 2) {
						//如果当前项越过中心点，将其放在屏幕中心
						this.scrollLeft = width - nowWidth/2 - windowWidth / 2;
					}else{
						this.scrollLeft = 0;
					}
					if(typeof e === 'object'){
						this.tabCurrentIndex = index; 
					}
					this.tabCurrentIndex = index; 
					
					//第一次切换tab，动画结束后需要加载数据
					let tabItem = this.tabBars[this.tabCurrentIndex];
					if(this.tabCurrentIndex !== 0 && tabItem.loaded !== true){
						this.getVideoList('add');
						tabItem.loaded = true;
					}
				}, 300)
				
			},
			//获得元素的size
			getElSize(id) { 
				return new Promise((res, rej) => {
					let el = uni.createSelectorQuery().select('#' + id);
					el.fields({
						size: true,
						scrollOffset: true,
						rect: true
					}, (data) => {
						res(data);
					}).exec();
				});
			},
		}
	}
</script>

<style lang='scss'>
	
	page, .content{
		background-color: #f8f8f8;
		height: 100%;
		overflow: hidden;
	}

	/* 顶部tabbar */
	.nav-bar{
		position: relative;
		z-index: 10;
		height: 90upx;
		white-space: nowrap;
		box-shadow: 0 2upx 8upx rgba(0,0,0,.06);
		background-color: #fff;
		.nav-item{
			display: inline-block;
			width: 150upx;
			height: 90upx;
			text-align: center;
			line-height: 90upx;
			font-size: 30upx;
			color: #303133;
			position: relative;
			&:after{
				content: '';
				width: 0;
				height: 0;
				border-bottom: 4upx solid #ec706b;
				position: absolute;
				left: 50%;
				bottom: 0;
				transform: translateX(-50%);
				transition: .3s;
			}
		}
		.current{
			color: #ec706b;
			&:after{
				width: 50%;
			}
		}
	}

	.swiper-box{
		height: 100%;
	}

	.panel-scroll-box{
		height: 100%;
		
		.panel-item{
			background: #fff;
			padding: 30px 0;
			border-bottom: 2px solid #000;
		}
	}
	
	/**
	 * 新闻列表 直接拿的nvue样式修改，,
	 * 一共需要修改不到10行代码, 另外px需要直接修改为upx，只有单位不一样，计算都是一样的
	 * 吐槽：难道不能在编译的时候把nuve中的upx转为px? 这样就不用修改单位了
	 */
	.video-wrapper{
		width: 100%;
		height: 440upx;
		.video{
			width: 100%;
		}
	}
	
	view{
		display:flex;
		flex-direction: column;
	}
	.img{
		width: 100%;
		height: 100%;
	}
	.news-item{
		position:relative;
	}
	/* 修改结束 */
	
	/* 新闻列表  emmm 仅供参考 */
	.news-item{
		width: 200upx;
		padding: 24upx 30upx;
		border-bottom-width: 1px;
		border-color: #eee;
		background-color: #fff;
	}
	.title{
		font-size: 32upx;
		color: #303133;
		line-height: 46upx;
	}
	.bot{
		flex-direction: row;
	}
	.author{
		font-size: 26upx;
		color: #aaa;
	}
	.time{
		font-size: 26upx;
		color: #aaa;
		margin-left: 20upx;
	}
	.img-list{
		flex-shrink: 0;
		flex-direction: row;
		background-color: #fff;
		width: 220upx;
		height: 140upx;
	}
	.img-wrapper{
		flex: 1;
		flex-direction: row;
		height: 140upx;
		position: relative;
		overflow: hidden;
	}
	.img{
		flex: 1;
	}
	.img-empty{
		height: 20upx;
	}
	
	/* 图在左 */
	.img-list1{
		position:absolute;
		left: 30upx;
		top: 24upx;
	}
	.title1{
		padding-left: 240upx; 
	}
	.bot1{
		padding-left: 240upx; 
		margin-top: 20upx;
	}
	/* 图在右 */
	.img-list2{
		position:absolute;
		right: 30upx;
		top: 24upx;
	}
	.title2{
		padding-right: 210upx; 
	}
	.bot2{
		margin-top: 20upx;
	}
	/* 底部3图 */
	.img-list3{
		width: 700upx;
		margin: 16upx 0upx;
	}
	.img-wrapper3{
		margin-right: 4upx;
	}
	/* 底部大图 */
	.img-list-single{
		width: 690upx;
		height: 240upx;
		margin: 16upx 0upx;
	}
	.img-wrapper-single{
		height: 240upx;
		margin-right: 0upx;
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
	}
	.video-tip-icon{
		width: 60upx;
		height:60upx; 
	}
</style>
