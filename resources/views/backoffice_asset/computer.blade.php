@extends('layouts.app')
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
    tr:nth-child(even){background-color: #f2f2f2}
</style>

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
      <div class="col-md-12">
          <form method="POST" enctype="multipart/form-data">
          @csrf
            <div class="row">
                <div class="col-md-9" align="left"></div>
                <div class="col-md-2" align="right">
                    <select class="form-select my-1" name="budget_year">
                    @foreach ($budget_year_select as $row)
                    <option value="{{$row->LEAVE_YEAR_ID}}" @if ($budget_year == "$row->LEAVE_YEAR_ID") selected="selected"  @endif>{{$row->LEAVE_YEAR_NAME}}</option>
                    @endforeach
                    </select>
                </div>
                <div class="col-md-1" align="right">
                    <button type="submit" class="btn btn-primary my-1 ">{{ __('ค้นหา') }}</button>
                </div>
            </div>
          </form>
      </div>
    </div>
</div>

<div class="container-fluid">
    <div class="card">
    <div class="card-header bg-primary text-white">ครุภัณฑ์คอมพิวเตอร์ทั้งหมด สถานะใช้งานปกติ {{$budget_year}}</div>  
        <div class="card-body"> 
            <div style="overflow-x:auto;">          
                <div class="row">
                    <div class="col-md-12">                         
                        <div class="row">                
                            <div class="col-md-12" align="right" >
                                <a class="btn btn-outline-danger mb-2"  href="{{ url('backoffice_asset/computer_pdf') }}" target="_blank" type="submit">
                                    พิมพ์
                                </a>     
                                <a class="btn btn-outline-success mb-2"  href="{{ url('backoffice_asset/computer_excel') }}" target="_blank" type="submit">
                                    Excel
                                </a>                    
                            </div>      
                        </div>    
                        <table id="office" class="table table-bordered table-striped">
                            <thead>
                            <tr class="table-secondary">
                                <th class="text-center">ลำดับ</th>   
                                <th class="text-center">ชื่อครุภัณฑ์</th>
                                <th class="text-center">รหัสครุภัณฑ์</th>
                                <th class="text-center">รหัสทรัพย์สิน</th>
                                <th class="text-center">วันที่ได้มา</th>
                                <th class="text-center">แหล่งเงิน</th>   
                                <th class="text-center">วิธีได้มา</th>    
                                <th class="text-center">ราคาทรัพย์สิน</th>   
                                <th class="text-center">ประจำหน่วยงาน</th> 
                                <th class="text-center">อายุการใช้งาน</th>                                                   
                            </thead>   
                            <?php $count = 1 ; ?>                                              
                            @foreach($asset as $row)  
                            <?php $diff = abs(strtotime(date('Y-m-d')) - strtotime($row->RECEIVE_DATE));  ?> 
                            <?php $years = floor($diff / (365*60*60*24));  ?> 
                            <?php $months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));  ?> 
                            <?php $days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));  ?> 
                            <tr>                          
                                <td align="left">{{ $count }}</td>      
                                <td align="left">{{ $row->ARTICLE_NAME }}</td>
                                <td align="left">{{ $row->ARTICLE_NUM }}</td>
                                <td align="left">{{ $row->SUP_FSN }}</td>
                                <td align="center">{{ DateThai($row->RECEIVE_DATE) }}</td>
                                <td align="left">{{ $row->BUDGET_NAME }}</td>
                                <td align="left">{{ $row->BUY_NAME  }}</td>
                                <td align="right">{{ number_format($row->PRICE_PER_UNIT,2) }}</td>   
                                <td align="left">{{ $row->HR_DEPARTMENT_SUB_SUB_NAME }}</td>
                                <td align="left">{{$years}} ปี {{$months}} เดือน {{$days}} วัน</td>
                                <?php $count++; ?>
                            @endforeach 
                        </table> 
                    </div> 
                </div>        
            </div>
        </div>
    </div>
