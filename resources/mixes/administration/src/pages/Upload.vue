<script>
    import { mapState } from 'vuex';
    import injection, { trans } from '../helpers/injection';

    export default {
        beforeRouteEnter(to, from, next) {
            injection.loading.start();
            injection.loading.start();
            injection.http.post(`${window.api}/administration`, {
                query: 'query {' +
                    'canManagementFileExtension:setting(key:"attachment.manager.image"),' +
                    'canManagementImageExtension:setting(key:"attachment.manager.image"),' +
                    'canUploadCatcherExtension:setting(key:"attachment.manager.image"),' +
                    'canUploadFileExtension:setting(key:"attachment.manager.image"),' +
                    'canUploadImageExtension:setting(key:"attachment.manager.image"),' +
                    'canUploadVideoExtension:setting(key:"attachment.manager.image"),' +
                    'fileMaxSize:setting(key:"attachment.manager.image"),' +
                    'imageMaxSize:setting(key:"attachment.manager.image"),' +
                    'imageProcessingEngine:setting(key:"attachment.manager.image"),' +
                    'videoMaxSize:setting(key:"attachment.manager.image"),' +
                '}',
            }).then(response => {
                const {
                    canManagementFileExtension,
                    canManagementImageExtension,
                    canUploadCatcherExtension,
                    canUploadFileExtension,
                    canUploadImageExtension,
                    canUploadVideoExtension,
                    fileMaxSize,
                    imageMaxSize,
                    imageProcessingEngine,
                    videoMaxSize,
                } = response.data.data;
                next(vm => {
                    vm.form.canManagementFileExtension = canManagementFileExtension;
                    vm.form.canManagementImageExtension = canManagementImageExtension;
                    vm.form.canUploadCatcherExtension = canUploadCatcherExtension;
                    vm.form.canUploadFileExtension = canUploadFileExtension;
                    vm.form.canUploadImageExtension = canUploadImageExtension;
                    vm.form.canUploadVideoExtension = canUploadVideoExtension;
                    vm.form.fileMaxSize = fileMaxSize;
                    vm.form.imageMaxSize = imageMaxSize;
                    vm.form.imageProcessingEngine = imageProcessingEngine;
                    vm.form.videoMaxSize = videoMaxSize;
                    injection.loading.finish();
                });
            }).catch(() => {
                injection.loading.error();
            });
        },
        computed: {
            ...mapState({
                canManagementFileExtension: state => state.setting['attachment.manager.image'],
                canManagementImageExtension: state => state.setting['attachment.manager.file'],
                canUploadCatcherExtension: state => state.setting['attachment.format.image'],
                canUploadFileExtension: state => state.setting['attachment.format.catcher'],
                canUploadImageExtension: state => state.setting['attachment.format.video'],
                canUploadVideoExtension: state => state.setting['attachment.format.file'],
                fileMaxSize: state => state.setting['attachment.limit.file'],
                imageMaxSize: state => state.setting['attachment.limit.image'],
                imageProcessingEngine: state => state.setting['attachment.engine'],
                videoMaxSize: state => state.setting['attachment.limit.video'],
            }),
        },
        data() {
            return {
                form: {
                    canManagementFileExtension: '',
                    canManagementImageExtension: '',
                    canUploadCatcherExtension: '',
                    canUploadFileExtension: '',
                    canUploadImageExtension: '',
                    canUploadVideoExtension: '',
                    fileMaxSize: 0,
                    imageMaxSize: 0,
                    imageProcessingEngine: 'gd',
                    videoMaxSize: 0,
                },
                loading: false,
                rules: {
                    canManagementFileExtension: [
                        {
                            required: true,
                            type: 'string',
                            message: '请输入允许管理文件的扩展名',
                            trigger: 'change',
                        },
                    ],
                    canManagementImageExtension: [
                        {
                            required: true,
                            type: 'string',
                            message: '请输入允许管理图片的扩展名',
                            trigger: 'change',
                        },
                    ],
                    canUploadCatcherExtension: [
                        {
                            required: true,
                            type: 'string',
                            message: '请输入允许管理截图的扩展名',
                            trigger: 'change',
                        },
                    ],
                    canUploadFileExtension: [
                        {
                            required: true,
                            type: 'string',
                            message: '请输入允许上传文件的扩展名',
                            trigger: 'change',
                        },
                    ],
                    canUploadImageExtension: [
                        {
                            required: true,
                            type: 'string',
                            message: '请输入允许管理图片的扩展名',
                            trigger: 'change',
                        },
                    ],
                    canUploadVideoExtension: [
                        {
                            required: true,
                            type: 'string',
                            message: '请输入允许上传视频的扩展名',
                            trigger: 'change',
                        },
                    ],
                    fileMaxSize: [
                        {
                            required: true,
                            type: 'string',
                            message: '请输入附件大小',
                            trigger: 'change',
                        },
                    ],
                    imageMaxSize: [
                        {
                            required: true,
                            type: 'string',
                            message: '请输入图片大小',
                            trigger: 'change',
                        },
                    ],
                    videoMaxSize: [
                        {
                            required: true,
                            type: 'string',
                            message: '请输入视频大小',
                            trigger: 'change',
                        },
                    ],
                },
            };
        },
        methods: {
            submit() {
                const self = this;
                self.loading = true;
                self.$refs.form.validate(valid => {
                    if (valid) {
                        self.$http.post(`${window.api}/administration/attachment/configurations`, self.form).then(() => {
                            self.$notice.open({
                                title: '更新上传配置信息成功！',
                            });
                        }).finally(() => {
                            self.loading = false;
                        });
                    } else {
                        self.loading = false;
                        self.$notice.error({
                            title: '请正确填写上传配置信息！',
                        });
                    }
                });
            },
        },
        mounted() {
            this.$store.commit('title', trans('administration.title.upload'));
        },
    };
