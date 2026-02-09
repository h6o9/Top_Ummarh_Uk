@extends('admin.layout.app')
@section('title', 'Create Umrah Package')

@section('css')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<style>
.form-control {
    height: 42px !important;
    line-height: 42px !important;
    padding: 0 12px !important;
}

textarea.form-control {
    height: auto !important;
    line-height: 1.5 !important;
    padding: 10px 12px !important;
}

.city-days-row {
    display: flex;
    gap: 10px;
    align-items: center;
}

.city-days-row .remove-city {
    height: 42px;
    width: 42px;
    font-size: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
}

#add-city { margin-top: 8px; }
.card-footer button { padding: 10px 40px; font-size: 16px; }
</style>
@endsection

@section('content')
<div class="main-content">
<section class="section">
<div class="section-body">

<a href="{{ route('umrahpackages.index') }}" class="btn btn-primary mb-3">Back</a>

<form action="{{ route('umrahpackages.store') }}" method="POST" enctype="multipart/form-data" id="umrahForm">
@csrf

<div class="card">
<h4 class="text-center my-4">Create Umrah Package</h4>
<div class="row px-4">

{{-- Package Name --}}
<div class="col-md-6">
<div class="form-group">
<label>Package Name *</label>
<input type="text" name="name" class="form-control" required>
</div>
</div>

{{-- Price --}}
<div class="col-md-6">
<div class="form-group">
<label>Price (£) Per Person *</label>
<input type="number" step="0.01" name="price" class="form-control" required>
</div>
</div>

{{-- Stars --}}
<div class="col-md-6">
<div class="form-group">
<label>Stars *</label>
<input type="number" name="stars" class="form-control" min="1" max="5" required>
</div>
</div>

{{-- Month --}}
<div class="col-md-6">
<div class="form-group">
<label>Month *</label>
<select name="month" class="form-control" required>
<option value="">Select Month</option>
@foreach([1=>'Jan',2=>'Feb',3=>'Mar',4=>'Apr',5=>'May',6=>'Jun',7=>'Jul',8=>'Aug',9=>'Sep',10=>'Oct',11=>'Nov',12=>'Dec'] as $k=>$m)
<option value="{{ $k }}">{{ $m }}</option>
@endforeach
</select>
</div>
</div>

{{-- Hotel --}}
<div class="col-md-6">
<div class="form-group">
<label>Hotel *</label>
<select name="hotel_id" class="form-control" required>
<option value="">Select Hotel</option>
@foreach($hotels as $hotel)
<option value="{{ $hotel->id }}">{{ $hotel->name }}</option>
@endforeach
</select>
</div>
</div>

{{-- Status --}}
<div class="col-md-6">
    <div class="form-group">
        <label>Status</label>
        <select name="status" id="status-select" class="form-control" required>
            <option value="1" selected>Active</option>
            <option value="0">Inactive</option>
        </select>
        <small id="status-text" class="text-success">Currently: Active</small>
    </div>
</div>
{{-- City wise nights --}}
<div class="col-md-12 mt-2">
<label class="fw-bold">City Wise Nights *</label>
<div id="city-days-wrapper">
<div class="city-days-row mb-2">
<div style="flex:2">
<select name="cities[]" class="form-control" required>
<option value="">Select City</option>
@foreach($cities as $city)
<option value="{{ $city->id }}">{{ $city->name }}</option>
@endforeach
</select>
</div>
<div style="flex:1">
<input type="number" name="days[]" class="form-control" placeholder="Nights" min="1" required>
</div>
<div>
<button type="button" class="btn btn-danger remove-city">×</button>
</div>
</div>
</div>

<button type="button" id="add-city" class="btn btn-sm btn-primary">+ Add Another City</button>
</div>

{{-- Flight Info --}}
<div class="col-md-6 mt-3">
<div class="form-group">
<label>Flight Info *</label>
<input type="text" name="flight_info" class="form-control" required>
</div>
</div>

{{-- Visa Service --}}
<div class="col-md-6 mt-3">
<div class="form-group">
<label>Visa Service *</label>
<input type="text" name="visa_service" class="form-control" required>
</div>
</div>

{{-- Description --}}
<div class="col-md-12 mt-3">
<div class="form-group">
<label>Description *</label>
<textarea name="description" rows="4" class="form-control" required></textarea>
</div>
</div>

{{-- Image --}}
<div class="col-md-6">
<div class="form-group">
<label>Package Image *</label>
<input type="file" name="image" class="form-control" accept="image/*" required>
</div>
</div>

</div>

<div class="card-footer text-center">
<button type="submit" class="btn btn-success">Save Package</button>
</div>

</div>
</form>

</div>
</section>
</div>
@endsection

@section('js')
<script>
$(document).ready(function(){

    // 🌆 Cities array from backend
    const cities = @json($cities);

    // ➕ Add new city row
    $('#add-city').click(function(){
        let options = `<option value="">Select City</option>`;
        cities.forEach(city => {
            options += `<option value="${city.id}">${city.name}</option>`;
        });

        $('#city-days-wrapper').append(`
            <div class="city-days-row mb-2">
                <div style="flex:2">
                    <select name="cities[]" class="form-control" required>
                        ${options}
                    </select>
                </div>
                <div style="flex:1">
                    <input type="number" name="days[]" class="form-control" placeholder="Nights" min="1" required>
                </div>
                <div>
                    <button type="button" class="btn btn-danger remove-city">×</button>
                </div>
            </div>
        `);
    });

    // ❌ Remove city row
    $(document).on('click', '.remove-city', function(){
        $(this).closest('.city-days-row').remove();
    });

    // ⚠ Prevent form submit if cities/days mismatch
    $('#umrahForm').submit(function(e){
        const citiesCount = $('select[name="cities[]"]').length;
        const daysCount   = $('input[name="days[]"]').length;
        if(citiesCount !== daysCount){
            alert("Each city must have a corresponding number of nights.");
            e.preventDefault();
        }
    });

    // 🔄 Status dropdown text update
    const statusSelect = $('#status-select');
    const statusText   = $('#status-text');

    function updateStatusText() {
        if(statusSelect.val() === '1'){
            statusText.text("Currently: Active")
                      .removeClass('text-danger')
                      .addClass('text-success');
        } else {
            statusText.text("Currently: Inactive")
                      .removeClass('text-success')
                      .addClass('text-danger');
        }
    }

    // Run on page load
    updateStatusText();

    // Update whenever dropdown changes
    statusSelect.change(updateStatusText);

});
</script>
@endsection