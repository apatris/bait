var express = require('express');
var app = express();
var parser = require('./parser');
var fs = require('fs');
var path = require('path');
const objectsToCsv = require('objects-to-csv');
var cron = require('node-cron');
const csv = require('csvtojson')

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
					console.log('parse - ' + link);
					fs.writeFile('test.txt', JSON.stringify(obj), function (err) { if (err) throw err; });

					const postData = await parser.parserTime(link, account);
					if (postData) {
						console.log('postData')
						new objectsToCsv([postData]).toDisk('./tmp/resData.csv', { append: true });
					}
				}
			}
		}
	}
});

app.get('/run-parser-10-times', async function (req, res) {
	//let account = {login:'glogr@me.com', pass:'7801'};
	//const postData = await parser.parserTime('https://10times.com/bookfest-brisbane', account);

	 fs.writeFile('test.txt', '', function (err) { if (err) throw err; });
	 const postsList = await parser.parserTimes();
	 fs.writeFile('test.txt', JSON.stringify(postsList.data), function (err) { if (err) throw err; });
})

app.listen(4000, function () {
  console.log('Example app listening on port 3000!');
});
