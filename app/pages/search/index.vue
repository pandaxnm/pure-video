<template>
	<view class="content">
		<!-- 热门搜索 -->
		<hot-list v-if="showHot" :list="hotList"></hot-list>
		
		<!-- 搜索 -->
		<view v-else>
			<!-- 搜索loading -->
			<mix-loading v-if="loading"></mix-loading>
			
			<!-- 搜索error -->
			<common-error v-else-if="error" ref="error" :errorMsg="errorMsg" @tryAgain="getSearchList(keyword)"></common-error>
			
			<mix-pulldown-refresh v-else ref="mixPulldownRefresh" class="panel-content" :top="0" @refresh="onPulldownReresh">
			<scroll-view 
				:enable-back-to-top="true" 
				class="panel-scroll-box" 
				:scroll-y="enableScroll" 
				@scrolltolower="loadMore"  
				@refresh="onPulldownReresh" 
				@setEnableScroll="setEnableScroll"
			>
				<!-- 搜索结果列表 -->
				<video-search-list :list="searchList"></video-search-list>

				<!-- 上滑加载更多组件 -->
				<mix-load-more :status="loadMoreStatus"></mix-load-more>
			</scroll-view>
			</mix-pulldown-refresh>
		</view>
	</view>
	
</template>

<script>
	import mixPulldownRefresh from '@/components/mix-pulldown-refresh/mix-pulldown-refresh';
	import mixLoadMore from '@/components/mix-load-more/mix-load-more';
	import mixLoading from '@/components/mix-loading/mix-loading.vue';
	import CommonError from '@/components/common-error/common-error.vue';
	import VideoSearchList from './video-search-list';
	import HotList from './hot-list.vue'
	
	export default {
		components: {
			mixPulldownRefresh,
			mixLoadMore,
			mixLoading,
			CommonError,
			VideoSearchList,
			HotList,
		},
		data() {
			return {
				showHot: true, //是否显示热门搜索
				hotList: [], //热门搜索列表
				error: false, //是否出错
				errorMsg: false, //错误消息
				enableScroll: true, //是否允许竖向滑动
				searchList: [], //搜索结果列表
				currentPage: 1, //搜索结果当前页码
				loadMoreStatus: 0, //加载更多状态 0-未加载 1-正在加载 2-没有更多数据了
				refreshing: false, //是否在刷新
				totalPage: 0, //搜索结果页数
				totalCount: 0, //搜索结果数量
				keyword: '', //搜索关键词
				loading: false, //是否正在搜索
			}
		},
		computed: {
			
		},
		onLoad(){
			//获取热门搜索数据
			this.getHotList();
		},
		methods: {
			setEnableScroll(enable){
				if(this.enableScroll !== enable){
					this.enableScroll = enable;
				}
			},
			//获取热门搜索
			getHotList() {
				this.$get('/api/video/hot')
				.then((res) => {
					if(res.retCode === 0){
						this.hotList = res.data.list;
					}
				});
			},
			//错误提示
			setError(msg) {
				this.error = true;
				this.errorMsg = msg;
				this.loading = false;
			},
			onPulldownReresh(){
				this.getSearchList(this.keyword);
			},
			//获取影片列表
			getSearchList(keyword, search = true){
				this.error = false;
				uni.hideToast();
				if(search){
					this.loading = true;
					this.currentPage = 1;
				}else{
					if(this.loadMoreStatus === 2){
						return;
					}
					this.currentPage += 1;
					this.loadMoreStatus = 1;
				}
				let data = {
					keyword: keyword
				}
				this.$get('/api/video/search', data, this.currentPage)
				.then((res) => {
                    if(res.retCode === 0){
                        if(search){
							console.log('search')
                            this.searchList = []; //刷新前清空数组
                        }
                        this.totalPage = res.data.totalPage;
                        this.totalCount = res.data.totalCount;
						
                        res.data.list.forEach(item=>{
                            this.searchList.push(item);
                        })
						
                        //处理加载更多结果
						if(res.data.list.length === 0 || res.data.totalPage == 1){
							this.loadMoreStatus = 2;//没有更多数据了
						}else{
							//加载更多完成
							this.loadMoreStatus = 0;
						}
						
						//toast提示
						if(search && res.data.totalCount > 0){
							uni.showToast({
								title: "共搜索到"+res.data.totalCount+"个结果",
								position:'bottom'
							})
						}
						this.loading = false;
                        
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
			
			//上滑加载
			loadMore(){
				this.getSearchList(this.keyword, false);
			},
			// onPulldownReresh() {
			// 	this.getSearchList(this.keyword, true)
			// }
		},
		//点击搜索栏按钮
		onNavigationBarButtonTap() {
			this.showHot = false;
		},
		//点击搜索框
		onNavigationBarSearchInputClicked() {
			console.log('点击了搜索框')
		},
		//点击软键盘搜索按键
		onNavigationBarSearchInputConfirmed(e) {
			let text = e.text;
			if (!text) {
				return;
			}
			uni.hideKeyboard();
			this.showHot = false;
			this.keyword = text;
			this.getSearchList(text);
		}
	}
</script>

<style lang='scss'>
	
	page, .content{
		background-color: #f8f8f8;
		height: 100%;
		overflow: hidden;
	}

	.panel-scroll-box{
		height: 100%;
		
		.panel-item{
			background: #fff;
			padding: 10px 0;
			border-bottom: 2px solid #000;
		}
	}
	.video-title {
		color:#303133;
		font-size: 26upx;
		padding-top: 10upx;
		padding-bottom: 10upx;
		overflow: hidden;
	}

</style>
