<?php 
    require("./Module/Template.php");
    InitPage("PCItemMaster","Items Master - Offset & Digital");
?>

<script>
    var LayoutMain;
    var DataPEForm;
    var AccessoryGrid;
    var GLID = "";
    GLID = getUrl("GLID");
    function DocumentStart() {
        LayoutMain = new dhtmlXLayoutObject({
            parent: document.body,
            pattern: "1C",
            offsets: {
                top: 65
            },
            cells: [
                {id: "a", header: false, text: "Control Panel", width: 300}
            ]
        });
        if(GLID == null) GLID = "";
        ToolbarMain.addText("text", null, "GLID: ");
        ToolbarMain.addInput("GLID", null, GLID.replaceAll("||","&"), 200);

        ToolbarMain.addButton("Find", null, "Find Item", "save.gif");
        ToolbarMain.addButton("New", null, "Add New", "save.gif");
        if(GLID != "") {
            ToolbarMain.addButton("Save", null, "Save Record", "save.gif");
            ToolbarMain.addButton("Copy", null, "Copy Record", "save.gif");
            ToolbarMain.addButton("Delete", null, "Delete", "save.gif");
        }
        ToolbarMain.addSpacer("Title");
        ToolbarMain.attachEvent("onEnter", function(id, value) {
            if (id == "GLID") window.location = "?GLID=" + ToolbarMain.getValue("GLID").trim().replaceAll("&","||");
        });
        ToolbarMain.attachEvent("onClick", function(id){
            if (id == "Find") window.location = "?GLID=" + ToolbarMain.getValue("GLID").trim().replaceAll("&","||");
            else if (id == "New") {
                DataPEForm.setReadonly("GLID", false);
                DataPEForm.clear();
                ToolbarMain.removeItem("New");
                ToolbarMain.removeItem("Save");
                ToolbarMain.removeItem("Copy");
                ToolbarMain.removeItem("Delete");
                ToolbarMain.addButton("Save", null, "Save Record", "save.gif");
            } else if (id == "Copy") {
                DataPEForm.setReadonly("GLID", false);
                ToolbarMain.removeItem("New");
                ToolbarMain.removeItem("Copy");
                ToolbarMain.removeItem("Delete");
            } else if (id == "Save") {
                DoEditItem();	
                // window.location = "?GLID=" + DataPEForm.getItemValue("GLID").trim();
            } else if (id == "Delete") {
                Result = AjaxAsync("Event.php",{
                        'EVENT': "DELETEITEM",
                        'GLID': DataPEForm.getItemValue("GLID")
                },"POST");	
                window.location = "ItemMaster.php";
		
            }
        });
        InitForm();
    }

    function DoEditItem() {
        var DataTable = {"EVENT":"UPDATEDATA"};
        DataTable.MAIN = JSON.stringify(DataPEForm.getFormData());
        var Result = AjaxAsync("Event.php",DataTable,"POST");
        var DataGrid = "";

        console.log('DataTable: ' + JSON.stringify(DataTable) );
        AccessoryGrid.forEachRow(function(id){
            if(AccessoryGrid.cells(id,1).getValue() != "" || AccessoryGrid.cells(id,2).getValue() != "" || AccessoryGrid.cells(id,0).getValue() != "") {
                DataGrid = DataGrid + 
                            "|" + AccessoryGrid.cells(id,0).getValue() + 
                            "^^" + AccessoryGrid.cells(id,1).getValue() + 
                            "^^" + AccessoryGrid.cells(id,2).getValue(); 
            }
        });

        Result = AjaxAsync("Event.php",{
                        'EVENT': "SAVEACCESSORY",
                        'GLID': DataPEForm.getItemValue("GLID"),
                        'DATA': DataGrid.substr(1),
                },"POST");
        if(Result == "OK") dhtmlx.alert("Đã save");
    }

    function InitForm() {
        DataPEForm = LayoutMain.cells("a").attachForm();
        var DataForm = AjaxAsync("EventForm.php",{"EVENT":"FORMMAIN","GLID": GLID},"GET","json");
        DataPEForm.loadStruct(DataForm.Main, "json");
        AccessoryGrid = new dhtmlXGridObject(DataPEForm.getContainer("Accessory_Information"));
        AccessoryGrid.enableBlockSelection(true);
        AccessoryGrid.load("Data.php?EVENT=LOADACCESSORY&GLID=" + GLID);

        AccessoryGrid.attachEvent("onKeyPress", function(code,cFlag,sFlag){
                if(!sFlag && code == 46) { //Delete
                    window.setTimeout(function(){
                        var top_row = AccessoryGrid.getSelectedBlock().LeftTopRow;
                        var bottom_row = AccessoryGrid.getSelectedBlock().RightBottomRow;
                        var left_column = AccessoryGrid.getSelectedBlock().LeftTopCol;
                        var right_column = AccessoryGrid.getSelectedBlock().RightBottomCol;
                        if(typeof top_row == "string") {
                            AccessoryGrid.cells(top_row,left_column).setValue("");
                        } else { 
                            for(var i = top_row; i <= bottom_row; i++){
                                for(var j = left_column; j <= right_column; j++) {
                                    AccessoryGrid.cells2(i,j).setValue("");
                                }
                            }
                        }
                    },1);
                    return true;
                } else if(sFlag && code == 46) { //Delete
                    window.setTimeout(function(){
                        var top_row = AccessoryGrid.getSelectedBlock().LeftTopRow;
                        var bottom_row = AccessoryGrid.getSelectedBlock().RightBottomRow;
                        var left_column = AccessoryGrid.getSelectedBlock().LeftTopCol;
                        var right_column = AccessoryGrid.getSelectedBlock().RightBottomCol;
                        if(typeof top_row == "string") {
                            AccessoryGrid.deleteRow(top_row);
                        } else {
                            for(var i = top_row; i <= bottom_row; i++){
                                AccessoryGrid.deleteRow(AccessoryGrid.getRowId(i));
                            }
                        }
                    },1);
                }
                return true;
        });


        DataPEForm.attachEvent("onButtonClick", function(name){
            if(name == "DetailProcess") {
                if(DataPEForm.getItemValue("GLID") != "") PProcessDetail(DataPEForm.getItemValue("GLID"),DataPEForm.getItemValue("Process"));
                else alert("Please Check GLID");
            }
        });

        DataPEForm.attachEvent("onChange", function (name, value, state){
				if(name == "Dig_Material" || name == "Material_Code") {
					for(var i = 0; i < Stock_Code_Array.length; i++) {
						if(Stock_Code_Array[i]["value"] == value) return;
					}
					DataPEForm.setItemValue(name, "");
					alert("Vui lòng kiểm tra vật tư");
				} else if(name == "Suited_Machine" || name == "Digital_Machine") {
					for(var i = 0; i < DataForm.DigMachine_Array.length; i++) {
						if(DataForm.DigMachine_Array[i]["value"] == value) {
							return;
						}
					}
					DataPEForm.setItemValue(name, "");
					alert("Vui lòng kiểm tra máy");
				} else if(name == "Digital_Available") {
					for(var i = 0; i < DataForm.Digital_Availability.length; i++) {
						if(DataForm.Digital_Availability[i]["value"] == value) {
							return;
						}
					}
                    DataPEForm.setItemValue(name, "");
                    alert("Vui lòng kiểm tra Digital Available");
				}
            });
            
        DataPEForm.attachEvent("onButtonClick", function(name){
            if(name == "DetailProcess") {
                if(DataPEForm.getItemValue("GLID") != "") PProcessDetail(DataPEForm.getItemValue("GLID"),DataPEForm.getItemValue("Process"));
                else alert("Please Check GLID");
                
            }
        });
    };
    function PProcessDetail(GLID, CODE) {
        var id = "WindowsDetail";
        var w = Number($(window).width() - 400);	var h = Number($(window).height() - 200);	var x = Number(200);	var y = Number(100);
        var Popup = dhxWins.createWindow(id, x, y, w, h);
        dhxWins.window(id).setText("Process Detail");
        <?php 
            $ProcessCode = array();
            $RowsTable = MiQuery( "SELECT Code,Process FROM access_item_process;", _conn());
            foreach($RowsTable as $row) {
                array_push($ProcessCode, $row);
            }

            echo "var ProcessCode = " . json_encode($ProcessCode) . ";";
        ?>
        var DataFormProcess = [
				{type: "settings", position: "label-left", labelWidth: 90, inputWidth: 130},
				{type: "block", width: "auto", blockOffset: 20, list: [
					{type: "input", name: "GLID", label: "Item Code:", value: "", inputWidth: "400"},
					{type: "input", name: "Code", label: "Code:", value: "", position: "label-top", inputWidth: "495"}
				]},
				{type: "block", width: "auto", blockOffset: 20, offsetTop: "20", list: [
					{type: "combo", label: "<a ondblclick=\"DownPro(\'1\')\">Process 01:</a> ", value: "", filtering: true, name: "Process01", options:[
								<?php 
									echo '{text: "", value: "", selected: true}';
									foreach($ProcessCode as $Rows)
									{
										echo ',{text: "' . $Rows["Process"] . '", value: "' . $Rows["Code"] . '"}';
									}
								?>
							]},
					<?php 
						for($i = 2; $i < 16; $i++) {
							$Index = str_pad($i, 2, '0', STR_PAD_LEFT);
							echo '{type: "combo", label: "<a ondblclick=\"DownPro(\''.$i.'\')\">Process ' .$Index. ':</a>", value: "", offsetTop: "12", name: "Process'.$Index.'", filtering: true, options:[';
							echo '{text: "", value: "", selected: true}';
							foreach($ProcessCode as $Rows) echo ',{text: "' . $Rows["Process"] . '", value: "' . $Rows["Code"] . '"}';
							echo ']},';
						}
					?>
					{type: "newcolumn"},
					{type: "button", value: "<a style='color:red;font-weight:bold'>X</a>", offsetTop: "0", name: "BDEL01"},
					{type: "button", value: "<a style='color:red;font-weight:bold'>X</a>", offsetTop: "0", name: "BDEL02"},
					{type: "button", value: "<a style='color:red;font-weight:bold'>X</a>", offsetTop: "0", name: "BDEL03"},
					{type: "button", value: "<a style='color:red;font-weight:bold'>X</a>", offsetTop: "0", name: "BDEL04"},
					{type: "button", value: "<a style='color:red;font-weight:bold'>X</a>", offsetTop: "0", name: "BDEL05"},
					{type: "button", value: "<a style='color:red;font-weight:bold'>X</a>", offsetTop: "0", name: "BDEL06"},
					{type: "button", value: "<a style='color:red;font-weight:bold'>X</a>", offsetTop: "0", name: "BDEL07"},
					{type: "button", value: "<a style='color:red;font-weight:bold'>X</a>", offsetTop: "0", name: "BDEL08"},
					{type: "button", value: "<a style='color:red;font-weight:bold'>X</a>", offsetTop: "0", name: "BDEL09"},
					{type: "button", value: "<a style='color:red;font-weight:bold'>X</a>", offsetTop: "0", name: "BDEL10"},
					{type: "button", value: "<a style='color:red;font-weight:bold'>X</a>", offsetTop: "0", name: "BDEL11"},
					{type: "button", value: "<a style='color:red;font-weight:bold'>X</a>", offsetTop: "0", name: "BDEL12"},
					{type: "button", value: "<a style='color:red;font-weight:bold'>X</a>", offsetTop: "0", name: "BDEL13"},
					{type: "button", value: "<a style='color:red;font-weight:bold'>X</a>", offsetTop: "0", name: "BDEL14"},
					{type: "button", value: "<a style='color:red;font-weight:bold'>X</a>", offsetTop: "0", name: "BDEL15"},
					{type: "newcolumn"},
					{type: "button", value: "DOWN", offsetTop: "0", name: "BDOWN01"},
					{type: "button", value: "DOWN", offsetTop: "0", name: "BDOWN02"},
					{type: "button", value: "DOWN", offsetTop: "0", name: "BDOWN03"},
					{type: "button", value: "DOWN", offsetTop: "0", name: "BDOWN04"},
					{type: "button", value: "DOWN", offsetTop: "0", name: "BDOWN05"},
					{type: "button", value: "DOWN", offsetTop: "0", name: "BDOWN06"},
					{type: "button", value: "DOWN", offsetTop: "0", name: "BDOWN07"},
					{type: "button", value: "DOWN", offsetTop: "0", name: "BDOWN08"},
					{type: "button", value: "DOWN", offsetTop: "0", name: "BDOWN09"},
					{type: "button", value: "DOWN", offsetTop: "0", name: "BDOWN10"},
					{type: "button", value: "DOWN", offsetTop: "0", name: "BDOWN11"},
					{type: "button", value: "DOWN", offsetTop: "0", name: "BDOWN12"},
					{type: "button", value: "DOWN", offsetTop: "0", name: "BDOWN13"},
					{type: "button", value: "DOWN", offsetTop: "0", name: "BDOWN14"},
					{type: "newcolumn"},
					{type: "button", value: "UP", offsetTop: "36", name: "BUP02"},
					{type: "button", value: "UP", offsetTop: "0", name: "BUP03"},
					{type: "button", value: "UP", offsetTop: "0", name: "BUP04"},
					{type: "button", value: "UP", offsetTop: "0", name: "BUP05"},
					{type: "button", value: "UP", offsetTop: "0", name: "BUP06"},
					{type: "button", value: "UP", offsetTop: "0", name: "BUP07"},
					{type: "button", value: "UP", offsetTop: "0", name: "BUP08"},
					{type: "button", value: "UP", offsetTop: "0", name: "BUP09"},
					{type: "button", value: "UP", offsetTop: "0", name: "BUP10"},
					{type: "button", value: "UP", offsetTop: "0", name: "BUP11"},
					{type: "button", value: "UP", offsetTop: "0", name: "BUP12"},
					{type: "button", value: "UP", offsetTop: "0", name: "BUP13"},
					{type: "button", value: "UP", offsetTop: "0", name: "BUP14"},
					{type: "button", value: "UP", offsetTop: "0", name: "BUP15"},
					{type: "newcolumn"},
					{type: "button", label: "New Input", value: "Clear Code", offsetLeft: "25", name: "ClearCode"},
					{type: "button", label: "New Input", value: "Normal Process", offsetTop: "30", offsetLeft: "25", name: "NormalProcess"},
                    {type: "button", label: "New Input", value: "Normal Process Offset", offsetTop: "15", offsetLeft: "25", name: "NormalProcessOffset"},
                    {type: "button", label: "New Input", value: "Normal Process ABG", offsetTop: "15", offsetLeft: "25", name: "NormalProcessABG"},
                    {type: "button", label: "New Input", value: "Normal Process Trimming", offsetTop: "15", offsetLeft: "25", name: "NormalProcessTrimming"},
                    
					{type: "button", label: "New Input", value: "Build Code LT", offsetTop: "50", offsetLeft: "25", name: "BuildCode"},
					{type: "button", label: "New Input", value: "Confirm Save", offsetTop: "50", offsetLeft: "25", name: "ConfirmSave"},
					{type: "newcolumn"}
				]}
			];
        DataProcess = Popup.attachForm();
        DataProcess.loadStruct(DataFormProcess, "json");
        DataProcess.setItemValue("GLID", GLID);
        DataProcess.setItemValue("Code", CODE);
        DataProcess.attachEvent("onChange", function (name, value, state){
				if(name.indexOf("Process") !== -1) {
					for(var i = 0; i < ProcessCode.length; i++) {
						if(ProcessCode[i]["Code"] == value) return;
					}
					DataProcess.setItemValue(name, "");
					alert("Vui lòng kiểm tra process");
				}
			})
        let NumCode = 2;
        let Binhdeptrai = "";
        for(let i = 0; i < CODE.length/4 ; i++) {
            DataProcess.setItemValue("Process" + ("0" + (i + 1)).slice(-2), CODE.substring(NumCode, NumCode + 2));
            NumCode = NumCode + 4;
        }
        DataProcess.attachEvent("onButtonClick", function(name){
            let Si = 0;
            if(name == "NormalProcess") {
                DataProcess.setItemValue("Process01", "ML");
                DataProcess.setItemValue("Process02", "PM");
                DataProcess.setItemValue("Process03", "PC");
                DataProcess.setItemValue("Process04", "PR");
                DataProcess.setItemValue("Process05", "VA");
                DataProcess.setItemValue("Process06", "DC");
                DataProcess.setItemValue("Process07", "PS");
            } else if(name == "NormalProcessOffset") {

                DataProcess.setItemValue("Process01", "ML");
                DataProcess.setItemValue("Process02", "PM");
                DataProcess.setItemValue("Process03", "PC");
                DataProcess.setItemValue("Process04", "PV");
                DataProcess.setItemValue("Process05", "DC");
                DataProcess.setItemValue("Process06", "PS");

            } else if(name == "NormalProcessABG") {

                DataProcess.setItemValue("Process01", "ML");
                DataProcess.setItemValue("Process02", "PM");
                DataProcess.setItemValue("Process03", "PC");
                DataProcess.setItemValue("Process04", "PR");
                DataProcess.setItemValue("Process05", "VD");
                DataProcess.setItemValue("Process06", "PS");


            } else if(name == "NormalProcessTrimming") {

                DataProcess.setItemValue("Process01", "ML");
                DataProcess.setItemValue("Process02", "PM");
                DataProcess.setItemValue("Process03", "TR");
                DataProcess.setItemValue("Process04", "PV");
                DataProcess.setItemValue("Process05", "DC");
                DataProcess.setItemValue("Process06", "PS");
                

            } else if(name == "ClearCode") {
                DataProcess.setItemValue("Code", "");
                for(let i = 1; i < 16; i++) {
                    Si = ("0" + i).slice(-2);
                    DataProcess.setItemValue("Process"+Si, "");
                }
            } else if(name == "BuildCode") {
                let CreateCode = "";
                let NumStep = 1;
                for(let i = 1; i < 16; i++) {
                    Si = ("0" + i).slice(-2);
                    if(DataProcess.getItemValue("Process"+Si)) {
                        CreateCode = CreateCode + ("0" + NumStep).slice(-2) + DataProcess.getItemValue("Process"+Si, "");
                        NumStep++;
                    }
                }

                DataProcess.setItemValue("Code", CreateCode)
            } else if(name == "ConfirmSave") {
                DataPEForm.setItemValue("Process", DataProcess.getItemValue("Code"));
                doOnUnload();
                doOnLoad();
            } else if(name.indexOf("BDOWN") == 0) {
                let Num = parseInt(name.substring(5,7)); //Có vấn đề, Cần check lại
                let TempProcess = DataProcess.getItemValue("Process"+("0" + Num).slice(-2));
                DataProcess.setItemValue("Process"+("0" + Num).slice(-2), DataProcess.getItemValue("Process"+("0" + (Num + 1)).slice(-2)));
                DataProcess.setItemValue("Process"+("0" + (Num + 1)).slice(-2), TempProcess);
            } else if(name.indexOf("BUP") == 0) {
                let Num = parseInt(name.substring(3,5)); //Có vấn đề, Cần check lại
                let TempProcess = DataProcess.getItemValue("Process"+("0" + (Num - 1)).slice(-2));
                DataProcess.setItemValue("Process"+("0" + (Num - 1)).slice(-2), DataProcess.getItemValue("Process"+("0" + Num).slice(-2)));
                DataProcess.setItemValue("Process"+("0" + Num).slice(-2), TempProcess);
            } else if(name.indexOf("BDEL") == 0) {
                let Num = parseInt(name.substring(5,7)); //Có vấn đề, Cần check lại
                for(var i = Num + 1; i < 16; i++) {
                    DataProcess.setItemValue("Process"+("0" + (i - 1)).slice(-2), DataProcess.getItemValue("Process"+("0" + i).slice(-2)));
                }
            }
        });
    }

    var DataProcess;

    function DownPro(ProcessCode) {
        for(var i = 14; i >= ProcessCode; i--) DataProcess.setItemValue("Process"+("0" + (i + 1)).slice(-2), DataProcess.getItemValue("Process"+("0" + (i)).slice(-2), true));
        DataProcess.setItemValue("Process"+("0" + ProcessCode).slice(-2),"");
    }

    function ReadyNow() {
        var date = new Date();
        DataPEForm.setItemValue("Ready_Date", date.yyyymmdd() + ":00");
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