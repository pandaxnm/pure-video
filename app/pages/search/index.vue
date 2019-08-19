<template>
	<view>
		<my-error v-if="error" ref="error"></my-error>
		<view v-else>
			<!--热门搜索-->
			<view v-if="hotList.length > 0 && showHot">
				<text class="hot-text">热门搜索</text>
				<view class="hot-list">
					<view
						class="hot-list-item"
						v-for="(item, index) in hotList"
						:key="index"
						@click="navToDetails(item)"
					>
						<text class="rank">{{index+1}}</text>
						<text class="title">{{item.title}}</text>
					</view>
				</view>
			</view>
			<!--搜索结果-->
			<view>
				
			</view>
		</view>
		
	</view>
</template>

<script>
import request from '@/utils/request';
import Error from '@/components/error/index.vue'

export default {
	comments: {
		'my-error': Error,
	},
	data() {
		return {
			historyList: [],
			isHistory: true,
			hotList: [],
			showHot: true,
			flng: true,
			timer: null
		};
	},
	onLoad() {
		this.getHotList();
	},
	methods: {
		//获取热门搜索
		getHotList() {
			const instance = new request();
			instance.get({
				url: '/video/hot',
			}).then((res) => {
				if(res.data.retCode === 0){
					this.hotList = res.data.data.list;
				}
			});
		},
		//搜索
		search(val) {
			const instance = new request()
			
			instance.get({
				url: '/video/search-tips',
				data: {keyword: val}
			}).then((res) => {
				if(res.data.retCode === 0){
					console.log(res)
					if(res.data.data.length === 0){
						this.tipsList = []
					}else{
						this.tipsList = res.data.data;
					}
					
				}
			});
		},
		//点击取消
		onNavigationBarButtonTap() {
			this.showHot = false;
		},
		//跳转到详情
		navToDetails(item){
			let data = {
				id: item.id,
				title: item.title,
				from: 'search'
			}
		
			uni.navigateTo({
				url: `/pages/details/videoDetails?data=${JSON.stringify(data)}`
			})
		}
	},
	
	onNavigationBarSearchInputClicked() {
		console.log(232323)
	},
	//点击软键盘搜索按键触发
	onNavigationBarSearchInputConfirmed(e) {
		let text = e.text;
		if (!text) {
			return;
		} else {
			
		}
	}
};
</script>

<style>
.hot-text {
	color: #666;
	padding: 10upx;
	font-size: 26upx;
	margin-left: 20upx;
}
.hot-list {
	/* margin: 10upx 0; */
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
	font-size: 26upx;
	margin-left: 32upx;
}
</style>
