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
                        header {
                position: fixed;
                font-family: "THSarabunNew";
                top: 1cm;
                left: 2cm;
                right: 1cm;          
                font-size: 12px;
                line-height: 0.75;  
                text-align: center; 
            }
            footer  {
                position: fixed;
                font-family: "THSarabunNew";
                bottom: 0.2cm;
                left: 2cm;
                right: 1cm;          
                font-size: 12px;
                line-height: 0.75;               
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
                margin-top:    3.5cm;
                margin-bottom: 2cm;
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
                    width: 100%; table-layout: fixed;
                    border-collapse: collapse;  //กรอบด้านในหายไป
                }
                table.one{
                border: 1px solid rgb(5, 5, 5);
                /* height: 800px; */
                /* padding: 15px; */
                }
                td {
                    word-wrap: break-word;  /* ตัดคำยาว */
                    white-space: normal;    /* อนุญาตให้ขึ้นบรรทัดใหม่ */
                    overflow-wrap: break-word; /* รองรับ browser อื่น */
                    vertical-align: top;    /* ข้อความชิดบน (จะดูเรียบร้อยขึ้น) */
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
        <header>
            <div>
                <strong>
                    <p align=center>
                        รายงานผลการตรวจสอบครุภัณฑ์วิทยาศาสตร์และการแพทย์<br>
                        ประจำปีงบประมาณ {{ $budget_year }}<br>                         
                        โรงพยาบาลหัวตะพาน อำเภอหัวตะพาน จังหวัดอำนาจเจริญ 
                    </p>
                </strong>
            </div>
        </header>

        <footer> 
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ลงชื่อ....................................ประธานกรรมการ
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            ลงชื่อ....................................กรรมการ&nbsp;&nbsp;&nbsp;
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            ลงชื่อ....................................กรรมการ<br><br><br>
        </footer>

        <main>
            <table width="100%">
                <thead>
                <tr class="table-secondary">
                    <td align="center" width="4%"><strong>ลำดับ</strong></td>   
                    <td align="center" width="20%"><strong>ชื่อครุภัณฑ์</strong></td>
                    <td align="center" width="10%"><strong>รหัสครุภัณฑ์</strong></td>
                    <td align="center"><strong>รหัสทรัพย์สิน</strong></td>
                    <td align="center"><strong>วันที่ได้มา</strong></td>
                    <td align="center"><strong>แหล่งเงิน</strong></td>   
                    <td align="center"><strong>วิธีได้มา</strong></td>    
                    <td align="center"><strong>ราคาทรัพย์สิน</strong></td>   
                    <td align="center"><strong>ประจำหน่วยงาน</strong></td> 
                    <td align="center"><strong>อายุการใช้งาน</strong></td>                      
                </thead>   
                <?php $count = 1 ; ?>                                              
                @foreach($asset as $row)  
                <?php $diff = abs(strtotime(date('Y-m-d')) - strtotime($row->RECEIVE_DATE));  ?> 
                <?php $years = floor($diff / (365*60*60*24));  ?> 
                <?php $months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));  ?> 
                <?php $days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));  ?> 
                <tr>                          
                    <td align="left" width="4%">&nbsp;{{ $count }}</td>      
                    <td align="left" width="20%">&nbsp;{{ $row->ARTICLE_NAME }}</td>
                    <td align="left" width="10%">&nbsp;{{ $row->ARTICLE_NUM }}</td>
                    <td align="left">&nbsp;{{ $row->SUP_FSN }}</td>
                    <td align="center">{{ DateThai($row->RECEIVE_DATE) }}</td>
                    <td align="left">&nbsp;{{ $row->BUDGET_NAME }}</td>
                    <td align="left">&nbsp;{{ $row->BUY_NAME }}</td>
                    <td align="right">{{ number_format($row->PRICE_PER_UNIT,2) }}</td>   
                    <td align="left">&nbsp;{{ $row->HR_DEPARTMENT_SUB_SUB_NAME }}</td>
                    <td align="center">{{$years}} ปี {{$months}} เดือน {{$days}} วัน</td>
                    <?php $count++; ?>
                @endforeach 
            </table>    
        </main>

    </body>
</html>



