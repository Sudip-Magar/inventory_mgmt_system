document.addEventListener('alpine:init', () => {
    Alpine.data('purchase', () => ({
        message: '',
        error: '',
        allVendor: [],
        allPurchase: [],
        allProduct: [],
        allDiscount: [],
        createPurchase: false,
        purchaseList: true,
        updatePurchase: false,
        data: {
            vendor_id: '',
            order_date: '',
            expected_date: '',
            payment_method: '',
            notes: '',
            pay: '',
        },
        return_reason:'',
        vendorInfo: null,
        purchaseInfo: {},

        // Term Amount Modal
        showTermModal: false,
        currentItemIndex: null,
        tempDiscounts: [],
        tempNetAmount: 0,
        baseAmount: 0,

        items: [{
            id: '',
            product_id: '',
            quantity: 1,
            rate: 0,
            amount: 0,
            termAmount: 0,
            netAmount: 0
        }],

        // ------------------- Toggles -------------------
        createPurchaseToggle() {
            this.createPurchase = true;
            this.purchaseList = false;
            this.updatePurchase = false;
            this.resetData();
            this.$nextTick(() => {
                document.querySelectorAll('.js-example-basic-single').forEach((el) => {
                    $(el).val('').trigger('change');
                });
            });
        },

        purchaseListToggle() {
            this.createPurchase = false;
            this.purchaseList = true;
            this.updatePurchase = false;
            this.resetData();
        },

        updatePurchaseToggle(id) {
            this.createPurchase = false;
            this.purchaseList = false;
            this.updatePurchase = true;
            this.resetData();

            this.purchaseInfo = this.allPurchase.find(p => p.id === id);
            if (!this.purchaseInfo) return;

            this.data.vendor_id = this.purchaseInfo.vendor_id;
            this.data.order_date = this.purchaseInfo.order_date;
            this.data.expected_date = this.purchaseInfo.expected_date;
            this.data.payment_method = this.purchaseInfo.payment_method;
            this.data.notes = this.purchaseInfo.notes || '';

            this.items = this.purchaseInfo.purchase_items.map(i => ({
                id: i.id || '',
                product_id: i.product_id,
                quantity: i.quantity,
                rate: Number(i.cost_price) || 0,
                amount: Number(i.subTotal) || 0,
                termAmount: Number(i.disount_amt) || 0,
                netAmount: Number(i.netAmount) || 0
            }));

            // Initialize Select2 after updating items
            this.$nextTick(() => {
                document.querySelectorAll('.js-example-basic-single').forEach((el, index) => {
                    $(el).select2();
                    if (this.items[index]) {
                        $(el).val(this.items[index].product_id).trigger('change');
                    }
                });
            });
        },

        resetData() {
            this.items = [{
                id: '',
                product_id: '',
                quantity: 1,
                rate: 0,
                amount: 0,
                termAmount: 0,
                netAmount: 0
            }];
            this.data = { vendor_id: '', order_date: '', expected_date: '', payment_method: '', notes: '' };
        },

        // ------------------- Initialization -------------------
        init() {
            this.getData();

            this.$watch('data.vendor_id', value => {
                this.vendorInfo = value ? this.allVendor.find(v => v.id == value) : null;
            });

            this.$watch('items', value => {
                value.forEach(item => {
                    let q = Number(item.quantity) || 0;
                    let r = Number(item.rate) || 0;
                    item.amount = q * r;
                    item.netAmount = item.amount - item.termAmount;
                });
            }, { deep: true });
        },

        initSelected(el, index) {
            let vm = this;
            $(el).select2();
            // Set initial value from Alpine state
            this.$nextTick(() => {
                setTimeout(() => {
                    document.querySelectorAll('.js-example-basic-single').forEach((el, index) => {
                        $(el).select2();
                        if (this.items[index]) {   // <-- check here
                            $(el).val(this.items[index].product_id).trigger("change");
                        }
                    });
                }, 50);
            });

            // Sync Alpine when Select2 changes
            $(el).on("change", function () {
                let selectedId = $(this).val();
                vm.items[index].product_id = selectedId;

                // Optional: update rate when product changes
                let product = vm.allProduct.find(p => String(p.id) === String(selectedId));
                vm.items[index].rate = product?.price ?? 0;
            });
        },



        initSelect(el, index) {
            $(el).select2();
            $(el).on('change', () => {
                let selectedId = $(el).val();
                this.items[index].product_id = selectedId;
                let product = this.allProduct.find(p => String(p.id) === String(selectedId));
                this.items[index].rate = product?.price || 0;
            });
        },

        // ------------------- Term Modal -------------------
        openTermModal(index) {
            this.currentItemIndex = index;
            this.baseAmount = this.items[index].amount;
            this.tempDiscounts = this.allDiscount.map(d => d.rate || 0);
            this.recalculateTempAmount();
            this.showTermModal = true;
        },

        recalculateTempAmount() {
            let totalDiscount = 0;
            this.tempDiscounts.forEach((rate, idx) => {
                let percentageAmt = (rate / 100) * this.baseAmount;
                this.allDiscount[idx].sign === '+' ? totalDiscount += percentageAmt : totalDiscount -= percentageAmt;
            });
            this.tempNetAmount = this.baseAmount - totalDiscount;
        },

        saveTermAmount() {
            let idx = this.currentItemIndex;
            let totalDiscount = 0;

            this.tempDiscounts.forEach((rate, i) => {
                let percentageAmt = (rate / 100) * this.baseAmount;
                this.allDiscount[i].sign === '+' ? totalDiscount += percentageAmt : totalDiscount -= percentageAmt;
            });

            this.items[idx].termAmount = Math.abs(totalDiscount);
            this.items[idx].netAmount = this.baseAmount - totalDiscount;

            this.closeTermModal();
        },

        closeTermModal() {
            this.showTermModal = false;
            this.currentItemIndex = null;
            this.baseAmount = 0;
        },

        // ------------------- Rows -------------------
        addRow() {
            this.items.push({
                id: '',
                product_id: '',
                quantity: 1,
                rate: 0,
                amount: 0,
                termAmount: 0,
                netAmount: 0
            });
        },

        removeRow(index) {
            this.items.splice(index, 1);
        },

        // ------------------- Data Fetch -------------------
        getData() {
            this.$wire.call('getdata').then(response => {
                this.allVendor = response[0];
                this.allPurchase = response[1];
                this.allProduct = response[2];
                this.allDiscount = response[3];
            });
        },

        // ------------------- Purchase -------------------
        savePurchase() {
            let payload = {
                vendor_id: this.data.vendor_id,
                order_date: this.data.order_date,
                expected_date: this.data.expected_date,
                payment_method: this.data.payment_method,
                notes: this.data.notes || '', // Add notes if you have a field for it
                totalTermAMount: this.totalTermAmount,
                total_amount: this.totalNetAmount,
                total_quantity: this.totalQuantity,
                product_id: this.items.map(i => i.product_id),
                quantity: this.items.map(i => i.quantity),
                cost_price: this.items.map(i => i.rate),
                subTotal: this.items.map(i => i.netAmount),
                termAmount: this.items.map(i => i.termAmount),      // If you want to send term amounts
                netAmount: this.items.map(i => i.netAmount),        // If you want to send net amounts
            };


            this.$wire.save(payload)
                .then(() => {
                    this.message = 'Purchase saved successfully!';
                    this.resetData();
                    this.getData();
                    this.purchaseListToggle();
                    this.timeoutFunc();
                })
                .catch(() => this.message = 'Error saving purchase!');
        },

        cancelOrder(id) {
            this.$wire.call('cancelOrder', id).then(() => {
                this.message = 'Purchase Cancelled successfully!';
                this.resetData();
                this.getData();
                this.purchaseListToggle();
                this.timeoutFunc();
            })
                .catch(() => this.message = 'Error saving purchase!');
        },

        confirmOrder(id) {
            this.$wire.call('confirmOrder', id).then((response) => {
                if (response) {
                    this.message = 'Order Confirmed Successfully!';
                    this.timeoutFunc();
                    this.purchaseListToggle();
                    this.getData();
                }
                else {
                    this.error = 'payment is more than total amount!!';
                    this.timeoutFunc();

                }
            }).catch((error) => {
                this.error = 'Error confirming order!';
                this.timeoutFunc();
            });
        },

        timeoutFunc() {
            if (this.message) {
                setTimeout(() => this.message = '', 2000);
            }
            if (this.error) {
                setTimeout(() => this.error = '', 2000)
            }
        },


        updatePurchaseData(id) {
            let payload = {
                vendor_id: this.data.vendor_id,
                order_date: this.data.order_date,
                expected_date: this.data.expected_date,
                payment_method: this.data.payment_method,
                notes: this.data.notes || '', // Add notes if you have a field for it
                totalTermAMount: this.totalTermAmount,
                total_amount: this.totalNetAmount,
                total_quantity: this.totalQuantity,
                product_id: this.items.map(i => i.product_id),
                quantity: this.items.map(i => i.quantity),
                cost_price: this.items.map(i => i.rate),
                subTotal: this.items.map(i => i.netAmount),
                termAmount: this.items.map(i => i.termAmount),      // If you want to send term amounts
                netAmount: this.items.map(i => i.netAmount),        // If you want to send net amounts
            };

            this.$wire.call('updatePurchase', id, payload).then((response) => {
                this.resetData();
                this.message = "Purchase Update Successfully"
                this.purchaseListToggle();
                this.timeoutFunc();
                this.getData();
            }).catch((error) => {

            });
        },

        resetToDraft(id) {
            this.$wire.call('resetToDraft', id).then((response) => {
                this.message = 'Purchase reset to draft successfully!';
                this.timeoutFunc();
                this.purchaseListToggle();
                this.getData();
            }).catch((error) => {
                this.message = 'Error resetting purchase to draft!';
                this.timeoutFunc();
            });
        },

        // ------------------- Computed -------------------
        get totalAmount() {
            return this.items.reduce((sum, i) => sum + (i.amount || 0), 0);
        },

        get totalQuantity() {
            return this.items.reduce((sum, i) => sum + (Number(i.quantity) || 0), 0);
        },

        get totalRate() {
            return this.items.reduce((sum, i) => sum + (Number(i.rate) || 0), 0);
        },

        get totalNetAmount() {
            return this.items.reduce((sum, i) => sum + (i.netAmount || 0), 0);
        },

        get totalTermAmount() {
            return this.items.reduce((sum, i) => sum + Math.abs(Number(i.termAmount) || 0), 0);

        },

        purchaseReturn(id){
            console.log(id)
            let payload={
                purchase_id: id,
                reason: this.return_reason,
                total_net_amount: this.totalNetAmount,
                total_qty: this.totalQuantity,
                total_term_amt: this.totalTermAmount,
                product_id : this.items.map(p=> p.product_id),
                quantity:this.items.map(p=>p.quantity),
                rate:this.items.map(p=>p.rate),
                netAmount: this.items.map(p=>p.netAmount),
                termAmount: this.items.map(p=>p.termAmount),
            }
            console.log(payload)
            this.$wire.createPurchaseReturn(payload).then((response)=>{
            }).then((error)=>{
                console.log(error)
            })
        },

    }));
});
