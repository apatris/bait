const puppeteer = require('puppeteer');
var rp = require('request-promise');

exports.parseSantander = async (login, pass, flag) => {
	let urlRequest = 'https://e.apatris.pl/mod/api/request-sms?token=bank-token&flag=' + flag;

	const browser = await puppeteer.launch({args: ['--no-sandbox', '--proxy-server=socks5://172.104.135.13:9050'], userDataDir: './data/data_' + login});
	//const browser = await puppeteer.launch({ headless: false, userDataDir: './data/data_' + login});

	const page = await browser.newPage();

	await page.goto('https://www.centrum24.pl/centrum24-web/login');
	await page.waitFor(1000);
//	const navigationPromise = page.waitForNavigation({waitUntil: ['networkidle2'] })

	//login step1
	await page.waitForSelector('#logowanie-inner-NIK #input_nik');
	await page.type('#logowanie #input_nik', login);
	await page.click('[name=loginButton]');
	//login step1 end

	await page.waitForResponse(response => response.status() === 200);
	await new Promise(function(resolve, reject) { setTimeout(function() { resolve(true); }, 3000); });

	//login step2
	let pasT = await page. $('.passwordTable');
	if (pasT) {
		for (var i = 0; i < pass.length; i++) {
			var pasTP = await page. $('.passwordTable #pass' + (i + 1));
			if (pasTP) {
				await page.type('.passwordTable #pass' + (i + 1), pass.charAt(i));
			}
		}
	} else {
		await page.waitForSelector('#logowanie #ordinarypin');
		await page.type('#logowanie #ordinarypin', pass);
	}

	//if chekbox remember
	var checkRemamber = await page. $('input[type="checkbox"]');
	if (checkRemamber) {
		await page.click('input[type="checkbox"]');
	}
	//if chekbox remember

	await page.click('#okBtn2');
	//login step2 end

	await page.waitForResponse(response => response.status() === 200);

	await new Promise(function(resolve, reject) { setTimeout(function() { resolve(true); }, 2000); });

	//if step3
	let checkRemamber2 = await page. $('input[type="checkbox"]');
	if (checkRemamber2) {
		await page.waitForSelector('.orderProcessWizard #confirm-button');
		await page. $('input[type="checkbox"]');
		await page.click('.orderProcessWizard input[type="checkbox"]');
		await page.click('.orderProcessWizard #confirm-button');
	}
	//if step3 end
	await page.waitForResponse(response => response.status() === 200);

	await new Promise(function(resolve, reject) { setTimeout(function() { resolve(true); }, 6000); });

	let inputCode = await page. $('#input_nik.input_sms_code');
	if (inputCode) {
		var optionsP = {uri: urlRequest + '&type=1', headers: {'User-Agent': 'Request-Promise'}, json: true};
	 	rp(optionsP).then(function (repos) {}).catch(function (err) {
			browser.close();
	 	  return {status: false};
	 	});

		let code = ''; //get query - check if insert code
		let options = {uri: urlRequest + '&type=2', headers: {'User-Agent': 'Request-Promise'}, json: true};
		await new Promise(function(resolve, reject) { setTimeout(function() {
			rp(options).then(function (repos) {
				if (repos.status) {
					code = repos.code;
					resolve(true);
				} else {
					resolve(false);
					console.log('error request get code 1');
				}
			}).catch(function (err) {
				resolve(false);
				console.log('error request get code 2');
			});
		}, 80000); });

		await page.type('#input_nik.input_sms_code', code.replace('-', ''));
		await page.click('[name=loginButton]');
	}

	//#wylogowanie .error

	await page.waitForResponse(response => response.status() === 200);

	//link to page history
	await new Promise(function(resolve, reject) { setTimeout(function() { resolve(true); }, 2000); });

	await new Promise(function(resolve, reject) { setTimeout(function() { resolve(true); }, 2000); });
	await page.waitForSelector('#menu_multichannel_cbt_history');
	await page.click('#menu_multichannel_cbt_history');
	//link to page history end

	await page.waitForResponse(response => response.status() === 200);
	await new Promise(function(resolve, reject) { setTimeout(function() { resolve(true); }, 8000); });

	let urlRR = __dirname + '/tmp/' + flag;

	await page._client.send('Page.setDownloadBehavior', {behavior: 'allow', downloadPath: urlRR});

	await page.click('#btn-csv');
	await page.click('#btn-csv');
	await page.click('.btn-csv-download');
	await page.waitFor(5000);

	browser.close();
	return {status:true};
}




exports.parseWniski = async (login, pass, email) => {
	//try {
		const browser = await puppeteer.launch({args: ['--no-sandbox', '--proxy-server=socks5://172.104.135.13:9050']});
		//const browser = await puppeteer.launch({headless: false});
		const page = await browser.newPage();

		await page.goto('https://wnioski.mazowieckie.pl/MuwWsc/PL');
		await page.waitFor(1000);
		const navigationPromise = page.waitForNavigation({waitUntil: ['networkidle2'] })

		//login form begin
		await page.waitForSelector('[name="UserName"]')
	  await page.type('[name="UserName"]', login);
	  await page.type('[name="Password"]', pass)
	  await page.click('#btnSubmitLogin');
		//login form end

		await page.waitForResponse(response => response.status() === 200);
		await navigationPromise;

		//check errors after login
		var item = await page. $('.validation-summary-errors');
		if (item) {
			browser.close();
			return {result:'parse_noauth>>>'};
		}
		//

		//email insert form begin
		var emailE = await page. $('#login-box [name="Email"]');
		if (emailE) {
			await page.type('[name="Email"]', email);
			await page.click('#btnSubmitLogin');
			await page.waitForResponse(response => response.status() === 200);
		}
		//email insert form end

		await page.waitForSelector('.formSection')
		const tds = await page.$$eval('.formSection', tds => tds.map((td) => {
			let text = td.textContent;
      return {title:text.trim(), section:td.getAttribute("id")};
 		}));

		let header = await page.$("h2.formHeader");
		let headerText = await page.evaluate(header => header.textContent, header);

		let resDataTds = [];
		for(const td of tds) {
			const td2 = await page.$("." + td.section);
			let textTd2 = '';
			if (td2) {
				 textTd2 = await page.evaluate(td2 => td2.textContent, td2);
			}

			resDataTds.push(td.title + (textTd2 ? '||' + textTd2.trim() + '###' : ''));
		}
		browser.close();


		return {result:'parse_complete>>>' + headerText + '::' + resDataTds.join('###')};
};


exports.parseCiti = async (login, pass) => {
	let result = false;

	//const browser = await puppeteer.launch({args: ['--no-sandbox', '--proxy-server=socks5://172.104.135.13:9050'], userDataDir: './data/data_' + login});
	const browser = await puppeteer.launch({ headless: false, userDataDir: './data/data_' + login});

	const page = await browser.newPage();
	await page.goto('https://www.citibankonline.pl/apps/auth/signin/');
	await page.waitFor(1000);

	//login begin
	await page.waitForSelector('#SignonForm');
	await page.type('#username_input', login);
	await page.type('#password_input', pass);
	await page.click('#submit_body');
	//login end

	await page.waitForResponse(response => response.status() === 200);
	await new Promise(function(resolve, reject) { setTimeout(function() { resolve(true); }, 5000); });

	await page.click('#headingTwo');

	await page.click('#subCCAccordion');

	return {result:result};
}
