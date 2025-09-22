<div x-data="purchaseReturn">
    <template x-if="message">
        <div class="message bg-success" x-text="message"></div>
    </template>

    <template x-if="error">
        <div class="message bg-danger" x-text="error"></div>
    </template>
    {{-- Purchase List --}}
    <div class="card" x-show="viewList">
        <div class="card-header">
            <strong>Purchase Return</strong>
        </div>

        <div class="card-body">
            <table class="table table-bordered table-hover table-striped">
                <thead>
                    <tr>
                        <th>S.No</th>
                        <th>Vendor Name</th>
                        <th>Total Quantity</th>
                        <th>Total Discount</th>
                        <th>Total Amount</th>
                        <th>Status</th>
                        <th>Return Date</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="(purchaseReturn, idx) in allpurchaseReturn">
                        <tr class="pointer" @click.prevent="updateListToggle(purchaseReturn.id)"
                            :key="purchaseReturn.id">
                            <td x-text="idx +1"></td>
                            <td x-text="purchaseReturn.purchase.vendor.name"></td>
                            <td x-text="purchaseReturn.total_quantity"></td>
                            <td x-text="purchaseReturn.total_discount_amt"></td>
                            <td x-text="purchaseReturn.total_amount"></td>
                            <td>
                                <template x-if="purchaseReturn.status == 'draft'">
                                    <span class="bg-danger px-3 py-1 rounded-pill text-white"> Draft</span>
                                </template>

                                <template x-if="purchaseReturn.status == 'confirmed'">
                                    <span class="bg-success px-3 py-1 rounded-pill text-white"> confirmed</span>
                                </template>

                                <template x-if="purchaseReturn.status == 'cancelled'">
                                    <span class="bg-warning px-3 py-1 rounded-pill text-white"> Cancelled</span>
                                </template>
                            </td>
                            <td
                                x-text="new Date(purchaseReturn.created_at).toLocaleDateString('ne-NP', {
                                year: 'numeric',
                                month: 'long',
                                day: 'numeric',
                                hour: '2-digit',
                                minute: '2-digit'
                            })">
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>

    {{-- Update Purchase Return --}}
    <div>
        <div class="card" x-show="viewUdate">
            <div class="card-header d-flex justify-content-between align-items-center">
                <strong>Update Purchase</strong>
                <div class="flex gap-0 mx-0 d-inline-flex">
                    <span
                        :class="['p-2 text-white rounded-start', purchaseInfo.status == 'draft' ? 'bg-primary active' :
                            'bg-secondary'
                        ]">Draft</span>
                    <span class="border"></span>
                    <span
                        :class="['p-2 text-white', purchaseInfo.status == 'confirmed' ? 'bg-primary' : 'bg-secondary']">Confirm</span>
                    <span class="border"></span>

                    <span
                        :class="['p-2 text-white rounded-end', purchaseInfo.status == 'cancelled' ? 'bg-primary' :
                            'bg-secondary'
                        ]">Cancel</span>
                </div>
            </div>
            <div class="card-body">
                <!-- Validation Error Banner -->
                <template x-if="Object.keys(errors).length > 0">
                    <div class="alert alert-danger" role="alert">
                        <h6 class="alert-heading">Validation Errors:</h6>
                        <ul class="mb-0">
                            <template x-for="error in Object.values(errors)" :key="error">
                                <li x-text="error"></li>
                            </template>
                        </ul>
                    </div>
                </template>

                <form>
                    <div class="row mb-3">
                        <!-- Vendor Info -->
                        <div class="col-6">
                            <div class="mb-3">
                                <label for="vendor">Vendor:</label>
                                <select x-model="data.vendor_id" class="form-control" disabled>
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
                                        <input type="text" class="form-control" :value="vendorInfo.company" disabled>
                                    </div>
                                    <div class="mb-2">
                                        <label>Email</label>
                                        <input type="text" class="form-control" :value="vendorInfo.email" disabled>
                                    </div>
                                    <div class="mb-2">
                                        <label>Address</label>
                                        <input type="text" class="form-control" :value="vendorInfo.address"
                                            disabled>
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
                                <input type="text" class="form-control" :value="totalNetAmount.toFixed(2)" disabled>
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
                                            min="0">
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
                                <!-- Error message row -->
                                <template x-if="errors[`item_${index}`]">
                                    <tr>
                                        <td colspan="8" class="text-danger bg-light">
                                            <small x-text="errors[`item_${index}`]"></small>
                                        </td>
                                    </tr>
                                </template>
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
                            <!-- Total error message -->
                            <template x-if="errors.total">
                                <tr>
                                    <td colspan="8" class="text-danger bg-light">
                                        <strong x-text="errors.total"></strong>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>

                    <div class="mb-3 d-flex gap-2">
                        <template x-if="purchaseInfo.status == 'draft'">
                            <div>
                                <button @click.prevent="updatePurchaseData(purchaseInfo.id)"
                                    class="btn btn-warning">Update Purchase</button>
                                <button @click.prevent="confirmOrder(purchaseInfo.id)" class="btn btn-success">Confirm
                                    Order</button>
                                <button class="btn btn-danger" @click.prevent="cancelOrder(purchaseInfo.id)">Cancel
                                    Purchase</button>
                            </div>
                        </template>

                        <template x-if="purchaseInfo.status == 'confirmed'">
                            <div>
                                <button class="btn btn-danger text-white"
                                    @click.prevent="cancelOrder(purchaseInfo.id)">Cancel Purchase</button>
                            </div>
                        </template>

                        <template x-if="purchaseInfo.status == 'cancelled'">
                            <button @click.prevent="resetToDraft(purchaseInfo.id)" class="btn btn-warning">Reset to
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
</div>
