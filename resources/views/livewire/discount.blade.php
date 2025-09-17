<div x-data="discount">
    <nav>
        <ul class="d-flex list gap-3 bg-success py-1 px-3 rounded">
            <li :class="list ? 'text-warning' : 'text-white'" @click.prevent="listToggle">Discount List</li>
            <span class="border border-white"></span>
            <li :class="create ? 'text-warning' : 'text-white'" @click.prevent="createToggle">Create Discount</li>
        </ul>
    </nav>

    {{-- Create Discount --}}
    <div class="card" x-show="create" x-cloak>
        <div class="card-header">
            Create New Discount
        </div>
        <div class="card-body">
            <form wire:submit.prevent="store">
                <div class="row">
                    <div class="col-6">
                        <div class="mb-3">
                            <label for="code">Code:</label>
                            <input type="text" id="code" class="form-control" placeholder="Enter Discount Code"
                                wire:model='code'>
                            @error('code')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="name">Name:</label>
                            <input type="text" id="name" class="form-control" placeholder="Enter Discount Name"
                                wire:model='name'>
                            @error('Name')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <input type="checkbox"id="isItemWire" wire:model='is_item_wise'>
                            <label for="isItemWire">Is Discount Counted Item Wise?</label>
                        </div>
                    </div>

                    <div class="col-6">
                        <div class="mb-3">
                            <label for="sign">Sign:</label>
                            <select name="" id="sign" class="form-control" wire:model='sign'>
                                <option value="">----Select Sign----</option>
                                <option value="-">-</option>
                                <option value="+">+</option>
                            </select>
                            @error('sign')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="rate">Rate:</label>
                            <input type="number" id="rate" class="form-control" wire:model='rate'>
                            @error('rate')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="mb-3">
                    <button class="btn btn-success">Create Discount</button>
                </div>
            </form>

        </div>
    </div>

    {{-- All Discount --}}
    <div class="card" x-show="list">
        <div class="card-header">
            All Discount
        </div>
        <div class="card-body">
            <table class="table table-hover table-bordered">
                <thead>
                    <tr>
                        <th>Code</th>
                        <th>Name</th>
                        <th>Sign</th>
                        <th>Rate</th>
                        <th>Item Wise</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($discounts as $discount)
                        <tr>
                            <td>{{ $discount->code }}</td>
                            <td>{{ $discount->name }}</td>
                            <td>{{ $discount->sign }}</td>
                            <td>{{ $discount->rate }}</td>
                            <td>
                                {{ $discount->isItemWise ? '✔' : '✘' }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
