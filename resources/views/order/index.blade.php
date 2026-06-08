@extends('layouts.auth')

@section('styleCss')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endsection

@section('content')
    <div class="main-content">
        <div class="page-content">
            <div class="container-fluid">

                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <div class="card">
                    <div class="card-header">
                        <ul class="nav nav-tabs card-header-tabs" id="orderTabs">
                            <li class="nav-item">
                                <a class="nav-link active" href="#listTab" data-bs-toggle="tab">
                                    <i class="fas fa-list me-1"></i> Orders List
                                    <span class="badge bg-primary ms-1">{{ $orders->count() }}</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#createTab" data-bs-toggle="tab" id="createTabLink">
                                    <i class="fas fa-plus me-1"></i> Create Order
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#trashTab" data-bs-toggle="tab">
                                    <i class="fas fa-trash me-1"></i> Deleted Orders
                                    <span class="badge bg-danger ms-1">{{ $trashedOrders->count() }}</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#invoiceTab" data-bs-toggle="tab" id="invoiceTabLink">
                                    <i class="fas fa-file-invoice me-1"></i> Invoice
                                </a>
                            </li>
                        </ul>
                    </div>

                    <div class="card-body">
                        <div class="tab-content">

                            {{-- LIST TAB --}}
                            <div class="tab-pane fade show active" id="listTab">
                                <div class="table-responsive">
                                    <table id="ordersTable" class="table table-bordered table-striped align-middle">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Customer</th>
                                                <th>Order No</th>
                                                <th>Total Amount</th>
                                                <th>Date</th>
                                                <th width="100">Invoice</th>
                                                <th width="150">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($orders as $order)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ $order->customer->name ?? 'N/A' }}</td>
                                                    <td>{{ $order->order_number }}</td>
                                                    <td>Rs. {{ number_format($order->total_amount, 2) }}</td>
                                                    <td>{{ \Carbon\Carbon::parse($order->date)->format('d-m-y') }}</td>
                                                    <td>
                                                        <button type="button" class="btn btn-info btn-sm viewInvoice"
                                                            data-id="{{ $order->id }}">
                                                            <i class="fas fa-file-invoice"></i> View
                                                        </button>
                                                    </td>
                                                    <td>
                                                        <button type="button" class="btn btn-warning btn-sm editOrder"
                                                            data-id="{{ $order->id }}">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-danger btn-sm softDelete"
                                                            data-id="{{ $order->id }}">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            {{-- CREATE / EDIT TAB --}}
                            <div class="tab-pane fade" id="createTab">
                                <form id="orderForm" action="{{ route('orders.store') }}" method="POST">
                                    @csrf

                                    <div class="row mb-4">
                                        <div class="col-md-4">
                                            <label>Order Number</label>
                                            <input type="text" class="form-control" name="order_number" id="orderNumber"
                                                value="{{ $orderNumber }}" readonly>
                                        </div>
                                        <div class="col-md-4">
                                            <label>Customer</label>
                                            <select name="customer_id" id="customerId" class="form-control" required>
                                                <option value="">Select Customer</option>
                                                @foreach ($customers as $customer)
                                                    <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label>Date</label>
                                            <input type="date" name="date" id="orderDate" class="form-control"
                                                value="{{ date('Y-m-d') }}" required>
                                        </div>
                                    </div>

                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead class="table-primary">
                                                <tr>
                                                    <th width="40%">Product</th>
                                                    <th>Price</th>
                                                    <th>Qty</th>
                                                    <th>Total</th>
                                                    <th width="120">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody id="orderTable">
                                                <tr>
                                                    <td>
                                                        <select name="product_ids[]" class="form-control product-select"
                                                            required>
                                                            <option value="">Select Product</option>
                                                            @foreach ($products as $product)
                                                                <option value="{{ $product->id }}"
                                                                    data-price="{{ $product->sell_price }}">
                                                                    {{ $product->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                    <td><input type="number" name="price[]" class="form-control price"
                                                            readonly></td>
                                                    <td><input type="number" name="quantity[]" class="form-control qty"
                                                            value="1" min="1"></td>
                                                    <td><input type="number" class="form-control total" readonly></td>
                                                    <td><button type="button" class="btn btn-success addRow">Add</button>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>

                                    <div class="row mt-3">
                                        <div class="col-md-2 offset-md-10">
                                            <label>Discount</label>
                                            <input type="number" name="discount" id="discount" class="form-control"
                                                value="0" min="0">
                                            <br>
                                            <label>Grand Total: </label>
                                            <span id="grandTotal">0.00</span>
                                            <input type="hidden" name="total_amount" id="total_amount">
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-between mt-3">
                                        <button type="button" class="btn btn-secondary" id="resetForm">
                                            <i class="fas fa-times me-1"></i> Cancel
                                        </button>
                                        <button type="submit" class="btn btn-primary" id="submitBtn">
                                            <i class="fas fa-save me-1"></i> Save Order
                                        </button>
                                    </div>

                                </form>
                            </div>

                            {{-- TRASH TAB --}}
                            <div class="tab-pane fade" id="trashTab">
                                <div class="table-responsive">
                                    <table id="trashedTable" class="table table-bordered table-striped align-middle">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Customer</th>
                                                <th>Order No</th>
                                                <th>Total Amount</th>
                                                <th>Date</th>
                                                <th width="200">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($trashedOrders as $order)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ $order->customer->name ?? 'N/A' }}</td>
                                                    <td>{{ $order->order_number }}</td>
                                                    <td>Rs. {{ number_format($order->total_amount, 2) }}</td>
                                                    <td>{{ \Carbon\Carbon::parse($order->date)->format('d M Y') }}</td>
                                                    <td>
                                                        <button type="button" class="btn btn-success btn-sm restoreOrder"
                                                            data-id="{{ $order->id }}">
                                                            <i class="fas fa-undo"></i> Restore
                                                        </button>
                                                        <button type="button"
                                                            class="btn btn-danger btn-sm permanentDelete"
                                                            data-id="{{ $order->id }}">
                                                            <i class="fas fa-times"></i> Delete
                                                        </button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            {{-- INVOICE TAB --}}
                            <div class="tab-pane fade" id="invoiceTab">
                                <div id="invoiceContent">
                                    <div class="text-center py-5 text-muted">
                                        <i class="fas fa-file-invoice fa-3x mb-3"></i>
                                        <p>Please click the Invoice button on any order to view it.</p>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection

