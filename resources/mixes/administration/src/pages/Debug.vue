<script>
    import injection, { trans } from '../helpers/injection';

    export default {
        beforeRouteEnter(to, from, next) {
            injection.loading.start();
            injection.http.post(`${window.api}/debug/get`).then(response => {
                next(vm => {
                    injection.loading.finish();
                    injection.sidebar.active('setting');
                    vm.form.enabled = response.data.data.debug === '1';
                    vm.form.testing = response.data.data.testing === '1';
                });
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
                injection.loading.start();
                injection.http.post(`${window.api}/debug/set`, self.form).then(() => {
                    injection.loading.finish();
                    self.$notice.success({
                        title: '模式切换成功！',
                    });
                });
            },
            clearRedisCache() {
                const self = this;
                self.loading = true;
                self.$http.post(`${window.api}/redis/clear`).then(() => {
                    self.$notice.open({
                        title: 'Redis 缓存清除成功！',
                    });
                }).catch(() => {
                    self.$notice.error({
                        title: 'Redis 缓存清除失败！',
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
                    <form-item label="Redis 缓存清除">
                        <i-button :loading="loading" type="primary" @click.native="clearRedisCache">
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