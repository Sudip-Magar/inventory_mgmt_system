 <div class="container d-flex justify-content-center align-items-center min-vh-100">
     <div class="card shadow w-100" style="max-width: 500px;">
         <div class="card-body p-4">
             <h2 class="text-center mb-4">Register</h2>

             <form wire:submit.prevent='store'>
                 <div class="mb-3 text-center user-image">
                     <div class="text-center images">
                         @if ($image)
                             <img class="d-inline-block" src="{{ $image->temporaryUrl() }}" alt="">
                         @else
                             <img class="d-inline-block" src="{{ asset('storage/common/images.png') }}" alt="">
                         @endif
                     </div>
                     <label class="d-inline-block bg-primary my-2 rounded py-1 px-3" for="image">Upload Image</label>
                     <input type="file" class="form-control d-none" id="image" wire:model='image'>
                     <small class="text-danger">
                         @error('image')
                             {{ $message }}
                         @enderror
                     </small>
                 </div>

                 <div class="mb-3">
                     <label for="name" class="form-label">Name</label>
                     <input type="text" class="form-control" id="name" wire:model='name'
                         placeholder="Enter your name">
                     <small class="text-danger">
                         @error('name')
                             {{ $message }}
                         @enderror
                     </small>
                 </div>

                 <div class="mb-3">
                     <label for="email" class="form-label">Email</label>
                     <input type="email" class="form-control" id="email" wire:model='email'
                         placeholder="Enter your email">
                     <small class="text-danger">
                         @error('email')
                             {{ $message }}
                         @enderror
                     </small>
                 </div>

                 <div class="mb-3">
                     <label for="phone" class="form-label">Phone</label>
                     <input type="text" class="form-control" id="phone" wire:model='phone'
                         placeholder="Enter your phone">
                     <small class="text-danger">
                         @error('phone')
                             {{ $message }}
                         @enderror
                     </small>
                 </div>

                 <div class="mb-3">
                     <label for="password" class="form-label">Create Password</label>
                     <input type="password" class="form-control" id="password" wire:model='password'
                         placeholder="Enter your password">
                     <small class="text-danger">
                         @error('password')
                             {{ $message }}
                         @enderror
                     </small>
                 </div>

                 <div class="mb-4">
                     <label for="course" class="form-label">Role</label>
                     <select class="form-select" id="course" wire:model='role'>
                         <option value="">Choose Role</option>
                         <option value="0">User</option>
                         <option value="1">Manager</option>
                         <option value="2">Admin</option>
                     </select>
                     <small class="text-danger">
                         @error('role')
                             {{ $message }}
                         @enderror
                     </small>
                 </div>

                 <div class="d-grid mb-3">
                     <button type="submit" class="btn btn-primary">Register</button>
                 </div>

                 {{-- <div class="text-center">
                        Already have an account? <a href="/">Login</a>
                    </div> --}}
             </form>
         </div>
     </div>
 </div>
