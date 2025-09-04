document.addEventListener('alpine:init', () => {
    Alpine.data('purchase', () => ({
        message: '',
        allVendor: [],
        allPurchase: [],
        allProduct: [],
        data: {
            vendor_id: '',
            order_date: '',
            expected_date: '',
            payment_method: '',
        },
        vendorInfo: null,
        search: '',
        productInfo: [],
        items: [],
        eachStock: [],

        get totalQuantity() {
            return this.eachStock.reduce((sum, q) => sum + (q || 0), 0);
        },
        get totalAmount() {
            return this.items.reduce((sum, item, index) =>
                sum + (item.price * (this.eachStock[index] || 0)), 0);
        },

        init() {
            this.getData();

            this.$watch('data.vendor_id', (value) => {
                this.vendorInfo = value
                    ? this.allVendor.find(v => v.id == value)
                    : null;
            });

            this.$watch('search', (value) => {
                if (value) {
                    const products = this.allProduct.filter(p =>
                        p.name.toLowerCase().includes(value.toLowerCase()) ||
                        (p.code && p.code.toLowerCase().includes(value.toLowerCase()))
                    ).slice(0, 8);
                    this.productInfo = products;
                } else {
                    this.productInfo = [];
                }
            });
        },

        addProduct(id) {
            const product = this.allProduct.find(p => p.id == id);
            if (product && !this.items.find(i => i.id == id)) {
                this.items.push(product);
                this.eachStock.push(1); // default quantity
            }
            this.search = '';
            this.productInfo = [];
        },

        getData() {
            this.$wire.call('getdata').then((response) => {
                this.allVendor = response[0];
                this.allPurchase = response[1];
                this.allProduct = response[2];
            }).catch((error) => {
                console.error(error);
            });
        },

        savePurchase() {
            let payload = {
                vendor_id: this.data.vendor_id,
                order_date: this.data.order_date,
                expected_date: this.data.expected_date,
                payment_method: this.data.payment_method,
                notes: this.data.notes,

                total_amount: this.totalAmount,
                total_quantity: this.totalQuantity,

                product_id: this.items.map(i => i.id),
                quantity: this.eachStock,
                cost_price: this.items.map(i => i.price),
                subTotal: this.items.map((i, index) => (this.eachStock[index] || 0) * i.price),
            };

            console.log('Sending to Livewire:', payload);

            this.$wire.call('store', payload)
                .then(() => {
                    this.message = 'Purchase saved successfully!';
                    this.items = [];
                    this.eachStock = [];
                })
                .catch(error => {
                    console.error(error);
                    this.message = 'Error saving purchase!';
                });
        },
    }))
});
