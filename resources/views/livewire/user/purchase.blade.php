<div x-data="purchase">
    <template x-if="message">
        <div class="message bg-success" x-text="message"></div>
    </template>

    <nav>
        <ul class="d-flex list gap-3 bg-success py-1 px-3 rounded">
            <li>Purchase List</li>
            <span class="border border-white"></span>
            <li>Create Purchase</li>
        </ul>
    </nav>

    <div class="card">
        <div class="card-header">Create Purchase</div>
        <div class="card-body">
            <form @submit.prevent="savePurchase">
                <div class="row">
                    <!-- Vendor Info -->
                    <div class="col-6">
                        <div class="mb-3">
                            <label for="vendor">Vendor:</label>
                            <select id="vendor" x-model="data.vendor_id" class="form-control">
                                <option value="" disabled>Select Vendor</option>
                                <template x-for="vendor in allVendor" :key="vendor.id">
                                    <option :value="vendor.id"><span x-text="vendor.name"></span></option>
                                </template>
                            </select>
                        </div>

                        <template x-if="vendorInfo">
                            <div>
                                <div class="mb-3">
                                    <label>Company</label>
                                    <input type="text" class="form-control" :value="vendorInfo.company" disabled>
                                </div>
                                <div class="mb-3">
                                    <label>Email</label>
                                    <input type="text" class="form-control" :value="vendorInfo.email" disabled>
                                </div>
                                <div class="mb-3">
                                    <label>Address</label>
                                    <input type="text" class="form-control" :value="vendorInfo.address" disabled>
                                </div>
                                <div class="mb-3">
                                    <label>Phone</label>
                                    <input type="text" class="form-control" :value="vendorInfo.phone" disabled>
                                </div>
                            </div>
                        </template>
                    </div>

                    <!-- Order Info -->
                    <div class="col-6">
                        <div class="mb-2">
                            <label for="orderDate">Order Date</label>
                            <input type="date" class="form-control" id="orderDate" x-model="data.order_date">
                        </div>

                        <div class="mb-2">
                            <label for="expectedDate">Expected Date</label>
                            <input type="date" class="form-control" id="expectedDate" x-model="data.expected_date">
                        </div>

                        <div class="mb-2">
                            <label>Total Amount</label>
                            <input type="text" class="form-control" :value="totalAmount" disabled>
                        </div>

                        <div class="mb-2">
                            <label>Total Quantity</label>
                            <input type="text" class="form-control" :value="totalQuantity" disabled>
                        </div>

                        <div class="mb-2">
                            <label>Payment Method</label>
                            <select class="form-control" x-model="data.payment_method">
                                <option value="" disabled>Select Payment Method</option>
                                <option value="cash">Cash</option>
                                <option value="bank">Bank</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Product Section -->
                <div>
                    <h6>Products</h6>
                    <table class="table table-bordered table-hover table-striped">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Quantity</th>
                                <th>Price</th>
                                <th>Total</th>
                                <th>term</th>
                                <th>Net Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="(item, index) in items" :key="item.id">
                                <tr>
                                    <td x-text="item.name"></td>
                                    <td>
                                        <input type="number" class="form-control w-50"
                                            x-model.number="eachStock[index]">
                                    </td>
                                    <td x-text="item.price"></td>
                                    <td x-text="(eachStock[index] || 0) * item.price"></td>
                                </tr>
                            </template>
                        </tbody>
                    </table>

                    <div class="mb-2">
                        <label>Search Products</label>
                        <input type="text" class="form-control" x-model="search"
                            placeholder="Search and Add Products">
                    </div>

                    <template x-if="productInfo.length">
                        <div class="list-group">
                            <template x-for="product in productInfo" :key="product.id">
                                <button type="button" class="list-group-item list-group-item-action"
                                    x-text="product.name" @click.prevent="addProduct(product.id)">
                                </button>
                            </template>
                        </div>
                    </template>

                    <template x-if="search && !productInfo.length">
                        <div class="alert alert-warning">
                            <span x-text="search"></span> related Product not found
                        </div>
                    </template>
                </div>

                <div class="mb-3">
                    <button class="btn btn-success"> Create Purchase</button>
                </div>
            </form>
        </div>
    </div>
</div>
