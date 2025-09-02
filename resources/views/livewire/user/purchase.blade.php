<div x-data="purchase">
    <template x-if="message">
        <div class="message bg-success " x-text="message"></div>
    </template>
    <nav>
        <ul class="d-flex list gap-3 bg-success py-1 px-3 rounded">
            <li>Purchase List
            </li>
            <span class="border border-white"></span>
            <li>Create
                Purchase</li>
        </ul>
    </nav>

    <div class="card">
        <div class="card-header">
            Create Purchase
        </div>
        <div class="card-body">
            <form>
                <div class="row">
                    <div class="col-3">
                        <div class="mb-2">
                            <label for="vendor">Vendor:</label>
                            <select id="vendor" x-model="data.vendor_id" class="form-control">
                                <option value="" disabled>Select Vendor</option>
                                <template x-for="vendor in allVendor">
                                    <option :value="vendor.id" :key="vendor.id" x-text="vendor.name"></option>
                                </template>
                            </select>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

</div>