</div>
<br>
<div class="container-fluid">
    <div class="card">
    <div class="card-header bg-primary text-white">เครื่องคอมพิวเตอร์ สถานะใช้งานปกติ</div>  
        <div class="card-body"> 
            <div style="overflow-x:auto;">          
                <div class="row">
                    <div class="col-md-12"> 
                        <li>@foreach($server as $item)เครื่องคอมพิวเตอร์ Server จำนวน <strong>{{$item->sum}}</strong> เครื่อง @endforeach</li>
                        <li>@foreach($client_pc as $item)เครื่องคอมพิวเตอร์ Client_PC จำนวน <strong>{{$item->sum}}</strong> เครื่อง | ติดตั้ง WindowsLicense <strong>{{$item->window}}</strong> เครื่อง | ติดตั้ง AntiVirus <strong>{{$item->antivirus}}</strong> เครื่อง @endforeach</li>
                        <li>@foreach($client_notebook as $item)เครื่องคอมพิวเตอร์ Client_Notebook จำนวน <strong>{{$item->sum}}</strong> เครื่อง | ติดตั้ง WindowsLicense <strong>{{$item->window}}</strong> เครื่อง | ติดตั้ง AntiVirus <strong>{{$item->antivirus}}</strong> เครื่อง @endforeach</li>
                        <li>@foreach($client_tablet as $item)เครื่องคอมพิวเตอร์ Client_Tablet จำนวน <strong>{{$item->sum}}</strong> เครื่อง @endforeach</li>     
                        <div class="row">                
                            <div class="col-md-12" align="right" >
                                <a class="btn btn-success mb-2"  href="{{ url('backoffice_asset/computer_7440_001_excel') }}" target="_blank" type="submit">
                                Excel
                                </a>                    
                            </div>      
                        </div>    
                        <table id="computer_7440_001" class="table table-bordered table-striped">
                            <thead>
                            <tr class="table-secondary">
                                <th class="text-center">รหัสกลุ่ม</th>
                                <th class="text-center">กลุ่ม</th>                               
                                <th class="text-center">รหัสครุภัณฑ์</th>
                                <th class="text-center">ชื่อครุภัณฑ์</th>
                                <th class="text-center">ยี่ห้อ</th>
                                <th class="text-center">รุ่น</th>
                                <th class="text-center">คุณลักษณะ</th>    
                                <th class="text-center">วันที่รับเข้า</th>   
                                <th class="text-center">ราคา</th>   
                                <th class="text-center">วิธีได้มา</th>     
                                <th class="text-center">งบที่ใช้</th>   
                                <th class="text-center">ประจำหน่วยงาน</th> 
                                <th class="text-center">ผู้รับผิดชอบ</th>                                                 
                            </thead>                          
                            @foreach($asset_7440_001 as $row)          
                            <tr>                          
                                <td align="left">{{ $row->SUP_FSN }}</td>
                                <td align="left">{{ $row->SUP_NAME }}</td>                                
                                <td align="left">{{ $row->ARTICLE_NUM }}</td>
                                <td align="left">{{ $row->ARTICLE_NAME }}</td>
                                <td align="left">{{ $row->BRAND_NAME }}</td>
                                <td align="left">{{ $row->MODEL_NAME }}</td>
                                <td align="left">{{ $row->ARTICLE_PROP }}</td>
                                <td align="center">{{ DateThai($row->RECEIVE_DATE) }}</td>
                                <td align="right">{{ number_format($row->PRICE_PER_UNIT,2) }}</td>
                                <td align="left">{{ $row->METHOD_NAME }}</td>
                                <td align="left">{{ $row->BUDGET_NAME }}</td>
                                <td align="left">{{ $row->HR_DEPARTMENT_SUB_SUB_NAME }}</td>
                                <td align="left">{{ $row->hr_name }}</td>
                            @endforeach 
                        </table> 
                    </div> 
                </div>        
            </div>
        </div>
    </div>
