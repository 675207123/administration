import {
    mixinAxios,
    mixinComponent,
    mixinExtension,
    mixinLocal,
    mixinModule,
    mixinRouter,
    mixinSidebar,
    mixinUse,
} from '../mixes/injection';
import App from '../App.vue';
import Page from '../pages/Page.vue';
import store from '../stores';
import { t } from '../local';

const injection = {
    modules: [],
};

function init(Vue) {
    mixinRouter(injection);
    mixinSidebar(injection);
    mixinUse(injection);
    mixinExtension(injection);
    mixinModule(injection);
    if (typeof injection.pages === 'object') {
        Object.keys(injection.pages).forEach(index => {
            switch (index) {
                case 'extension':
                    Object.keys(injection.pages[index]).forEach(node => {
                        injection.useExtensionRoute([
                            {
                                beforeEnter: injection.middleware.requireAuth,
                                component: Page,
                                path: `extension/${injection.pages[index][node].identification}`,
                            },
                        ]);
                        injection.useSidebarExtension({
                            path: `extension/${injection.pages[index][node].identification}`,
                            title: injection.pages[index][node].name,
                        });
                    });
                    break;
                case 'global':
                    Object.keys(injection.pages[index]).forEach(node => {
                        injection.useGlobalRoute([
                            {
                                beforeEnter: injection.middleware.requireAuth,
                                component: Page,
                                path: `${injection.pages[index][node].identification}`,
                            },
                        ]);
                        injection.useSidebarGlobal({
                            path: injection.pages[index][node].identification,
                            title: injection.pages[index][node].name,
                        });
                    });
                    break;
                default:
                    break;
            }
        });
    }
    const a = [];
    a.push(new Promise((resolve, reject) => {
        const script = document.createElement('script');
        script.type = 'text/javascript';
        if (script.readyState) {
            script.onreadystatechange = data => {
                if (script.readyState === 'loaded' || script.readyState === 'complete') {
                    script.onreadystatechange = null;
                    resolve(data);
                    script.remove();
                }
            };
        } else {
            script.onload = (...args) => {
                window.console.log(args);
                resolve(args);
                script.remove();
            };
        }
        script.onerror = () => {
            reject(Error('What load error!'));
        };
        script.src = 'http://notadd.io/assets/extension.js';
        document.body.appendChild(script);
    }));
    Promise.all(a).then((...args) => {
        window.console.log(args);
    });
    injection.vue = new Vue({
        el: '#app',
        router: injection.router,
        store,
        template: '<App/>',
        components: {
            App,
        },
    });
}

function loadScript(identification, url) {
    return new Promise((resolve, reject) => {
        const script = document.createElement('script');
        script.type = 'text/javascript';
        if (script.readyState) {
            script.onreadystatechange = () => {
                if (script.readyState === 'loaded' || script.readyState === 'complete') {
                    script.onreadystatechange = null;
                    resolve(window[identification]);
                    script.remove();
                }
            };
        } else {
            script.onload = () => {
                resolve(window[identification]);
                script.remove();
            };
        }
        script.onerror = () => {
            reject(Error(`${url} load error!`));
        };
        script.src = url;
        document.body.appendChild(script);
    });
}

function loadStylesheet(url) {
    return new Promise(() => {
        const stylesheet = document.createElement('link');
        stylesheet.rel = 'stylesheet';
        stylesheet.href = url;
        document.body.appendChild(stylesheet);
    });
}

function install(Vue) {
    mixinLocal(injection);
    mixinComponent(Vue, injection);
    mixinAxios(injection, Vue);
    const token = JSON.parse(window.localStorage.getItem('token'));
    injection.extensions = [];
    injection.modules = [];
    injection.navigation = [];
    injection.pages = [];
    if (token && token.access_token) {
        Vue.http.defaults.headers.common.Accept = 'application/json';
        Vue.http.defaults.headers.common.Authorization = `Bearer ${token.access_token}`;
        injection.notice.open({
            title: '正在加载模块和插件...',
        });
        injection.loading.start();
        injection.http.post(`${window.api}/administration/informations`).then(response => {
            const imports = [];
            const informations = [];
            injection.navigation = response.data.data.navigation;
            injection.pages = response.data.data.pages;
            Object.keys(response.data.data.scripts).forEach(index => {
                const script = response.data.data.scripts[index];
                imports.push(loadScript(index, script.link));
                informations.push(script);
            });
            Object.keys(response.data.data.stylesheets).forEach(index => {
                const stylesheet = response.data.data.stylesheets[index];
                loadStylesheet(stylesheet);
            });
            Promise.all(imports).then(data => {
                Object.keys(data).forEach(index => {
                    switch (informations[index].type) {
                        case 'extension':
                            if (data[index] && data[index].default) {
                                injection.extensions.push(data[index].default);
                                window.console.log(`插件[${informations[index].name}]加载成功！`);
                            } else {
                                window.console.warn(`插件[${informations[index].name}]加载失败！`);
                            }
                            break;
                        case 'module':
                            if (data[index] && data[index].default) {
                                injection.modules.push(data[index].default);
                                window.console.log(`模块[${informations[index].name}]加载成功！`);
                            } else {
                                window.console.warn(`模块[${informations[index].name}]加载失败！`);
                            }
                            break;
                        default:
                            window.console.error('不支持的注入类型！');
                            break;
                    }
                });
                init(Vue);
                injection.loading.finish();
                injection.notice.open({
                    title: '加载模块和插件成功！',
                });
            });
            if (response.data.data.debug) {
                store.commit('debug', true);
            }
        }).catch(() => {
            injection.loading.error();
            init(Vue);
        });
    } else {
        init(Vue);
    }
}

export const trans = t;

export default Object.assign(injection, {
    install,
});