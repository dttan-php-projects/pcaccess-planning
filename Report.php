<?php 
    require("./Module/Template.php");
    InitPage("PCDataInformation","Data Information");
?>

<script>
    var LayoutMain;    
    var PageView;
    var DateFrom = getUrl("F");
    var DateTo = getUrl("T");
    var Routing = getUrl("R");

    function DocumentStart()
    {
        PageView = getUrl("P");
        if(DateFrom == undefined || DateTo == undefined) {
            DateFrom = "<?php echo date("Y-m-d", strtotime("- 1 days")); ?>";
            DateTo = "<?php echo date("Y-m-d"); ?>";
        }

        LayoutMain = new dhtmlXLayoutObject({
            parent: document.body,
            pattern: "1C",
            offsets: {
                top: 65,
                bottom:40
            },
            cells: [
                {id: "a", header: true, text: PageView},
            ]
        });
        ToolbarMain.addButton("PrintingSchedule", null, "Printing Schedule", "save.gif");
        ToolbarMain.addButton("PrintingScheduleOffset", null, "Printing Schedule Offset", "save.gif");
        ToolbarMain.addButton("PrintingScheduleDigital", null, "Printing Schedule Digital", "save.gif");
        ToolbarMain.addButton("PrintingScheduleABG", null, "Printing Schedule ABG", "save.gif");
        ToolbarMain.addButton("PrintingScheduleINKJET", null, "Printing Schedule ABG", "save.gif");
        ToolbarMain.addButton("BacklogReport", null, "Backlog Report", "save.gif");
        ToolbarMain.addButton("ItemInformation", null, "Item Information", "save.gif");
        ToolbarMain.addButton("Export", null, "Export Excel", "save.gif");

        ToolbarMain.addText("TitleText", null, '<textarea  style="height:0px;width:0" id="ClipBoard"/>');
        ToolbarMain.attachEvent("onClick", function(id)
        {
            if (id == "Export") {
                var Header1 = "";
				for(var i = 0; i< 100; i++) {
					Header1 = Header1 + GridMain.getColLabel(i) + ",";
					if(GridMain.getColLabel(i) == "") break;
				}

                GridMain.csvParser = GridMain.csvExtParser;
                GridMain.setCSVDelimiter(',');
                GridMain.csv.row = "\r\n";
                var gridCsvData = GridMain.serializeToCSV();
				download("Data " + PageView + ".csv", Header1 + "\r\n" + gridCsvData);

            } else if(id=="PrintingSchedule") {
                window.open("http://147.121.59.138/Intranet/Report/PCSchedulePrint.php");
            } else if(id=="PrintingScheduleOffset") {
                window.open("http://147.121.59.138/Intranet/Report/PCSchedulePrint.php?PL=OFFSET");
            } else if(id=="PrintingScheduleDigital") {
                window.open("http://147.121.59.138/Intranet/Report/PCSchedulePrint.php?PL=DIGITAL");
            } else if(id=="PrintingScheduleABG") {
                window.open("http://147.121.59.138/Intranet/Report/PCSchedulePrint.php?PL=DIGITAL");
            } else if(id=="PrintingScheduleINKJET") {
                window.open("http://147.121.59.138/Intranet/Report/PCSchedulePrint.php?PL=INKJET");
            }
        });



        InitGrid();
        InitToolbar();
    }

    var ToolbarCalendar;
    var GridMain;
    var dp;
    var ToolbarBot;
    function InitToolbar(){
        ToolbarBot = new dhtmlXToolbarObject({
                parent: "ToolbarBottomBot",
                align: "right",
                icons_path: "/Module/dhtmlx/common/imgs/"
            });
            ToolbarBot.addText("text_from", null, "From");
            ToolbarBot.addInput("date_from", null, "", 75);
            ToolbarBot.addText("text_to", null, "To");
            ToolbarBot.addInput("date_to", null, "", 75);
            ToolbarBot.addButton("OrderCreated", null, "Order Created Report", "save.gif");
            ToolbarBot.addButton("OrderShipment", null, "Order Shipment Report", "save.gif");
            ToolbarBot.addButton("OrderProcess", null, "Order Process Report", "save.gif");
            ToolbarBot.addButton("SOLine", null, "SOLine Report", "save.gif");
            ToolbarBot.addButton("OrderReceive", null, "Received Report", "save.gif");
            ToolbarBot.addButton("OrderReceiveNon", null, "Received But No Create", "save.gif");
            ToolbarBot.addButton("ProcessOutput", null, "Process Output Report", "save.gif");
            ToolbarBot.addText("ProcessOutput", null, `<select ID="Process">
                                                            <option value="Plate making">Plate making</option>
                                                            <option value="Paper Cutting">Paper Cutting</option>
                                                            <option value="Printing">Printing</option>
                                                            <option value="Varnishing">Varnishing</option>
                                                            <option value="Special varnishing">Special varnishing</option>
                                                            <option value="UV">UV</option>
                                                            <option value="Foil-sigle side">Foil-sigle side</option>
                                                            <option value="Foil-double side">Foil-double side</option>
                                                            <option value="Lamination">Lamination</option>
                                                            <option value="PP Lamination-single side">PP Lamination-single side</option>
                                                            <option value="PP Lamination-double side">PP Lamination-double side</option>
                                                            <option value="Eyeleting">Eyeleting</option>
                                                            <option value="String">String</option>
                                                            <option value="Pin">Pin</option>
                                                            <option value="Punching/Clear hole">Punching/Clear hole</option>
                                                            <option value="Drilling">Drilling</option>
                                                            <option value="Embossing">Embossing</option>
                                                            <option value="Debossing">Debossing</option>
                                                            <option value="Double sides tape">Double sides tape</option>
                                                            <option value="Gluing">Gluing</option>
                                                            <option value="Folding">Folding</option>
                                                            <option value="Labeling">Labeling</option>
                                                            <option value="Saddle stitching(dong kim)">Saddle stitching(dong kim)</option>
                                                            <option value="Line Impressing/Can van">Line Impressing/Can van</option>
                                                            <option value="Combo hangtag">Combo hangtag</option>
                                                            <option value="Die cutting">Die cutting</option>
                                                            <option value="Pack & Sort">Pack & Sort</option>
                                                            <option value="Spot Varnish">Spot Varnish</option>
                                                            <option value="Full Varnish">Full Varnish</option>
                                                            <option value="Combine Hangtag">Combine Hangtag</option>
                                                            <option value="Dong goi ban thanh pham">Dong goi ban thanh pham</option>
                                                            <option value="Dan Nhan Thermal RFID">Dan Nhan Thermal RFID</option>
                                                            <option value="Inpector Base Sheet">Inpector Base Sheet</option>
                                                </select>`);


            input_from = ToolbarBot.getInput("date_from");
            input_from.setAttribute("readOnly", "false");
            input_from.onclick = function(){ setSens(input_till,"max"); }
            
            input_till = ToolbarBot.getInput("date_to");
            input_till.setAttribute("readOnly", "false");
            input_till.onclick = function(){ setSens(input_from,"min"); }
            ToolbarCalendar = new dhtmlXCalendarObject([input_from,input_till]);
            ToolbarCalendar.setDateFormat("%Y-%m-%d");
            ToolbarBot.setValue("date_from",DateFrom);
            ToolbarBot.setValue("date_to",DateTo);
            ToolbarBot.attachEvent("onClick", function(name){
                window.open("ReportData.php?EVENT=" + name.toUpperCase() + "&F=" + ToolbarBot.getValue("date_from") + "&T=" + ToolbarBot.getValue("date_to") + "&R=" + $("#Process").val());
            });
    }

    function setSens(inp, k) {
        if (k == "min") {
            ToolbarCalendar.setSensitiveRange(inp.value, null);
        } else {
            ToolbarCalendar.setSensitiveRange(null, inp.value);
        }
    }

    function download(filename, text) {
		var element = document.createElement('a');
		element.setAttribute('href', 'data:text/plain;charset=utf-8,' + encodeURIComponent(text));
		element.setAttribute('download', filename);

		element.style.display = 'none';
		document.body.appendChild(element);

		element.click();

		document.body.removeChild(element);
	}


    function ProductionOpenGrid(PL){
        GridMain.setImagePath("/Module/dhtmlx/skins/skyblue/imgs/");
        GridMain.setRowTextStyle("1", "background-color: red; font-family: arial;");
        GridMain.entBox.id = "GridMain";
        GridMain.enableBlockSelection(true);
        GridMain.init();
        GridMain.load("ReportData.php?EVENT=" + PL);
    }

    function InitGrid(){
        GridMain = LayoutMain.cells("a").attachGrid();
        if(PageView == null || PageView == "PrintingSchedule") ProductionOpenGrid("PRODUCTIONOPEN&PL=");
        else if(PageView == "PrintingScheduleOffset") ProductionOpenGrid("PRODUCTIONOPEN&PL=Offset");
        else if(PageView == "PrintingScheduleDigital") ProductionOpenGrid("PRODUCTIONOPEN&PL=Digital");
        else if(PageView == "PrintingScheduleABG") ProductionOpenGrid("PRODUCTIONOPEN&PL=ABG");
        else if(PageView == "BacklogReport") ProductionOpenGrid("BACKLOGREPORT");
        else if(PageView == "OrderCreated") ProductionOpenGrid("ORDERCREATED&F=" + DateFrom + "&T=" + DateTo);
        else if(PageView == "OrderShipment") ProductionOpenGrid("ORDERSHIPMENT&F=" + DateFrom + "&T=" + DateTo);
        else if(PageView == "OrderProcess") ProductionOpenGrid("ORDERPROCESS&F=" + DateFrom + "&T=" + DateTo);
        else if(PageView == "SOLine") ProductionOpenGrid("SOLINE&F=" + DateFrom + "&T=" + DateTo);
        else if(PageView == "OrderReceive") ProductionOpenGrid("ORDERRECEIVE&F=" + DateFrom + "&T=" + DateTo);
        else if(PageView == "OrderReceiveNon") ProductionOpenGrid("ORDERRECEIVE&F=" + DateFrom + "&T=" + DateTo);
        else if(PageView == "ProcessOutput") ProductionOpenGrid("PROCESSOUTPUT&F=" + DateFrom + "&T=" + DateTo + "&R=" + Routing);
        // else if(PageView == "ItemInformation") ProductionOpenGrid("ITEMINFORMATION");
            
        
       
        GridMain.attachEvent("onKeyPress", function(code,cFlag,sFlag){
            if(cFlag && code == 67) { //Copy
                window.setTimeout(function(){
                    var top_row = GridMain.getSelectedBlock().LeftTopRow;
                    var bottom_row = GridMain.getSelectedBlock().RightBottomRow;
                    var left_column = GridMain.getSelectedBlock().LeftTopCol;
                    var right_column = GridMain.getSelectedBlock().RightBottomCol;
                    var DataCB = "";
                    var DataCB1 = "";
                    if(typeof top_row == "string") {
                        DataCB = GridMain.cells(top_row,left_column).getValue()
                    } else {
                        for(var i = top_row; i <= bottom_row; i++){
                            for(var j = left_column; j <= right_column; j++) {
                                DataCB1 += "\t" + GridMain.cells2(i,j).getValue();
                            }
                            DataCB += DataCB1.substring(1) + "\n";
                            DataCB1 = "";
                        }
                    }
                    $("#ClipBoard").val(DataCB);

                    var copyText = document.getElementById("ClipBoard");
                    copyText.select();
                    document.execCommand("copy");

                },1);
            }
            return true;
        });
    }
    
    function LoadDataGrid(){
        // var DataTable = {"EVENT":"LOADDATAINFORMATION"};
        // var Result = AjaxAsync("Data.php",DataTable,"GET","JSON");
        // GridOracle.clearAll();
        // GridOracleIN.clearAll();
        // GridOracle.parse(Result.UN, function(){
        //     $("#NumRowClick").html("0/" + GridOracle.getRowsNum());
        // });
        // GridOracleIN.parse(Result.IN);
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
<div style="position:absolute;width:100%;height:35px;bottom:0;background:white">
		<div id="ToolbarBottomBot" ></div> 
	</div>
</body>
</html>