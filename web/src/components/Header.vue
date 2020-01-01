<template>
    <van-nav-bar
        :title="data.title"
        :left-arrow="data.showBack"
        :left-text="data.leftText"
        :right-text="data.rightText"
        @click-left="data.clickLeft"
        @click-right="data.clickRight"
        border
        fixed
        :z-index=100
    >
    </van-nav-bar>
</template>

<script>
    import { NavBar } from 'vant'

    export default {
        name: 'Header',
        // props: ["title", "leftText", "rightText", "showBack", "clickLeft", "clickRight", "showLeftArrow"],
        props: ["data"],
        components: {
            'van-nav-bar': NavBar,
        },
    }
</script>
