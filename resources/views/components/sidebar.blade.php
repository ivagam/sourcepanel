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
                  <a href="{{ route('categoryList') }}">
                        <iconify-icon icon="mdi:format-list-bulleted" class="menu-icon"></iconify-icon>
                    <span>Category List</span>
                </a>
            </li>

            <li>
                  <a href="{{ route('addcategory') }}">
                        <iconify-icon icon="mdi:plus-box" class="menu-icon"></iconify-icon>
                    <span>Add Category</span>
                </a>
            </li>

            <li>
                  <a href="{{ route('productList') }}">
                        <iconify-icon icon="mdi:shopping-outline" class="menu-icon"></iconify-icon>
                    <span>Product List</span>
                </a>
            </li>
            
            <li>
                <a href="{{ route('addProduct', ['main_category' => 1]) }}">
                    <iconify-icon icon="mdi:plus-circle-outline" class="menu-icon"></iconify-icon>
                    <span>Watches</span>
                </a>
            </li>

            <li>
                <a href="{{ route('addProduct', ['main_category' => 113]) }}">
                    <iconify-icon icon="mdi:plus-circle-outline" class="menu-icon"></iconify-icon>
                    <span>Others Brands</span>
                </a>
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