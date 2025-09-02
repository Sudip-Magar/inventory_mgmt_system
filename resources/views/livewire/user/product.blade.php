<div x-data="product">
    <template x-if="message">
        <div class="message bg-success " x-text="message"></div>
    </template>
    <nav>
        <ul class="d-flex list gap-3 bg-success py-1 px-3 rounded">
            <li @click.prevent="productListToggle" :class="productList ? 'text-warning' : 'text-white'">Product List</li>
            <span class="border border-white"></span>
            <li :class="createProduct ? 'text-warning' : 'text-white'" @click.prevent="createProductToggle">Create
                Product
            </li>
        </ul>
    </nav>

    {{-- Create Product --}}
    <div class="card" x-show="createProduct"  x-cloak>
        <div class="card-header">Create Product</div>
        <div class="card-body">
            <form @submit.prevent="store">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <label for="image" class="bg-primary py-1 px-2 text-white rounded upload">Upload
                            Image</label>
                        <input type="file" id="image" wire:model.live='image' hidden>
                    </div>
                    <div class="text-end images">
                        @if ($image)
                            <img class="d-inline-block" src="{{ $image->temporaryUrl() }}" alt="">
                        @else
                            <img class="d-inline-block" src="{{ asset('storage/common/images.png') }}" alt="">
                        @endif
                    </div>
                </div>

                <div class="row">
                    <div class="col-6">
                        <div class="mb-2">
                            <label for="code" class="form-label"> Product Code:</label>
                            <input type="text" class="form-control" id="code" x-model="datas.code"
                                placeholder="Enter Product Code">
                            <template x-if="errors.code">
                                <span class="text-danger" x-text="errors.code"></span>
                            </template>
                        </div>

                        <div class="mb-2">
                            <label for="name" class="form-label"> Product Name:</label>
                            <input type="text" class="form-control" id="code" x-model="datas.name"
                                placeholder="Enter Product Name">
                            <template x-if="errors.name">
                                <span class="text-danger" x-text="errors.name"></span>
                            </template>
                        </div>

                        <div class="mb-2">
                            <label for="category" class="form-label"> Product Category:</label>
                            <select class="form-control" id="category" x-model="datas.category_id"
                                placeholder="Enter Product Name">
                                <option value="" disabled> select Category</option>
                                <template x-for="category in allCategory">
                                    <option :value="category.id" :key="category.id" x-text="category.name">
                                    </option>
                                </template>
                            </select>
                            <template x-if="errors.category_id">
                                <span class="text-danger" x-text="errors.category_id"></span>
                            </template>
                        </div>
                    </div>

                    <div class="col-6">
                        <div class="mb-2">
                            <label for="cost" class="form-label"> Product Cost:</label>
                            <input type="number" class="form-control" id="cost" x-model="datas.cost"
                                placeholder="Enter Product Cost">
                            <template x-if="errors.cost">
                                <span class="text-danger" x-text="errors.cost"></span>
                            </template>
                        </div>

                        <div class="mb-2">
                            <label for="price" class="form-label"> Product price:</label>
                            <input type="number" class="form-control" id="price" x-model="datas.price"
                                placeholder="Enter Product price">
                            <template x-if="errors.price">
                                <span class="text-danger" x-text="errors.price"></span>
                            </template>
                        </div>

                        <div class="mb-2">
                            <label for="stock" class="form-label"> Product stock:</label>
                            <input type="number" class="form-control" id="stock" x-model="datas.stock"
                                placeholder="Enter Product price">
                            <template x-if="errors.stock">
                                <span class="text-danger" x-text="errors.stock"></span>
                            </template>
                        </div>
                    </div>
                </div>

                <div class="mb-2">
                    <label for="description">description</label>
                    <textarea name="" id="" class="form-control" placeholder="Enter Short Description about the Product"
                        cols="30" x-model="datas.description"></textarea>
                    <template x-if="errors.description">
                        <span class="text-danger" x-text="errors.description"></span>
                    </template>
                </div>

                <div class="mb-2">
                    <button class="btn btn-success">Create Product</button>
                </div>

            </form>
        </div>
    </div>

    {{-- Product List --}}
    <div class="card" x-show="productList">
        <div class="card-header">
            Product List
        </div>
        <div class="card-body">
            <table class="table table-bordered table-hover table-striped">
                <thead>
                    <th>Code</th>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Cost</th>
                    <th>Stock</th>
                    <th>Action</th>
                </thead>

                <tbody>
                    <template x-for="product in allProduct">
                        <tr :key="product.id">
                            <td x-text="product.code"></td>
                            <td x-text="product.name"></td>
                            <td x-text="product.category.name"></td>
                            <td x-text="product.price"></td>
                            <td x-text="product.cost"></td>
                            <td x-text="product.stock"></td>
                            <td>
                                <button @click.prevent="updateProductToggle(product.id)"
                                    class="btn btn-primary">Edit</button>
                                <button class="btn btn-danger" @click.prevent="deleteProduct(product.id)">Delete</button>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>

    {{-- Update Product --}}
    <div class="card" x-show="updateProduct"  x-cloak>
        <div class="card-header">Update Product</div>
        <div class="card-body">
            <form @submit.prevent="updateProductDetail">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <label for="image" class="bg-primary py-1 px-2 text-white rounded upload">Upload
                            Image</label>
                        <input type="file" id="image" wire:model.live='image' hidden>
                    </div>
                    <div class="text-end images">
                        @if (!$image)
                            <template x-if="datas.image">
                                <img :src="'/storage/' + datas.image" alt="">
                            </template>
                            <template x-if="!datas.image">
                                <img class="d-inline-block" src="{{ asset('storage/common/images.png') }}"
                                    alt="">
                            </template>
                        @else
                            <img class="d-inline-block" src="{{ $image->temporaryUrl() }}" alt="">
                        @endif

                    </div>
                </div>

                <div class="row">
                    <div class="col-6">
                        <div class="mb-2">
                            <label for="code" class="form-label"> Product Code:</label>
                            <input type="text" class="form-control" id="code" x-model="datas.code"
                                placeholder="Enter Product Code">
                            <template x-if="errors.code">
                                <span class="text-danger" x-text="errors.code"></span>
                            </template>
                        </div>

                        <div class="mb-2">
                            <label for="name" class="form-label"> Product Name:</label>
                            <input type="text" class="form-control" id="code" x-model="datas.name"
                                placeholder="Enter Product Name">
                            <template x-if="errors.name">
                                <span class="text-danger" x-text="errors.name"></span>
                            </template>
                        </div>

                        <div class="mb-2">
                            <label for="category" class="form-label"> Product Category:</label>
                            <select class="form-control" id="category" x-model="datas.category_id"
                                placeholder="Enter Product Name">
                                <option value="" disabled> select Category</option>
                                <template x-for="category in allCategory">
                                    <option :value="category.id" :key="category.id" x-text="category.name">
                                    </option>
                                </template>
                            </select>
                            <template x-if="errors.category_id">
                                <span class="text-danger" x-text="errors.category_id"></span>
                            </template>
                        </div>
                    </div>

                    <div class="col-6">
                        <div class="mb-2">
                            <label for="cost" class="form-label"> Product Cost:</label>
                            <input type="number" class="form-control" id="cost" x-model="datas.cost"
                                placeholder="Enter Product Cost">
                            <template x-if="errors.cost">
                                <span class="text-danger" x-text="errors.cost"></span>
                            </template>
                        </div>

                        <div class="mb-2">
                            <label for="price" class="form-label"> Product price:</label>
                            <input type="number" class="form-control" id="price" x-model="datas.price"
                                placeholder="Enter Product price">
                            <template x-if="errors.price">
                                <span class="text-danger" x-text="errors.price"></span>
                            </template>
                        </div>

                        <div class="mb-2">
                            <label for="stock" class="form-label"> Product stock:</label>
                            <input type="number" class="form-control" id="stock" x-model="datas.stock"
                                placeholder="Enter Product price">
                            <template x-if="errors.stock">
                                <span class="text-danger" x-text="errors.stock"></span>
                            </template>
                        </div>
                    </div>
                </div>

                <div class="mb-2">
                    <label for="description">description</label>
                    <textarea name="" id="" class="form-control" placeholder="Enter Short Description about the Product"
                        cols="30" x-model="datas.description"></textarea>
                    <template x-if="errors.description">
                        <span class="text-danger" x-text="errors.description"></span>
                    </template>
                </div>

                <div class="mb-2">
                    <button class="btn btn-success" >Update Product</button>
                </div>

            </form>
        </div>
    </div>

</div>
