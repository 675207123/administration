<script>
    import injection, { trans } from '../helpers/injection';

    export default {
        beforeRouteEnter(to, from, next) {
            injection.loading.start();
            injection.http.post(`${window.api}/debug/get`).then(response => {
                next(vm => {
                    injection.loading.finish();
                    injection.sidebar.active('setting');
                    const data = response.data.data;
                    vm.form.enabled = data.debug === '1';
                    vm.form.testing = data.testing === '1';
                });
            });
        },
        data() {
            return {
                form: {
                    enabled: false,
                    testing: false,
                },
            };
        },
        methods: {
            change() {
                const self = this;
                injection.loading.start();
                injection.http.post(`${window.api}/debug/set`, self.form).then(() => {
                    injection.loading.finish();
                    self.$notice.success({
                        title: '模式切换成功！',
                    });
                });
            },
        },
        mounted() {
            this.$store.commit('title', trans('administration.title.debug'));
        },
    };
</script>
<template>
    <card :bordered="false">
        <p slot="title">调试工具</p>
        <i-form :label-width="200" :form="form">
            <row>
                <i-col span="12">
                    <form-item label="Debug 模式">
                        <i-switch v-model="form.enabled" size="large" @on-change="change">
                            <span slot="open">开启</span>
                            <span slot="close">关闭</span>
                        </i-switch>
                    </form-item>
                </i-col>
            </row>
            <row>
                <i-col span="12">
                    <form-item label="测试模式">
                        <i-switch v-model="form.testing" size="large" @on-change="change">
                            <span slot="open">开启</span>
                            <span slot="close">关闭</span>
                        </i-switch>
                    </form-item>
                </i-col>
            </row>
        </i-form>
    </card>
</template>