document.addEventListener('alpine:init', () => {
    Alpine.data('sale', () => ({
        message: '',
        error: '',
        errors: {},
        allCustomer: [],
        allSale: [],
        allProduct: [],
        allDiscount: [],
        showTermModal: false,
        tempNetAmount: 0,
        tempDiscounts: [],
        createView: false,
        listView: true,
        updateView: false,
        saleInfo: {},
        data: {
            customer_id: '',
            sales_date: '',
            expected_date: '',
            payment_method: '',
            notes: '',
            pay: '',
        },
        items: [{
            id: '',
            product_id: '',
            quantity: 1,
            rate: 0,
            amount: 0,
            termAmount: 0,
            netAmount: 0
        }],
        customerInfo: null,

        createViewToggle() {
            this.createView = true;
            this.listView = false;
            this.updateView = false;
        },
        listViewToggle() {
            this.createView = false;
            this.listView = true;
            this.updateView = false;
        },
        updateViewToggle(id) {
            this.createView = false;
            this.listView = false;
            this.updateView = true;
            this.resetData();

            this.saleInfo = this.allSale.find(p => p.id == id);
            if (!this.saleInfo) return;

            this.data.customer_id = this.saleInfo.customer_id;
            this.data.sales_date = this.saleInfo.sales_date;
            this.data.expected_date = this.saleInfo.expected_date;
            this.data.payment_method = this.saleInfo.payment_method;
            this.data.notes = this.saleInfo.notes || '';

            this.items = this.saleInfo.sales_items.map(i => ({
                id: i.id || '',
                product_id: i.product_id,
                quantity: i.quantity,
                rate: Number(i.selling_price) || 0,
                amount: Number(i.subTotal) || 0,
                termAmount: Number(i.dicount_amt) || 0,
                netAmount: Number(i.netAmount) || 0,
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

        init() {
            this.getData();

            this.$watch('data.customer_id', value => {
                this.customerInfo = value ? this.allCustomer.find(c => c.id == value) : null;
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

        timeoutFunc() {
            if (this.message) {
                setTimeout(() => this.message = '', 2000);
            }
            if (this.error) {
                setTimeout(() => this.error = '', 2000)
            }

            if (this.errors) {
                setTimeout(() => this.errors = {}, 3000)
            }
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

        getData() {
            this.$wire.getData().then((response) => {
                this.allCustomer = response[0];
                this.allSale = response[1];
                this.allProduct = response[2];
                this.allDiscount = response[3];
            })
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
            this.data = { customer_id: '', sales_date: '', expected_date: '', payment_method: '', notes: '' };
        },

        validation() {
            this.errors = { quantity: [] };

            this.items.forEach((item, idx) => {
                this.errors.quantity[idx] = null;

                // Product selection validation
                if (!item.product_id) {
                    this.errors.quantity[idx] = 'Please select a product';
                    return;
                }

                const product = this.allProduct.find(p => String(p.id) === String(item.product_id));
                if (!product) {
                    this.errors.quantity[idx] = 'Selected product not found';
                    return;
                }

                const requestedQty = Number(item.quantity) || 0;
                if (requestedQty <= 0) {
                    this.errors.quantity[idx] = 'Quantity must be at least 1';
                    return;
                }

                const availableStock = Number(product.stock) || 0;
                if (requestedQty > availableStock) {
                    this.errors.quantity[idx] = `${product.name} has only ${availableStock} left`;
                }
            });

            const hasRowErrors = this.errors.quantity.some(msg => msg);
            return !hasRowErrors;
        },

        saveSale() {
            if (!this.validation()) {
                this.timeoutFunc();
                return;
            }
            let payload = {
                customer_id: this.data.customer_id,
                sales_date: this.data.sales_date,
                expected_date: this.data.expected_date,
                payment_method: this.data.payment_method,
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
            this.$wire.saveSale(payload)
                .then(() => {
                    this.message = 'Sale Created successfully!';
                    this.resetData();
                    this.getData();
                    this.timeoutFunc();
                })
                .catch(() => this.message = 'Error saving Sale!');
        },

        updateSaleData(id) {
            if (!this.validation()) {
                this.timeoutFunc();
                return;
            }
            let payload = {
                customer_id: this.data.customer_id,
                sales_date: this.data.sales_date,
                expected_date: this.data.expected_date,
                payment_method: this.data.payment_method,
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

            this.$wire.updateSale(id, payload).then((response) => {
                this.resetData();
                this.message = "Sale Update Successfully";
                this.listViewToggle();
                this.timeoutFunc();
                this.getData();
            }).catch((error) => {
                console.log(error)
            })

        },

        confirmOrder(id){
            
        },

    }));
});