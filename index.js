const WebSocket = require("ws");
const ws = new WebSocket.Server({port:8008});
let ALL_USER=[];
ws.on("connection",function connect(websocket,req){
   websocket.on("message",function incoming(message){
      console.log(JSON.parse(message));
      message=JSON.parse(message);
      switch (message.code){
         case "member_login":
            login(message.memberCode,message.memberAlias);
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
});