@extends('layouts.app')

@section('content')
<div class="container-fluid">
  <div class="row justify-content-center">  
    <div class="col-md-12"> 
      <div class="card border-primary">
        <div class="card-header bg-primary text-white">นำเข้าข้อมูล Statement</div>
        <div class="card-body">
          <table class="table table-hover">
            <thead>
              <tr class="table-primary">
                  <th class="text-left text-primary">Statement เบิกจ่ายตรงกรมบัญชีกลาง OFC | กทม.BKK | ขสมก.BMT</th>            
              </tr>  
            </thead> 
            <tbody> 
              <tr>
                <td><a href="{{ url('finance_stm/stm_ofc') }}" target="_blank"><li>Statement เบิกจ่ายตรงกรมบัญชีกลาง OFC | กทม.BKK | ขสมก.BMT [OP-IP]</li></a></td>
              </tr>
              <tr>
                <td><a href="{{ url('finance_stm/stm_ofc_detail') }}" target="_blank"><li>Statement เบิกจ่ายตรงกรมบัญชีกลาง OFC | กทม.BKK | ขสมก.BMT [OP-IP] รายละเอียด</li></a></td>
              </tr>
              <tr>
                <td><a href="{{ url('finance_stm/stm_ofc_kidney') }}" target="_blank"><li>Statement เบิกจ่ายตรงกรมบัญชีกลาง OFC [ฟอกไต]</li></a></td>
              </tr>
              <tr>
                <td><a href="{{ url('finance_stm/stm_ofc_kidneydetail') }}" target="_blank"><li>Statement เบิกจ่ายตรงกรมบัญชีกลาง OFC [ฟอกไต] รายละเอียด</li></a></td>
              </tr>
            </tbody>
            <thead>
              <tr class="table-primary">
                  <th class="text-left text-primary">Statement เบิกจ่ายตรง อปท.LGO</th>            
              </tr>  
            </thead> 
            <tbody>
              <tr>
                <td><a href="{{ url('finance_stm/stm_lgo') }}" target="_blank"><li>Statement เบิกจ่ายตรง อปท.LGO [OP-IP]</li></a></td>
              </tr>
              <tr>
                <td><a href="{{ url('finance_stm/stm_lgo_detail') }}" target="_blank"><li>Statement เบิกจ่ายตรง อปท.LGO [OP-IP] รายละเอียด</li></a></td>
              </tr>
              <tr>
                <td><a href="{{ url('finance_stm/stm_lgo_kidney') }}" target="_blank"><li>Statement เบิกจ่ายตรง อปท.LGO [ฟอกไต HD]</li></a></td>
              </tr>
              <tr>
                <td><a href="{{ url('finance_stm/stm_lgo_kidneydetail') }}" target="_blank"><li>Statement เบิกจ่ายตรง อปท.LGO [ฟอกไต HD] รายละเอียด</li></a></td>
              </tr>
            </tbody>
            <thead>
              <tr class="table-primary">
                  <th class="text-left text-primary">Statement ประกันสุขภาพ UCS</th>            
              </tr>  
            </thead> 
            <tbody>
              <tr>
                <td><a href="{{ url('finance_stm/stm_ucs') }}" target="_blank"><li>Statement ประกันสุขภาพ UCS [OP-IP]</li></a></td>
              </tr>
              <tr>
                <td><a href="{{ url('finance_stm/stm_ucs_detail') }}" target="_blank"><li>Statement ประกันสุขภาพ UCS [OP-IP] รายละเอียด</li></a></td>
              </tr>
              <tr>
                <td><a href="{{ url('finance_stm/stm_ucs_kidney') }}" target="_blank"><li>Statement ประกันสุขภาพ UCS [ฟอกไต HD]</li></a></td>
              </tr>
              <tr>
                <td><a href="{{ url('finance_stm/stm_ucs_kidneydetail') }}" target="_blank"><li>Statement ประกันสุขภาพ UCS [ฟอกไต HD] รายละเอียด</li></a></td>
              </tr>
            </tbody>
            <thead>
              <tr class="table-primary">
                  <th class="text-left text-primary">Statement ประกันสังคม SSS</th>            
              </tr>  
            </thead> 
            <tbody>
              <tr>
                <td><a href="{{ url('finance_stm/stm_sss_kidney') }}" target="_blank"><li>Statement ประกันสังคม SSS [ฟอกไต HD]</li></a></td>
              </tr>
              <tr>
                <td><a href="{{ url('finance_stm/stm_sss_kidneydetail') }}" target="_blank"><li>Statement ประกันสังคม SSS [ฟอกไต HD] รายละเอียด</li></a></td>
              </tr>
            </tbody>
          </table>
      </div> 
    </div> 
  </div> 
</div> 

@endsection

