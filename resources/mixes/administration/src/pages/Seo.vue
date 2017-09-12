<script>
    import injection from '../helpers/injection';

    export default {
        beforeRouteEnter(to, from, next) {
            injection.loading.start();
            injection.http.post(`${window.api}/administration/seo/module`).then(response => {
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
            window.console.log(self);

            return {
                columns: [
                    {
                        key: 'order',
                        render(h, data) {
                            const row = data.row;
                            if (row.identification === 'global') {
                                return '/';
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
                                                    row.order = event.target.value;
                                                },
                                                'on-enter': () => {
                                                    self.list[data.index].order = row.order;
                                                    self.update();
                                                },
                                            },
                                            props: {
                                                value: data.row.order,
                                            },
                                        });
                                    },
                                },
                            });
                        },
                        title: '匹配排序',
                        width: 100,
                    },
                    {
                        key: 'name',
                        title: '名称',
                        width: 300,
                    },
                    {
                        key: 'alias',
                        title: '别名/域名',
                    },
                    {
                        key: 'handle',
                        render(h, data) {
                            return h('router-link', {
                                props: {
                                    to: `/seo/module?identification=${data.row.identification}`,
                                },
                            }, [
                                h('i-button', {
                                    props: {
                                        size: 'small',
                                        type: 'primary',
                                    },
                                }, '编辑'),
                            ]);
                        },
                        title: '操作',
                        width: 300,
                    },
                ],
                list: [],
            };
        },
        methods: {

        },
    };
</script>
<template>
    <div class="seo-wrap">
        <tabs>
            <tab-pane label="SEO 模板">
                <card :bordered="false">
                    <i-table :columns="columns" :data="list"></i-table>
                </card>
            </tab-pane>
        </tabs>
    </div>
</template>