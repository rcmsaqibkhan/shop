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
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addSupplierModal">
                        <i class="bi-plus-circle"></i>
                        Add Supplier
                    </button>
                </div>
                <!-- TABS -->
                <ul class="nav nav-tabs mb-3">
                    <li class="nav-item">
                        <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#activeTab">
                            All Suppliers
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#trashTab">
                            Trash Suppliers
                        </button>
                    </li>
                </ul>

                <!-- TAB CONTENT -->
                <div class="tab-content">
                    <!-- ACTIVE TAB -->
                    <div class="tab-pane fade show active" id="activeTab">
                        <div id="show_all_suppliers"></div>
                    </div>
                    <!-- TRASH TAB -->
                    <div class="tab-pane fade" id="trashTab">
                        <div id="show_trash_suppliers"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ADD MODAL -->
    <div class="modal fade" id="addSupplierModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="add_supplier_form" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Add Supplier</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <!-- NAME -->
                        <div class="mb-3">
                            <label>Name</label>
                            <input type="text" name="name" class="form-control">
                        </div>

                        <!-- EMAIL -->
                        <div class="mb-3">
                            <label>Email</label>
                            <input type="email" name="email" class="form-control">
                        </div>

                        <!-- PHONE -->
                        <div class="mb-3">
                            <label>Phone</label>
                            <input type="text" name="phone" class="form-control">
                        </div>
                        <!-- ADDRESS -->
                        <div class="mb-3">
                            <label>Address</label>
                            <input type="text" name="address" class="form-control">
                        </div>

                        {{-- SHOPNAME --}}
                        <div class="mb-3">
                            <label>Shop Name</label>
                            <input type="text" name="shop_name" class="form-control">
                        </div>

                        <!-- IMAGE -->
                        <div class="mb-3">
                            <label>Image</label>
                            <input type="file" name="image" class="form-control">
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-success">
                            Save Supplier
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- EDIT MODAL -->
    <div class="modal fade" id="editSupplierModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="edit_supplier_form" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="supplier_id" id="supplier_id">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            Edit Supplier
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <!-- NAME -->
                        <div class="mb-3">
                            <label>Name</label>
                            <input type="text" name="name" id="edit_name" class="form-control">
                        </div>
                        <!-- EMAIL -->
                        <div class="mb-3">
                            <label>Email</label>
                            <input type="email" name="email" id="edit_email" class="form-control">
                        </div>
                        <!-- PHONE -->
                        <div class="mb-3">
                            <label>Phone</label>
                            <input type="text" name="phone" id="edit_phone" class="form-control">
                        </div>

                        <!-- ADDRESS -->
                        <div class="mb-3">
                            <label>Address</label>
                            <input type="text" name="address" id="edit_address" class="form-control">
                        </div>

                        {{-- SHOPNAME --}}
                        <div class="mb-3">
                            <label>Shop Name</label>
                            <input type="text" name="shop_name" id="edit_shop_name" class="form-control">
                        </div>

                        <!-- IMAGE -->
                        <div class="mb-3">
                            <label>Image</label>
                            <input type="file" name="image" class="form-control">
                        </div>

                        <!-- OLD IMAGE -->
                        <div class="mb-3" id="show_image"></div>

                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">
                            Update Supplier
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

        // ADD SUPPLIER
        $("#add_supplier_form").submit(function(e) {
            e.preventDefault();
            let formData = new FormData(this);
            $.ajax({
                url: '{{ route('suppliers.store') }}',
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
                        fetchAllSuppliers();
                        $("#add_supplier_form")[0].reset();
                        $('#addSupplierModal').modal('hide');
                        $('.modal-backdrop').remove();
                        $('body').removeClass('modal-open');
                        $('body').css('padding-right', '');
                    }
                }
            });
        });

        // FETCH ACTIVE
        fetchAllSuppliers();

        function fetchAllSuppliers() {
            $.ajax({
                url: '{{ route('suppliers.fetchAll') }}',
                method: 'GET',
                success: function(response) {
                    $("#show_all_suppliers").html(response);
                    if ($("#supplierTable tbody tr td[colspan]").length == 0) {
                        if ($.fn.DataTable.isDataTable("#supplierTable")) {
                            $("#supplierTable").DataTable().destroy();
                        }
                        $("#supplierTable").DataTable();
                    }
                }
            });
        }

        // EDIT SUPPLIER
        $(document).on('click', '.editIcon', function(e) {
            e.preventDefault();
            let id = $(this).attr('id');
            $.ajax({
                url: '{{ route('suppliers.edit') }}',
                method: 'GET',
                data: {
                    id: id
                },
                success: function(response) {
                    $("#supplier_id").val(response.id);
                    $("#edit_name").val(response.name);
                    $("#edit_email").val(response.email);
                    $("#edit_phone").val(response.phone);
                    $("#edit_address").val(response.address);
                    $("#edit_shop_name").val(response.shop_name);
                    $("#show_image").html(
                        `
                    <img src="/storage/${response.image}"
                    width="80"
                    class="img-thumbnail rounded-circle">
                    `
                    );
                    $("#editSupplierModal").modal('show');
                }
            });
        });

        // UPDATE SUPPLIER
        $("#edit_supplier_form").submit(function(e) {
            e.preventDefault();
            let formData = new FormData(this);
            $.ajax({
                url: '{{ route('suppliers.update') }}',
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
                        $("#edit_supplier_form")[0].reset();
                        let modal =
                            bootstrap.Modal.getInstance(
                                document.getElementById('editSupplierModal')
                            );
                        modal.hide();
                        fetchAllSuppliers();
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
                text: "Supplier will move to trash!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes Delete'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ route('suppliers.delete') }}',
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
                            fetchAllSuppliers();
                            fetchTrashSuppliers();
                        }
                    });
                }
            });
        });

        // FETCH TRASH
        fetchTrashSuppliers();

        function fetchTrashSuppliers() {
            $.ajax({
                url: '{{ route('suppliers.fetchTrash') }}',
                method: 'GET',
                success: function(response) {
                    $("#show_trash_suppliers").html(response);
                    if ($("#trashTable tbody tr td[colspan]").length == 0) {
                        if ($.fn.DataTable.isDataTable("#trashTable")) {
                            $("#trashTable").DataTable().destroy();
                        }
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
                url: '{{ route('suppliers.restore') }}',
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
                    fetchAllSuppliers();
                    fetchTrashSuppliers();
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
                        url: '{{ route('suppliers.permanentDelete') }}',
                        method: 'POST',
                        data: {
                            id: id
                        },
                        success: function(response) {
                            Swal.fire('Deleted!', response.message, 'success');
                            fetchTrashSuppliers();
                        }
                    });
                }
            });
        });
    </script>
@endpush
