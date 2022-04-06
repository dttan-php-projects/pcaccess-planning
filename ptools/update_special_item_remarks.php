<?php
    set_time_limit(6000); 
    date_default_timezone_set('Asia/Ho_Chi_Minh');

    require_once ('../Module/Database.php');

    $table = "access_item_remark";

    $updateBy = isset($_COOKIE["ZeroIntranet"]) ? $_COOKIE["ZeroIntranet"] : "";

    $data = array(
        '1H014401-REV-00',
        '1-135596-000-00',
        '1-142922-000-00',
        'B87286',
        'B276654',
        'B275312',
        'B86940',
        'B165143',
        'B31572',
        'B277918',
        'B172606',
        'B188136',
        'B87296',
        'B282466',
        'B165144',
        'CB376497A',
        'B965027',
        'B35022',
        'B35024',
        'B231777',
        'B234300',
        'B241915',
        'B263275',
        '1-169545-000-00',
        'B41030',
        'B249269',
        'B34663',
        'B87300',
        'B94944',
        'B104391',
        'B248868',
        'B248789',
        'B38349',
        'B231819',
        '1H014830-000-00',
        '1-169540-000-00',
        '1-169333-000-00',
        'B227417',
        'B234339',
        'B53501',
        'B87399',
        'B401198',
        'B275531',
        'B67776',
        'B87404',
        'B67779',
        'B87416',
        'B67778',
        'B231822',
        'B67777',
        'B87412',
        '1-116903-000-00',
        '1-138706-000-00',
        '1-167372-000-00',
        '1-080348-000-00',
        '1-103744-000-00',
        '1-080362-000-00',
        '1-103711-000-00',
        '1-260943-000-00',
        'B53502',
        'B87402',
        'B289233',
        'B228922',
        'B89743',
        'B90679',
        'B89372',
        'B90680',
        '1-124306-000-00',
        '1-122656-000-00',
        'B164327',
        'B262587',
        'B260480',
        'B268006',
        'B268009',
        'B31570',
        'CB400410A',
        'B392876',
        'B87418',
        '1-177804-000-00',
        'B249260',
        'B361310',
        'B362425',
        '2-081850-IN2-EU',
        'B250965',
        'B248796',
        'CB376492A',
        'B370837',
        'B87414',
        '1-238045-000-00',
        'B267624',
        '1-098451-000-00',
        'P186880',
        'WW504130A',
        'WX949290A',
        'CB378691A',
        'CB366795A',
        'B101084',
        '1-116640-000-00',
        'CB374066A',
        'CB551634A',
        'CB437847A',
        '25HASICS30HS(VN)',
        'CB562285A',
        'CB327910A',
        'CB538272A',
        'CB538275A',
        'CB538282A',
        'CB538256A',
        'CB538257A',
        'CB538264A',
        '25HASICS20HS (VN)',
        '25HASICS30HSA (VN)',
        'CB437846C',
        'CB437838C',
        'CB525262A',
        'CB525256A',
        'CB525266A',
        'CB525250A',
        'P524470A',
        'CB525336A',
        'CB525342A',
        'P524466A',
        'CB525345A',
        'CB525348A',
        'P524752A',
        'CB524967A',
        'P524923A',
        'CB525064A',
        'P524765A',
        'CB525065A',
        'P524925A',
        'CB525067A',
        'CB525309A',
        'CB525312A',
        'CB525263A',
        'CB525253A',
        'P524476A',
        'CB525349A',
        'CB525351A',
        'P524764A',
        'CB525068A',
        'P524924A',
        'CB525070A',
        'CB525316A',
        'CB525331A',
        'CB525332A',
        'P494364A',
        'P524483A',
        'B578732A',
        'WX958670A',
        'B650350A',
        'WX958660A',
        '2-340014-000-00',
        '2-340017-000-00',
        '2-353257-000-00',
        '2-343923-000-00',
        '1-104072-000-00',
        'WX828510A',
        'WX828530A',
        'WX828570A',
        'WX828550A',
        'WX828610A',
        'WX828630A',
        'WX828650A',
        'WX828660A',
        'WX736060A',
        'WX743710A',
        'CB382846A',
        'CB400398A',
        'CB428241A',
        'CB400331A',
        'CB382801A',
        'CB428246A',
        'CB400418A',
        '2-084622-IN2-AP',
        '2-084622-IN1-EU',
        '2-084622-IN1-NA',
        '2-231123-INV-EU',
        '2-080091-IN2-NA',
        '2-080091-IN1-AP',
        '2-080091-IN1-EU',
        '2-081850-IN5-NA',
        '2-081850-IN2-AP',
        '2-072527-IN1-NA',
        '2-077710-IN2-NA',
        '2-077710-IN4-AP',
        '2-077710-IN1-JP',
        '25KLAC404925(VN)',
        'WW460180A',
        'WX235200A',
        'WW295500A',
        'WY413218A',
        'CB364708A',
        'CB366794A',
        'CB378689A',
        'CB378690A',
        'CB378768A',
        'CB378694A',
        'CB376494A',
        'CB378770A',
        'CB378771A',
        'CB378834A',
        'CB378835A',
        'CB378836A',
        'CB367164A',
        'CB367165A',
        'CB378772A',
        'CB376638A',
        'CB378776A',
        'CB378778A',
        'CB378779A',
        'CB376516A',
        'CB376517A',
        'CB376518A',
        'CB376519A',
        'CB376520A',
        'CB376631A',
        'CB378696A',
        'CB378697A',
        'CB376632A',
        'CB378698A',
        'CB378699A',
        'CB376633A',
        'CB378700A',
        'CB378701A',
        'CB376635A',
        'CB378702A',
        'CB378703A',
        'CB378839A',
        'CB378840A',
        'CB378841A',
        'CB393719A',
        'CB393720A',
        'CB393721A',
        'CB376654A',
        'CB376659A',
        'CB376660A',
        'CB402759A',
        'CB402760A',
        'CB402762A',
        'CB402763A',
        'CB402764A',
        'CB402765A',
        'CB411664A',
        'CB424216A',
        'CB433097A',
        'CB385283A',
        'CB385284A',
        'CB385286A',
        'CB385287A',
        'CB385289A',
        'CB385290A',
        'CB394820A',
        'CB396983A',
        'CB485023A',
        'CB427709A',
        'CB427710A',
        '25H-200215-000-00',
        'CB472263A',
        'CB427710C',
        'CB427710B',
        'CB476905A',
        'CB472276A',
        'CB476911C',
        'CB476911B',
        'CB476911A',
        'CB435664A',
        'CB445980A',
        'CB445997A',
        'CB445999A',
        'CB446000A',
        'CB446001A',
        'CB446004A',
        'CB446006A',
        'CB446011A',
        'CB446012A',
        'CB540919A',
        '2-608695-000-00',
        '2-327800-000-01',
        '2-302256-000-01',
        '2-608702-MX5-01',
        '2-608700-000-00',
        '2-608702-MX4-00',
        '2-608694-000-01',
        '2-318332-001-00',
        '2-608690-000-00',
        '2-608702-APC-00',
        '2-608702-MX5-00',
        '2-608698-000-00',
        '2-608684-000-00',
        'WY244788B',
        'WX999450C',
        'WY007108B',
        'WY270148A',
        'WY269728A',
        'WY269698A',
        'CB604023A',
        'CB600037',
        'CB389668A',
        'CB389668B',
        'CB350790A',
        'CB389404A',
        'CB389404B',
        'CB389405A',
        '1-186454-000-00',
        '1-186454-PLT-00',
        'CB398182A',
        'CB402069A',
        'CB412404A',
        '2-611516-034-00',
        '2-611516-035-00',
        '2-344193-007-00',
        '2-344193-008-00',
        '2-611617-000-00',
        '2-611617-001-00',
        '2-611617-002-00',
        '2-611617-003-00',
        '2-611617-004-00',
        '2-341015-003-00',
        '2-341015-004-00',
        '2-341015-005-00',
        '2-341015-006-00',
        '2-341015-007-00',
        '2-341015-008-00',
        '2-341015-009-00',
        '2-611516-027-00',
        '2-611516-028-00',
        '2-611516-029-00',
        '2-611516-030-00',
        '2-611516-031-00',
        '2-611516-032-00',
        '2-611516-033-00',
        '2-611617-005-00',
        '2-611617-006-00',
        '2-611617-007-00',
        '2-611617-008-00',
        '2-611617-011-01',
        '2-611516-045-00',
        '2-344193-000-00',
        '2-344193-001-00',
        '2-344193-002-00',
        '2-344193-003-00',
        '2-344193-004-00',
        '2-344193-005-00',
        '2-344193-006-00',
        '2-611617-017-01',
        '2-611617-013-01',
        '2-611617-015-01',
        '2-611617-054-00',
        '2-611617-057-00',
        '2-611617-058-00',
        '2-611617-059-00',
        '2-294778-000-00',
        '2-294778-001-00',
        '2-294778-002-00',
        '2-294778-003-00',
        '2-294778-004-00',
        '2-294778-005-00',
        '2-611617-010-00',
        '2-611617-033-00',
        '2-611617-019-01',
        '2-611516-036-00',
        '2-611516-037-00',
        '2-611516-038-00',
        '2-344193-009-00',
        '2-344193-010-00',
        '2-344193-011-00',
        'CB428252A',
        'CB400406A',
        'CB428249A',
        'CB382843A',
        'CB600037',
        'CB389668A',
        'CB350790A',
        'CB389404A',
        'CB389668B',
        'CB389404B',
        'CB389405A',
        'CB603448A',
        'CB346595B',
        'CB517445A',
        'CB604023B',
        'CB604023A',
        'CB517444A',
        'CB604024B',
        'CB604024A',
        'CB494752B',
        'CB562289A',
        'CB564325A',
        'CB564340A',
        'CB564346A',
        'CB564361A',
        'CB564356A',
        'CB564486A',
        'CB564981A',
        'P566174A',
        'P566126A',
        'P566158A',
        'P566161A',
        'P566167A',
        'P566138A',
        'P566171',
        'CB564487A',
        'CB564489A',
        'CB564488A',
        'CB564493A',
        'WY455358A',
        '1-280912-000-00',
        '25KADI4818R1AR2 (VN)',
        '25KADI60118R1A-S (VN)',
        '25KADI4043118SR (VN)',
        '25KADI4043254SR (VN)',
        '25KADI4043113SR (VN)',
        '25KADI4043130SR (VN)',
        '2-050964-IN7-0E',
        '2-050966-IN5-0A',
        '2-050966-IN6-0E',
        '2-072527-IN3-0A',
        '2-072527-IN2-0E',
        '2-072527-IN2-NA',
        '2-163003-IN3-0A',
        '2-163003-IN2-0E',
        '2-163003-IN2-NA',
        'WY207028A',
        'CB559453A',
        '2-077709-IN5-AP',
        '2-077709-IN2-NA',
        '2-077709-IN2-0E',
        'WY904908A',
        'WY904858A',
        'WY904838A',
        'WY904778A',
        'CB512743A',
        '2-375513-000-00',
        '2-334310-000-01',
        '2-340201-000-01',
        '2-375510-000-00',
        '2-334306-000-01',
        '2-340212-000-01',
        'WY663928A',
        'WY645048A',
        'WY664018A',
        'WY644788A',
        'WY663978A',
        'WY664038A',
        'CB375719B',
        'WY007128B',
        'CB375724B',
        '1-254741-000-02',
        'P185900',
        'B545350A',
        'B329444',
        'B229654',
        'B408022',
        'B413894',
        'B562782A',
        'CB321379A',
        'B234343',
        'B414837',
        'CB524455A',
        'B570623A',
        'CB485023B',
        'CB321378A',
        'B608748A',
        'B608790A',
        'B566986A',
        'B567085A',
        '1-280887-000-00',
        'CB623538A',
        'CB623540A',
        'CB623544A',
        'CB623545A',
        'CB623562A',
        'CB627489A',
        'CB627497A',
        'CB627501A',
        'CB627505A',
        'CB624909A',
        'CB627525A',
        'CB627526A',
        'CB627528A',
        'CB627532A',
        'CB512743B',
        'CB631081A',
        'CB620293A',
        'CB620432A',
        'CB627484A',
        'B627895A',
        'B627892A',
        'B627894A',
        'B627891A',
        'B566986A',
        'B567085A',
        'B578732A',
        'B608748A',
        'B608790A',
        'B634562A',
        'CB321378A',
        'B649881',
        'CB628261A',
        '1-288512-000-00',
        'WX958750A',
        'CB579726A',
        '1-288528-000-00',
        'WX736830A',
        'WY818488A',
        '1-293429-000-00',
        '1-293433-000-00',
        '1-292932-000-00',
        '1-292947-000-00',
        '1-292948-000-00',
        '1-292949-000-00',
        '1-292952-000-00',
        '1-293423-000-00',
        '1-293424-000-00',
        '1-293425-000-00',
        '1-293426-000-00',
        '1-293427-000-00',
        '1-293432-000-00',
        '1-293436-000-00',
        '1-293439-000-00',
        '2-385946-000-00',
        'CB375725B',
        'B650372A',
        'B650272A',
        'B650283A',
        'B650288A',
        'B650298A',
        'B650343A',
        'B650302A',
        'B650303A',
        'B650347A',
        'B650352A',
        'B650308A',
        'B650363A',
        'CB460249A',
        'CB447171A',
        'CB459261A',
        'CB469646B',
        'CB469482B',
        'CB485996A',
        'CB518378A',
        'CB461579A',
        'CB531544A',
        'CB452366A',
        'CB542307A',
        'CB442995A',
        'CB489524A',
        'CB489068A',
        'CB578899A',
        'CB641928A',
        'CB641930A',
        'CB641931A',
        'CB641926A',
        'CB641927A',
        'CB641927B',
        'CB641927C',
        'CB641927D',
        'CB641933A',
        'CB641935A',
        'CB489524A',
        'CB405695A',
        'CB433470A',
        'CB438738A',
        'CB559971A',
        '1-280525-000-00',
        '1-160716-000-00',
        'CB624221A',
        'P539164A',
        'CB538272A',
        'CB538275A',
        'CB538282A',
        'CB538256A',
        'CB538257A',
        'CB538264A',
        '1-261041-000-00',
        '2-375512-000-00',
        '2-375516-000-00',
        '2-385945-000-00',
        'WX657210A',
        'WX823740B',
        'WX823820A',
        'WX999450C',
        'WY007108B',
        'WY007128B',
        'WY207028A',
        'WY244788B',
        'WY269698A',
        'WY269708A',
        'WY269728A',
        'WY270148A',
        'WY562918A',
        'WY563298A',
        'WY810688A',
        'WY824668A',
        'WY873798A',
        'WZ188560A',
        'WZ284910A',
        'WZ287860A',
        'WZ288260A',
        'WZ288900A',
        '1-186454-000-01',
        '2-363948-000-00',
        '2-363948-001-00',
        '2-363948-003-00',
        '2-363948-002-00',
        '2-363948-004-00',
        'WY895228A',
        'WY901828A',
        'WY915418A',
        'WY993888A',
        'WZ044320A',
        'WZ093340A',
        'WZ093360A',
        'CB445395A',
        'CB597858A',
        'WW295430A',
        'WW295450A',
        'WW296630A',
        '2-363948-000-00',
        'WW382330A',
        'WX737200B',
        'WX493100A',
        'WX977880A',
        'CB437847B',
        'WZ478120A',
        'WX646420A',
        'WY770988A',
        'WY816538A',
        'CB620436A',
        'ATE576464',
        'CB554987A',
        'WW295550A',
        'WW296620A',
        'WW303400A',
        '2-363948-001-00',
        '2-600501-IN1-AP',
        '25HSPD4730NWT202S',
        'WX978050A',
        'ATE579787',
        'WY816518A',
        'WZ044310A',
        '2-363948-003-00',
        'WW382390A',
        'WW824410A',
        'WY309158A',
        'WY855298A',
        'WW824400A',
        '2-147019-IN3-00',
        'WX827030A',
        '2-608698-000-01',
        'WY309288A',
        'WZ288970A',
        'WY816508A',
        'WY855308A',
        'WY917498A',
        'WY917478A',
        '2-416067-000-00',
        'WZ308970A',
        'WW295470A',
        'WW311520A',
        '2-084089-IN1-NA',
        'WX737240B',
        'WX737180B',
        '1-089087-000-00',
        'WY715228A',
        'P613016A',
        'CB641931B',
        'WW295490A',
        '2-363948-002-00',
        'WW382360A',
        'WY157918A',
        'WW567570A',
        'WW590860A',
        'WX951960A',
        'WY888308A',
        'WY917488A',
        'WZ309000A',
        'WZ343080A',
        'CB641930B',
        'CB554996A',
        'WW295410A',
        'WZ093370A',
        'WZ308990A',
        'P566171A',
        'WW296580A',
        'P539162A',
        'WX480310A',
        '2-363948-004-00',
        'WW382320A',
        'WW505710A',
        'WX493090A',
        '25HLAC404925',
        'WW911110A',
        '25HPUMABPCL2S',
        '25HUAFWVID2525',
        '25HUAFWVID3232',
        '25HUAVID904484',
        '25HUAVID-905974R',
        'CB445395A',
        '1-089087-000-00',
        '25KADI005L',
        'CB430471A',
        'CB458057A',
        'CB459263A',
        'CB559454A',
        '1-248472-000-00',
        'CB554983A',
        'CB554987A',
        'CB554996A',
        '1-241537-000-00',
        '1-247605-000-01',
        '1-247605-003-01',
        '1-294011-000-00',
        '1-294011-001-00',
        'WZ288970A',
        '25KADI714E18 (VN)'
    );

    $Remark = 'Item Đặc Biệt';
    $PPCRemark = 'Item Đặc Biệt';

    $index = 0;
    foreach ($data as $key => $GLID ) {
        
        $index++;

        $check = MiQuery( "SELECT * FROM $table WHERE `GLID`='$GLID';", _conn() );
        if (!empty($check) ) {
            $CreatedDate = date('Y-m-d H:i:s');
            $sql = "UPDATE $table SET `Remark`='$Remark', `PPCRemark`='$PPCRemark', `CreatedDate`='$CreatedDate' WHERE `GLID`='$GLID';";
        } else {
            $sql = "INSERT INTO $table (`GLID`, `Remark`, `PPCRemark`) VALUES ('$GLID', '$Remark', '$PPCRemark');";
        }

        $Result = MiNonQuery( $sql, _conn() );
        if ($Result == false ) {
            echo "$index. Error: $GLID -- $sql <br>";
        } else {
            echo "$index. Success: $GLID <br>";
        }

    }



?>