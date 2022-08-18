const WebSocket = require("ws");
const mysql = require("mysql");
const ws = new WebSocket.Server({port:8008});
let ALL_USER=[];
let ALL_ROOM=[];
ws.on("connection",function connect(websocket,req){
   websocket.on("message",function incoming(message){
      console.log(JSON.parse(message));
      message=JSON.parse(message);
      switch (message.code){
         case "member_login":
            login(message.memberCode,message.memberAlias);
            break;
         case "create_room":
            message.members.sort(function (a,b){
               return a.memberCode-b.memberCode;
            });
            ROOM_ID=createRoom(message.members);
            break;
      }
   });
   function login(memberCode,memberAlias){
      let member_data={
         "memberCode":memberCode,"memberAlias":memberAlias,"ws":websocket
      };
      ALL_USER.push(member_data);
      console.log("Login OK");
   }
   function createRoom(members){
      let ROOM_ID="";
      const conn=mysql.createConnection({
         host:"localhost",
         user:"",
         password:"",
         database:""
      });
      let all_member="";
      members.forEach(function (element,index){
         if(!all_member){
            all_member=element.memberCode
         }else{
            all_member+=","+element.memberCode
         }
      });
      let sql="select roomCode from room where members='"+all_member+"'";
      conn.query(sql,function (err,rows,fields){
         if(rows && rows.length>0){
            ROOM_ID=rows[0].roomCode;
         }else{
            sql="INSERT INTO room(members) values('"+all_member+"')";
            conn.query(sql,function(){});
            sql="select max(roomCode) as roomCode from room";
            conn.query(sql,function(err,rows,fields){
               ROOM_ID=rows[0].roomCode;
            });
         }
         conn.end();
         createRoomStep2(ROOM_ID,members);
         return ROOM_ID;
      });

   }
   function createRoomStep2(t_ROOM_ID,t_members){
      let room_data={
         "id":t_ROOM_ID,
         "members":t_members
      }
      let findSameRoomid = ALL_ROOM.filter(function (element){
         return element.id == t_ROOM_ID;
      });
      if(findSameRoomid.length==0){
         ALL_ROOM.push(room_data);
      }
      console.log("createRoom OK");
      sendRoomInfo(t_ROOM_ID);
   }
   function sendRoomInfo(t_ROOM_ID){
      let data={"code":"send_roominfo","room_id":t_ROOM_ID};
      sendMessage(data);
      console.log("sendRoomInfo OK");
   }
   function sendMessage(msg){
      websocket.send(JSON.stringify(msg));
   }
});