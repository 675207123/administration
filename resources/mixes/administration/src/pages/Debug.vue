<script>
    import injection, { trans } from '../helpers/injection';

    export default {
        beforeRouteEnter(to, from, next) {
            injection.loading.start();
            injection.http.get(`${window.api}/administration/debug/configurations`).then(response => {
                next(vm => {
                    vm.form.enabled = response.data.data.debug;
                    vm.form.testing = response.data.data.testing;
                    injection.loading.finish();
                });
            }).catch(() => {
                injection.loading.error();
            });
        },
        data() {
            return {
                form: {
                    enabled: false,
                    testing: false,
                },
                loading: false,
            };
        },
        methods: {
            change() {
                const self = this;
                self.$loading.start();
                self.$http.post(`${window.api}/administration/debug/configurations`, self.form).then(() => {
                    self.$loading.finish();
                    self.$notice.success({
                        title: '模式切换成功！',
                    });
                }).catch(() => {
                    self.$loading.error();
                });
            },
            clearCache() {
                const self = this;
                self.loading = true;
                self.$http.post(`${window.api}/administration/cache/clear`).then(() => {
                    self.$notice.open({
                        title: '缓存清除成功！',
                    });
                }).catch(() => {
                    self.$notice.error({
                        title: '缓存清除失败！',
                    });
                }).finally(() => {
                    self.loading = false;
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
                    <form-item label="缓存清除">
                        <i-button :loading="loading" type="primary" @click.native="clearCache">
                            <span v-if="loading">正在清除...</span>
                            <span v-else>清除</span>
                        </i-button>
                    </form-item>
                </i-col>
            </row>
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