</div>
<br>
<div class="container-fluid">
    <div class="card">
    <div class="card-header bg-primary text-white">อุปกรณ์และส่วนเชื่อมต่อคอมพิวเตอร์ สถานะใช้งานปกติ</div>  
        <div class="card-body"> 
            <div style="overflow-x:auto;">          
                <div class="row">
                    <div class="col-md-12">  
                        <li>@foreach($switch_l2 as $item)อุปกรณ์กระจายสัญญาณ (L2 Switch) ขนาด 24 ช่อง จำนวน <strong>{{$item->sum}}</strong> เครื่อง @endforeach</li>
                        <li>@foreach($switch_l3 as $item)อุปกรณ์กระจายสัญญาณ (L3 Switch) ขนาด 24 ช่อง จำนวน <strong>{{$item->sum}}</strong> เครื่อง @endforeach</li>   
                        <li>@foreach($ap as $item)อุปกรณ์กระจายสัญญาณไร้สาย (Access Point) จำนวน <strong>{{$item->sum}}</strong> เครื่อง @endforeach</li> 
                        <li>@foreach($smart_card as $item)อุปกรณ์อ่านบัตรแบบอเนกประสงค์ (Smart Card Reader) จำนวน <strong>{{$item->sum}}</strong> เครื่อง @endforeach</li> 
                        <div class="row">                
                            <div class="col-md-12" align="right" >
                                <a class="btn btn-success mb-2"  href="{{ url('backoffice_asset/computer_7440_003_excel') }}" target="_blank" type="submit">
                                Excel
                                </a>                    
                            </div>      
                        </div>    
                        <table id="computer_7440_003" class="table table-bordered table-striped">
                            <thead>
                            <tr class="table-secondary">
                                <th class="text-center">รหัสกลุ่ม</th>
                                <th class="text-center">กลุ่ม</th>                               
                                <th class="text-center">รหัสครุภัณฑ์</th>
                                <th class="text-center">ชื่อครุภัณฑ์</th>
                                <th class="text-center">ยี่ห้อ</th>
                                <th class="text-center">รุ่น</th>
                                <th class="text-center">คุณลักษณะ</th>    
                                <th class="text-center">วันที่รับเข้า</th>   
                                <th class="text-center">ราคา</th>   
                                <th class="text-center">วิธีได้มา</th>     
                                <th class="text-center">งบที่ใช้</th>   
                                <th class="text-center">ประจำหน่วยงาน</th> 
                                <th class="text-center">ผู้รับผิดชอบ</th>                                                 
                            </thead>                          
                            @foreach($asset_7440_003 as $row)          
                            <tr>                          
                                <td align="left">{{ $row->SUP_FSN }}</td>
                                <td align="left">{{ $row->SUP_NAME }}</td>                                
                                <td align="left">{{ $row->ARTICLE_NUM }}</td>
                                <td align="left">{{ $row->ARTICLE_NAME }}</td>
                                <td align="left">{{ $row->BRAND_NAME }}</td>
                                <td align="left">{{ $row->MODEL_NAME }}</td>
                                <td align="left">{{ $row->ARTICLE_PROP }}</td>
                                <td align="center">{{ DateThai($row->RECEIVE_DATE) }}</td>
                                <td align="right">{{ number_format($row->PRICE_PER_UNIT,2) }}</td>
                                <td align="left">{{ $row->METHOD_NAME }}</td>
                                <td align="left">{{ $row->BUDGET_NAME }}</td>
                                <td align="left">{{ $row->HR_DEPARTMENT_SUB_SUB_NAME }}</td>
                                <td align="left">{{ $row->hr_name }}</td>
                            @endforeach 
                        </table> 
                    </div> 
                </div>        
            </div>
        </div>
    </div>
