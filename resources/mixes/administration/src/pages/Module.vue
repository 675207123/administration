<script>
    import file from '../helpers/export';
    import injection, { trans } from '../helpers/injection';

    export default {
        beforeRouteEnter(to, from, next) {
            injection.loading.start();
            injection.http.post(`${window.api}/administration/module`).then(response => {
                next(vm => {
                    injection.loading.finish();
                    injection.sidebar.active('setting');
                    const all = response.data.data.all;
                    const domains = response.data.data.domains;
                    const enabled = response.data.data.enabled;
                    const installed = response.data.data.installed;
                    const notInstalled = response.data.data.notInstall;
                    vm.list.all = Object.keys(all).map(key => all[key]);
                    vm.list.domains = Object.keys(domains).map(key => domains[key]);
                    vm.list.enabled = Object.keys(enabled).map(key => enabled[key]);
                    vm.list.installed = Object.keys(installed).map(key => {
                        const data = installed[key];
                        data.loading = false;
                        return data;
                    });
                    vm.list.notInstalled = Object.keys(notInstalled).map(key => {
                        const data = notInstalled[key];
                        data.loading = false;
                        return data;
                    });
                });
            });
        },
        data() {
            const self = this;
            return {
                columns: {
                    domains: [
                        {
                            alias: 'right',
                            key: 'name',
                            title: '模块名称',
                            width: 200,
                        },
                        {
                            key: 'host',
                            render(h, data) {
                                let changed = false;
                                const row = data.row;
                                return h('i-input', {
                                    on: {
                                        'on-blur': () => {
                                            if (changed) {
                                                changed = false;
                                                self.updateDomain(row);
                                            }
                                        },
                                        'on-change': event => {
                                            if (row.host !== event.target.value) {
                                                changed = true;
                                            }
                                            row.host = event.target.value;
                                        },
                                    },
                                    props: {
                                        placeholder: '请填写不带 http:// 或 https:// 的域名',
                                        value: self.list.domains[data.index].host,
                                    },
                                });
                            },
                            title: '域名',
                            width: 300,
                        },
                        {
                            alias: 'center',
                            key: 'default',
                            render(h, data) {
                                if (data.row.identification === 'notadd/api' || data.row.identification === 'notadd/notadd') {
                                    return '';
                                }
                                return h('checkbox', {
                                    on: {
                                        'on-change': value => {
                                            data.row.default = value;
                                            self.updateDomain(data.row);
                                        },
                                    },
                                    props: {
                                        value: self.list.domains[data.index].default,
                                    },
                                });
                            },
                            title: '默认',
                            width: 60,
                        },
                        {
                            key: 'alias',
                            render(h, data) {
                                if (data.row.identification === 'notadd/notadd') {
                                    return data.row.alias;
                                }
                                let changed = false;
                                const row = data.row;
                                return h('i-input', {
                                    on: {
                                        'on-blur': () => {
                                            if (changed) {
                                                changed = false;
                                                self.updateDomain(row);
                                            }
                                        },
                                        'on-change': event => {
                                            if (row.alias !== event.target.value) {
                                                changed = true;
                                            }
                                            row.alias = event.target.value;
                                        },
                                    },
                                    props: {
                                        value: self.list.domains[data.index].alias,
                                    },
                                });
                            },
                            title: '别名',
                            width: 300,
                        },
                        {
                            key: 'extra',
                            title: ' ',
                        },
                        {
                            key: 'using',
                            render(h, data) {
                                return h('i-switch', {
                                    on: {
                                        input(status) {
                                            if (self.list.domains[data.index].host === '') {
                                                self.$notice.error({
                                                    title: '请先填写正确的域名，再提交开启！',
                                                });
                                            } else {
                                                data.row.enabled = status;
                                                self.updateDomain(data.row);
                                            }
                                        },
                                    },
                                    props: {
                                        value: self.list.domains[data.index].enabled,
                                    },
                                });
                            },
                            title: '使用域名',
                            width: 160,
                        },
                    ],
                    exports: [
                        {
                            align: 'center',
                            type: 'selection',
                            width: 60,
                        },
                        {
                            alias: 'right',
                            key: 'name',
                            title: '模块名称',
                            width: 200,
                        },
                        {
                            key: 'description',
                            title: '描述',
                            width: 400,
                        },
                        {
                            key: 'version',
                            title: '版本',
                        },
                    ],
                    installed: [
                        {
                            key: 'name',
                            title: '模块名称',
                            width: 200,
                        },
                        {
                            key: 'author',
                            title: '作者',
                            width: 200,
                        },
                        {
                            key: 'description',
                            title: '描述',
                        },
                        {
                            key: 'status',
                            render(h, data) {
                                return h('i-switch', {
                                    on: {
                                        input(status) {
                                            injection.loading.start();
                                            injection.http.post(`${window.api}/module/enable`, {
                                                name: data.row.identification,
                                                value: status,
                                            }).then(() => {
                                                injection.loading.finish();
                                                injection.notice.open({
                                                    title: status ? `开启模块${data.row.name}成功！` : `关闭模块${data.row.name}成功！`,
                                                });
                                                injection.notice.warning({
                                                    title: '将在3秒后重载网页！',
                                                });
                                                window.setTimeout(() => {
                                                    window.location.reload();
                                                }, 3000);
                                            });
                                        },
                                    },
                                    props: {
                                        value: self.list.installed[data.index].enabled,
                                    },
                                });
                            },
                            title: '状态',
                            width: 200,
                        },
                        {
                            key: 'handle',
                            render(h, data) {
                                return h('i-button', {
                                    on: {
                                        click() {
                                            self.uninstall(data.index);
                                        },
                                    },
                                    props: {
                                        loading: self.list.installed[data.index].loading,
                                        size: 'small',
                                        type: 'error',
                                    },
                                }, '卸载');
                            },
                            title: '操作',
                            width: 200,
                        },
                    ],
                    notInstalled: [
                        {
                            key: 'name',
                            title: '模块名称',
                            width: 200,
                        },
                        {
                            key: 'author',
                            title: '作者',
                            width: 200,
                        },
                        {
                            key: 'description',
                            title: '描述',
                        },
                        {
                            key: 'handle',
                            render(h, data) {
                                return h('i-button', {
                                    on: {
                                        click() {
                                            self.install(data.index);
                                        },
                                    },
                                    props: {
                                        loading: self.list.notInstalled[data.index].loading,
                                        type: 'primary',
                                    },
                                }, '安装');
                            },
                            title: '操作',
                            width: 200,
                        },
                    ],
                },
                list: {
                    all: [],
                    domains: [],
                    enabled: [],
                    installed: [],
                    notInstalled: [],
                },
                loading: false,
                selection: [],
            };
        },
        methods: {
            imports() {
                const self = this;
                self.$notice.open({
                    title: '开始导入...',
                });
            },
            exports() {
                const self = this;
                if (self.selection.length === 0) {
                    self.$notice.error({
                        title: '请先选择一个模块，再执行导出的操作！',
                    });
                } else {
                    self.$notice.open({
                        title: '开始导出...',
                    });
                    self.loading = true;
                    const data = self.selection.map(module => module.identification);
                    self.$http.post(`${window.api}/module/exports`, {
                        modules: data,
                    }).then(response => {
                        file.download(response.data.data.file, response.data.data.content, 'yaml');
                    }).catch(() => {
                        self.$notice.error({
                            title: '导出失败！',
                        });
                    }).finally(() => {
                        self.loading = false;
                    });
                }
            },
            install(index) {
                const self = this;
                const item = self.list.notInstalled[index];
                item.loading = true;
                injection.http.post(`${window.api}/module/install`, {
                    identification: item.identification,
                }).then(response => {
                    injection.console.log(response);
                    injection.console.log(response.data.data);
                    self.$notice.open({
                        title: '安装模块成功！',
                    });
                    self.refresh();
                }).finally(() => {
                    item.loading = false;
                });
            },
            refresh() {
                const self = this;
                self.$notice.open({
                    title: '正在刷新数据……',
                });
                injection.loading.start();
                injection.http.post(`${window.api}/administration/module`).then(result => {
                    injection.loading.finish();
                    injection.sidebar.active('setting');
                    const all = result.data.data.all;
                    const domains = result.data.data.domains;
                    const enabled = result.data.data.enabled;
                    const installed = result.data.data.installed;
                    const notInstalled = result.data.data.notInstall;
                    self.$nextTick(() => {
                        self.list.all = Object.keys(all).map(key => all[key]);
                        self.list.domains = Object.keys(domains).map(key => domains[key]);
                        self.list.enabled = Object.keys(enabled).map(key => enabled[key]);
                        self.list.installed = Object.keys(installed).map(key => {
                            const data = installed[key];
                            data.loading = false;
                            return data;
                        });
                        self.list.notInstalled = Object.keys(notInstalled).map(key => {
                            const data = notInstalled[key];
                            data.loading = false;
                            return data;
                        });
                        self.$notice.open({
                            title: '刷新数据完成！',
                        });
                    });
                });
            },
            selectionChanged(selection) {
                this.selection = selection;
            },
            uninstall(index) {
                const self = this;
                const module = self.list.installed[index];
                module.loading = true;
                if (module.enabled) {
                    self.$notice.error({
                        title: `必须先关闭模块${module.name}，才能卸载！`,
                    });
                    module.loading = false;
                } else {
                    injection.http.post(`${window.api}/module/uninstall`, {
                        identification: module.identification,
                    }).then(response => {
                        injection.console.log(response);
                        injection.console.log(response.data.data);
                        const messages = response.data.message;
                        messages.forEach(message => {
                            self.$notice.open({
                                title: message,
                            });
                        });
                        self.refresh();
                    }).finally(() => {
                        module.loading = false;
                    });
                }
            },
            updateDomain(data) {
                const self = this;
                self.$loading.start();
                self.$http.post(`${window.api}/module/domain`, data).then(() => {
                    self.$loading.finish();
                    self.$notice.open({
                        title: '更新模块域名信息成功！',
                    });
                    self.refresh();
                }).catch(() => {
                    self.$loading.error();
                    self.$notice.error({
                        title: '更新模块域名信息失败！',
                    });
                });
            },
        },
        mounted() {
            this.$store.commit('title', trans('administration.title.module'));
        },
    };
</script>
<template>
    <div class="module-wrap">
        <card :bordered="false">
            <tabs value="installed">
                <tab-pane label="开启模块" name="installed">
                    <i-table :columns="columns.installed" :data="list.installed"></i-table>
                </tab-pane>
                <tab-pane label="域名配置" name="domain">
                    <i-table :columns="columns.domains" :data="list.domains"></i-table>
                </tab-pane>
                <tab-pane label="导入/导出" name="exchange">
                    <div style="margin-bottom: 20px">
                        <i-button :loading="loading" type="default" @click.native="imports">
                            <span v-if="!loading">导入</span>
                            <span v-else>正在导入…</span>
                        </i-button>
                        <i-button :loading="loading" type="default" @click.native="exports">
                            <span v-if="!loading">导出</span>
                            <span v-else>正在导出…</span>
                        </i-button>
                    </div>
                    <i-table :columns="columns.exports" :data="list.enabled" @on-selection-change="selectionChanged"></i-table>
                </tab-pane>
                <tab-pane label="本地安装" name="no-installed">
                    <i-table :columns="columns.notInstalled" :data="list.notInstalled"></i-table>
                </tab-pane>
            </tabs>
        </card>
    </div>
</template>