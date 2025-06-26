@extends('layouts.app')
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.2.0/css/bootstrap.min.css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.1/css/dataTables.bootstrap5.min.css">

@section('content')
<div class="container-fluid">
    <h5 class="alert alert-primary"><strong>รายงานการคัดกรองสุขภาพเจ้าหน้าที่</strong></h5>  
</div> 
<div class="container-fluid">  
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
</div>
<!-- row -->
<div class="container-fluid">
  <div class="row justify-content-center">
    <div class="col-md-4">
      <div class="card">
        <div class="card-header bg-primary bg-opacity-75 text-white">ดัชนีมวลกายตามเกณท์ วันที่ {{ DateThai($start_date) }} ถึง {{ DateThai($end_date) }}</div>
        <div id="bmi" style="width: 100%; height: 350px"></div>
      </div>
    </div>    
    <div class="col-md-4">
      <div class="card">
        <div class="card-header bg-primary bg-opacity-75 text-white">ประวัติเจ็บป่วยโรคเบาหวาน วันที่ {{ DateThai($start_date) }} ถึง {{ DateThai($end_date) }}</div>
        <div id="dm" style="width: 100%; height: 350px"></div>
      </div>
    </div>    
    <div class="col-md-4">
      <div class="card">
        <div class="card-header bg-primary bg-opacity-75 text-white">ประวัติเจ็บป่วยโรคความดัน วันที่ {{ DateThai($start_date) }} ถึง {{ DateThai($end_date) }}</div>
        <div id="ht" style="width: 100%; height: 350px"></div>
      </div>
    </div>    
  </div>
</div>
<br>
<!-- row -->
<div class="container-fluid">
  <div class="row justify-content-center">
    <div class="col-md-4">
      <div class="card">
        <div class="card-header bg-primary bg-opacity-75 text-white">อุบัติเหตุจากการทำงาน วันที่ {{ DateThai($start_date) }} ถึง {{ DateThai($end_date) }}</div>
        <div id="accident" style="width: 100%; height: 350px"></div>
      </div>
    </div>    
    <div class="col-md-4">
      <div class="card">
        <div class="card-header bg-primary bg-opacity-75 text-white">ติดเชื้อจากการทำงาน วันที่ {{ DateThai($start_date) }} ถึง {{ DateThai($end_date) }}</div>
        <div id="infect" style="width: 100%; height: 350px"></div>
      </div>
    </div>    
    <div class="col-md-4">
      <div class="card">
        <div class="card-header bg-primary bg-opacity-75 text-white">การออกกำลังกาย วันที่ {{ DateThai($start_date) }} ถึง {{ DateThai($end_date) }}</div>
        <div id="exer" style="width: 100%; height: 350px"></div>
      </div>
    </div>    
  </div>