</div>
<br>
<div class="container-fluid">
    <div class="card">
    <div class="card-header bg-primary text-white">เครื่องพิมพ์ สถานะใช้งานปกติ</div>  
        <div class="card-body"> 
            <div style="overflow-x:auto;">          
                <div class="row">
                    <div class="col-md-12">    
                        <div class="row">                
                            <div class="col-md-12" align="right" >
                                <a class="btn btn-success mb-2"  href="{{ url('backoffice_asset/computer_7440_005_excel') }}" target="_blank" type="submit">
                                Excel
                                </a>                    
                            </div>      
                        </div>    
                        <table id="computer_7440_005" class="table table-bordered table-striped">
                            <thead>
                            <tr class="table-secondary">
                                <th class="text-center">รหัสกลุ่ม</th>
                                <th class="text-center">กลุ่ม</th>                               
                                <th class="text-center">รหัสครุภัณฑ์</th>
                                <th class="text-center">ชื่อครุภัณฑ์</th>
                                <th class="text-center">ยี่ห้อ</th>
                                <th class="text-center">รุ่น</th>
                                <th class="text-center">คุณลักษณะ</th>    
                                <th class="text-center">วันที่รับเข้า</th>   
                                <th class="text-center">ราคา</th>   
                                <th class="text-center">วิธีได้มา</th>     
                                <th class="text-center">งบที่ใช้</th>   
                                <th class="text-center">ประจำหน่วยงาน</th> 
                                <th class="text-center">ผู้รับผิดชอบ</th>                                                 
                            </thead>                          
                            @foreach($asset_7440_005 as $row)          
                            <tr>                          
                                <td align="left">{{ $row->SUP_FSN }}</td>
                                <td align="left">{{ $row->SUP_NAME }}</td>                                
                                <td align="left">{{ $row->ARTICLE_NUM }}</td>
                                <td align="left">{{ $row->ARTICLE_NAME }}</td>
                                <td align="left">{{ $row->BRAND_NAME }}</td>
                                <td align="left">{{ $row->MODEL_NAME }}</td>
                                <td align="left">{{ $row->ARTICLE_PROP }}</td>
                                <td align="center">{{ DateThai($row->RECEIVE_DATE) }}</td>
                                <td align="right">{{ number_format($row->PRICE_PER_UNIT,2) }}</td>
                                <td align="left">{{ $row->METHOD_NAME }}</td>
                                <td align="left">{{ $row->BUDGET_NAME }}</td>
                                <td align="left">{{ $row->HR_DEPARTMENT_SUB_SUB_NAME }}</td>
                                <td align="left">{{ $row->hr_name }}</td>
                            @endforeach 
                        </table> 
                    </div> 
                </div>        
            </div>
        </div>
    </div>
</div>
<br>
<div class="container-fluid">
    <div class="card">
    <div class="card-header bg-primary text-white">จอแสดงภาพ สถานะใช้งานปกติ</div>  
        <div class="card-body"> 
            <div style="overflow-x:auto;">          
                <div class="row">
                    <div class="col-md-12">    
                        <div class="row">                
                            <div class="col-md-12" align="right" >
                                <a class="btn btn-success mb-2"  href="{{ url('backoffice_asset/computer_7440_006_excel') }}" target="_blank" type="submit">
                                Excel
                                </a>                    
                            </div>      
                        </div>    
                        <table id="computer_7440_006" class="table table-bordered table-striped">
                            <thead>
                            <tr class="table-secondary">
                                <th class="text-center">รหัสกลุ่ม</th>
                                <th class="text-center">กลุ่ม</th>                               
                                <th class="text-center">รหัสครุภัณฑ์</th>
                                <th class="text-center">ชื่อครุภัณฑ์</th>
                                <th class="text-center">ยี่ห้อ</th>
                                <th class="text-center">รุ่น</th>
                                <th class="text-center">คุณลักษณะ</th>    
                                <th class="text-center">วันที่รับเข้า</th>   
                                <th class="text-center">ราคา</th>   
                                <th class="text-center">วิธีได้มา</th>     
                                <th class="text-center">งบที่ใช้</th>   
                                <th class="text-center">ประจำหน่วยงาน</th> 
                                <th class="text-center">ผู้รับผิดชอบ</th>                                                 
                            </thead>                          
                            @foreach($asset_7440_006 as $row)          
                            <tr>                          
                                <td align="left">{{ $row->SUP_FSN }}</td>
                                <td align="left">{{ $row->SUP_NAME }}</td>                                
                                <td align="left">{{ $row->ARTICLE_NUM }}</td>
                                <td align="left">{{ $row->ARTICLE_NAME }}</td>
                                <td align="left">{{ $row->BRAND_NAME }}</td>
                                <td align="left">{{ $row->MODEL_NAME }}</td>
                                <td align="left">{{ $row->ARTICLE_PROP }}</td>
                                <td align="center">{{ DateThai($row->RECEIVE_DATE) }}</td>
                                <td align="right">{{ number_format($row->PRICE_PER_UNIT,2) }}</td>
                                <td align="left">{{ $row->METHOD_NAME }}</td>
                                <td align="left">{{ $row->BUDGET_NAME }}</td>
                                <td align="left">{{ $row->HR_DEPARTMENT_SUB_SUB_NAME }}</td>
                                <td align="left">{{ $row->hr_name }}</td>
                            @endforeach 
                        </table> 
                    </div> 
                </div>        
            </div>
        </div>
    </div>
