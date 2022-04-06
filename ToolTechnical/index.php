<!DOCTYPE html>
<html>

<head>
    <title>Technical - Offset & Digital</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <script src="../Module/dhtmlx/codebase/dhtmlx.js" type="text/javascript"></script>
    <link rel="stylesheet" type="text/css" href="../Module/dhtmlx/skins/skyblue/dhtmlx.css">
    <link rel="stylesheet" type="text/css" href="../Module/font-awesome/css/font-awesome.min.css">
    <script src="../Module/JS/jquery-1.10.1.min.js"></script>
    <link rel="icon" href="../images/Logo.ico" type="image/x-icon">
</head>
<style>
    html,
    body {
        width: 100%;
        height: 100%;
        padding: 0;
        margin: 0;
        font-family: "Source Sans Pro", "Helvetica Neue", Helvetica;
        background-repeat: no-repeat;
        background-size: 100%;
    }
</style>
<body>
    <div id="MasterItemToolbar" style="width:100%;"> </div>
</body>

<script>

    var MasterItemToolbar;
    var MasterItemGrid;
    var dhxWins;

    function AjaxAsync(urlsend, dtsend, typeSend = "GET", datatype = "html") {
        var it_works;

        $.ajax({
            url: urlsend,
            type: typeSend.toUpperCase(),
            dataType: datatype.toUpperCase(),
            cache: false,
            data: dtsend,
            success: function(string) {
                it_works = string;
            },
            error: function() {
                it_works = 'ERROR';
            },
            async: false
        });
        return it_works;
    }

    function AjaxNonAsync(urlsend, dtsend, typeSend = "GET", datatype = "html") {
        $.ajax({
            url: urlsend,
            type: typeSend.toUpperCase(),
            dataType: datatype.toUpperCase(),
            cache: false,
            data: dtsend,
            success: function(string) {
                console.log(string);
            },
            error: function() {
                console.log("Error");
            },
            async: true
        });
    }


    function getCookie(cname) {
        var name = cname + "=";
        var decodedCookie = decodeURIComponent(document.cookie);
        var ca = decodedCookie.split(';');
        for (var i = 0; i < ca.length; i++) {
            var c = ca[i];
            while (c.charAt(0) == ' ') {
                c = c.substring(1);
            }
            if (c.indexOf(name) == 0) {
                return c.substring(name.length, c.length);
            }
        }
        return "";
    }

    Date.prototype.yyyymmdd = function() {
        var mm = this.getMonth() + 1; // getMonth() is zero-based
        var dd = this.getDate();
        var hh = this.getHours();
        var MM = this.getMinutes();

        return [this.getFullYear(), (mm > 9 ? '' : '0') + mm, (dd > 9 ? '' : '0') + dd].join('-') + " " + [(hh > 9 ? '' : '0') + hh, (MM > 9 ? '' : '0') + MM].join(':');
    };

    var LayoutMain;
    function initLayout(){
        LayoutMain = new dhtmlXLayoutObject({
            parent: document.body,
            pattern: "1C",
            offsets: {
                top: 30
            },
            cells: [
                {id: "a", header: true, text: "MASTER ITEM"}, 				
            ]
        });
    }

    
    function MasterItemGrid() 
    {
        LayoutMain.cells("a").progressOn();
        MasterItemGrid = LayoutMain.cells("a").attachGrid();

        // inti Grid
        MasterItemGrid.setImagePath("../Module/dhtmlx/skins/skyblue/imgs/");	
        MasterItemGrid.setRowTextStyle("1", "background-color: red; font-family: arial;");
        MasterItemGrid.attachHeader(",,#text_filter,#text_filter,#text_filter,,,#text_filter");	

        MasterItemGrid.init();

        // MasterItemGrid.enableAutoWidth(true);
        MasterItemGrid.enableSmartRendering(true); // false to disable
        
        loadMasterItem('limit');
        
    }

    function loadMasterItem(limit )
    {
        MasterItemGrid.clearAll();

        LayoutMain.cells("a").progressOn();

        MasterItemGrid.loadXML("./DataTechnical.php?EVENT=LOADDATAGRID&limit="+limit,function(){
            LayoutMain.cells("a").progressOff();
        });
        
    }


    function ItemProcessGrid(name )
    {
        // close if exist
            if(dhxWins){ dhxWins.window("Windows").close(); }

        // create
            dhxWins= new dhtmlXWindows(); 

        if (!dhxWins.isWindow("Windows")){

            
            // init win
                var id = "Windows";
                var w = 1280;
                var h = 650;
                var x = Number(($(window).width()-w)/2);
                var y = Number(($(window).height()-h)/2);
                var Popup = dhxWins.createWindow(id, x, y, w, h);
            
            // Grid
                if (name == 'load_item_process' ) {
                    // init grid
                        grid = dhxWins.window(id).attachGrid();

                    // close
                        Popup.attachEvent("onClose", function(win){ if (win.getId() == "Windows") win.hide(); });

                    // title
                        var currentTime = new Date();
                        var dd = String(currentTime.getDate()).padStart(2, '0');
                        var mm = String(currentTime.getMonth() + 1).padStart(2, '0'); //January is 0!
                        var yyyy = currentTime.getFullYear();
                        today = yyyy + '-' + mm + '-' + dd;
                        dhxWins.window(id).setText("Item Process - "+today);

                    // init grid
                        grid.setImagePath("../Module/dhtmlx/skins/skyblue/imgs/");	
                        grid.attachHeader(",#text_filter,#text_filter,#text_filter,#text_filter,#text_filter");
                        grid.setRowTextStyle("1", "background-color: red; font-family: arial;");
                        grid.init();
                        grid.enableSmartRendering(true); // false to disable

                    // load data
                        grid.clearAll();    
                        grid.loadXML("./DataTechnical.php?EVENT=LOADPROCESSGRID",function(){
                            grid.enableSmartRendering(false);
                            // load last row
                            var state=grid.getStateOfView();
                            if(state[2]>0) grid.showRow(grid.getRowId(state[2]-1));
                        });

                        // update
                        grid.attachEvent("onCheckbox", function(rId,cInd,state){

                            var Code = grid.cells(rId,2).getValue();
                            var Process = grid.cells(rId,3).getValue();
                            var Process_Ability = grid.cells(rId,4).getValue();
                            var Ability_Unit = grid.cells(rId,5).getValue();
                            var QTY500 = grid.cells(rId,6).getValue();
                            var QTY501_2000 = grid.cells(rId,7).getValue();
                            var QTY2001_5000 = grid.cells(rId,8).getValue();
                            var QTY5001 = grid.cells(rId,9).getValue();
                            var Remark = grid.cells(rId,10).getValue();
                            var VN_vi = grid.cells(rId,11).getValue();
                            
                            //json data encode
                            var jsonObjects = { 
                                "Code": Code, 
                                "Process": Process,
                                "Process_Ability": Process_Ability,
                                "Ability_Unit": Ability_Unit,
                                "QTY500": QTY500,
                                "QTY501_2000": QTY501_2000,
                                "QTY2001_5000": QTY2001_5000,
                                "QTY5001": QTY5001,
                                "Remark": Remark,
                                "VN_vi": VN_vi
                            };

                            if (cInd == 12 ) { // save
                                var url = "./DataTechnical.php?EVENT=SAVEAUTO";
                                ajaxFunc(jsonObjects, url);
                                    
                            } else if (cInd == 13 ) { // del
                                var conf = confirm("Bạn muốn Xóa " + Code + "-" + Process + "(" + VN_vi + ")" + "?" );
                                if (conf ) {
                                    var url = "./DataTechnical.php?EVENT=DELETEAUTO";
                                    ajaxFunc(jsonObjects, url );
                                }
                            }




                        });
                }
            
        } else {
            dhxWins.window("Windows").show(); 
        }
        
    }

    function ajaxFunc(jsonObjects, url )
    {
        console.log('jsonObjects: ' + JSON.stringify(jsonObjects));
        console.log('url: ' + url);

        // check 
        if (jsonObjects && url ) {

            //excute with ajax
            $.ajax({
                type: "POST",
                data: { data: JSON.stringify(jsonObjects) },
                url: url,
                dataType: 'json',
                beforeSend: function(x) { if (x && x.overrideMimeType) { x.overrideMimeType("application/j-son;charset=UTF-8");} },
                success: function(data) {

                    alert(data.message );
                    location.reload();
                        
                },
                error: function(xhr, status, error) {
                    alert('Error. Vui lòng liên hệ quản trị hệ thống!');
                    location.reload();
                    return false;
                }
            });

        }

    }

    
    function MasterItemToolbar() 
    {
        MasterItemToolbar = new dhtmlXToolbarObject({
            parent: "MasterItemToolbar",
            // icons_path: "../Module/dhtmlx/common/imgs/",
            align: "left",
            icons_size: 18,
            iconset: "awesome"
        });

        MasterItemToolbar.addText("", 1, "<a style='font-size:20pt;font-weight:bold'>P&C MASTER FILE</a>");
        MasterItemToolbar.addButton("spacer",2, "", "");
		MasterItemToolbar.addSpacer("spacer");
        
        // MasterItemToolbar.addButton("Import_Insert_List",8, "<a style='font-size:9pt;font-weight:bold;color:blue;'>1. Import To Insert/Update (New)</a>", "");
        // MasterItemToolbar.addText("", 9, " | ");
        
        // MasterItemToolbar.addButton("Import_Update_List",10, "<a style='font-size:9pt;font-weight:bold;color:blue;'>2. Import To Update GLID (Old)</a>", "");
        // MasterItemToolbar.addText("", 9, " | ");

        // MasterItemToolbar.addButton("Import_Delete_List",10, "<a style='font-size:9pt;font-weight:bold;color:blue;'>3. Import To Delete GLID</a>", "");
        // MasterItemToolbar.addText("", 11, " | ");

        // Imports
        var imp_opts = [
            ['Import_Insert_List', 'obj', '1. Import To Insert/Update (New)', 'fa fa-upload'],
            ['sep01', 'sep', '', ''],
            ['Import_Update_List', 'obj', '2. Import To Update GLID (Old)', 'fa fa-upload'],
            ['sep02', 'sep', '', ''],
            ['Import_Delete_List', 'obj', '3. Import To Delete GLID', 'fa fa-upload'],
            ['sep03', 'sep', '', '']
        ];

        MasterItemToolbar.addButtonSelect("Imports", 5, "<a style='font-size:9pt;font-weight:bold;color:blue;'>Imports</span>", imp_opts, "fa fa-upload");
        MasterItemToolbar.addText("", 6, " | ");

        // Process
        MasterItemToolbar.addButton("load_item_process",7, "<a style='font-size:9pt;font-weight:bold;color:blue;'>Item Process Data</a>", "fa fa-database");
        MasterItemToolbar.addText("", 8, " | ");

        // Exports
        var exp_opts = [
            ['Export_1000', 'obj', '1. Export 1000 GLID mới nhất', 'fa fa-download'],
            ['sep01', 'sep', '', ''],
            ['Export_All', 'obj', '2. Export All', 'fa fa-download'],
            ['sep02', 'sep', '', '']
        ];
        MasterItemToolbar.addButtonSelect("Exports", 9, "<a style='font-size:9pt;font-weight:bold;color:blue;'>Exports</span>", exp_opts, "fa fa-download");
        MasterItemToolbar.addText("", 10, " | ");

        // Sample file
        var mf_opts = [
            ['Sample_Import_Master_Data', 'obj', 'Download Sample File (Insert/Update)', 'fa fa-cloud-download'],
            ['sep04', 'sep', '', ''],
            ['Sample_Delete_Master_Data', 'obj', 'Download Sample File (Delete)', 'fa fa-cloud-download'],
            ['sep05', 'sep', '', '']
        ];
        MasterItemToolbar.addButtonSelect("Sample_File", 12, "Sample File", mf_opts, "fa fa-cloud-download");
        MasterItemToolbar.addText("", 13, " | ");

        // Options
        var view_opts = [
            ['View_All', 'obj', 'Hiển thị tất cả dữ liệu', 'fa fa-cloud-download'],
            ['sep06', 'sep', '', ''],
            
        ];
        MasterItemToolbar.addButtonSelect("Views", 14, "Options", view_opts, "fa fa-list");
        MasterItemToolbar.addText("", 15, " | ");
        
        MasterItemToolbar.addText("", 17, " ||| ");
        // MasterItemToolbar.addButton("copy_item",4, "Copy And Paste Item", "save.gif");

        MasterItemGrid();

        MasterItemToolbar.attachEvent("onClick", function(name)
		{
			//console.log(name);
			if(name == "Import_Insert_List" ){ // insert or update
				ImportNewFile();
			} else if(name == "Import_Update_List"){
                UploadFile();
			} else if(name == "Import_Delete_List"){
				importDelFile();
			}  else if(name == "Sample_Import_Master_Data"){
				var url = 'https://docs.google.com/spreadsheets/d/1n2Pxoe8-fPhmaWNeZyvz7BdD39Z7Scvr1LWJu5zX2SI/edit#gid=0';
                window.open(url,'_blank');
			}  else if(name == "Sample_Delete_Master_Data"){
				var url = 'https://docs.google.com/spreadsheets/d/1XhErdFaMx58qbM4Vtq96o0efRRzdmUJ3uE8KZU3fAwI/edit#gid=0';
                window.open(url,'_blank');
			} else if (name == 'load_item_process' ) {
                ItemProcessGrid(name);
            }  else if (name == 'Export_1000' ) {
                window.open("./Exports.php?limit=limit",'_blank');
            } else if (name == 'Export_All' ) {
                window.open("./Exports.php?limit=all",'_blank');
            } else if (name == 'View_All' ) {
                loadMasterItem('all')
            }

            
            

        });

        // default
        
    }


    // import: Chức năng cũ: Dùng để cập nhật thông tin GLID (không có Insert)
    function UploadFile() 
    {
        var conf = confirm("Chức năng (cũ) CẬP NHẬT danh sách GLID. Chọn Ok để tiếp tục");
        if (!conf ) location.reload();

        var dhxWins;
        if(!dhxWins){ dhxWins= new dhtmlXWindows(); }

        var id = "WindowsDetail";
        var w = 400;
        var h = 100;
        var x = Number(($(window).width()-400)/2);
        var y = Number(($(window).height()-50)/2);
        var Popup = dhxWins.createWindow(id, x, y, w, h);
        dhxWins.window(id).setText("Update (Old) GLID Data");
        Popup.attachHTMLString(
            '<div style="width:500%;margin:20px">' +
                '<form action="./Upload.php" enctype="multipart/form-data" method="post" accept-charset="utf-8">' +
                    '<input type="file" name="FileToUpload" id="file" class="form-control filestyle" value="value" data-icon="false"  />' +
                    '<input type="submit" name="submit" value="Upload" id="importfile-id" class="btn btn-block btn-primary"  />' +
                '</form>' +
            '</div>'
        );
    }

    // import: Chức năng mới: Dùng để Thêm mới hoặc cập nhật thông tin GLID
    function ImportNewFile() 
    {
        var conf = confirm("Chức năng THÊM/CẬP NHẬT GLID. Danh sách Import tối ưu không vượt quá 1,000 dòng");
        if (!conf ) location.reload();

        var dhxWins;
        if(!dhxWins){ dhxWins= new dhtmlXWindows(); }

        var id = "WindowsDetail";
        var w = 400;
        var h = 100;
        var x = Number(($(window).width()-400)/2);
        var y = Number(($(window).height()-50)/2);
        var Popup = dhxWins.createWindow(id, x, y, w, h);
        dhxWins.window(id).setText("Insert/Update GLID Data");
        Popup.attachHTMLString(
            '<div style="width:500%;margin:20px">' +
                '<form action="./ImportNewFile.php" enctype="multipart/form-data" method="post" accept-charset="utf-8">' +
                    '<input type="file" name="file" id="file" class="form-control filestyle" value="value" data-icon="false"  />' +
                    '<input type="submit" name="submit" value="Import" id="importfile-id" class="btn btn-block btn-primary"  />' +
                '</form>' +
            '</div>'
        );
    }

    // import: Chức năng Xóa nhiều GLID
    function importDelFile() 
    {
        // check thêm trường hợp chọn chức năng này
        var conf = confirm("Đây là chức năng XÓA danh sách GLID. Chọn Ok để tiếp tục");
        if (!conf ) location.reload();

        var dhxWins;
        if(!dhxWins){ dhxWins= new dhtmlXWindows(); }

        var id = "WindowsDetail";
        var w = 400;
        var h = 100;
        var x = Number(($(window).width()-400)/2);
        var y = Number(($(window).height()-50)/2);
        var Popup = dhxWins.createWindow(id, x, y, w, h);
        dhxWins.window(id).setText("Delete GLID Data");
        Popup.attachHTMLString(
            '<div style="width:500%;margin:20px">' +
                '<form action="./DeleteList.php" enctype="multipart/form-data" method="post" accept-charset="utf-8">' +
                    '<input type="file" name="file" id="file" class="form-control filestyle" value="value" data-icon="false"  />' +
                    '<input type="submit" name="submit" value="Import" id="importfile-id" class="btn btn-block btn-primary"  />' +
                '</form>' +
            '</div>'
        );
    }

    // Start
    $(document).ready(function(){

        initLayout();
        
        MasterItemToolbar();

    });

    
    

    
</script>