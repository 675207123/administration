<script>
    import injection from '../helpers/injection';

    export default {
        beforeRouteEnter(to, from, next) {
            injection.loading.start();
            injection.http.get(`${window.api}/administration/menus`).then(response => {
                next(vm => {
                    vm.items = Object.keys(response.data.data).map(index => response.data.data[index]);
                    vm.originals = Object.keys(response.data.originals).map(index => response.data.originals[index]);
                    injection.loading.finish();
                    injection.sidebar.active('setting');
                });
            });
        },
        data() {
            const self = this;

            return {
                columns: [
                    {
                        render(h) {
                            return h('p', {
                                style: {
                                    textAlign: 'center',
                                },
                            }, '子级菜单，尽请期待！');
                        },
                        type: 'expand',
                        width: 50,
                    },
                    {
                        key: 'order',
                        render(h, data) {
                            const store = data.row;
                            if (store.identification === 'global') {
                                return '0';
                            }

                            return h('tooltip', {
                                props: {
                                    placement: 'right-end',
                                },
                                scopedSlots: {
                                    content() {
                                        return '回车以修改数据';
                                    },
                                    default() {
                                        return h('i-input', {
                                            on: {
                                                'on-change': event => {
                                                    store.order = event.target.value;
                                                },
                                                'on-enter': () => {
                                                    self.list[data.index].order = store.order;
                                                    self.update();
                                                },
                                            },
                                            props: {
                                                number: true,
                                                value: self.list[data.index].order,
                                            },
                                        });
                                    },
                                },
                            });
                        },
                        title: '排序',
                        width: 200,
                    },
                    {
                        key: 'text',
                        title: '菜单名称',
                    },
                    {
                        key: 'show',
                        render(h, data) {
                            return h('i-switch', {
                                on: {
                                    'on-change': value => {
                                        self.list[data.index].show = value;
                                        self.update();
                                    },
                                },
                                props: {
                                    size: 'large',
                                    value: data.row.show,
                                },
                                scopedSlots: {
                                    close() {
                                        return h('span', '关闭');
                                    },
                                    open() {
                                        return h('span', '开启');
                                    },
                                },
                            });
                        },
                        title: '是否显示',
                        width: 200,
                    },
                ],
                items: [],
                originals: [],
            };
        },
        methods: {
            update() {
                const self = this;
                self.$loading.start();
                self.$http.post(`${window.api}/administration/navigation`, {
                    data: self.list,
                }).then(() => {
                    self.$loading.finish();
                    self.$notice.open({
                        title: '更新菜单成功！',
                    });
                }).catch(() => {
                    self.$loading.error();
                    self.$notice.error({
                        title: '更新菜单失败！',
                    });
                });
            },
        },
    };
</script>
<template>
    <div class="page-wrap">
        <tabs>
            <tab-pane label="菜单管理">
                <card :bordered="false">
                    <i-table :columns="columns" :data="items"></i-table>
                </card>
            </tab-pane>
        </tabs>
    </div>
</template>