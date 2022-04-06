<?php 
    require("./Module/Template.php");
    InitPage("PCOrderlist","Offset Digital Order List");
    $email = getUser();
?>

<script>
    var LayoutMain;
    var GridMain;
    function setCookie(cname,cvalue,exdays) {
		var d = new Date();
		d.setTime(d.getTime() + (exdays*24*60*60*1000));
		var expires = "expires=" + d.toGMTString();
		document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
	}

    
    var check_gg = 0;
    
    function DocumentStart()
    {
        // Kiểm tra đăng nhập
        var VNRISIntranet = '<?php echo isset($_COOKIE["ZeroIntranet"]) ? $_COOKIE["ZeroIntranet"] : ""; ?>';
        console.log("ZeroIntranet: "+VNRISIntranet);
        if (!VNRISIntranet ) {
            var pr = prompt('Nhập tiền tố email trước @. Ví dụ: tan.doan', '');
            pr = pr.trim();
            if (!pr || pr.indexOf('@') !== -1 ) {
                alert('Bạn vui lòng nhập đúng tiền tố email là phần trước @');
            } else {
                // Save email đến bảng thống kê (au_avery.planning_user_statistics)
                setCookie('ZeroIntranet', pr, 30 );
                // setCookie('VNRISIntranet', pr, 30 );
                var VNRISIntranet = '<?php echo isset($_COOKIE["ZeroIntranet"]) ? $_COOKIE["ZeroIntranet"] : ""; ?>';
                var pr_s = '<?php echo planning_user_statistics($email); ?>';
                console.log('save planning_user_statistics: ' + pr_s);
                
                check_gg = 1;
            }
        }

        // if (check_gg) location.reload();
        if (check_gg ) {
            location.href= './';
        } else {
            var email = '<?php echo isset($_COOKIE["ZeroIntranet"]) ? $_COOKIE["ZeroIntranet"] : ""; ?>';
            if (!email ) location.reload();
        }

        LayoutMain = new dhtmlXLayoutObject({
            parent: document.body,
            pattern: "1C",
            offsets: {
                top: 65
            },
            cells: [
                {id: "a", header: true, text: "Order List"},
            ]
        });
       
        ToolbarMain.addText("text", null, "JobJacket: ");
        ToolbarMain.addInput("JobJacket", null, "", 200);
        ToolbarMain.addButton("Print", null, "<a style='background:yellow;font-weight:bold;font-size:16pt'>Print</a>", "save.gif");
        ToolbarMain.addButton("DeleteList", null, "List Delete", "save.gif");
        ToolbarMain.addButton("ExportCSV", null, "Export To Excel File", "save.gif");
        ToolbarMain.addButton("TriggerTimelines", null, "Trừ thêm Trigger Date", "save.gif");
        ToolbarMain.attachEvent("onClick", function(name) {
            if(name == "DeleteList") {
                GridMain.clearAll();
                GridMain.loadXML("Data.php?EVENT=ORDERLISTDELETE");
            } else if(name == "Print") {
                window.open("PrintPages.php?JJ=" + ToolbarMain.getValue("JobJacket").trim());
            } else if (name == "TriggerTimelines" ) {
                TriggerTimelines();
            }
        });

        ToolbarMain.attachEvent("onEnter", function(id, value) {
            if (id == "JobJacket") {
                window.open("PrintPages.php?JJ=" + ToolbarMain.getValue("JobJacket").trim());
            }
        });

        InitGrid();

    }

    function InitGrid(){
        GridMain = LayoutMain.cells("a").attachGrid();
        GridMain.setHeader("Num,ID,GLID,Bo,Order_Quantity,DueDay,Order_Receive_Day,Submit_Date,Print_Sheet,Print_Scrap,Finish_Scrap,Order_Style,Urgent_Status,Order_Check,Stock_Code_F,UPS,Stock_Size,Cut_Number,Order_Handler,PPC_Remark,Print_Machine,Request_Date,Promise_Date,SO,SO_Lines,ActiveOrder,DeleteBy,MLA");
        GridMain.setInitWidths("50,100,100,100,100,100,100,100,100,100,100,100,100,100,100,100,100,100,100,100,100,100,100,100,100,100,100,100,100,100,100,100,100");
        GridMain.attachHeader(",#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter");
        GridMain.setColAlign("center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center");
        GridMain.setColTypes("ro,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed");
        // GridMain.setColSorting("str,str,str,str");
        GridMain.setRowTextStyle("1", "background-color: red; font-family: arial;");
        GridMain.entBox.id = "GridMain";
        GridMain.enableBlockSelection(true);

        GridMain.init();
        GridMain.loadXML("Data.php?EVENT=ORDERLIST",function(){
          
        });
    }

    var dhxWins;
    function TriggerTimelines()
    {
        if(!dhxWins){ dhxWins= new dhtmlXWindows(); }
        var id = "WindowsDetail";
        var w = 680;
        var h = 300;
        var x = Number(($(window).width()-400)/2);
        var y = Number(($(window).height()-50)/2);
        var Popup = dhxWins.createWindow(id, x, y, w, h);
        dhxWins.window(id).setText("Trigger Date Timelines");

        TriggerDateTimelinesGrid = Popup.attachGrid();
        TriggerDateTimelinesGrid.setHeader("ID,MLA,NON MLA,UPDATED BY,UPDATED DATE");
        TriggerDateTimelinesGrid.setInitWidths("50,100,100,200,200");
        TriggerDateTimelinesGrid.attachHeader(",#text_filter,#text_filter,#text_filter,#text_filter");	
        TriggerDateTimelinesGrid.setColAlign("center,center,center,center,center");
        TriggerDateTimelinesGrid.setColTypes("ro,ed,ed,ed,ed");
        TriggerDateTimelinesGrid.setRowTextStyle("1", "background-color: red; font-family: arial;");
        TriggerDateTimelinesGrid.enableSmartRendering(true);
        
			
        TriggerDateTimelinesGrid.init();

        TriggerDateTimelinesGrid.loadXML("Data.php?EVENT=TRIGGERTIMELINES",function(){

            TriggerDateTimelinesGrid.attachEvent("onEnter", function(id,ind){
                TriggerDateTimelinesGrid.editCell();

                var idSave = TriggerDateTimelinesGrid.cells(id,0).getValue();
                var mla_timelines = TriggerDateTimelinesGrid.cells(id,1).getValue();
                var non_mla_timelines = TriggerDateTimelinesGrid.cells(id,2).getValue();

                // save to server
                    var jsonObjects = { idSave : idSave, mla_timelines : mla_timelines, non_mla_timelines : non_mla_timelines }

                // url to server
                    var url = "Event.php?EVENT=SAVETRIGGERTIMELINES";

                //excute with ajax function 
                $.ajax({
                    type: "POST",
                    data: { data: JSON.stringify(jsonObjects) },
                    url: url,
                    dataType: 'json',
                    beforeSend: function(x) { if (x && x.overrideMimeType) { x.overrideMimeType("application/j-son;charset=UTF-8"); } },
                    success: function(data) {
                        try{     
                            alert(data.message);
                            location.href = './';
                        }catch(e) {     
                            alert('Lỗi. Vui lòng liên hệ quản trị hệ thống. Lỗi: '+e);
                            return false;
                        }
                    },
                    error: function(xhr, status, error) {
                        alert('Lưu dữ liệu không thành công (Trigger Date Timelines): Vui lòng liên hệ quản trị hệ thống. '+xhr.responseText);
                        return false;
                    }
                });
                
            });
        });


        
        

        // TriggerDateTimelinesGrid.attachEvent("onEnter", function(id,ind){
        //     TriggerDateTimelinesGrid.editCell();
            
        // });
        



    }

</script>
<body>
</body>
</html>