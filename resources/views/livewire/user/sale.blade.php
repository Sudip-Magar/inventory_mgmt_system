<div x-data="sale">
    <template x-if="message">
        <div class="message bg-success" x-text="message"></div>
    </template>

    <template x-if="error">
        <div class="message bg-danger" x-text="error"></div>
    </template>

    <nav>
        <ul class="d-flex list gap-3 bg-success py-1 px-3 rounded">
            <li @click.prevent="listViewToggle" :class="listView ? 'text-warning' : 'text-white'">Sale List
            </li>
            <span class="border border-white"></span>
            <li :class="createView ? 'text-warning' : 'text-white'" @click.prevent="createViewToggle">Create
                Sale
            </li>
        </ul>
    </nav>

    {{-- Create Sale --}}
    <div class="card" x-show="createView" x-cloak>
        <div class="card-header">
            Create Sale
        </div>
        <div class="card-body">
            <form @submit.prevent="saveSale">
                <div class="row mb-3">
                    <!-- Vendor Info -->
                    <div class="col-6">
                        <div class="mb-3">
                            <label for="vendor">Customer:</label>
                            <select id="vendor" x-model="data.customer_id" class="form-control">
                                <option value="" disabled>Select Customer</option>
                                <template x-for="customer in allCustomer" :key="customer.id">
                                    <option :value="customer.id" x-text="customer.name"></option>
                                </template>
                            </select>
                        </div>

                        <template x-if="customerInfo">
                            <div>
                                <div class="mb-2">
                                    <label>Email</label>
                                    <input type="text" class="form-control" :value="customerInfo.email" disabled>
                                </div>
                                <div class="mb-2">
                                    <label>Address</label>
                                    <input type="text" class="form-control" :value="customerInfo.address" disabled>
                                </div>
                                <div class="mb-2">
                                    <label>Phone</label>
                                    <input type="text" class="form-control" :value="customerInfo.phone" disabled>
                                </div>
                            </div>
                        </template>
                    </div>

                    <!-- Order Info -->
                    <div class="col-6">
                        <div class="mb-2">
                            <label>Order Date</label>
                            <input type="date" class="form-control" x-model="data.sales_date">
                        </div>
                        <div class="mb-2">
                            <label>Expected Date</label>
                            <input type="date" class="form-control" x-model="data.expected_date">
                        </div>
                        <div class="mb-2">
                            <label>Total Amount</label>
                            <input type="text" class="form-control" :value="totalAmount.toFixed(2)" disabled>
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

                <!-- Product Table -->
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>S.N</th>
                            <th>Item</th>
                            <th>Quantity</th>
                            <th>Rate</th>
                            <th>Amount</th>
                            <th>Term Amount</th>
                            <th>Net Amount</th>
                            <th style="width:15%">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="(item, index) in items" :key="index">
                            <tr>
                                <td x-text="index + 1"></td>
                                <td wire:ignore class="w-25">
                                    <select class="js-example-basic-single form-control"
                                        style="width: 100%; height:100%;" x-model="item.product_id"
                                        x-init="initSelect($el, index)">
                                        <option value="" class="py-3">Select product</option>
                                        <template x-for="product in allProduct" :key="product.id">
                                            <option :value="product.id" x-text="product.name"></option>
                                        </template>
                                    </select>
                                </td>
                                <td>
                                    <input type="number" class="form-control" x-model.number="item.quantity"
                                        min="1">
                                    <template x-if="errors.quantity && errors.quantity[index]">
                                        <span x-text="errors.quantity[index]" class="text-danger"></span>
                                    </template>
                                </td>
                                <td>
                                    <input type="number" class="form-control" x-model.number="item.rate"
                                        min="0">
                                </td>
                                <td>
                                    <input type="number" class="form-control" :value="item.amount.toFixed(2)"
                                        disabled>
                                </td>
                                <td>
                                    <input type="number" @click.prevent="openTermModal(index)" readonly
                                        class="form-control" :value="item.termAmount.toFixed(2)">
                                </td>
                                <td>
                                    <input type="number" class="form-control" :value="item.netAmount.toFixed(2)"
                                        disabled>
                                </td>
                                <td>
                                    <button type="button" @click="addRow()" class="btn btn-success">+</button>
                                    <template x-if="index > 0">
                                        <button type="button" @click="removeRow(index)"
                                            class="btn btn-danger">-</button>
                                    </template>
                                </td>
                            </tr>
                        </template>

                        <tr>
                            <td colspan="2">Total</td>
                            <td x-text="totalQuantity"></td>
                            <td x-text="totalRate"></td>
                            <td x-text="totalAmount"></td>
                            <td x-text="totalTermAmount.toFixed(2)"></td>
                            <td x-text="totalNetAmount.toFixed(2)"></td>

                        </tr>
                    </tbody>
                </table>

                <div class="mb-3">
                    <button class="btn btn-success">Create Purchase</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Sale List --}}
    <div class="card" x-show="listView">
        <div class="card-header">
            All Sale
        </div>
        <div class="card-body">
            <table class="table  table-bordered table-striped table-hover">
                <thead class="table-active">
                    <tr>
                        <th>Sn</th>
                        <th>Customer Name</th>
                        <th>Total Quantity</th>
                        <th>Total Discount</th>
                        <th>Total Amount</th>
                        <th>Status</th>
                        <th>Order Date</th>
                        <th>Arrival Date</th>
                    </tr>
                </thead>

                <tbody>
                    <template x-for="(sale,idx) in allSale">
                        <tr class="pointer" @click.prevent="updateViewToggle(sale.id)" :key="sale.id">
                            <td x-text="idx +1"></td>
                            <td x-text="sale.customer.name"></td>
                            <td x-text="sale.quantity"></td>
                            <td x-text="sale.total_discount_amt"></td>
                            <td x-text="sale.total_amount"></td>
                            <td class="d-flex justify-content-center ">
                                <template x-if="sale.status == 'draft'">
                                    <span class="bg-danger px-3 py-1 rounded-pill text-white">Draft</span>
                                </template>

                                <template x-if="sale.status == 'confirmed'">
                                    <span class="bg-success px-3 py-1 text-white rounded-pill">confirmed</span>
                                </template>

                                <template x-if="sale.status == 'cancelled'">
                                    <span class="bg-warning px-3 py-1 rounded-pill">Cancelled</span>
                                </template>
                            </td>
                            <td x-text="sale.sales_date"></td>
                            <td x-text="sale.expected_date"></td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>

    {{-- Update Sale --}}
    <div x-show="updateView">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <strong>Update Sale</strong>
                <div class="flex gap-0 mx-0 d-inline-flex">
                    <span
                        :class="['p-2 text-white rounded-start', saleInfo.status == 'draft' ? 'bg-primary active' :
                            'bg-secondary'
                        ]">Draft</span>
                    <span class="border"></span>
                    <span
                        :class="['p-2 text-white', saleInfo.status == 'confirmed' ? 'bg-primary' : 'bg-secondary']">Confirm</span>
                    <span class="border"></span>

                    <span
                        :class="['p-2 text-white', saleInfo.status == 'return' ? 'bg-primary' : 'bg-secondary']">Return</span>
                    <span class="border"></span>

                    <span
                        :class="['p-2 text-white rounded-end', saleInfo.status == 'cancelled' ? 'bg-primary' :
                            'bg-secondary'
                        ]">Cancel</span>
                </div>
            </div>
            <div class="card-body">
                <form>
                    <div class="row mb-3">
                        <!-- Vendor Info -->
                        <div class="col-6">
                            <div class="mb-3">
                                <label for="vendor">Vendor:</label>
                                <select x-model="data.customer_id" class="form-control">
                                    <option value="">Select Vendor</option>
                                    <template x-for="customer in allCustomer" :key="customer.id">
                                        <option :value="customer.id" x-text="customer.name"></option>
                                    </template>
                                </select>
                            </div>

                            <template x-if="customerInfo">
                                <div>
                                    <div class="mb-2">
                                        <label>Email</label>
                                        <input type="text" class="form-control" :value="customerInfo.email"
                                            disabled>
                                    </div>
                                    <div class="mb-2">
                                        <label>Address</label>
                                        <input type="text" class="form-control" :value="customerInfo.address"
                                            disabled>
                                    </div>
                                    <div class="mb-2">
                                        <label>Phone</label>
                                        <input type="text" class="form-control" :value="customerInfo.phone"
                                            disabled>
                                    </div>


                                </div>
                            </template>
                        </div>

                        <!-- Order Info -->
                        <div class="col-6">
                            <div class="mb-2">
                                <label>Order Date</label>
                                <input type="date" class="form-control" x-model="data.sales_date">
                            </div>
                            <div class="mb-2">
                                <label>Expected Date</label>
                                <input type="date" class="form-control" x-model="data.expected_date">
                            </div>
                            <div class="mb-2">
                                <label>Total Amount</label>
                                <input type="text" class="form-control" :value="totalNetAmount.toFixed(2)"
                                    disabled>
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

                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>S.N</th>
                                <th>Item</th>
                                <th>Quantity</th>
                                <th>Rate</th>
                                <th>Amount</th>
                                <th>Term Amount</th>
                                <th>Net Amount</th>
                                <th style="width:15%">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="(item, index) in items" :key="index">
                                <tr>
                                    <td x-text="index + 1"></td>
                                    <td wire:ignore class="w-25">
                                        <select class="js-example-basic-single form-control"
                                            style="width: 100%; height:100%;" x-model="item.product_id"
                                            x-init="initSelected($el, index)">
                                            <option value="" class="py-3">Select product</option>
                                            <template x-for="product in allProduct" :key="product.id">
                                                <option :value="product.id" x-text="product.name"></option>
                                            </template>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="number" class="form-control" x-model.number="item.quantity"
                                            min="1">
                                        <template x-if="errors.quantity && errors.quantity[index]">
                                            <span x-text="errors.quantity[index]" class="text-danger"></span>
                                        </template>
                                    </td>
                                    <td>
                                        <input type="number" class="form-control" x-model.number="item.rate"
                                            min="0">
                                    </td>
                                    <td>
                                        <input type="number" class="form-control" :value="item.amount.toFixed(2)"
                                            disabled>
                                    </td>
                                    <td>
                                        <input type="number" @click.prevent="openTermModal(index)" readonly
                                            class="form-control" :value="item.termAmount.toFixed(2)">
                                    </td>
                                    <td>
                                        <input type="number" class="form-control" :value="item.netAmount.toFixed(2)"
                                            disabled>
                                    </td>
                                    <td>
                                        <button type="button" @click="addRow()" class="btn btn-success">+</button>
                                        <template x-if="index > 0">
                                            <button type="button" @click="removeRow(index)"
                                                class="btn btn-danger">-</button>
                                        </template>
                                    </td>
                                </tr>
                            </template>

                            <tr>
                                <td colspan="2">Total</td>
                                <td x-text="totalQuantity"></td>
                                <td x-text="totalRate.toFixed(2)"></td>
                                <td x-text="totalAmount.toFixed(2)"></td>
                                <td x-text="totalTermAmount.toFixed(2)"></td>
                                <td x-text="totalNetAmount.toFixed(2)"></td>
                                <td></td>

                            </tr>
                        </tbody>
                    </table>

                    <div class="mb-3 d-flex gap-2">
                        <template x-if="saleInfo.status == 'draft'">
                            <div>
                                <button @click.prevent="updateSaleData(saleInfo.id)" class="btn btn-warning">Update
                                    Sale</button>
                                <button @click.prevent="confirmOrder(saleInfo.id)" class="btn btn-success">Confirm
                                    Order</button>
                                <button class="btn btn-danger" @click.prevent="cancelOrder(saleInfo.id)">Cancel
                                    Sale</button>
                            </div>
                        </template>

                        <template x-if="saleInfo.status == 'confirmed'">
                            <div>
                                <button type="button" class="btn btn-warning" data-bs-toggle="modal"
                                    data-bs-target="#exampleModal">
                                    Sale Return
                                </button>

                                <button class="btn btn-danger text-white"
                                    @click.prevent="cancelOrder(saleInfo.id)">Cancel Sale</button>
                            </div>
                        </template>

                        <template x-if="saleInfo.status == 'cancelled'">
                            <button @click.prevent="resetToDraft(saleInfo.id)" class="btn btn-warning">Reset to
                                Draft</button>
                        </template>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Term Amount Modal -->
    <div class="custom-Model" x-show="showTermModal" x-cloak>
        <h2 class="text-lg font-bold mb-3">Edit Term Amount</h2>
        <div class="text-end">
            <p class="bg-white d-inline-block text-black w-25 text-center" x-text="tempNetAmount.toFixed(2)"></p>
        </div>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>S.No</th>
                    <th>Code</th>
                    <th>Name</th>
                    <th>Sign</th>
                    <th>Rate (%)</th>
                </tr>
            </thead>
            <tbody>
                <template x-for="(discount, idx) in allDiscount" :key="discount.id">
                    <tr>
                        <td x-text="idx + 1"></td>
                        <td x-text="discount.code"></td>
                        <td x-text="discount.name"></td>
                        <td x-text="discount.sign"></td>
                        <td>
                            <input type="number" x-model.number="tempDiscounts[idx]"
                                @input="recalculateTempAmount()">
                        </td>
                    </tr>
                </template>

            </tbody>

        </table>
        <div class="flex justify-end gap-2">
            <button class="btn btn-secondary" @click.prevent="closeTermModal">Cancel</button>
            <button class="btn btn-primary" @click.prevent="saveTermAmount">Save</button>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-danger" id="exampleModalLabel">Do you really wnat to create Sale Return!</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <strong class="mb-1 d-inline-block">Please reason to return the product:</strong>
                    <textarea min="2" class="form-control" name="" id="" x-model="return_reason"></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" @click.prevent="saleReturn(saleInfo.id)"
                        class="btn btn-primary">Create Sale Return</button>
                </div>
            </div>
        </div>
    </div>
</div>
