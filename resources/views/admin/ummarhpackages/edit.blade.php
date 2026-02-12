@extends('admin.layout.app')
@section('title', 'Edit Umrah Package')

@section('css')
<style>
.form-control { height:42px !important; padding:0 12px !important; }
textarea.form-control { height:auto !important; padding:10px 12px !important; }

.city-days-row { display:flex; gap:10px; align-items:center; }
.city-days-row .remove-city { height:42px; width:42px; font-size:20px; display:flex; justify-content:center; align-items:center; }

#add-city { margin-top:8px; }
.card-footer button { padding:10px 40px; font-size:16px; }

.invalid-feedback { font-size:12px; color:#dc3545; margin-top:4px; }
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
<label>Package Name *</label>
<input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $package->package_name) }}" >
@error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>

{{-- Price --}}
<div class="col-md-6">
<label>Price (PKR) Per Person *</label>
<input type="number" name="price" class="form-control @error('price') is-invalid @enderror" min="1" max="100000" value="{{ old('price', $package->price_per_person) }}" >
@error('price')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>

{{-- Stars --}}
<div class="col-md-6">
<label>Stars *</label>
<input type="number" name="stars" class="form-control @error('stars') is-invalid @enderror" min="1" value="{{ old('stars', $package->stars) }}" >
@error('stars')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>

{{-- Month --}}
<div class="col-md-6">
<label>Month *</label>
<select name="month" class="form-control @error('month') is-invalid @enderror" >
<option value="">Select Month</option>
@foreach([1=>'Jan',2=>'Feb',3=>'Mar',4=>'Apr',5=>'May',6=>'Jun',7=>'Jul',8=>'Aug',9=>'Sep',10=>'Oct',11=>'Nov',12=>'Dec'] as $k=>$m)
<option value="{{ $k }}" {{ old('month', $package->month)==$k?'selected':'' }}>{{ $m }}</option>
@endforeach
</select>
@error('month')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>

{{-- Hotel --}}
<div class="col-md-6">
<label>Hotel *</label>
<select name="hotel_id" class="form-control @error('hotel_id') is-invalid @enderror" >
<option value="">Select Hotel</option>
@foreach($hotels as $hotel)
<option value="{{ $hotel->id }}" {{ old('hotel_id', $package->packageDetails->first()->hotel_id ?? '')==$hotel->id?'selected':'' }}>{{ $hotel->name }}</option>
@endforeach
</select>
@error('hotel_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>

{{-- Status --}}
<div class="col-md-6">
<label>Status</label>
<select name="status" class="form-control @error('status') is-invalid @enderror" >
<option value="1" {{ $package->status?'selected':'' }}>Active</option>
<option value="0" {{ !$package->status?'selected':'' }}>Inactive</option>
</select>
@error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>

{{-- City Wise Nights --}}
<div class="col-md-12 mt-2">
<label class="fw-bold">City Wise Nights *</label>
<div id="city-days-wrapper">
@foreach($package->packageDetails as $detail)
<div class="city-days-row mb-2">
<div style="flex:2">
<select name="cities[]" class="form-control @error('cities.*') is-invalid @enderror" >
<option value="">Select City</option>
@foreach($cities as $city)
<option value="{{ $city->id }}" {{ $detail->city_id==$city->id?'selected':'' }}>{{ $city->name }}</option>
@endforeach
</select>
@error('cities.*')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>
<div style="flex:1">
<input type="number" name="days[]" class="form-control @error('days.*') is-invalid @enderror" placeholder="Nights" min="1" max="30" value="{{ intval($detail->time_duration) }}" >
@error('days.*')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>
<div>
<button type="button" class="btn btn-danger remove-city">×</button>
</div>
</div>
@endforeach
</div>
<button type="button" id="add-city" class="btn btn-sm btn-primary mt-2">+ Add Another City</button>
</div>

{{-- Flight Info --}}
<div class="col-md-6 mt-3">
<label>Flight Info *</label>
<input type="text" name="flight_info" class="form-control @error('flight_info') is-invalid @enderror" value="{{ old('flight_info', $package->flight_info) }}" >
@error('flight_info')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>

{{-- Visa Service --}}
<div class="col-md-6 mt-3">
<label>Visa Service *</label>
<input type="text" name="visa_service" class="form-control @error('visa_service') is-invalid @enderror" value="{{ old('visa_service', $package->visa_service) }}" >
@error('visa_service')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>

{{-- Description --}}
<div class="col-md-12 mt-3">
<label>Description *</label>
<textarea name="description" rows="4" class="form-control @error('description') is-invalid @enderror" >{{ old('description', $package->description) }}</textarea>
@error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>

{{-- Image --}}
<div class="col-md-6 mt-3">
<label>Package Image</label>
<input type="file" name="image" class="form-control @error('image') is-invalid @enderror">
@error('image')<div class="invalid-feedback">{{ $message }}</div>@enderror
@if($package->image)
<small class="d-block mt-2">Current: <img src="{{ asset('public/' . $package->image) }}" width="80"></small>
@endif
</div>

<div class="col-md-12 text-center mt-4 mb-3">
<button type="submit" class="btn btn-success">Update Package</button>
</div>

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

    // Clear validation error on focus
    $(document).on('focus', 'input, select, textarea', function(){
        $(this).removeClass('is-invalid');
        $(this).next('.invalid-feedback').remove();
    });

    // Add city row dynamically
    $('#add-city').click(function(){
        let html = `<div class="city-days-row mb-2">
        <div style="flex:2">
            <select name="cities[]" class="form-control" >
                <option value="">Select City</option>
                @foreach($cities as $city)
                <option value="{{ $city->id }}">{{ $city->name }}</option>
                @endforeach
            </select>
        </div>
        <div style="flex:1">
            <input type="number" name="days[]" class="form-control" placeholder="Nights" min="1" max="30" >
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

});
</script>
@endsection