</script>
<template>
    <card :bordered="false">
        <p slot="title">上传配置</p>
        <i-form :label-width="200" :model="form" ref="form" :rules="rules">
            <row>
                <i-col span="12">
                    <form-item label="图片处理引擎" prop="imageProcessingEngine">
                        <radio-group v-model="form.imageProcessingEngine">
                            <radio label="gd">GD 库</radio>
                        </radio-group>
                    </form-item>
                </i-col>
            </row>
            <row>
                <i-col span="12">
                    <form-item label="附件大小" prop="fileMaxSize">
                        <i-input placeholder="请输入附件大小" v-model="form.fileMaxSize">
                            <span slot="append">KB</span>
                        </i-input>
                    </form-item>
                </i-col>
            </row>
            <row>
                <i-col span="12">
                    <form-item label="图片大小" prop="imageMaxSize">
                        <i-input placeholder="请输入图片大小" v-model="form.imageMaxSize">
                            <span slot="append">KB</span>
                        </i-input>
                    </form-item>
                </i-col>
            </row>
            <row>
                <i-col span="12">
                    <form-item label="视频大小" prop="videoMaxSize">
                        <i-input placeholder="请输入视频大小" v-model="form.videoMaxSize">
                            <span slot="append">KB</span>
                        </i-input>
                    </form-item>
                </i-col>
            </row>
            <row>
                <i-col span="12">
                    <form-item label="允许图片的扩展名" prop="canUploadImageExtension">
                        <i-input type="textarea" placeholder="请输入允许管理图片的扩展名" v-model="form.canUploadImageExtension"
                                 :autosize="{minRows: 2,maxRows: 5}"></i-input>
                    </form-item>
                </i-col>
            </row>
            <row>

                <i-col span="12">
                    <form-item label="允许上传截图的扩展名" prop="canUploadCatcherExtension">
                        <i-input type="textarea" placeholder="请输入允许管理截图的扩展名" v-model="form.canUploadCatcherExtension"
                                 :autosize="{minRows: 2,maxRows: 5}"></i-input>
                    </form-item>
                </i-col>
            </row>
            <row>
                <i-col span="12">
                    <form-item label="允许上传视频的扩展名" prop="canUploadVideoExtension">
                        <i-input type="textarea" placeholder="请输入允许上传视频的扩展名" v-model="form.canUploadVideoExtension"
                                 :autosize="{minRows: 2,maxRows: 5}"></i-input>
                    </form-item>
                </i-col>
            </row>
            <row>
                <i-col span="12">
                    <form-item label="允许上传文件的扩展名" prop="canUploadFileExtension">
                        <i-input type="textarea" placeholder="请输入允许上传文件的扩展名" v-model="form.canUploadFileExtension"
                                 :autosize="{minRows: 2,maxRows: 5}"></i-input>
                    </form-item>
                </i-col>
            </row>
            <row>
                <i-col span="12">
                    <form-item label="允许管理图片的扩展名" prop="canManagementImageExtension">
                        <i-input type="textarea" placeholder="请输入允许管理图片的扩展名" v-model="form.canManagementImageExtension"
                                 :autosize="{minRows: 2,maxRows: 5}"></i-input>
                    </form-item>
                </i-col>
            </row>
            <row>
                <i-col span="12">
                    <form-item label="允许管理文件的扩展名" prop="canManagementFileExtension">
                        <i-input type="textarea" placeholder="请输入允许管理文件的扩展名" v-model="form.canManagementFileExtension"
                                 :autosize="{minRows: 2,maxRows: 5}"></i-input>
                    </form-item>
                </i-col>
            </row>
            <row>
                <i-col span="12">
                    <form-item>
                        <i-button :loading="loading" type="primary" @click.native="submit">
                            <span v-if="!loading">确认提交</span>
                            <span v-else>正在提交…</span>
                        </i-button>
                    </form-item>
                </i-col>
            </row>
        </i-form>
    </card>
</template>