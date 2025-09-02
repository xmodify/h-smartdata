@extends('layouts.hrims') {{-- หรือสร้าง layout เฉพาะ hrims ก็ได้ --}}

@section('content')
<div class="container">
    <h3 class="text-center text-danger">
        <strong>ยินดีต้อนรับเข้าสู่</strong>...
    </h3>
    <h1 class="text-center text-success">       
        <img src="{{ asset('images/logo_hrims.jpg') }}" alt="H-RiMS Logo" height="200">
    </h1>
    <h3 class="text-center text-primary">
        <strong>Huataphanhospital Revenue Intelligent Management System</strong>
    </h3>
</div>
@endsection