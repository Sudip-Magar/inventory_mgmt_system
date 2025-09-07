<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ $title ?? 'Page Title' }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
        integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css">

    @vite(['resources/css/app.css', 'resources/css/side.css', 'resources/js/app.js'])
</head>

<body>
    <div class="d-flex">
        {{-- Sidebar --}}
        <aside id="sidebar" class="sidebar-toggle">
            <div class="sidebar-logo">
                <a href="#">Inventory</a>
            </div>

            {{-- sidebar Navigation --}}
            <ul class="sidebar-nav p-0">
                <li class="sidebar-item">
                    <a href="{{ url('home') }}" class="sidebar-links">
                        <i class="fa-solid fa-chart-line"></i>
                        <span>Dashboard</span>
                    </a>
                </li>

                <li class="sidebar-item">
                    <a href="{{ url('product') }}" class="sidebar-links">
                        <i class="fa-solid fa-cubes"></i>
                        <span>Product</span>
                    </a>
                </li>

                <li class="sidebar-item">
                    <a href="{{ url('purchase') }}" class="sidebar-links">
                        <i class="fa-solid fa-cart-shopping"></i>
                        <span>Pruchase</span>
                    </a>
                </li>

                <li class="sidebar-item">
                    <a href="{{ url('sale') }}" class="sidebar-links">
                        <i class="fa-solid fa-dollar-sign"></i>
                        <span>Sales</span>
                    </a>
                </li>

                <li class="sidebar-item">
                    <a href="{{ url('customer') }}" class="sidebar-links">
                        <i class="fa-solid fa-users"></i>
                        <span>Customer</span>
                    </a>
                </li>

                <li class="sidebar-item">
                    <a href="{{ url('vendor') }}" class="sidebar-links">
                        <i class="fa-solid fa-industry"></i>
                        <span>Vendor</span>
                    </a>
                </li>

                <li class="sidebar-item">
                    <a href="{{ url('category') }}" class="sidebar-links">
                        <i class="fa-solid fa-layer-group"></i>
                        <span>Category</span>
                    </a>
                </li>

                <li class="sidebar-item">
                    <a href="{{ url('movement') }}" class="sidebar-links">
                        <i class="fa-solid fa-warehouse"></i>
                        <span>Stock Movement</span>
                    </a>
                </li>


                <li class="sidebar-item">
                    <a href="{{ url('discount') }}" class="sidebar-links">
                        <i class="fa-solid fa-percent"></i>
                        <span>Discount</span>
                    </a>
                </li>

                <li class="sidebar-item">
                    <a href="{{ url('profile') }}" class="sidebar-links">
                        <i class="fa-solid fa-gear"></i>
                        <span>Setting</span>
                    </a>
                </li>

                <div class="logout-btn">
                    <form action="/logout" method="post">
                        @csrf
                        <button class="">
                            <i class="fa-solid fa-right-from-bracket"></i>
                            <span>Logout</span>
                        </button>
                    </form>
                </div>
            </ul>



        </aside>

        <div class="main">
            <nav class="navbar navbar-expand border-bottom ">
                <button class="toggle-btn" type="button"><i class="fa-solid fa-bars-staggered"></i></button>
                <div class="userImg">
                    {{-- @if ($user)
                        <img class="dashboard-image" src="{{ asset('storage/' . $user->image) }}" alt="User Image">
                        @if ($user->role == '2')
                            <span>{{ $user->name }} (Admin)</span>
                        @elseif($user->role == '1')
                            <span>{{ $user->name }} (Manager)</span>
                        @else
                            <span>{{ $user->name }} (User)</span>
                        @endif
                    @endif --}}
                </div>
            </nav>

            <main class="p-3">
                <div class="container-fluid">
                    <div class="mb-3">
                        {{ $slot }}
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
