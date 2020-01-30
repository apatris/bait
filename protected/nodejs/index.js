var express = require('express');
var app = express();
var parser = require('./parser');
var fs = require('fs');
var path = require('path');
const objectsToCsv = require('objects-to-csv');
var cron = require('node-cron');

cron.schedule('* * * * *', async function () {
	var date = new Date();
	var hours = date.getHours();
	var minutes = date.getMinutes();
	var wDay = date.getDay()
	console.log(hours + ':' + minutes)
	if ((hours == 7) && (minutes == 1)) {

		fs.writeFile('test.txt', '', function (err) { if (err) throw err; });
		const postsList = await parser.parserTimes();
		fs.writeFile('test.txt', JSON.stringify(postsList.data), function (err) { if (err) throw err; });

	} else if ((hours > 7) || ((hours == 7) && (minutes > 20))) {
		if(minutes % 3 == 0) {
			let account = {login:'glogr@me.com', pass:'7801'};
			if (wDay % 2 == 0) {
				account = {login:'datsivStepan@gmail.com', pass:'dats29'}
			}
			let file = fs.readFileSync('test.txt', 'utf8');
			if (file && (obj = JSON.parse(file))) {
				let firstPost = obj[0];
				if (firstPost && firstPost.link) {
					let link = firstPost.link;
					obj.splice(0, 1);
					fs.writeFile('test.txt', JSON.stringify(obj), function (err) { if (err) throw err; });

					const postData = await parser.parserTime(link, account);
					if (postData) {
						new objectsToCsv([postData]).toDisk('./tmp/resData2.csv', { append: true, allColumns: true });
					}
				}
			}
		}
	}
});

app.get('/run-parser-10-times', async function (req, res) {
	//let account = {login:'glogr@me.com', pass:'7801'};
	//const postData = await parser.parserTime('https://10times.com/bookfest-brisbane', account);

	// fs.writeFile('test.txt', '', function (err) { if (err) throw err; });
	// const postsList = await parser.parserTimes();
	// fs.writeFile('test.txt', JSON.stringify(postsList.data), function (err) { if (err) throw err; });
})

app.get('/get-parse-data', function (req, res) {
	var query = req.query;
	if (query && query.login && query.pass && query.email) {
		parser.parseWniski(query.login, query.pass, query.email).then(result => {
			res.write(JSON.stringify(result));
			res.end();
		}) ;
	}
});

app.get('/get-bank-centrum24', function (req, res) {
	var query = req.query;
	if (query && query.login && query.pass && query.flag) {
		parser.parseSantander(query.login, query.pass, query.flag).then(result => {
			res.write(JSON.stringify(result));
			res.end();
		}) ;
	}
});

app.get('/get-bank-citibankonline', function (req, res) {
	var query = req.query;
	if (query && query.login && query.pass && query.flag && query.card) {
		let cardEnd = query.card;
		parser.parseCiti(query.login, query.pass, query.flag, cardEnd.substr(cardEnd.length - 4)).then(result => {
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
