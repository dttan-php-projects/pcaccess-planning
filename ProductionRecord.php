<?php 
    require("./Module/Template.php");
    InitPage("PCProductionRecord","Production Record - Offset & Digital");
?>
<style>
.dhxcombo_option{
    font-weight:Bold;
    color:Red;
}
</style>
<script>
    var LayoutMain;
    var DataPEForm;
    var AccessoryGrid,GridProcess;
    var JobJacket = "";
    var Customer_Item = "";
    JobJacket = getUrl("JJ");
    var CopyClipBoard;
    document.addEventListener('paste', function (event) {
        CopyClipBoard = event.clipboardData.getData('Text');
    });

    async function AddClipBoard(){
        var clipText = CopyClipBoard;
        var RowData = clipText.split("\r\n");
        var newId = (new Date()).valueOf();
        for(var i = 0; i < RowData.length; i++){
            newId = (new Date()).valueOf();
            GridSO.addRow(newId,"<a style='color:red;font-weight:bold'>X</a>," + RowData[i].replaceAll("\t",","));
        }
        GridSO.sortRows(1,"str","desc");
    }


    function SubmitJobJacket(){
        if(DataPEForm.getItemValue("Print_Machine",true) != "String only") {
            if(DataPEForm.getItemValue("Order_Style",true) == "FG" || DataPEForm.getItemValue("Order_Style",true).indexOf("Digital") !== -1) {
                if(DataPEForm.getItemValue("Urgent_Status",true) == "NORMAL" || DataPEForm.getItemValue("Urgent_Status",true) == "URGENT" || DataPEForm.getItemValue("Urgent_Status",true) == "FG") {
                    if(DataPEForm.getItemValue("Print_Sheet",true) == "") {
                        dhtmlx.alert("Vui lòng tính scrap để hoàn thành đơn hàng");
                        return;
                    }  
                }
            }
        }
        
        var DataTable = {"EVENT":"UPDATEDATAJJ"};
        var DataGrid = "";
        GridSO.forEachRow(function(id){
            if(GridSO.cells(id,1).getValue() != "") {
                DataGrid += "|" + GridSO.cells(id,1).getValue() + 
                            "*" + GridSO.cells(id,4).getValue() + 
                            "*" + GridSO.cells(id,6).getValue(); 
            }
        });

        var LotPrint = "";
        GridLot.forEachRow(function(id){
            if(GridLot.cells(id,1).getValue() != "") {
                LotPrint += "|" + GridLot.cells(id,0).getValue() + "*" + GridLot.cells(id,1).getValue();
            }
        });

        

        DataTable.MAIN = JSON.stringify(DataPEForm.getFormData());
        DataTable.SOLine = DataGrid;
        DataTable.Lot = LotPrint;

        // console.log("UPDATE JJ: " + JSON.stringify(DataTable));

        var Result = AjaxAsync("Event.php",DataTable,"POST");
        location.href = "PrintPages.php?JJ=" + ToolbarMain.getValue("JobJacket").trim() + "&P=1";
    }


    function DocumentStart()
    {
        $(window).keydown(function(event) {
            if(event.ctrlKey && event.keyCode == 32) { 
                CalculateScrap();
            } else if(event.ctrlKey && event.keyCode == 13) { 
                SubmitJobJacket();
            } else if(event.ctrlKey && event.keyCode == 76) { 
                GridLot.selectCell(0,1,false,false,true,true);
                window.setTimeout(function(){
                    $(".dhx_combo_edit").select();
                },100);
                event.preventDefault(); 
            } else if(event.ctrlKey && event.keyCode == 49) {
                Cal();
                event.preventDefault(); 
            } else if(event.ctrlKey && event.keyCode == 50) {
                CalConfirm();
                event.preventDefault(); 
            } else if(event.ctrlKey && event.keyCode == 68) {
                event.preventDefault(); 
            }
        });

        LayoutMain = new dhtmlXLayoutObject({
            parent: document.body,
            pattern: "1C",
            offsets: {
                top: 65
            },
            cells: [
                {id: "a", header: false, text: "Control Panel"}
            ]
        });
        if(JobJacket == null) JobJacket = "";
        JobJacket = JobJacket.toUpperCase();

        ToolbarMain.addText("text", null, "JobJacket: ");
        ToolbarMain.addInput("JobJacket", null, JobJacket, 200);
        ToolbarMain.addButton("Find", null, "Find Item", "save.gif");
        ToolbarMain.addButton("Save", null, "<a style='font-size:14pt'>Submit Print</a>", "save.gif");
        ToolbarMain.addButton("SaveWithout", null, "Save", "save.gif");
        ToolbarMain.addButton("Print", null, "View", "save.gif");
        ToolbarMain.addButton("PrintWithout", null, "Without Checklist", "save.gif");
        ToolbarMain.addButton("DeleteJob", null, "Delete Job", "save.gif");

        ToolbarMain.attachEvent("onEnter", function(id, value) {
            if (id == "JobJacket") {
                window.location = "?JJ=" + ToolbarMain.getValue("JobJacket").trim();
            }
        });
        ToolbarMain.attachEvent("onClick", function(id) {
            if (id == "JobJacket") {
                window.location = "?JJ=" + ToolbarMain.getValue("JobJacket").trim();
            } else if (id == "Print") {
                location.href = "PrintPages.php?JJ=" + ToolbarMain.getValue("JobJacket").trim();
            } else if (id == "PrintWithout") {
                location.href = "PrintPages.php?JJ=" + ToolbarMain.getValue("JobJacket").trim() + "&P=1&CL=false";
            } else if(id == "Save") {
                SubmitJobJacket();
            } else if(id == "SaveWithout") {
                var DataTable = {"EVENT":"UPDATEDATAJJ"};
                var DataGrid = "";
                GridSO.forEachRow(function(id){
                    if(GridSO.cells(id,1).getValue() != "") {
                        DataGrid += "|" + GridSO.cells(id,1).getValue() + 
                                    "*" + GridSO.cells(id,4).getValue() + 
                                    "*" + GridSO.cells(id,6).getValue(); 
                    }
                });

                var LotPrint = "";
                GridLot.forEachRow(function(id){
                    if(GridLot.cells(id,1).getValue() != "") {
                        LotPrint += "|" + GridLot.cells(id,0).getValue() + "*" + GridLot.cells(id,1).getValue();
                    }
                });

                DataTable.MAIN = JSON.stringify(DataPEForm.getFormData());
                DataTable.SOLine = DataGrid;
                DataTable.Lot = LotPrint;
                var Result = AjaxAsync("Event.php",DataTable,"POST");
                dhtmlx.alert("Đã Submit");
            } else if(id == "DeleteJob") {
                if(prompt("Huynh đài muốn xóa đơn ah") == ToolbarMain.getValue("JobJacket").trim())
                {
                    var DataTable = {"EVENT":"DELETEJJ"};
                    DataTable.JJ = ToolbarMain.getValue("JobJacket").trim();
                    var DeleteJob = AjaxAsync("Event.php",DataTable,"POST","HTML");
                    window.location = "ProductionRecord.php"
                } else {
                    dhtmlx.alert("Vui lòng xóa đơn đang xem.");
                }
            }
        });
        InitForm();
    }
    var GridSO,GridLot,FormScrap;

    function CalConfirm(){
        if(!dhxWins.isWindow("WindowsDetail")) return;
        var UPS = FormScrap.getItemValue("UPS", true);
        var OrderQty = DataPEForm.getItemValue("Order_Quantity",true);
        var PrintMachine = FormScrap.getItemValue("SuitedMachine", true);
        var ColorFQ = FormScrap.getItemValue("Color_F", true);
        var ColorBQ = FormScrap.getItemValue("Color_B", true);
        var ImprintF = FormScrap.getItemValue("ImprintF", true);
        var BO = FormScrap.getItemValue("BO", true);
        var ImprintF = FormScrap.getItemValue("ImprintB", true);
        var ColorMax = 1;
        var Side = 1
        var Lot = 0;
        var FDRx = 0;
        var PDRx = 0;
        var LotsScrap = 0;
        var Machinestopfrequence = 2000;
        var Machinestopallowance = 5;
        var QualityCheckfrequence = 300;
        var QualityCheckallowance = 1;
        var Setupbaseline = 0;
        var L500 = 0;
        var O500 = 0;
        var Scrapmax = 30;
        var PrintingScrap = 0;
        var FinishScrap = 0;
        var Runs = 1.0;
        var ColorManagement = FormScrap.getItemValue("ColorManagement", true);
        DataPEForm.setItemValue("Print_Scrap", Math.round(FormScrap.getItemValue("PrintScrap",true)));
        DataPEForm.setItemValue("Finish_Scrap", Math.round(FormScrap.getItemValue("FinishScrap",true)));
        DataPEForm.setItemValue("Color_By_Size", parseInt(FormScrap.getItemValue("SizeColorF",true)) + parseInt(FormScrap.getItemValue("SizeColorB",true)));
        DataPEForm.setItemValue("Imprint_Lot", parseInt(FormScrap.getItemValue("ImprintF",true)) + parseInt(FormScrap.getItemValue("ImprintB",true)));
        DataPEForm.setItemValue("Color_Sum_FB", FormScrap.getItemValue("ColorSum",true));
        DataPEForm.setItemValue("Print_Machine", FormScrap.getItemValue("SuitedMachine",true));
        if(PrintMachine == "WS6800") {
            DataPEForm.setItemValue("ActualPCS", parseInt(PrintSheet * UPS * 1000/parseInt(DataPEForm.getItemValue("Stock_Size",true))));
        } else {
            DataPEForm.setItemValue("ActualPCS", parseInt(PrintSheet * UPS));
        }					
        // DataPEForm.setItemValue("OverRate", Math.round((PrintSheet * UPS - OrderQty)*10000/OrderQty)/100 + "%");
        DataPEForm.setItemValue("OverRate", (Math.round(DataPEForm.getItemValue("ActualPCS",true)) / Math.round(DataPEForm.getItemValue("Order_Quantity",true)) - 1)*100 + "%");
        
        if(parseFloat(DataPEForm.getItemValue("OverRate",true)) < 0) dhtmlx.alert("Mọi người ơi, mọi người ơi Over Rate đang âm kìa");
        if((PrintSheet * UPS - OrderQty)*10000/OrderQty > 5000) dhtmlx.alert("Mọi người ơi, mọi người ơi Over Rate đang gấp đôi kìa" + ((PrintSheet * UPS - OrderQty)*10000/OrderQty))

        DataPEForm.setItemValue("ScrapRate", Math.round((parseInt(FormScrap.getItemValue("PrintScrap",true)) + parseInt(FormScrap.getItemValue("FinishScrap",true)))*10000/PrintSheet)/100 + "%");

        var ArrRout = [];
        GridProcess.forEachRow(function(id){
            if(GridProcess.cells(id,1).getValue() != ""){
                ArrRout.push({
                    "SEQ": GridProcess.cells(id,1).getValue(),
                    "Process": GridProcess.cells(id,2).getValue().replaceAll("&amp;","&"),
                    "PFD": GridProcess.cells(id,3).getValue()
                });
            }
        });
        // console.log("ArrRout: "+ JSON.stringify(ArrRout) );
        var DataTable = {"EVENT":"UPDATEROUTING"};
        DataTable.MAIN = JSON.stringify(ArrRout);
        DataTable.JobJacket = JobJacket;
        // console.log("DataTable: "+ JSON.stringify(DataTable) );
        var Result = AjaxAsync("Event.php",DataTable,"POST");

        doOnUnload();
        doOnLoad();
    }

    function Cal(){
        
        if(!dhxWins.isWindow("WindowsDetail")) return;
        var UPS = FormScrap.getItemValue("UPS", true);
        var OrderQty = DataPEForm.getItemValue("Order_Quantity",true);
        var PrintMachine = FormScrap.getItemValue("SuitedMachine", true);
        var ColorFQ = FormScrap.getItemValue("Color_F", true);
        var ColorBQ = FormScrap.getItemValue("Color_B", true);
        var ImprintF = FormScrap.getItemValue("ImprintF", true);
        var BO = FormScrap.getItemValue("BO", true);
        var ImprintF = FormScrap.getItemValue("ImprintB", true);
        var ColorMax = 1;
        var Side = 1
        var Lot = 0;
        var FDRx = 0;
        var PDRx = 0;
        var LotsScrap = 0;
        var Machinestopfrequence = 2000;
        var Machinestopallowance = 5;
        var QualityCheckfrequence = 300;
        var QualityCheckallowance = 1;
        var Setupbaseline = 0;
        var L500 = 0;
        var O500 = 0;
        var Scrapmax = 30;
        var PrintingScrap = 0;
        var FinishScrap = 0;
        var Runs = 1.0;
        var ColorManagement = FormScrap.getItemValue("ColorManagement", true);

        // Tại đây bỏ đi máy INKJET, do máy INKJET tính PrintScrap khác
        var PMDigitalArr = ['INDIGO', 'C800P', 'G-U', 'C1000I', 'NON G-U', 'KM1', '1', '4', '5', '6', '2,3,7', 'ALL'];
        
        if(parseInt(FormScrap.getItemValue("Color_F", true)) > parseInt(FormScrap.getItemValue("Color_B", true))) ColorMax = FormScrap.getItemValue("Color_F", true); 
        else ColorMax = FormScrap.getItemValue("Color_B", true);

        if(parseInt(FormScrap.getItemValue("Color_F", true)) * parseInt(FormScrap.getItemValue("Color_B", true)) != 0) Side = 2;

        Lot = parseInt(FormScrap.getItemValue("ImprintF", true)) + parseInt(FormScrap.getItemValue("ImprintB", true));
        if(Lot > 0) Lot = Lot - 1;
        if(FormScrap.getItemValue("FDR", true) == 0 || FormScrap.getItemValue("FDR", true) == "") {
            // alert("Scrap adjustment chưa được setup bởi technology team, Hệ thống sẽ nhập 1, Vui lòng liên hệ với Technical Team để Setup sau");
            FDRx = 1;
        } else FDRx = FormScrap.getItemValue("FDR", true)

        if(FormScrap.getItemValue("PDR", true) == 0 || FormScrap.getItemValue("PDR", true) == "") {
            // alert("Finishing difficult chưa được setup bởi technology team, Hệ thống sẽ nhập 1, Vui lòng liên hệ với Technical Team để Setup sau");
            PDRx = 1;
        } else PDRx = FormScrap.getItemValue("PDR", true)

        // @tandoan - 20211027: Thêm các máy 1, 4, 5, 6, (2,3,7), ALL. mail: Re: Alignment to split order among PP/PPC/Operation
            // if (PMDigitalArr.indexOf(PrintMachine) ) {        
        if(PrintMachine == "INDIGO" || PrintMachine == "C800P" || PrintMachine == "G-U" || PrintMachine == "C1000i" || PrintMachine == "NON G-U" || PrintMachine == "KM1" || PrintMachine == "1" || PrintMachine == "4" || PrintMachine == "5" || PrintMachine == "6" || PrintMachine == "2,3,7" || PrintMachine.toUpperCase() == "ALL" ) {
        
            $.ajax({
                url: 'Process.php',
                type: 'GET',
                dataType: 'json',
                cache: false,
                data: {
                        'EVENT': "LOADITEMSETUP",
                        'GLID': DataPEForm.getItemValue("GLID",true),
                        'COLORMAX': ColorMax,
                        'SIDE': Side
                    },
                success: function(Data){
                    Data = Data[0];
                    Setupbaseline = parseInt(Data["Setup_Baseline"]);
                    K500 =  parseInt(Data["0-50"]);
                    L500 =  parseInt(Data["0-500"]);
                    O500 =  parseFloat(Data[">500"])/100.0;
                    Scrapmax =  parseInt(Data["Max"]);
                },
                error: function (){
                    alert('ERROR');
                },
                async: false
            }); 

            if(PrintSheet > 500) {
                if(L500 + parseInt(PrintSheet * O500) >= Scrapmax) PrintingScrap = Setupbaseline + Scrapmax; 
                else PrintingScrap = Setupbaseline + L500 + parseInt(PrintSheet * O500);
            } else if(PrintSheet > 0 && PrintSheet < 51) PrintingScrap = Setupbaseline + K500;
            else if(PrintSheet > 50 && PrintSheet < 501) PrintingScrap = Setupbaseline + L500;

            if (ColorManagement.toUpperCase().indexOf("MELLOW") !== -1) PrintingScrap = PrintingScrap + 10;
            if ( (ColorManagement.toUpperCase().indexOf("GMI") !== -1) || (ColorManagement.toUpperCase().indexOf("COLORCERT") !== -1) ) {
                // @TanDoan - 20211227: Cập nhật COLORCERT theo mail: "Re: Update chuẩn so màu/cách tính bù hao submit GMI <=> COLORCERT/LAB"
                PrintingScrap = PrintingScrap + Lot*6;
            }  
            if(Lot > 0 && (BO == "RYOHIN KEIKAKU" || BO == "MUJI")) PrintingScrap = PrintingScrap + (Lot + 1)*2;
        } else if (PrintMachine == "WS6800") {
            PrintingScrap = 36; 
        } 
        else if (PrintMachine == "Die-cut only" || PrintMachine == "String only" || PrintMachine == "ABG" || PrintMachine.toUpperCase() == "INKJET") PrintingScrap = 0;
        else {
            if(FormScrap.getItemValue("Runs", true) == 0) {
                alert("Runs can't be null or zero \n Runs không có được rỗng nha bà kon");
                return;
            } else {
                Runs = parseInt(FormScrap.getItemValue("Runs", true));
            }

            $.ajax({
                url: 'Process.php',
                type: 'GET',
                dataType: 'json',
                cache: false,
                data: {
                        'EVENT': "LOADITEMSETUPOFFSET",
                        'GLID': DataPEForm.getItemValue("GLID",true),
                        'COLORMAX': ColorMax,
                        'SIDE': Side
                    },
                success: function(data){
                    var Data = data[0];
                    LotsScrap = parseInt(Data["Lot_Scrap"]);
                    Setupbaseline = parseInt(Data["Setup_Baseline"]);
                    Machinestopfrequence = parseInt(Data["Machine_Stop_Frequence"]);
                    Machinestopallowance = parseInt(Data["Machine_Stop_Allowance"]);
                    QualityCheckfrequence = parseInt(Data["Quality_Check_Frequence"]);
                    QualityCheckallowance = parseInt(Data["Quality_Check_Allowance"]);
                },
                error: function (){
                    alert('ERROR');
                },
                async: false
            }); 
            if(PrintSheet * Runs > 2000) {
                PrintingScrap = Setupbaseline + Lot * LotsScrap * PDRx + (PrintSheet * Runs * QualityCheckallowance/QualityCheckfrequence) + (PrintSheet * Runs - 2000) * Machinestopallowance/Machinestopfrequence;
            } else {
                PrintingScrap = Setupbaseline + Lot * LotsScrap * PDRx + (PrintSheet * Runs * QualityCheckallowance/QualityCheckfrequence)
            }


            if(FormScrap.isItemChecked("Mellow") == true || ColorManagement.substring(0,6) == "Mellow") PrintingScrap = PrintingScrap + 100;
            else if ( (ColorManagement.substring(0,3) == "GMI") || (ColorManagement.substring(0,3) == "COLORCERT") ) {
                // @TanDoan - 20211227: Cập nhật COLORCERT theo mail: "Re: Update chuẩn so màu/cách tính bù hao submit GMI <=> COLORCERT/LAB"
                PrintingScrap = PrintingScrap + 60;
            } 
            
            if (ColorManagement.toUpperCase().indexOf("MELLOW") !== -1) PrintingScrap = PrintingScrap + 100;
            if ( (ColorManagement.toUpperCase().indexOf("GMI") !== -1) || (ColorManagement.toUpperCase().indexOf("COLORCERT") !== -1) ) {
                // @TanDoan - 20211227: Cập nhật COLORCERT theo mail: "Re: Update chuẩn so màu/cách tính bù hao submit GMI <=> COLORCERT/LAB"
                PrintingScrap = PrintingScrap + Lot*6;
            }  
        };

        FormScrap.setItemValue("PrintScrap", Math.round(PrintingScrap));

        //Calculate FinishScrap
        var FinishScrap = 0;
        var PlanDate = new Date(FormScrap.getItemValue("DueDay", true));
        var Quantity = DataPEForm.getItemValue("Order_Quantity",true);
        var ProcessGrid = "";
        var X500 = "";
        var X501 = "";
        var X2001 = "";
        var X5000 = "";
        GridProcess.forEachRow(function(id){
            ProcessGrid = GridProcess.cells(id,2).getValue();
            X500 = parseInt(GridProcess.cells(id,6).getValue());
            X501 = parseInt(GridProcess.cells(id,7).getValue());
            X2001 = parseInt(GridProcess.cells(id,8).getValue());
            X5000 = parseFloat(GridProcess.cells(id,9).getValue());
            if(ProcessGrid == "Die cutting" && PrintMachine == "WS6800") {
                FinishScrap = FinishScrap + 4;
            } else {
                if(PrintSheet > 5000) FinishScrap = FinishScrap + X2001 + Math.round((PrintSheet - 5000) * X5000 + 0.499999);
                else if(PrintSheet < 501) FinishScrap = FinishScrap + X500;
                else if(PrintSheet > 500 && PrintSheet < 2001) FinishScrap = FinishScrap + X501;
                else if(PrintSheet > 2000 && PrintSheet < 5001) FinishScrap = FinishScrap + X2001;
            }
        });

        if(FormScrap.isItemChecked("New") == true) {
            FinishScrap = FinishScrap + 30/UPS;
        }
        FinishScrap = FinishScrap * FDRx;
        if(PrintMachine == "WS6800") {
            FinishScrap = FinishScrap * parseInt(DataPEForm.getItemValue("Stock_Size",true))/1000;
        } else if(PrintMachine == "ABG") {
            FinishScrap = 60;
        } else if (PrintMachine.toUpperCase() == "INKJET") {
            FinishScrap = 0;
            var QtySX = parseInt(Quantity); 
            if(QtySX > 0 && QtySX < 51) {
                FormScrap.setItemValue("PrintScrap", 2);
            } else if(QtySX > 50 && QtySX < 101) {
                FormScrap.setItemValue("PrintScrap", 3);
            } else if(QtySX > 100 && QtySX < 301) {
                FormScrap.setItemValue("PrintScrap", 5);
            } else if(QtySX > 300 && QtySX < 501) {
                FormScrap.setItemValue("PrintScrap", 6);
            } else if(QtySX > 500 && QtySX < 1001) {
                FormScrap.setItemValue("PrintScrap", 10);
            } else if(QtySX > 1000) {
                FormScrap.setItemValue("PrintScrap", 20);
            }

        }

        // @TanDoan - 2021-01-06: Nếu có công đoạn GLUING (GL) thì cộng thêm 150PCS vào FinishScrap.
        

        // if(Customer_Item.indexOf("HD") == 0) FinishScrap = FinishScrap + PrintSheet * 0.03;

        FormScrap.setItemValue("FinishScrap", Math.round(FinishScrap));
        FormScrap.setItemValue("ColorSum", parseInt(FormScrap.getItemValue("Color_F",true)) +"+"+ parseInt(FormScrap.getItemValue("Color_B",true)));
        var CodeProcess = "";
        GridProcess.forEachRow(function(id){
            CodeProcess = CodeProcess + "," + GridProcess.cells(id,10).getValue();
        });
        CodeProcess = CodeProcess.substring(1);
        GridProcess.clearAll();
        GridProcess.parse(ReloadRouting(CodeProcess));
    }


    function CalculateScrap() {
        if(DataPEForm.getItemValue("Print_Machine",true) == 0 || DataPEForm.getItemValue("Print_Machine",true) == "") {
            dhtmlx.alert("Đơn không có máy");
            return;
        }
        if(DataPEForm.getItemValue("Order_Style",true).indexOf("FGS-OUT") !== -1) return;

        PrintSheet = DataPEForm.getItemValue("Print_Sheet",true);
        if(PrintSheet == 0 || PrintSheet == "")
        {
            if(confirm("Bạn có muốn tính PrintSheet")) {
                CalculateSheet();
            } else {
                return;
            }
        }
        var id = "WindowsDetail";
        var w = 1100;	var h = 630;	var x = Number(($(window).width()-w)/2);	var y = Number(($(window).height()-h)/2);
        var Popup = dhxWins.createWindow(id, x, y, w, h);
        dhxWins.window(id).setText("Insert/Modify Item Information");
        var DataFormScrap = [
                        {type: "settings", position: "label-left", labelWidth: 90, inputWidth: 130},
                        {type: "block", width: "auto", blockOffset: 20, list: [
                            {type: "block", width: "auto", blockOffset: 20, list: [
                                {type: "input", name: "PrintSheet", label: "Print Sheet:", offsetTop: "10", value: DataPEForm.getItemValue("Print_Sheet",true), inputWidth: "250"}
                            ]},
                            {type: "block", width: "auto", blockOffset: 20, offsetTop: "10", list: [
                                {type: "settings", position: "label-right"},
                                {type: "fieldset", label: "Color F", width: "auto",  blockOffset: "", list: [
                                    {type: "radio", label: "0 Color", value: "0", name: "Color_F"},
                                    {type: "radio", label: "1 Color", value: "1", name: "Color_F"},
                                    {type: "radio", label: "2 Color", value: "2", name: "Color_F"},
                                    {type: "radio", label: "3 Color", value: "3", name: "Color_F"},
                                    {type: "radio", label: "4 Color", value: "4", name: "Color_F"},
                                    {type: "radio", label: "5 Color", value: "5", name: "Color_F"},
                                    {type: "radio", label: "6 Color", value: "6", name: "Color_F"},
                                    {type: "radio", label: ">= 7 Color", value: "7", name: "Color_F"}
                                ]},
                                {type: "newcolumn"},
                                {type: "fieldset", label: "Color B", width: "auto", blockOffset: 20, offsetLeft: "20", list: [
                                    {type: "radio", label: "0 Color", value: "0", name: "Color_B"},
                                    {type: "radio", label: "1 Color", value: "1", name: "Color_B"},
                                    {type: "radio", label: "2 Color", value: "2", name: "Color_B"},
                                    {type: "radio", label: "3 Color", value: "3", name: "Color_B"},
                                    {type: "radio", label: "4 Color", value: "4", name: "Color_B"},
                                    {type: "radio", label: "5 Color", value: "5", name: "Color_B"},
                                    {type: "radio", label: "6 Color", value: "6", name: "Color_B"},
                                    {type: "radio", label: ">= 7 Color", value: "7", name: "Color_B"}
                                ]}
                            ]},
                            {type: "block", width: "auto", blockOffset: 20, list: [
                                {type: "input", name: "ColorSum", label: "Color Sum F/B:", value: DataPEForm.getItemValue("Color_Sum_FB",true), labelWidth: "120", inputWidth: "220", offsetTop: "10"},
                                {type: "input", name: "ColorManagement", label: "Color Management:", value: "", labelWidth: "120", inputWidth: "220"}
                            ]},
                            {type: "block", width: "auto", blockOffset: 20, list: [
                                {type: "input", name: "PDR", label: "Printting Difficult Rate:", value: "", labelWidth: "125", inputWidth: "40"},
                                {type: "input", name: "FDR", label: "Finishing Difficult Rate:", value: "", labelWidth: "125", inputWidth: "40"},
                                {type: "combo", name: "SizeColorF", label: "Size Color", value: "", inputWidth: "50", options:[
                                        {value: "0", text: "0"},
                                        {value: "1", text: "1"},
                                        {value: "2", text: "2"},
                                        {value: "3", text: "3"},
                                        {value: "4", text: "4"},
                                        {value: "5", text: "5"},
                                        {value: "6", text: "6"},
                                        {value: "7", text: "7"},
                                        {value: "8", text: "8"},
                                        {value: "9", text: "9"},
                                        {value: "10", text: "10"}
                                    ]},
                                {type: "combo", name: "ImprintF", label: "Imprint", value: "", inputWidth: "50", options:[
                                        {value: "0", text: "0", selected: true},
                                        {value: "1", text: "1"},
                                        {value: "2", text: "2"},
                                        {value: "3", text: "3"},
                                        {value: "4", text: "4"},
                                        {value: "5", text: "5"},
                                        {value: "6", text: "6"},
                                        {value: "7", text: "7"},
                                        {value: "8", text: "8"},
                                        {value: "9", text: "9"},
                                        {value: "10", text: "10"}
                                    ]},
                                {type: "checkbox", name: "New", label: "New", checked: DataPEForm.isItemChecked("New_Product_If")},
                                {type: "block", width: "auto", blockOffset: 20},
                                {type: "newcolumn"},
                                {type: "combo", label: "Runs", value: "", labelWidth: "100", offsetLeft:"5", inputWidth: "65", name: "Runs", options:[
                                        {value: "0", text: "0"},
                                        {value: "1", text: "1"},
                                        {value: "2", text: "2"},
                                        {value: "3", text: "3"},
                                        {value: "4", text: "4"},
                                        {value: "5", text: "5"},
                                        {value: "6", text: "6"},
                                        {value: "7", text: "7"},
                                        {value: "8", text: "8"},
                                        {value: "9", text: "9"},
                                        {value: "10", text: "10"}
                                    ]},
                                {type: "combo", name: "SuitedMachine", label: "Suited Machine", value: "", labelWidth: "82", offsetLeft:"5", inputWidth: "83", options:[
                                        <?php 
                                            echo '{text: "", value: ""}';
                                            $RawData = MiQuery( "SELECT `Production_Type`, `Machine`, `Production_Line`, `Color_Management`, `Social_Compliance`, `System_Name`, `Digital_Availability`, `Status`, `FSC_Type` FROM access_optinal_pewindow;", _conn());
                                            
                                            foreach($RawData as $R)
                                            {
                                                if($R["Machine"] != "") echo ',{text: "' . $R["Machine"] . '", value: "' . $R["Machine"] . '"}';
                                            }
                                        ?>
                                    ]},
                                {type: "combo", name: "SizeColorB", label: "Size Color", value: "", inputWidth: "50", offsetLeft:"5", labelWidth: "100", options:[
                                        {value: "0", text: "0"},
                                        {value: "1", text: "1"},
                                        {value: "2", text: "2"},
                                        {value: "3", text: "3"},
                                        {value: "4", text: "4"},
                                        {value: "5", text: "5"},
                                        {value: "6", text: "6"},
                                        {value: "7", text: "7"},
                                        {value: "8", text: "8"},
                                        {value: "9", text: "9"},
                                        {value: "10", text: "10"}
                                    ]},
                                {type: "combo", name: "ImprintB", label: "Imprint", value: "", labelWidth: "100", offsetLeft:"5", inputWidth: "50", options:[
                                        {value: "0", text: "0"},
                                        {value: "1", text: "1"},
                                        {value: "2", text: "2"},
                                        {value: "3", text: "3"},
                                        {value: "4", text: "4"},
                                        {value: "5", text: "5"},
                                        {value: "6", text: "6"},
                                        {value: "7", text: "7"},
                                        {value: "8", text: "8"},
                                        {value: "9", text: "9"},
                                        {value: "10", text: "10"}
                                    ]},
                                {type: "checkbox", name: "Mellow", label: "Mellow", value: "", offsetLeft:"5"},
                                {type: "block", width: "auto", blockOffset: 20}
                            ]}
                        ]},
                        {type: "newcolumn"},
                        {type: "block", width: "auto", blockOffset: 20, list: [
                            {type: "block", width: "auto", blockOffset: 20, offsetLeft: 0, list: [
                                {type: "block", width: "auto", blockOffset: 20, list: [
                                    {type: "input", name: "JobJacket", label: "ID:", value: DataPEForm.getItemValue("ID",true), inputWidth: "100", labelWidth: "70"},
                                    {type: "input", name: "OrderDate", label: "Order Date:", value: DataPEForm.getItemValue("Order_Receive_Day",true), labelWidth: "70"},
                                    {type: "input", name: "OrderQty", label: "Order Qty:", value: DataPEForm.getItemValue("Order_Quantity",true), labelWidth: "70", inputWidth: "80"}
                                ]},
                                {type: "newcolumn"},
                                {type: "block", width: "auto", blockOffset: 20, list: [
                                    {type: "input", name: "GLID", label: "GLID:", value: DataPEForm.getItemValue("GLID",true), labelWidth: "50"},
                                    {type: "input", name: "DueDay", label: "Dueday", value: DataPEForm.getItemValue("DueDay",true), labelWidth: "50", readonly:true},
                                    {type: "input", name: "UPS", label: "UPS", value: DataPEForm.getItemValue("UPS",true), labelWidth: "50"}
                                ]},
                                {type: "newcolumn"},
                                {type: "block", width: "auto", blockOffset: 20, list: [
                                    {type: "input", name: "Size", label: "Size", value: DataPEForm.getItemValue("Stock_Size",true), labelWidth: "50", inputWidth: "100"},
                                    {type: "input", name: "BO", label: "BO", value: DataPEForm.getItemValue("Bo",true), labelWidth: "50", inputWidth: "100"},
                                    {type: "input", name: "UrgentLevel", label: "Urgent Level", value: DataPEForm.getItemValue("Urgent_Status",true), labelWidth: "80", inputWidth: "70"}
                                ]}
                            ]},
                            {type: "block", width: "auto", blockOffset: 20, list: [
                                {type: "container", value: "", inputHeight: "300", inputWidth: "597", name: "GridProcess"}
                            ]},
                            {type: "block", width: "auto", blockOffset: 20, list: [
                                {type: "input", name: "PrintScrap", label: "Printing Scrap", value: ""},
                                {type: "input", name: "FinishScrap", label: "Finish Scrap", value: ""}
                            ]},
                            {type: "block", width: "auto", blockOffset: 20, list: [
                                {type: "button", name: "Calculate", value: "Calculate"},
                                {type: "newcolumn"},
                                {type: "button", name: "Confirm", value: "Confirm"},
                                {type: "newcolumn"},
                                {type: "button", name: "Routing", value: "Reload Routing"}
                            ]}
                        ]}
                    ];
        var DataScrap = AjaxAsync("Data.php",{"EVENT":"LOADPROCESS","JJ": DataPEForm.getItemValue("ID",true),"GLID":DataPEForm.getItemValue("GLID",true)},"GET","json");
        FormScrap = Popup.attachForm();
        FormScrap.loadStruct(DataFormScrap, "json");
        FormScrap.setItemValue("SuitedMachine", DataPEForm.getItemValue("Print_Machine",true));
        if(DataScrap.Color_FQ > 7) DataScrap.Color_FQ = 7;
        if(DataScrap.Color_B > 7) DataScrap.Color_B = 7;
        FormScrap.setItemValue("Color_F", DataScrap.Color_FQ);
        FormScrap.setItemValue("Color_B", DataScrap.Color_BQ);
        FormScrap.setItemValue("Runs", DataScrap.Offset_Imp);
        FormScrap.setItemValue("ColorManagement", DataScrap.Color_Management);
        FormScrap.setItemValue("FDR", DataScrap.Finishing_Difficult_Rate);
        FormScrap.setItemValue("PDR", DataScrap.Scrap_Adjustment);
        Customer_Item = DataScrap.Item_Code;
        var count = 0;
        GridLot.forEachRow(function(id){
            if(GridLot.cells(id,1).getValue() != "") {
                count++;
            }
        });
        if(count > 10) FormScrap.getCombo("ImprintB").addOption(String(count),String(count));
        FormScrap.setItemValue("ImprintB", count);
            GridProcess = new dhtmlXGridObject(FormScrap.getContainer("GridProcess"));
			GridProcess.setImagePath("./Module/dhtmlx/skins/skyblue/imgs/");
			GridProcess.setHeader("ID,SEQ, Process, Planning Finish Date, Ability, Unit, <501,501-2000,2001-5000,>5000,Code");
			GridProcess.setInitWidths("30,50,100,150,80,80,80,80,80,80,80")
			GridProcess.setColAlign("center,center,center,center,center,center,center,center,center,center,center");
			GridProcess.setColTypes("ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro");
			GridProcess.setColSorting("str,str,str,str,str,str,str,str,str,str,str")
			GridProcess.setRowTextStyle("1", "background-color: red; font-family: arial;");
            GridProcess.init();
            GridProcess.parse(DataScrap.MAIN, function(){
                if(GridProcess.getRowsNum() == 0 && DataPEForm.getItemValue("Order_Style",true) != "FGS-OUT"){
                    var DataTable = {"EVENT":"REROUTING"};
                        DataTable.PM = DataPEForm.getItemValue("Print_Machine",true);
                        DataTable.QTY = DataPEForm.getItemValue("Order_Quantity",true);
                        DataTable.CODE = DataPEForm.getItemValue("Process",true);
                        DataTable.DUEDATE = DataPEForm.getItemValue("DueDay",true);
                        DataTable.GLID = DataPEForm.getItemValue("GLID",true);
                        DataTable.JOBJACKET = JobJacket;

                        // console.log('DataTable ' + JSON.stringify(DataTable) ) ;
                        var Result = AjaxAsync("Event.php",DataTable,"POST");
                        console.log('Result:: ' + Result);
                        if(Result == "OK") {
                            DataScrap = AjaxAsync("Data.php",{"EVENT":"LOADPROCESS","JJ": DataPEForm.getItemValue("ID",true),"GLID":DataPEForm.getItemValue("GLID",true)},"GET","json");
                            GridProcess.clearAll();
                            GridProcess.parse(DataScrap.MAIN);
                        }
                }
                
            });

            GridProcess.attachEvent("onRowSelect", function(id,ind){
                if(ind == 0){
                    if(confirm("Bạn muốn xóa")){
                        GridProcess.deleteRow(id);
                    }
                }
                
            });

            FormScrap.attachEvent("onButtonClick", function(name){
                var UPS = FormScrap.getItemValue("UPS", true);
                var OrderQty = DataPEForm.getItemValue("Order_Quantity",true);
                var PrintMachine = FormScrap.getItemValue("SuitedMachine", true);

				if(name == "Calculate"){
					Cal();
				} else if(name == "Routing"){
                    var DataTable = {"EVENT":"REROUTING"};
                    DataTable.PM = FormScrap.getItemValue("SuitedMachine",true);
                    DataTable.QTY = DataPEForm.getItemValue("Order_Quantity",true);
                    DataTable.CODE = DataPEForm.getItemValue("Process",true);
                    DataTable.DUEDATE = DataPEForm.getItemValue("DueDay",true);
                    DataTable.GLID = DataPEForm.getItemValue("GLID",true);
                    DataTable.JOBJACKET = JobJacket;
                    var Result = AjaxAsync("Event.php",DataTable,"POST");
                    if(Result == "OK") {
                        DataScrap = AjaxAsync("Data.php",{"EVENT":"LOADPROCESS","JJ": DataPEForm.getItemValue("ID",true),"GLID":DataPEForm.getItemValue("GLID",true)},"GET","json");
                        GridProcess.clearAll();
                        GridProcess.parse(DataScrap.MAIN);
                    }
                } else {
                    CalConfirm();
				}
			});
            
    };

    function ReloadRouting(ProcessCode){
        var DataTable = {"EVENT":"REROUTINGPROCESS"};
            DataTable.PM = FormScrap.getItemValue("SuitedMachine",true);
            DataTable.QTY = DataPEForm.getItemValue("Order_Quantity",true);
            DataTable.CODE = ProcessCode;
            DataTable.DUEDATE = DataPEForm.getItemValue("DueDay",true);
            DataTable.GLID = DataPEForm.getItemValue("GLID",true);
            console.log("Here:::: " + JSON.stringify(DataTable));
        return AjaxAsync("Event.php",DataTable,"POST");
    }

    function CalculateSheet(){
        if(SubS.indexOf("2 TAG") !== -1) dhtmlx.alert("Con nhãn 2 TAG");
        if(DataPEForm.getItemValue("Order_Style",true).indexOf("FGS-OUT") !== -1) return;
        if(DataPEForm.getItemValue("Print_Machine",true) == "WS6800" || DataPEForm.getItemValue("Print_Machine",true) == "ABG")
        {
            PrintSheet = Math.round(DataPEForm.getItemValue("Order_Quantity",true) * DataPEForm.getItemValue("Stock_Size",true).replace("mm","") / (DataPEForm.getItemValue("UPS",true) * 1000) + 0.499999)
        } else
        {
            if(DataPEForm.getItemValue("Order_Quantity",true) == "" ||  DataPEForm.getItemValue("Stock_Size",true) == "" || DataPEForm.getItemValue("Order_Quantity",true) == 0 || DataPEForm.getItemValue("UPS",true) == 0) alert("Không Thể Tính Print Sheet");
            else PrintSheet = Math.round(DataPEForm.getItemValue("Order_Quantity",true)/DataPEForm.getItemValue("UPS",true) + 0.499999)
        }
        DataPEForm.setItemValue("Print_Sheet",PrintSheet);
    };

    var SubS = "";
    var RID = "";
    var CID = "";
    function InitForm(){
        DataPEForm = LayoutMain.cells("a").attachForm();
        var DataForm = AjaxAsync("EventForm.php",{"EVENT":"LOADJOBJACKET","JJ": JobJacket},"GET","json");
        console.log(DataForm);
        if(JobJacket != "") {
            SubS = DataForm.SUB.toUpperCase();
            if(DataForm.Delete != ""){
                dhtmlx.alert("Đơn đã bị xóa bởi " + DataForm.Delete, function(){
                    window.location = "ProductionRecord.php";
                });
            }
        }
        DataPEForm.loadStruct(DataForm.Main, "json");
        DataPEForm.setItemValue('Urgent_Status', DataForm.US);
        if(DataForm.OS.toUpperCase() == "OFFSET") DataForm.OS = "FG";
        DataPEForm.setItemValue('Order_Style', DataForm.OS);


        DataPEForm.attachEvent("onFocus", function(name, value){
            if(name != "SOLineID") RID = "";
        });


        var PrintSheet = DataPEForm.getItemValue("Print_Sheet",true);
        var UPS = DataPEForm.getItemValue("UPS",true);
        var OrderQty = DataPEForm.getItemValue("Order_Quantity",true);
        var PrintMachine = DataPEForm.getItemValue("Print_Machine",true);
        if(PrintMachine == "WS6800") {
            DataPEForm.setItemValue("ActualPCS", parseInt(PrintSheet * UPS * 1000/DataPEForm.getItemValue("Stock_Size",true)));
        } else {
            DataPEForm.setItemValue("ActualPCS", parseInt(PrintSheet * UPS));
        }					
        // DataPEForm.setItemValue("OverRate", Math.round((PrintSheet * UPS - OrderQty)*10000/OrderQty)/100 + "%");
        DataPEForm.setItemValue("OverRate", (Math.round(DataPEForm.getItemValue("ActualPCS",true)) / Math.round(DataPEForm.getItemValue("Order_Quantity",true)) - 1)*100 + "%");
        // if(PrintSheet * UPS - OrderQty < 0) dhtmlx.alert("Mọi người ơi, mọi người ơi Over Rate đang âm kìa")
        DataPEForm.setItemValue("ScrapRate", Math.round((parseInt(DataPEForm.getItemValue("Print_Scrap",true)) + parseInt(DataPEForm.getItemValue("Finish_Scrap",true)))*10000/PrintSheet)/100 + "%");

        DataPEForm.attachEvent("onChange",function(name, value, state){
            if(name == "Request_Date" || name == "Promise_Date") {
                var DataTime = {
                            "EVENT":"GETDUEDATE",
                            "PD": DataPEForm.getItemValue("Promise_Date",true),
                            "CRD": DataPEForm.getItemValue("Request_Date",true),
                            "MLA": DataPEForm.getItemValue("MLA",true)
                        };
                var DueDateNew = AjaxAsync("Event.php",DataTime,"POST","HTML");
                // var DueDateNew = AjaxAsync("Event.php",DataTime,"GET","HTML");

                if(DueDateNew != DataPEForm.getItemValue("DueDay",true)) {
                    DataPEForm.setItemValue("DueDay", DueDateNew)
                }
                
            } else if(name == "Order_Style") {
                if(value == "WIP" || value == "FGS-IN Offset" || value == "FGS-IN Digital" || value == "WIP") {
                    // if(confirm("Xóa Promise Date, Request Date, Due Date?")) {
                    //     DataPEForm.setItemValue("Promise_Date", "")
                    //     DataPEForm.setItemValue("Request_Date", "")
                    //     DataPEForm.setItemValue("DueDay", "")
                    // }
                } else if(value == "FGS-OUT" || value == "FGS-OUT-BK") {
                    if(confirm("Clear Dữ liệu")) {
                        DataPEForm.setItemValue("Print_Sheet", "0");
                        DataPEForm.setItemValue("Print_Scrap", "0");
                        DataPEForm.setItemValue("Finish_Scrap", "0");
                        DataPEForm.setItemValue("Color_By_Size", "0");
                        DataPEForm.setItemValue("Imprint_Lot", "0");
                        DataPEForm.setItemValue("Color_Sum_FB", "0");
                        var ArrRout = [];
                        var DataTable = {"EVENT":"UPDATEROUTING"};
                            DataTable.MAIN = JSON.stringify(ArrRout);
                            DataTable.JobJacket = DataPEForm.getItemValue("ID", true);
                            var Result = AjaxAsync("Event.php",DataTable,"POST");
                    }
                }
            } else if(name == "Urgent_Status"){
                if(value == "RE-PRINT" || value == "FOD" || value == "CCR" || value == "TECHNICAL TEST" || value == "SAMPLE") {
                    if(confirm("Xóa Promise Date, Request Date, Due Date?")) {
                        DataPEForm.setItemValue("Promise_Date", "")
                        DataPEForm.setItemValue("Request_Date", "")
                        DataPEForm.setItemValue("DueDay", "")
                    }
                    if(DataPEForm.getItemValue("SO", true) == "" && value == "SAMPLE") {
                        DataPEForm.setItemValue("SO", "SAMPLE - " + DataPEForm.getItemValue("GLID", true));
                        DataPEForm.setItemValue("SO_Lines", "SAMPLE - " + DataPEForm.getItemValue("GLID", true));
                        DataPEForm.setItemValue("PPC_Remark", "SAMPLE - " + DataPEForm.getItemValue("GLID", true));
                    } else if(DataPEForm.getItemValue("SO", true) == "" && value == "TECHNICAL TEST") {
                        DataPEForm.setItemValue("SO", "TECHNICAL TEST - " + DataPEForm.getItemValue("GLID", true));
                        DataPEForm.setItemValue("SO_Lines", "TECHNICAL TEST - " + DataPEForm.getItemValue("GLID", true));
                        DataPEForm.setItemValue("PPC_Remark", "TECHNICAL TEST - " + DataPEForm.getItemValue("GLID", true));
                    }
                }
            } else if(name == "Order_Quantity"){
                    DataPEForm.setItemValue("Order_Quantity", DataPEForm.getItemValue("Order_Quantity", true).trim());
            }
        });
        if(DataPEForm.getItemValue("GLID",true).indexOf("2-") == 0) {
            DataPEForm.hideItem("LotData");
        }

        DataPEForm.attachEvent("onButtonClick", function(name){
                if (name == "LoadData"){
                    var DataGLID = AjaxAsync("Data.php",{"EVENT":"LOADDATAGLID","GLID":DataPEForm.getItemValue("GLID",true)},"POST","JSON");
                    if(DataPEForm.getItemValue("Order_Style",true) == "Digital"){
                        DataPEForm.setItemValue("Stock_Code_F",DataGLID.Digital_Stock_Code_F);
                        DataPEForm.setItemValue("Stock_Size",DataGLID.Digital_Sheet_Size);
                        DataPEForm.setItemValue("UPS",DataGLID.Digital_UPS);
                        DataPEForm.setItemValue("Cut_Number",DataGLID.Digital_Cut_No);
                        DataPEForm.setItemValue("Print_Machine",DataGLID.Digital_Machine);
                        dhtmlx.alert("Đã cập nhật dữ liệu Digital");
                    } else {
                        DataPEForm.setItemValue("Stock_Code_F",DataGLID.Stock_Code);
                        DataPEForm.setItemValue("Stock_Size",DataGLID.Sheet_Size);
                        DataPEForm.setItemValue("UPS",DataGLID.Offset_UPS);
                        DataPEForm.setItemValue("Cut_Number",DataGLID.Offset_Cut_No);
                        DataPEForm.setItemValue("Print_Machine",DataGLID.Suited_Machine);
                        dhtmlx.alert("Đã cập nhật dữ liệu Offset");
                    }
                } else if(name == "ShowLOT") {
                    DataPEForm.showItem("LotData");
                } else if(name == "LayMau") {
                    DataPEForm.setItemValue("PPC_Remark", "LAY MAU \n" + DataPEForm.getItemValue("PPC_Remark",true));
                } else if(name == "COMBINEITEM") {
                    DataPEForm.setItemValue("PPC_Remark", "COMBINE ITEM \n" + DataPEForm.getItemValue("PPC_Remark",true));
                } else if(name == "CopyJob") {
                    var JobCopy = prompt("Vui lòng nhập số JobJacket cần sao chép (Copy)");
                    if(JobCopy != ""){
                        var DataTable = {"EVENT":"GETSOLINECOPY", "JJ":JobCopy};
                        var Result = AjaxAsync("Data.php",DataTable,"GET","HTML");
                        Result = Result.split("||");
                        var DataJob = JSON.parse(Result[1])[0];
                        DataPEForm.setItemValue("SO",DataJob.SO);
                        DataPEForm.setItemValue("Order_Quantity",DataJob.Order_Quantity);
                        DataPEForm.setItemValue("Promise_Date",DataJob.Promise_Date);
                        DataPEForm.setItemValue("Request_Date",DataJob.Request_Date);
                        DataPEForm.setItemValue("DueDay",DataJob.DueDay);
                        DataPEForm.setItemValue("PPC_Remark",DataJob.PPC_Remark);
                        GridSO.clearAll();
                        GridSO.parse(Result[0], function(){
                            GridSO.forEachRow(function(id){
                                if(GridSO.cells(id,2).getValue() != "") {
                                    GridSO.cells(id,5).setValue("");
                                    GridSO.cells(id,6).setValue("From " + JobCopy);
                                }
						    });
                        });

                        // GridSO.load("Data.php?EVENT=GETSOLINE&JJ=" + JobCopy,function(){
                        //     GridSO.forEachRow(function(id){
                        //         if(GridSO.cells(id,2).getValue() != "") {
                        //             GridSO.cells(id,5).setValue("");
                        //             GridSO.cells(id,6).setValue("From " + JobCopy);
                        //         }
						//     });
                        // });
                    }
                }

			});
        GridSO = new dhtmlXGridObject(DataPEForm.getContainer("SOLineID"));
        GridSO.enableBlockSelection(true);
        GridSO.load("Data.php?EVENT=GETSOLINE&JJ=" + JobJacket, function(){
            var TotalQty = 0;
            var TotalSOLINE = 0;
            GridSO.forEachRow(function(id){
                    if(!isNaN(GridSO.cells(id,4).getValue()) && GridSO.cells(id,4).getValue() != ""){
                        TotalQty += parseInt(GridSO.cells(id,4).getValue());
                        TotalSOLINE++;
                    }
                });
                if(TotalSOLINE != 0) {
                    DataPEForm.setItemValue("Order_Quantity",TotalQty);
                }
            
            $("#TotalSOLINE").html(TotalSOLINE);
        });

        GridSO.attachEvent("onEditCell", function(stage,rId,cInd,nValue,oValue){
            if(cInd == 4 && stage == 2) {
            var TotalQty = 0;
            var TotalSOLINE = 0;

                GridSO.forEachRow(function(id){
                    if(!isNaN(GridSO.cells(id,4).getValue()) && GridSO.cells(id,4).getValue() != ""){
                        TotalQty += parseInt(GridSO.cells(id,4).getValue());
                        TotalSOLINE++;
                    }
                });
                DataPEForm.setItemValue("Order_Quantity",TotalQty);
                $("#TotalSOLINE").html(TotalSOLINE); 
            }
            return true;
        });

        GridSO.attachEvent("onRowSelect", function(id,ind){
            RID = id;
            CID = ind;
        });

        GridSO.attachEvent("onKeyPress", function(code,cFlag,sFlag){
            var LastRow = GridSO.getRowsNum();
            if(!sFlag && code == 46) { //Delete
                window.setTimeout(function(){
                    var top_row = GridSO.getSelectedBlock().LeftTopRow;
                    var bottom_row = GridSO.getSelectedBlock().RightBottomRow;
                    var left_column = GridSO.getSelectedBlock().LeftTopCol;
                    var right_column = GridSO.getSelectedBlock().RightBottomCol;
                    if(typeof top_row == "string") {
                        GridSO.cells(top_row,left_column).setValue("");
                    } else { 
                        for(var i = top_row; i <= bottom_row; i++){
                            for(var j = left_column; j <= right_column; j++) {
                                GridSO.cells2(i,j).setValue("");
                            }
                        }
                    }
                },1);
                return true;
            } else if(sFlag && code == 46) { //Delete
                window.setTimeout(function(){
                    var top_row = GridSO.getSelectedBlock().LeftTopRow;
                    var bottom_row = GridSO.getSelectedBlock().RightBottomRow;
                    var left_column = GridSO.getSelectedBlock().LeftTopCol;
                    var right_column = GridSO.getSelectedBlock().RightBottomCol;
                    if(typeof top_row == "string") {
                        GridSO.deleteRow(top_row);
                    } else {
                        for(var i = top_row; i <= bottom_row; i++){
                            GridSO.deleteRow(GridSO.getRowId(i));
                        }
                    }
                },1);
                return true;
            }
        })

        GridLot = new dhtmlXGridObject(DataPEForm.getContainer("LotData"));
        GridLot.enableBlockSelection(true);
        GridLot.load("Data.php?EVENT=GETLOT&JOBJACKET=" + JobJacket);
        var RowLot = 0;
        var ColLot = 0;
        GridLot.attachEvent("onEditCell", function(stage,rId,cInd,nValue,oValue){
            RowLot = GridLot.getRowIndex(rId);
            ColLot = cInd;
            if(stage == 2){
                if(nValue != null && nValue.indexOf("+") !== false){
                    var DataX = nValue.split("+");
                    var NumPlus = 0;
                    for(var i = 0; i < DataX.length; i++){
                        if(DataX[i] != ""){
                            if(isNaN(DataX[i])){
                                NumPlus = "";
                                break;
                            }
                            NumPlus += parseInt(DataX[i]);
                        }
                    }
                    if(NumPlus != "") GridLot.cells(rId,cInd).setValue(NumPlus);
                }

                if(isNaN(GridLot.cells(rId,cInd).getValue())){
                    GridLot.cells(rId,cInd).setValue("");
                    dhtmlx.alert("Vui lòng nhập đúng dữ liệu");
                    return;
                }

                var PrintSheetGrid = 0;
					GridLot.forEachRow(function(id){
                        if(GridLot.cells(id,1).getValue() != "") {
                            PrintSheetGrid = PrintSheetGrid + parseInt(GridLot.cells(id,1).getValue());
                        }
					})
					DataPEForm.setItemValue("Print_Sheet",PrintSheetGrid);
            }
            return true;
        });
        
        GridLot.attachEvent("onKeyPress", function(code,cFlag,sFlag){
                if(!sFlag && code == 46) { //Delete
                    window.setTimeout(function(){
                        var top_row = GridLot.getSelectedBlock().LeftTopRow;
                        var bottom_row = GridLot.getSelectedBlock().RightBottomRow;
                        var left_column = GridLot.getSelectedBlock().LeftTopCol;
                        var right_column = GridLot.getSelectedBlock().RightBottomCol;
                        if(typeof top_row == "string") {
                            GridLot.cells(top_row,left_column).setValue("");
                        } else { 
                            for(var i = top_row; i <= bottom_row; i++){
                                for(var j = left_column; j <= right_column; j++) {
                                    GridLot.cells2(i,j).setValue("");
                                }
                            }
                        }
                    },1);
                    return true;
                } else if(code == 40)
				{
					window.setTimeout(function(){
                        RowLot++;
                        GridLot.selectCell(RowLot,ColLot,false,false,true,true);
						GridLot.editCell();
					},1);
				} else if(code == 38) {
					window.setTimeout(function(){
                        if(RowLot - 1 < 0) return;
                        RowLot--;
						GridLot.selectCell(RowLot,ColLot,false,false,true,true);
						GridLot.editCell();
					},1);
				} else if(!cFlag && code == 13) {
					window.setTimeout(function(){
						RowLot++;
						GridLot.selectCell(RowLot,ColLot,false,false,true,true);
						GridLot.editCell();
					},1);
                } else if(code == 39) {
					window.setTimeout(function(){
                        ColLot++;
						GridLot.selectCell(RowLot,ColLot,false,false,true,true);
						GridLot.editCell();
					},1);
				} else if(code == 37) {
					window.setTimeout(function(){
                        if(ColLot - 1 < 1) return;
                        ColLot--;
						GridLot.selectCell(RowLot,ColLot,false,false,true,true);
						GridLot.editCell();
					},1);
				} else if(cFlag && code == 13) {
					var DataTable = {"EVENT":"UPDATEDATAJJ"};
                    var DataGrid = "";
                    GridSO.forEachRow(function(id){
                        if(GridSO.cells(id,1).getValue() != "") {
                            DataGrid += "|" + GridSO.cells(id,1).getValue() + 
                                        "*" + GridSO.cells(id,4).getValue() + 
                                        "*" + GridSO.cells(id,6).getValue(); 
                        }
                    });

                    var LotPrint = "";
                    GridLot.forEachRow(function(id){
                        if(GridLot.cells(id,1).getValue() != "") {
                            LotPrint += "|" + GridLot.cells(id,0).getValue() + "*" + GridLot.cells(id,1).getValue();
                        }
                    });

                    // console.log("UPDATE JJ: " + DataTable);

                    DataTable.MAIN = JSON.stringify(DataPEForm.getFormData());
                    DataTable.SOLine = DataGrid;
                    DataTable.Lot = LotPrint;
                    var Result = AjaxAsync("Event.php",DataTable,"POST");
                    location.href = "PrintPages.php?JJ=" + ToolbarMain.getValue("JobJacket").trim() + "&P=1";
                } else if(cFlag && code == 68) { //Clone
                    window.setTimeout(function(){
                        var top_row = GridLot.getSelectedBlock().LeftTopRow;
                        var bottom_row = GridLot.getSelectedBlock().RightBottomRow;
                        var left_column = GridLot.getSelectedBlock().LeftTopCol;
                        var right_column = GridLot.getSelectedBlock().RightBottomCol;
                        for(var i = top_row + 1; i <= bottom_row; i++){
                            for(var j = left_column; j <= right_column; j++) {
                                GridLot.cells2(i,j).setValue(GridLot.cells2(top_row,j).getValue());
                            }
                        }
                    },1);
                } else if(cFlag && code == 67) { //Copy
                    window.setTimeout(function(){
                        var top_row = GridLot.getSelectedBlock().LeftTopRow;
                        var bottom_row = GridLot.getSelectedBlock().RightBottomRow;
                        var left_column = GridLot.getSelectedBlock().LeftTopCol;
                        var right_column = GridLot.getSelectedBlock().RightBottomCol;
                        var DataCB = "";
                        var DataCB1 = "";
                        if(typeof top_row == "string") {
                            DataCB = GridLot.cells(top_row,left_column).getValue() + "\n";
                        } else {
                            for(var i = top_row; i <= bottom_row; i++){
                                for(var j = left_column; j <= right_column; j++) {
                                    DataCB1 += "\t" + GridLot.cells2(i,j).getValue().replace("\n","");
                                }
                                if(DataCB1 != "") DataCB += DataCB1.substring(1) + "\r\n";
                                DataCB1 = "";
                            }
                        }
                        $("#ClipBoard").val(DataCB);
                        var copyText = document.getElementById("ClipBoard");
                        copyText.select();
                        document.execCommand("copy");
                    },1);
                    return true;
                } else if(cFlag && code == 86){
                    window.setTimeout(function(){
                        // var top_row = GridLot.getSelectedBlock().LeftTopRow;
                        // var bottom_row = GridLot.getSelectedBlock().RightBottomRow;
                        // var left_column = GridLot.getSelectedBlock().LeftTopCol;
                        // var right_column = GridLot.getSelectedBlock().RightBottomCol;
                        // var clipText = CopyClipBoard;
                        // var RowData = clipText.split("\r\n");
                        // var newId = (new Date()).valueOf();
                        
                        // if(GridLot.getColumnsNum() - left_column < RowData[0].split("\t").length) {
                        //     dhtmlx.alert("Cột nhiều hơn, vui lòng dán đúng vị trí");
                        //     return;
                        // } else {
                        //     var TurnAdd = false;
                        //     var SplitString = "";
                        //     var LastRow = "";
                        //     var StringLength = 0;
                        //     for(var i = 0; i < RowData.length; i++){
                        //         SplitString = RowData[i].replace("\t","").replace("\r","").replace("\n","");
                        //         GridLot.cells2(left_column + i,1).setValue(SplitString);
                        //     }
                        // }
                    },1);
                }
				return true;
			});

            GridSO.attachEvent("onKeyPress", function(code,cFlag,sFlag){
				if(cFlag == true && code == 67){
                    var DataCB = "";
                    // console.log("X" + DataCB);
					var rowCount = GridSO.getRowsNum();
					for (var i=0; i < rowCount; i++) {
                        var XArray = [];
						var id = GridSO.getRowId(i);
						GridSO.forEachCell(id,function(cellObj,ind){
                            if(GridSO.cells(id,2).getValue() != "" && ind > 0) XArray.push(GridSO.cells(id,ind).getValue());
						});
						if(GridSO.cells(id,2).getValue() != "") DataCB += XArray.join("\t") + "\n";
					}
					$("#ClipBoard").val(DataCB);
					var copyText = document.getElementById("ClipBoard");
					copyText.select();
					document.execCommand("copy");
                }
				return true;
			});

        // setTimeout(function(){
        //     DataPEForm.setItemFocus("Order_Style");
        // },1000);

    };
    
    function doOnLoad(){
        dhxWins = new dhtmlXWindows();
        dhxWins.attachViewportTo(document.body);
    };

    function doOnUnload(){
        if (dhxWins != null && dhxWins.unload != null) {
            dhxWins.unload();
            dhxWins = null;
        }
    };
</script>
<body onload="doOnLoad();" onunload="doOnUnload();">
</body>
</html>