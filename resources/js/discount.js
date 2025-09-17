document.addEventListener('alpine:init', () => {
    Alpine.data('discount', () => ({
        create: false,
        list: true,

        listToggle() {
            this.list = true;
            this.create = false;
        },
        createToggle() {
            this.list = false;
            this.create = true;
        },
    }))
})  