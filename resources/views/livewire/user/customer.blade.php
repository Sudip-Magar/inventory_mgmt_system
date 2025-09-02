<div x-data="customer">
    <template x-if="message">
        <div class="message bg-success " x-text="message"></div>
    </template>
    <nav>
        <ul class="d-flex list gap-3 bg-success py-1 px-3 rounded">
            <li :class="CustomerList ? 'text-warning' : 'text-white'" @click.prevent="productListToggle">Customer List
            </li>
            <span class="border border-white"></span>
            <li :class="createCustomer ? 'text-warning' : 'text-white'" @click.prevent="createProductToggle">Create
                Customer </li>
        </ul>
    </nav>
    {{-- Create Customer --}}
    <div class="card" x-show="createCustomer" x-cloak>
        <div class="card-header">Create Customer</div>
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
                            <label for="name" class="form-label"> Customer Name:</label>
                            <input type="text" class="form-control" id="name" x-model="datas.name"
                                placeholder="Enter Customer Name">
                            <template x-if="errors.name">
                                <span class="text-danger" x-text="errors.name"></span>
                            </template>
                        </div>

                        <div class="mb-2">
                            <label for="email" class="form-label"> Customer Email:</label>
                            <input type="email" class="form-control" id="email" x-model="datas.email"
                                placeholder="Enter Customer Email">
                            <template x-if="errors.email">
                                <span class="text-danger" x-text="errors.email"></span>
                            </template>
                        </div>
                    </div>

                    <div class="col-6">
                        <div class="mb-2">
                            <label for="address" class="form-label"> Customer Address:</label>
                            <input type="text" class="form-control" id="address" x-model="datas.address"
                                placeholder="Enter Customer Address">
                            <template x-if="errors.address">
                                <span class="text-danger" x-text="errors.address"></span>
                            </template>
                        </div>

                        <div class="mb-2">
                            <label for="phone" class="form-label"> Customer Phone:</label>
                            <input type="number" class="form-control" id="address" x-model="datas.phone"
                                placeholder="Enter Customer Phone Number">
                            <template x-if="errors.phone">
                                <span class="text-danger" x-text="errors.phone"></span>
                            </template>
                        </div>
                    </div>
                </div>
                <div class="mb-2">
                    <button class="btn btn-success">Create Customer</button>
                </div>

            </form>
        </div>
    </div>

    {{-- Customer List --}}
    <div class="card" x-show="CustomerList">
        <div class="card-header">
            Customer List
        </div>
        <div class="card-body">
            <table class="table table-bordered table-striped table-hover">
                <thead>
                    <tr>
                        <th>S.no</th>
                        <th>Name</th>
                        <th>email</th>
                        <th>Address</th>
                        <th>phone</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="(customer,index) in allCustomer">
                        <tr :key="customer.id">
                            <td x-text="index + 1"></td>
                            <td x-text="customer.name"></td>
                            <td x-text="customer.email"></td>
                            <td x-text="customer.address"></td>
                            <td x-text="customer.phone"></td>
                            <td>
                                <button class="btn btn-primary" @click="updateProductToggle(customer.id)">Edit</button>
                                <button class="btn btn-danger" @click ="DeleteProduct(customer.id)">Delete</button>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>

    {{-- Update Customer --}}
    <div class="card" x-show="updateCustomer" x-cloak>
        <div class="card-header">Update Customer</div>
        <div class="card-body">
            <form @submit.prevent="updateCustomerdata">
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
                            <label for="name" class="form-label"> Customer Name:</label>
                            <input type="text" class="form-control" id="name" x-model="datas.name"
                                placeholder="Enter Customer Name">
                            <template x-if="errors.name">
                                <span class="text-danger" x-text="errors.name"></span>
                            </template>
                        </div>

                        <div class="mb-2">
                            <label for="email" class="form-label"> Customer Email:</label>
                            <input type="email" class="form-control" id="email" x-model="datas.email"
                                placeholder="Enter Customer Email">
                            <template x-if="errors.email">
                                <span class="text-danger" x-text="errors.email"></span>
                            </template>
                        </div>
                    </div>

                    <div class="col-6">
                        <div class="mb-2">
                            <label for="address" class="form-label"> Customer Address:</label>
                            <input type="text" class="form-control" id="address" x-model="datas.address"
                                placeholder="Enter Customer Address">
                            <template x-if="errors.address">
                                <span class="text-danger" x-text="errors.address"></span>
                            </template>
                        </div>

                        <div class="mb-2">
                            <label for="phone" class="form-label"> Customer Phone:</label>
                            <input type="number" class="form-control" id="address" x-model="datas.phone"
                                placeholder="Enter Customer Phone Number">
                            <template x-if="errors.phone">
                                <span class="text-danger" x-text="errors.phone"></span>
                            </template>
                        </div>
                    </div>
                </div>
                <div class="mb-2">
                    <button class="btn btn-success">Update Customer</button>
                </div>

            </form>
        </div>
    </div>
</div>
