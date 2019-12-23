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
	if (query && query.login && query.pass) {
		parser.parseSantander(query.login, query.pass).then(result => {
			res.write(JSON.stringify(result));
			res.end();
		}) ;
	}
});

app.get('/get-bank-files', function (req, res) {
	let token = 'jjff6fda%f';
	let resultG = {file:null};
	var query = req.query;
	if (query && query.token == token) {
			var getFiles = function (dir, files_){
		  files_ = files_ || [];
		    var files = fs.readdirSync(dir);
		    for (var i in files){
		        var name = dir + '/' + files[i];
		        if (fs.statSync(name).isDirectory()){
		            getFiles(name, files_);
		        } else {
		            files_.push(name);
		        }
		    }
		    return files_;
			};

			let file = getFiles(__dirname + '\\tmp');
			if (file) {
				resultG = {file:file[0]};
			}
	}

	res.write(JSON.stringify(resultG));
	res.end();
});

app.listen(3000, function () {
  console.log('Example app listening on port 3000!');
});
