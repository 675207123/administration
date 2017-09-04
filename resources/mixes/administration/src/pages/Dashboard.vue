<script>
    import injection, { trans } from '../helpers/injection';

    export default {
        beforeRouteEnter(to, from, next) {
            injection.loading.start();
            injection.http.post(`${window.api}/administration/dashboard`).then(response => {
                next(vm => {
                    vm.dashboards.left = response.data.data.left;
                    vm.dashboards.right = response.data.data.right;
                    injection.loading.finish();
                    injection.sidebar.active('setting');
                });
            }).catch(() => {
                injection.loading.error();
            });
        },
        data() {
            return {
                dashboards: {
                    left: [],
                    right: [],
                },
            };
        },
        mounted() {
            this.$store.commit('title', trans('administration.title.dashboard'));
        },
    };
</script>
<template>
    <div class="bashboard-wrap">
        <row :gutter="20">
            <i-col span="12">
                <dashboard :options="{ group: 'dashboards' }" v-model="dashboards.left" style="min-height: 100px;">
                    <card :bordered="false" :key="index" v-for="(dashboard, index) in dashboards.left" style="margin-bottom: 20px;">
                        <p slot="title">{{ dashboard.title }}</p>
                        <dashboard-content :template="dashboard.template"></dashboard-content>
                    </card>
                </dashboard>
            </i-col>
            <i-col span="12">
                <dashboard :options="{ group: 'dashboards' }" v-model="dashboards.right" style="min-height: 100px;">
                    <card :bordered="false" :key="index" v-for="(dashboard, index) in dashboards.right" style="margin-bottom: 20px;">
                        <p slot="title">{{ dashboard.title }}</p>
                        <dashboard-content :template="dashboard.template"></dashboard-content>
                    </card>
                </dashboard>
            </i-col>
        </row>
    </div>
</template>