@push('scripts')
    {{-- jQuery PEHLE load hona chahiye --}}
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        function initSelect2OnRow(row) {
            $(row).find('.product-select').select2({
                placeholder: 'Select Product',
                allowClear: true,
                width: '100%'
            });
        }

        function calculateGrandTotal() {
            let grandTotal = 0;
            $('.total').each(function() {
                grandTotal += parseFloat($(this).val()) || 0;
            });
            let discount = parseFloat($('#discount').val()) || 0;
            grandTotal -= discount;
            $('#grandTotal').html(grandTotal.toFixed(2));
            $('#total_amount').val(grandTotal.toFixed(2));
        }

        function buildProductOptionsFromArray(products, selectedId) {
            let options = '<option value="">Select Product</option>';
            products.forEach(function(p) {
                let selected = p.id == selectedId ? 'selected' : '';
                options += `<option value="${p.id}" data-price="${p.sell_price}" ${selected}>${p.name}</option>`;
            });
            return options;
        }

        $(document).ready(function() {

            $('#customerId').select2({
                placeholder: 'Select Customer',
                allowClear: true,
                width: '100%'
            });

            initSelect2OnRow($('#orderTable tr:first'));

            $('#ordersTable').DataTable({
                responsive: true,
                pageLength: 5,
                lengthMenu: [
                    [5, 10, 25, 50, -1],
                    [5, 10, 25, 50, 'All']
                ],
                columnDefs: [{
                    orderable: false,
                    targets: [5, 6]
                }]
            });

            $('#trashedTable').DataTable({
                responsive: true,
                pageLength: 10,
                lengthMenu: [
                    [10, 25, 50, -1],
                    [10, 25, 50, 'All']
                ],
                order: [
                    [0, 'desc']
                ],
                columnDefs: [{
                    orderable: false,
                    targets: 5
                }]
            });

            $(document).on('select2:select change', '.product-select', function() {
                let selected = $(this).find('option:selected');
                let price = selected.data('price');
                let row = $(this).closest('tr');

                if (price !== undefined && price !== '') {
                    row.find('.price').val(parseFloat(price).toFixed(2));
                    let qty = parseFloat(row.find('.qty').val()) || 1;
                    row.find('.total').val((qty * parseFloat(price)).toFixed(2));
                } else {
                    row.find('.price').val('');
                    row.find('.total').val('');
                }
                calculateGrandTotal();
            });

            $(document).on('keyup change', '.qty', function() {
                let row = $(this).closest('tr');
                let qty = parseFloat(row.find('.qty').val()) || 0;
                let price = parseFloat(row.find('.price').val()) || 0;
                row.find('.total').val((qty * price).toFixed(2));
                calculateGrandTotal();
            });

            $('#discount').on('keyup change', function() {
                calculateGrandTotal();
            });

            $(document).on('click', '.addRow', function() {
                let newRow = `
                    <tr>
                        <td>
                            <select name="product_ids[]" class="form-control product-select" required>
                                <option value="">Select Product</option>
                                @foreach ($products as $product)
                                    <option value="{{ $product->id }}" data-price="{{ $product->sell_price }}">{{ $product->name }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td><input type="number" name="price[]" class="form-control price" readonly></td>
                        <td><input type="number" name="quantity[]" class="form-control qty" value="1" min="1"></td>
                        <td><input type="number" class="form-control total" readonly></td>
                        <td><button type="button" class="btn btn-danger removeRow">Remove</button></td>
                    </tr>
                `;
                $('#orderTable').append(newRow);
                initSelect2OnRow($('#orderTable tr:last'));
            });

            $(document).on('click', '.removeRow', function() {
                $(this).closest('tr').remove();
                calculateGrandTotal();
            });

            $(document).on('click', '.editOrder', function() {
                let id = $(this).data('id');

                $.ajax({
                    url: `/orders/edit-data/${id}`,
                    type: 'GET',
                    success: function(res) {
                        $('#orderForm').attr('action', `/orders/update/${id}`);
                        $('#submitBtn').html('<i class="fas fa-save me-1"></i> Update Order');
                        $('#createTabLink').html('<i class="fas fa-edit me-1"></i> Edit Order');

                        $('#orderNumber').val(res.order.order_number);
                        $('#customerId').val(res.order.customer_id).trigger('change');
                        $('#orderDate').val(res.order.date);
                        $('#discount').val(res.order.discount);

                        $('#orderTable').empty();

                        res.items.forEach(function(item, index) {
                            let options = buildProductOptionsFromArray(res.products,
                                item.product_id);
                            let totalVal = (parseFloat(item.price) * parseInt(item
                                .quantity)).toFixed(2);
                            let btn = index == 0 ?
                                `<button type="button" class="btn btn-success addRow">Add</button>` :
                                `<button type="button" class="btn btn-danger removeRow">Remove</button>`;

                            $('#orderTable').append(`
                                <tr>
                                    <td>
                                        <select name="product_ids[]" class="form-control product-select" required>
                                            ${options}
                                        </select>
                                    </td>
                                    <td><input type="number" name="price[]" class="form-control price" value="${parseFloat(item.price).toFixed(2)}" readonly></td>
                                    <td><input type="number" name="quantity[]" class="form-control qty" value="${item.quantity}" min="1"></td>
                                    <td><input type="number" class="form-control total" value="${totalVal}" readonly></td>
                                    <td>${btn}</td>
                                </tr>
                            `);
                        });

                        $('#orderTable tr').each(function() {
                            initSelect2OnRow(this);
                        });

                        calculateGrandTotal();

                        let tab = new bootstrap.Tab(document.querySelector(
                            '#orderTabs a[href="#createTab"]'));
                        tab.show();
                    },
                    error: function(xhr) {
                        Swal.fire('Error!', 'Failed to load order data. Status: ' + xhr.status,
                            'error');
                    }
                });
            });

            $('#resetForm').on('click', function() {
                $('#orderForm').attr('action', "{{ route('orders.store') }}");
                $('#submitBtn').html('<i class="fas fa-save me-1"></i> Save Order');
                $('#createTabLink').html('<i class="fas fa-plus me-1"></i> Create Order');
                $('#orderNumber').val('{{ $orderNumber }}');
                $('#customerId').val('').trigger('change');
                $('#orderDate').val('{{ date('D-M-y') }}');
                $('#discount').val(0);

                $('#orderTable').html(`
                    <tr>
                        <td>
                            <select name="product_ids[]" class="form-control product-select" required>
                                <option value="">Select Product</option>
                                @foreach ($products as $product)
                                    <option value="{{ $product->id }}" data-price="{{ $product->sell_price }}">{{ $product->name }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td><input type="number" name="price[]" class="form-control price" readonly></td>
                        <td><input type="number" name="quantity[]" class="form-control qty" value="1" min="1"></td>
                        <td><input type="number" class="form-control total" readonly></td>
                        <td><button type="button" class="btn btn-success addRow">Add</button></td>
                    </tr>
                `);

                initSelect2OnRow($('#orderTable tr:first'));
                calculateGrandTotal();

                let tab = new bootstrap.Tab(document.querySelector('#orderTabs a[href="#listTab"]'));
                tab.show();
            });

            // Soft Delete
            $(document).on('click', '.softDelete', function() {
                let id = $(this).data('id');
                Swal.fire({
                    title: 'Are you sure?',
                    text: 'This order will be moved to trash!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, Delete it!',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/orders/delete/${id}`,
                            type: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(res) {
                                Swal.fire('Deleted!', res.message, 'success').then(() =>
                                    location.reload());
                            },
                            error: function() {
                                Swal.fire('Error!',
                                    'Something went wrong. Please try again.',
                                    'error');
                            }
                        });
                    }
                });
            });

            // Restore Order
            $(document).on('click', '.restoreOrder', function() {
                let id = $(this).data('id');
                Swal.fire({
                    title: 'Restore Order?',
                    text: 'This order will be restored!',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#28a745',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, Restore it!',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/orders/restore/${id}`,
                            type: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(res) {
                                Swal.fire('Restored!', res.message, 'success').then(
                                    () => location.reload());
                            },
                            error: function() {
                                Swal.fire('Error!',
                                    'Something went wrong. Please try again.',
                                    'error');
                            }
                        });
                    }
                });
            });

            // Permanent Delete
            $(document).on('click', '.permanentDelete', function() {
                let id = $(this).data('id');
                Swal.fire({
                    title: 'Permanently Delete?',
                    text: 'This record will be deleted forever and cannot be recovered!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, Delete Permanently!',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/orders/force-delete/${id}`,
                            type: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(res) {
                                Swal.fire('Deleted!', res.message, 'success').then(() =>
                                    location.reload());
                            },
                            error: function() {
                                Swal.fire('Error!',
                                    'Something went wrong. Please try again.',
                                    'error');
                            }
                        });
                    }
                });
            });

            // Invoice View
            $(document).on('click', '.viewInvoice', function() {
                let id = $(this).data('id');

                $.ajax({
                    url: `/orders/invoice/${id}`,
                    type: 'GET',
                    success: function(res) {
                        let order = res.order;
                        let customer = order.customer;
                        let items = order.order_items;

                        let itemRows = '';
                        let subtotal = 0;

                        items.forEach(function(item, index) {
                            let total = parseFloat(item.price) * parseInt(item
                                .quantity);
                            subtotal += total;
                            itemRows += `
                                <tr>
                                    <td>${index + 1}</td>
                                    <td>${item.product ? item.product.name : 'N/A'}</td>
                                    <td class="text-center">${item.quantity}</td>
                                    <td class="text-end">Rs. ${parseFloat(item.price).toFixed(2)}</td>
                                    <td class="text-end">Rs. ${total.toFixed(2)}</td>
                                </tr>
                            `;
                        });

                        let discount = parseFloat(order.discount) || 0;
                        let grandTotal = subtotal - discount;

                        let invoiceHtml = `
                            <div id="printArea" style="max-width:800px; margin:0 auto; font-family: 'Segoe UI', sans-serif;">

                                <div style="background: linear-gradient(135deg, #1a237e, #283593); color:white; padding:30px; border-radius:8px 8px 0 0;">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h2 style="margin:0; font-size:28px; font-weight:700; letter-spacing:1px;">SMART SHOP</h2>
                                            <p style="margin:5px 0 0; opacity:0.85; font-size:13px;">
                                                <i class="fas fa-map-marker-alt me-1"></i> 123 Business Street, City, Country<br>
                                                <i class="fas fa-phone me-1"></i> +92 300 0000000 &nbsp;
                                                <i class="fas fa-envelope me-1"></i> info@company.com
                                            </p>
                                        </div>
                                        <div class="text-end">
                                            <h1 style="margin:0; font-size:42px; font-weight:800; letter-spacing:3px; opacity:0.9;">INVOICE</h1>
                                            <p style="margin:5px 0 0; font-size:13px; opacity:0.85;"># ${order.order_number}</p>
                                        </div>
                                    </div>
                                </div>

                                <div style="background:#f8f9fa; padding:20px 30px; border-left:1px solid #dee2e6; border-right:1px solid #dee2e6;">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p style="color:#6c757d; font-size:11px; text-transform:uppercase; letter-spacing:1px; margin-bottom:5px;">Bill To</p>
                                            <h5 style="margin:0; color:#1a237e; font-weight:700;">${customer ? customer.name : 'N/A'}</h5>
                                            <p style="margin:3px 0; color:#495057; font-size:13px;">
                                                ${customer && customer.email ? '<i class="fas fa-envelope me-1"></i>' + customer.email + '<br>' : ''}
                                                ${customer && customer.phone ? '<i class="fas fa-phone me-1"></i>' + customer.phone + '<br>' : ''}
                                                ${customer && customer.address ? '<i class="fas fa-map-marker-alt me-1"></i>' + customer.address : ''}
                                            </p>
                                        </div>
                                        <div class="col-md-6 text-md-end">
                                            <p style="color:#6c757d; font-size:11px; text-transform:uppercase; letter-spacing:1px; margin-bottom:5px;">Invoice Details</p>
                                            <table style="margin-left:auto; font-size:13px;">
                                                <tr>
                                                    <td style="color:#6c757d; padding-right:15px;">Invoice No:</td>
                                                    <td style="font-weight:600; color:#1a237e;">${order.order_number}</td>
                                                </tr>
                                                <tr>
                                                    <td style="color:#6c757d; padding-right:15px;">Date:</td>
                                                    <td style="font-weight:600;">${order.date}</td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                <div style="border:1px solid #dee2e6; border-top:none;">
                                    <table class="table table-hover mb-0">
                                        <thead>
                                            <tr style="background:#1a237e; color:white;">
                                                <th style="padding:12px 20px; width:50px;">#</th>
                                                <th style="padding:12px 20px;">Product</th>
                                                <th style="padding:12px 20px; text-align:center;">Qty</th>
                                                <th style="padding:12px 20px; text-align:right;">Unit Price</th>
                                                <th style="padding:12px 20px; text-align:right;">Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>${itemRows}</tbody>
                                    </table>
                                </div>

                                <div style="border:1px solid #dee2e6; border-top:none; padding:20px 30px; background:#fff;">
                                    <div class="row justify-content-end">
                                        <div class="col-md-4">
                                            <table style="width:100%; font-size:14px;">
                                                <tr>
                                                    <td style="padding:5px 0; color:#6c757d;">Subtotal:</td>
                                                    <td style="text-align:right; font-weight:500;">Rs. ${subtotal.toFixed(2)}</td>
                                                </tr>
                                                <tr>
                                                    <td style="padding:5px 0; color:#6c757d;">Discount:</td>
                                                    <td style="text-align:right; color:#dc3545; font-weight:500;">- Rs. ${discount.toFixed(2)}</td>
                                                </tr>
                                                <tr>
                                                    <td colspan="2"><hr style="margin:8px 0; border-color:#dee2e6;"></td>
                                                </tr>
                                                <tr>
                                                    <td style="padding:5px 0; font-weight:700; font-size:16px; color:#1a237e;">Grand Total:</td>
                                                    <td style="text-align:right; font-weight:700; font-size:16px; color:#1a237e;">Rs. ${grandTotal.toFixed(2)}</td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                <div style="background:#f8f9fa; padding:20px 30px; border:1px solid #dee2e6; border-top:none; border-radius:0 0 8px 8px;">
                                    <div class="row align-items-center">
                                        <div class="col-md-8">
                                            <p style="margin:0; font-size:12px; color:#6c757d;">
                                                <i class="fas fa-info-circle me-1"></i>
                                                Thank you for your business! Payment is due within 30 days.
                                            </p>
                                        </div>
                                        <div class="col-md-4 text-end">
                                            <button onclick="printInvoice()" class="btn btn-primary btn-sm me-2">
                                                <i class="fas fa-print me-1"></i> Print
                                            </button>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        `;

                        $('#invoiceContent').html(invoiceHtml);

                        let tab = new bootstrap.Tab(document.querySelector(
                            '#orderTabs a[href="#invoiceTab"]'));
                        tab.show();
                        $('#invoiceTabLink').html(
                            '<i class="fas fa-file-invoice me-1"></i> Invoice');
                    },
                    error: function() {
                        Swal.fire('Error!', 'Failed to load invoice. Please try again.',
                            'error');
                    }
                });
            });

            calculateGrandTotal();

        }); // END document.ready

        function printInvoice() {
            let printContents = document.getElementById('printArea').innerHTML;
            let originalContents = document.body.innerHTML;
            document.body.innerHTML = printContents;
            window.print();
            document.body.innerHTML = originalContents;
            location.reload();
        }
    </script>
@endpush
