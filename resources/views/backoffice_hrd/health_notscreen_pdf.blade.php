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
                width:    30cm;
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
<body>
    <div>
        <p align=center><strong>รายงานการคัดกรองสุขภาพเจ้าหน้าที่ วันที่ {{ DateThai($start_date) }} ถึง {{ DateThai($end_date) }}</strong></p>
    </div> 
    <div>  
        <table>
            <thead>
                <tr>
                    <td align="center"  width="10"><strong>ลำดับ</strong></td> 
                    <td align="center"  width="120"><strong>ชื่อ-สกุล</strong></td>
                    <td align="center"  width="20"><strong>เพศ</strong></td>  
                    <td align="center"  width="20"><strong>อายุ</strong></td> 
                    <td align="center"  width="30"><strong>หน่วยงาน</strong></td>  
                    <td align="center"  width="50"><strong>เบอร์โทร</strong></td> 
                    <td align="center"  width="100"><strong>หมายเหตุ</strong></td>     
                </tr>
            </thead>
            <?php $count = 1 ; ?>
            @foreach($health_notscreen as $row)
                <tr>
                    <td align="center" width="10">{{ $count }}</td>
                    <td align="left" width="120">{{ $row->hrd_name }} </td>
                    <td align="center" width="20">{{ $row->SEX }} </td>
                    <td align="center" width="20">{{ $row->AGE }} </td> 
                    <td align="left"width="30">{{ $row->HR_DEPARTMENT_SUB_SUB_NAME }}</td>
                    <td align="center"width="50">{{ $row->HR_PHONE }}</td>
                    <td align="center" width="100"></td>
                </tr>
            <?php $count++; ?>
            @endforeach
        </table>
    </div>
</body>
