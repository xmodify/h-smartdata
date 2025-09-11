<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
     <link rel="icon" href="{{ asset('/images/favicon.ico') }}" type="image/x-icon">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title >OP Anywhere</title>

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

    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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
  <div class="row"  >
    <div class="col-sm-12"> 
        <div class="alert alert-success text-primary" role="alert"><strong>รายชื่อผู้มารับบริการ OP Anywhere วันที่ {{ DateThai($start_date) }} ถึง {{ DateThai($end_date) }}</strong></div>          
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
                  <th class="text-center" width="6%">Action</th>
                  <th class="text-center">Authen</th>  
                  <th class="text-center">ปิดสิทธิ</th>
                  <th class="text-center">Queue</th>                    
                  <th class="text-center">ชื่อ-สกุล</th>    
                  <th class="text-center">CID</th>           
                  <th class="text-center">วันที่รับบริการ</th> 
                  <th class="text-center">เวลา</th>                                      
                  <th class="text-center">เบอร์โทร</th>
                  <th class="text-center">ค่าบริการที่เบิกได้</th>
                  <th class="text-center">สิทธิการรักษา</th> 
                  <th class="text-center">Department</th>   
              </tr>
            </thead> 
            <tbody> 
              <?php $count = 1 ; ?>
              @foreach($sql as $row) 
              <tr>
                <td align="center">{{ $count }}</td>
                <td align="center" width="6%">                  
                  <button onclick="pullNhsoData('{{ $row->vstdate }}', '{{ $row->cid }}')" class="btn btn-outline-info btn-sm w-100">
                      ดึงปิดสิทธิ
                  </button>
                </td> 
                <td align="center" @if($row->auth_code == 'Y') style="color:green"
                  @elseif($row->auth_code == 'N') style="color:red" @endif>
                  <strong>{{ $row->auth_code }}</strong></td>               
                <td align="center" @if($row->endpoint == 'Y') style="color:green"
                  @elseif($row->endpoint == 'N') style="color:red" @endif>
                  <strong>{{ $row->endpoint }}</strong></td>
                <td align="left">{{$row->oqueue}}</td>
                <td align="left">{{$row->ptname}}</td> 
                <td align="center">{{$row->cid}}</td> 
                <td align="left">{{ DateThai($row->vstdate) }}</td>             
                <td align="rigth">{{$row->vsttime}}</td>                
                <td align="center">{{$row->mobile_phone_number}}</td> 
                <td align="right">{{ number_format($row->debtor,2) }}</td>
                <td align="left">{{$row->pttype}} [{{$row->hospmain}}]</td>
                <td align="left">{{$row->department}}</td>                  
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

<script>
  function pullNhsoData(vstdate, cid) {
      Swal.fire({
          title: 'กำลังดึงข้อมูล...',
          text: 'กรุณารอสักครู่',
          allowOutsideClick: false,
          didOpen: () => {
              Swal.showLoading()
          }
      });

      fetch("{{ url('dashboard/nhso_endpoint_pull') }}/" + vstdate + "/" + cid)
          .then(async response => {
              const data = await response.json();
              if (!response.ok) {
                  throw new Error(data.message || 'เกิดข้อผิดพลาดในการดึงข้อมูล');
              }
              return data;
          })
          .then(data => {
              Swal.fire({
                  icon: 'success',
                  title: 'ดึงข้อมูลสำเร็จ',
                  text: data.message || 'ข้อมูลถูกบันทึกเรียบร้อยแล้ว',
                  timer: 2000,
                  showConfirmButton: false
              }).then(() => {
                  location.reload();
              });
          })
          .catch(error => {
              Swal.fire({
                  icon: 'error',
                  title: 'เกิดข้อผิดพลาด',
                  text: error.message || 'ไม่สามารถเชื่อมต่อกับระบบได้',
              });
          });
  }
</script>

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