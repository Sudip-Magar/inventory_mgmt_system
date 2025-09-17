<div x-data="purchase">
    <template x-if="message">
        <div class="message bg-success" x-text="message"></div>
    </template>

    <nav>
        <ul class="d-flex list gap-3 bg-success py-1 px-3 rounded">
            <li @click.prevent="purchaseListToggle" :class="purchaseList ? 'text-warning' : 'text-white'">Purchase List
            </li>
            <span class="border border-white"></span>
            <li :class="createPurchase ? 'text-warning' : 'text-white'" @click.prevent="createPurchaseToggle">Create
                Purchase
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
                                        <select class="js-example-basic-single form-control"
                                            style="width: 100%; height:100%;" x-model="item.product_id"
                                            x-init="initSelect($el, index)">
                                            <option value="" class="py-3">Select product</option>
                                            <template x-for="product in allProduct" :key="product.id">
                                                <option :value="product.id" x-text="product.name"></option>
                                            </template>
                                        </select>

                                        {{-- <select class="form-control" x-model="item.id">
                                            <option value="">Select product</option>
                                            <template x-for="product in allProduct" :key="product.id">
                                                <option :value="product.id" x-text="product.name"></option>
                                            </template> --}}
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
                            <th>Total Discount</th>
                            <th>Total Amount</th>
                            <th>Status</th>
                            <th>Payment</th>
                            <th>Order Date</th>
                            <th>Arrival Date</th>
                        </tr>
                    </tbody>
                    <tbody>
                        <template x-for="(purchase,idx) in allPurchase">
                            <tr class="pointer" @click.prevent="updatePurchaseToggle(purchase.id)"
                                :key="purchase.id">
                                <td x-text="idx + 1"></td>
                                <td x-text="purchase.vendor.name"></td>
                                <td x-text="purchase.total_quantity"></td>
                                <td x-text="purchase.total_discount_amt"></td>
                                <td x-text="purchase.total_amount"></td>
                                <template x-if="purchase.status == 'draft'">
                                    <td class="d-flex justify-content-center "> <span x-text="purchase.status"
                                            class="bg-danger px-3 py-1 rounded-pill text-white"></span>
                                    </td>
                                </template>

                                <template x-if="purchase.status == 'confirmed'">
                                    <td class="d-flex justify-content-center "> <span x-text="purchase.status"
                                            class="bg-success px-3 py-1 text-white rounded-pill"></span>
                                    </td>
                                </template>

                                <template x-if="purchase.status == 'cancelled'">
                                    <td class="d-flex justify-content-center "> <span x-text="purchase.status"
                                            class="bg-warning px-3 py-1 rounded-pill"></span>
                                    </td>
                                </template>
                                <td x-text="purchase.payment_status"></td>
                                <td x-text="purchase.order_date"></td>
                                <td x-text="purchase.expected_date"></td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div x-show="updatePurchase">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <strong>Update Purchase</strong>
                <div class="flex gap-0 mx-0 d-inline-flex">
                    <span
                        :class="['p-2 text-white rounded-start', purchaseInfo.status == 'draft' ? 'bg-primary active' : 'bg-secondary']">Draft</span>
                    <span class="border"></span>
                    <span
                        :class="['p-2 text-white', purchaseInfo.status == 'confirmed' ? 'bg-primary' : 'bg-secondary']">Confirm</span>
                    <span class="border"></span>

                    <span
                        :class="['p-2 text-white rounded-end', purchaseInfo.status == 'cancelled' ? 'bg-primary' : 'bg-secondary']">Cancel</span>
                </div>
            </div>
            <div class="card-body">
                <form @submit="updatePurchaseData(purchaseInfo.id)">
                    <div class="row mb-3">
                        <!-- Vendor Info -->
                        <div class="col-6">
                            <div class="mb-3">
                                <label for="vendor">Vendor:</label>
                                <select x-model="data.vendor_id" class="form-control">
                                    <option value="">Select Vendor</option>
                                    <template x-for="vendor in allVendor" :key="vendor.id">
                                        <option :value="vendor.id" x-text="vendor.name"></option>
                                    </template>
                                </select>
                            </div>

                            <template x-if="vendorInfo">
                                <div>
                                    <div class="mb-2">
                                        <label>Company</label>
                                        <input type="text" class="form-control" :value="vendorInfo.company"
                                            disabled>
                                    </div>
                                    <div class="mb-2">
                                        <label>Email</label>
                                        <input type="text" class="form-control" :value="vendorInfo.email"
                                            disabled>
                                    </div>
                                    <div class="mb-2">
                                        <label>Address</label>
                                        <input type="text" class="form-control" :value="vendorInfo.address"
                                            disabled>
                                    </div>
                                    <div class="mb-2">
                                        <label>Phone</label>
                                        <input type="text" class="form-control" :value="vendorInfo.phone"
                                            disabled>
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
                        <button class="btn btn-warning">Update Purchase</button>
                        <template x-if="purchaseInfo.status == 'draft'">
                            <div>
                                <button @click.prevent="confirmOrder(purchaseInfo.id)" class="btn btn-success">Confirm
                                    Order</button>
                            </div>
                        </template>

                        <template x-if="purchaseInfo.status == 'confirmed'">
                            <button @click.prevent="cancelOrder(purchaseInfo.id)" class="btn btn-danger">Purchase
                                Return</button>
                        </template>

                        <template x-if="purchaseInfo.status == 'cancelled'">
                            <div> order is cancelled</div>
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
</div>
