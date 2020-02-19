const puppeteer = require('puppeteer');
const rp = require('request-promise');
const fs = require('fs');
const path = require('path');

exports.parseSantander = async (login, pass, flag) => {
	let messageError = 'Uwaga! Zarejestrowana nieudana próba parsowania banku %bankName%. Proszę sprawdzić.';
	let urlRequest = 'https://e.apatris.pl/mod/api/request-sms?token=bank-token&flag=' + flag;
	let balance = 0;
	const browser = await puppeteer.launch({args: ['--no-sandbox', '--proxy-server=socks5://172.104.135.13:9050'], userDataDir: './data/data_' + login});
	//const browser = await puppeteer.launch({ headless: false, userDataDir: './data/data_' + login});

	const page = await browser.newPage();
	await page.setDefaultNavigationTimeout(0);
	// try {
		await page.goto('https://www.centrum24.pl/centrum24-web/login');
		await page.waitFor(3000);
	// } catch (e) {
	// 	try {
	// 		await page.goto('https://www.centrum24.pl/centrum24-web/login');
	// 		await page.waitFor(3000);
	// 	} catch (e) {
	// 		browser = await puppeteer.launch({args: ['--no-sandbox'], userDataDir: './data/data_' + login});
	// 		page = await browser.newPage();
	//
	// 		try {
	// 			await page.goto('https://www.centrum24.pl/centrum24-web/login');
	// 			await page.waitFor(3000);
	// 		} catch (e) {
	// 			browser.close();
	// 			return {status:false, message: messageError + ' Connection Error'};
	// 		}
	// 	}
	// }

	// try {
		//login step1
		await page.waitForSelector('#logowanie-inner-NIK #input_nik');
		await page.type('#logowanie #input_nik', login);
		await page.click('[name=loginButton]');
		//login step1 end

		await page.waitForResponse(response => response.status() === 200);
		await page.waitFor(6000);
	// } catch (e) {
	// 	browser.close();
	// 	return {status:false, message: messageError + ' Login Step 1 Error'};
	// }

	// try {
		//login step2
		let pasT = await page. $('.passwordTable');
		if (pasT) {
			const pass2 = pass;
			let www = await page.evaluate((pass2) => {
				let arrayPass = pass2.split('');
				let k = 0;
				$(".passwordTable input").each(function() {
					if (arrayPass[k] !== undefined) {
						$(this).val(arrayPass[k]);
					}
					k = k + 1;
				});
			}, pass2);
		} else {
			await page.waitForSelector('#logowanie #ordinarypin');
			await page.type('#logowanie #ordinarypin', pass);
		}
	// } catch (e) {
	// 	browser.close();
	// 	return {status:false, message: messageError + ' Login Step 2 Error'};
	// }

	// try {
		//if chekbox remember
		var checkRemamber = await page. $('input[type="checkbox"]');
		if (checkRemamber) {
			await page.click('input[type="checkbox"]');
		}
		//if chekbox remember

		await page.click('#okBtn2');
		//login step2 end

		await page.waitForResponse(response => response.status() === 200);
		await page.waitFor(2000);

		//if step3
		let checkRemamber2 = await page. $('.orderProcessWizard input[type="checkbox"]');
		if (checkRemamber2) {
			await page.waitForSelector('.orderProcessWizard #confirm-button');
			await page. $('input[type="checkbox"]');
			await page.click('.orderProcessWizard input[type="checkbox"]');
			await page.click('.orderProcessWizard #confirm-button');
		}
		//if step3 end
		await page.waitForResponse(response => response.status() === 200);
		await page.waitFor(6000);
	// } catch (e) {
	// 	browser.close();
	// 	return {status:false, message: messageError + ' Login checkbox Error'};
	// }

	// try {
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
			}, 59000); });


			await page.type('#input_nik.input_sms_code', code.replace('-', ''));
			await page.click('[name=loginButton]');
		}
	// } catch (e) {
	// 	browser.close();
	// 	return {status:false, message: messageError + ' Login sms Error'};
	// }

	//#wylogowanie .error

	await page.waitForResponse(response => response.status() === 200);
	await page.waitFor(3000);

	const accAmount = await page.$(".md-account-ammount-big");
  balance = await page.evaluate(accAmount => accAmount.textContent.replace(/\s+/g,''), accAmount);
	await page.waitFor(2000);
	console.log(balance);

	//link to page history
	let bHistory = await page. $('#menu_multichannel_cbt_history');
	let bHistory2 = await page. $('#menu_multichannel_your_finances');
	if (bHistory) {
		await page.waitForSelector('#menu_multichannel_cbt_history');
		await page.click('#menu_multichannel_cbt_history');
	} else if (bHistory2) {
		await page.waitForSelector('#menu_multichannel_your_finances');
		await page.click('#menu_multichannel_your_finances');
	}
	//link to page history end

	await page.waitForResponse(response => response.status() === 200);
	await page.waitFor(11000);

	// try {
		await page.evaluate(() => {
			$('#presetDateSelect').find('input[value="LAST_30_DAYS"]').click();
		});

		await page.waitFor(8000);

		let urlRR = __dirname + '/tmp/' + flag;

		await page._client.send('Page.setDownloadBehavior', {behavior: 'allow', downloadPath: urlRR});

		await page.click('#btn-csv');
		await page.click('#btn-csv');
		await page.click('.btn-csv-download');
		await page.waitFor(5000);
	// } catch (e) {
	// 	browser.close();
	// 	return {status:false, message: messageError + ' Parse Error'};
	// }

	browser.close();
	return {status:true, message:'', balance : balance};
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


