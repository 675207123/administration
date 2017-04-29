<script>
    import injection, { trans } from '../helpers/injection';

    export default {
        beforeRouteEnter(to, from, next) {
            next(() => {
                injection.sidebar.active('setting');
            });
        },
        mounted() {
            this.$store.commit('title', trans('administration.title.template'));
        },
    };
</script>
<template></template>