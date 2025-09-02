document.addEventListener('alpine:init', () => {
    Alpine.data('purchase', () => ({
        message: '',
        allVendor:{},
        allPurchase:{},
        allProduct:{},
        data:{
            vendor_id:'',
        },

        init(){
            this.getData();
        },

        getData() {
            this.$wire.call('getdata').then((response) => {
                // console.log(response)
                this.allVendor = response[0];
                this.allPurchase = response[1];
                this.allProduct = response[2];
            }).catch((error) => {
                console.log(error)
            })
        },
    }))
})