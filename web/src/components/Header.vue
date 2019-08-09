<template>
    <van-nav-bar
        :title="title"
        :left-arrow="showBack"
        :left-text="leftText"
        :right-text="rightText"
        @click-left="clickLeft"
        @click-right="clickRight"
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
        props: ["title", "leftText", "rightText", "showBack", "clickLeft", "clickRight", "showLeftArrow"],
        components: {
            'van-nav-bar': NavBar,
        },
    }
</script>