</div>
<br>
<!--row-->
<div class="container-fluid"> 
    <div class="row" >
        <div class="col-md-11">
        </div>
        <div class="col-md-1" align="right">
            <a class="btn btn-success my-1" href="{{ url('backoffice_hrd/health_screen_excel') }}" 
                target="_blank" type="submit"> {{ __('Excel') }}
            </a>
        </div>
    </div>
    <div class="card"> 
        <div class="card-header bg-primary bg-opacity-75 text-white"><strong>รายชื่อเจ้าหน้าที่ที่ทำการคัดกรองตัวเอง วันที่ {{ DateThai($start_date) }} ถึง {{ DateThai($end_date) }}</strong></div> 
        <div class="card-body">  
            <table id="health_screen" class="table table-bordered table-striped my-3">
                <thead>
                    <tr class="table-secondary">
                        <th class="text-center">ลำดับ</th> 
                        <th class="text-center">ชื่อ-สกุล</th>   
                        <th class="text-center">หน่วยงาน</th>                     
                        <th class="text-center">วันที่คัดกรอง</th>
                        <th class="text-center">อายุ</th> 
                        <th class="text-center">กรุ๊ปเลือด</th>
                        <th class="text-center">ส่วนสูง</th>
                        <th class="text-center">น้ำหนัก</th>
                        <th class="text-center">ดัชนีมวลกาย</th>
                        <th class="text-center">อยู่ในเกณท์</th>
                        <th class="text-center">เบาหวาน</th>
                        <th class="text-center">ความดัน</th>
                        <th class="text-center">อุบัติเหตุจากทำงาน</th>
                        <th class="text-center">ติดเชื้อจากทำงาน</th>
                        <th class="text-center">ออกกำลังกาย</th>
                        <th class="text-center">สูบบุหรี่</th>
                        <th class="text-center">ดื่มแอลกอฮอล์</th>
                    </tr>
                </thead>
                <?php $count = 1 ; ?>
                @foreach($health_screen as $row)
                    <tr>
                        <td align="right">{{ $count }}</td>
                        <td align="left">{{ $row->hrd_name }} </td>
                        <td align="left">{{ $row->HR_DEPARTMENT_SUB_SUB_NAME }} </td>
                        <td align="right">{{ DateThai($row->HEALTH_SCREEN_DATE) }}</td> 
                        <td align="right">{{ $row->HEALTH_SCREEN_AGE }} </td>
                        <td align="center">{{ $row->HR_BLOODGROUP_NAME }} </td>
                        <td align="right">{{ $row->HEALTH_SCREEN_HEIGHT }}</td>
                        <td align="right">{{ $row->HEALTH_SCREEN_WEIGHT }}</td>
                        <td align="right">{{ $row->HEALTH_SCREEN_BODY }}</td>
                        <td align="left">{{ $row->bmi }}</td>
                        <td align="left">{{ $row->dm }}</td>
                        <td align="left">{{ $row->ht }}</td>
                        <td align="left">{{ $row->accident }} {{ $row->accident_comment }}</td>
                        <td align="left">{{ $row->infect }} {{ $row->infect_comment }}</td>
                        <td align="left">{{ $row->exer }}</td>
                        <td align="left">{{ $row->smok }}</td>
                        <td align="left">{{ $row->drink }}</td>
                    </tr>
                <?php $count++; ?>
                @endforeach
            </table>
        </div>         
    </div>
</div>
<br>
<!--row--> 
<div class="container-fluid"> 
    <div class="row" >
            <div class="col-md-11">
            </div>
            <div class="col-md-1" align="right">
                <a class="btn btn-primary my-1" href="{{ url('backoffice_hrd/health_notscreen_pdf') }}" 
                    target="_blank" type="submit"> {{ __('พิมพ์') }}
                </a>
            </div>
    </div>
    <div class="card">
        <div class="card-header bg-primary bg-opacity-75 text-white"><strong>รายชื่อเจ้าหน้าที่ที่ยังไม่ทำการคัดกรองตัวเอง วันที่ {{ DateThai($start_date) }} ถึง {{ DateThai($end_date) }}</strong></div> 
        <div class="card-body">     
            <table id="health_notscreen" class="table table-bordered table-striped my-3">
                <thead>
                    <tr class="table-secondary">
                        <th class="text-center">ลำดับ</th> 
                        <th class="text-center">ชื่อ-สกุล</th>
                        <th class="text-center">เพศ</th>  
                        <th class="text-center">อายุ</th> 
                        <th class="text-center">กรุ๊ปเลือด</th>
                        <th class="text-center">ประเภทการจ้าง</th>
                        <th class="text-center">หน่วยงาน</th>  
                        <th class="text-center">เบอร์โทร.</th>   
                    </tr>
                </thead>
                <?php $count = 1 ; ?>
                @foreach($health_notscreen as $row)
                    <tr>
                        <td align="right">{{ $count }}</td>
                        <td align="left">{{ $row->hrd_name }} </td>
                        <td align="center">{{ $row->SEX }} </td>
                        <td align="center">{{ $row->AGE }} </td>  
                        <td align="center">{{ $row->HR_BLOODGROUP_NAME }} </td>
                        <td align="left">{{ $row->HR_PERSON_TYPE_NAME }}</td>
                        <td align="left">{{ $row->HR_DEPARTMENT_SUB_SUB_NAME }}</td>
                        <td align="center">{{ $row->HR_PHONE }}</td>
                    </tr>
                <?php $count++; ?>
                @endforeach
            </table>
        </div>         
    </div>
