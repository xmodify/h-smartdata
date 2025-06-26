<?php
    $files = "10989_EditExport$date.csv";
    header('Content-Encoding: UTF-8');
    header('Content-type: text/csv; charset=UTF-8');
    header("Content-Disposition: attachment; filename=".$files); //ชื่อไฟล์
    header("Pragma: no-cache");
    header("Expires: 0");
    header('Content-Transfer-Encoding: binary');
    echo "\xEF\xBB\xBF";
?>
@foreach ($nrls as $row)
{{$row->hospital}}|{{$row->risk_id}}|{{$row->datadic1}}|{{$row->effect_code}}|{{$row->pt_sex}}|{{$row->person_age}}|{{$row->datadic4}}|{{$row->risk_date}}|{{$row->risk_date}}|{{$row->datadic5}}|{{$row->datadic6}}|{{$row->risk_detail}}|{{$row->risk_detail_edit}}|{{$row->risk_detail_edit}}|-|{{ $row->RISKREP_INFER_IMPROVE }}|{{$row->risk_detail_group}}
@endforeach