<?php

function getConnection138($db = null)
{
    if ($db == null) $db = "avery"; // mặc định
    $host = "147.121.59.138";
    $username = "planning";
    $password = "PELS&Auto@{2020}";
    $conn = mysqli_connect($host, $username, $password, $db) or die('Không thể kết nối tới Server ' . $host);
    $conn->query("SET NAMES 'utf8'");

    return $conn;
}
function toQueryArr($conn, $query)
{
    $result = mysqli_query($conn, $query);
    if (!$result) return array();
    $result = mysqli_fetch_array($result);
    // if ($conn) mysqli_close($conn);
    return $result;
}

?>
<!DOCTYPE html>
<html>

<head>
    <title>Other Tools</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <script src="../Module/dhtmlx/codebase/dhtmlx.js" type="text/javascript"></script>
    <link rel="STYLESHEET" type="text/css" href="../Module/dhtmlx/skins/skyblue/dhtmlx.css">
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
    <!-- <div id="p" style="width:100%;"> </div> -->
</body>

<script>
    var p;
    var countDown;

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

    function initLayout() {
        LayoutMain = new dhtmlXLayoutObject({
            parent: document.body,
            pattern: "1C",
            offsets: {
                top: 30
            },
            cells: [{
                    id: "a",
                    header: true,
                    text: "DANH SÁCH ĐÃ LẤY ĐƯỢC DỮ LIỆU RECEIVING"
                },
            ]
        });


    }

    var loadDataGrid;

    function loadDataGrid() {
        LayoutMain.cells("a").progressOn();
        loadDataGrid = LayoutMain.cells("a").attachGrid();
        loadDataGrid.setImagePath("../Module/dhtmlx/skins/skyblue/imgs/");
        loadDataGrid.attachHeader(",#text_filter,#text_filter,#text_filter");
        loadDataGrid.setRowTextStyle("1", "background-color: red; font-family: arial;");
        loadDataGrid.init();

        loadDataGrid.enableSmartRendering(true); // false to disable

        loadDataGrid.loadXML("./Data.php?EVENT=LoadData", function() {
            LayoutMain.cells("a").progressOff();
        });

    }


    var MasterItemToolbar;

    function MasterItemToolbar() {
        MasterItemToolbar = new dhtmlXToolbarObject({
            parent: "MasterItemToolbar",
            icons_path: "../Module/dhtmlx/common/imgs/",
            align: "left"
        });

        MasterItemToolbar.addText("", 1, "<a style='font-size:20pt;font-weight:bold'>P&C TOOLS</a>");
        
        MasterItemToolbar.addButton("spacer", 6, "", "");
        MasterItemToolbar.addSpacer("spacer");

        MasterItemToolbar.addButton("Import_Delete_List", 10, "<a style='font-size:10pt;font-weight:bold;color:blue;'>Import Delete List</a>", "");
        MasterItemToolbar.addText("", 11, " | ");

        var mf_opts = [
            ['Sample_Delete_List', 'obj', 'Download File mẫu Delete', 'xlsx.gif'],
            ['sep02', 'sep', '', '']
        ];

        MasterItemToolbar.addButtonSelect("Sample_File", 12, "Download Sample File", mf_opts, "database.gif");
        MasterItemToolbar.addText("", 13, " | ");

        MasterItemToolbar.addText("", 17, " ||| ");
        // MasterItemToolbar.addButton("copy_item",4, "Copy And Paste Item", "save.gif");

        MasterItemToolbar.attachEvent("onClick", function(name) {
            //console.log(name);
            if (name == "Import_SOLine_List") { // insert or update
                UploadFile();
            } else if (name == "Import_Delete_List") {
                importDelFile();
            } else if (name == "Sample_Delete_List") {
                var url = 'https://docs.google.com/spreadsheets/d/1C38cjRImJLkHGshidHDP6ROWIDhj6CgxNiuF4LyL_n0/edit?usp=sharing';
                window.open(url, '_blank');
            }


        });
    }


    // import: Chức năng Xóa nhiều GLID
    function importDelFile() {
        // check thêm trường hợp chọn chức năng này
        var conf = confirm("Đây là chức năng XÓA nhiều JOBJACKET. Chọn Ok để tiếp tục");
        if (!conf) location.reload();

        var dhxWins;
        if (!dhxWins) {
            dhxWins = new dhtmlXWindows();
        }

        var id = "WindowsDetail";
        var w = 400;
        var h = 100;
        var x = Number(($(window).width() - 400) / 2);
        var y = Number(($(window).height() - 50) / 2);
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



    initLayout();
    loadDataGrid();
    MasterItemToolbar();
</script>