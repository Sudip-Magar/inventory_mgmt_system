<div x-data="category">
    <template x-if="message">
        <div class="message bg-success " x-text="message"></div>
    </template>
    <nav>
        <ul class="d-flex list gap-3 bg-success py-1 px-3 rounded">
            <li @click.prevent="categoryListToggle" :class="categoryList ? 'text-warning' : 'text-white'">Category List
            </li>
            <span class="border border-white"></span>
            <li @click.prevent="createCategoryToggle" :class="createCategory ? 'text-warning' : 'text-white'">Create
                Category</li>
        </ul>
    </nav>

    <div>
        {{-- category list --}}
        <div class="card" x-show="categoryList">
            <div class="card-header">Category List</div>
            <div class="card-body">
                <table class="table table-bordered table-striped table-hover">
                    <thead>
                        <tr>
                            <th>S.No</th>
                            <th>Name</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="(category,index) in allCategory">
                            <tr :key="category.id">
                                <td x-text="index+1" style="width: 20%;"></td>
                                <td x-text="category.name"></td>
                                <th style="width:20%">
                                    <button @click.prevent="updateChange(category.id)" class="btn btn-primary">Edit</button>
                                    <button @click.prevent="deleteData(category.id)" class="btn btn-danger">Delete</button>
                                </th>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>
        {{-- Create Category --}}
        <div class="card" x-show="createCategory">
            <div class="card-header">
                Create New Category
            </div>
            <div class="card-body">
                <form action="" @submit.prevent="store">
                    <div class="mb-2">
                        <label class="form-label" for="name">Name</label>
                        <input type="text" id="name" class="form-control" placeholder="Enter your name"
                            x-model="data.name">
                        <template x-if="errors.name">
                            <span class="text-danger" x-text="errors.name"></span>
                        </template>

                    </div>

                    <div class="mb-2">
                        <label for="description" class="form-label">Description</label>
                        <textarea placeholder="write description about the product" id="description" rows="10" class="form-control"
                            x-model="data.description"></textarea>
                    </div>

                    <div class="mb-2">
                        <button class="btn btn-success">Create Category</button>
                    </div>
                </form>
            </div>
        </div>
        {{-- updateCategory --}}
        <div class="card" x-show="updateCategory">
            <div class="card-header">
                Update Category
            </div>
           <div class="card-body">
                <form action="" @submit.prevent="updateData">
                    <div class="mb-2">
                        <label class="form-label" for="name">Name</label>
                        <input type="text" id="name" class="form-control" placeholder="Enter your name"
                            x-model="data.name">
                        <template x-if="errors.name">
                            <span class="text-danger" x-text="errors.name"></span>
                        </template>

                    </div>

                    <div class="mb-2">
                        <label for="description" class="form-label">Description</label>
                        <textarea placeholder="write description about the product" id="description" rows="10" class="form-control"
                            x-model="data.description"></textarea>
                    </div>

                    <div class="mb-2">
                        <button class="btn btn-success" >Update Category</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
