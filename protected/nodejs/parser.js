const puppeteer = require('puppeteer');

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
