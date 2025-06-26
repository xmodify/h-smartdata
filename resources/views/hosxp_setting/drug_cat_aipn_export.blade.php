<?php
header("Content-Type: application/vnd.ms-excel");
header('Content-Disposition: attachment; filename="DrugCat AIPN.xls"');//ชื่อไฟล์
?>       

    <table>
        <thead>
            <tr>
                <th class="text-center">Chk_AIPN</th>
                <th class="text-center">HospDrugCode</th>
                <th class="text-center">ProductCat</th>
                <th class="text-center">TMTID</th>
                <th class="text-center">SpecPrep</th>
                <th class="text-center">GenericName</th>
                <th class="text-center">TradeName</th>
                <th class="text-center">DFSCode</th>
                <th class="text-center">DosageForm</th>
                <th class="text-center">Strength</th> 
                <th class="text-center">Content</th>                   
                <th class="text-center">UnitPrice</th> 
                <th class="text-center">Distributor</th>
                <th class="text-center">Manufacture</th>
                <th class="text-center">ISED</th>
                <th class="text-center">NDC24</th>
                <th class="text-center">Packsize</th>
                <th class="text-center">Packprice</th>
                <th class="text-center">UpdateFlag</th>                
                <th class="text-center">DateChange</th>
                <th class="text-center">DateUpdate</th>
                <th class="text-center">DateEffective</th> 
                <th class="text-center">RP</th>      
            </tr>     
        </thead>        
        @foreach($drug as $row)          
            <tr>
                <td align="right">{{ $row->chk_aipn_drugcat }}</td>
                <td align="right">{{ $row->HospDrugCode }}</td>
                <td align="right">{{ $row->ProductCat }}</td>
                <td align="center">{{ $row->TMTID }}</td>
                <td align="center">{{ $row->SpecPrep }}</td>
                <td align="left">{{ $row->GenericName }}</td>
                <td align="right">{{ $row->TradeName }}</td>
                <td align="left">{{ $row->DFSCode }}</td>
                <td align="center">{{ $row->DosageForm }}</td>
                <td align="center">{{ $row->Strength }}</td>
                <td align="right">{{ $row->Content }}</td>
                <td align="right">{{ $row->UnitPrice }}</td>    
                <td align="right">{{ $row->Distributor }}</td>
                <td align="right">{{ $row->Manufacture }}</td>
                <td align="right">{{ $row->ISED }}</td>
                <td align="center">{{ $row->NDC24 }}</td>
                <td align="center">{{ $row->Packsize }}</td>
                <td align="left">{{ $row->Packprice }}</td>
                <td align="right">{{ $row->UpdateFlag }}</td>
                <td align="left">{{ $row->DateChange }}</td>
                <td align="center">{{ $row->DateUpdate }}</td>
                <td align="center">{{ $row->DateEffective }}</td>
                <td align="right">{{ $row->RP }}</td>              
            </tr>                   
            </tr>      
        @endforeach  
    </table>     



