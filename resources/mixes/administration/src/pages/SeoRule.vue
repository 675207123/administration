<script>
    import injection from '../helpers/injection';
    import Input from '../cells/Input.vue';

    export default {
        beforeRouteEnter(to, from, next) {
            injection.loading.start();
            injection.http.post(`${window.api}/administration/seo/list`, {
                identification: to.query.identification,
            }).then(response => {
                const list = response.data.data;
                next(vm => {
                    vm.list = Object.keys(list).map(index => list[index]);
                    injection.loading.finish();
                    injection.sidebar.active('setting');
                });
            });
        },
        data() {
            const self = this;

            return {
                changed: false,
                columns: [
                    {
                        key: 'order',
                        render(h, data) {
                            return h(Input, {
                                props: {
                                    index: data.index,
                                    key: 'order',
                                    list: self.list,
                                },
                            });
                        },
                        title: '匹配排序',
                        width: 200,
                    },
                    {
                        key: 'path',
                        render(h, data) {
                            const row = data.row;

                            return h('i-input', {
                                on: {
                                    'on-change': event => {
                                        row.path = event.target.value;
                                    },
                                    'on-blur': () => {
                                        self.list[data.index].path = row.path;
                                    },
                                },
                                props: {
                                    number: true,
                                    value: self.list[data.index].path,
                                },
                            });
                        },
                        title: '路由',
                    },
                    {
                        key: 'open',
                        render(h, data) {
                            return h('i-switch', {
                                on: {
                                    'on-change': value => {
                                        self.list[data.index].open = value;
                                    },
                                },
                                props: {
                                    size: 'large',
                                    value: data.row.open,
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
                        title: '是否开启',
                        width: 200,
                    },
                    {
                        key: 'handle',
                        render(h, data) {
                            const buttons = [];
                            if (data.row.id) {
                                buttons.push(h('router-link', {
                                    props: {
                                        to: '',
                                    },
                                }, [
                                    h('i-button', {
                                        props: {
                                            size: 'small',
                                            type: 'primary',
                                        },
                                        style: {
                                            marginRight: '10px',
                                        },
                                    }, '编辑模板'),
                                ]));
                            }
                            buttons.push(h('i-button', {
                                on: {
                                    click() {
                                        if (data.row.id) {
                                            window.console.log(data);
                                        } else {
                                            self.list.splice(data.index, 1);
                                        }
                                    },
                                },
                                props: {
                                    size: 'small',
                                    type: 'danger',
                                },
                            }, '删除'));
                            return h('div', buttons);
                        },
                        title: '操作',
                        width: 300,
                    },
                ],
                jump: h => (h('router-link', {
                    props: {
                        to: '/seo',
                    },
                }, '<')),
                list: [],
            };
        },
        methods: {
            add() {
                const self = this;
                self.list.push({
                    module: self.$route.query.identification,
                    open: true,
                    order: 0,
                    path: '',
                    template: '',
                });
            },
        },
        watch: {
            list: {
                deep: true,
                handler(value, old) {
                    const self = this;
                    window.console.log(value);
                    if (old.length > 0) {
                        self.changed = true;
                    }
                },
            },
        },
    };
</script>
<template>
    <div class="seo-wrap">
        <tabs :animated="false" style="overflow: visible;" value="current">
            <tab-pane :label="jump"></tab-pane>
            <tab-pane label="编辑" name="current">
                <card :bordered="false">
                    <div style="margin-bottom: 16px">
                        <i-button style="margin-right: 10px" type="primary" v-if="changed" @click.native="update">批量更新</i-button>
                        <i-button style="margin-right: 10px" @click.native="add">添加</i-button>
                        <p style="color: #a00; display: inline-block;" v-if="changed">数据已修改！修改后请批量更新数据！</p>
                    </div>
                    <i-table :columns="columns" :data="list"></i-table>
                </card>
            </tab-pane>
        </tabs>
    </div>
</template>