exports.parseCiti = async (login, pass, flag, cardEnd) => {
	let messageError = 'Uwaga! Zarejestrowana nieudana próba parsowania banku %bankName%. Proszę sprawdzić.';
	let urlRequest = 'https://e.apatris.pl/mod/api/request-sms?token=bank-token&flag=' + flag;

	const browser = await puppeteer.launch({args: ['--no-sandbox', '--proxy-server=socks5://172.104.135.13:9050'], userDataDir: './data/data_' + login});
//	const browser = await puppeteer.launch({ headless: false, userDataDir: './data/data_' + login});

	const page = await browser.newPage();
	// try{
		await page.goto('https://www.citibankonline.pl/apps/auth/signin/');
		await page.waitFor(1000);

		//login begin
		await page.waitForSelector('#SignonForm');
		await page.type('#username_input', login);
		await page.waitFor(2000);

		await page.type('#password_input', pass);
		await page.click('#submit_body');
		//login end
	// } catch (e) {
	// 	browser.close();
	// 	return {status:false, message: messageError + ' Login Error'};
	// }

	await page.waitForResponse(response => response.status() === 200);
	await page.waitFor(12000);


	let inputCode = await page. $('#otpInputTextFP_root input[name=otpInputTextFP]');
	if (inputCode) {
			var optionsP = {uri: urlRequest + '&type=1', headers: {'User-Agent': 'Request-Promise'}, json: true};
			rp(optionsP).then(function (repos) {}).catch(function (err) {
				browser.close();
				return {status: false};
			});

			//await page.waitFor(20000);

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

			if (code == '') {
				browser.close();
				return {status:false, message: messageError + ' sms Error'};
			}
			await page.type('#otpInputTextFP_root input[name=otpInputTextFP]', code.replace('-', ''));
			await page.click('#cookieAcceptBtn');

			//await page.waitForResponse(response => response.status() === 200);
			await page.waitFor(8000);
			await page.waitForSelector('#headingTwo');
	}

	await page.waitForSelector('#headingTwo');
	await page.click('#headingTwo');

	await page.waitFor(2000);

	await page.waitForSelector('#subCCAccordion');

	let cardExist = await page.evaluate((strCard) => {
		if ($('#subCCAccordion h5:contains("' + strCard + '")').length) {
			$('h5:contains("' + strCard + '")').trigger('click');
			return 1;
		}
		return 0;
	}, cardEnd);

	if (cardExist == 0) {
		browser.close();
		return {status:false, message: messageError + ' Card Error'};
	}

	try {
		await page.waitFor(30000);
		await page.evaluate(() => {
			$('#paginated-datagrid-body a.ui-grid-search-filter').trigger('click');
		});

		await page.waitFor(2000);
		await page.evaluate(() => {
			$('#durationFilter_input').val(60).change();
		});


		const directory = __dirname + '/tmp/' + flag;
		fs.readdir(directory, (err, files) => {
		  if (err) throw err;

		  for (const file of files) {
		    fs.unlink(path.join(directory, file), err => {
		      if (err) throw err;
		    });
		  }
		});

		//let directory = __dirname + '\\tmp\\citi';
		await page._client.send('Page.setDownloadBehavior', {behavior: 'allow', downloadPath: directory});

		await page.waitFor(3000);
		await page.evaluate(() => {
			$('#subapp-transaction-links a.download-icon-click').trigger('click');
		});

		await page.waitFor(2000);
		await page.evaluate(() => {
			$('.popover.bottom #cbol-download-popover-selected').trigger('click');
		});

		await page.waitFor(2000);
		await page.evaluate(() => {
			$('.popover.bottom #cbol-download-popover-list li[id="5"]').trigger('click');
		});

		await page.waitFor(2000);
		await page.evaluate(() => {
			$('.popover.bottom #btnPolish_root #btnPolish_body').trigger('click');
		});

		await page.waitFor(10000);
	} catch (e) {
		browser.close();
		return {status:false, message: messageError + ' Parse Error'};
	}

	browser.close();
	return {status:true};
}

