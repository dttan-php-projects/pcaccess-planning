<?php 
    require("./Module/Template.php");
    InitPage("PCItemOption","Item Optional");
?>

<script>
    var LayoutMain;    
    var PageView;
    function DocumentStart()
    {
        PageView = getUrl("P");

        LayoutMain = new dhtmlXLayoutObject({
            parent: document.body,
            pattern: "1C",
            offsets: {
                top: 65
            },
            cells: [
                {id: "a", header: true, text: "Digital Max Scrap"}
            ]
        });

        
        ToolbarMain.addButton("Export", null, "Export To Excel", "save.gif");

        ToolbarMain.addText("TitleText", null, '<textarea  style="height:0px;width:0" id="ClipBoard"/>');
        ToolbarMain.attachEvent("onClick", function(id) {
            if(id == "Export") {

            }
        });
        InitGrid();

        $(window).keydown(function(event) {
            if(event.ctrlKey && event.keyCode == 68) { 
                event.preventDefault(); 
            }
        });

        document.addEventListener('paste',function (event) {
            CopyClipBoard = event.clipboardData.getData('Text');
        });

    }

   
    var GridMain;
    var dp;
    function AddRowGrid(){
        var D = (new Date()).valueOf();
        GridMain.addRow(UserString + D,[UserString + D]);
        GridMain.selectCell(GridMain.getRowIndex(UserString + D));
    }
    var UserString = UserVNRIS.replace(".","_");
    var MaxRowID, MaxRowLot, MaxColLot, EditMode, CopyClipBoard;

    async function AddClipBoard(){
        var clipText = CopyClipBoard;
        var RowData = clipText.split("\r\n");
        var newId = (new Date()).valueOf();
        
        if(GridMain.getColumnsNum() - MaxColLot < RowData[0].split("\t").length) {
            dhtmlx.alert("Cột nhiều hơn, vui lòng dán đúng vị trí");
            return;
        } else {
            var TurnAdd = false;
            var SplitString = "";
            var LastRow = "";
            for(var i = 0; i < RowData.length - 1; i++){
                SplitString = RowData[i].split("\t");
                if(MaxRowLot + i == GridMain.getRowsNum() && i != 0) {
                    var ArrAdd = [GridMain.getRowsNum() + 1];
                    for(var j = 1; j < MaxColLot; j++) {
                        ArrAdd.push("");
                    }
                    for(var j = 0; j < SplitString.length; j++) {
                        if(SplitString[j][0] == '"' && SplitString[j][SplitString[j].length - 1] == '"') {
                            SplitString[j] = SplitString[j].substring(1,SplitString[j].length - 2);
                        }
                        ArrAdd.push(SplitString[j]);
                    }

                    LastRow = GridMain.getRowsNum();
                    var D = (new Date()).valueOf();
                    GridMain.addRow(UserString + D + i,ArrAdd);
                    TurnAdd = true;
                    await sleep(100);
                } else {
                    for(var j = 0; j < SplitString.length; j++) {
                        if(SplitString[j][0] == '"' && SplitString[j][SplitString[j].length - 1] == '"') {
                            SplitString[j] = SplitString[j].substring(1,SplitString[j].length - 2);
                        }

                        GridMain.cells2(MaxRowLot + i,MaxColLot + j).setValue(SplitString[j]);
                    }
                    dp.setUpdated(GridMain.getRowId(MaxRowLot + i), true);
                }
                
            }

            if(TurnAdd) setTimeout(AddRowGrid(LastRow),100);
        }
    }

    function sleep(ms) {
        return new Promise(resolve => setTimeout(resolve, ms));
    }

   
    function InitGrid(){
        GridMain = LayoutMain.cells("a").attachGrid();
        GridMain.setImagePath("./Module/dhtmlx/skins/skyblue/imgs/");
        GridMain.setHeader("STT,Production Type,Machine,Production Line,Color Management,Social Compliance,System Name,Digital Avilability,Status,FSC Type, BO");
        GridMain.setInitWidths("50,150,150,150,150,150,150,*,150,150,150");
        GridMain.setColumnMinWidth("50,150,150,150,150,150,150,150,150,150,150");
        GridMain.setColAlign("center,center,center,center,center,center,center,center,center,center,center");
        GridMain.setColTypes("ro,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed");
        GridMain.setColSorting("str,str,str,str,str,str,str,str,str,str,str")
        GridMain.setRowTextStyle("1", "background-color: red; font-family: arial;");
        GridMain.entBox.id = "GridMain";
        GridMain.enableBlockSelection(true);
        GridMain.init();
        GridMain.load("Data.php?EVENT=LOADITEMOPTIONAL",function(){
            var LastRow = GridMain.getRowsNum();
            if(LastRow == 0 || GridMain.cells2(LastRow - 1,3).getValue() != "") {
                AddRowGrid(LastRow);
            }
        });
        dp = new dataProcessor("Data.php?EVENT=LOADITEMOPTIONAL");
        dp.init(GridMain);

        GridMain.attachEvent("onEditCell", function(stage,rId,cInd,nValue,oValue){
            if(stage == 2) EditMode = false; 
            else if(stage == 1) {
                EditMode = true;
                window.setTimeout(function(){
                    $(".dhx_combo_edit").select();
                },1);
            }
            return true;
        });	 

        GridMain.attachEvent("onRowSelect", function(id,ind){
            MaxRowID = id;
            MaxRowLot = GridMain.getRowIndex(id);
            MaxColLot = ind;
        });

        GridMain.attachEvent("onKeyPress", function(code,cFlag,sFlag){
            var LastRow = GridMain.getRowsNum();
            if(!sFlag && code == 46) { //Delete
                window.setTimeout(function(){
                    var top_row = GridMain.getSelectedBlock().LeftTopRow;
                    var bottom_row = GridMain.getSelectedBlock().RightBottomRow;
                    var left_column = GridMain.getSelectedBlock().LeftTopCol;
                    var right_column = GridMain.getSelectedBlock().RightBottomCol;
                    if(typeof top_row == "string") {
                        GridMain.cells(top_row,left_column).setValue("");
                        dp.setUpdated(top_row,true);
                    } else { 
                        for(var i = top_row; i <= bottom_row; i++){
                            for(var j = left_column; j <= right_column; j++) {
                                GridMain.cells2(i,j).setValue("");
                            }
                            dp.setUpdated(GridMain.getRowId(i),true);
                        }
                    }
                },1);
                return true;
            } else if(sFlag && code == 46) { //Delete
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
                return true;
            } else if(code == 40) { //Down
                window.setTimeout(function(){
                    MaxRowLot++;
                    if(MaxRowLot == GridMain.getRowsNum()) {
                        AddRowGrid(LastRow);
                    }
                    GridMain.selectCell(MaxRowLot,MaxColLot,false,false,true,true);
                    GridMain.editCell();
                },1);
                return true;
            } else if(code == 38) { //Up
                window.setTimeout(function(){
                    if(MaxRowLot - 1 < 0) return;
                    MaxRowLot--;
                    GridMain.selectCell(MaxRowLot,MaxColLot,false,false,true,true);
                    GridMain.editCell();
                },1);
                return true;
            } else if(code == 13) { //Enter
                window.setTimeout(function(){
                    MaxRowLot++;
                    if(MaxRowLot == GridMain.getRowsNum()) {
                        AddRowGrid(LastRow);
                    }
                    GridMain.selectCell(MaxRowLot,MaxColLot,false,false,true,true);
                    GridMain.editCell();
                },1);
                return true;
            } else if(code == 39 || (!sFlag && code == 9)) { //Right
                window.setTimeout(function(){
                    if(MaxColLot + 1 >= GridMain.getColumnsNum()) return;
                    MaxColLot++;
                    GridMain.selectCell(MaxRowLot,MaxColLot,false,false,true,true);
                    GridMain.editCell();
                },1);
                return true;
            } else if(code == 37 || (sFlag && code == 9)) { //Left
                window.setTimeout(function(){
                    if(MaxColLot - 1 < 1) return;
                    MaxColLot--;
                    GridMain.selectCell(MaxRowLot,MaxColLot,false,false,true,true);
                    GridMain.editCell();
                },1);
                return true;
            } else if(cFlag && code == 67) { //Copy
                window.setTimeout(function(){
                    var top_row = GridMain.getSelectedBlock().LeftTopRow;
                    var bottom_row = GridMain.getSelectedBlock().RightBottomRow;
                    var left_column = GridMain.getSelectedBlock().LeftTopCol;
                    var right_column = GridMain.getSelectedBlock().RightBottomCol;
                    var DataCB = "";
                    var DataCB1 = "";
                    if(typeof top_row == "string") {
                        DataCB = GridMain.cells(top_row,left_column).getValue() + "\n";
                    } else {
                        for(var i = top_row; i <= bottom_row; i++){
                            for(var j = left_column; j <= right_column; j++) {
                                DataCB1 += "\t" + GridMain.cells2(i,j).getValue().replace("\n","");
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
            } else if(cFlag && code == 86) { //Copy
                window.setTimeout(function(){
                    if(!EditMode) AddClipBoard();
                },10);
                return true;
            } else if(cFlag && code == 68) { //Clone
                window.setTimeout(function(){
                    var top_row = GridMain.getSelectedBlock().LeftTopRow;
                    var bottom_row = GridMain.getSelectedBlock().RightBottomRow;
                    var left_column = GridMain.getSelectedBlock().LeftTopCol;
                    var right_column = GridMain.getSelectedBlock().RightBottomCol;
                    for(var i = top_row + 1; i <= bottom_row; i++){
                        for(var j = left_column; j <= right_column; j++) {
                            GridMain.cells2(i,j).setValue(GridMain.cells2(top_row,j).getValue());
                        }
                        dp.setUpdated(GridMain.getRowId(i),true);
                    }
                },1);
            }

            if(!EditMode && (!cFlag) && ((code > 64 && code < 91) || (code > 47 && code < 58))) {
                var top_row = GridMain.getSelectedBlock().LeftTopRow;
                var bottom_row = GridMain.getSelectedBlock().RightBottomRow;
                var left_column = GridMain.getSelectedBlock().LeftTopCol;
                var right_column = GridMain.getSelectedBlock().RightBottomCol;
                if(typeof top_row == "string") {
                    if(GridMain.cells(top_row,MaxColLot).getValue() == "") GridMain.cells(top_row,MaxColLot).setValue(".");
                    GridMain.selectCell(GridMain.getRowIndex(top_row),MaxColLot,false,false,true,true);
                    GridMain.editCell();
                    window.setTimeout(function(){
                        $(".dhx_combo_edit").val(String.fromCharCode(code));
                    },15);
                } else { 
                    if(GridMain.cells2(top_row,MaxColLot).getValue() == "") GridMain.cells2(top_row,MaxColLot).setValue(".");
                    GridMain.selectCell(top_row,left_column,false,false,true,true);
                    GridMain.editCell();
                    window.setTimeout(function(){
                        $(".dhx_combo_edit").val(String.fromCharCode(code));
                    },15);
                }
                return false;
            }
        })
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
</body>
</html>