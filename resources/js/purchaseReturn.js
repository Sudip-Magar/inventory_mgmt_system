document.addEventListener('alpine:init', () => {
    Alpine.data('purchaseReturn', () => ({
        allpurchaseReturn: {},
        viewList: true,
        viewUdate: false,
        purchaseInfo: {},
        allVendor: [],
        allProduct: [],
        allDiscount: [],
        data: {
            vendor_id: '',
            order_date: '',
            expected_date: '',
            payment_method: '',
            notes: '',
            pay: '',
        },
        message: '',
        error: '',
        tempNetAmount: 0,
        showTermModal: false,
        tempDiscounts: [],

        vendorInfo: null,
        errors: {},

        items: [{
            id: '',
            product_id: '',
            quantity: 1,
            rate: 0,
            amount: 0,
            termAmount: 0,
            netAmount: 0
        }],

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
                this.allpurchaseReturn = response[0];
                this.allVendor = response[1];
                this.allProduct = response[2];
                this.allDiscount = response[3];

            }).then((error) => {
            })
        },

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

                // Clear errors when items change
                this.errors = {};
            }, { deep: true });
        },

        viewListToggle() {
            this.viewList = true;
            this.viewUdate = false;
            this.resetData();
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

        updateListToggle(id) {
            this.viewList = false;
            this.viewUdate = true;
            this.purchaseInfo = this.allpurchaseReturn.find(p => p.id == id);

            if (!this.purchaseInfo) {
                console.error('Purchase not found');
                return;
            }

            this.data.vendor_id = this.purchaseInfo.purchase.vendor_id;
            this.data.order_date = this.purchaseInfo.purchase.order_date;
            this.data.expected_date = this.purchaseInfo.purchase.expected_date;
            this.data.payment_method = this.purchaseInfo.payment_method;
            this.data.notes = this.purchaseInfo.notes || '';
            // Check if purchase_items exists and is an array
            this.items = this.purchaseInfo.purchase_return_items.map(i => ({
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
        validate() {
            this.errors = {};

            // Get original purchase items
            const originalItems = this.purchaseInfo.purchase.purchase_items || [];

            // Check if current items exceed original purchase items
            this.items.forEach((currentItem, index) => {
                if (currentItem.product_id) {
                    // Find matching original item
                    const originalItem = originalItems.find(orig =>
                        orig.product_id == currentItem.product_id
                    );

                    if (originalItem) {
                        // Check if quantity exceeds original
                        if (Number(currentItem.quantity) > Number(originalItem.quantity)) {
                            this.errors[`item_${index}`] = `Quantity (${currentItem.quantity}) exceeds original purchase quantity (${originalItem.quantity})`;
                        }
                    } else {
                        // Item not in original purchase
                        this.errors[`item_${index}`] = 'This product was not in the original purchase';
                    }
                }
            });

            // Check if total items exceed original
            const totalCurrentQuantity = this.items.reduce((sum, item) =>
                sum + (Number(item.quantity) || 0), 0
            );
            const totalOriginalQuantity = originalItems.reduce((sum, item) =>
                sum + (Number(item.quantity) || 0), 0
            );

            if (totalCurrentQuantity > totalOriginalQuantity) {
                this.errors.total = `Total quantity (${totalCurrentQuantity}) exceeds original purchase total (${totalOriginalQuantity})`;
            }
            return Object.keys(this.errors).length === 0;
        },
        updatePurchaseData(id) {
            if (!this.validate()) {
                this.showValidationErrors();
                return false;
            }
            let payload = {
                id: id,
                purchase_id: this.purchaseInfo.purchase_id,
                note: this.purchaseInfo.return_reason,
                total_amount: this.totalNetAmount,
                total_qty: this.totalQuantity,
                total_term_amt: this.totalTermAmount,
                product_id: this.items.map(i => i.product_id),
                quantity: this.items.map(i => i.quantity),
                cost_price: this.items.map(i => i.rate),
                subTotal: this.items.map(i => i.netAmount),
                termAmount: this.items.map(i => i.termAmount),      // If you want to send term amounts
                netAmount: this.items.map(i => i.netAmount),
            }
            this.$wire.updateData(payload).then((response) => {
                this.getData();
                this.viewListToggle();
                this.message = 'Purchase Return Update Successfull'
                this.resetData();
                this.timeoutFunc();
            }).catch((error) => {
                console.log(error)
            })
        },

        confirmOrder(id) {
            if (!this.validate()) {
                this.showValidationErrors();
                return false;
            }
            this.$wire.confirmOrder(id).then((response) => {
                this.getData();
                this.viewListToggle();
                this.message = 'Purchase Return Confirm Successfull'
                this.resetData();
                this.timeoutFunc();
            }).catch((error) => {
                console.log(error)
            })
        },

        cancelOrder(id) {
            this.$wire.cancelPurchaseReturn(id).then(() => {
                this.message = "purchase Cancel Successfull";
                this.resetData();
                this.getData();
                this.viewListToggle();
                this.timeoutFunc();
            }).catch(() => {
                this.message = 'Error Cancelling Purchase Return'
            })
        },

        resetToDraft(id) {
            this.$wire.call('resetToDraft', id).then((response) => {
                this.message = 'Purchase reset to draft successfully!';
                this.timeoutFunc();
                this.viewListToggle();
                this.getData();
            }).catch((error) => {
                this.message = 'Error resetting purchase to draft!';
                this.timeoutFunc();
            });
        },

        showValidationErrors() {
            // Create error message
            let errorMessage = 'Validation failed:\n';
            Object.values(this.errors).forEach(error => {
                errorMessage += `• ${error}\n`;
            });

            // Show alert or toast
            alert(errorMessage);
        },
    }));
});