<script>
    import { trans } from '../helpers/injection';

    export default {
        beforeRouteEnter(to, from, next) {
            next(() => {
            });
        },
        mounted() {
            this.$store.commit('title', trans('administration.title.expand'));
        },
    };
</script>
<template></template>