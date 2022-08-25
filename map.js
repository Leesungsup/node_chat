var express = require('express');
var app = express();
var port = process.env.PORT || 3003
app.use(express.static('public_html'));

app.listen(port,function(){
    console.log("서버시작");
});