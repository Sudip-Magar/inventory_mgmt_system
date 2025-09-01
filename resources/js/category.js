document.addEventListener('alpine:init', () => {
    Alpine.data('category', () => ({
        data: {
            name: '',
            description: '',
        },
        errors: {},
        message: '',
        createCategory: false,
        categoryList:true,

        createCategoryToggle(){
            this.createCategory = true;
            this.categoryList = false
        },

        categoryListToggle(){
            this.categoryList = true;
            this.createCategory = false;
        },

        getData() {
            this.$wire.call('   ').then((response) => {
                console.log(response)
            }).catch((error) => {
                console.log(error)
            })
        },

        timeoutFunc(){
            if(this.message){
                setTimeout(()=>{
                    this.message = '';
                },2000)
            }
        },

        validate() {
            this.errors = {};
            if (!this.data.name) {
                this.errors.name = 'Name is required'
            }
            else if (this.data.name.length < 1) {
                this.errors.name = "name should be atleast 3 character long"
            }
            else if (this.data.name.length > 25) {
                this.errors.name = "name should be not more the 25 character long"
            }

            return Object.keys(this.errors).length === 0;
        },

        store() {
            if (!this.validate()) {
                return;
            }

            this.errors = {};

            this.$wire.call('storeData', this.data).then((response) => {
                console.log(response)
                if (response.original) {
                    Object.entries(response.original).forEach(([key, message]) => {
                        this.errors[key] = message[0];
                    })
                }
                else {
                    this.message = response;
                    this.timeoutFunc();
                    this.data = {};

                }

            }).catch((error) => {
                console.log(error);
            })
        },

    }))
})