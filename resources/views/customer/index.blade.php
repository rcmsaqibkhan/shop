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
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCustomerModal">
                        <i class="bi-plus-circle"></i>
                        Add Customer
                    </button>
                </div>
                <!-- TABS -->
                <ul class="nav nav-tabs mb-3">
                    <li class="nav-item">
                        <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#activeTab">
                            All Customers
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#trashTab">
                            Trash Customers
                        </button>
                    </li>
                </ul>

                <!-- TAB CONTENT -->
                <div class="tab-content">
                    <!-- ACTIVE TAB -->
                    <div class="tab-pane fade show active" id="activeTab">
                        <div id="show_all_customers"></div>
                    </div>
                    <!-- TRASH TAB -->
                    <div class="tab-pane fade" id="trashTab">
                        <div id="show_trash_customers"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ADD MODAL -->
    <div class="modal fade" id="addCustomerModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="add_customer_form" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Add Customer</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <!-- NAME -->
                        <div class="mb-3">
                            <label>Name</label>
                            <input type="text" name="name" class="form-control">
                            <span class="text-danger error-text name_error"></span>
                        </div>

                        <!-- EMAIL -->
                        <div class="mb-3">
                            <label>Email</label>
                            <input type="email" name="email" class="form-control">
                            <span class="text-danger error-text email_error"></span>
                        </div>

                        <!-- PHONE -->
                        <div class="mb-3">
                            <label>Phone</label>
                            <input type="text" name="phone" class="form-control">
                            <span class="text-danger error-text phone_error"></span>
                        </div>
                        <!-- ADDRESS -->
                        <div class="mb-3">
                            <label>Address</label>
                            <input type="text" name="address" class="form-control">
                            <span class="text-danger error-text address_error"></span>
                        </div>
                        <!-- IMAGE -->
                        <div class="mb-3">
                            <label>Image</label>
                            <input type="file" name="image" class="form-control">
                            <span class="text-danger error-text image_error"></span>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-success">
                            Save Customer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- EDIT MODAL -->
    <div class="modal fade" id="editCustomerModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="edit_customer_form" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="customer_id" id="customer_id">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            Edit Customer
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

                        <!-- IMAGE -->
                        <div class="mb-3">
                            <label>Image</label>
                            <input type="file" name="image" class="form-control">
                        </div>

                        <!-- OLD IMAGE -->
                        <div class="mb-3" id="show_image"></div>

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
                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">
                            Update Customer
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
        // CSRF TOKEN
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // FETCH ACTIVE
        fetchAllCustomers();

        function fetchAllCustomers() {
            $.ajax({
                url: '{{ route('customers.fetchAll') }}',
                method: 'GET',
                success: function(response) {
                    $("#show_all_customers").html(response);
                    if ($("#customerTable tbody tr td[colspan]").length == 0) {
                        if ($.fn.DataTable.isDataTable("#customerTable")) {
                            $("#customerTable").DataTable().destroy();
                        }
                        $("#customerTable").DataTable();
                    }
                }
            });
        }

        // ADD CUSTOMER
        $("#add_customer_form").submit(function(e) {
            e.preventDefault();
            let formData = new FormData(this);
            $.ajax({
                url: '{{ route('customers.store') }}',
                method: 'POST',
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                success: function(response) {
                    $('.error-text').text('');
                    if (response.status == 400) {
                        $.each(response.errors, function(key, value) {
                            $('.' + key + '_error').text(value[0]);
                        });
                    } else if (response.status == 200) {
                        Swal.fire('Added!', response.message, 'success');
                        fetchAllCustomers();
                        $("#add_customer_form")[0].reset();
                        $('#addCustomerModal').modal('hide');
                        $('.modal-backdrop').remove();
                        $('body').removeClass('modal-open');
                        $('body').css('padding-right', '');
                    }
                }
            });
        });



        // EDIT CUSTOMER
        $(document).on('click', '.editIcon', function(e) {
            e.preventDefault();
            let id = $(this).attr('id');
            $.ajax({
                url: '{{ route('customers.edit') }}',
                method: 'GET',
                data: {
                    id: id
                },
                success: function(response) {
                    $("#customer_id").val(response.id);
                    $("#edit_name").val(response.name);
                    $("#edit_email").val(response.email);
                    $("#edit_phone").val(response.phone);
                    $("#edit_address").val(response.address);
                    $("#show_image").html(
                        `
                    <img src="/storage/${response.image}"
                    width="80"
                    class="img-thumbnail rounded-circle">
                    `
                    );
                    $("#editCustomerModal").modal('show');
                }
            });
        });

        // UPDATE CUSTOMER
        $("#edit_customer_form").submit(function(e) {
            e.preventDefault();
            let formData = new FormData(this);
            $.ajax({
                url: '{{ route('customers.update') }}',
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
                        $("#edit_customer_form")[0].reset();
                        let modal =
                            bootstrap.Modal.getInstance(
                                document.getElementById('editCustomerModal')
                            );
                        modal.hide();
                        fetchAllCustomers();
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
                text: "Customer will move to trash!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes Delete'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ route('customers.delete') }}',
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
                            fetchAllCustomers();
                            fetchTrashCustomers();
                        }
                    });
                }
            });
        });

        // FETCH TRASH
        fetchTrashCustomers();

        function fetchTrashCustomers() {
            $.ajax({
                url: '{{ route('customers.fetchTrash') }}',
                method: 'GET',
                success: function(response) {
                    $("#show_trash_customers").html(response);
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
                url: '{{ route('customers.restore') }}',
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
                    fetchAllCustomers();
                    fetchTrashCustomers();
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
                        url: '{{ route('customers.permanentDelete') }}',
                        method: 'POST',
                        data: {
                            id: id
                        },
                        success: function(response) {
                            Swal.fire('Deleted!', response.message, 'success');
                            fetchTrashCustomers();
                        }
                    });
                }
            });
        });
    </script>
@endpush