</div>
<br>
<div class="container-fluid">
    <div class="card">
    <div class="card-header bg-primary text-white">สแกนเนอร์ สถานะใช้งานปกติ</div>  
        <div class="card-body"> 
            <div style="overflow-x:auto;">          
                <div class="row">
                    <div class="col-md-12">    
                        <div class="row">                
                            <div class="col-md-12" align="right" >
                                <a class="btn btn-success mb-2"  href="{{ url('backoffice_asset/computer_7440_007_excel') }}" target="_blank" type="submit">
                                Excel
                                </a>                    
                            </div>      
                        </div>    
                        <table id="computer_7440_007" class="table table-bordered table-striped">
                            <thead>
                            <tr class="table-secondary">
                                <th class="text-center">รหัสกลุ่ม</th>
                                <th class="text-center">กลุ่ม</th>                               
                                <th class="text-center">รหัสครุภัณฑ์</th>
                                <th class="text-center">ชื่อครุภัณฑ์</th>
                                <th class="text-center">ยี่ห้อ</th>
                                <th class="text-center">รุ่น</th>
                                <th class="text-center">คุณลักษณะ</th>    
                                <th class="text-center">วันที่รับเข้า</th>   
                                <th class="text-center">ราคา</th>   
                                <th class="text-center">วิธีได้มา</th>     
                                <th class="text-center">งบที่ใช้</th>   
                                <th class="text-center">ประจำหน่วยงาน</th> 
                                <th class="text-center">ผู้รับผิดชอบ</th>                                                 
                            </thead>                          
                            @foreach($asset_7440_007 as $row)          
                            <tr>                          
                                <td align="left">{{ $row->SUP_FSN }}</td>
                                <td align="left">{{ $row->SUP_NAME }}</td>                                
                                <td align="left">{{ $row->ARTICLE_NUM }}</td>
                                <td align="left">{{ $row->ARTICLE_NAME }}</td>
                                <td align="left">{{ $row->BRAND_NAME }}</td>
                                <td align="left">{{ $row->MODEL_NAME }}</td>
                                <td align="left">{{ $row->ARTICLE_PROP }}</td>
                                <td align="center">{{ DateThai($row->RECEIVE_DATE) }}</td>
                                <td align="right">{{ number_format($row->PRICE_PER_UNIT,2) }}</td>
                                <td align="left">{{ $row->METHOD_NAME }}</td>
                                <td align="left">{{ $row->BUDGET_NAME }}</td>
                                <td align="left">{{ $row->HR_DEPARTMENT_SUB_SUB_NAME }}</td>
                                <td align="left">{{ $row->hr_name }}</td>
                            @endforeach 
                        </table> 
                    </div> 
                </div>        
            </div>
        </div>
    </div>
