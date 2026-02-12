@extends('admin.layout.app')
@section('title', 'Umrah Packages')

<style>
	.description-cell {
    display: -webkit-box;
    -webkit-line-clamp: 3; /* sirf 3 lines show */
    -webkit-box-orient: vertical;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 300px; /* optional: column width */
    cursor: pointer;
}
.description-cell:hover {
    color: #007bff; /* optional: hover color */
}
</style>

@section('content')
<div class="main-content" style="min-height: 562px;">
    <section class="section">
        <div class="section-body">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>Umrah Packages</h4>
                        </div>
                        <div class="card-body table-striped table-bordered table-responsive">
                            @if (Auth::guard('admin')->check() ||
                                ($sideMenuPermissions->has('Umrah Packages') && $sideMenuPermissions['Umrah Packages']->contains('create')))
                                <a class="btn btn-primary mb-3" href="{{ route('umrahpackages.create') }}">Create</a>
                            @endif
                            <table class="table table-striped table-bordered" id="table_id_packages">
                                <thead>
                                    <tr>
                                        <th>Sr.</th>
                                        <th>Package Name</th>
                                        <th>Month</th>
                                        <th>Stars</th>
										<th>Description</th>
                                        <th>Flight Info</th>
                                        <th>Visa Service</th>
                                        <th>City</th>
                                        <th>Hotel</th>
										<th>Time Duration</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($packages as $package)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $package->package_name }}</td>
                                        <td>{{ $package->month }}</td>
                                        <td>{{ $package->stars }}</td>
										<td>
											<div class="description-cell" title="{{ $package->description }}">
												{{ $package->description }}
											</div>
										</td>
										<td>{{ $package->flight_info ?? '-' }}</td>
                                        <td>{{ $package->visa_service}}</td>
                                        <td>
                                            @foreach ($package->packageDetails as $detail)
                                                {{ $detail->city->name ?? '-' }}<br>
                                            @endforeach
                                        </td>
                                        <td>
                                            @foreach ($package->packageDetails as $detail)
                                                {{ $detail->hotel->name ?? '-' }}<br>
                                            @endforeach
                                        </td>
										<td>
											@foreach ($package->packageDetails as $detail)
												{{ $detail->time_duration }} {{ $detail->city->name ?? '-' }}<br>
											@endforeach
										</td>                                        
										<td>
												<div class="d-flex align-items-center">
													<div class="custom-switch mr-0">
														<input type="checkbox" class="custom-control-input status-toggle" 
															id="status_{{ $package->id }}" 
															data-id="{{ $package->id }}" 
															{{ $package->status ? 'checked' : '' }}>
														<label class="custom-control-label" for="status_{{ $package->id }}"></label>
													</div>
													<span class="status-text" id="status-text-{{ $package->id }}">
														{{ $package->status ? 'Activated' : 'Deactivated' }}
													</span>
												</div>
											</td>
											<td>
                                            <div class="d-flex">
                                                <a href="{{ route('umrahpackages.edit',$package->id) }}" class="btn btn-primary mr-1">
                                                    <i class="fa fa-edit"></i>
                                                </a>

                                                <form id="delete-form-{{ $package->id }}" action="{{ route('umrahpackages.destroy',$package->id) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                </form>

                                                <button class="show_confirm btn" style="background-color: #343f52;" data-form="delete-form-{{ $package->id }}" type="button">
                                                    <span><i class="fa fa-trash"></i></span>
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
    </section>
</div>
@endsection

@section('js')
<script>
$(document).ready(function(){

    // Status toggle (aapka pehle ka code)
    $('.status-toggle').change(function(){
        var checkbox = $(this);
        var packageId = checkbox.data('id');
        var statusText = $('#status-text-' + packageId);
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
                    statusText.text(response.status ? 'Activated' : 'Deactivated');
                    statusText.removeClass('text-success text-danger')
                              .addClass(response.status ? 'text-success' : 'text-danger');
                    toastr.success('Package Status Updated Successfully');
                } else {
                    toastr.error('Something went wrong!');
                    checkbox.prop('checked', !newStatus); // revert
                }
            },
            error: function(){
                toastr.error('Error updating status!');
                checkbox.prop('checked', !newStatus); // revert
            }
        });
    });

    // Delete button confirm
    $('.show_confirm').click(function(event){
        event.preventDefault();
        var formId = $(this).data('form');
        Swal.fire({
            title: 'Are you sure you want to delete this record?',
            text: "If you delete this Package record, it will be gone forever.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $('#' + formId).submit(); // ✅ Submit the form
            }
        });
    });

});
</script>
@endsection