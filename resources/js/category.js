document.addEventListener('alpine:init', () => {
    Alpine.data('category', () => ({
        data: {
            name: '',
            description: '',
        },
        errors: {},
        message: '',
        createCategory: false,
        categoryList: true,
        updateCategory:false,
        allCategory: {},

        init(){
            this.getData();
        },

        createCategoryToggle() {
            this.createCategory = true;
            this.categoryList = false;
            this.updateCategory = false
        },

        categoryListToggle() {
            this.categoryList = true;
            this.createCategory = false;
            this.updateCategory = false
        },

        getData() {
            this.$wire.call('getData').then((response) => {
                console.log(response);
                this.allCategory = response;
            }).catch((error) => {
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
                    this.categoryListToggle();
                    this.getData();
                    this.data = {};

                }

            }).catch((error) => {
                console.log(error);
            })
        },

        updateChange(id){
            this.updateCategory = true;
            this.createCategory = false;
            this.categoryList = false
            console.log(id)
            const category = this.allCategory.find(cat => cat.id === id);
            if(category){
                this.data ={
                    id:category.id,
                    name:category.name,
                    description: category.description || ''
                };
                console.log(this.data)
            }
        },

        updateData(){
            if(!this.validate()){
                return
            }

            this.$wire.call('updateStore',this.data).then((response)=>{
                this.errors = {};
                if(response.original){
                    Object.entries(response.original).forEach(([key,message])=>{
                        this.errors[key] = message[0];
                    })
                }

                else{
                    this.categoryListToggle();
                    this.message = response;
                    this.timeoutFunc();
                    this.getData();
                    this.data = {};
                }
            }).catch((error)=>{
                console.log(error)
            })
        },
        deleteData(id){
            this.$wire.call('delete',id).then((response)=>{
                this.message = response;
                this.timeoutFunc();
                this.getData();
                this.categoryListToggle();
            }).catch((error)=>{
                console.log(error)
            })
        },
    }))
})