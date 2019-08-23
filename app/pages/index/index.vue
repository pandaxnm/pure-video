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
		
		<common-error v-if="error" ref="error" :errorMsg="errorMsg" @tryAgain="getVideoList('refresh')"></common-error>
		<!-- 下拉刷新组件 -->
		<view v-else>
			<mix-pulldown-refresh
				ref="mixPulldownRefresh" 
				class="panel-content" 
				:top="0" 
				@refresh="onPulldownReresh"  
				@setEnableScroll="setEnableScroll"
			>
				
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
							:enable-back-to-top="true"
							:scroll-y="enableScroll" 
							@scrolltolower="loadMore"
							:scroll-top="scrollTop"
							@scroll="scroll"
							>
							<!-- 影片列表 -->
							<video-list :list="tabItem.videoList"></video-list>
							<!-- 上滑加载更多组件 -->
							<mix-load-more :status="tabItem.loadMoreStatus"></mix-load-more>
						</scroll-view>
					</swiper-item>
				</swiper>
			</mix-pulldown-refresh>
			<to-top v-if="showToTop" ref="toTop" @tap="toTop"></to-top>
		</view>
	</view>
</template>

<script>
	import mixPulldownRefresh from '@/components/mix-pulldown-refresh/mix-pulldown-refresh';
	import mixLoadMore from '@/components/mix-load-more/mix-load-more';
	import CommonError from '@/components/common-error/common-error.vue'
	import ToTop from '@/components/to-top/index.vue'
	import VideoList from './video-list.vue'
	
	
	let windowWidth = 0,windowHeight=0, scrollTimer = false, tabBar;
	export default {
		components: {
			mixPulldownRefresh,
			mixLoadMore,
			CommonError,
			VideoList,
			ToTop
		},
		data() {
			return {
				error: false,
				errorMsg: false,
				tabCurrentIndex: 0, //当前选项卡索引
				scrollLeft: 0, //顶部选项卡左滑距离
				tabBars: [],
				enableScroll: true,
				scrollTop: 0,
				old: {
					scrollTop: 0
				},
				showToTop: false,
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
			windowHeight = uni.getSystemInfoSync().windowHeight;
			this.loadTabbars();
		},
		onReady(){
			
		},
		methods: {
			scroll: function(e) {
				this.old.scrollTop = e.detail.scrollTop
				if(e.target.scrollTop > windowHeight){
					this.showToTop = true;
				}else{
					this.showToTop = false;
				}
			},
			toTop: function(e) {
				this.showToTop = false;
				// 解决view层不同步的问题
				this.scrollTop = this.old.scrollTop
				this.$nextTick(function() {
					this.scrollTop = 0
				});
			},
			//设置scroll-view是否允许滚动
			setEnableScroll(enable){
				if(this.enableScroll !== enable){
					this.enableScroll = enable;
				}
			},
			//错误提示
			setError(msg) {
				this.error = true;
				this.errorMsg = msg;
			},
			
			//获取tabbar
			loadTabbars(){
				this.error = false;
                this.$get('/video/categories')
                    .then((res) => {
					if(res.retCode === 0){
						let tabList = res.data;
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
						this.setError(res.retMsg)
					}
				}).catch(err => {
                    this.setError(err)
                })		
			},
			
			//获取影片列表
			getVideoList(type){
				this.error = false;
				uni.hideToast();
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
				let url = '/video/index';
				let data = {
					p: tabItem.page,
					category: tabItem.name
				}
				
				this.$get('/video/index', data, tabItem.page)
				.then((res) => {
					console.log(res);
                    if(res.retCode === 0){
                        if(type === 'refresh'){
                            tabItem.videoList = []; //刷新前清空数组
                        }
                        tabItem.totalPage = res.data.totalPage;
                        tabItem.totalCount = res.data.totalCount;
                        res.data.list.forEach(item=>{
                            tabItem.videoList.push(item);
                        })
                        //上滑加载 处理状态
                        if(type === 'add'){
                            if(res.data.list.length === 0 || tabItem.totalPage == 1){
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
							uni.showToast({
								title: "刷新成功",
								position:'bottom'
							})
                        }
                    }else{
						console.log('请求出错: '+res.retMsg)
						this.setError(res.retMsg)
					
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
				}, 0)
				
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
	.title {
		color:#303133;
		font-size: 26upx;
		padding-top: 10upx;
		padding-bottom: 10upx;
		overflow: hidden;
	}
	
	view{
		display:flex;
		flex-direction: column;
	}

</style>
