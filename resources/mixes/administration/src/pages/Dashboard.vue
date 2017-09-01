<script>
    import injection, { trans } from '../helpers/injection';

    export default {
        beforeRouteEnter(to, from, next) {
            injection.loading.start();
            injection.http.post(`${window.api}/administration/dashboard`).then(response => {
                next(vm => {
                    vm.dashboards.first = response.data.data;
                    vm.dashboards.last = response.data.data;
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
                    first: [],
                    last: [],
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
                <dashboard :options="{ group: 'dashboards' }" v-model="dashboards.first" style="min-height: 100px;">
                    <card :bordered="false" v-for="dashboard in dashboards.first" style="margin-bottom: 20px;">
                        <p slot="title">{{ dashboard.title }}</p>
                        <dashboard-content :template="dashboard.template"></dashboard-content>
                    </card>
                </dashboard>
            </i-col>
            <i-col span="12">
                <dashboard :options="{ group: 'dashboards' }" v-model="dashboards.last" style="min-height: 100px;">
                    <card :bordered="false" v-for="dashboard in dashboards.last" style="margin-bottom: 20px;">
                        <p slot="title">{{ dashboard.title }}</p>
                        <dashboard-content :template="dashboard.template"></dashboard-content>
                    </card>
                </dashboard>
            </i-col>
        </row>
    </div>
</template>