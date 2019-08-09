import Vue from 'vue';
import Vuex from 'vuex';
Vue.use(Vuex);

const state = {
    show_header: true,
    show_back: true,
    left_text: '',
    right_text: '',
    header_title: 'test',
    click_left: '',
    click_right: '',
};

const getters = {
    isShowHeader(state) {
        return state.show_header
    },
    isShowFooter() {
        return state.show_footer
    },
    isShowBack(){
        return state.show_back
    },
    getLeftText(state){
        return state.left_text
    },
    getRightText(state){
        return state.right_text
    },
    getTitle(state){
        return state.header_title;
    },
    getClickLeft() {
        return state.click_left;
    },
    getClickRight() {
        return state.click_right;
    }
};

const mutations = {
    showHeader(state) {
        state.show_header = true;
    },
    hideHeader(state) {
        state.show_header = false;
    },
    showBack(state) {
        state.show_back = true;
    },
    hideBack(state) {
        state.show_back = false;
    },
    setLeftText(state, value) {
        state.left_text = value;
    },
    setRightText(state, value) {
        state.right_text = value;
    },
    setTitle(state,title){
        state.header_title = title;
    },
    setClickLeft(state, action){
        state.click_left = action;
    },
    setClickRight(state, action) {
        state.click_right = action;
    }
};


const actions = {

};


const store = new Vuex.Store({
    state,
    getters,
    mutations,
    actions,
});

export default store;