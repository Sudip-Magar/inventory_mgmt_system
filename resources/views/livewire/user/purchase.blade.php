<div x-data="purchase">
    <template x-if="message">
        <div class="message bg-success" x-text="message"></div>
    </template>

    <nav>
        <ul class="d-flex list gap-3 bg-success py-1 px-3 rounded">
            <li @click.prevent="purchaseListToggle" :class="purchaseList ? 'text-warning' : 'text-white'">Product List
            </li>
            <span class="border border-white"></span>
            <li :class="createPurchase ? 'text-warning' : 'text-white'" @click.prevent="createPurchaseToggle">Create
                Product
            </li>
        </ul>
    </nav>

    {{-- create purchase --}}
    <div x-show="createPurchase" x-cloak>
        <div class="card mt-3">
            <div class="card-header">Create Purchase</div>
            <div class="card-body">
                <form @submit.prevent="savePurchase">
                    <div class="row mb-3">
                        <!-- Vendor Info -->
                        <div class="col-6">
                            <div class="mb-3">
                                <label for="vendor">Vendor:</label>
                                <select id="vendor" x-model="data.vendor_id" class="form-control">
                                    <option value="" disabled>Select Vendor</option>
                                    <template x-for="vendor in allVendor" :key="vendor.id">
                                        <option :value="vendor.id" x-text="vendor.name"></option>
                                    </template>
                                </select>
                            </div>

                            <template x-if="vendorInfo">
                                <div>
                                    <div class="mb-2">
                                        <label>Company</label>
                                        <input type="text" class="form-control" :value="vendorInfo.company" disabled>
                                    </div>
                                    <div class="mb-2">
                                        <label>Email</label>
                                        <input type="text" class="form-control" :value="vendorInfo.email" disabled>
                                    </div>
                                    <div class="mb-2">
                                        <label>Address</label>
                                        <input type="text" class="form-control" :value="vendorInfo.address" disabled>
                                    </div>
                                    <div class="mb-2">
                                        <label>Phone</label>
                                        <input type="text" class="form-control" :value="vendorInfo.phone" disabled>
                                    </div>
                                </div>
                            </template>
                        </div>

                        <!-- Order Info -->
                        <div class="col-6">
                            <div class="mb-2">
                                <label>Order Date</label>
                                <input type="date" class="form-control" x-model="data.order_date">
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
                                        <select class="js-example-basic-single form-control" x-model="item.id"
                                            x-init="initSelect($el, index)">
                                            <option value="">Select product</option>
                                            <template x-for="product in allProduct" :key="product.id">
                                                <option :value="product.id" x-text="product.name"></option>
                                            </template>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="number" class="form-control" x-model.number="item.quantity"
                                            min="1">
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
    </div>

    {{-- purchase list --}}
    <div x-show="purchaseList">
        <div class="card">
            <div class="card-header">
                Purchase List
            </div>
            <div class="card-body">
                <table class="table table-bordered table-hover">
                    <tbody>
                        <tr>
                            <th>Sn</th>
                            <th>Vendor Name</th>
                            <th>Total Quantity</th>
                            <th>Total Amount</th>
                            <th>Status</th>
                            <th>Payment</th>
                        </tr>
                    </tbody>
                    <tbody>
                        <template x-for="(purchase,idx) in allPurchase">
                            <tr :key="purchase.id">
                                <td x-text="idx + 1"></td>
                                <td x-text="purchase.vendor.name"></td>
                                <td x-text="purchase.total_quantity"></td>
                                <td x-text="purchase.total_amount"></td>
                                <td x-text="purchase.status"></td>
                                <td x-text="purchase.payment_status"></td>

                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
