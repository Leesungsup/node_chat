var express = require('express');
var app = express();
var axios = require("axios");
var port = process.env.PORT || 3003;
app.use(express.static('public_html'));

app.listen(port,function(){
    console.log("서버시작");
});

app.get("/pharmach_list",(req,res)=>{
    let response=null;
    try {
        let api = async () => {
            response = await axios.get("");
        }
        api.then((response)=>{
            res.setHeader("Acess-Control-Allow-Origin","*");
            res.json(response.data,response.body);
        });
    }
    catch (e){
        console.log(e);
    }
    return response;
});