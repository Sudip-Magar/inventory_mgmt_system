document.addEventListener('alpine:init', () => {
    Alpine.data('customer', () => ({
        datas: {
            name: '',
            email: '',
            address: '',
            phone: '',
        },
        errors: {},
        message: '',
        createCustomer: false,
        CustomerList: true,
        updateCustomer: false,
        allCustomer: {},

        init() {
            this.getData();
        },
        getData() {
            this.$wire.call('getData').then((response) => {
                this.allCustomer = response;
            }).catch((error) => {
                console.log(error)
            })
        },

        createProductToggle() {
            this.createCustomer = true;
            this.CustomerList = false;
            this.updateCustomer = false;
            this.datas ={};
            
        },

        productListToggle() {
            this.createCustomer = false;
            this.CustomerList = true;
            this.updateCustomer = false;
        },

        updateProductToggle(id) {
            this.createCustomer = false;
            this.CustomerList = false;
            this.updateCustomer = true;

            const customer = this.allCustomer.find(customer => customer.id === id);
            this.datas = customer;
        },

        updateCustomerdata() {
            if (!this.validate()) {
                return
            }
            this.$wire.call('updateCustomerdata', this.datas).then((response) => {
                this.errors = {};
                if (response.original) {
                    Object.entries(response.original).forEach(([key, message]) => {
                        this.errors[key] = message[0];
                    })
                }
                else {
                    this.message = response;
                    this.datas = {};
                    this.timeoutFunc();
                    this.productListToggle();
                    this.getData();
                }
            }).catch((error) => {
                console.log(error);
            })
        },

        DeleteProduct(id) {
            this.$wire.call('DeleteProduct', id).then((response) => {
                this.message = response;
                this.datas = {};
                this.timeoutFunc();
                this.productListToggle();
                this.getData();
            }).catch((error)=>{
                console.log(error)
            })
        },

        timeoutFunc() {
            if (this.message) {
                setTimeout(() => {
                    this.message = '';
                }, 2000)
            }
        },

        validate() {
            this.errors = {};
            if (!this.datas.name) {
                this.errors.name = "Customer name is Required"
            }
            else if (this.datas.name.length < 3) {
                this.errors.name = "Customer name must be atleast 3 character long";
            }
            else if (this.datas.name.length > 20) {
                this.errors.name = "Customer name must not be more than 20 character long";
            }

            if (!this.datas.email) {
                this.errors.email = "Customer Email is Required"
            }

            if (!this.datas.address) {
                this.errors.address = "Customer address is Required"
            }
            else if (this.datas.address.length < 3) {
                this.errors.address = "Customer address must be atleast 3 character long";
            }
            else if (this.datas.address.length > 50) {
                this.errors.address = "Customer address must not be more than 50 character long";
            }

            if (!this.datas.phone) {
                this.errors.phone = "Customer phone number is Required"
            }

            return Object.keys(this.errors).length === 0

        },
        store() {
            if (!this.validate()) {
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
                    this.datas = {};
                    this.timeoutFunc();
                    this.productListToggle();
                    this.getData();

                }
            }).catch((error) => {
                console.log(error);
            })
        },
    }))
})