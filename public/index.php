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
		let NOW_ROOM_ID="";
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
			websocket.onmessage=function(e){
				let message = JSON.parse(e.data);
				switch(message.code){
					case "send_roominfo":
						NOW_ROOM_ID=message.room_id;
						getAllMessageFromRoom(NOW_ROOM_ID);
						break;
				}
			}
	    }
		function getAllMessageFromRoom(room_id){
			$.ajax({
                        type: 'POST',
                        url: "getAllMessageFromRoom.php",
                        data: {"room_id":room_id},
                        dataType : 'text',
                        cache: false,
                        async: false
                    })
                        .done(function( result ) {
							let chatList={"CHAT":JSON.parse(result)};
							chatList.CHAT.forEach(function(element,index){
								let isMy=false;
								let isYou=true;
								if(element.memberCode=="<?php echo $_SESSION["kakao_member_code"]?>"){
									isMy=true;
									isYou=false;
								}
								element.chat_contents=chatList.CHAT[index].chat_contents.replace(/(?:\r,\n|\r|\n)/g,'<br/>');
								chatList.CHAT[index].isMy=isMy;
								chatList.CHAT[index].isYou=isYou;
							});
							var output=Mustache.render($("#MAIN").html(), chatList);
                            $("#MAIN").html(output);

                        })
                        .fail(function( result, status, error ) {
                            //실패했을때
                            alert("에러 발생:" + error);
                        });
		}
	    function sendMessage(msg){
	        websocket.send(JSON.stringify(msg));
	    }
	    function loadMemberList(){
			$("#MAIN").css("left",(0-$(document).width()));
		    $("#MAIN").load("member.php",function(){
		        $("#MAIN").animate({left:0,top:0});
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
			});
		}
		function openChat(new_member_code,new_member_alias){
		    $("#MAIN").css("left",($(document).width()+100));
		    $("#MAIN").load("chat.php",function(){
		        $("#MAIN").animate({left:0,top:0});
		    });
			let members=[];
			let me = {
				"memberCode":"<?php echo $_SESSION["kakao_member_code"]?>",
				"memberAlias":"<?php echo $_SESSION["kakao_member_alias"]?>"
			}
			members.push(me);
			let you={
				"memberCode":new_member_code,
				"memberAlias":new_member_alias
			}
			if("<?php echo $_SESSION["kakao_member_code"]?>"!=new_member_code){
				members.push(you);
			}
			let data={
				"code":"create_room",
				"members":members
			};
			sendMessage(data);
		}
		function sendChat(){
			let chat_message=$("#chat_message").val();
			$.ajax({
                        type: 'POST',
                        url: "chat_message_insert.php",
                        data: {"roomId":NOW_ROOM_ID,"chat_message":chat_message},
                        dataType : 'text',
                        cache: false,
                        async: false
                    })
                        .done(function( result ) {
                            //성공했을때
							console.log(result);
                            if(result=="OK"){
								let data={
									"code":"send_chat",
									"room_id":NOW_ROOM_ID,
									"send_memberCode":"<?php echo $_SESSION["kakao_member_code"]?>"
								};
								sendMessage(data);
							}
                        })
                        .fail(function( result, status, error ) {
                            //실패했을때
                            alert("에러 발생:" + error);
                        });
		}
	</script>
</head>
<body style="margin:0px">
	<div style="width:100%; display:inline-block; height:630px; padding:0px; margin:0px; position:relative; left:0px; top:0px" id="MAIN">
	</div>
    <div style="width:0%; height:0px; padding:0px; margin:0px; position:relative; left:0px; top:0px" id="BACKGROUND">
	</div>
</body>
</html>