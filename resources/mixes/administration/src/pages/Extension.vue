<script>
    import injection, { trans } from '../helpers/injection';

    export default {
        beforeRouteEnter(to, from, next) {
            injection.loading.start();
            injection.http.get(`${window.api}/administration/extensions`).then(response => {
                const extensions = response.data.data;
                next(vm => {
                    vm.extensions = Object.keys(extensions).map(index => {
                        const extension = extensions[index];
                        extension.loading = {
                            install: false,
                            uninstall: false,
                        };

                        return extension;
                    });
                    injection.loading.finish();
                });
            }).catch(() => {
                injection.loading.error();
            });
        },
        data() {
            const self = this;

            return {
                columns: [
                    {
                        key: 'description',
                        title: '拓展名称',
                        width: 200,
                    },
                    {
                        key: 'authors',
                        render(h, data) {
                            const authors = [];
                            Object.keys(data.row.authors).forEach(index => {
                                const author = data.row.authors[index];
                                authors.push(h('p', `${author.name}<${author.email}>`));
                            });

                            return h('div', authors);
                        },
                        title: '作者',
                        width: 300,
                    },
                    {
                        key: 'installed',
                        title: '安装状态',
                    },
                    {
                        key: 'handler',
                        render(h, data) {
                            return h('div', [
                                h('i-button', {
                                    on: {
                                        click() {
                                            self.extensions[data.index].loading.install = true;
                                            self.$http.post(`${window.api}/administration/extensions`, {
                                                identification: data.row.identification,
                                            }).then(() => {
                                                self.$notice.open({
                                                    desc: '请前往控制器执行命令 php notadd extension install，以执行拓展的安装！',
                                                    title: '添加到待安装列表成功！',
                                                });
                                            }).catch(() => {
                                                self.$notice.error({
                                                    title: '添加到待安装列表失败！',
                                                });
                                            }).finally(() => {
                                                self.extensions[data.index].loading.install = false;
                                            });
                                        },
                                    },
                                    props: {
                                        disabled: data.row.installed,
                                        loading: self.extensions[data.index].loading.install,
                                        size: 'small',
                                        type: 'primary',
                                    },
                                    style: {
                                        marginRight: '10px',
                                    },
                                }, '添加到待安装列表'),
                                h('i-button', {
                                    on: {
                                        click() {
                                            self.extensions[data.index].loading.uninstall = true;
                                            self.$http.delete(`${window.api}/administration/extensions`, {
                                                identification: data.row.identification,
                                            }).then().catch(() => {
                                                self.$notice.error({
                                                    title: '添加到待卸载列表失败！',
                                                });
                                            }).finally(() => {
                                                self.extensions[data.index].loading.uninstall = false;
                                            });
                                        },
                                    },
                                    props: {
                                        disabled: !data.row.installed,
                                        loading: self.extensions[data.index].loading.uninstall,
                                        size: 'small',
                                        type: 'error',
                                    },
                                }, '添加到待卸载列表'),
                            ]);
                        },
                        title: '操作',
                        width: 400,
                    },
                ],
                extensions: [],
            };
        },
        mounted() {
            this.$store.commit('title', trans('administration.title.expand'));
        },
    };
</script>
<template>
    <div class="module-wrap">
        <tabs value="installed">
            <tab-pane label="拓展配置" name="installed">
                <card :bordered="false">
                    <i-table :columns="columns" :data="extensions"></i-table>
                </card>
            </tab-pane>
        </tabs>
    </div>
</template>