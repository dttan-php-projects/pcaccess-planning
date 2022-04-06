<?php 
    require("./Module/Template.php");
    InitPage("PCInventoryList","Inventory List");
?>

<script>
    var LayoutMain;
    var DataPEForm;
    function DocumentStart()
    {
        LayoutMain = new dhtmlXLayoutObject({
            parent: document.body,
            pattern: "2U",
            offsets: {
                top: 65
            },
            cells: [
                {id: "a", header: true, text:"Control Panel", width: 300},
                {id: "b", header: true, text:"Accessory Material Total List"}
            ]
        });
        
        InitForm();
        InitGrid();
    }
    var ControlForm;
    function InitGrid(){
        GridMain = LayoutMain.cells("b").attachGrid();
        GridMain.setImagePath("./Module/dhtmlx/skins/skyblue/imgs/");
        GridMain.setHeader("NO,Stock Code, Unit Price, Sort, Remark, Active ");
        GridMain.attachHeader(",#text_filter,,,#text_filter,");
        GridMain.setInitWidths("50,200,100,200,*,50")
        GridMain.setColAlign("center,center,center,center,center,center");
        GridMain.setColTypes("ro,ed,ed,ed,ed,ch");
        GridMain.setColSorting("str,str,str,str,str,str")
        GridMain.setRowTextStyle("1", "background-color: red; font-family: arial;");
        GridMain.enableSmartRendering(true);
        GridMain.entBox.id = "GridMain";
        GridMain.init();
        GridMain.loadXML("Data.php?EVENT=LOADINVENTORY");
        var dp = new dataProcessor("Data.php?EVENT=LOADINVENTORY"); 
        dp.init(GridMain);

        GridMain.attachEvent("onRowSelect",function(rowId,columnIndex){
            ControlForm.setItemValue("StockCode",GridMain.cells(rowId,1).getValue());
            ControlForm.setItemValue("UnitPrice",GridMain.cells(rowId,2).getValue());
            ControlForm.setItemValue("Sort",GridMain.cells(rowId,3).getValue());
            ControlForm.setItemValue("Remark",GridMain.cells(rowId,4).getValue());
            if(GridMain.cells(rowId,5).getValue() == 1)
            {
                ControlForm.checkItem("Active");
            } else
            {
                ControlForm.uncheckItem("Active");						
            }
        })
    }

    

    function InitForm()
    {
        var DataForm = [
					{type: "settings", position: "label-top", labelWidth: 90, inputWidth: 130},
					{type: "block", width: "auto", blockOffset: 20, list: [
						{type: "settings", labelWidth: "200", inputWidth: "250"},
						{type: "input", label: "Stock Code:", value: "", name: "StockCode"},
						{type: "input", label: "Unit Price:", value: "", name: "UnitPrice"},
						{type: "input", label: "Sort:", value: "", name: "Sort"},
						{type: "input", label: "Remark:", value: "", rows: "5", name: "Remark"},
						{type: "checkbox", label: "Active", checked: "0", position: "label-left", name: "Active"},
					]}, 
					{type: "block", width: "auto", blockOffset: 20, list: [
						{type: "settings", labelWidth: "200", inputWidth: "250"},
						{type: "button", name: "Delete", value: "Delete"},
						{type: "newcolumn"},
						{type: "button", offsetLeft: 10, name: "Save", value: "Save"}
					]}
				];

			ControlForm = LayoutMain.cells("a").attachForm();
			ControlForm.loadStruct(DataForm, "json");


			ControlForm.attachEvent("onButtonClick", function(name){
				if(name == "Delete")
				{
                    AjaxAsync("Event.php",{'EVENT': "DELETEINVENTORY",
								'StockCode': ControlForm.getItemValue("StockCode")},"POST","HTML");
                                
                    GridMain.clearAll();
                    GridMain.loadXML("Data.php?EVENT=LOADINVENTORY",function(){
                    });
                } else if(name == "Save")
				{
                    var DataTable = {"EVENT":"UPDATEINVENTORY"};
                    DataTable.MAIN = JSON.stringify(ControlForm.getFormData());
                    var Result = AjaxAsync("Event.php",DataTable,"POST");
                    alert("UPDATE OK");
                    GridMain.clearAll();
                    GridMain.loadXML("Data.php?EVENT=LOADINVENTORY",function(){
                        GridMain.selectRowById(ControlForm.getItemValue("StockCode"));
                    });
                }
			});
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