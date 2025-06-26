<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title >ระบบตรวจสอบความพร้อมของเครื่องมือแพทย์และอุปกรณ์ฉุกเฉิน</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <!-- <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet"> -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body>
<!-- <div class="container"> -->
    <div class="card">        
        <h5 class="alert alert-primary text-center"><strong>ระบบตรวจสอบความพร้อมของเครื่องมือแพทย์และอุปกรณ์ฉุกเฉิน<br>
        @if($depart =='er') งานอุบัติเหตุ-ฉุกเฉิน ER @elseif($depart =='lr') ห้องคลอด LR @elseif($depart =='or') ห้องผ่าตัด OR 
        @elseif($depart =='hd') ศูนย์ฟอกไต HD รพ. @elseif($depart =='ipd') งานผู้ป่วยในสามัญ
        @elseif($depart =='vip') งานผู้ป่วยใน VIP @endif<br>วันที่ {{ DatetimeThai(date('Y-m-d H:i:s')) }}</strong></h5> 
        <div class="card-body">
            <div class="row mb-3"> 
                @if ($message = Session::get('success'))
                <div class="alert alert-success text-center">
                <h5><strong>{{ $message }}</strong></h5>
                </div>
                @endif
            </div>
            <form action="{{ route('check_asset_save') }}" method="POST" enctype="multipart/form-data">
                @csrf 
                <input type="hidden" id="depart" name="depart" value="{{ $depart }}">
                <div class="mb-3">
                    <label class="form-label"><strong>ผู้ตรวจสอบ</strong></label>
                    <input type="text" name="hr_check" id="hr_check" class="form-control" placeholder="ชื่อ-สกุล" required>
                </div>      
                <div class="mb-3">
                    <label class="form-label"><strong>1.สถานะ Defibrillator</strong></label>  
                    <select required class="form-select my-1" name="asset1">                                                             
                        <option value="พร้อมใช้">พร้อมใช้</option> 
                        <option value="other">ชำรุด</option>  
                        <option value="ไม่เพียงพอ">ไม่เพียงพอ</option>                        
                        <option value="ไม่มี">ไม่มี</option>                           
                    </select>              
                </div>      
                <div class="mb-3">
                    <label class="form-label"><strong>2.Laryngoscope</strong></label>
                    <select required class="form-select my-1" name="asset2">                                                             
                        <option value="พร้อมใช้">พร้อมใช้</option> 
                        <option value="other">ชำรุด</option>  
                        <option value="ไม่เพียงพอ">ไม่เพียงพอ</option>                        
                        <option value="ไม่มี">ไม่มี</option>                           
                    </select>                    
                </div>    
                <div class="mb-3">
                    <label class="form-label"><strong>3.Ambu bag</strong></label>
                    <select required class="form-select my-1" name="asset3">                                                             
                        <option value="พร้อมใช้">พร้อมใช้</option> 
                        <option value="other">ชำรุด</option>  
                        <option value="ไม่เพียงพอ">ไม่เพียงพอ</option>                        
                        <option value="ไม่มี">ไม่มี</option>                           
                    </select>                   
                </div>    
                <div class="mb-3">
                    <label class="form-label"><strong>4.ETT+อุปกรณ์Away</strong></label>
                    <select required class="form-select my-1" name="asset4">                                                             
                        <option value="พร้อมใช้">พร้อมใช้</option> 
                        <option value="other">ชำรุด</option>  
                        <option value="ไม่เพียงพอ">ไม่เพียงพอ</option>                        
                        <option value="ไม่มี">ไม่มี</option>                           
                    </select>               
                </div>    
                <div class="mb-3">
                    <label class="form-label"><strong>5.Oxygen+อุปกรณ์</strong></label>
                    <select required class="form-select my-1" name="asset5">                                                             
                        <option value="พร้อมใช้">พร้อมใช้</option> 
                        <option value="other">ชำรุด</option>  
                        <option value="ไม่เพียงพอ">ไม่เพียงพอ</option>                        
                        <option value="ไม่มี">ไม่มี</option>                           
                    </select>                
                </div>    
                <div class="mb-3">
                    <label class="form-label"><strong>6.Auto CPR</strong></label>
                    <select required class="form-select my-1" name="asset6">                                                             
                        <option value="พร้อมใช้">พร้อมใช้</option> 
                        <option value="other">ชำรุด</option>  
                        <option value="ไม่เพียงพอ">ไม่เพียงพอ</option>                        
                        <option value="ไม่มี">ไม่มี</option>                           
                    </select>                
                </div>    
                <div class="mb-3">
                    <label class="form-label"><strong>7.กล่องยาฉุกเฉิน</strong></label>
                    <select required class="form-select my-1" name="asset7">                                                             
                        <option value="พร้อมใช้">พร้อมใช้</option> 
                        <option value="other">ชำรุด</option>  
                        <option value="ไม่เพียงพอ">ไม่เพียงพอ</option>                        
                        <option value="ไม่มี">ไม่มี</option>                           
                    </select>                    
                </div>    
                <div class="mb-3">
                    <label class="form-label"><strong>8.Ventilator</strong></label>
                    <select required class="form-select my-1" name="asset8">                                                             
                        <option value="พร้อมใช้">พร้อมใช้</option> 
                        <option value="other">ชำรุด</option>  
                        <option value="ไม่เพียงพอ">ไม่เพียงพอ</option>                        
                        <option value="ไม่มี">ไม่มี</option>                           
                    </select>                     
                </div>    
                <div class="mb-3">
                    <label class="form-label"><strong>9.Ekg 12 lead</strong></label>
                    <select required class="form-select my-1" name="asset9">                                                             
                        <option value="พร้อมใช้">พร้อมใช้</option> 
                        <option value="other">ชำรุด</option>  
                        <option value="ไม่เพียงพอ">ไม่เพียงพอ</option>                        
                        <option value="ไม่มี">ไม่มี</option>                           
                    </select>              
                </div>   
                <div class="mb-3">
                    <label class="form-label"><strong>10.Suction+อุปกรณ์</strong></label>
                    <select required class="form-select my-1" name="asset10">                                                             
                        <option value="พร้อมใช้">พร้อมใช้</option> 
                        <option value="other">ชำรุด</option>  
                        <option value="ไม่เพียงพอ">ไม่เพียงพอ</option>                        
                        <option value="ไม่มี">ไม่มี</option>                           
                    </select>              
                </div>  
                <div class="mb-3">
                    <label class="form-label"><strong>11.เครื่องดมยา</strong></label>
                    <select required class="form-select my-1" name="asset11">                                                             
                        <option value="พร้อมใช้">พร้อมใช้</option> 
                        <option value="other">ชำรุด</option>  
                        <option value="ไม่เพียงพอ">ไม่เพียงพอ</option>                        
                        <option value="ไม่มี">ไม่มี</option>                           
                    </select>              
                </div>  
                <div class="mb-3">
                    <label class="form-label"><strong>12.เครื่อง NST</strong></label>
                    <select required class="form-select my-1" name="asset12">                                                             
                        <option value="พร้อมใช้">พร้อมใช้</option> 
                        <option value="other">ชำรุด</option>  
                        <option value="ไม่เพียงพอ">ไม่เพียงพอ</option>                        
                        <option value="ไม่มี">ไม่มี</option>                           
                    </select>              
                </div>  
                <div class="mb-3">
                    <label class="form-label"><strong>13.Radiant Warmer</strong></label>
                    <select required class="form-select my-1" name="asset13">                                                             
                        <option value="พร้อมใช้">พร้อมใช้</option> 
                        <option value="other">ชำรุด</option>  
                        <option value="ไม่เพียงพอ">ไม่เพียงพอ</option>                        
                        <option value="ไม่มี">ไม่มี</option>                           
                    </select>              
                </div>  
                <div class="mb-3">
                    <label label for="detail" class="form-label"><strong>หมายเหตุ</strong></label>
                    <textarea class="form-control" name ="outher" rows="4"></textarea>           
                </div>                
                <div class="mb-3 text-center">
                    <button type="submit" class="btn btn-primary mt-3">ส่งข้อมูล</button>
                    <button type="reset" class="btn btn-secondary mt-3">Reset</button>
                </div>                
            </form>
        </div>
    </div>
</body>
</html>