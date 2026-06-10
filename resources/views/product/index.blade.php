@extends('layouts.auth')
@section('styleCss')
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
@endsection

@section('content')
    <div class="main-content">
        <div class="page-content">
            <div class="container-fluid">
                <!-- HEADING -->
                <div class="d-flex justify-content-end mb-3">
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addProductModal">
                        <i class="bi-plus-circle"></i>
                        Add Product
                    </button>
                </div>
                <!-- TABS -->
                <ul class="nav nav-tabs mb-3">
                    <li class="nav-item">
                        <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#activeTab">
                            All Products
                            <span class="badge bg-primary ms-1">{{ $product->count() }}</span>
                        </button>

                    </li>
                    <li class="nav-item">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#trashTab">
                            Trash Products
                            <span class="badge bg-primary ms-1">{{ $trashPro->count() }}</span>
                        </button>
                    </li>
                </ul>
                <!-- TAB CONTENT -->
                <div class="tab-content">
                    <!-- ACTIVE TAB -->
                    <div class="tab-pane fade show active" id="activeTab">
                        <div id="show_all_products"></div>
                    </div>
                    <!-- TRASH TAB -->
                    <div class="tab-pane fade" id="trashTab">
                        <div id="show_trash_products"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ADD MODAL -->
    <div class="modal fade" id="addProductModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="add_product_form" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Add Product</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label>Name</label>
                                <input type="text" name="name" class="form-control">
                            </div>

                            <div class="col-md-6">
                                <label>Category</label>
                                <select name="category_id" id="" class="form-control">
                                    <option value="">Select Category</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label>Code</label>
                                <input type="text" name="code" class="form-control">
                            </div>

                            <div class="col-md-6">
                                <label>Buy Price</label>
                                <input type="text" name="buy_price" class="form-control">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label>Sell Price</label>
                                <input type="text" name="sell_price" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label>Supplier</label>
                                <select name="supplier_id" id="" class="form-control">
                                    <option value="">Select Supplier</option>
                                    @foreach ($suppliers as $supplier)
                                        <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label>Buy Date</label>
                                <input type="date" name="buy_date" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label>Quantity</label>
                                <input type="number" name="quantity" class="form-control">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label>Image</label>
                            <input type="file" name="image" class="form-control">
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-success">
                            Save Product
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Edit Modal --}}
    <div class="modal fade" id="editProductModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="edit_product_form" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="product_id" id="edit_product_id">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Product</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label>Name</label>
                                <input type="text" name="name" id="edit_name" class="form-control">
                            </div>

                            <div class="col-md-6">
                                <label>Category</label>
                                <select name="category_id" id="edit_category_id" class="form-control">
                                    <option value="">Select Category</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label>Code</label>
                                <input type="text" name="code" id="edit_code" class="form-control">
                            </div>

                            <div class="col-md-6">
                                <label>Buy Price</label>
                                <input type="text" name="buy_price" id="edit_buy_price" class="form-control">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label>Sell Price</label>
                                <input type="text" name="sell_price" id="edit_sell_price" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label>Supplier</label>
                                <select name="supplier_id" id="edit_supplier_id" class="form-control">
                                    <option value="">Select Supplier</option>
                                    @foreach ($suppliers as $supplier)
                                        <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label>Buy Date</label>
                                <input type="date" name="buy_date" id="edit_buy_date" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label>Quantity</label>
                                <input type="number" name="quantity" id="edit_quantity" class="form-control">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label>Image</label>
                            <input type="file" name="image" id="edit_image" class="form-control">
                            <div id="show_image" class="mt-2"></div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-success">
                                Update Product
                            </button>
                        </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <!-- Sweet Alert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- DataTables -->
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>

    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // ADD PRODUCT
        $("#add_product_form").submit(function(e) {
            e.preventDefault();
            let formData = new FormData(this);
            $.ajax({
                url: '{{ route('products.store') }}',
                method: 'POST',
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                success: function(response) {
                    if (response.status == 200) {
                        Swal.fire(
                            'Added!',
                            response.message,
                            'success'
                        );
                        fetchAllProducts();
                        $("#add_product_form")[0].reset();
                        $('#addProductModal').modal('hide');
                        $('.modal-backdrop').remove();
                        $('body').removeClass('modal-open');
                        $('body').css('padding-right', '');
                    }
                }
            });
        });

        // fetch all products
        fetchAllProducts();

        function fetchAllProducts() {
            $.ajax({
                url: '{{ route('products.fetchAll') }}',
                method: 'GET',
                success: function(response) {
                    $("#show_all_products").html(response);
                    if ($("#productsTable tbody tr td[colspan]").length == 0) {
                        $("#productsTable").DataTable();

                    }
                }
            });
        }

        // EDIT PRODUCT
        $(document).on('click', '.editIcon', function(e) {
            e.preventDefault();
            let id = $(this).attr('id');
            $.ajax({
                url: '{{ route('products.edit') }}',
                method: 'GET',
                data: {
                    id: id
                },
                success: function(response) {

                    $("#edit_product_id").val(response.id);
                    $("#edit_name").val(response.name);
                    $("#edit_category_id").val(response.category_id);
                    $("#edit_code").val(response.code);
                    $("#edit_buy_price").val(response.buy_price);
                    $("#edit_sell_price").val(response.sell_price);
                    $("#edit_buy_date").val(response.buy_date);
                    $("#edit_supplier_id").val(response.supplier_id);
                    $("#edit_quantity").val(response.quantity);
                    $("#show_image").html(
                        `<img src="/storage/${response.image}" width="80" class="img-thumbnail rounded-circle">`
                    );
                    $("#editProductModal").modal('show');
                }
            });
        });

        // UPDATE PRODUCT
        $("#edit_product_form").submit(function(e) {
            e.preventDefault();
            let formData = new FormData(this);
            $.ajax({
                url: '{{ route('products.update') }}',
                method: 'POST',
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                success: function(response) {
                    if (response.status == 200) {
                        Swal.fire(
                            'Updated!',
                            response.message,
                            'success'
                        );
                        $("#edit_product_form")[0].reset();
                        let modal =
                            bootstrap.Modal.getInstance(
                                document.getElementById('editProductModal')
                            );
                        modal.hide();
                        fetchAllProducts();
                    }
                }
            });
        });

        // SOFT DELETE
        $(document).on('click', '.deleteIcon', function(e) {
            e.preventDefault();
            let id = $(this).attr('id');
            Swal.fire({
                title: 'Are you sure?',
                text: "Product will move to trash!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes Delete'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ route('products.delete') }}',
                        method: 'POST',
                        data: {
                            id: id
                        },
                        success: function(response) {
                            Swal.fire(
                                'Deleted!',
                                response.message,
                                'success'
                            );
                            fetchAllProducts();
                            fetchTrashProducts();
                        }
                    });
                }
            });
        });

        // FETCH TRASH
        fetchTrashProducts();

        function fetchTrashProducts() {
            $.ajax({
                url: '{{ route('products.fetchTrash') }}',
                method: 'GET',
                success: function(response) {
                    $("#show_trash_products").html(response);
                    if ($("#trashTable tbody tr td[colspan]").length == 0) {
                        $("#trashTable").DataTable();
                    }
                }
            });
        }


        // RESTORE
        $(document).on('click', '.restoreIcon', function(e) {
            e.preventDefault();
            let id = $(this).attr('id');
            $.ajax({
                url: '{{ route('products.restore') }}',
                method: 'POST',
                data: {
                    id: id
                },
                success: function(response) {
                    Swal.fire(
                        'Restored!',
                        response.message,
                        'success'
                    );
                    fetchAllProducts();
                    fetchTrashProducts();
                }
            });
        });

        // PERMANENT DELETE
        $(document).on('click', '.permanentDeleteIcon', function(e) {
            e.preventDefault();
            let id = $(this).attr('id');
            Swal.fire({
                title: 'Are you sure?',
                text: "This data will be permanently deleted!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                confirmButtonText: 'Yes Delete'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ route('products.permanentDelete') }}',
                        method: 'POST',
                        data: {
                            id: id
                        },
                        success: function(response) {
                            Swal.fire('Deleted!', response.message, 'success');
                            fetchTrashProducts();
                        }
                    });
                }
            });
        });
    </script>
@endpush
