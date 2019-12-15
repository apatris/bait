var express = require('express');
var app = express();
var parser = require('./parser');

app.get('/get-parse-data', function (req, res) {
	var query = req.query;
	if (query && query.login && query.pass && query.email) {
		parser.parseWniski(query.login, query.pass, query.email).then(result => {
			res.statusCode = 200;
			res.setHeader('Access-Control-Allow-Origin', '*');
			res.setHeader('Access-Control-Allow-Methods', 'GET, POST, OPTIONS, PUT, PATCH, DELETE');
			res.setHeader('Access-Control-Allow-Headers', 'X-Requested-With,content-type');

			res.setHeader('Access-Control-Allow-Credentials', true);
			res.setHeader('Content-Type', 'application/json');

			res.write(JSON.stringify(result));
			res.end();
		}) ;
	}
});

app.listen(3000, function () {
  console.log('Example app listening on port 3000!');
});
