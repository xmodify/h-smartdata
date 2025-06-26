@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="card">
    <div class="card-header bg-primary text-white">หมวดค่ารักษาพยาบาล </div>  
        <div class="card-body">         
            <div class="row">
                <div class="col-md-12">                          
                    <table class="table table-bordered table-striped">
                        <thead>
                        <tr class="table-secondary">
                            <th class="text-center">รหัส</th>
                            <th class="text-center">หมวดค่ารักษาพยาบาล</th>
                            <th class="text-center">หมวด Eclaim</th>
                            <th class="text-center">หมวด ADP</th>
                        </tr>     
                        </thead>   
                        @foreach($income as $row)          
                        <tr>
                            <td class="text-left">{{ $row->income }}</td>
                            <td class="text-left">{{ $row->name }}</td>
                            <td class="text-left">{{ $row->eclaim }}</td>
                            <td class="text-left">{{ $row->nhso_adp_type_name }}</td>
                        </tr>
                        @endforeach 
                    </table> 
                </div> 
            </div>        
        </div>
    </div>
 </div>
@endsection

