document.addEventListener('alpine:init',()=>{
    Alpine.data('purchaseReturn',() => ({
        allpurchaseReturn:{},

        getData(){
            this.$wire.getData().then((response)=>{
                this.allpurchaseReturn = response;
            }).then((error)=>{
                console.log(error)
            })
        },
    }));
});