export default {
    props: {
        value: String,
        theme: {
            type: String,
            default: 'vs',
        },
        language: String,
        options: Object,
        placeholder: null,
    },
    model: {
        event: 'change',
    },
    data() {
        return {
            editorLoaded: false,
        };
    },
    watch: {
        options: {
            deep: true,
            handler(options) {
                if (this.editor) {
                    this.editor.updateOptions(options);
                }
            },
        },
        value(newValue) {
            if (this.editor && newValue !== this.editor.getValue()) {
                this.editor.setValue(newValue);
            }
        },
        language(newVal) {
            if (this.editor) {
                window.monaco.editor.setModelLanguage(this.editor.getModel(), newVal);
            }
        },
        theme(newVal) {
            if (this.editor) {
                window.monaco.editor.setTheme(newVal);
            }
        },
    },
    mounted() {
        const self = this;
        const options = {
            value: self.value,
            theme: self.theme,
            language: self.language,
            ...self.options,
        };
        window.require(['vs/editor/editor.main'], () => {
            self.editorLoaded = true;
            self.editor = window.monaco.editor.create(self.$el, options);
            self.$emit('editorMount', self.editor);
            self.editor.onContextMenu(event => self.$emit('contextMenu', event));
            self.editor.onDidBlurEditor(() => self.$emit('blur'));
            self.editor.onDidBlurEditorText(() => self.$emit('blurText'));
            self.editor.onDidChangeConfiguration(event => {
                self.$emit('configuration', event);
            });
            self.editor.onDidChangeCursorPosition(event => {
                self.$emit('position', event);
            });
            self.editor.onDidChangeCursorSelection(event => {
                self.$emit('selection', event);
            });
            self.editor.onDidChangeModel(event => self.$emit('model', event));
            self.editor.onDidChangeModelContent(event => {
                const value = self.editor.getValue();
                if (self.value !== value) {
                    self.$emit('change', value, event);
                }
            });
            self.editor.onDidChangeModelDecorations(event => {
                self.$emit('modelDecorations', event);
            });
            self.editor.onDidChangeModelLanguage(event => {
                this.$emit('modelLanguage', event);
            });
            self.editor.onDidChangeModelOptions(event => {
                self.$emit('modelOptions', event);
            });
            self.editor.onDidDispose(event => self.$emit('afterDispose', event));
            self.editor.onDidFocusEditor(() => self.$emit('focus'));
            self.editor.onDidFocusEditorText(() => self.$emit('focusText'));
            self.editor.onDidLayoutChange(event => self.$emit('layout', event));
            self.editor.onDidScrollChange(event => self.$emit('scroll', event));
            self.editor.onKeyDown(event => self.$emit('keydown', event));
            self.editor.onKeyUp(event => self.$emit('keyup', event));
            self.editor.onMouseDown(event => self.$emit('mouseDown', event));
            self.editor.onMouseLeave(event => self.$emit('mouseLeave', event));
            self.editor.onMouseMove(event => self.$emit('mouseMove', event));
            self.editor.onMouseUp(event => self.$emit('mouseUp', event));
        });
    },
    beforeDestroy() {
        const self = this;
        if (self.editor) {
            self.editor.dispose();
        }
    },
    methods: {
        getMonaco() {
            return this.editor;
        },
        focus() {
            this.editor.focus();
        },
    },
    render(h) {
        return h('div', null, [this.editorLoaded ? null : this.placeholder]);
    },
};