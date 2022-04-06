<?php 
    require("./Module/Template.php");
    InitPage("PCCreateJob","Created Job PPC");
?>

<script>
    var LayoutMain;
    var DataPEForm;
    var AccessoryGrid;
    var GLID = "";
    var GridOracle;
    var GridOracleIN;
    var ToolbarOracle;
    var TurnSend = true;
    GLID = getUrl("GLID");

    function SendToDigital() {
        if(!TurnSend) return;
        TurnSend = !TurnSend;
        LayoutMain.cells("a").progressOn();
        if(DataPEForm.getItemValue("Active", true) != "1") {
            dhtmlx.alert("Đơn này chưa Active, không thể làm lệnh"); return;
        }
        if(DataPEForm.getItemValue("Digital_Stock_Code_F", true) == "") {
            if(confirm("Item này không có vật tư cho Digital")) CreateJob("Digital");
        } else {CreateJob("Digital")}
    }

    function SendToOffset() {
        if(!TurnSend) return;
        TurnSend = !TurnSend;
        LayoutMain.cells("a").progressOn();
        if(DataPEForm.getItemValue("Active", true) != "1") {
            dhtmlx.alert("Đơn này chưa Active, không thể làm lệnh");return;
        }
        if(DataPEForm.getItemValue("Stock_Code", true) == "") {
            if(confirm("Item này không có vật tư cho Offset")) CreateJob("Offset");
        } else {CreateJob("Offset");}
    }

    function DocumentStart() {

        $(window).keydown(function(event) {
            if(event.ctrlKey && event.keyCode == 32) { // Space Key code
                if(GridOracle.getSelectedRowId() != null) {
                    if(GridOracle.cells(GridOracle.getSelectedRowId(),0).getValue() == 1) {
                        GridOracle.cells(GridOracle.getSelectedRowId(),0).setValue(false);
                    } else {
                        GridOracle.cells(GridOracle.getSelectedRowId(),0).setValue(true);
                    }
                }
            } else if(event.ctrlKey && event.keyCode == 68) {   // d key code 
                SendToDigital();
                event.preventDefault(); 
            } else if(event.ctrlKey && event.keyCode == 79) { // o key code (char)
                SendToOffset();
                event.preventDefault(); 
            } else if(event.altKey && event.keyCode == 70) {
                document.getElementsByClassName("hdrcell filter")[0].getElementsByTagName("input")[0].select();
                document.getElementsByClassName("hdrcell filter")[0].getElementsByTagName("input")[0].focus();
                event.preventDefault(); 
            } else if(event.altKey && event.keyCode == 68) {
                $(".dhxtoolbar_input").focus();
                event.preventDefault(); 
            }
        });

        LayoutMain = new dhtmlXLayoutObject({
            parent: document.body,
            pattern: "4C",
            offsets: {
                top: 65
            },
            cells: [
                {id: "a", header: false, text: "Control Panel", width: 600},
                {id: "b", header: true, text: "UnInstruction <button onClick='FSelectAll()'>Select All</button> -  <button onClick='FUnSelectAll()'>Un-Select All</button>",height:600},
                {id: "c", header: true, text: "InInstruction"},
                {id: "d", header: true, text: "NonOracle"}
            ]
        });
        LayoutMain.cells("d").collapse();
        ToolbarOracle = LayoutMain.cells("d").attachToolbar({
                        parent: "ToolbarBottom",
                        icons_path: "./Module/dhtmlx/common/imgs/",
                        align: "left",
                    });
        ToolbarOracle.addText("text", null, "ORDER NUMBER: ");
        ToolbarOracle.addInput("ORDER_NUMBER", null, "", 200);
        ToolbarOracle.addButton("Find", null, "Find ORDER", "save.gif");
        ToolbarOracle.attachEvent("onEnter", function(id, value){
            GridNonOracle.clearAll();
            GridNonOracle.loadXML("Data.php?EVENT=LOADORDERITEMNON&GLID=" + GLID + "&ORDER_NUMBER=" + ToolbarOracle.getValue("ORDER_NUMBER").trim());
        });
        ToolbarOracle.attachEvent("onClick", function(id){
            GridNonOracle.clearAll();
            GridNonOracle.loadXML("Data.php?EVENT=LOADORDERITEMNON&GLID=" + GLID + "&ORDER_NUMBER=" + ToolbarOracle.getValue("ORDER_NUMBER").trim());
        });


        if(GLID == null) GLID = "";
        ToolbarMain.addText("text", null, "<a style=''>____________________________</a>");			
        ToolbarMain.addText("text", null, "<input ID='ProductionLine' style='font-weight:bold;color:white;background:#009933;text-align:center;font-size:12pt;width:130px;border:none;'readonly=true></input>");
        ToolbarMain.addText("text", null, "<a style=''>_____</a>",1000);			
        ToolbarMain.addButton("Offset", null, "<a style='font-size:16pt;font-weight:bold;background:red;color:white'>To Offset</a>", "save.gif");
        ToolbarMain.addButton("Digital", null, "<a style='font-size:16pt;font-weight:bold;background:black;color:white'>To Digital</a>", "save.gif");
        ToolbarMain.addSeparator("Space", null);
        ToolbarMain.addText("text", null, "GLID: ");
        ToolbarMain.addInput("GLID", null, "", 200);
        ToolbarMain.addButton("Find", null, "Find Item", "save.gif");
        ToolbarMain.addSpacer("Digital");
        
        ToolbarMain.attachEvent("onEnter", function(id, value)
        {
            if (id == "GLID")
            {
                window.location = "?GLID=" + ToolbarMain.getValue("GLID").trim();
            }
        });
        ToolbarMain.attachEvent("onClick", function(id)
        {
            if (id == "Find") {
                window.location = "Index.php?GLID=" + ToolbarMain.getValue("GLID");
            } else if (id == "Offset") {
                SendToOffset();
            } else if (id == "Digital") {
                SendToDigital();
            }
        });
        InitForm();
        InitGrid();
    }

    function FSelectAll(){

        var Offset_UPS = Number(DataPEForm.getItemValue("Offset_UPS") );
        var Digital_UPS = Number(DataPEForm.getItemValue("Digital_UPS") );
        var qtySum = 0;
        GridOracle.forEachRow(function(id){
            GridOracle.cells(id,0).setValue(1);
            qtySum += Number(GridOracle.cells(id,3).getValue());
        });

        qtyOffsetSheet = (Offset_UPS != 0 ) ?  Math.ceil(qtySum/Offset_UPS) : 0;
        qtyDigitalSheet = (Digital_UPS != 0 ) ? Math.ceil(qtySum/Digital_UPS) : 0;

        $("#QtySum").html(qtySum );
        $("#SheetSum").html( qtyOffsetSheet + "/" + qtyDigitalSheet );

    }

    function FUnSelectAll(){

        GridOracle.forEachRow(function(id){
            GridOracle.cells(id,0).setValue(0);
            
        });

        $("#QtySum").html('0');
        $("#SheetSum").html( "0/0" );

    }

    function CreateJob(ProductLine){
        var SOLine = [];
        var Remark = "";
        var SOArr = [];
        var DontCombine = false;
        var SHIPTO = '';
			GridOracle.forEachRow(function(id){
				if(GridOracle.cells(id,0).getValue() == 1 && GridOracle.cells(id,8).getValue() == "")
				{
                    //@tandoan - 20200928 Lấy Shipto nếu SOLine được check, cho remark KHONG KIM LOAI
                    if (GridOracle.cellByIndex(0,9).getValue() ) {
                        SHIPTO = GridOracle.cells(id,9).getValue();
                    }

                    if(SOArr.indexOf(GridOracle.cells(id,2).getValue()) === -1) {
                        SOArr.push(GridOracle.cells(id,2).getValue());
                        if(SOArr.length > 20){
                            DontCombine = true;
                        }
                    }
					SOLine.push({
                        "SOLINE":GridOracle.cells(id,1).getValue(),
                        "QTY":GridOracle.cells(id,3).getValue(),
                        "PD":GridOracle.cells(id,7).getValue(),
                        "CRD":GridOracle.cells(id,6).getValue()
                    });

                    if(Remark.indexOf(GridOracle.cells(id,12).getValue()) === -1) {
                        Remark = Remark + "|" + GridOracle.cells(id,12).getValue();
                    }
				}
			});
            if(DontCombine) {
                alert("Không được combine quá 20 SO");
                return;
            }
            if(Remark != "") Remark = Remark.substring(1);
        
        
        var DataTable = {
            "EVENT":"CREATEDJOB", 
            "GLID":GLID, 
            "ORDER":JSON.stringify(SOLine),
            "PL":ProductLine,
            "REMARK":Remark,
            "CODE":DataPEForm.getItemValue("Process"),
            "BO":DataPEForm.getItemValue("Buying_Office"),
            "SHIPTO":SHIPTO
        };

        console.log("Data Create: " + JSON.stringify(DataTable));
        var Result = AjaxAsync("Event.php",DataTable,"POST","HTML");
        console.log("Create Job: " + Result);
        if(Result != "" && Result[0] == "D"){
            WPro = window.open("ProductionRecord.php?JJ=" + Result);
            var intervalID = window.setInterval(function(){
                if(WPro && WPro.closed) {
                    window.clearInterval(intervalID);
                    var DataFGS = AjaxAsync("Event.php", {"EVENT":"GETFGSDATA","GLID":GLID},"POST","HTML");
                    DataPEForm.setItemValue("FGS_Qty",Math.round(DataFGS));
                    LoadDataGrid();
                    LayoutMain.cells("a").progressOff();
                    TurnSend = !TurnSend;
                }
            }, 500);
        } else {
            console.log("save error");
        }
    }

    function InitGrid(){
        GridOracle = LayoutMain.cells("b").attachGrid();
        GridOracle.setImagePath("./Module/dhtmlx/skins/skyblue/imgs/");
        GridOracle.setHeader("Choose,Order Line, Order, Qty, UOM, Customer, Request Date, Promise Date, Job ID, Ship To Customer, Bill To Customer,AF, Customer Request");
        GridOracle.attachHeader("X,#text_search,#text_search,#text_search,#text_search,#text_search,#text_search,#text_search,#text_search,#text_search,#text_search,#text_search,#text_search,#text_search");
        GridOracle.setInitWidths("50,100,130,70,50,140,100,100,90,150,150,50,130")
        GridOracle.setColAlign("center,center,center,center,center,center,center,center,center,center,center,center,center");
        GridOracle.setColTypes("ch,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed");
        GridOracle.setColSorting("str,str,str,int,str,str,str,str,str,str,str,str,str")
        GridOracle.setRowTextStyle("1", "background-color: red; font-family: arial;");
        GridOracle.entBox.id = "GridMain";
        // GridOracle.enableSmartRendering(true);
        GridOracle.attachFooter("Qty: <a ID='QtySum' style='font-weight:bold;font-size:11pt;color:red;'>0</a>,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan",["text-align:left;"]);
        GridOracle.attachFooter("Sheet (Offset/Digital): <a ID='SheetSum' style='font-weight:bold;font-size:12pt;color:red; '>0 / 0</a>,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan",["text-align:left;"]);
        GridOracle.attachFooter("Record: <a ID='NumRowClick' style='font-weight:bold;font-size:10pt'>0 / 0</a>,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan",["text-align:left;"]);

        GridOracle.init();

        
        var Offset_UPS = Number(DataPEForm.getItemValue("Offset_UPS") );
        var Digital_UPS = Number(DataPEForm.getItemValue("Digital_UPS") );
        
        GridOracle.attachEvent("onRowSelect", function(id,ind){
            var qtySum = 0;
            var qtyOffsetSheet = 0;
            var qtyDigitalSheet = 0;
            //var selectedId = GridOracle.getSelectedRowId();
            // GridOracle.forEachRow
            var countLines = 0;
            GridOracle.forEachRow(function(idx){
                if (GridOracle.cells(idx,0).getValue() == 1 ) {
                    countLines = (countLines+1);
                    qtySum = qtySum + Number(GridOracle.cells(idx,3).getValue());
                }

            });

            // Add qty sum, sheet sum @@@@@@@@
                // qtySum += Number(GridOracle.cells(id,3).getValue());
                qtyOffsetSheet = (Offset_UPS != 0 ) ?  Math.ceil(qtySum/Offset_UPS) : 0;
                qtyDigitalSheet = (Digital_UPS != 0 ) ? Math.ceil(qtySum/Digital_UPS) : 0;
                
                $("#QtySum").html(qtySum );
                $("#SheetSum").html( qtyOffsetSheet + " / " + qtyDigitalSheet );
                $("#NumRowClick").html( countLines + " / " + GridOracle.getRowsNum());

            // // if (GridOracle.cells(id,0).getValue() == 1 ) {
            // //     countLines = (countLines+1);
            // //     qtySum = qtySum + Number(GridOracle.cells(id,3).getValue());

            // //     // Add qty sum, sheet sum @@@@@@@@
            // //     // qtySum += Number(GridOracle.cells(id,3).getValue());
            // //     qtyOffsetSheet = (Offset_UPS != 0 ) ?  Math.ceil(qtySum/Offset_UPS) : 0;
            // //     qtyDigitalSheet = (Digital_UPS != 0 ) ? Math.ceil(qtySum/Digital_UPS) : 0;
                
            // //     $("#QtySum").html(qtySum );
            // //     $("#SheetSum").html( qtyOffsetSheet + "/" + qtyDigitalSheet );
                
            // // } else {
            // //     countLines = (countLines-1);
            // //     qtySum = qtySum - Number(GridOracle.cells(id,3).getValue());

            // //     // Add qty sum, sheet sum
            // //     // qtySum += Number(GridOracle.cells(id,3).getValue());
            // //     qtyOffsetSheet = (Offset_UPS != 0 ) ?  Math.ceil(qtySum/Offset_UPS) : 0;
            // //     qtyDigitalSheet = (Digital_UPS != 0 ) ? Math.ceil(qtySum/Digital_UPS) : 0;
                
            // //     $("#QtySum").html(qtySum );
            // //     $("#SheetSum").html( qtyOffsetSheet + "/" + qtyDigitalSheet );
                
            // // }
            // countLines = (GridOracle.cells(id,0).getValue() == 1 ) ? (countLines+1) : (countLines-1);
            console.log('ValueID: '+GridOracle.cells(id,0).getValue());
            console.log('countLines: '+countLines);

            

            

        });
        
        
        GridOracle.attachEvent("onEditCell", function(stage,rId,cInd,nValue,oValue){
            if(cInd == 0) {
                return true;
            } else if(cInd != 0 && nValue != oValue){
                return false;
            }
        });

        
        GridOracle.attachEvent("onCheck", function(rId,cInd,state){
            GridOracle.selectRowById(rId,true,true,true);
        });

        GridOracleIN = LayoutMain.cells("c").attachGrid();
        GridOracleIN.setImagePath("./Module/dhtmlx/skins/skyblue/imgs/");
        GridOracleIN.setHeader("Choose,Order Line, Order, Qty, UOM, Customer, Request Date, Promise Date, Job ID, Ship To Customer, Bill To Customer, AF, Customer Request");
        GridOracleIN.attachHeader(",#text_search,#text_search,#text_search,#text_search,#text_search,#text_search,#text_search,#text_search,#text_search,#text_search,#text_search,#text_search,#text_search");
        GridOracleIN.setInitWidths("50,100,130,70,50,140,100,100,90,150,150,50,130")
        GridOracleIN.setColAlign("center,center,center,center,center,center,center,center,center,center,center,center,center");
        GridOracleIN.setColTypes("ch,ro,ro,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed");
        GridOracleIN.setColSorting("str,str,str,int,str,str,str,str,str,str,str,str,str")
        GridOracleIN.setRowTextStyle("1", "background-color: red; font-family: arial;");
        GridOracleIN.entBox.id = "GridMain";
        GridOracleIN.init();

        GridOracleIN.attachEvent("onEditCell", function(stage,rId,cInd,nValue,oValue){
            if(cInd == 0) {
                return true;
            } else if(cInd != 0 && nValue != oValue){
                return false;
            }
        });


        GridNonOracle = LayoutMain.cells("d").attachGrid();
        GridNonOracle.setImagePath("./Module/dhtmlx/skins/skyblue/imgs/");
        GridNonOracle.setHeader("Choose,Order Line, Order, Qty, UOM, Customer, Request Date, Promise Date, Job ID, Ship To Customer, Bill To Customer, AF, Customer Request");
        GridNonOracle.attachHeader(",#text_search,#text_search,,,,,,,,,,,");
        GridNonOracle.setInitWidths("50,100,130,70,50,140,100,100,90,150,150,50,130")
        GridNonOracle.setColAlign("center,center,center,center,center,center,center,center,center,center,center,center,center");
        GridNonOracle.setColTypes("ro,ro,ro,ro,ed,ed,ed,ed,ed,ed,ed,ed,ed");
        GridNonOracle.setColSorting("str,str,str,int,str,str,str,str,str,str,str,str,str")
        GridNonOracle.setRowTextStyle("1", "background-color: red; font-family: arial;");
        GridNonOracle.enableSmartRendering(true);
        GridNonOracle.entBox.id = "GridMain";
        GridNonOracle.init();
        GridNonOracle.attachEvent("onRowSelect", function(id,ind){
            if(ind == 0){
                var TurnAdd = true;
                GridOracle.forEachRow(function(idx){
                    if(idx == id) TurnAdd = false;
                });
                if(!TurnAdd) return;
                var ArrAdd = [];
                GridNonOracle.forEachCell(id,function(cellObj,ind){
                    ArrAdd.push(GridNonOracle.cells(id,ind).getValue());
                });
            }
            GridOracle.addRow(id,ArrAdd.join(","));
        });

        LoadDataGrid();
    }

    var GridNonOracle;

    function LoadDataGrid(){
        var DataTable = {"EVENT":"LOADORDERITEM", "GLID":GLID};
        var Result = AjaxAsync("Data.php",DataTable,"GET","JSON");
        GridOracle.clearAll();
        GridOracleIN.clearAll();
        GridOracle.parse(Result.UN, function(){
            $("#NumRowClick").html("0 / " + GridOracle.getRowsNum());
        });
        GridOracleIN.parse(Result.IN);
    }

    function DoEditItem() {
        var DataTable = {"EVENT":"UPDATEDATA"};
        DataTable.MAIN = JSON.stringify(DataPEForm.getFormData());
        var Result = AjaxAsync("Event.php",DataTable,"POST");

        var DataGrid = "";
        AccessoryGrid.forEachRow(function(id){
            if(AccessoryGrid.cells(id,1).getValue() != "") {
                DataGrid = DataGrid + 
                            "|" + AccessoryGrid.cells(id,0).getValue() + 
                            "*" + AccessoryGrid.cells(id,1).getValue() + 
                            "*" + AccessoryGrid.cells(id,2).getValue(); 
            }
        });

        Result = AjaxAsync("Event.php",{
                        'EVENT': "SAVEACCESSORY",
                        'GLID': DataPEForm.getItemValue("GLID"),
                        'DATA': DataGrid.substr(1),
                },"POST");
    }
    var DataForm;
    function InitForm() {
        DataPEForm = LayoutMain.cells("a").attachForm();
        DataForm = AjaxAsync("EventForm.php",{"EVENT":"FORMMAINCREATE","GLID": GLID},"GET","json");
        $("#ProductionLine").val(DataForm.PL);
        DataPEForm.loadStruct(DataForm.Main, "json");
        if(DataPEForm.getItemValue("GLID",true) == "") {
            if(GLID != "") dhtmlx.alert("Item chưa setup đồng chí ơi");
        }
        AccessoryGrid = new dhtmlXGridObject(DataPEForm.getContainer("Accessory_Information"));
        AccessoryGrid.load("Data.php?EVENT=LOADACCESSORY&GLID=" + GLID);
    }

    function doOnLoad() {
        dhxWins = new dhtmlXWindows();
        dhxWins.attachViewportTo(document.body);
    }

    function doOnUnload() {
        if (dhxWins != null && dhxWins.unload != null) {
            dhxWins.unload();
            dhxWins = null;
        }
    }
</script>
<body onload="doOnLoad();" onunload="doOnUnload();">
</body>
</html>