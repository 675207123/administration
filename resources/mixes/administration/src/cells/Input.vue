<script>
    export default {
        props: {
            index: 0,
            key: '',
            list: [],
        },
    };
</script>
<template>
    <i-input v-model.lazy="list[index][key]"></i-input>
</template>