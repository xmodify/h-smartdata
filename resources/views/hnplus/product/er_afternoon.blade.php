<!doctype html>
<html lang="th">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>ระบบบันทึกผลิตภาพทางการพยาบาล</title>

    <!-- ✅ Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- ✅ SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> 

    <style>
        body {
            background: #f4f7fa;
            font-family: "Prompt", sans-serif;
        }
        .card {
            border-radius: 15px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        }
        .form-label {
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="container py-4">
        <div class="card mx-auto" style="max-width: 700px;">
            <div class="card-header text-white text-center" style="background-color:#23A7A7;">
                <h5 class="mb-0"><strong>ระบบบันทึกผลิตภาพทางการพยาบาล<br>แผนกอุบัติเหตุ-ฉุกเฉิน (ER)</strong></h5>
            </div>

            <div class="card-body">
                <h6 class="text-primary mb-3">วันที่ {{ DateThai(date('Y-m-d')) }} <strong>สรุปเวรบ่าย</strong></h6>

                <form id="productForm" action="{{ url('hnplus/product/er_afternoon_save') }}" method="POST">
                    @csrf 
                    <input type="hidden" name="report_date" value="{{ date('Y-m-d') }}">
                    <input type="hidden" name="shift_time" value="เวรบ่าย">

                    @foreach($shift as $row)
                    <div class="row mb-2">
                        <div class="col-8">จำนวนผู้ป่วยเวรบ่าย</div>
                        <div class="col text-end"><strong>{{ $row->visit }}</strong> ราย</div>
                    </div>
                    <input type="hidden" name="patient_all" value="{{ $row->visit }}">

                    <div class="row mb-2">
                        <div class="col-8">Emergent</div>
                        <div class="col text-end"><strong>{{ $row->Emergent }}</strong> ราย</div>
                    </div>
                    <input type="hidden" name="emergent" value="{{ $row->Emergent }}">

                    <div class="row mb-2">
                        <div class="col-8">Urgent</div>
                        <div class="col text-end"><strong>{{ $row->Urgent }}</strong> ราย</div>
                    </div>
                    <input type="hidden" name="urgent" value="{{ $row->Urgent }}">

                    <div class="row mb-2">
                        <div class="col-8">Acute illness</div>
                        <div class="col text-end"><strong>{{ $row->Acute_illness }}</strong> ราย</div>
                    </div>
                    <input type="hidden" name="acute_illness" value="{{ $row->Acute_illness }}">

                    <div class="row mb-4">
                        <div class="col-8">Non Acute illness</div>
                        <div class="col text-end"><strong>{{ $row->Non_acute_illness }}</strong> ราย</div>
                    </div>
                    <input type="hidden" name="non_acute_illness" value="{{ $row->Non_acute_illness }}">
                    @endforeach

                    <div class="mb-3">
                        <label for="nurse_oncall" class="form-label">อัตรากำลัง Oncall (ไม่มีใส่ 0)</label>
                        <input type="number" id="nurse_oncall" name="nurse_oncall" class="form-control" placeholder="ระบุจำนวน">
                    </div>

                    <div class="mb-3">
                        <label for="nurse_partime" class="form-label">อัตรากำลังเสริม (ไม่มีใส่ 0)</label>
                        <input type="number" id="nurse_partime" name="nurse_partime" class="form-control" placeholder="ระบุจำนวน">
                    </div>

                    <div class="mb-3">
                        <label for="nurse_fulltime" class="form-label">อัตรากำลังปกติ</label>
                        <input type="number" id="nurse_fulltime" name="nurse_fulltime" class="form-control" placeholder="ระบุจำนวน">
                    </div>

                    <div class="mb-3">
                        <label for="recorder" class="form-label">ผู้บันทึก</label>
                        <input type="text" id="recorder" name="recorder" class="form-control" placeholder="ชื่อ-สกุล ผู้บันทึก">
                    </div>

                    <div class="mb-3">
                        <label for="note" class="form-label">หมายเหตุ</label>
                        <input type="text" id="note" name="note" class="form-control" placeholder="หมายเหตุเพิ่มเติม (ถ้ามี)">
                    </div>

                    <div class="text-center">
                        <button type="submit" class="btn text-white px-4" style="background-color:#23A7A7;">ส่งข้อมูล</button>
                        <button type="reset" class="btn btn-secondary px-4">ล้างข้อมูล</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- ✅ SweetAlert ตรวจสอบก่อนส่ง -->
    <script>
    document.getElementById('productForm').addEventListener('submit', function (e) {
        e.preventDefault();

        const nurse_oncall = document.getElementById('nurse_oncall').value.trim();
        const nurse_partime = document.getElementById('nurse_partime').value.trim();
        const nurse_fulltime = document.getElementById('nurse_fulltime').value.trim();
        const recorder = document.getElementById('recorder').value.trim();

        if (!nurse_oncall || !nurse_partime || !nurse_fulltime || !recorder) {
            Swal.fire({
                icon: 'warning',
                title: 'กรอกข้อมูลไม่ครบ',
                text: 'กรุณากรอกข้อมูลให้ครบทุกช่องที่จำเป็นก่อนส่ง!',
                confirmButtonText: 'ตกลง'
            });
            return;
        }

        Swal.fire({
            title: 'ยืนยันการบันทึก?',
            text: "ตรวจสอบข้อมูลให้ถูกต้องก่อนส่ง",
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'บันทึกข้อมูล',
            cancelButtonText: 'ยกเลิก'
        }).then((result) => {
            if (result.isConfirmed) {
                e.target.submit();
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
