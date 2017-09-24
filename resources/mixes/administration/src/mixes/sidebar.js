export default function (injection) {
    Object.assign(injection, {
        sidebar: {
            active: null,
            lists: {
                setting: [
                    {
                        children: [],
                        icon: 'ios-cog',
                        title: injection.trans('administration.menu.sidebar.global.group'),
                    },
                    {
                        children: [
                            {
                                path: '/upload',
                                title: injection.trans('administration.menu.sidebar.attachment.upload'),
                            },
                        ],
                        icon: 'ios-paper',
                        title: injection.trans('administration.menu.sidebar.attachment.group'),
                    },
                    {
                        children: [
                            {
                                path: '/expand',
                                title: injection.trans('administration.menu.sidebar.application.expand'),
                            },
                            {
                                path: '/module',
                                title: injection.trans('administration.menu.sidebar.application.module'),
                            },
                            {
                                path: '/addon',
                                title: injection.trans('administration.menu.sidebar.application.extension'),
                            },
                        ],
                        icon: 'plus',
                        title: injection.trans('administration.menu.sidebar.application.group'),
                    },
                    {
                        children: [],
                        icon: 'plus',
                        title: injection.trans('administration.menu.sidebar.extension.group'),
                    },
                    {
                        children: [
                            {
                                path: '/menu',
                                title: '菜单管理',
                            },
                            {
                                path: '/seo',
                                title: 'SEO 管理',
                            },
                            // {
                            //     path: '/navigation',
                            //     title: '导航管理',
                            // },
                            {
                                path: '/mail',
                                title: injection.trans('administration.menu.sidebar.component.mail'),
                            },
                            {
                                path: '/debug',
                                title: injection.trans('administration.menu.sidebar.component.debug'),
                            },
                        ],
                        icon: 'stats-bars',
                        title: injection.trans('administration.menu.sidebar.component.group'),
                    },
                ],
            },
        },
    });
}