</div>
<br>
<div class="container-fluid">
    <div class="card">
    <div class="card-header bg-primary text-white">เครื่องสำรองกระแสไฟฟ้า สถานะใช้งานปกติ</div>  
        <div class="card-body"> 
            <div style="overflow-x:auto;">          
                <div class="row">
                    <div class="col-md-12">    
                        <div class="row">                
                            <div class="col-md-12" align="right" >
                                <a class="btn btn-success mb-2"  href="{{ url('backoffice_asset/computer_7440_009_excel') }}" target="_blank" type="submit">
                                Excel
                                </a>                    
                            </div>      
                        </div>    
                        <table id="computer_7440_009" class="table table-bordered table-striped">
                            <thead>
                            <tr class="table-secondary">
                                <th class="text-center">รหัสกลุ่ม</th>
                                <th class="text-center">กลุ่ม</th>                               
                                <th class="text-center">รหัสครุภัณฑ์</th>
                                <th class="text-center">ชื่อครุภัณฑ์</th>
                                <th class="text-center">ยี่ห้อ</th>
                                <th class="text-center">รุ่น</th>
                                <th class="text-center">คุณลักษณะ</th>    
                                <th class="text-center">วันที่รับเข้า</th>   
                                <th class="text-center">ราคา</th>   
                                <th class="text-center">วิธีได้มา</th>     
                                <th class="text-center">งบที่ใช้</th>   
                                <th class="text-center">ประจำหน่วยงาน</th> 
                                <th class="text-center">ผู้รับผิดชอบ</th>                                                 
                            </thead>                          
                            @foreach($asset_7440_009 as $row)          
                            <tr>                          
                                <td align="left">{{ $row->SUP_FSN }}</td>
                                <td align="left">{{ $row->SUP_NAME }}</td>                                
                                <td align="left">{{ $row->ARTICLE_NUM }}</td>
                                <td align="left">{{ $row->ARTICLE_NAME }}</td>
                                <td align="left">{{ $row->BRAND_NAME }}</td>
                                <td align="left">{{ $row->MODEL_NAME }}</td>
                                <td align="left">{{ $row->ARTICLE_PROP }}</td>
                                <td align="center">{{ DateThai($row->RECEIVE_DATE) }}</td>
                                <td align="right">{{ number_format($row->PRICE_PER_UNIT,2) }}</td>
                                <td align="left">{{ $row->METHOD_NAME }}</td>
                                <td align="left">{{ $row->BUDGET_NAME }}</td>
                                <td align="left">{{ $row->HR_DEPARTMENT_SUB_SUB_NAME }}</td>
                                <td align="left">{{ $row->hr_name }}</td>
                            @endforeach 
                        </table> 
                    </div> 
                </div>        
            </div>
        </div>
    </div>
</div>
@endsection
<script type="text/javascript" language="javascript" src="https://code.jquery.com/jquery-3.5.1.js"></script>
<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/1.13.1/js/dataTables.bootstrap5.min.js"></script>
<script type="text/javascript" class="init">
    $(document).ready(function () {
        $('#office').DataTable();
    });
</script>
<script type="text/javascript" class="init">
    $(document).ready(function () {
        $('#computer_7440_001').DataTable();
    });
</script>
<script type="text/javascript" class="init">
    $(document).ready(function () {
        $('#computer_7440_003').DataTable();
    });
</script>
<script type="text/javascript" class="init">
    $(document).ready(function () {
        $('#computer_7440_005').DataTable();
    });
</script>
<script type="text/javascript" class="init">
    $(document).ready(function () {
        $('#computer_7440_006').DataTable();
    });
</script>
<script type="text/javascript" class="init">
    $(document).ready(function () {
        $('#computer_7440_007').DataTable();
    });
</script>
<script type="text/javascript" class="init">
    $(document).ready(function () {
        $('#computer_7440_009').DataTable();
    });
</script>


