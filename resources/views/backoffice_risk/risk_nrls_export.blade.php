<?php
    $files = "10989_Export$date.csv";
    header('Content-Encoding: UTF-8');
    header('Content-type: text/csv; charset=UTF-8');
    header("Content-Disposition: attachment; filename=".$files); //ชื่อไฟล์
    header("Pragma: no-cache");
    header("Expires: 0");
    header('Content-Transfer-Encoding: binary');
    echo "\xEF\xBB\xBF";
?>
@foreach ($nrls as $row)
{{$row->nrls}}
@endforeach