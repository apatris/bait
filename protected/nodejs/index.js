var express = require('express');
var app = express();
var parser = require('./parser');

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


app.get('/get-uatopl', function (req, res) {
	var query = req.query;
	if (query && query.login && query.pass) {
		parser.parseApatris(query.login, query.pass).then(result => {
			res.write(JSON.stringify(result));
			res.end();
		}) ;
	}
});

app.listen(3000, function () {
  console.log('Example app listening on port 3000!');
});
