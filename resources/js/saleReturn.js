document.addEventListener('alpine:init', () => {
    Alpine.data('saleReturn', () => ({
        allCustomer: [],
        allProduct: [],
        allDiscount: [],
        allSaleReturn: [],
        viewList: true,
        customerInfo: null,
        saleInfo: {},
        message: '',
        error: '',
        errors: {},
        data: {
            customer_id: '',
            sales_date: '',
            expected_date: '',
            payment_method: '',
            notes: '',
            pay: '',
        },
        viewList: true,
        viewUdate: false,
        tempNetAmount: 0,
        showTermModal: false,
        tempDiscounts: [],
        items: [{
            id: '',
            product_id: '',
            quantity: 1,
            rate: 0,
            amount: 0,
            termAmount: 0,
            netAmount: 0
        }],

        init() {
            this.getData();

            this.$watch('data.customer_id', value => {
                this.customerInfo = value ? this.allCustomer.find(v => v.id == value) : null;
            });

            this.$watch('items', value => {
                value.forEach(item => {
                    let q = Number(item.quantity) || 0;
                    let r = Number(item.rate) || 0;
                    item.amount = q * r;
                    item.netAmount = item.amount - item.termAmount;
                });
                // Clear errors when items change
                this.errors = {};
            }, { deep: true });
        },

        timeoutFunc() {
            if (this.message) {
                setTimeout(() => this.message = '', 2000);
            }
            if (this.error) {
                setTimeout(() => this.error = '', 2000)
            }
        },

        getData() {
            this.$wire.getData().then((response) => {
                this.allSaleReturn = response[0];
                this.allCustomer = response[1];
                this.allProduct = response[2];
                this.allDiscount = response[3];
                this.timeoutFunc();
            }).then((error) => {
            })
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
                vm.items[index].rate = product?.selling_price ?? 0;
            });
        },

        initSelect(el, index) {
            $(el).select2();
            $(el).on('change', () => {
                let selectedId = $(el).val();
                this.items[index].product_id = selectedId;
                let product = this.allProduct.find(p => String(p.id) === String(selectedId));
                this.items[index].rate = product?.selling_price || 0;
            });
        },
        viewListToggle() {
            this.viewList = true;
            this.viewUdate = false;
            this.resetData();
        },

        updateListToggle(id) {
            this.viewList = false;
            this.viewUdate = true;
            this.saleInfo = this.allSaleReturn.find(p => p.id == id);

            if (!this.saleInfo) {
                console.error('Purchase not found');
                return;
            }

            this.data.customer_id = this.saleInfo.sale.customer_id;
            this.data.sales_date = this.saleInfo.sale.sales_date;
            this.data.expected_date = this.saleInfo.sale.expected_date;
            this.data.payment_method = this.saleInfo.payment_method;
            this.data.notes = this.saleInfo.notes || '';
            // Check if purchase_items exists and is an array
            this.items = this.saleInfo.sale_return_items.map(i => ({
                id: i.id || '',
                product_id: i.product_id,
                quantity: Number(i.quantity) || 0,
                rate: Number(i.cost_price) || 0,
                amount: Number(i.subTotal) || 0,
                termAmount: Number(i.disount_amt) || 0, // backend typo, keep this spelling unless you fix backend
                netAmount: (Number(i.subTotal) || 0) - (Number(i.disount_amt) || 0)
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

        updateSaleData(id) {
            if (!this.validate()) {
                return
            }

            let payload = {
                id: id,
                sale_id: this.saleInfo.id,
                customer_id: this.data.customer_id,
                notes: this.data.notes || '',
                totalTermAmount: this.totalTermAmount,
                total_amount: this.totalNetAmount,
                total_quantity: this.totalQuantity,
                product_id: this.items.map(i => i.product_id),
                quantity: this.items.map(i => i.quantity),
                selling_price: this.items.map(i => i.rate),
                netAmount: this.items.map(i => i.netAmount),
                termAmount: this.items.map(i => i.termAmount),
            };
            this.$wire.updateSaleReturn(payload).then(() => {
                this.getData();
                this.viewListToggle();
                this.message = 'Sale Return Update Successfull'
                this.resetData();
                this.timeoutFunc();
            }).catch((error) => {
                console.log(error)
            })
            console.log(payload)
        },

        confirmOrder(id){
            if(!this.validate){
                return
            }

            this.$wire.confirmOrder(id).then(()=>{
                this.getData()
                this.viewListToggle();
                this.message = "Sale Return Confirm Successfully"
                this.resetData();
                this.timeoutFunc();
            }).catch((error)=>{
               console.log(error) 
            })
        },

        cancelOrder(id){
            if(!this.validate){
                return
            }

            this.$wire.cancelOrder(id).then(()=>{
                this.getData()
                this.viewListToggle();
                this.message = "Sale Return Cancel Successfully"
                this.resetData();
                this.timeoutFunc();
            }).catch((error)=>{
               console.log(error) 
            })
        },

        resetToDraft(id){
            this.$wire.resetToDraft(id).then(()=>{
                this.message = 'sale reset to draft successfully'
                this.timeoutFunc();
                this.viewListToggle();
                this.getData();
            }).catch((error)=>{
                this.message = 'error while resetting sale to draft';
                this.timeoutFunc();
            });
        },

        validate() {
            this.errors = { quantity: [] };

            this.items.forEach((item, idx) => {
                this.errors.quantity[idx] = null;

                if (!item.product_id) {
                    this.errors.quantity[idx] = 'Please select a product';
                    return;
                }

                const product = this.saleInfo.sale.sales_items.find(p => p.product_id == item.product_id);

                if (!product) {
                    this.errors.quantity[idx] = 'This product was not in the original sale';
                    return;
                }

                const requestedQty = Number(item.quantity) || 0;
                if (requestedQty <= 0) {
                    this.errors.quantity[idx] = 'Quantity must be at least 1';
                    return;
                }

                const availableStock = Number(product.quantity) || 0;
                if (requestedQty > availableStock) {
                    this.errors.quantity[idx] = `Less than ${availableStock} quantity can be returned`;
                }
            });

            const hasRowErrors = this.errors.quantity.some(msg => msg);
            return !hasRowErrors;
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
    }))
})