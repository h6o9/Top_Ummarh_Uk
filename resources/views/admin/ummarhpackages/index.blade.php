@extends('admin.layout.app')
@section('title', 'Umrah Packages')

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
                                            <label class="custom-switch">
                                                <input type="checkbox" class="custom-switch-input toggle-status" data-id="{{ $package->id }}" {{ $package->status ? 'checked' : '' }}>
                                                <span class="custom-switch-indicator"></span>
                                                <span class="custom-switch-description">
                                                    {{ $package->status ? 'Activated' : 'Deactivated' }}
                                                </span>
                                            </label>
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

                                                <button class="show_confirm btn" style="background-color: #cb84fe;" data-form="delete-form-{{ $package->id }}" type="button">
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
$(document).ready(function() {
    // DataTable init (unchanged)
    $('#table_id_packages').DataTable({
        "order": [[0, "asc"]],
        "responsive": true
    });

    // Delete confirmation (unchanged)
    $(document).on('click', '.show_confirm', function(e) {
        e.preventDefault();
        let form = $(this).closest('form');

        Swal.fire({
            title: 'Are you sure you want to delete this record??',
            text: "If you delete this Ummrah Package record, it will be gone forever.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if(result.isConfirmed){
                form.submit();
            }
        });
    });

    // ✅ Toggle Status AJAX
    $('.toggle-status').on('change', function() {
        var packageId = $(this).data('id');
        var checkbox = $(this);
        $.ajax({
            url: "{{ route('umrahpackages.toggleStatus') }}",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                id: packageId
            },
            success: function(response) {
                if(response.success){
                    toastr.success(response.message);
                    checkbox.closest('.custom-switch').find('.custom-switch-description')
                        .text(response.status ? 'Activated' : 'Deactivated');
                } else {
                    toastr.error(response.message);
                    checkbox.prop('checked', !checkbox.prop('checked')); // revert if fail
                }
            },
            error: function() {
                toastr.error('Failed to update status!');
                checkbox.prop('checked', !checkbox.prop('checked')); // revert if fail
            }
        });
    });
});
</script>
@endsection