const puppeteer = require('puppeteer');

exports.parseWniski = async (login, pass) => {
	//try {
		const browser = await puppeteer.launch({args: ['--no-sandbox', '--proxy-server=socks5://172.104.135.13:9050']});
		//const browser = await puppeteer.launch({headless: false});
		const page = await browser.newPage();

		await page.goto('https://wnioski.mazowieckie.pl/MuwWsc/PL');
		await page.waitFor(1000);
		const navigationPromise = page.waitForNavigation({waitUntil: ['networkidle2'] })

		await page.waitForSelector('[name="UserName"]')
	  await page.type('[name="UserName"]', login);

		await page.waitForSelector('[name="Password"]')
	  await page.type('[name="Password"]', pass)
	  await page.click('#btnSubmitLogin');

		await page.waitForResponse(response => response.status() === 200);
		await navigationPromise;

		var item = await page. $('.validation-summary-errors');
		if (item) {
			browser.close();
			return {status:'parse_noauth>>>'};
		}

		const tds = await page.$$eval('.formSection', tds => tds.map((td) => {
			var text = td.textContent;
      return {title:text.trim(), section:td.getAttribute("id")};
 		}));

		let res2 = '';
		let header = await page.$("h2.formHeader");
		let headerText = await page.evaluate(header => header.textContent, header);

		for(const elem of tds) {
			const element2 = await page.$("." + elem.section);
			let text2 = '';
			if (element2) {
				 text2 = await page.evaluate(element2 => element2.textContent, element2);
			}
			res2 = res2 + elem.title + '||' + text2.trim() + '###';
		}
		browser.close();

		return {status:'parse_complete>>>' + headerText + '::' + res2};
};
