@extends('layouts.app')

@section('content')
<div class="container-fluid">
<!-- Row -->
  <div class="row justify-content-center">   
    <!-- Column left -->
    <div class="col-md-6"> 
      <div class="card border-primary">
        <div class="card-header bg-primary text-white">ประกันสุขภาพ UCS,STP</div>
        <div class="card-body">
          <table class="table table-hover">
            <thead>
              <tr class="table-primary">
                  <th class="text-left text-primary">UCS-IP</th>            
              </tr>  
            </thead> 
            <tbody>  
              <tr>
                <td><a href="{{ url('finance_claim/ucs_claim_ipd') }}" target="_blank"><li>UC-IP ผู้ป่วยใน [ส่ง FDH]</li></a></td>
              </tr>
            </tbody>
            <thead>
              <tr class="table-primary">
                  <th class="text-left text-primary">UCS-OP บริการเฉพาะ CR</th>            
              </tr>  
            </thead> 
            <tbody>  
              <tr>
                <td><a href="{{ url('finance_claim/ucs_claim_kidney') }}" target="_blank"><li>ฟอกไต HD</li></a></td>
              </tr>   
              <tr>
                <td><a href="{{ url('finance_claim/ucs_claim_opanywhere') }}" target="_blank"><li>OP Anywhere [ส่ง FDH]</li></a></td>
              </tr> 
              <tr>
                <td><a href="{{ url('finance_claim/ucs_claim_instrument') }}" target="_blank"><li>Instrument [ส่ง FDH]</li></a></td>
              </tr>
              <tr>
                <td><a href="{{ url('finance_claim/ucs_claim_telehealth') }}" target="_blank"><li>Telehealth / Telemedicine [ส่ง FDH]</li></a></td>
              </tr> 
              <tr>
                <td><a href="{{ url('finance_claim/ucs_claim_rider') }}" target="_blank"><li>จัดส่งยาทางไปรษณีย์ [ส่ง FDH]</li></a></td>
              </tr>  
              <tr>
                <td><a href="{{ url('finance_claim/ucs_claim_palliative') }}" target="_blank"><li>Palliative Care [ส่ง FDH]</li></a></td>
              </tr> 
              <tr>
                <td><a href="{{ url('finance_claim/ucs_claim_t1dm_gdm_pdm') }}" target="_blank"><li>บริการในกลุ่ม T1DM/GDM/PDM [ส่ง FDH]</li></a></td>
              </tr> 
              <tr>
                <td><a href="{{ url('finance_claim/ucs_claim_drug_herb') }}" target="_blank"><li>ยาสมุนไพร 9 รายการ [ส่ง FDH]</li></a></td>
              </tr> 
                            <tr>
                <td><a href="{{ url('finance_claim/ucs_claim_drug_herb32') }}" target="_blank"><li>ยาสมุนไพร 32 รายการ [ส่ง FDH]</li></a></td>
              </tr> 
              <tr>
                <td><a href="{{ url('finance_claim/ucs_claim_drug_morphine') }}" target="_blank"><li>ยา Morphine [ส่ง FDH]</li></a></td>
              </tr> 
              <tr>
                <td><a href="{{ url('finance_claim/ucs_claim_drug_clopidogrel') }}" target="_blank"><li>ยา Clopidogrel [ส่ง FDH]</li></a></td>
              </tr> 
              <tr>
                <td><a href="{{ url('finance_claim/ucs_claim_drug_sk') }}" target="_blank"><li>ยา StreptoKINASE [ส่ง FDH]</li></a></td>
              </tr> 
            </tbody>
            <thead>
              <tr class="table-primary">
                  <th class="text-left text-primary">UCS-PP FeeSchedule</th>            
              </tr>  
            </thead> 
            <tbody>   
              <tr>
                <td><a href="{{ url('finance_claim/ucs_ppfs_2') }}" target="_blank"><li>PPFS_บริการฝากครรภ์ ANC [ส่ง FDH]</li></a></td>
              </tr>
              <tr>
                <td><a href="{{ url('finance_claim/ucs_ppfs_7') }}" target="_blank"><li>PPFS_การตรวจหลังคลอด [ส่ง FDH]</li></a></td>
              </tr>
              <tr>
                <td><a href="{{ url('finance_claim/ucs_ppfs_8') }}" target="_blank"><li>PPFS_การทดสอบการตั้งครรภ์ [ส่ง FDH]</li></a></td>
              </tr>
              <tr>
                <td><a href="{{ url('finance_claim/ucs_ppfs_9') }}" target="_blank"><li>PPFS_บริการวางแผนครอบครัวของการป้องกันการตั้งครรภ์ไม่พึงประสงค์ [ส่ง FDH]</li></a></td>
              </tr>
              <tr>
                <td><a href="{{ url('finance_claim/ucs_ppfs_14') }}" target="_blank"><li>PPFS_บริการคัดกรองและประเมินปัจจัยเสี่ยงต่อสุขภาพกาย/สุขภาพจิต [ส่ง FDH]</li></a></td>
              </tr> 
              <tr>
                <td><a href="{{ url('finance_claim/ucs_ppfs_17') }}" target="_blank"><li>PPFS_บริการคัดกรองโลหิตจางจากการขาดธาตุเหล็ก [ส่ง FDH]</li></a></td>
              </tr> 
              <tr>
                <td><a href="{{ url('finance_claim/ucs_ppfs_18') }}" target="_blank"><li>PPFS_บริการยาเม็ดเสริมธาตุเหล็ก [ส่ง FDH]</li></a></td>
              </tr> 
              <tr>
                <td><a href="{{ url('finance_claim/ucs_ppfs_20') }}" target="_blank"><li>PPFS_บริการเคลือบฟลูออไรด์ กลุ่มสี่ยง [ส่ง FDH]</li></a></td>
              </tr> 
              <tr>
                <td><a href="{{ url('finance_claim/ucs_ppfs_21') }}" target="_blank"><li>PPFS_บริการตรวจคัดกรองมะเร็งลำไส้ใหญ่และลำไส้ตรง Fit test [ส่ง FDH]</li></a></td>
              </tr>          
            </tbody>
            <thead>
              <tr class="table-primary">
                  <th class="text-left text-primary">STP-บุคคลที่มีปัญหาสถานะและสิทธิ </th>            
              </tr>  
            </thead> 
            <tbody>  
              <tr>
                <td><a href="{{ url('finance_claim/stp_claim_opd') }}" target="_blank"><li>บุคคลที่มีปัญหาสถานะและสิทธิ STP ผู้ป่วยนอก [ส่ง FDH]</li></a></td>
              </tr> 
              <tr>
                <td><a href="{{ url('finance_claim/stp_claim_ipd') }}" target="_blank"><li>บุคคลที่มีปัญหาสถานะและสิทธิ STP ผู้ป่วยใน [ส่ง FDH]</li></a></td>
              </tr>
            </tbody>
          </table>
        </div> 
      </div>  
      <br>     
    </div>   
    <div class="col-md-6">
      <div class="card border-primary">
        <div class="card-header bg-primary text-white">เบิกจ่ายตรงกรมบัญชีกลาง OFC</div>
        <div class="card-body">
          <table class="table table-hover">
            <tbody>  
              <tr>
                <td><a href="{{ url('finance_claim/ofc_claim_opd') }}" target="_blank"><li>เบิกจ่ายตรงกรมบัญชีกลาง OFC ผู้ป่วยนอก [ส่ง E-claim]</li></a></td>
              </tr> 
              <tr>
                <td><a href="{{ url('finance_claim/ofc_claim_ipd') }}" target="_blank"><li>เบิกจ่ายตรงกรมบัญชีกลาง OFC ผู้ป่วยใน [ส่ง E-claim]</li></a></td>
              </tr>
            </tbody>
          </table>
        </div>        
      </div> 
      <br>
      <div class="card border-primary">
        <div class="card-header bg-primary text-white">เบิกจ่ายตรง อปท.รูปแบบพิเศษ กทม.BKK</div>
        <div class="card-body">
          <table class="table table-hover">
            <tbody>  
              <tr>
                <td><a href="{{ url('finance_claim/bkk_claim_opd') }}" target="_blank"><li>เบิกจ่ายตรง อปท.รูปแบบพิเศษ กทม.BKK ผู้ป่วยนอก [ส่ง E-claim]</li></a></td>
              </tr> 
              <tr>
                <td><a href="{{ url('finance_claim/bkk_claim_ipd') }}" target="_blank"><li>เบิกจ่ายตรง อปท.รูปแบบพิเศษ กทม.BKK ผู้ป่วยใน [ส่ง E-claim]</li></a></td>
              </tr>              
            </tbody>
          </table>
        </div>         
      </div> 
      <br>    
      <div class="card border-primary">
        <div class="card-header bg-primary text-white">เบิกจ่ายตรง อปท.รูปแบบพิเศษ ขสมก.BMT</div>
        <div class="card-body">
          <table class="table table-hover">
            <tbody>  
              <tr>
                <td><a href="{{ url('finance_claim/bmt_claim_opd') }}" target="_blank"><li>ขนส่งมวลชนกรุงเทพ ขสมก.BMT ผู้ป่วยนอก [ส่ง E-claim]</li></a></td>
              </tr> 
              <tr>
                <td><a href="{{ url('finance_claim/bmt_claim_ipd') }}" target="_blank"><li>ขนส่งมวลชนกรุงเทพ ขสมก.BMT ผู้ป่วยใน [ส่ง E-claim]</li></a></td>
              </tr>
            </tbody>
          </table>
        </div>         
      </div> 
      <br>    
      <div class="card border-primary">
        <div class="card-header bg-primary text-white">เบิกจ่ายตรง อปท.LGO</div>
        <div class="card-body">
          <table class="table table-hover">
            <tbody>  
              <tr>
                <td><a href="{{ url('finance_claim/lgo_claim_opd') }}" target="_blank"><li>เบิกจ่ายตรง อปท.LGO ผู้ป่วยนอก [ส่ง E-claim]</li></a></td>
              </tr> 
              <tr>
                <td><a href="{{ url('finance_claim/lgo_claim_ipd') }}" target="_blank"><li>เบิกจ่ายตรง อปท.LGO ผู้ป่วยใน [ส่ง E-claim]</li></a></td>
              </tr>
            </tbody>
          </table>
        </div>        
      </div> 
      <br>   
      <div class="card border-primary">
        <div class="card-header bg-primary text-white">ประกันสังคม SSS,SSI</div>
        <div class="card-body">
          <table class="table table-hover">
            <tbody>  
              <tr>
                <td><a href="{{ url('finance_claim/sss_claim_kidney') }}" target="_blank"><li>ประกันสังคม ฟอกไต HD</li></a></td>
              </tr> 
            </tbody>
          </table>
        </div>        
      </div> 
      <br>               
    </div>
  </div>
@endsection

