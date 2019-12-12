const puppeteer = require('puppeteer');

exports.parseSite = async (login, pass, res) => {
	const browser = await puppeteer.launch({args: ['--no-sandbox', '--proxy-server=socks5://172.104.135.13:9050']});
//	const browser = await puppeteer.launch({headless: false});
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

	const element = await page.$("#section1");
    const text = await page.evaluate(element => element.textContent, element);

	browser.close();
        res.statusCode = 200;
        res.setHeader('Content-Type', 'application/json');
        res.end(JSON.stringify({ a: text}));
};
