<?php 
	ini_set('max_execution_time', 3000);
    require("./Module/Database.php");

    $table_list = "access_order_list";
    $table_information = "access_item_information";
    $table_fgs_data = "access_fgs_data";
    $table_mla = "access_mla";
    $table_optinal_pewindow = "access_optinal_pewindow";
    $table_inventory_list = "access_inventory_list";

    if(isset($_GET["EVENT"]) && $_GET["EVENT"] == "LOADJOBJACKET")
    {
        $JJ = $_GET["JJ"];
        $Selection = "  Num,
                        ID,
                        GLID,
                        Bo,
                        Order_Quantity,
                        DueDay,
                        Order_Receive_Day,
                        Submit_Date,
                        Print_Sheet,
                        Print_Scrap,
                        Finish_Scrap,
                        Order_Style,
                        Urgent_Status,
                        Order_Check,
                        Stock_Code_F,
                        UPS,
                        Stock_Size,
                        Cut_Number,
                        Order_Handler,
                        FGS_Check,
                        Color_Sum_FB,
                        Color_By_Size,
                        Imprint_Lot,
                        New_Product_If,
                        PPC_Remark,
                        Print_Machine,
                        Complete_Status,
                        Complete_Date,
                        Complete_Quality,
                        Request_Date,
                        Promise_Date,
                        SO,
                        SO_Lines,
                        ActiveOrder,
                        DeleteBy,
                        IDS";
        $RowTable = MiQuery( "SELECT $Selection FROM $table_list WHERE ID = '$JJ';", _conn());
        if(count($RowTable) != 0) {
            $RTF = $RowTable[0];
            foreach(explode(",",$Selection) as $R) $RTF[str_replace(["\r","\n", " "],"",$R)] = str_replace(["\r","\n"],"\\n",$RTF[str_replace(["\r","\n", " "],"",$R)]);
        } else {
            foreach(explode(",",$Selection) as $R) {
                $RTF[str_replace(["\r","\n", " "],"",$R)] = "";
            }
        }
        $RTF["Bo"] = str_replace("'","\'",$RTF["Bo"]);
        $RTF["Bo"] = htmlspecialchars_decode($RTF["Bo"], ENT_QUOTES);
        $MLA = MiQueryScalar( "SELECT MLA FROM $table_mla WHERE RBO = '" . $RTF["Bo"] . "' LIMIT 1;", _conn());

        if($MLA != "") $MLA = "MLA";
        else $MLA = "";
        $SubCon = MiQueryScalar( "SELECT SubContract_Detail FROM $table_information WHERE GLID = '" . $RTF["GLID"] . "' LIMIT 1;", _conn());
        if($SubCon == "") $SubCon = "";
        $RTF["Order_Handler"][0] = strtoupper($RTF["Order_Handler"][0]);
        $StringJsonMain = '[
                            {type: "settings", position: "label-left", labelWidth: 90, inputWidth: 130},
                            {type: "block", width: "auto", blockOffset: 20, offsetTop: 20, list: [
                                {type: "settings", labelWidth: "100", inputWidth: "200"},
                                {type: "input", label: "ID", style: "color:blue;font-weight:bold", value: "' . $JJ . '", name: "ID"},
                                {type: "combo", style: "color:red;font-weight:bold", label: "Order Style", value: "", name: "Order_Style", readonly: true, options:[
                                        {text: "FG", value: "FG"},
                                        {text: "Digital", value: "Digital"},
                                        {text: "WIP", value: "WIP"},
                                        {text: "Imprint", value: "Imprint"},
                                        {text: "FGS-IN Offset", value: "FGS-IN Offset"},
                                        {text: "FGS-IN Digital", value: "FGS-IN Digital"},
                                        {text: "FGS-OUT", value: "FGS-OUT"},
                                        {text: "FGS-OUT-BK", value: "FGS-OUT-BK"}
                                ]},
                                {type: "combo", style: "color:red;font-weight:bold", label: "Urgent Level", value: "", name: "Urgent_Status", readonly: true, options:[
                                        {text: "", value: ""},
                                        {text: "CCR", value: "CCR"},
                                        {text: "RE-PRINT", value: "RE-PRINT"},
                                        {text: "FOD", value: "FOD"},
                                        {text: "SAMPLE", value: "SAMPLE"},
                                        {text: "TECHNICAL TEST", value: "TECHNICAL TEST"}
                                ]},
                                {type: "input", label: "BO", style: "color:blue;font-weight:bold", value: "' . $RTF["Bo"] . '", name: "Bo"},
                                {type: "input", label: "MLA", style: "color:blue;font-weight:bold", value: "' . $MLA . '", name: "MLA"},
                                {type: "input", label: "GLID", style: "color:blue;font-weight:bold", value: "' . $RTF["GLID"] . '", name: "GLID"},
                                {type: "input", label: "SO", style: "color:blue;font-weight:bold", value: "' . $RTF["SO"] . '", name: "SO"},
                                {type: "input", label: "Quantity", style: "color:blue;font-weight:bold", value: "' . ($RTF["Order_Quantity"] != "0" ? $RTF["Order_Quantity"] : "") . '", name: "Order_Quantity"},
                                {type: "input", label: "PPC name", style: "color:blue;font-weight:bold", value: "' . explode("@",$RTF["Order_Handler"])[0] . '", name: "Order_Handler"},
                                {type: "input", label: "Handling date", style: "color:blue;font-weight:bold", value: "' . $RTF["Order_Receive_Day"] . '", name: "Order_Receive_Day"},
                                {type: "calendar", style: "color:blue;font-weight:bold", dateFormat: "%d/%m/%Y", label: "Promise Date", value: "' . $RTF["Promise_Date"] . '", name: "Promise_Date"},
                                {type: "calendar", style: "color:blue;font-weight:bold", dateFormat: "%d/%m/%Y", label: "Request Date", value: "' . $RTF["Request_Date"] . '", name: "Request_Date"},
                                {type: "input", style: "color:red;font-weight:bold", label: "Print Sheet <button onclick=\'CalculateSheet()\'>C</button>", value: "' . $RTF["Print_Sheet"] . '", name: "Print_Sheet"},
                                {type: "input", style: "color:blue;font-weight:bold", label: "Print Scrap <button onclick=\'CalculateScrap()\'>S</button>", value: "' . $RTF["Print_Scrap"] . '", name: "Print_Scrap"},
                                {type: "input", style: "color:blue;font-weight:bold", label: "Finishing Scrap", value: "' . $RTF["Finish_Scrap"] . '", name: "Finish_Scrap"},
                                {type: "input", style: "color:blue;font-weight:bold", label: "Color By Size", value: "' . $RTF["Color_By_Size"] . '", name: "Color_By_Size"},
                                {type: "input", style: "color:blue;font-weight:bold", label: "Imprint lot", value: "' . $RTF["Imprint_Lot"] . '", name: "Imprint_Lot"},
                                {type: "input", style: "color:blue;font-weight:bold", label: "Color sum F/B", value: "' . $RTF["Color_Sum_FB"] . '", name: "Color_Sum_FB"},
                                {type: "input", style: "color:blue;font-weight:bold", label: "Print Machine", value: "' . $RTF["Print_Machine"] . '", name: "Print_Machine"},
                                {type: "input", style: "color:blue;font-weight:bold", label: "Due Date", value: "' . $RTF["DueDay"] . '", name: "DueDay", readonly:true},
                            ]},
                            {type: "newcolumn", offset: "10"},
                            {type: "block", width: "auto", blockOffset: 20, offsetTop: 20, list: [
                                {type: "input", style: "color:blue;font-weight:bold", label: "Material Code", name:"Stock_Code_F", value: "' . $RTF["Stock_Code_F"] . '", inputWidth: "200"},
                                {type: "newcolumn"},
                                {type: "button", style: "color:blue;font-weight:bold", name: "LoadData",label: "New Input", offsetTop: 0, value: "Load Relative Technology"}
                            ]},
                            {type: "block", width: "auto", blockOffset: 20, list: [
                                {type: "input", style: "color:blue;font-weight:bold", label: "Sheetsize", name: "Stock_Size", value: "' . $RTF["Stock_Size"] . '"},
                                {type: "newcolumn"},
                                {type: "input", style: "color:blue;font-weight:bold", label: "UPS", value: "' . $RTF["UPS"] . '", name:"UPS", inputWidth: "100", labelAlign: "center", labelWidth: "40"},
                                {type: "newcolumn"},
                                {type: "input", style: "color:blue;font-weight:bold", label: "Cut", value: "' . $RTF["Cut_Number"] . '", name:"Cut_Number", labelAlign: "center", inputWidth: "60", labelWidth: "40"}
                            ]},
                            {type: "block", width: "auto", blockOffset: 20, list: [
                                {type: "container", label: "ID Lines SO - Total SO Line = <a style=\'font-weight:bold; font-size:14pt;color:red\' ID=\'TotalSOLINE\'><\a>", name: "SOLineID", value: "", position: "label-top", labelWidth:"200", inputHeight: "200", inputWidth: "470"},
                                {type: "input", label: "Remark", name:"PPC_Remark", value: "' . $RTF["PPC_Remark"] . '", inputWidth: "470", position: "label-top", rows: "7"}
                            ]},
                            {type: "block", width: "auto", blockOffset: 0, offsetLeft:"20", list: [
                                {type: "input", style: "color:blue;font-weight:bold", name: "ActualPCS", label: "Actual PCS", value: "", position: "label-top", inputWidth: "100", offsetLeft: "10", labelAlign: "center"},
                                {type: "newcolumn"},
                                {type: "input", name: "OverRate", style: "color:red;font-weight:bold", label: "Over Rate", value: "", inputWidth: "100", position: "label-top", offsetLeft: "10",  labelAlign: "center"},
                                {type: "newcolumn"},
                                {type: "input", name: "ScrapRate", style: "color:red;font-weight:bold", label: "Scrap Rate", value: "", position: "label-top", inputWidth: "100", offsetLeft: "10",  labelAlign: "center"},
                                {type: "newcolumn"},
                                {type: "checkbox", label: "New Products", checked: "' . $RTF["New_Product_If"] . '", position: "label-top", offsetLeft: "20", name: "New_Product_If"}
                            ]},
                            {type: "block", width: "auto", blockOffset: 0, offsetTop: "0",  list: [
                                {type: "input", name:"SO_Lines", label: "SO Line <textarea  style=\'height:0px;width:0px\' id=\'ClipBoard\'/>", value: "' . $RTF["SO_Lines"] . '",  position: "label-left", inputWidth: "395", rows: "3", labelAlign: "center"},
                            ]},
                            {type: "newcolumn"},
                            {type: "block", width: "auto", offsetTop: 20, blockOffset: 20, list: [
                                {type: "container", label: "Nhập dữ liệu LOT", value: "", name: "LotData", inputWidth: "450", inputHeight: "310", labelWidth: "300", position: "label-top"}
                            ]},
                            {type: "block", width: "auto", blockOffset: 20, list: [
                                {type: "button", name: "CopyJob", offsetTop: 0, value: "Copy SO Line From Another Job"},
                                {type: "button", name: "ShowLOT", offsetTop: 0, value: "Input Lot"},
                                {type: "button", name: "LayMau", offsetTop: 0, value: "Lấy Mẫu"},
                                {type: "button", name: "COMBINEITEM", offsetTop: 0, value: "Combine Item"},
                                {type: "input", name: "Submit_Date", label:"Submit Date", offsetTop: 0, value: "' . $RTF["Submit_Date"] . '"},
                            ]}
                        ];';
        $DataMain["Main"] = $StringJsonMain;
        $DataMain["OS"] = $RTF["Order_Style"];
        $DataMain["US"] = $RTF["Urgent_Status"];
        $DataMain["Delete"] = $RTF["DeleteBy"];
        $DataMain["SUB"] = $SubCon;
        echo json_encode($DataMain);
        // echo $StringJsonMain;
    } else if(isset($_GET["EVENT"]) && $_GET["EVENT"] == "FORMMAIN")
    {
        $GLID = str_replace("||","&",$_GET["GLID"]);
        $Selection = "GLID,
        Item_Code,
        Buying_Office,
        Fit_Variable,
        Production_Type,
        Production_Line,
        DS_Sample,
        OS_Sample,
        ProductionWidth,
        ProductionLength,
        Sheet_Size,
        Stock_Code,
        Color_F,
        Color_B,
        Color_FQ,
        Color_BQ,
        Varnish_F,
        Varnish_B,
        Imprint_F,
        Imprint_B,
        Offset_Level,
        Offset_Imp_Front,
        Offset_Imp_Back,
        Offset_UPS,
        Offset_Cut_No,
        Digital_Level,
        Digital_F_Click,
        Digital_B_Click,
        Digital_UPS,
        Digital_Cut_No,
        Digital_Sheet_Size,
        Digital_Stock_Code_F,
        Digital_DieCut_No,
        Digital_Availability,
        Hot_Folder,
        Variable_F,
        Variable_B,
        DieCut_Machine,
        DieCut_No,
        Suited_Machine,
        Digital_Machine,
        Special_Instruction,
        Crocking_Test,
        SubContract,
        SubContract_Detail,
        Process,
        Special_Drying_Time,
        Standard_LeadTime,
        Hole,
        UV_F,
        UV_B,
        Active,
        Color_Management,
        CS_Sample,
        Last_Order_Time,
        Last_Revise_Date,
        Original_System,
        PE_Name,
        PE_Receive_Date,
        Ready_Date,
        Revise_People,
        Scrap_Adjustment,
        Social_Compliance,
        Status,
        Setup_Date,
        Offset_Extra_Time,
        Offset_Waiting_Drying,
        Digital_Extra_Time,
        Digital_Waiting_Drying,
        FSC,
        Finishing_Difficult_Rate,
        StringCut_ComboTag,
        FirstOrder,
        Inactive_Reason,
        CheckReplaceMaterial,
        Brand_Protection";

        $RowTable = MiQuery( "SELECT $Selection FROM $table_information WHERE GLID = '$GLID';", _conn());

        if(count($RowTable) != 0) {
            $RTF = $RowTable[0];
            foreach(explode(",",$Selection) as $R) $RTF[str_replace(["\r","\n", " "],"",$R)] = str_replace('"','\\"',str_replace(["\r","\n"],"\\n",$RTF[str_replace(["\r","\n", " "],"",$R)]));
        } else {
            foreach(explode(",",$Selection) as $R) $RTF[str_replace(["\r","\n", " "],"",$R)] = "";
        }


        $Production_Type = array();
        $Production_Line = array();
        $DigMachine_Array = array();
        $FSC_Array = array();
        $SystemName_Array = array();
        $Color_Management = array();
        $Digital_Availability = array();
        $Social_Compliance = array();
        $BO = array();
        $StatusArr = array(); //["","Sampling","Ready","Pending","Obsolete","Approved"];

        array_push($Production_Type, array("text" => "", "value" => ""));
        array_push($SystemName_Array, array("text" => "", "value" => ""));
        array_push($DigMachine_Array, array("text" => "", "value" => ""));
        array_push($Digital_Availability, array("text" => "", "value" => ""));
        array_push($Production_Line, array("text" => "", "value" => ""));
        array_push($FSC_Array, array("text" => "", "value" => ""));
        array_push($Color_Management, array("text" => "", "value" => ""));
        array_push($StatusArr, array("text" => "", "value" => ""));
        $Buying_Office = array();
        array_push($Buying_Office, array("text" => "", "value" => ""));

        $RowTable = MiQuery( "SELECT Production_Type, Machine, Production_Line, Color_Management, Social_Compliance, System_Name, Digital_Availability, Status, FSC_Type, BO FROM $table_optinal_pewindow;", _conn());
        foreach($RowTable as $row) {
            if($row["BO"] != "") array_push($Buying_Office, array("text" => $row["BO"], "value" => $row["BO"]));
            if($row["Production_Type"] != "") array_push($Production_Type, array("text" => $row["Production_Type"], "value" => $row["Production_Type"]));
            if($row["Machine"] != "") array_push($DigMachine_Array, array("text" => $row["Machine"], "value" => $row["Machine"]));
            if($row["Production_Line"] != "") array_push($Production_Line, array("text" => $row["Production_Line"], "value" => $row["Production_Line"]));
            if($row["FSC_Type"] != "") array_push($FSC_Array, array("text" => $row["FSC_Type"], "value" => $row["FSC_Type"]));
            if($row["System_Name"] != "") array_push($SystemName_Array, array("text" => $row["System_Name"], "value" => $row["System_Name"]));
            if($row["Color_Management"] != "") array_push($Color_Management, array("text" => $row["Color_Management"], "value" => $row["Color_Management"]));
            if($row["Digital_Availability"] != "") array_push($Digital_Availability, array("text" => $row["Digital_Availability"], "value" => $row["Digital_Availability"]));
            if($row["Status"] != "") array_push($StatusArr, array("text" => $row["Status"], "value" => $row["Status"]));
            if(!in_array($row["Social_Compliance"], $Social_Compliance)) array_push($Social_Compliance, $row["Social_Compliance"]);
        }

        $Stock_Code_Array = array();
        array_push($Stock_Code_Array, array("text" => "", "value" => ""));
        
        $RowTable = MiQuery( "SELECT StockCode FROM $table_inventory_list GROUP BY StockCode;", _conn());
        foreach($RowTable as $row){
            array_push($Stock_Code_Array, array("text" => $row["StockCode"], "value" => $row["StockCode"]));
        }
        $Buying_Office = AddSelection($Buying_Office,$RTF["Buying_Office"]);
        $Production_Type = AddSelection($Production_Type,$RTF["Production_Type"]);
        $Production_Line = AddSelection($Production_Line,$RTF["Production_Line"]);
        $FSC_Array = AddSelection($FSC_Array,$RTF["FSC"]);
        $SystemName_Array = AddSelection($SystemName_Array,$RTF["Original_System"]);
        $Color_Management = AddSelection($Color_Management,$RTF["Color_Management"]);
        $MaterialCodeArr = AddSelection($Stock_Code_Array,$RTF["Stock_Code"]);
        $Digital_Availability = AddSelection($Digital_Availability,$RTF["Digital_Availability"]);
        $DigitalMaterial = AddSelection($Stock_Code_Array,$RTF["Digital_Stock_Code_F"]);
        $Digital_Machine = AddSelection($DigMachine_Array,$RTF["Digital_Machine"]);
        $Suited_Machine = AddSelection($DigMachine_Array,$RTF["Suited_Machine"]);

        $DataMain = array();
        $DataMain["Production_Type"] = $Production_Type;
        $DataMain["SystemName_Array"] = $SystemName_Array;
        $DataMain["DigMachine_Array"] = $DigMachine_Array;
        $DataMain["Digital_Availability"] = $Digital_Availability;
        $DataMain["Production_Line"] = $Production_Line;
        $DataMain["FSC_Array"] = $FSC_Array;
        $DataMain["Color_Management"] = $Color_Management;
        $DataMain["Buying_Office"] = $Buying_Office;
        $DataMain["Stock_Code_Array"] = $Stock_Code_Array;
        
        $StringJsonMain = '[
            {type: "settings", position: "label-left", labelWidth: 90, offsetTop:5, offsetLeft:0, inputWidth: 130},
            {type: "block", width: "auto", blockOffset: 5, list: [
                {type: "block", width: "auto", blockOffset: 20, list: [
                    {type: "block", width: "auto", blockOffset: "", list: [
                        {type: "settings", labelWidth: "105", inputWidth: "150"},
                        {type: "input", name: "GLID", label: "GLID", value: "' . $RTF["GLID"] . '", readonly: true},
                        {type: "input", name: "Item_Code", label: "Item Code", value: "' . $RTF["Item_Code"] . '"},
                        {type: "combo", name: "Buying_Office", label: "Buying Office", filtering: true, options:' . json_encode($Buying_Office) . '},
                        {type: "combo", name: "Production_Type", label: "Product Type", filtering: true, options:' . json_encode($Production_Type) . '},
                        {type: "input", name: "Fit_Variable", label: "Fit Variable", value: "' . $RTF["Fit_Variable"] . '"},
                        {type: "input", name: "ProductionLength", label: "Product Length", value: "' . $RTF["ProductionLength"] . '"},
                        {type: "input", name: "CS_Sample", label: "FG Sample", value: "' . $RTF["CS_Sample"] . '"},
                        {type: "input", name: "Color_F", label: "Color F", value: "' . $RTF["Color_F"] . '", rows: "3"},
                        {type: "input", name: "Color_FQ", label: "Color FQ", value: "' . $RTF["Color_FQ"] . '"},
                        {type: "input", name: "Variable_F", label: "Variable F", value: "' . $RTF["Variable_F"] . '"},
                        {type: "input", name: "Imprint_F", label: "Imprint F", value: "' . $RTF["Imprint_F"] . '"},
                        {type: "input", name: "Varnish_F", label: "Varnish F", value: "' . $RTF["Varnish_F"] . '"},
                        {type: "input", name: "UV_F", label: "UV F", value: "' . $RTF["UV_F"] . '"},
                        {type: "combo", name: "Color_Management", label: "Color Management", filtering: true, options:' . json_encode($Color_Management) . '},
                        {type: "checkbox", name: "SubContract", label: "Sub Contract", checked: "' . $RTF["SubContract"] . '"},
                    ]},
                    {type: "newcolumn"},
                    {type: "block", width: "auto", blockOffset: 20, list: [
                        {type: "settings", labelWidth: "100", inputWidth: "150"},
                        {type: "checkbox", name: "Active", label: "Active", checked: "' . $RTF["Active"] . '"},
                        {type: "checkbox", name: "Hole", label: "Hole", checked: "' . $RTF["Hole"] . '"},
                        {type: "checkbox", name: "FirstOrder", label: "Lấy mẫu", checked: "' . $RTF["FirstOrder"] . '"},
                        {type: "combo", name: "Production_Line", label: "Product Line", value: "", filtering: true, options:' . json_encode($Production_Line) . '},
                        {type: "combo", name: "FSC", label: "FSC", filtering: true, options:' . json_encode($FSC_Array) . '},
                        {type: "input", name: "ProductionWidth", label: "<a style=\'color:red;font-weight:bold\'>X</a> Product Width", value: "' . $RTF["ProductionWidth"] . '"},
                        {type: "combo", name: "Original_System", label: "Original System", filtering: true, options:' . json_encode($SystemName_Array) . '},
                        {type: "input", name: "Color_B", label: "Color B", value: "' . $RTF["Color_B"] . '", rows: "3"},
                        {type: "input", name: "Color_BQ", label: "Color BQ", value: "' . $RTF["Color_BQ"] . '"},
                        {type: "input", name: "Variable_B", label: "Variable B", value: "' . $RTF["Variable_B"] . '"},
                        {type: "input", name: "Imprint_B", label: "Imprint B", value: "' . $RTF["Imprint_B"] . '"},
                        {type: "input", name: "Varnish_B", label: "Varnish B", value: "' . $RTF["Varnish_B"] . '"},
                        {type: "input", name: "UV_B", label: "UV B", value: "' . $RTF["UV_B"] . '"},
                        {type: "checkbox", name: "Crocking_Test", label: "Crocking Test", checked: "' . $RTF["Crocking_Test"] . '"},
                        {type: "checkbox", name: "Brand_Protection", label: "Brand Protection", checked: "' . $RTF["Brand_Protection"] . '"}
                    ]}
                ]},
                {type: "block", width: "auto", blockOffset: 20, list: [
                    {type: "settings", labelWidth: "100", inputWidth: "430"},
                    
                    {type: "input", name: "SubContract_Detail", label: "Sub Contract Details", value: "' . $RTF["SubContract_Detail"] . '", rows: "5"},
                    {type: "input", name: "Special_Instruction", label: "Special Instructions", value: "' . $RTF["Special_Instruction"] . '", rows: "7"}
                ]}
            ]},
            {type: "newcolumn"},
            {type: "block", width: "auto", blockOffset: 10, list: [
                {type: "block", width: "auto", blockOffset: 10, list: [
                    {type: "block", width: "auto", blockOffset: 20, list: [
                        {type: "settings", labelWidth: "100", inputWidth: "150"},
                        {type: "label", name: "Offset", label: "Offset", value: "", inputWidth: "0", readonly: true},
                        {type: "combo", name: "Stock_Code", label: "Material Code", filtering: true, options:' . json_encode($MaterialCodeArr) . '},
                        {type: "input", name: "Sheet_Size", label: "Sheet Size", value: "' . $RTF["Sheet_Size"] . '"},
                        {type: "input", name: "Offset_UPS", label: "Offset UPS", value: "' . $RTF["Offset_UPS"] . '"},
                        {type: "input", name: "Offset_Cut_No", label: "Offset Cut No", value: "' . $RTF["Offset_Cut_No"] . '"},
                        {type: "input", name: "Offset_Imp_Front", label: "Offset Imp Front", value: "' . $RTF["Offset_Imp_Front"] . '"},
                        {type: "input", name: "Offset_Imp_Back", label: "Offset Imp Back", value: "' . $RTF["Offset_Imp_Back"] . '"},
                        {type: "input", name: "Offset_Level", label: "Offset Level", value: "' . $RTF["Offset_Level"] . '"},
                        {type: "input", name: "OS_Sample", label: "OS Sample", value: "' . $RTF["OS_Sample"] . '"},
                        {type: "combo", name: "Suited_Machine", label: "Suited Machine", filtering: true, options:' . json_encode($Suited_Machine) . '},
                        {type: "input", name: "Offset_Extra_Time", label: "Off Extra Time", value: "' . $RTF["Offset_Extra_Time"] . '"},
                        {type: "input", name: "Offset_Waiting_Drying", label: "Off Waiting Dry", value: "' . $RTF["Offset_Waiting_Drying"] . '"},
                        {type: "input", name: "Special_Drying_Time", label: "Special Drying", value: "' . $RTF["Special_Drying_Time"] . '"},
                        {type: "input", name: "DieCut_No", label: "DieCut No", value: "' . $RTF["DieCut_No"] . '"},
                        {type: "input", name: "DieCut_Machine", label: "DieCut Machine", value: "' . $RTF["DieCut_Machine"] . '"},
                        {type: "input", name: "Scrap_Adjustment", label: "Printing Difficult", value: "' . $RTF["Scrap_Adjustment"] . '"},
                    ]},
                    {type: "newcolumn"},
                    {type: "block", width: "auto", blockOffset: 20, list: [
                        {type: "settings", labelWidth: "110", inputWidth: "150"},
                        {type: "combo", name: "Digital_Availability", label: "Digital Available", filtering: true, options:' . json_encode($Digital_Availability) . '},
                        {type: "combo", name: "Digital_Stock_Code_F", label: "Dig. Material", filtering: true, options:' . json_encode($DigitalMaterial) . '
                        },
                        {type: "input", name: "Digital_Sheet_Size", label: "Dig. Sheet Size", value: "' . $RTF["Digital_Sheet_Size"] . '"},
                        {type: "input", name: "Digital_UPS", label: "Dig. UPS", value: "' . $RTF["Digital_UPS"] . '"},
                        {type: "input", name: "Digital_Cut_No", label: "Dig. Cut No", value: "' . $RTF["Digital_Cut_No"] . '"},
                        {type: "input", name: "Digital_F_Click", label: "Dig. F Click", value: "' . $RTF["Digital_F_Click"] . '"},
                        {type: "input", name: "Digital_B_Click", label: "Dig. B Click", value: "' . $RTF["Digital_B_Click"] . '"},
                        {type: "input", name: "Digital_Level", label: "Digital Level", value: "' . $RTF["Digital_Level"] . '"},
                        {type: "input", name: "DS_Sample", label: "DS Sample", value: "' . $RTF["DS_Sample"] . '"},
                        {type: "combo", name: "Digital_Machine", label: "Dig. Machine", filtering: true, options:' . json_encode($Digital_Machine) . '},
                        {type: "input", name: "Digital_Extra_Time", label: "Dig. Extra Time", value: "' . $RTF["Digital_Extra_Time"] . '"},
                        {type: "input", name: "Digital_Waiting_Drying", label: "Dig. Waiting Dry", value: "' . $RTF["Digital_Waiting_Drying"] . '"},
                        {type: "input", name: "Hot_Folder", label: "Hot Folder", value: "' . $RTF["Hot_Folder"] . '"},
                        {type: "input", name: "Digital_DieCut_No", label: "Dig DieCut No", value: "' . $RTF["Digital_DieCut_No"] . '"},
                        {type: "input", name: "Standard_LeadTime", label: "Standard LeadTime", value: "' . $RTF["Standard_LeadTime"] . '"},
                        {type: "input", name: "Finishing_Difficult_Rate", label: "Finishing Diff Rate", value: "' . $RTF["Finishing_Difficult_Rate"] . '"},
                    ]}
                ]}
            ]},
            {type: "block", width: "auto", blockOffset: 20, offsetLeft:20, offsetTop: 15, list: [
                {type: "settings", inputWidth: "350"},
                {type: "input", name: "Process", labelWidth: "100", offsetTop: 6, label: "Process", value: "' . $RTF["Process"] . '"},
                {type: "input", name: "StringCut_ComboTag", labelWidth: "100", offsetTop: 6, label: "String Cut/ Combo Tag", value: "' . $RTF["StringCut_ComboTag"] . '"},
                {type: "input", name: "Inactive_Reason", labelWidth: "100", offsetTop: 6, label: "Inactive Reason", value: "' . $RTF["Inactive_Reason"] . '"},
                
                {type: "newcolumn"},
                {type: "button", name: "DetailProcess", label: "Detail", offsetTop: 0, value: "Detail"}
            ]},
            {type: "newcolumn"},
            {type: "block", width: "auto", blockOffset: 20, offsetLeft:20, offsetTop: 15, list: [
                {type: "settings", inputWidth: "300"},
                {type: "checkbox", name: "CheckReplaceMaterial", label: "Vật tư thay thế", checked: "' . $RTF["CheckReplaceMaterial"] . '"},
                {type: "container", value: "", name: "Accessory_Information", labelWidth: 200 ,inputHeight: "160", inputWidth: "390", label: "Accessory Information", position: "label-top"},
                {type: "combo", name: "Social_Compliance", label: "Social Compliance", filtering: true, options:[';
                                foreach($Social_Compliance as $Rows)
                                {
                                    $StringJsonMain = $StringJsonMain . '{text: "' . $Rows . '", value: "' . $Rows . '"},';
                                }
        $StringJsonMain = $StringJsonMain .    ']},{type: "calendar", dateFormat: "%Y-%m-%d %H:%i:%s", enableTime: "true", name: "PE_Receive_Date", label: "PE Recieve Date", value: "' . $RTF["PE_Receive_Date"] . '"},
                {type: "calendar", dateFormat: "%Y-%m-%d %H:%i:%s", enableTime: "true", name: "Setup_Date", label: "Setup Date", value: "' . $RTF["Setup_Date"] . '"},
                {type: "calendar", dateFormat: "%Y-%m-%d %H:%i:%s", enableTime: "true", name: "Ready_Date", label: "Ready Date <button onclick=\'ReadyNow()\'>N</button>", value: "' . $RTF["Ready_Date"] . '", labelWidth: 100, inputWidth: 290},
                {type: "input", name: "PE_Name", label: "PE Name", value: "' . $RTF["PE_Name"] . '"},
                {type: "calendar", dateFormat: "%Y-%m-%d %H:%i:%s", enableTime: "true", name: "Last_Revise_Date", label: "Last Revise Date", value: "' . $RTF["Last_Revise_Date"] . '"},
                {type: "input", name: "Revise_People", label: "Revise People", value: "' . $RTF["Revise_People"] . '"},
                {type: "combo", name: "Status", label: "Status", filtering: true, options:[';
                    foreach($StatusArr as $Rows)
                    {
                        if($Rows["value"] == $RTF["Status"])
                        {
                            $StringJsonMain = $StringJsonMain . '{text: "' . $Rows["text"] . '", value: "' . $Rows["value"] . '", selected: true},';
                        } else {
                            $StringJsonMain = $StringJsonMain . '{text: "' . $Rows["text"] . '", value: "' . $Rows["value"] . '"},';
                        }
                    }
        $StringJsonMain = $StringJsonMain .    ']},
                {type: "calendar", dateFormat: "%Y-%m-%d %H:%i:%s", enableTime: "true", name: "Last_Order_Time", label: "Last Order Time", value: "' . $RTF["Last_Order_Time"] . '"}
            ]}
        ];';
        $DataMain["Main"] = $StringJsonMain;
        echo json_encode($DataMain);
        // echo $StringJsonMain;
    } else if(isset($_GET["EVENT"]) && $_GET["EVENT"] == "FORMMAINCREATE")
    {
        $GLID = $_GET["GLID"];
        $Selection = "GLID,
        Item_Code,
        Buying_Office,
        Fit_Variable,
        Production_Type,
        Production_Line,
        DS_Sample,
        OS_Sample,
        ProductionWidth,
        ProductionLength,
        Sheet_Size,
        Stock_Code,
        Color_F,
        Color_B,
        Color_FQ,
        Color_BQ,
        Varnish_F,
        Varnish_B,
        Imprint_F,
        Imprint_B,
        Offset_Level,
        Offset_Imp_Front,
        Offset_Imp_Back,
        Offset_UPS,
        Offset_Cut_No,
        Digital_Level,
        Digital_F_Click,
        Digital_B_Click,
        Digital_UPS,
        Digital_Cut_No,
        Digital_Sheet_Size,
        Digital_Stock_Code_F,
        Digital_DieCut_No,
        Digital_Availability,
        Hot_Folder,
        Variable_F,
        Variable_B,
        DieCut_Machine,
        DieCut_No,
        Suited_Machine,
        Digital_Machine,
        Special_Instruction,
        Crocking_Test,
        SubContract,
        SubContract_Detail,
        Process,
        Special_Drying_Time,
        Standard_LeadTime,
        Hole,
        UV_F,
        UV_B,
        Active,
        Color_Management,
        CS_Sample,
        Last_Order_Time,
        Last_Revise_Date,
        Original_System,
        PE_Name,
        PE_Receive_Date,
        Ready_Date,
        Revise_People,
        Scrap_Adjustment,
        Social_Compliance,
        Status,
        Setup_Date,
        Offset_Extra_Time,
        Offset_Waiting_Drying,
        Digital_Extra_Time,
        Digital_Waiting_Drying,
        FSC,
        Finishing_Difficult_Rate";
        $RowTable = MiQuery( "SELECT $Selection FROM $table_information WHERE GLID = '$GLID';", _conn());

        if(count($RowTable) != 0) 
        {
            $RTF = $RowTable[0];
            foreach(explode(",",$Selection) as $R)
            {
                $RTF[str_replace(["\r","\n", " "],"",$R)] = str_replace("\"","\\\"",str_replace(["\r","\n"],"\\n",$RTF[str_replace(["\r","\n", " "],"",$R)]));
            }
        } else
        {
            foreach(explode(",",$Selection) as $R)
            {
                $RTF[str_replace(["\r","\n", " "],"",$R)] = "";
            }
        }

        $FGSQty = MiQueryScalar("SELECT SUM(FGS_IN_OUT) AS FGS FROM $table_fgs_data WHERE GLID = '$GLID';",_conn());
        if($FGSQty == "") $FGSQty = "0";

        $StringJsonMain = '[
            {type: "settings", position: "label-left", labelAlign: "right", labelWidth: 90, inputWidth: 130},
            {type: "block", width: "auto", blockOffset: 10, list: [
                {type: "block", width: "auto", blockOffset: 20, list: [
                    {type: "block", width: "auto", blockOffset: "", list: [
                        {type: "settings", labelWidth: "120"},
                        {type: "input", name: "GLID", label: "<a style=\'background:#993300; font-weight: bold; color: white;padding:5px\'>GLID</a>", value: "' . $RTF["GLID"] . '", readonly: true},
                        {type: "input", name: "Item_Code", label: "<a style=\'background:#993300; font-weight: bold; color: white;padding:5px\'>Item Code</a>", value: "' . $RTF["Item_Code"] . '", readonly: true},
                        {type: "input", name: "Buying_Office", label: "<a style=\'background:#993300; font-weight: bold; color: white;padding:5px\'>Buying Office</a>", value: "' . $RTF["Buying_Office"] . '", readonly: true},
                        {type: "input", name: "Color_F", label: "<a style=\'background:#993300; font-weight: bold; color: white;padding:5px\'>Color F</a>", value: "' . $RTF["Color_F"] . '", rows: "2", readonly: true},
                        {type: "input", name: "Color_FQ", label: "<a style=\'background:#993300; font-weight: bold; color: white;padding:5px\'>Color FQ</a>", value: "' . $RTF["Color_FQ"] . '", readonly: true},
                        {type: "input", name: "Variable_F", label: "<a style=\'background:#993300; font-weight: bold; color: white;padding:5px\'>Variable F</a>", value: "' . $RTF["Variable_F"] . '", readonly: true},
                        {type: "input", name: "Imprint_F", label: "<a style=\'background:#993300; font-weight: bold; color: white;padding:5px\'>Imprint F</a>", value: "' . $RTF["Imprint_F"] . '", readonly: true},
                        {type: "input", name: "Varnish_F", label: "<a style=\'background:#993300; font-weight: bold; color: white;padding:5px\'>Varnish F</a>", value: "' . $RTF["Varnish_F"] . '", readonly: true},
                        {type: "input", name: "FSC", label: "<a style=\'background:#993300; font-weight: bold; color: white;padding:5px\'>FSC</a>", value: "' . $RTF["GLID"] . '", readonly: true},
                        {type: "checkbox", name: "SubContract", label: "<a style=\'background:#993300; font-weight: bold; color: white;padding:5px\'>Sub Contract</a>", checked: "' . $RTF["SubContract"] . '", readonly: true}
                    ]},
                    {type: "newcolumn"},
                    {type: "block", width: "auto", blockOffset: 20, list: [
                        {type: "settings", labelWidth: "120"},
                        {type: "checkbox", name: "Active", label: "<a style=\'background:#993300; font-weight: bold; color: white;padding:5px\'>Active</a>", checked: "' . $RTF["Active"] . '", readonly: true},
                        {type: "checkbox", name: "Hole", label: "<a style=\'background:#993300; font-weight: bold; color: white;padding:5px\'>Hole</a>", checked: "' . $RTF["Hole"] . '", readonly: true},
                        {type: "input", name: "Original_System", label: "<a style=\'background:#993300; font-weight: bold; color: white;padding:5px\'>Original System</a>", value: "' . $RTF["Original_System"] . '", readonly: true},
                        {type: "input", name: "Color_B", label: "<a style=\'background:#993300; font-weight: bold; color: white;padding:5px\'>Color B</a>", value: "' . $RTF["Color_B"] . '", rows: "2", readonly: true},
                        {type: "input", name: "Color_BQ", label: "<a style=\'background:#993300; font-weight: bold; color: white;padding:5px\'>Color BQ</a>", value: "' . $RTF["Color_BQ"] . '", readonly: true},
                        {type: "input", name: "Variable_B", label: "<a style=\'background:#993300; font-weight: bold; color: white;padding:5px\'>Variable B</a>", value: "' . $RTF["Variable_B"] . '", readonly: true},
                        {type: "input", name: "Imprint_B", label: "<a style=\'background:#993300; font-weight: bold; color: white;padding:5px\'>Imprint B</a>", value: "' . $RTF["Imprint_B"] . '", readonly: true},
                        {type: "input", name: "Varnish_B", label: "<a style=\'background:#993300; font-weight: bold; color: white;padding:5px\'>Varnish B</a>", value: "' . $RTF["Varnish_B"] . '", readonly: true},
                        {type: "input", name: "FGS_Qty", style: "background:black;color:white;font-weight:bold;text-align:center;", label: "<a style=\'background:#6F3198; font-weight: bold; color: white;padding:5px\'>FGS Qty</a>", value: "' . round($FGSQty) . '", readonly: true},
                        {type: "input", name: "Last_Order_Time", label: "<a style=\'background:#993300; font-weight: bold; color: white;padding:5px\'>Last Order Time</a>", value: "' . $RTF["Last_Order_Time"] . '", readonly: true}
                    ]}
                ]},
                {type: "block", width: "auto", blockOffset: 20, list: [
                    {type: "settings", inputWidth: "404", labelWidth: 120},
                    {type: "input", name: "SubContract_Detail", label: "<a style=\'background:#993300; font-weight: bold; color: white;padding:10px\'>Sub Contract <br/><br/>Details</a>", value: "' . $RTF["SubContract_Detail"] . '", rows: "6", readonly: true},
                    {type: "input", name: "Process", label: "<a style=\'background:#3369FF; font-weight: bold; color: white;padding:5px\'>Process", value: "' . $RTF["Process"] . '"}
                ]}
            ]},
            {type: "block", width: "auto", blockOffset: 10, list: [
                {type: "block", width: "auto", blockOffset: 10, list: [
                    {type: "block", width: "auto", blockOffset: 10, list: [
                        {type: "settings", labelWidth: "120"},
                        {type: "input", label: "<a style=\'background:#008000; font-weight: bold; color: white;padding:10px\'>Offset</a>", value: "", readonly: true},
                        {type: "input", name: "Stock_Code", label: "<a style=\'background:#008000; font-weight: bold; color: white;padding:10px\'>Material Code</a>", value: "' . $RTF["Stock_Code"] . '", readonly: true},
                        {type: "input", name: "Sheet_Size", label: "<a style=\'background:#008000; font-weight: bold; color: white;padding:10px\'>Sheet Size</a>", value: "' . $RTF["Sheet_Size"] . '", readonly: true},
                        {type: "input", name: "Offset_UPS", label: "<a style=\'background:#008000; font-weight: bold; color: white;padding:10px\'>Offset UPS</a>", value: "' . $RTF["Offset_UPS"] . '", readonly: true},
                        {type: "input", name: "Offset_Cut_No", label: "<a style=\'background:#008000; font-weight: bold; color: white;padding:10px\'>Offset Cut No</a>", value: "' . $RTF["Offset_Cut_No"] . '", readonly: true},
                        {type: "input", name: "Offset_Imp_Front", label: "<a style=\'background:#008000; font-weight: bold; color: white;padding:5px\'>Offset Imp Front</a>", value: "' . $RTF["Offset_Imp_Front"] . '", readonly: true},
                        {type: "input", name: "Offset_Imp_Back", label: "<a style=\'background:#008000; font-weight: bold; color: white;padding:10px\'>Offset Imp  Back</a>", value: "' . $RTF["Offset_Imp_Back"] . '", readonly: true},
                        {type: "input", name: "Offset_Level", label: "<a style=\'background:#008000; font-weight: bold; color: white;padding:10px\'>Offset Level</a>", value: "' . $RTF["Offset_Level"] . '", readonly: true},
                        {type: "input", name: "OS_Sample", label: "<a style=\'background:#008000; font-weight: bold; color: white;padding:10px\'>OS Sample</a>", value: "' . $RTF["OS_Sample"] . '", readonly: true},
                        {type: "input", name: "Suited_Machine", label: "<a style=\'background:#008000; font-weight: bold; color: white;padding:10px\'>Suited Machine</a>", value: "' . $RTF["Suited_Machine"] . '", readonly: true},
                        {type: "input", name: "Scrap_Adjustment", label: "<a style=\'background:#008000; font-weight: bold; color: white;padding:1px\'>Printing Different</a>", value: "' . $RTF["Scrap_Adjustment"] . '", readonly: true}
                    ]},
                    {type: "newcolumn"},
                    {type: "block", width: "auto", blockOffset: 20, list: [
                        {type: "settings", labelWidth: "120"},
                        {type: "input", name: "Digital_Availability", label: "<a style=\'background:#008000; font-weight: bold; color: white;padding:10px\'>Digital Available</a>", value: "' . $RTF["Digital_Availability"] . '", readonly: true},
                        {type: "input", name: "Digital_Stock_Code_F", label: "<a style=\'background:#008000; font-weight: bold; color: white;padding:10px\'>Dig. Material</a>", value: "' . $RTF["Digital_Stock_Code_F"] . '", readonly: true},
                        {type: "input", name: "Digital_Sheet_Size", label: "<a style=\'background:#008000; font-weight: bold; color: white;padding:10px\'>Dig. Sheet Size</a>", value: "' . $RTF["Digital_Sheet_Size"] . '", readonly: true},
                        {type: "input", name: "Digital_UPS", label: "<a style=\'background:#008000; font-weight: bold; color: white;padding:10px\'>Dig. UPS</a>", value: "' . $RTF["Digital_UPS"] . '", readonly: true},
                        {type: "input", name: "Digital_Cut_No", label: "<a style=\'background:#008000; font-weight: bold; color: white;padding:10px\'>Dig. Cut No</a>", value: "' . $RTF["Digital_Cut_No"] . '", readonly: true},
                        {type: "input", name: "Digital_F_Click", label: "<a style=\'background:#008000; font-weight: bold; color: white;padding:10px\'>Dig. F Click</a>", value: "' . $RTF["Digital_F_Click"] . '", readonly: true},
                        {type: "input", name: "Digital_B_Click", label: "<a style=\'background:#008000; font-weight: bold; color: white;padding:10px\'>Dig. B Click</a>", value: "' . $RTF["Digital_B_Click"] . '", readonly: true},
                        {type: "input", name: "Digital_Level", label: "<a style=\'background:#008000; font-weight: bold; color: white;padding:10px\'>Digital Level</a>", value: "' . $RTF["Digital_Level"] . '", readonly: true},
                        {type: "input", name: "DS_Sample", label: "<a style=\'background:#008000; font-weight: bold; color: white;padding:10px\'>DS Sample</a>", value: "' . $RTF["DS_Sample"] . '", readonly: true},
                        {type: "input", name: "Digital_Machine", label: "<a style=\'background:#008000; font-weight: bold; color: white;padding:10px\'>Dig. Machine</a>", value: "' . $RTF["Digital_Machine"] . '", readonly: true},
                        {type: "input", name: "Finishing_Difficult_Rate", label: "<a style=\'background:#008000; font-weight: bold; color: white;padding:1px\'>Standard LeadTime</a>", value: "' . $RTF["Finishing_Difficult_Rate"] . '", readonly: true}
                    ]}
                ]},
                {type: "block", width: "auto", blockOffset: 20, list: [
                    {type: "settings", inputWidth: "520", labelWidth: 200},
                    {type: "container", name: "Accessory_Information", inputHeight: "120", label: "Accessory Information", position: "label-top"}
                ]}
            ]}
        ]';
        $DataArr = array();
        $DataArr["PL"] = $RTF["Production_Line"];
        $DataArr["Main"] = $StringJsonMain;
        echo json_encode($DataArr);
    }

    function AddSelection($Data,$Value)
    {
        foreach($Data as $K=>$R)
        {
            if(strtolower($R["value"]) == strtolower($Value))
            {
                $Data[$K]["selected"] = "true";
            }
        }
        return $Data;
    }

?>