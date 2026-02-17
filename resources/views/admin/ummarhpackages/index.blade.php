@extends('admin.layout.app')
@section('title', 'Umrah Packages')

<style>
    .description-cell {
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 300px;
        cursor: pointer;
    }
    .description-cell:hover {
        color: #007bff;
    }

    /* City & Hotel styles */
    .city-hotel-list {
        max-height: 100px;
        overflow-y: auto;
        padding: 5px;
        background: #f8f9fa;
        border-radius: 4px;
    }
    .city-hotel-item {
        padding: 3px 0;
        border-bottom: 1px dashed #dee2e6;
    }
    .city-hotel-item:last-child {
        border-bottom: none;
    }
    .city-name {
        font-weight: 600;
        color: #495057;
    }
    .hotel-name {
        color: #007bff;
        font-size: 0.9em;
    }
    .duration-badge {
        background: #28a745;
        color: white;
        padding: 2px 8px;
        border-radius: 12px;
        font-size: 0.85em;
        display: inline-block;
    }
    .nights-info {
        background: #e9ecef;
        padding: 5px 10px;
        border-radius: 4px;
        text-align: center;
    }
    .package-stars {
        color: #ffc107;
        font-size: 1.1em;
    }
    .status-badge {
        padding: 5px 10px;
        border-radius: 20px;
        font-size: 0.85em;
        font-weight: 600;
        margin-left: 5px;
    }
    .status-active {
        background: #d4edda;
        color: #155724;
    }
    .status-inactive {
        background: #f8d7da;
        color: #721c24;
    }
    /* Fix toggle size and alignment */
    .form-check.form-switch {
        min-width: 50px;
        margin-bottom: 0;
    }
    .form-check-input {
        cursor: pointer;
    }
</style>

@section('content')
<div class="main-content" style="min-height: 562px;">
    <section class="section">
        <div class="section-body">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4>Umrah Packages Management</h4>
                            @if (Auth::guard('admin')->check() ||
                                ($sideMenuPermissions->has('Umrah Packages') && $sideMenuPermissions['Umrah Packages']->contains('create')))
                                <a class="btn btn-primary" href="{{ route('umrahpackages.create') }}">
                                    <i class="fa fa-plus"></i> Create New Package
                                </a>
                            @endif
                        </div>

                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered" id="table_id_packages">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th width="5%">Sr.</th>
                                            <th width="12%">Package Name</th>
                                            <th width="5%">Month</th>
                                            <th width="5%">Stars</th>
                                            <th width="15%">Description</th>
                                            <th width="10%">Flight Info</th>
                                            <th width="10%">Visa Service</th>
                                            <th width="15%">City wise Hotels & Nights</th>
                                            <th width="8%">Status</th>
                                            <th width="10%">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($packages as $package)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>

                                            <td>
                                                <strong>{{ $package->package_name }}</strong>
                                                @if($package->image)
                                                    <br>
                                                    <small>
                                                        <img src="{{ asset('public/' . $package->image) }}" width="50" height="50" style="object-fit:cover; border-radius:4px; margin-top:5px;">
                                                    </small>
                                                @endif
                                            </td>

                                            <td>
                                                <span class="badge badge-info">
                                                    {{ DateTime::createFromFormat('!m', $package->month)->format('F') }}
                                                </span>
                                            </td>

                                            <td>
                                                <div class="package-stars">
                                                    @for($i = 1; $i <= 5; $i++)
                                                        @if($i <= $package->stars)
                                                            <i class="fa fa-star text-warning"></i>
                                                        @else
                                                            <i class="fa fa-star-o text-secondary"></i>
                                                        @endif
                                                    @endfor
                                                    <br>
                                                    <small class="text-muted">{{ $package->stars }} Star</small>
                                                </div>
                                            </td>

                                            <td>
                                                <div class="description-cell" title="{{ $package->description }}">
                                                    {{ $package->description }}
                                                </div>
                                                <br>
                                                <small class="text-muted">
                                                    <i class="fa fa-money"></i> £ {{ number_format($package->price_per_person) }}/person
                                                </small>
                                            </td>

                                            <td>
                                                <span class="badge badge-secondary">
                                                    <i class="fa fa-plane"></i> {{ $package->flight_info ?? '-' }}
                                                </span>
                                            </td>

                                            <td>
                                                <span class="badge badge-success">
                                                    <i class="fa fa-passport"></i> {{ $package->visa_service }}
                                                </span>
                                            </td>

                                            <td>
                                                <div class="city-hotel-list">
                                                    @foreach ($package->packageDetails as $index => $detail)
                                                        @php
                                                            $nights = intval(str_replace(' Nights', '', $detail->time_duration));
                                                        @endphp
                                                        <div class="city-hotel-item">
                                                            <div class="d-flex justify-content-between align-items-start">
                                                                <div>
                                                                    <span class="city-name">
                                                                        <i class="fa fa-building"></i> 
                                                                        {{ $detail->city->name ?? 'N/A' }}
                                                                    </span>
                                                                    <br>
                                                                    <span class="hotel-name">
                                                                        <i class="fa fa-hotel"></i> 
                                                                        {{ $detail->hotel->name ?? 'N/A' }}
                                                                    </span>
                                                                </div>
                                                                <div class="ml-2">
                                                                    <span class="duration-badge">
                                                                        {{ $nights }} {{ $nights == 1 ? 'Night' : 'Nights' }}
                                                                    </span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        @if(!$loop->last)
                                                            <hr style="margin:5px 0;">
                                                        @endif
                                                    @endforeach
                                                </div>
                                                <div class="nights-info mt-2">
                                                    <strong>Total Nights:</strong> 
                                                    @php
                                                        $totalNights = 0;
                                                        foreach($package->packageDetails as $detail) {
                                                            $nights = intval(str_replace(' Nights', '', $detail->time_duration));
                                                            $totalNights += $nights;
                                                        }
                                                    @endphp
                                                    <span class="badge badge-primary">{{ $totalNights }} Nights</span>
                                                </div>
                                            </td>

	                                        <td class="text-center">
											@php $status = (int) $package->status; @endphp
											<div class="d-flex justify-content-center align-items-center">
												<div class="form-check form-switch mb-0">
													<input class="form-check-input status-toggle" type="checkbox"
														id="status_{{ $package->id }}"
														data-id="{{ $package->id }}"
														{{ $status === 1 ? 'checked' : '' }}>
												</div>
												<span class="status-badge {{ $status === 1 ? 'status-active' : 'status-inactive' }} ms-2"
													id="status-text-{{ $package->id }}">
													{{ $status === 1 ? 'Active' : 'Inactive' }}
												</span>
											</div>
											</td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('umrahpackages.edit', $package->id) }}" 
                                                       class="btn btn-sm btn-primary" 
                                                       title="Edit Package">
                                                        <i class="fa fa-edit"></i>
                                                    </a>

                                                    <form id="delete-form-{{ $package->id }}" 
                                                          action="{{ route('umrahpackages.destroy', $package->id) }}" 
                                                          method="POST" 
                                                          style="display: none;">
                                                        @csrf
                                                        @method('DELETE')
                                                    </form>

                                                    <button class="btn btn-sm btn-danger show_confirm me-5" style="margin-left: 7px;"
                                                            data-form="delete-form-{{ $package->id }}" 
                                                            type="button"
                                                            title="Delete Package">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