</div>
@endsection
<!-- Vendor JS Files -->
<script src="{{ asset('assets/vendor/apexcharts/apexcharts.min.js') }}"></script>
<script src="{{ asset('assets/vendor/chart.js/chart.min.js') }}"></script>
<script src="{{ asset('assets/vendor/echarts/echarts.min.js') }}"></script>
<!-- datatable -->
<script type="text/javascript" language="javascript" src="https://code.jquery.com/jquery-3.5.1.js"></script>
<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/1.13.1/js/dataTables.bootstrap5.min.js"></script>
<script type="text/javascript" class="init">
    $(document).ready(function () {
        $('#health_screen').DataTable();
    });
</script>
<script type="text/javascript" class="init">
    $(document).ready(function () {
        $('#health_notscreen').DataTable();
    });
</script>

<!-- Pie Chart -->
<script>
    document.addEventListener("DOMContentLoaded", () => {
        new ApexCharts(document.querySelector("#bmi"), {
            series: [{{$bmi_1}},{{$bmi_2}},{{$bmi_3}},{{$bmi_4}},{{$bmi_5}}],
            chart: {
                height: 350,
                type: 'pie',
                toolbar: {
                    show: true
                }
            },
            labels: ['นน.ต่ำกว่าเกณฑ์','สมส่วน','น้ำหนักเกิน','โรคอ้วน','โรคอ้วนอันตราย']
        }).render();
    });
</script>
<script>
    document.addEventListener("DOMContentLoaded", () => {
        new ApexCharts(document.querySelector("#dm"), {
            series: [{{$dm_1}},{{$dm_2}},{{$dm_3}}],
            chart: {
                height: 350,
                type: 'pie',
                toolbar: {
                    show: true
                }
            },
            labels: ['เป็นเบาหวาน','ไม่เป็นเบาหวาน','ไม่เคยตรวจ']
        }).render();
    });
</script>
<script>
    document.addEventListener("DOMContentLoaded", () => {
        new ApexCharts(document.querySelector("#ht"), {
            series: [{{$ht_1}},{{$ht_2}},{{$ht_3}}],
            chart: {
                height: 350,
                type: 'pie',
                toolbar: {
                    show: true
                }
            },
            labels: ['เป็นความดัน','ไม่เป็นความดัน','ไม่เคยตรวจ']
        }).render();
    });
</script>
<script>
    document.addEventListener("DOMContentLoaded", () => {
        new ApexCharts(document.querySelector("#accident"), {
            series: [{{$accident_1}},{{$accident_2}},{{$accident_3}}],
            chart: {
                height: 350,
                type: 'pie',
                toolbar: {
                    show: true
                }
            },
            labels: ['มี','ไม่มี','ไม่เคยตรวจ']
        }).render();
    });
</script>
<script>
    document.addEventListener("DOMContentLoaded", () => {
        new ApexCharts(document.querySelector("#infect"), {
            series: [{{$infect_1}},{{$infect_2}},{{$infect_3}}],
            chart: {
                height: 350,
                type: 'pie',
                toolbar: {
                    show: true
                }
            },
            labels: ['มี','ไม่มี','ไม่เคยตรวจ']
        }).render();
    });
</script>
<script>
    document.addEventListener("DOMContentLoaded", () => {
        new ApexCharts(document.querySelector("#exer"), {
            series: [{{$exer_1}},{{$exer_2}},{{$exer_3}},{{$exer_4}}],
            chart: {
                height: 350,
                type: 'pie',
                toolbar: {
                    show: true
                }
            },
            labels: ['ทุกวันครั้งละ 30นาที','สัปดาห์ละ 3ครั้ง ครั้งละ 30นาที','น้อยกว่าสัปดาห์ละ 3ครั้ง','ไม่ออกกำลังกาย']
        }).render();
    });
</script>
