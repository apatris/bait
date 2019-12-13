var http = require('http');
var url = require('url');
var parser = require('./parser');

http.createServer(function(req, res) {
	var urlParsed = url.parse(req.url, true);
	var query = urlParsed.query;

	switch (urlParsed.pathname) {
		case '/get-parse-data':
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
			break;
		default:
			res.end();
		break;
	}
}).listen(3000);
