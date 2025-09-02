document.addEventListener('alpine:init', () => {
    Alpine.data('vendor', () => ({
        message: '',
        data: {
            name: '',
            email: '',
            address: '',
            company: '',
            phone: '',
        },
        errors: {},
        allVendor: {},
        createVendor: false,
        vendorList: true,
        updateVendor: false,

        init() {
            this.getData();
        },

        timeoutFunc() {
            if (this.message) {
                setTimeout(() => {
                    this.message = '';
                }, 2000)
            }
        },

        getData() {
            this.$wire.call('getData').then((response) => {
                this.allVendor = response;
            }).catch((error) => {
                console.log(error)
            })
        },

        createVendorToggle() {
            this.createVendor = true;
            this.vendorList = false;
            this.updateVendor = false;
            this.data = {};

        },

        VendorListToggle() {
            this.createVendor = false;
            this.vendorList = true;
            this.updateVendor = false;
        },

        updateVendorToggle(id) {
            this.createVendor = false;
            this.vendorList = false;
            this.updateVendor = true;

            const vendor = this.allVendor.find(vendor => vendor.id === id);
            console.log(vendor)
            this.data = vendor;
        },

        validate() {
            this.errors = {};
            if (!this.data.name) {
                this.errors.name = "Vendor name is Required"
            }
            else if (this.data.name.length < 3) {
                this.errors.name = "Vendor name must be atleast 3 character long";
            }
            else if (this.data.name.length > 20) {
                this.errors.name = "Vendor name must not be more than 20 character long";
            }

            if (!this.data.email) {
                this.errors.email = "Vendor Email is Required"
            }

            if (!this.data.address) {
                this.errors.address = "Vendor address is Required"
            }
            else if (this.data.address.length < 3) {
                this.errors.address = "Vendor address must be atleast 3 character long";
            }
            else if (this.data.address.length > 50) {
                this.errors.address = "Vendor address must not be more than 50 character long";
            }

            if (!this.data.phone) {
                this.errors.phone = "Vendor phone number is Required"
            }

            if (!this.data.company) {
                this.errors.company = "Vendor company is Required"
            }
            else if (this.data.company.length < 3) {
                this.errors.company = "Vendor company must be atleast 3 character long";
            }
            else if (this.data.company.length > 50) {
                this.errors.company = "Vendor company must not be more than 50 character long";
            }

            return Object.keys(this.errors).length === 0

        },

        store() {
            if (!this.validate()) {
                return;
            }

            this.$wire.call('storeData', this.data).then((response) => {
                this.errors = {};
                if (response.original) {
                    Object.entries(response.original).forEach(([key, message]) => {
                        this.errors[key] = message[0];
                    })
                }
                else {
                    this.message = response;
                    this.data = {};
                    this.timeoutFunc();
                    this.VendorListToggle();
                    this.getData();

                }
            }).catch((error) => {
                console.log(error);
            })
        },

        updateVendordata() {
            if (!this.validate()) {
                return
            }
            this.$wire.call('updateVendordata', this.data).then((response) => {
                this.errors = {};
                if (response.original) {
                    Object.entries(response.original).forEach(([key, message]) => {
                        this.errors[key] = message[0];
                    })
                }
                else {
                    this.message = response;
                    this.data = {};
                    this.timeoutFunc();
                    this.VendorListToggle();
                    this.getData();
                }
            }).catch((error) => {
                console.log(error);
            })
        },

         DeleteVendor(id) {
            this.$wire.call('DeleteVendor', id).then((response) => {
                this.message = response;
                this.data = {};
                this.timeoutFunc();
                this.VendorListToggle();
                this.getData();
            }).catch((error)=>{
                console.log(error)
            })
        },
    }))
})