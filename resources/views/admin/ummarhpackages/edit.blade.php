@extends('admin.layout.app')
@section('title', 'Edit Umrah Package')

@section('css')

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

/* Error message styling */
/* Add this to your CSS */
.invalid-feedback {
    display: none; /* Change from block to none */
    width: 100%;
    margin-top: 4px;
    font-size: 12px;
    color: #dc3545;
}

.is-invalid {
    border-color: #dc3545 !important;
}

.is-invalid:focus {
    border-color: #dc3545;
    box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
}
</style>
@endsection

@section('content')
<div class="main-content">
<section class="section">
<div class="section-body">

<a href="{{ route('umrahpackages.index') }}" class="btn btn-primary mb-3">Back</a>

<form action="{{ route('umrahpackages.update', $package->id) }}" method="POST" enctype="multipart/form-data" id="umrahForm">
@csrf
@method('PUT')

<div class="card">
<h4 class="text-center my-4">Edit Umrah Package</h4>
<div class="row px-4">

{{-- Package Name --}}
<div class="col-md-6">
<div class="form-group">
<label>Package Name *</label>
<input type="text" name="name" class="form-control" required value="{{ old('name', $package->package_name) }}">
</div>
</div>

{{-- Price --}}
<div class="col-md-6">
<div class="form-group">
<label>Price (£) Per Person *</label>
<input type="number" step="0.01" name="price" class="form-control" required value="{{ old('price', $package->price_per_person) }}">
</div>
</div>

{{-- Stars --}}
<div class="col-md-6">
<div class="form-group">
<label>Stars *</label>
<input type="number" name="stars" class="form-control" min="1" max="5" required value="{{ old('stars', $package->stars) }}">
</div>
</div>

{{-- Month --}}
<div class="col-md-6">
<div class="form-group">
<label>Month *</label>
<select name="month" class="form-control" required>
<option value="">Select Month</option>
@foreach([1=>'Jan',2=>'Feb',3=>'Mar',4=>'Apr',5=>'May',6=>'Jun',7=>'Jul',8=>'Aug',9=>'Sep',10=>'Oct',11=>'Nov',12=>'Dec'] as $k=>$m)
<option value="{{ $k }}" {{ old('month', $package->month)==$k?'selected':'' }}>{{ $m }}</option>
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
<option value="{{ $hotel->id }}" {{ old('hotel_id', $package->packageDetails->first()->hotel_id ?? '')==$hotel->id?'selected':'' }}>{{ $hotel->name }}</option>
@endforeach
</select>
</div>
</div>

{{-- Status --}}
<div class="col-md-6">
<div class="form-group">
<label>Status</label>
<select name="status" id="status-select" class="form-control" required>
<option value="1" {{ $package->status ? 'selected' : '' }}>Active</option>
<option value="0" {{ !$package->status ? 'selected' : '' }}>Inactive</option>
</select>
<small id="status-text" class="text-success">Currently: {{ $package->status?'Active':'Inactive' }}</small>
</div>
</div>

{{-- City Wise Nights --}}
<div class="col-md-12 mt-2">
<label class="fw-bold">City Wise Nights *</label>
<div id="city-days-wrapper">
@foreach($package->packageDetails as $detail)
<div class="city-days-row mb-2">
<div style="flex:2">
<select name="cities[]" class="form-control" required>
<option value="">Select City</option>
@foreach($cities as $city)
<option value="{{ $city->id }}" {{ $detail->city_id==$city->id?'selected':'' }}>{{ $city->name }}</option>
@endforeach
</select>
</div>
<div style="flex:1">
<input type="number" name="days[]" class="form-control" placeholder="Nights" min="1" value="{{ intval($detail->time_duration) }}" required>
</div>
<div>
<button type="button" class="btn btn-danger remove-city">×</button>
</div>
</div>
@endforeach
</div>
<button type="button" id="add-city" class="btn btn-sm btn-primary">+ Add Another City</button>
</div>

{{-- Flight Info --}}
<div class="col-md-6 mt-3">
<div class="form-group">
<label>Flight Info *</label>
<input type="text" name="flight_info" class="form-control" required value="{{ old('flight_info', $package->flight_info) }}">
</div>
</div>

{{-- Visa Service --}}
<div class="col-md-6 mt-3">
<div class="form-group">
<label>Visa Service *</label>
<input type="text" name="visa_service" class="form-control" required value="{{ old('visa_service', $package->visa_service) }}">
</div>
</div>

