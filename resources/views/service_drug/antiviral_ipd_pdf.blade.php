<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <style>
            @font-face {
                font-family: 'THSarabunNew';
                src: url('fonts/thsarabunnew-webfont.eot');
                src: url('fonts/thsarabunnew-webfont.eot?#iefix') format('embedded-opentype'),
                    url('fonts/thsarabunnew-webfont.woff') format('woff'),
                    url('fonts/thsarabunnew-webfont.ttf') format('truetype');
                font-weight: normal;
                font-style: normal;
            }        
            @font-face {
                font-family: 'THSarabunNew';
                src: url('fonts/thsarabunnew_bolditalic-webfont.eot');
                src: url('fonts/thsarabunnew_bolditalic-webfont.eot?#iefix') format('embedded-opentype'),
                    url('fonts/thsarabunnew_bolditalic-webfont.woff') format('woff'),
                    url('fonts/thsarabunnew_bolditalic-webfont.ttf') format('truetype');
                font-weight: bold;
                font-style: italic;
            }
            @font-face {
                font-family: 'THSarabunNew';
                src: url('fonts/thsarabunnew_italic-webfont.eot');
                src: url('fonts/thsarabunnew_italic-webfont.eot?#iefix') format('embedded-opentype'),
                    url('fonts/thsarabunnew_italic-webfont.woff') format('woff'),
                    url('fonts/thsarabunnew_italic-webfont.ttf') format('truetype');
                font-weight: normal;
                font-style: italic;
            }
            @font-face {
                font-family: 'THSarabunNew';
                src: url('fonts/thsarabunnew_bold-webfont.eot');
                src: url('fonts/thsarabunnew_bold-webfont.eot?#iefix') format('embedded-opentype'),
                    url('fonts/thsarabunnew_bold-webfont.woff') format('woff'),
                    url('fonts/thsarabunnew_bold-webfont.ttf') format('truetype');
                font-weight: bold;
                font-style: normal;
            } 
            @page {
                    margin: 0cm 0cm;
                        }
            body {
                /* font-family: 'THSarabunNew', sans-serif;
                    font-size: 13px;
                line-height: 0.9;  
                margin-top:    0.2cm;
                margin-bottom: 0.2cm;
                margin-left:   1cm;
                margin-right:  1cm;  */
                font-family: "THSarabunNew";
                font-size: 12px;
                line-height: 0.75;  
                margin-top:    1cm;
                margin-bottom: 1cm;
                margin-left:   2cm;
                margin-right:  1cm;                  
            }
            #watermark {     
                position: fixed;
                        bottom:   0px;
                        left:     0px;                   
                        width:    29.5cm;
                        height:   21cm;
                        z-index:  -1000;
            }
            table,td {
                border: 1px solid rgb(5, 5, 5); 
                }   
                .text-pedding{
                /* padding-left:10px;
                padding-right:10px; */
                }                     
                table{
                    border-collapse: collapse;  //กรอบด้านในหายไป
                }
                table.one{
                border: 1px solid rgb(5, 5, 5);
                /* height: 800px; */
                /* padding: 15px; */
                }
                td {
                    margin: .2rem;
                /* height: 3px; */
                /* padding: 5px; */
                /* text-align: left; */
                }
                td.o{
                    border: 1px solid rgb(5, 5, 5); 
                    font-family: "THSarabunNew";
                    font-size: 12px;
                }
                td.b{
                    border: 1px solid rgb(5, 5, 5); 
                }
                td.d{
                    border: 1px solid rgb(5, 5, 5); 
                    height: 170px;
                }
                td.e{
                    border: 1px solid rgb(5, 5, 5);
                    
                }
                td.h{
                    border: 1px solid rgb(5, 5, 5); 
                    height: 10px;
                }
                .page-break {
                    page-break-after: always;
                } 
                
                input {
                    margin: .3rem;
                }
                .tsm{
                    font-family: "THSarabunNew";
                    font-size: 11px;
                }
                .tss{
                    font-family: "THSarabunNew";
                    font-size: 10px;
                }   
        </style> 
    </head>
    <body>
        <div>
            <strong>
                <p align=center>
                    รายงานรายชื่อผู้ใช้เวชภัณฑ์ยาต้านไวรัส<br> 
                    สิทธิประกันสังคมผู้ป่วยใน โรงพยาบาลหัวตะพาน<br>                     
                    วันที่ {{dateThaifromFull($start_date)}} ถึง {{dateThaifromFull($end_date)}} <br>
                </p>
            </strong>
        </div>
        <div class="container">
            <div class="row justify-content-center">            
                <table class="table table-bordered table-striped my-3">
                    <thead>
                    <tr class="table-secondary">
                        <td align="center"><storage>ลำดับ</storage></td>
                        <td align="center"><storage>วันที่ได้รับ</storage></td>
                        <td align="center"><storage>HN</storage></td>
                        <td align="center"><storage>AN</storage></td>
                        <td align="center"><storage>CID</storage></td>
                        <td align="center"><storage>ชื่อ-สกุล</storage></td>
                        <td align="center"><storage>อายุ</storage></td>
                        <td align="center"><storage>รายการยา</storage></td>
                        <td align="center"><storage>จำนวน</storage></td>             
                    </tr>     
                    </thead> 
                    <?php $count = 1 ; ?> 
                    @foreach($drug_ipd_sss as $row)          
                    <tr>
                        <td align="center">{{$count}}</td>
                        <td align="right">{{DateThai($row->rxdate)}}</td>
                        <td align="center">{{$row->hn}}</td>
                        <td align="center">{{$row->an}}</td>
                        <td align="center">{{$row->cid}}</td>
                        <td align="left">{{$row->ptname}}</td>
                        <td align="center">{{$row->age_y}}</td>
                        <td align="left">{{$row->drug}}</td>
                        <td align="center">{{$row->qty}}</td>            
                    </tr>                
                    <?php $count++; ?>
                    @endforeach  
                    @foreach($drug_ipd_sss_sum as $row)  
                    <tr>
                        <td align="left" colspan = "9">รวมยา {{$row->drug}} ทั้งหมดจำนวน {{number_format($row->qty)}} เม็ด</td>   
                    </tr>  
                    @endforeach  
                </table>            
            </div>
        </div>         
    </body>
</html>



