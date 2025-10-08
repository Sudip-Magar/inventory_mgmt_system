 <div class="container d-flex justify-content-center align-items-center min-vh-100">
     <div class="card shadow w-100" style="max-width: 500px;">
         <div class="card-body p-4">
             <h2 class="text-center mb-4">Login</h2>

             <form wire:submit.prevent='check'>

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
                     <label for="password" class="form-label">Create Password</label>
                     <input type="password" class="form-control" id="password" wire:model='password'
                         placeholder="Enter your password">
                     <small class="text-danger">
                         @error('password')
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
