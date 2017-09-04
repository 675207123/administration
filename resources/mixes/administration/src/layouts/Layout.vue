<script>
    import injection from '../helpers/injection';

    export default {
        computed: {
            name() {
                return this.$store.state.token.name;
            },
        },
        created() {
            Object.assign(injection.sidebar, {
                active: this.active,
            });
        },
        data() {
            return {
                hideSidebar: false,
                navigation: injection.navigation,
                sidebar: [],
                url: window.url,
            };
        },
        methods: {
            active(key) {
                if (injection.sidebar.lists[key]) {
                    this.sidebar = injection.sidebar.lists[key];
                }
            },
            logout() {
                window.localStorage.clear();
                this.$router.push('/login');
            },
            toggleClick() {
                this.hideSidebar = !this.hideSidebar;
            },
        },
        mounted() {
            const self = this;
            self.$http.post(`${window.api}/administration/access`).then(() => {
                window.console.log('登录状态正常！');
            });
        },
    };
</script>
<template>
    <div class="layout" :class="{'layout-hide-sidebar': hideSidebar}">
        <div class="layout-left">
            <i-menu :accordion="true" theme="dark" width="auto">
                <div class="layout-logo-left"></div>
                <template v-for="(item, key) in sidebar">
                    <submenu :name="'sidebar-' + key" v-if="item.children">
                        <template slot="title">
                            <icon :type="item.icon"></icon>
                            {{ item.title }}
                        </template>
                        <menu-item :name="'sidebar-' + key + '-' + index" v-for="(sub, index) in item.children"
                                   :key="index">
                            <router-link :to="sub.path">{{ sub.title }}</router-link>
                        </menu-item>
                    </submenu>
                    <menu-item :name="'sidebar-' + key" v-else>
                        <router-link :to="item.path">
                            <icon :type="item.icon"></icon>
                            {{ item.title }}
                        </router-link>
                    </menu-item>
                </template>
            </i-menu>
        </div>
        <div class="layout-header">
            <i-button type="text" @click.native="toggleClick">
                <icon size="32" type="navicon"></icon>
            </i-button>
            <i-menu mode="horizontal" theme="light">
                <menu-item :name="'nav-' + key" v-for="(nav, key) in navigation" :key="key">
                    <router-link :to="nav.path">
                        <icon :type="nav.icon"></icon>
                        {{ nav.title }}
                    </router-link>
                </menu-item>
            </i-menu>
            <i-menu mode="horizontal" theme="light">
                <menu-item name="right-1">
                    <a :href="url" target="_blank">前台</a>
                </menu-item>
                <submenu name="right-2">
                    <template slot="title">
                        <icon type="person"></icon>
                        {{ name }}
                    </template>
                    <menu-item name="right-2-1" @click.native="logout">退出</menu-item>
                </submenu>
            </i-menu>
        </div>
        <div class="layout-content">
            <div class="layout-content-main">
                <router-view></router-view>
            </div>
            <div class="layout-copy">2017 &copy; Notadd</div>
        </div>
    </div>
</template>