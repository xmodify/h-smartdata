<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title >UC-OP บริการเฉพาะ CR</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <!-- <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet"> -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
        
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.2.0/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.1/css/dataTables.bootstrap5.min.css">

<style>
  table {
  border-collapse: collapse;
  border-spacing: 0;
  width: 100%;
  border: 1px solid #ddd;
  }
  th, td {
  padding: 8px;
  }  
</style>
</head>
<body>

<div class="container-fluid">  
  <div class="row">
    <div class="col-sm-12"> 
        <div class="alert alert-success text-primary" role="alert"><strong>รายชื่อผู้มารับบริการ UC-OP บริการเฉพาะ CR วันที่ {{ DateThai($start_date) }} ถึง {{ DateThai($end_date) }}</strong></div>          
    </div>
  </div>   
  <form method="POST" enctype="multipart/form-data">
      @csrf            
      <div class="row" >
              <label class="col-md-3 col-form-label text-md-end my-1">{{ __('วันที่') }}</label>
          <div class="col-md-2">
              <input type="date" name="start_date" class="form-control my-1" placeholder="Date" value="{{ $start_date }}" > 
          </div>
              <label class="col-md-1 col-form-label text-md-end my-1">{{ __('ถึง') }}</label>
          <div class="col-md-2">
              <input type="date" name="end_date" class="form-control my-1" placeholder="Date" value="{{ $end_date }}" > 
          </div>                     
          <div class="col-md-1" >                            
              <button type="submit" class="btn btn-primary my-1 ">{{ __('ค้นหา') }}</button>
          </div>
      </div>
  </form> 
</div> <!-- row --> 

<div class="container-fluid"> 
  <div class="card-body">
    <div class="row">        
      <div class="col-md-12"> 
        <div style="overflow-x:auto;">            
          <table id="list" class="table table-striped table-bordered" width = "100%">
            <thead>
              <tr class="table-primary"> 
                <th class="text-center">ลำดับ</th>
                <th class="text-center">Authen</th>
                <th class="text-center">ปิดสิทธิ</th>
                {{-- <th class="text-center">Action</th>                 --}}
                <th class="text-center">Q</th>              
                <th class="text-center">ชื่อ-สกุล</th>
                <th class="text-center">CID</th>
                <th class="text-center" width="5%">วันที่</th>
                <th class="text-center">เวลา</th>
                <th class="text-center">เบอร์โทร</th>
                <th class="text-center">ค่าบริการที่เบิกได้</th> 
                <th class="text-center">สิทธิ</th>
                <th class="text-center">ICD10</th>
                <th class="text-center">Project</th>
                <th class="text-center">เรียกเก็บ</th>   
                <th class="text-center">รายการเรียกเก็บ</th> 
                <th class="text-center">Department</th>                 
                <th class="text-center text-success" width="4%">พร้อมส่ง</th>
                <th class="text-center text-success" width="4%">ประสงค์เบิก</th>
                <th class="text-center text-success" width="6%">Upload DateTime</th>
            </tr>
            </thead> 
            <tbody> 
              <?php $count = 1 ; ?>
              @foreach($sql as $row) 
              <tr>                
                <td align="center">{{ $count }}</td> 
                <td align="center" @if($row->auth_code == 'Y') style="color:green"
                  @elseif($row->auth_code == 'N') style="color:red" @endif>
                  <strong>{{ $row->auth_code }}</strong></td> 
                <td align="center" @if($row->endpoint == 'Y') style="color:green"
                  @elseif($row->endpoint == 'N') style="color:red" @endif>
                  <strong>{{ $row->endpoint }}</strong></td> 
                {{-- <td align="center"><a class="btn btn-outline-info btn-sm" href="{{ url('medicalrecord_opd/nhso_endpoint_pull'.$row->vstdate,$row->cid) }}" >Pull Endpoint</a></td>  --}}
                <td align="center">{{ $row->oqueue }}</td>                
                <td align="left">{{ $row->ptname }}</td>
                <td align="center">{{ $row->cid }}</td>
                <td align="center">{{ DateThai($row->vstdate) }}</td>
                <td align="center">{{ $row->vsttime }}</td>
                <td align="center">{{ $row->informtel }}</td>
                <td align="right">{{ number_format($row->debtor,2) }}</td>
                <td align="left">{{ $row->pttype }} [{{ $row->hospmain }}]</td>
                <td align="right">{{ $row->pdx }}</td>     
                <td align="right">{{ $row->project }}</td>  
                <td align="right">{{ number_format($row->debtor1,2) }}</td>               
                <td align="left">{{ $row->other_list }}</td>
                <td align="left">{{ $row->department }}</td>                    
                <td align="center">{{ $row->confirm_and_locked }}</td> 
                <td align="center">{{ $row->request_funds }}</td> 
                <td align="center">{{ DateTimeThai($row->fdh) }}</td> 
              </tr>
              <?php $count++; ?>
              @endforeach                 
            </tbody>
          </table>   
        </div>          
      </div>  
    </div> 
  </div> 
</div>      
</body>

<script type="text/javascript" language="javascript" src="https://code.jquery.com/jquery-3.5.1.js"></script>
<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/1.13.1/js/dataTables.bootstrap5.min.js"></script>
<script type="text/javascript" class="init">
    $(document).ready(function () {
        $('#list').DataTable();
    });
</script>

</html>