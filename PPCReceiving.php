<?php 
    require("./Module/Template.php");
    InitPage("PPCReceiving","ADVN Offset Digital Receiving Order");
?>

<script>
    var LayoutMain;
    var ToolbarBot;
    function download(filename, text) {
		var element = document.createElement('a');
		element.setAttribute('href', 'data:text/plain;charset=utf-8,' + encodeURIComponent(text));
		element.setAttribute('download', filename);
		element.style.display = 'none';
		document.body.appendChild(element);
		element.click();
		document.body.removeChild(element);
	}
    var DateFrom = getUrl("F");
    var DateTo = getUrl("T");
    function DocumentStart() {
        if(DateFrom == undefined || DateTo == undefined) {
            DateFrom = "<?php echo date("Y-m-d 00:00:00", strtotime("- 1 days")); ?>";
            DateTo = "<?php echo date("Y-m-d 23:59:00"); ?>";
        }
        LayoutMain = new dhtmlXLayoutObject({
            parent: document.body,
            pattern: "1C",
            offsets: {
                top: 65,
                bottom: 40
            },
            cells: [
                {id: "a", header: false, text: "Control Panel"},
            ]
        });

        ToolbarMain.addText("id", null, `<form action="UploadData.php?USERNAME=` + UserVNRIS + `" enctype="multipart/form-data" method="post">
                                            <input id="FileToUpload" name="FileToUpload" type="file"/>
                                            <input name="submit" type="submit" value="Upload" />
                                        </form>`);
        ToolbarMain.addButton("ExportExcel", null, "Export Excel", "save.gif");
        ToolbarMain.addSeparator("Space", null);
        ToolbarMain.addSeparator("Space", null);
        ToolbarMain.addSeparator("Space", null);

        ToolbarMain.addText("text", null, "SOLINE: ");
        ToolbarMain.addInput("JobJacket", null, "", 200);
        ToolbarMain.attachEvent("onClick", function(name){
            if(name == "ExportExcel"){
                var ArrLine = []
                var Header1 = "";
                var X = 0;
				for(var i = 0; i< 100; i++)
				{
					Header1 = Header1 + GridMain.getColLabel(i) + ",";
					if(GridMain.getColLabel(i) == "") {
                        X++;
                        if(X > 3) break;
                    }
                    X = 0;
				}

                GridMain.csvParser = GridMain.csvExtParser;
                GridMain.setCSVDelimiter(',');
                GridMain.csv.row = "\r\n";
                var gridCsvData = GridMain.serializeToCSV();
				download("DataOrderReceived.csv", Header1 + "\r\n" + gridCsvData);
            }
        });

        ToolbarMain.attachEvent("onEnter", function(id, value) {
            GridMain.clearAll();
            GridMain.loadXML("Data.php?EVENT=LOADPPCRECEIVINGSOLINE&SOLINE=" + value);
        });

        ToolbarBot = new dhtmlXToolbarObject({
            parent: "ToolbarBottomBot",
            align: "right",
            icons_path: "./Module/dhtmlx/common/imgs/"
        });
        ToolbarBot.addText("text_from", null, "From");
        ToolbarBot.addInput("date_from", null, "", 150);
        ToolbarBot.addText("text_to", null, "To");
        ToolbarBot.addInput("date_to", null, "", 150);
        ToolbarBot.addButton("View", null, "<a style='font-size:14pt;font-weight:bold'>View</a>", "save.gif");
        input_from = ToolbarBot.getInput("date_from");
        // input_from.setAttribute("readOnly", "false");
        input_from.onclick = function(){ setSens(input_till,"max"); }
        input_till = ToolbarBot.getInput("date_to");
        // input_till.setAttribute("readOnly", "false");
        input_till.onclick = function(){ setSens(input_from,"min"); }
        ToolbarCalendar = new dhtmlXCalendarObject([input_from,input_till]);
        ToolbarCalendar.setDateFormat("%Y-%m-%d %H:%i:%s");
        ToolbarBot.setValue("date_from",DateFrom);
        ToolbarBot.setValue("date_to",DateTo);
        ToolbarBot.attachEvent("onClick", function(name){
            location.href = "?F=" + ToolbarBot.getValue("date_from") + "&T=" + ToolbarBot.getValue("date_to") + "&P=" + name;
        });
        InitGrid();
    }

    var GridItem;

    function InitGrid(){

        GridMain = LayoutMain.cells("a").attachGrid();
        GridMain.setImagePath("./Module/dhtmlx/skins/skyblue/imgs/");
        GridMain.setHeader("No, ORDER#, LINE#, SOLINE, CUSTOMER PO, ORDERED ITEM, INTERNAL ITEM, QTY, UOM, REQUEST DATE, PROMISE DATE, SHIP TO CUSTOMER, BILL TO CUSTOMER, PPC Receiving Time, Issue, Customer Request, JobJacket,Order Status,Urgent");
        GridMain.setInitWidths("50,100,50,100,120,120,120,50,70,100,100,*,*,*,50,200,100,100,100");
        GridMain.setColumnMinWidth("50,100,50,100,120,120,120,50,70,100,100,150,150,150,50,200,100,100,100");
        GridMain.attachHeader(",#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,,,,,,,,,");
        GridMain.setColAlign("center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center");
        GridMain.setColTypes("ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ch,ed,ro,ro,ro");
        GridMain.setColSorting("str,str,str,str,str,str,str,str,str,str,str,str,str,str,str,str,str,str,str")
        GridMain.setRowTextStyle("1", "background-color: red; font-family: arial;");
        GridMain.entBox.id = "GridMain";
        GridMain.enableSmartRendering(true);
        GridMain.enableBlockSelection(true);

        GridMain.init();
        GridMain.loadXML("Data.php?EVENT=LOADPPCRECEIVING&F=" + DateFrom + "&T=" + DateTo);

        var dp = new dataProcessor("Data.php?EVENT=LOADPPCRECEIVING&F=" + DateFrom + "&T=" + DateTo); 
        dp.init(GridMain);

        GridMain.attachEvent("onKeyPress", function(code,cFlag,sFlag){
            if(sFlag && code == 46) { //Delete
                window.setTimeout(function(){
                    var top_row = GridMain.getSelectedBlock().LeftTopRow;
                    var bottom_row = GridMain.getSelectedBlock().RightBottomRow;
                    var left_column = GridMain.getSelectedBlock().LeftTopCol;
                    var right_column = GridMain.getSelectedBlock().RightBottomCol;
                    if(typeof top_row == "string") {
                        GridMain.deleteRow(top_row);
                        dp.setUpdated(top_row,true);
                    } else {
                        for(var i = top_row; i <= bottom_row; i++){
                            GridMain.deleteRow(GridMain.getRowId(i));
                            dp.setUpdated(GridMain.getRowId(i),true);
                        }
                    }
                },1);
            } else if(!sFlag && !cFlag && code == 46) { //Delete
                window.setTimeout(function(){
                    var top_row = GridMain.getSelectedBlock().LeftTopRow;
                    var bottom_row = GridMain.getSelectedBlock().RightBottomRow;
                    var left_column = GridMain.getSelectedBlock().LeftTopCol;
                    var right_column = GridMain.getSelectedBlock().RightBottomCol;
                    if(left_column != 15 || left_column != 15) return true;
                    for(var i = top_row; i <= bottom_row; i++){
                        for(var j = left_column; j <= right_column; j++) {
                            GridMain.cells2(i,j).setValue("");
                        }
                        dp.setUpdated(GridMain.getRowId(i),true);
                    }
                },1);
            }
            return true;
        });
    }



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
    <div style="position:absolute;width:100%;height:35px;bottom:0;background:white">
		<div id="ToolbarBottomBot" ></div> 
	</div>
</body>
</html>