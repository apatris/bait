var express = require('express');
var app = express();
var parser = require('./parser');
var fs = require('fs');
var path = require('path');

app.get('/get-parse-data', function (req, res) {
	var query = req.query;
	if (query && query.login && query.pass && query.email) {
		parser.parseWniski(query.login, query.pass, query.email).then(result => {
			res.write(JSON.stringify(result));
			res.end();
		}) ;
	}
});

app.get('/get-bank-santander', function (req, res) {
	var query = req.query;
	if (query && query.login && query.pass && query.flag) {
		parser.parseSantander(query.login, query.pass, query.flag).then(result => {
			res.write(JSON.stringify(result));
			res.end();
		}) ;
	}
});

app.get('/get-bank-citi', function (req, res) {
	var query = req.query;
	if (query && query.login && query.pass) {
		parser.parseCiti(query.login, query.pass).then(result => {
			res.write(JSON.stringify(result));
			res.end();
		}) ;
	}
});

app.get('/get-bank-files', function (req, res) {
	let token = 'jjff6fda%f';
	let resultG = {file:null};
	var query = req.query;
	if (query && (query.token == token) && query.flag) {
		let getFiles = function (dir, files_){
		  files_ = files_ || [];
		   let files = fs.readdirSync(dir);
		   for (var i in files){
		     let name = dir + '/' + files[i];
		     if (fs.statSync(name).isDirectory()){
		            getFiles(name, files_);
		     } else {
		       files_.push(name);
		     }
		   }
		  return files_;
		};

		let file = getFiles(__dirname + '/tmp/' + query.flag);
		if (file.length > 0) {
			let thisF = file[file.length -1];
			let fileName = path.parse(thisF).name;
			let fileExt = path.parse(thisF).ext;
			resultG = {file:"http://tech.uatopl.com/protected/nodejs/tmp/" + query.flag + "/" + fileName + fileExt};
		}
	}

	res.write(JSON.stringify(resultG));
	res.end();
});

app.listen(3000, function () {
  console.log('Example app listening on port 3000!');
});
