<!DOCTYPE html>
<html>

<head>
    <title>Trigger Date Timelines</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <script src="./Module/dhtmlx/codebase/dhtmlx.js" type="text/javascript"></script>
    <link rel="STYLESHEET" type="text/css" href="./Module/dhtmlx/skins/skyblue/dhtmlx.css">
    <script src="./Module/JS/jquery-1.10.1.min.js"></script>
    <link rel="icon" href="./Images/Logo.ico" type="image/x-icon">
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
    <!-- <div id="p" style="width:100%;"> </div> -->
</body>

<script>
    var p;
    var countDown;


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
            pattern: "2U",
            offsets: {
                top: 30
            },
            cells: [
                {id: "a", header: true, text: "DANH SÁCH ĐÃ LẤY ĐƯỢC DỮ LIỆU RECEIVING"},
                {id: "b", header: true, text: "CHƯA LẤY ĐƯỢC DỮ LIỆU"},
            ]
        });


        p = document.getElementById('p');

        // thời gian bắt đầu để đếm ngược
        var begin = new Date().getTime();

        // chạy hàm run sau 10 phút
        // countDown = setInterval(run(begin),1000*60);
        // console.log('countDown: ' + countDown);
    }

    var inAutomailGrid;
    function inAutomailGrid() 
    {
        LayoutMain.cells("a").progressOn();
        inAutomailGrid = LayoutMain.cells("a").attachGrid();
        inAutomailGrid.setImagePath("../Module/dhtmlx/skins/skyblue/imgs/");	
        inAutomailGrid.setRowTextStyle("1", "background-color: red; font-family: arial;");
        inAutomailGrid.init();

        inAutomailGrid.enableSmartRendering(true); // false to disable
        
        inAutomailGrid.loadXML("./Data.php?EVENT=InAutomail",function(){
            LayoutMain.cells("a").progressOff();
        });

        // inAutomailGrid.load("./DataTechnical.php?EVENT=LOADDATAGRID",function(){
		// 	LayoutMain.cells("a").progressOff();
		// });
    }


    var outAutomailGrid;
    function outAutomailGrid() 
    {
        LayoutMain.cells("b").progressOn();
        outAutomailGrid = LayoutMain.cells("b").attachGrid();
        outAutomailGrid.setImagePath("../Module/dhtmlx/skins/skyblue/imgs/");	
        outAutomailGrid.setRowTextStyle("1", "background-color: red; font-family: arial;");
        outAutomailGrid.init();

        outAutomailGrid.enableSmartRendering(true); // false to disable
        
        outAutomailGrid.loadXML("./Data.php?EVENT=OutAutomail",function(){
            LayoutMain.cells("b").progressOff();
        });

        // outAutomailGrid.load("./DataTechnical.php?EVENT=LOADDATAGRID",function(){
		// 	LayoutMain.cells("a").progressOff();
		// });
    }

    var MasterItemToolbar;
    function MasterItemToolbar() 
    {
        MasterItemToolbar = new dhtmlXToolbarObject({
            parent: "MasterItemToolbar",
            icons_path: "../Module/dhtmlx/common/imgs/",
            align: "left"
        });

        MasterItemToolbar.addText("", 1, "<a style='font-size:20pt;font-weight:bold'>P&C RECEIVING</a>");
        MasterItemToolbar.addButton("spacer",2, "", "");
		MasterItemToolbar.addSpacer("spacer");

        MasterItemToolbar.addButton("Update",5, "<a style='font-size:12pt;font-weight:bold;color:red;'>Cập nhật</a>", "");
        MasterItemToolbar.addText("", 6, " | ");
        
        MasterItemToolbar.addButton("Import_SOLine_List",8, "<a style='font-size:10pt;font-weight:bold;color:blue;'>Import SOLine List</a>", "");
        MasterItemToolbar.addText("", 9, " | ");

        MasterItemToolbar.addButton("Import_Delete_List",10, "<a style='font-size:10pt;font-weight:bold;color:blue;'>Import Delete List</a>", "");
        MasterItemToolbar.addText("", 11, " | ");

        var mf_opts = [
            ['Sample_Imports', 'obj', 'Download File mẫu Import', 'xlsx.gif'],
            ['sep01', 'sep', '', ''],
            ['Sample_Delete_List', 'obj', 'Download File mẫu Delete', 'xlsx.gif'],
            ['sep02', 'sep', '', '']
        ];

        MasterItemToolbar.addButtonSelect("Sample_File", 12, "Download Sample File", mf_opts, "database.gif");
        MasterItemToolbar.addText("", 13, " | ");
        
        MasterItemToolbar.addText("", 17, " ||| ");
        // MasterItemToolbar.addButton("copy_item",4, "Copy And Paste Item", "save.gif");

        MasterItemToolbar.attachEvent("onClick", function(name)
		{
			//console.log(name);
			if(name == "Import_SOLine_List" ){ // insert or update
				UploadFile();
			} else if(name == "Import_Delete_List"){
				importDelFile();
			}  else if(name == "Sample_Imports"){
				var url = 'https://docs.google.com/spreadsheets/d/17ldIdWBDKZxi9mDjHZ8vteWM_G5odhQJA2PbmrKoHic/edit?usp=sharing';
                window.open(url,'_blank');
			}  else if(name == "Sample_Delete_List"){
				var url = 'https://docs.google.com/spreadsheets/d/1n39jTWmZSFwPhLDr8V0dRv3XBwhSI4UH-vD-AipLY50/edit?usp=sharing';
                window.open(url,'_blank');
			} else if (name == "Update" ) {
                location.href = "./Update.php";
            }

            
        });
    }


    // import: Chức năng cũ: Dùng để cập nhật thông tin GLID (không có Insert)
    function UploadFile() 
    {
        // var conf = confirm("Chức năng (cũ) CẬP NHẬT danh sách GLID. Chọn Ok để tiếp tục");
        // if (!conf ) location.reload();

        var dhxWins;
        if(!dhxWins){ dhxWins= new dhtmlXWindows(); }

        var id = "WindowsDetail";
        var w = 400;
        var h = 100;
        var x = Number(($(window).width()-400)/2);
        var y = Number(($(window).height()-50)/2);
        var Popup = dhxWins.createWindow(id, x, y, w, h);
        dhxWins.window(id).setText("Import Receiving Data");
        Popup.attachHTMLString(
            '<div style="width:500%;margin:20px">' +
                '<form action="./Imports.php" enctype="multipart/form-data" method="post" accept-charset="utf-8">' +
                    '<input type="file" name="file" id="file" class="form-control filestyle" value="value" data-icon="false"  />' +
                    '<input type="submit" name="submit" value="Upload" id="importfile-id" class="btn btn-block btn-primary"  />' +
                '</form>' +
            '</div>'
        );
    }

    // import: Chức năng Xóa nhiều GLID
    function importDelFile() 
    {
        // check thêm trường hợp chọn chức năng này
        var conf = confirm("Đây là chức năng XÓA danh sách Receiving. Chọn Ok để tiếp tục");
        if (!conf ) location.reload();

        var dhxWins;
        if(!dhxWins){ dhxWins= new dhtmlXWindows(); }

        var id = "WindowsDetail";
        var w = 400;
        var h = 100;
        var x = Number(($(window).width()-400)/2);
        var y = Number(($(window).height()-50)/2);
        var Popup = dhxWins.createWindow(id, x, y, w, h);
        dhxWins.window(id).setText("Delete Receiving List");
        Popup.attachHTMLString(
            '<div style="width:500%;margin:20px">' +
                '<form action="./DeleteList.php" enctype="multipart/form-data" method="post" accept-charset="utf-8">' +
                    '<input type="file" name="file" id="file" class="form-control filestyle" value="value" data-icon="false"  />' +
                    '<input type="submit" name="submit" value="Import" id="importfile-id" class="btn btn-block btn-primary"  />' +
                '</form>' +
            '</div>'
        );
    }

    
    function run(begin){

        var now = new Date().getTime();
        console.log('Đã cập nhật lúc '+ begin);

        //Số s đến thời gian hiện tại
            var timeRest = begin - now;
        //Số s còn lại để đến tết;
            var day = Math.floor(timeRest/(1000*60*60*24));
        //Số ngày còn lại
            var hours = Math.floor(timeRest%(1000*60*60*24)/(1000*60*60));
        // Số giờ còn lại
            var minute = Math.floor(timeRest%(1000*60*60)/(1000*60));
        // Số phút còn lại
            var sec = Math.floor(timeRest%(1000*60)/(1000));
        // // Số giây còn lại
            // p.innerHTML = day+' DAY '+hours+' : ' + minute + ' : ' + sec +"  ";

        console.log('timeRest: ' + timeRest);

        if(timeRest <= 0){

            // clearInterval(counDown); // reset lại counDown
            // p.innerHTML = "HPNY";
            
            // cập nhật lại dữ liệu
                // updateNow();
                
            // reset lại giá trị bắt đầu
                clearInterval(now);
                clearInterval(begin);
                begin = new Date().getTime();
                
                console.log('Đã cập nhật lúc '+ begin);

            // đệ quy
                run(begin);
        }
        
        
    }

    initLayout();
    inAutomailGrid();
    outAutomailGrid();
    MasterItemToolbar();

    
</script>