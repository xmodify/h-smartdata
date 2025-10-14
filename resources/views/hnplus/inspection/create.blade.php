<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>ระบบบันทึกเวรตรวจการพยาบาล</title>

    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

    <!-- ✅ SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <div class="card">        
        <h5 class="alert alert-primary text-center">
            <strong>ระบบบันทึกเวรตรวจการพยาบาล</strong><br>
            วันที่ {{ DatetimeThai(date('Y-m-d H:i:s')) }}
        </h5> 

        <div class="card-body">         
            <form id="inspectionForm" action="{{ url('hnplus/inspection/save') }}" method="POST">
                @csrf 
                <input type="hidden" name="depart" value="{{ $depart }}">

                <div class="mb-3">
                    <label class="form-label"><strong>ความเสี่ยง/เหตุการณ์ในเวร</strong></label>
                    <textarea class="form-control" name="risk" id="risk" rows="3"></textarea>           
                </div>  

                <div class="mb-3">
                    <label class="form-label"><strong>การแก้ไขจัดการ</strong></label>
                    <textarea class="form-control" name="correct" id="correct" rows="3"></textarea>           
                </div>  

                <div class="mb-3">
                    <label class="form-label"><strong>นิเทศ/แนะนำในขณะตรวจเวร</strong></label>
                    <textarea class="form-control" name="complain" id="complain" rows="3"></textarea>           
                </div>    

                <div class="mb-3">
                    <label class="form-label"><strong>หมายเหตุ</strong></label>
                    <textarea class="form-control" name="note" id="note" rows="2"></textarea>           
                </div>  

                <div class="mb-3">
                    <label class="form-label"><strong>พยาบาลเวรตรวจการ</strong></label>  
                    <textarea class="form-control" name="supervisor" id="supervisor" rows="1"></textarea>                
                </div>  

                <div class="text-center">
                    <button type="submit" class="btn btn-primary mt-3">ส่งข้อมูล</button>
                    <button type="reset" class="btn btn-secondary mt-3">Reset</button>
                </div>                
            </form>
        </div>
    </div>

    <!-- ✅ Validate ก่อนส่ง -->
    <script>
    document.getElementById('inspectionForm').addEventListener('submit', function (e) {
        e.preventDefault(); // หยุดส่งก่อนตรวจสอบ

        const risk = document.getElementById('risk').value.trim();
        const correct = document.getElementById('correct').value.trim();
        const complain = document.getElementById('complain').value.trim();
        const supervisor = document.getElementById('supervisor').value.trim();

        if (!risk || !correct || !complain || !supervisor) {
            Swal.fire({
                icon: 'warning',
                title: 'กรอกข้อมูลไม่ครบ',
                text: 'กรุณากรอกข้อมูลให้ครบทุกช่องที่จำเป็นก่อนส่ง!',
                confirmButtonText: 'ตกลง'
            });
            return; // ไม่ส่งฟอร์ม
        }

        // ถ้าครบแล้วให้ยืนยันก่อนส่ง
        Swal.fire({
            title: 'ยืนยันการบันทึก?',
            text: "ตรวจสอบข้อมูลให้ถูกต้องก่อนส่ง",
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'บันทึกข้อมูล',
            cancelButtonText: 'ยกเลิก'
        }).then((result) => {
            if (result.isConfirmed) {
                e.target.submit(); // ส่งฟอร์มจริง
            }
        });
    });
    </script>

    <!-- ✅ SweetAlert หลังบันทึกสำเร็จ -->
    @if (session('success'))
    <script>
        Swal.fire({
            title: 'บันทึกสำเร็จ!',
            text: '{{ session('success') }}',
            icon: 'success',
            confirmButtonText: 'ตกลง'
        });
    </script>
    @endif
</body>
</html>
