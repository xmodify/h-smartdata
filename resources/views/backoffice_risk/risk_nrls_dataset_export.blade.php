<?php
$files = "10989_DataSet$date.csv";
header('Content-Type: text/csv; charset=utf-8');
header("Content-Disposition: attachment; filename=".$files); //ชื่อไฟล์
?>
@foreach($rr001 as $row)10989|RR001|{{ $date }}|{{$row->rr001}}@endforeach 
@foreach($rr003 as $row)10989|RR003|{{ $date }}|{{$row->rr003}}@endforeach 
@foreach($rr004 as $row)10989|RR004|{{ $date }}|{{$row->rr004}}@endforeach 
@foreach($rr005 as $row)10989|RR005|{{ $date }}|{{$row->rr005}}@endforeach 
@foreach($rr006 as $row)10989|RR006|{{ $date }}|{{$row->rr006}}@endforeach 
@foreach($rr007 as $row)10989|RR007|{{ $date }}|{{$row->rr007}}@endforeach 
@foreach($rr008 as $row)10989|RR008|{{ $date }}|{{$row->rr008}}@endforeach 
@foreach($rr009 as $row)10989|RR009|{{ $date }}|{{$row->rr009}}@endforeach 
@foreach($rr010 as $row)10989|RR010|{{ $date }}|{{$row->rr010}}@endforeach 
@foreach($rr011 as $row)10989|RR011|{{ $date }}|{{$row->rr011}}@endforeach 
@foreach($rr015 as $row)10989|RR015|{{ $date }}|{{$row->rr015}}@endforeach 
@foreach($rr016 as $row)10989|RR016|{{ $date }}|{{$row->rr016}}@endforeach 
@foreach($rr022 as $row)10989|RR022|{{ $date }}|{{$row->rr022}}@endforeach 
@foreach($rr024 as $row)10989|RR024|{{ $date }}|{{$row->rr024}}@endforeach