exports.parserTime = async (link, account) => {
	const browser = await puppeteer.launch({args: ['--no-sandbox', '--proxy-server=socks5://172.104.135.13:9050'], userDataDir: './data/data_' + account.login});
	//const browser = await puppeteer.launch({ headless: false, userDataDir: './data/data_' + account.login});
	const page = await browser.newPage();
	try {
		const viewPort={width:1280, height:960}
		await page.setViewport(viewPort);

		await page.goto(link);
		await page.waitFor(2000);

		let loginhide = await page. $('#loginHide.x-thm');
		if (loginhide) {
			await page.click('#loginHide.x-thm');
			await page.waitFor(6000);

			await page.waitForSelector('#modalData #userEmail')
		  await page.type('#modalData #userEmail', account.login);

			await page.waitFor(2000);
			await page.click('#modalData .btn.x-na');

			await page.waitFor(2000);
			await page.waitForSelector('#modalData #userPassword')
			await page.type('#modalData #userPassword', account.pass);

			await page.waitFor(2000);
			await page.click('#modalData .btn.x-na');

			await page.waitFor(5000);
		}
		await page.waitFor(2000);

		await page.click('table.mng td a.x-ob-cd');
		await page.waitFor(6000);

		let eEmail = await page.$('#modalData table div[class ^= block]');
		let mEmailT = '';
		if (eEmail) {
			mEmailT = await page.evaluate(() => {
				let ass = '';
				$("#modalData table div[class ^= block]" ).each(function( index ) {
						let assP = '';
						let assPS = '';
						let aStrong = $(this).find('strong');
						if (aStrong.length) {

							let fNSpan = $(this).find('small:first');
							if (fNSpan.length && !fNSpan.find('a').length) {
								assPS = fNSpan.text();
							}
							assP  = $(this).find('strong').text() + '-' + assPS;
						}
						ass	+= (ass ? ';' : '') + assP + '<' + $(this).find('a').text() + '>';
				});
				return ass;
			});
		}

		let data = await page.evaluate((emailT) => {
				var textS = $('#editionid').html();
				var tagList2 = '';
				if ($('#editionid').length) {
					var re = /\s*<br>\s*/
					var tagList = textS.split(re);
					var datesList = [];
					for (const tlist of tagList) {
							var dateP = tlist.split(' - ');
							var d1 = dateP[0];
							var d2 = dateP[1];

							var dateP2 = new Date(d2);
							if (dateP2 != 'Invalid Date') {
								var monthP2 = '' + (dateP2.getMonth() + 1);
								var dayP2 = '' + dateP2.getDate();
								var yearP2 = dateP2.getFullYear();
								var resDate2 = dayP2 + '.' + monthP2 + '.' + yearP2;

								var dateP1 = new Date(d1);
								if (dateP1 != 'Invalid Date') {
									var monthP1 = '' + (dateP1.getMonth() + 1);
									var dayP1 = '' + dateP1.getDate();
									var yearP1 = dateP1.getFullYear();
									var resDate1 = dayP1 + '.' + monthP1 + '.' + yearP1;
								} else {
									var dateP1 = new Date(yearP2 + '-' + monthP2 + '-' + parseInt(d1));
									var monthP1 = '' + (dateP1.getMonth() + 1);
									var dayP1 = '' + dateP1.getDate();
									var yearP1 = dateP1.getFullYear();
									var resDate1 = dayP1 + '.' + monthP1 + '.' + yearP1;
								}
								datesList.push(resDate1 + '-' + resDate2);
							}
					}

					tagList2 = datesList.join('; ');
				}


				var category1 = $("#hvrout2").clone().children().remove().end().text();
				var category2 = $("#hvrout2 a").text();

				var textS2 = $("#hvrout3 td").clone().children().remove().end().text();
				var re = /\s*\n\s*/
				var tagList = textS2.split(re);
				var frequency = '';
				if (tagList.length >= 1) {
					frequency = tagList[tagList.length - 1];
				}

				var date1 = new Date($('.lead:eq(0) span:first').attr('content')),
		         month1 = '' + (date1.getMonth() + 1),
		         day1 = '' + date1.getDate(),
		         year1 = date1.getFullYear();

				var date2T = '';
				if ($('.lead:eq(0) span:eq(1)').length) {
					var date2 = new Date($('.lead:eq(0) span:eq(1)').attr('content')),
					    month2 = '' + (date2.getMonth() + 1),
					    day2 = '' + date2.getDate(),
					    year2 = date2.getFullYear();
					date2T = day2 + '.' + (month2 < 10 ? '0' + month2 : month2) + '.' + year1;
				}
				let now = new Date();
				return {
					dateParse: now.getDate() + '.' + (now.getMonth() + 1) + '.' + now.getFullYear(),
					title: $('.page-wrapper h1').text(),
					date1: day1 + '.' + (month1 < 10 ? '0' + month1 : month1) + '.' + year1,
					date2: date2T,
					address: $('.lead:eq(1)').text(),
					contact: emailT,
					categories: category1 + '; ' + category2,
					frequency: frequency.trim(),
					dates: tagList2.trim()
				};
		}, mEmailT);
		console.log(data);

		browser.close();
		return data;
	} catch (e) {
		console.log('error');
		browser.close();
		return null;
	}
}

