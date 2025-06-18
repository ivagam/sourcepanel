<aside class="sidebar">
    <button type="button" class="sidebar-close-btn">
        <iconify-icon icon="radix-icons:cross-2"></iconify-icon>
    </button>
    <div>
        <a href="{{ route('index') }}" class="sidebar-logo">
            <img src="{{ asset('assets/images/sourcepanel.webp') }}" alt="site logo" class="light-logo">
            <img src="{{ asset('assets/images/logo-light.png') }}" alt="site logo" class="dark-logo">
            <img src="{{ asset('assets/images/logo-icon.png') }}" alt="site logo" class="logo-icon">
        </a>
    </div>
    <div class="sidebar-menu-area">
        <ul class="sidebar-menu" id="sidebar-menu">
           
            <li class="sidebar-menu-group-title">Application</li>

            <li>
                  <a href="{{ route('index') }}">
                    <iconify-icon icon="heroicons:home" class="menu-icon"></iconify-icon>
                    <span>Dashboard</span>
                </a>
            </li>

            <li>
                  <a href="{{ route('categoryIndex') }}">
                    <iconify-icon icon="heroicons:table-cells" class="menu-icon"></iconify-icon>
                    <span>Category</span>
                </a>
            </li>
            
            <li class="dropdown">
                <a  href="javascript:void(0)">
                    <iconify-icon icon="heroicons:film" class="menu-icon"></iconify-icon>
                    <span>Media</span>
                </a>
                <ul class="sidebar-submenu">
                    <li>
                        <a  href="{{ route('mediaList') }}"><i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> Meida List</a>
                    </li>
                    <li>
                        <a  href="{{ route('addMedia') }}"><i class="ri-circle-fill circle-icon text-warning-main w-auto"></i> Add Media</a>
                    </li>
                    
                </ul>
            </li>

            <li class="dropdown">
                <a  href="javascript:void(0)">
                    <iconify-icon icon="heroicons:cube" class="menu-icon"></iconify-icon>
                    <span>Product</span>
                </a>
                <ul class="sidebar-submenu">
                    <li>
                        <a  href="{{ route('productList') }}"><i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> Product List</a>
                    </li>
                    <li>
                        <a  href="{{ route('addProduct') }}"><i class="ri-circle-fill circle-icon text-warning-main w-auto"></i> Add Product</a>
                    </li>
                    
                </ul>
            </li>

            <li>
                  <a href="{{ route('customerIndex') }}">
                    <iconify-icon icon="heroicons:users" class="menu-icon"></iconify-icon>
                    <span>Customer</span>
                </a>
            </li>

            <li>
                  <a href="{{ route('salesIndex') }}">
                    <iconify-icon icon="heroicons:shopping-cart" class="menu-icon"></iconify-icon>
                    <span>Sales</span>
                </a>
            </li>             
            
            <li class="dropdown">
                <a  href="javascript:void(0)">
                    <iconify-icon icon="heroicons:user" class="menu-icon"></iconify-icon>
                    <span>Users</span>
                </a>
                <ul class="sidebar-submenu">
                    <li>
                        <a  href="{{ route('usersList') }}"><i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> User List</a>
                    </li>
                    <li>
                        <a  href="{{ route('addUser') }}"><i class="ri-circle-fill circle-icon text-warning-main w-auto"></i> Add User</a>
                    </li>
                    
                </ul>
            </li>
            <li class="dropdown">
                <a href="javascript:void(0)">
                    <iconify-icon icon="heroicons:wrench-screwdriver" class="menu-icon"></iconify-icon>
                    <span>Setting</span>
                </a>
                <ul class="sidebar-submenu">
                    
                    <li>
                        <a href="{{ route('domainIndex') }}">
                            <iconify-icon icon="heroicons:globe-alt" class="menu-icon"></iconify-icon>
                            <span>Domain</span>
                        </a>
                    </li>           

                    <li>
                        <a href="{{ route('bannerIndex') }}">
                            <iconify-icon icon="heroicons:photo" class="menu-icon"></iconify-icon>
                            <span>Banner</span>
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('brandIndex') }}">
                            <iconify-icon icon="heroicons:bookmark" class="menu-icon"></iconify-icon>
                            <span>Brand</span>
                        </a>
                    </li>
                    
                </ul>
            </li>
                </ul>
            </li>
            
        </ul>
    </div>
</aside>