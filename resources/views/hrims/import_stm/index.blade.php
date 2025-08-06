@extends('layouts.hrims')

@section('content')
<div class="container-fluid">
  <div class="row">
    <div class="col-sm-12"> 
        <div class="alert alert-success text-primary" role="alert"><strong>นำเข้าข้อมูล Statement</strong></div>          
    </div>
  </div>

  <div class="row justify-content-center">  
    <div class="col-md-12">
      <div class="card border-success">        
        <div class="card-body">    
          <table class="table table-hover">
            <thead>
              <tr class="table-primary">
                  <th class="text-left text-primary">Statement เบิกจ่ายตรงกรมบัญชีกลาง OFC | กทม.BKK | ขสมก.BMT</th>            
              </tr>  
            </thead> 
            <tbody> 
              <tr>
                <td><a href="{{ url('hrims/import_stm/ofc') }}" target="_blank"><li>Statement เบิกจ่ายตรงกรมบัญชีกลาง OFC | กทม.BKK | ขสมก.BMT [OP-IP]</li></a></td>
              </tr>
              <tr>
                <td><a href="{{ url('hrims/import_stm/ofc_detail') }}" target="_blank"><li>Statement เบิกจ่ายตรงกรมบัญชีกลาง OFC | กทม.BKK | ขสมก.BMT [OP-IP] รายละเอียด</li></a></td>
              </tr>
              <tr>
                <td><a href="{{ url('hrims/import_stm/ofc_kidney') }}" target="_blank"><li>Statement เบิกจ่ายตรงกรมบัญชีกลาง OFC [ฟอกไต]</li></a></td>
              </tr>
              <tr>
                <td><a href="{{ url('hrims/import_stm/ofc_kidneydetail') }}" target="_blank"><li>Statement เบิกจ่ายตรงกรมบัญชีกลาง OFC [ฟอกไต] รายละเอียด</li></a></td>
              </tr>
            </tbody>
            <thead>
              <tr class="table-primary">
                  <th class="text-left text-primary">Statement เบิกจ่ายตรง อปท.LGO</th>            
              </tr>  
            </thead> 
            <tbody>
              <tr>
                <td><a href="{{ url('hrims/import_stm/lgo') }}" target="_blank"><li>Statement เบิกจ่ายตรง อปท.LGO [OP-IP]</li></a></td>
              </tr>
              <tr>
                <td><a href="{{ url('hrims/import_stm/lgo_detail') }}" target="_blank"><li>Statement เบิกจ่ายตรง อปท.LGO [OP-IP] รายละเอียด</li></a></td>
              </tr>
              <tr>
                <td><a href="{{ url('hrims/import_stm/lgo_kidney') }}" target="_blank"><li>Statement เบิกจ่ายตรง อปท.LGO [ฟอกไต HD]</li></a></td>
              </tr>
              <tr>
                <td><a href="{{ url('hrims/import_stm/lgo_kidneydetail') }}" target="_blank"><li>Statement เบิกจ่ายตรง อปท.LGO [ฟอกไต HD] รายละเอียด</li></a></td>
              </tr>
            </tbody>
            <thead>
              <tr class="table-primary">
                  <th class="text-left text-primary">Statement ประกันสุขภาพ UCS</th>            
              </tr>  
            </thead> 
            <tbody>
              <tr>
                <td><a href="{{ url('hrims/import_stm/ucs') }}" target="_blank"><li>Statement ประกันสุขภาพ UCS [OP-IP]</li></a></td>
              </tr>
              <tr>
                <td><a href="{{ url('hrims/import_stm/ucs_detail') }}" target="_blank"><li>Statement ประกันสุขภาพ UCS [OP-IP] รายละเอียด</li></a></td>
              </tr>
              <tr>
                <td><a href="{{ url('hrims/import_stm/ucs_kidney') }}" target="_blank"><li>Statement ประกันสุขภาพ UCS [ฟอกไต HD]</li></a></td>
              </tr>
              <tr>
                <td><a href="{{ url('hrims/import_stm/ucs_kidneydetail') }}" target="_blank"><li>Statement ประกันสุขภาพ UCS [ฟอกไต HD] รายละเอียด</li></a></td>
              </tr>
            </tbody>
            <thead>
              <tr class="table-primary">
                  <th class="text-left text-primary">Statement ประกันสังคม SSS</th>            
              </tr>  
            </thead> 
            <tbody>
              <tr>
                <td><a href="{{ url('hrims/import_stm/sss_kidney') }}" target="_blank"><li>Statement ประกันสังคม SSS [ฟอกไต HD]</li></a></td>
              </tr>
              <tr>
                <td><a href="{{ url('hrims/import_stm/sss_kidneydetail') }}" target="_blank"><li>Statement ประกันสังคม SSS [ฟอกไต HD] รายละเอียด</li></a></td>
              </tr>
            </tbody>
          </table>
      </div> 
    </div> 
  </div> 
</div> 

@endsection