exports.parserTimes = async () => {
	let login = 'glogr@me.com';
	let pass = '7801';

	async function autoScroll(page) {
    await page.evaluate(async () => {
        await new Promise((resolve, reject) => {
            var totalHeight = 0;
            var distance = 100;
            var timer = setInterval(() => {
                var scrollHeight = document.body.scrollHeight;
                window.scrollBy(0, distance);
                totalHeight += distance;

                if(totalHeight >= scrollHeight){
                    clearInterval(timer);
                    resolve();
                }
            }, 300);
        });
    });
	}

	const browser = await puppeteer.launch({args: ['--no-sandbox', '--proxy-server=socks5://172.104.135.13:9050'], userDataDir: './data/data_' + login});
	//const browser = await puppeteer.launch({ headless: false, userDataDir: './data/data_' + login});
console.log('brouser');
	const page = await browser.newPage();
	const viewPort={width:1280, height:960}
 	await page.setViewport(viewPort);

	await page.goto('https://10times.com/events');
	await page.waitFor(2000);

	await page.waitForSelector('.btn[data-id=today]');
	await page.click('.btn[data-id=today]');

	await page.waitForResponse(response => response.status() === 200);
	await page.waitFor(5000);

	let inputCode = await page. $('#loginHide.x-thm');
	if (inputCode) {
		await page.click('#loginHide.x-thm');
		await page.waitFor(6000);

		await page.waitForSelector('#modalData #userEmail')
	  await page.type('#modalData #userEmail', login);

		await page.waitFor(2000);
		await page.click('#modalData .btn.x-na');

		await page.waitFor(2000);
		await page.waitForSelector('#modalData #userPassword')
		await page.type('#modalData #userPassword', pass);

		await page.waitFor(2000);
		await page.click('#modalData .btn.x-na');

		await page.waitFor(5000);
	}
	console.log('login');
	await autoScroll(page);
console.log('scrool');
	const listA = await page.$$eval('.listing.text-muted tr.box h2 a', listA => listA.map((a) => {
		let text = a.textContent;
		return {title:text.trim(), link: a.getAttribute("href")};
	}));

	browser.close();
	console.log('end')
	return {data:listA};
}
