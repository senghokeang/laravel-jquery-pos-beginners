<!-- Laravel POS With jQuery @ https://laravelcenter.com -->
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="images/favicon.png">
    <title>{{ env('APP_NAME') }}</title>
    <!-- Google Fonts -->
    <link href="https://fonts.gstatic.com" rel="preconnect">
    <link
        href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i"
        rel="stylesheet">
    @vite('resources/js/cashier.js')
</head>

<body style="display: none">
    <!-- Error Modal -->
    <div class="fade modal" tabindex="-1" id="errorModal" style="z-index: 5000;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header py-2 bg-danger text-light">
                    <h5 class="modal-title">ERROR</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="fs-5"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">OK</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Success Modal -->
    <div class="fade modal" tabindex="-1" id="successModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header py-2 bg-success text-light">
                    <h5 class="modal-title">SUCCESS</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="fs-5"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Ok</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Delete Modal -->
    <div class="fade modal" tabindex="-1" id="confirmDelete">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header py-2 bg-danger text-light">
                    <h5 class="modal-title">Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="fs-5">Are you sure want to delete?</p>
                </div>
                <div class="modal-footer">
                    <form id="deleteForm" method="post" style="padding-bottom: 0px;margin-bottom: 0px;">
                        @method('DELETE')
                        @csrf
                        <input type="hidden" name="delete_id" id="delete_id" />
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger" data-bs-dismiss="modal">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Form Modal -->
    <div class="modal fade" id="formModal" tabindex="-1" aria-hidden="true" data-bs-keyboard="false"
        data-bs-focus="false">
        <div class="modal-dialog modal-lg">
            <div class="modal-content"></div>
        </div>
    </div>
    <!-- For Bill and Receipt Printing -->
    <div id="printContent" class="d-none"></div>
    <header id="header" class="header fixed-top d-flex align-items-center">

        <div class="d-flex align-items-center justify-content-between">
            <a href="{{ url('/') }}" class="logo d-flex align-items-center">
                <img src="images/logo.png" alt="">
            </a>
        </div>

        <nav class="header-nav ms-auto">
            <ul class="d-flex align-items-center">
                <li class="d-none d-md-inline-block form-inline ms-auto nav-item dropdown me-5">
                    <i class="bi bi-alarm-fill text-secondary pe-2"></i>
                    <span class="text-secondary">{{ date('d-M-Y H:i:s') }}</span>
                </li>
                <li class="nav-item dropdown pe-3">
                    <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#"
                        data-bs-toggle="dropdown">
                        <i class="bi bi-person-fill" style="font-size: 35px;"></i>
                        <span
                            class="d-none d-md-block dropdown-toggle ps-2">{{ ucwords(request()->user()?->username) }}</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
                        <li>
                            <button class="dropdown-item d-flex align-items-center"
                                onclick="ajaxPopup(`{{ url('user/change-password') }}`,false)">
                                <i class="bi bi-shield-lock"></i>
                                <span>Change Password</span>
                            </button>
                        </li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li>
                            <form method="post" action="{{ url('./user/logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item d-flex align-items-center">
                                    <i class="bi bi-box-arrow-right"></i>
                                    <span>Sign Out</span>
                                </button>
                            </form>
                        </li>
                    </ul>
                </li>
            </ul>
        </nav>

    </header>

    <main id="main" class="main ms-0 pt-3">
        <div class="row">
            <div class="col-xl-7 col-lg-6">
                <div class="card mb-0" style="height: 88vh">
                    <ul class="nav nav-tabs nav-fill">
                        <li class="nav-item">
                            <span class="nav-link menu-item active" style="cursor: pointer" data-category="0"
                                onclick="getProduct(event)">ALL</span>
                        </li>
                        @foreach ($product_categories as $category)
                        <li class="nav-item">
                            <span class="nav-link menu-item" style="cursor: pointer"
                                data-category="{{ $category->id }}"
                                onclick="getProduct(event)">{{ $category->name }}</span>
                        </li>
                        @endforeach
                        <li class="nav-item">
                            <span class="nav-link menu-item" style="padding: 5px; background: whitesmoke !important">
                                <div class="input-group">
                                    <input type="text" class="form-control" placeholder="Search..."
                                        onchange="getProduct()" id="search_product" style="background: lightyellow">
                                    <button class="btn btn-success" type="button"
                                        onclick="getProduct()">Search</button>
                                </div>
                            </span>
                        </li>
                    </ul>
                    <div class="card-body p-1" style="overflow-y: scroll">
                        <div class="row row-cols-2 row-cols-sm-3 row-cols-lg-4 row-cols-xl-5 g-1" id="productList">

                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-5 col-lg-6">
                <div class="card mb-0" style="height: 88vh" id="orderList"> </div>
            </div>
        </div>
    </main>

    <div class="loading"></div>
</body>

</html>