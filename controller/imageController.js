// multer to upload image
const multer = require('multer');
const fs = require('fs');
const spawn = require('child_process').spawn;

module.exports = {
    uploadImage: (req, res) => {
        let tmp_path = req.files[0].path;
        console.log('req path'+req.files[0].originalname);
        let target_path = 'uploads/' + req.files[0].originalname;
        console.log('req originalname'+req.files[0].originalname);
        var src = fs.createReadStream(tmp_path);
        var dest = fs.createWriteStream(target_path);
        src.pipe(dest);
        const { spawn } = require('child_process');
        const pyProg = spawn('python', ['./../pypy.py']);
        const result_02 = spawn('python', ['function_argv.py', '카레유', '20',req.files[0].originalname]);
        result_02.stdout.on('data', (result)=>{
            console.log(result.toString());
        });

        pyProg.stdout.on('data', function(data) {

            console.log('imageController'+data.toString());
            res.write(data);
            res.end('end');
        });
        src.on('end', function() { res.sendStatus(200); });
        src.on('error', function(err) { res.sendStatus(500); });
    }
}