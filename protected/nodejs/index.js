var http = require('http');
var url = require('url');
var parser = require('./parser');

http.createServer(function(req, res) {
	var urlParsed = url.parse(req.url, true);
	
	var query = urlParsed.query;
	var parseData;
	
	if (query && query.login && query.pass) {
		parser.parseSite(query.login, query.pass, res);
	}
}).listen(3000);



