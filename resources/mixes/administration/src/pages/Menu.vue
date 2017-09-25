<script>
    import injection from '../helpers/injection';
    import MenuChildren from './MenuChildren.vue';

    export default {
        beforeRouteEnter(to, from, next) {
            injection.loading.start();
            injection.http.get(`${window.api}/administration/menus`).then(response => {
                next(vm => {
                    vm.items = Object.keys(response.data.data).map(index => response.data.data[index]);
                    Object.keys(response.data.originals).forEach(index => {
                        response.data.originals[index].expand = false;
                    });
                    window.console.log(response.data.originals);
                    vm.originals = response.data.originals;
                    injection.loading.finish();
                    injection.sidebar.active('setting');
                });
            });
        },
        components: {
            MenuChildren,
        },
        data() {
            return {
                items: [],
                originals: {},
                rules: {
                    order: [
                        {
                            message: '排序不能为空',
                            required: true,
                            trigger: 'change',
                            type: 'number',
                        },
                    ],
                    text: [
                        {
                            required: true,
                            message: '菜单名称不能为空',
                            trigger: 'change',
                            type: 'string',
                        },
                    ],
                },
            };
        },
        methods: {
            expand(index) {
                this.originals[index].expand = !this.originals[index].expand;
            },
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
        watch: {
            originals: {
                deep: true,
                handler(val) {
                    window.console.log(val);
                },
            },
        },
    };
</script>
<template>
    <div class="page-wrap">
        <tabs>
            <tab-pane label="菜单管理">
                <card :bordered="false">
                    <i-form :model="originals" ref="form">
                        <div class="ivu-table-wrapper">
                            <div class="ivu-table">
                                <div class="ivu-table-header">
                                    <table cellspacing="0" cellpadding="0" border="0" width="100%">
                                        <colgroup>
                                            <col width="60">
                                            <col width="200">
                                            <col>
                                            <col width="200">
                                        </colgroup>
                                        <thead>
                                        <tr>
                                            <th></th>
                                            <th>
                                                <div class="ivu-table-cell"><span>排序</span></div>
                                            </th>
                                            <th>
                                                <div class="ivu-table-cell"><span>菜单名称</span></div>
                                            </th>
                                            <th>
                                                <div class="ivu-table-cell"><span>是否开启</span></div>
                                            </th>
                                        </tr>
                                        </thead>
                                    </table>
                                </div>
                                <div class="ivu-table-body" v-if="items.length">
                                    <table cellspacing="0" cellpadding="0" border="0" width="100%">
                                        <colgroup>
                                            <col width="60">
                                            <col width="200">
                                            <col>
                                            <col width="300">
                                        </colgroup>
                                        <tbody class="ivu-table-tbody">
                                        <template v-for="(item, index) in items">
                                            <tr class="ivu-table-row">
                                                <td>
                                                    <div class="ivu-table-cell ivu-table-cell-with-expand"
                                                         v-if="Object.keys(item.children).length > 0"
                                                         @click="expand(item.index)">
                                                        <template v-if="originals[item.index].expand">
                                                            <div class="ivu-table-cell-expand ivu-table-cell-expand-expanded">
                                                                <div class="ivu-table-cell-expand">
                                                                    <i class="ivu-icon ivu-icon-ios-arrow-right"></i>
                                                                </div>
                                                            </div>
                                                        </template>
                                                        <template v-else>
                                                            <div class="ivu-table-cell-expand">
                                                                <div class="ivu-table-cell-expand">
                                                                    <i class="ivu-icon ivu-icon-ios-arrow-right"></i>
                                                                </div>
                                                            </div>
                                                        </template>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="ivu-table-cell">
                                                        <form-item label=""
                                                                   :prop="item.index + '.order'"
                                                                   :rules="rules.order"
                                                                   style="margin-bottom: 0;">
                                                            <i-input :number="true" placeholder="请输入规则排序" v-model="originals[item.index].order"></i-input>
                                                        </form-item>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="ivu-table-cell">
                                                        <form-item label=""
                                                                   :prop="item.index + '.text'"
                                                                   :rules="rules.text"
                                                                   style="margin-bottom: 0;">
                                                            <i-input placeholder="请输入菜单名称" v-model="originals[item.index].text"></i-input>
                                                        </form-item>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="ivu-table-cell">
                                                        <form-item label=""
                                                                   :prop="item.index + '.text'"
                                                                   :rules="rules.text"
                                                                   style="margin-bottom: 0;">
                                                            <i-switch v-model="originals[item.index].enabled"></i-switch>
                                                        </form-item>
                                                    </div>
                                                </td>
                                            </tr>
                                            <menu-children :items="item.children"
                                                           :originals="originals"
                                                           :rules="rules"
                                                           v-if="Object.keys(item.children).length > 0"></menu-children>
                                        </template>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="ivu-table-tip" v-if="items.length === 0">
                                    <table cellspacing="0" cellpadding="0" border="0">
                                        <tbody>
                                        <tr>
                                            <td><span>暂无筛选结果</span></td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </i-form>
                </card>
            </tab-pane>
        </tabs>
    </div>
</template>