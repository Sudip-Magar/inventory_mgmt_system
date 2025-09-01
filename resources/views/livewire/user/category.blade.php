<div x-data="category">
    <template x-if="message">
        <div class="message bg-success " x-text="message"></div>
    </template>
    <nav>
        <ul class="d-flex list gap-3 bg-success py-1 px-3 rounded text-white">
            <li>Category List</li>
            <span class="border border-white"></span>
            <li>Create Category</li>
        </ul>
    </nav>

    <div>
        {{-- category list --}}
        <div class="card">
            <div class="card-header">Category List</div>
            
        </div>
        {{-- Create Category --}}
        <div class="card" >
            <div class="card-header">
                Create New Category
            </div>
            <div class="card-body">
                <form action="" @submit.prevent="store">
                    <div class="mb-2">
                        <label class="form-label" for="name">Name</label>
                        <input type="text" id="name" class="form-control" placeholder="Enter your name" x-model="data.name">
                        <template x-if="errors.name">
                            <span class="text-danger" x-text="errors.name"></span>
                        </template>
                        
                    </div>

                    <div class="mb-2">
                        <label for="description" class="form-label">Description</label>
                        <textarea placeholder="write description about the product" id="description" rows="10" class="form-control" x-model="data.description"></textarea>
                    </div>

                    <div class="mb-2">
                        <button class="btn btn-success">Create Category</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
