<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
     <link rel="icon" href="{{ asset('/images/favicon.ico') }}" type="image/x-icon">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title >Non HospMain</title>

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
        <div class="alert alert-success text-primary" role="alert"><strong>รายชื่อผู้มารับบริการที่ ดึงข้อมูลปิดสิทธิจาก สปสช. วันที่ {{ DateThai($start_date) }} ถึง {{ DateThai($end_date) }}</strong></div>
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
                <div class="col-md-3" >
                    <button type="submit" class="btn btn-primary my-1 ">{{ __('ค้นหา') }}</button>
                    <!-- ปุ่มเรียก Modal -->
                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#nhsoModal">
                        ดึงปิดสิทธิ สปสช.
                    </button>
                </div>
            </div>
        </form>
        <div class="card-body">
            <div class="row">        
                <div class="col-md-12"> 
                    <div style="overflow-x:auto;">            
                        <table id="list" class="table table-striped table-bordered" width = "100%">
                            <thead>
                            <tr class="table-primary">
                                <th class="text-center">ลำดับ</th>               
                                <th class="text-center">CID</th>
                                <th class="text-center">ชื่อ-สกุล</th> 
                                <th class="text-center">สิทธิ</th> 
                                <th class="text-center">วัน-เวลาที่รับบริการ</th>
                                <th class="text-center">claimCode</th>          
                                <th class="text-center">claimType</th>   
                            </tr>     
                            </thead> 
                            <tbody> 
                            <?php $count = 1 ; ?>
                            @foreach($sql as $row) 
                            <tr>
                                <td align="center">{{ $count }}</td>                 
                                <td align="center">{{ $row->cid }}</td>
                                <td align="left">{{ $row->firstName }} {{ $row->lastName }}</td>
                                <td align="left">{{ $row->subInscl }} {{ $row->subInsclName }}</td>  
                                <td align="left">{{ DatetimeThai($row->serviceDateTime) }}</td>
                                <td align="center">{{ $row->claimCode }}</td>
                                <td align="center">{{ $row->claimType }}</td>
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

    <!-- Modal -->
    <div class="modal fade" id="nhsoModal" tabindex="-1" aria-labelledby="nhsoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content text-center">
                <div class="modal-header">
                    <h5>เลือกวันที่เข้ารับบริการ</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form id="nhsoForm">
                    <div class="modal-body">         
                    <input type="date" id="vstdate" name="vstdate" class="form-control"  value="{{ date('Y-m-d') }}" required>

                    <div id="loadingSpinner" class="mt-4 d-none">
                        <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2">กำลังดึงข้อมูลจาก สปสช....</p>
                    </div>

                    <div id="resultMessage" class="mt-3 d-none"></div>
                    </div>

                    <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">ดึงข้อมูล</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
                    </div>
                </form>
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

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const form = document.getElementById("nhsoForm");
            const spinner = document.getElementById("loadingSpinner");
            const resultMessage = document.getElementById("resultMessage");
            const nhsoModal = document.getElementById('nhsoModal');

            form.addEventListener("submit", function (e) {
                e.preventDefault();
                spinner.classList.remove("d-none");
                resultMessage.classList.add("d-none");
                resultMessage.innerHTML = "";

                const formData = new FormData(form);

                fetch("{{ url('dashboard/nhso_endpoint_pull') }}", {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": "{{ csrf_token() }}",
                        "Accept": "application/json"
                    },
                    body: formData
                })
                .then(response => {
                    spinner.classList.add("d-none");
                    if (!response.ok) throw new Error("โหลดล้มเหลว");
                    return response.json();
                })
                .then(data => {
                    resultMessage.classList.remove("d-none");
                    resultMessage.classList.add("text-success");
                    resultMessage.innerHTML = "✅ " + (data.message || "ดึงข้อมูลสำเร็จ");
                })
                .catch(err => {
                    resultMessage.classList.remove("d-none");
                    resultMessage.classList.add("text-danger");
                    resultMessage.innerHTML = "❌ ดึงข้อมูลล้มเหลว";
                });
            });

            nhsoModal.addEventListener('hide.bs.modal', function () {
                // ✅ Redirect ไปหน้า /home เมื่อปิด Modal
                window.location.href = "{{ url('dashboard/nhso_endpoint') }}";
            });
        });
    </script>
   