@section('js')
<script>
$(document).ready(function(){

    // Initialize DataTable
    $('#table_id_packages').DataTable({
        pageLength: 10,
        ordering: true,
        responsive: true,
        language: {
            search: "Search Packages:",
            lengthMenu: "Show _MENU_ packages per page",
            info: "Showing _START_ to _END_ of _TOTAL_ packages"
        }
    });

    // Status toggle AJAX
    $('#table_id_packages').on('change', '.status-toggle', function(){
        var checkbox = $(this);
        var packageId = checkbox.data('id');
        var statusSpan = $('#status-text-' + packageId);
        var newStatus = checkbox.is(':checked') ? 1 : 0;

        $.ajax({
            url: "{{ route('umrahpackages.toggleStatus') }}",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                id: packageId,
                status: newStatus
            },
            success: function(response){
                if(response.success){
                    // Update toggle and badge text
                    checkbox.prop('checked', response.status ? true : false);
                    statusSpan.text(response.status ? 'Active' : 'Inactive')
                              .removeClass('status-active status-inactive')
                              .addClass(response.status ? 'status-active' : 'status-inactive');

                    Swal.fire({
                        icon: 'success',
                        title: 'Updated!',
                        text: 'Package status changed',
                        timer: 1200,
                        showConfirmButton: false
                    });

					// Reload page after 5 seconds
        setTimeout(function(){
            location.reload();
        }, 2000);

                } else {
                    checkbox.prop('checked', !newStatus);
                    Swal.fire({ icon: 'error', title: 'Error!', text: 'Failed to update status' });
                }
            },
            error: function(){
                checkbox.prop('checked', !newStatus);
                Swal.fire({ icon: 'error', title: 'Error!', text: 'Server error occurred' });
            }
        });
    });

    // Delete confirmation
    $('#table_id_packages').on('click', '.show_confirm', function(event){
        event.preventDefault();
        var formId = $(this).data('form');

        Swal.fire({
            title: 'Are you sure you want to delete this record?',
            text: "If you delete this Package record, it will be gone forever.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Deleting...',
                    text: 'Please wait',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    willOpen: () => { Swal.showLoading(); }
                });
                $('#' + formId).submit();
            }
        });
    });

    // Initialize tooltips
    $('#table_id_packages').on('draw.dt', function(){ $('[title]').tooltip(); });
    $('[title]').tooltip();
});
</script>
@endsection