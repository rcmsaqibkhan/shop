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
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                        <i class="bi-plus-circle"></i>
                        Add Category
                    </button>
                </div>

                <!-- TABS -->
                <ul class="nav nav-tabs mb-3">
                    <li class="nav-item">
                        <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#activeTab">
                            All Categories
                        </button>
                    </li>

                    <li class="nav-item">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#trashTab">
                            Trash Categories
                        </button>
                    </li>
                </ul>

                <!-- TAB CONTENT -->
                <div class="tab-content">
                    <!-- ACTIVE TAB -->
                    <div class="tab-pane fade show active" id="activeTab">
                        <div id="show_all_categories"></div>
                    </div>
                    <!-- TRASH TAB -->
                    <div class="tab-pane fade" id="trashTab">
                        <div id="show_trash_categories"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ADD MODAL -->
    <div class="modal fade" id="addCategoryModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="add_category_form" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Add Category</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <!-- NAME -->
                        <div class="mb-3">
                            <label>Name</label>
                            <input type="text" name="name" class="form-control">
                        </div>

                        <!-- IMAGE -->
                        <div class="mb-3">
                            <label>Image</label>
                            <input type="file" name="image" class="form-control">
                        </div>

                        <!-- STATUS -->
                        <div class="mb-3">
                            <label>Status</label>
                            <select name="status" class="form-control">
                                <option value="active">
                                    Active
                                </option>
                                <option value="inactive">
                                    Inactive
                                </option>
                            </select>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-success">
                            Save Category
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- EDIT MODAL -->
    <div class="modal fade" id="editCategoryModal">

        <div class="modal-dialog">

            <div class="modal-content">

                <form id="edit_category_form" enctype="multipart/form-data">

                    @csrf

                    <input type="hidden" name="cat_id" id="cat_id">

                    <div class="modal-header">

                        <h5 class="modal-title">
                            Edit Category
                        </h5>

                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>

                    </div>

                    <div class="modal-body">
                        <!-- NAME -->
                        <div class="mb-3">
                            <label>Name</label>
                            <input type="text" name="name" id="edit_name" class="form-control">
                        </div>

                        <!-- IMAGE -->
                        <div class="mb-3">
                            <label>Image</label>
                            <input type="file" name="image" class="form-control">
                        </div>

                        <!-- OLD IMAGE -->
                        <div class="mb-3" id="show_image"></div>
                        <!-- STATUS -->
                        <div class="mb-3">
                            <label>Status</label>
                            <select name="status" id="edit_status" class="form-control">
                                <option value="active">
                                    Active
                                </option>
                                <option value="inactive">
                                    Inactive
                                </option>
                            </select>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">
                            Update Category
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

        // ADD CATEGORY
        $("#add_category_form").submit(function(e) {
            e.preventDefault();
            let formData = new FormData(this);
            $.ajax({
                url: '{{ route('categories.store') }}',
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
                        // REFRESH TABLE
                        fetchAllCategories();
                        // RESET FORM
                        $("#add_category_form")[0].reset();
                        // CLOSE MODAL
                        $('#addCategoryModal').modal('hide');
                        // REMOVE BLUR
                        $('.modal-backdrop').remove();
                        $('body').removeClass('modal-open');
                        $('body').css('padding-right', '');
                    }
                }
            });
        });

        // FETCH ACTIVE
        fetchAllCategories();

        function fetchAllCategories() {
            $.ajax({
                url: '{{ route('categories.fetchAll') }}',
                method: 'GET',
                success: function(response) {
                    $("#show_all_categories").html(response);
                    if ($("#categoryTable tbody tr td[colspan]").length == 0) {
                        $("#categoryTable").DataTable();
                    }
                }
            });
        }

        // EDIT CATEGORY
        $(document).on('click', '.editIcon', function(e) {

            e.preventDefault();

            let id = $(this).attr('id');

            $.ajax({

                url: '{{ route('categories.edit') }}',
                method: 'GET',
                data: {
                    id: id
                },

                success: function(response) {
                    $("#cat_id").val(response.id);
                    $("#edit_name").val(response.name);
                    $("#edit_status").val(response.status);
                    $("#show_image").html(
                        `
                    <img src="/storage/${response.image}"
                    width="80"
                    class="img-thumbnail rounded-circle">
                    `
                    );

                    $("#editCategoryModal").modal('show');
                }
            });
        });

        // UPDATE CATEGORY
        $("#edit_category_form").submit(function(e) {
            e.preventDefault();
            let formData = new FormData(this);
            $.ajax({
                url: '{{ route('categories.update') }}',
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
                        $("#edit_category_form")[0].reset();
                        let modal =
                            bootstrap.Modal.getInstance(
                                document.getElementById('editCategoryModal')
                            );
                        modal.hide();
                        fetchAllCategories();
                    }
                }
            });
        });

        // FETCH TRASH
        fetchTrashCategories();

        function fetchTrashCategories() {
            $.ajax({
                url: '{{ route('categories.fetchTrash') }}',
                method: 'GET',
                success: function(response) {
                    $("#show_trash_categories").html(response);
                    if ($("#trashTable tbody tr td[colspan]").length == 0) {
                        if ($.fn.DataTable.isDataTable("#trashTable")) {
                            $("#trashTable").DataTable().destroy();
                        }
                        $("#trashTable").DataTable();
                    }
                }
            });
        }

        // SOFT DELETE
        $(document).on('click', '.deleteIcon', function(e) {
            e.preventDefault();
            let id = $(this).attr('id');
            Swal.fire({
                title: 'Are you sure?',
                text: "Category will move to trash!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes Delete'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ route('categories.delete') }}',
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
                            fetchAllCategories();
                            fetchTrashCategories();
                        }
                    });
                }
            });
        });

        // RESTORE
        $(document).on('click', '.restoreIcon', function(e) {

            e.preventDefault();

            let id = $(this).attr('id');

            $.ajax({

                url: '{{ route('categories.restore') }}',
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

                    fetchAllCategories();
                    fetchTrashCategories();
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

                        url: '{{ route('categories.permanentDelete') }}',
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

                            fetchTrashCategories();
                        }
                    });
                }
            });
        });
    </script>
@endpush