{{-- Description --}}
<div class="col-md-12 mt-3">
<div class="form-group">
<label>Description *</label>
<textarea name="description" rows="4" class="form-control" required>{{ old('description', $package->description) }}</textarea>
</div>
</div>

{{-- Image --}}
<div class="col-md-6">
<div class="form-group">
<label>Package Image</label>
<input type="file" name="image" class="form-control">
@if($package->image)
<small class="d-block mt-2">
Current:
<img src="{{ asset($package->image) }}" width="80">
</small>
@endif
</div>
</div>

<div class="card-footer text-center">
<button type="submit" class="btn btn-success">Update Package</button>
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

    // Function to show error below input
    function showError(element, message) {
        element.addClass('is-invalid');
        // Remove existing error first
        element.next('.invalid-feedback').remove();
        // Add new error message
        element.after('<div class="invalid-feedback" style="display: block;">'+message+'</div>');
    }

    // Function to clear error
    function clearError(element){
        element.removeClass('is-invalid');
        element.next('.invalid-feedback').remove();
    }

    // On input focus or change, clear error
    $(document).on('focus input change', 'input, select, textarea', function(){
        clearError($(this));
    });

    // Add city row
    $('#add-city').click(function(){
        const html = `
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
        </div>`;
        $('#city-days-wrapper').append(html);
    });

    // Remove city row
    $(document).on('click', '.remove-city', function(){
        $(this).closest('.city-days-row').remove();
    });

    $('#umrahForm').submit(function(e){
        let valid = true;

        // Clear all existing errors first
        $('input, select, textarea').each(function(){
            clearError($(this));
        });

        // Package Name
        const name = $('input[name="name"]');
        if(name.val().trim() === ''){
            showError(name, 'Package name is required.');
            valid = false;
        }

        // Price
        const price = $('input[name="price"]');
        if(price.val() === '' || parseFloat(price.val()) <= 0){
            showError(price, 'Price must be greater than 0.');
            valid = false;
        }

        // Stars
        const stars = $('input[name="stars"]');
        if(stars.val() === '' || parseInt(stars.val()) < 1 || parseInt(stars.val()) > 5){
            showError(stars, 'Stars must be between 1 and 5.');
            valid = false;
        }

        // Month
        const month = $('select[name="month"]');
        if(month.val() === ''){
            showError(month, 'Please select a month.');
            valid = false;
        }

        // Hotel
        const hotel = $('select[name="hotel_id"]');
        if(hotel.val() === '' || hotel.val() === null){
            showError(hotel, 'Please select a hotel.');
            valid = false;
        }

        // Cities & Days
        $('select[name="cities[]"]').each(function(index){
            if($(this).val() === ''){
                showError($(this), 'Please select city at row ' + (index+1));
                valid = false;
            }
        });

        $('input[name="days[]"]').each(function(index){
            if($(this).val() === '' || parseInt($(this).val()) < 1){
                showError($(this), 'Please enter valid nights for row ' + (index+1));
                valid = false;
            }
        });

        // Flight Info
        const flight = $('input[name="flight_info"]');
        if(flight.val().trim() === ''){
            showError(flight, 'Flight info is required.');
            valid = false;
        }

        // Visa Service
        const visa = $('input[name="visa_service"]');
        if(visa.val().trim() === ''){
            showError(visa, 'Visa service is required.');
            valid = false;
        }

        // Description
        const desc = $('textarea[name="description"]');
        if(desc.val().trim() === ''){
            showError(desc, 'Description is required.');
            valid = false;
        }

        // Image validation (optional for edit)
        const imageInput = $('input[name="image"]');
        if(imageInput.val() !== ''){
            const fileExt = imageInput.val().split('.').pop().toLowerCase();
            const allowedExt = ['jpg','jpeg','png','gif','webp'];
            if(!allowedExt.includes(fileExt)){
                showError(imageInput, 'Image must be jpg, jpeg, png, gif or webp.');
                valid = false;
            }
        }

        if(!valid){
            e.preventDefault(); // stop form submission
            
            // Scroll to first error
            const firstError = $('.is-invalid').first();
            if(firstError.length){
                $('html, body').animate({
                    scrollTop: firstError.offset().top - 100
                }, 300);
            }
        }
    });

});
</script>
@endsection