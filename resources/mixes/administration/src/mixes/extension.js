export default function (injection) {
    const list = [];

    Object.keys(injection.extensions).forEach(index => {
        const module = injection.modules[index];
        injection.use(module);
        list.push(module);
    });

    Object.defineProperties(injection, {
        extensions: {
            get() {
                return list;
            },
        },
    });
}