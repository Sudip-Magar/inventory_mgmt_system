document.addEventListener('alpine:init', () => {
    Alpine.data('product', () => ({
        message: '',
        datas: {
            code: '',
            name: '',
            price: '',
            description: '',
            cost: '',
            stock: '',
            category_id: '',
        },
        errors: {},
        message: '',
        allProduct: {},
        allCategory: [],
        createProduct: false,
        updateProduct: false,
        productList: true,


        init() {
            this.getData()
        },

        timeoutFunc() {
            if (this.message) {
                setTimeout(() => {
                    this.message = '';
                }, 2000)
            }
        },

        getData() {
            this.$wire.call('getdata').then((response) => {
                console.log(response)
                this.allCategory = response[0];
                this.allProduct = response[1];
                console.log(this.allCategory)
            }).catch((error) => {
                console.log(error)
            })
        },

        createProductToggle() {
            this.createProduct = true;
            this.updateProduct = false;
            this.productList = false;
            this.datas ={};
        },

        productListToggle() {
            this.createProduct = false;
            this.updateProduct = false;
            this.productList = true;
        },

        updateProductToggle(id) {
            this.createProduct = false;
            this.updateProduct = true;
            this.productList = false;

            const product = this.allProduct.find(prod => prod.id === id);
            console.log(product)
            if (product) {
                this.datas = product
            }

        },

        updateProductDetail() {
            if (!this.validation()) {
                return
            }
            this.$wire.call('updateProduct', this.datas).then((response) => {
                this.errors = {};
                if (response.original) {
                    Object.entries(response.original).forEach(([key, message]) => {
                        this.errors[key] = message[0];
                    })
                }
                else {
                    this.message = response;
                    this.datas = {};
                    this.getData();
                    this.timeoutFunc();
                    this.productListToggle();
                }
            }).catch((error) => {
                console.log(error)
            })
        },

        deleteProduct(id) {
            this.$wire.call('deleteProduct', id).then((response) => {
                this.message = response;
                this.datas = {};
                this.getData();
                this.timeoutFunc();
                this.productListToggle();
            }).catch((error) => {
                console.log(error);
            })
        },

        validation() {
            this.errors = {};
            if (!this.datas.code) {
                this.errors.code = "Product Code is Required"
            }
            else if (this.datas.code.length < 2) {
                this.errors.code = "product Code must be atleast 2 character long";
            }
            else if (this.datas.code.length > 10) {
                this.errors.code = "product code must not be more than 10 character long";
            }

            if (!this.datas.name) {
                this.errors.name = "Product name is Required"
            }
            else if (this.datas.name.length < 3) {
                this.errors.name = "product name must be atleast 3 character long";
            }
            else if (this.datas.name.length > 20) {
                this.errors.name = "product name must not be more than 20 character long";
            }

            if (!this.datas.price) {
                this.errors.price = "Product price is Required"
            }

            if (!this.datas.description) {
                this.errors.description = "Product description is Required"
            }
            else if (this.datas.description.length < 3) {
                this.errors.description = "product description must be atleast 3 character long";
            }
            else if (this.datas.description.length > 50) {
                this.errors.description = "product description must not be more than 50 character long";
            }

            if (!this.datas.cost) {
                this.errors.cost = "Product cost is Required"
            }

            if (!this.datas.category_id) {
                this.errors.category_id = "Category is Required"
            }

            return Object.keys(this.errors).length === 0

        },

        store() {
            if (!this.validation()) {
                return;
            }
            this.$wire.call('storeData', this.datas).then((response) => {
                this.errors = {};
                if (response.original) {
                    Object.entries(response.original).forEach(([key, message]) => {
                        this.errors[key] = message[0];
                    })
                }
                else {
                    this.message = response;
                    this.timeoutFunc();
                    this.datas = {};
                    this.getData();
                    this.productListToggle()
                }
            }).catch((error) => {
                console.log(error)
            })
        },
    }))
})