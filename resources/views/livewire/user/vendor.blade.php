<div x-data="vendor">
    <template x-if="message">
        <div class="message bg-success " x-text="message"></div>
    </template>
    <nav>
        <ul class="d-flex list gap-3 bg-success py-1 px-3 rounded">
            <li :class="vendorList ? 'text-warning' : 'text-white'" @click.prevent="VendorListToggle">Vendor List
            </li>
            <span class="border border-white"></span>
            <li :class="createVendor ? 'text-warning' : 'text-white'" @click.prevent="createVendorToggle">Create
                Vendor </li>
        </ul>
    </nav>

    {{-- Create Vendor --}}
    <div class="card" x-show="createVendor" x-cloak>
        <div class="card-header">Create Vendor</div>
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
                            <label for="name" class="form-label"> Vendor Name:</label>
                            <input type="text" class="form-control" id="name" x-model="data.name"
                                placeholder="Enter Vendor Name">
                            <template x-if="errors.name">
                                <span class="text-danger" x-text="errors.name"></span>
                            </template>
                        </div>

                        <div class="mb-2">
                            <label for="email" class="form-label"> Vendor Email:</label>
                            <input type="email" class="form-control" id="email" x-model="data.email"
                                placeholder="Enter Vendor Email">
                            <template x-if="errors.email">
                                <span class="text-danger" x-text="errors.email"></span>
                            </template>
                        </div>

                        <div class="mb-2">
                            <label for="company" class="form-label"> Vendor Cmpany:</label>
                            <input type="text" class="form-control" id="company" x-model="data.company"
                                placeholder="Enter Vendor's Company">
                            <template x-if="errors.company">
                                <span class="text-danger" x-text="errors.company"></span>
                            </template>
                        </div>
                    </div>

                    <div class="col-6">
                        <div class="mb-2">
                            <label for="address" class="form-label"> Vendor Address:</label>
                            <input type="text" class="form-control" id="address" x-model="data.address"
                                placeholder="Enter Vendor Address">
                            <template x-if="errors.address">
                                <span class="text-danger" x-text="errors.address"></span>
                            </template>
                        </div>

                        <div class="mb-2">
                            <label for="phone" class="form-label"> Vendor Phone:</label>
                            <input type="number" class="form-control" id="address" x-model="data.phone"
                                placeholder="Enter Customer Vendor Number">
                            <template x-if="errors.phone">
                                <span class="text-danger" x-text="errors.phone"></span>
                            </template>
                        </div>
                    </div>
                </div>
                <div class="mb-2">
                    <button class="btn btn-success">Create Vendor</button>
                </div>

            </form>
        </div>
    </div>

    {{-- Vendor List --}}
    <div class="card" x-show="vendorList">
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
                    <template x-for="(vendor,index) in allVendor">
                        <tr :key="vendor.id">
                            <td x-text="index + 1"></td>
                            <td x-text="vendor.name"></td>
                            <td x-text="vendor.email"></td>
                            <td x-text="vendor.address"></td>
                            <td x-text="vendor.phone"></td>
                            <td>
                                <button class="btn btn-primary" @click="updateVendorToggle(vendor.id)">Edit</button>
                                <button class="btn btn-danger" @click ="DeleteVendor(vendor.id)">Delete</button>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>


    {{-- Update Vendor --}}
    <div class="card" x-show="updateVendor" x-cloak>
        <div class="card-header">Create Vendor</div>
        <div class="card-body">
            <form @submit.prevent="updateVendordata">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <label for="image" class="bg-primary py-1 px-2 text-white rounded upload">Upload
                            Image</label>
                        <input type="file" id="image" wire:model.live='image' hidden>
                    </div>
                    <div class="text-end images">
                        @if (!$image)
                            <template x-if="data.image">
                                <img :src="'/storage/' + data.image" alt="">
                            </template>
                            <template x-if="!data.image">
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
                            <label for="name" class="form-label"> Vendor Name:</label>
                            <input type="text" class="form-control" id="name" x-model="data.name"
                                placeholder="Enter Vendor Name">
                            <template x-if="errors.name">
                                <span class="text-danger" x-text="errors.name"></span>
                            </template>
                        </div>

                        <div class="mb-2">
                            <label for="email" class="form-label"> Vendor Email:</label>
                            <input type="email" class="form-control" id="email" x-model="data.email"
                                placeholder="Enter Vendor Email">
                            <template x-if="errors.email">
                                <span class="text-danger" x-text="errors.email"></span>
                            </template>
                        </div>

                        <div class="mb-2">
                            <label for="company" class="form-label"> Vendor Cmpany:</label>
                            <input type="text" class="form-control" id="company" x-model="data.company"
                                placeholder="Enter Vendor's Company">
                            <template x-if="errors.company">
                                <span class="text-danger" x-text="errors.company"></span>
                            </template>
                        </div>
                    </div>

                    <div class="col-6">
                        <div class="mb-2">
                            <label for="address" class="form-label"> Vendor Address:</label>
                            <input type="text" class="form-control" id="address" x-model="data.address"
                                placeholder="Enter Vendor Address">
                            <template x-if="errors.address">
                                <span class="text-danger" x-text="errors.address"></span>
                            </template>
                        </div>

                        <div class="mb-2">
                            <label for="phone" class="form-label"> Vendor Phone:</label>
                            <input type="number" class="form-control" id="address" x-model="data.phone"
                                placeholder="Enter Customer Vendor Number">
                            <template x-if="errors.phone">
                                <span class="text-danger" x-text="errors.phone"></span>
                            </template>
                        </div>
                    </div>
                </div>
                <div class="mb-2">
                    <button class="btn btn-success">Create Vendor</button>
                </div>

            </form>
        </div>
    </div>
</div>
