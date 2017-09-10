<script>
    import injection from '../helpers/injection';

    export default {
        beforeRouteEnter(to, from, next) {
            injection.loading.start();
            injection.http.post(`${window.api}/administration/info`).then(response => {
                const data = response.data.data.menus;
                next(vm => {
                    vm.list = Object.keys(data).map(index => data[index]);
                    injection.loading.finish();
                    injection.sidebar.active('setting');
                });
            });
        },
        data() {
            return {
                columns: [
                    {
                        key: 'order',
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
                            return data;
                        },
                        title: '是否显示',
                        width: 200,
                    },
                ],
                list: [],
            };
        },
    };
</script>
<template>
    <div class="page-wrap">
        <tabs>
            <tab-pane label="菜单管理">
                <card :bordered="false">
                    <i-table :columns="columns" :data="list"></i-table>
                </card>
            </tab-pane>
        </tabs>
    </div>
</template>