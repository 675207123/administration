<script>
    import { swiper, swiperSlide } from 'vue-awesome-swiper';

    export default {
        components: {
            swiper,
            swiperSlide,
        },
        props: {
            list: {
                type: Array,
                required: true,
            },
            slidesPerView: {
                type: Number,
                default: 5,
            },
            spaceBetween: {
                type: Number,
                default: 42,
            },
        },
    };
</script>
<style lang="less">

</style>
<template>
    <div class="team-wraper">
        <div class="swiper-box">
            <swiper :options="swiperOption">
                <swiper-slide v-for="(slide, index) in list"
                              :key="index"
                              :class="{'active-slide': activeIndex === index}"
                              @click.native="switchActive(index)">
                    <img :src="slide.image" alt="">
                    <p>{{ slide.name }}</p>
                </swiper-slide>
                <div class="swiper-button-prev hidden-xs" slot="button-prev"></div>
                <div class="swiper-button-next hidden-xs" slot="button-next"></div>
            </swiper>
        </div>
    </div>
</template>