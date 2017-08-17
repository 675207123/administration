<script>
    import injection from '../helpers/injection';

    export default {
        beforeRouteEnter(to, from, next) {
            injection.loading.start();
            const params = {};
            if (to.path.indexOf('/extension') !== -1) {
                params.page = to.path.replace('/extension/', '');
                params.type = 'extension';
            } else {
                params.page = to.path.replace('/', '');
                params.type = 'module';
            }
            injection.http.post(`${window.api}/administration/configuration`, params).then(response => {
                window.console.log(response);
                window.console.log(to);
                next(() => {
                    injection.sidebar.active('setting');
                });
            }).catch(() => {
                injection.loading.error();
                injection.notice.error({
                    title: '初始化页面失败！',
                });
            });
        },
    };
</script>
<template>
    <p>自定义表单</p>
</template>