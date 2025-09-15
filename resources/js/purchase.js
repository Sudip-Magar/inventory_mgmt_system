document.addEventListener('alpine:init', () => {
    Alpine.data('purchase', () => ({
        message: '',
        allVendor: [],
        allPurchase: [],
        allProduct: [],
        allDiscount: [],
        createPurchase: false,
        purchaseList: true,
        data: {
            vendor_id: '',
            order_date: '',
            expected_date: '',
            payment_method: ''
        },
        vendorInfo: null,

        // Term Amount Modal
        showTermModal: false,
        currentItemIndex: null,
        tempDiscounts: [],
        tempNetAmount: 0,
        baseAmount: 0,

        items: [{
            id: '',
            quantity: 1,
            rate: 0,
            amount: 0,
            termAmount: 0,
            netAmount: 0
        }],

        createPurchaseToggle() {
            this.createPurchase = true;
            this.purchaseList = false;
        },
        purchaseListToggle() {
            this.createPurchase= false;
            this.purchaseList= true;
        },

        timeoutFunc() {
            if (this.message) {
                setTimeout(() => {
                    this.message = '';
                }, 2000)
            }
        },

        init() {
            this.getData();

            this.$watch('data.vendor_id', (value) => {
                this.vendorInfo = value ? this.allVendor.find(v => v.id == value) : null;
            });

            this.$watch('items', (value) => {
                value.forEach((item) => {
                    let q = Number(item.quantity) || 0;
                    let r = Number(item.rate) || 0;
                    item.amount = q * r;
                    item.netAmount = item.amount - item.termAmount;
                });
            }, {
                deep: true
            });
        },

        initSelect(el, index) {
            $(el).select2();
            $(el).on('change', () => {
                let selectedId = $(el).val();
                this.items[index].id = selectedId;
                let product = this.allProduct.find(p => String(p.id) === String(selectedId));
                this.items[index].rate = product?.price ?? 0;
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
            let result = this.baseAmount;
            let totalDiscount = 0;

            this.tempDiscounts.forEach((rate, idx) => {
                let percentageAmt = (rate / 100) * this.baseAmount;
                if (this.allDiscount[idx].sign === '+') {
                    totalDiscount += percentageAmt;
                } else if (this.allDiscount[idx].sign === '-') {
                    totalDiscount -= percentageAmt;
                }
            });

            this.tempNetAmount = this.baseAmount - totalDiscount;
        },



        saveTermAmount() {
            let idx = this.currentItemIndex;
            let totalDiscount = 0;

            this.tempDiscounts.forEach((rate, i) => {
                let percentageAmt = (rate / 100) * this.baseAmount;
                if (this.allDiscount[i].sign === '+') {
                    totalDiscount += percentageAmt;
                } else if (this.allDiscount[i].sign === '-') {
                    totalDiscount -= percentageAmt;
                }
            });

            this.items[idx].termAmount = totalDiscount;
            this.items[idx].netAmount = this.baseAmount - totalDiscount;

            this.closeTermModal();
        },

        closeTermModal() {
            this.showTermModal = false;
            this.currentItemIndex = null;
            this.baseAmount = 0;
        },

        addRow() {
            this.items.push({
                id: '',
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

        getData() {
            this.$wire.call('getdata').then((response) => {
                this.allVendor = response[0];
                this.allPurchase = response[1];
                this.allProduct = response[2];
                this.allDiscount = response[3];
                console.log(this.allPurchase);
                // No need to initialize tempDiscounts here
            });
        },

        // In your Alpine.js component (purchase.js)
        savePurchase() {
            let payload = {
                vendor_id: this.data.vendor_id,
                order_date: this.data.order_date,
                expected_date: this.data.expected_date,
                payment_method: this.data.payment_method,
                notes: this.data.notes || '', // Add notes if you have a field for it
                total_amount: this.totalNetAmount,
                total_quantity: this.totalQuantity,
                product_id: this.items.map(i => i.id),
                quantity: this.items.map(i => i.quantity),
                cost_price: this.items.map(i => i.rate),
                subTotal: this.items.map(i => i.netAmount),
                termAmount: this.items.map(i => i.termAmount),      // If you want to send term amounts
                netAmount: this.items.map(i => i.netAmount),        // If you want to send net amounts
            };

            this.$wire.save(payload)
                .then(() => {
                    this.message = 'Purchase saved successfully!';
                    this.items = [{
                        id: '',
                        quantity: 1,
                        rate: 0,
                        amount: 0,
                        termAmount: 0,
                        netAmount: 0
                    }];
                    this.payload = {}
                })
                .catch(() => this.message = 'Error saving purchase!');
        },

        get totalAmount() {
            return this.items.reduce((sum, i) => sum + (i.amount || 0), 0);
        },

        get totalQuantity() {
            return this.items.reduce((sum, i) => sum + (i.quantity || 0), 0);
        },

        get totalRate() {
            return this.items.reduce((sum, i) => sum + (Number(i.rate) || 0), 0);
        },

        get totalNetAmount() {
            return this.items.reduce((sum, i) => sum + (i.netAmount || 0), 0);
        },

        get totalTermAmount() {
            return this.items.reduce((sum, i) => sum + (Number(i.termAmount) || 0), 0);
        },
    }));
});