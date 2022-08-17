<?php
    include "conn.php";
    if(isset($_SESSION["kakao_member_code"])==false || !$_SESSION['kakao_member_code']){
    ?>
        <script>
            location.replace("login.php");
        </script>
    <?php
      exit;
    }

?>
<!DOCTYPE html>
<html lang="ko">
<head>
	<meta charset="UTF-8" />
	<title>카카오톡</title>
	<meta name="viewport" content="width=device-width,initial-scale=1.0,minimum-scale=1.0,maximum-scale=1.0" />
	<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css" rel="stylesheet">
	<style>
		.divFriendTr {
			height:33px;
			display:inline-block;
			line-height:33px;
			vertical-align:middle;
			padding-top:6px;
			padding-bottom:6px;
			padding-left:14px;
			margin:0px;
			width:calc(100% - 14px);
			clear:both;
		}

		.divChatTr {
			min-height:33px;
			display:inline-block;
/*			line-height:33px;*/
			vertical-align:middle;
			padding-top:6px;
			padding-bottom:6px;
			padding-left:10px;
			margin:0px;
			width:calc(100% - 10px);
			float:left;
			clear:both;
			font-size:13px
		}

		.divChatTrMy {
			min-height:33px;
			display:inline-block;
/*			line-height:33px;*/
			vertical-align:middle;
			padding-top:6px;
			padding-bottom:6px;
			padding-right:30px;
			margin:0px;
			width:calc(100% - 30px);
			float:right;
			clear:both;
			font-size:13px
		}
	</style>
	<script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
	<!-- Mustache CDN -->
	<script src="https://cdnjs.cloudflare.com/ajax/libs/mustache.js/0.1/mustache.min.js"></script>
	<script src="/jquery.bpopup.min.js"></script>

	<script>
	    let websocket=null;
	    $(document).ready(function(){
	        connect();
	        loadMemberList();
	    });
	    function connect(){
	        websocket=new WebSocket('ws://');
	        websocket.onopen=function(e){
	            let data={
					"code":"member_login",
					"memberCode":"<?php echo $_SESSION["kakao_member_code"]?>",
					"memberAlias":"<?php echo $_SESSION["kakao_member_alias"]?>"
				};
	            sendMessage(data);
	        }
	    }
	    function sendMessage(msg){
	        websocket.send(JSON.stringify(msg));
	    }
	    function loadMemberList(){
                    $.ajax({
                        type: 'POST',
                        url: "getMemberList.php",
                        data: {},
                        dataType : 'text',
                        cache: false,
                        async: false
                    })
                        .done(function( result ) {
                            //성공했을때
                            let memberList = {"MEMBER": JSON.parse(result)};

                            var output = Mustache.render($("#divMemberList").html(), memberList);
                            $("#divMemberList").html(output);
                        })
                        .fail(function( result, status, error ) {
                            //실패했을때
                            alert("에러 발생:" + error);
                        });
                }
                function login(){
                    if(!document.getElementById("ddlMemberList").value){
                        alert("아이디를 선택해 주세요");
                        return false;
                    }
                    document.frm.submit();
                }
		function openChat(){
		    $("#MAIN").css("left",($(document).width()+100));
		    $("#MAIN").load("chat.php",function(){
		        $("MAIN").animate({left:0,top:0});
		    });
		}
	</script>
</head>
<body style="margin:0px">
	<div style="width:100%; display:inline-block; height:630px; padding:0px; margin:0px; position:relative; left:0px; top:0px" id="MAIN">
	<div style="width:20%; display:inline-block; height:100%; background-color:#ececed; padding:0px; padding-top:10px; margin:0px; text-align:center; float:left;">
	<i class="fas fa-user" style="font-size:28px; color:#909297"></i>
	</div>
	<div style="width:76%; display:inline-block; height:100px; background-color:#ffffff; padding:0px; padding-top:10px; margin:0px; float:left">
	<div style="width:100%; height:30px; padding:0px; margin:0px; color:black; padding-left:14px">
	친구
	</div>
	<div style="width:100%; height:calc(100%-30px); padding:0px; margin:0px; margin-bottom:-30px; color:black; overflow-y:auto" id="divMemberList">
    		{{#MEMBER}}
    				<div class="divFriendTr">
    					<div style="float:left">
    						<img src="/kakaoimg/kakaoicon.png" style="width:33px; height:33px">
    					</div>
    					<div style="float:left; margin-left:7px" onclick="openChat();">
    						{{alias}}
    					</div>
    				</div>

    		{{/MEMBER}}
	</div>
	</div>
    <div style="width:0%; height:0px; padding:0px; margin:0px; position:relative; left:0px; top:0px" id="BACKGROUND">
	</div>
</body>
</html>