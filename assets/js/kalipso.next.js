/*!
 * Kalipso Next Basic Scripts
 * Version: v1.0.0
 * Copyright 2022, Kalipso Collective
 * Released under the MIT License
 */


async function kalipsoFetch(url = null, method = 'POST', data = {}) {

	url = url ?? window.location.href;

	method = method ?? 'POST';
	method = method.toUpperCase();

	data = typeof data === 'string' ? JSON.parse(data) : data;
	data = typeof data === 'object' ? data : {};

	// Fetch
	return await fetch(url, {
		method: method,
		mode: 'cors',
		cache: 'no-cache',
		headers: {
			"X-KALIPSONEXT": "1.0.0",
			"Accept": "application/json",
		},
		credentials: 'same-origin',
		referrerPolicy: 'same-origin',
		redirect: 'follow',
		body: data
	})
	.then((response) => {
		if (response.status >= 200 && response.status < 300) {
			return response.json();
		} else {
			throw new Error(JSON.stringify({
				alerts: '<div class=\"kn-toast-alert\"><div class=\"kn-alert kn-alert-danger\">Server Response Problem! ['+ response.status +']</div></div>'
			}));
		}
	})
	.then(data => { return data; })
	.catch((error) => {
		if (typeof error.message === 'string') {
			try {
		        return JSON.parse(error.message);
			} catch (e) {
		        return {
					alerts: '<div class=\"kn-toast-alert\"><div class=\"kn-alert kn-alert-danger\">Server Response Problem!</div></div>'
				};
			}
		} else {
			return error;
		}
	});
}

function kalipsoInit(firstLoad = false) {

	// Stored alert remove action
	alertRemove();

	/* Async. Form Submit */
	const forms = document.querySelectorAll('form[data-kn-form]');
	for (let i = 0; i < forms.length; i++) {
		forms[i].addEventListener("submit", async function(e) {
			e = e || window.event;
			e.preventDefault();
			NProgress.start();

			// Form Reset, Init
			const dom = e.target;
			dom.classList.add('sending');
			dom.querySelectorAll('[name]').forEach((el) => {
				el.classList.remove('is-valid');
				el.classList.remove('is-invalid');
			})

			// Append Datas
			const data = new FormData(dom);

			// Fetch
			const response = await kalipsoFetch(dom.getAttribute('action'), dom.getAttribute('method'), data);

			if (response !== undefined) {
				responseFormatter(response, dom);
			}
			setTimeout(() => {
				dom.classList.remove('sending');
				NProgress.done();
			}, 500);
			
		});
	}
	
	if (firstLoad) {

		document.addEventListener("click", async function(e) {
			// Async. Action Buttons
			if (e.target.nodeName.toUpperCase() === 'BUTTON') {
				if (e.target.getAttribute('data-kn-action')) {
					
					e.preventDefault();

					let keep = true;
					if (e.target.getAttribute('data-kn-again')) {

						if (e.target.getAttribute('data-kn-again-check')) {
							keep = false;
						} else {
							let text = e.target.innerHTML;
							e.target.innerHTML = sanitizeHTML(e.target.getAttribute('data-kn-again'));
							e.target.setAttribute('data-kn-again-check', true);
							setTimeout(() => {
								e.target.innerHTML = text;
								e.target.removeAttribute('data-kn-again-check');
							}, 3000);
						}
					} else {
						keep = false;
					}

					if (! keep) {
						
						NProgress.start();
						response = await kalipsoFetch(
							e.target.getAttribute('data-kn-action'), 
							e.target.getAttribute('data-kn-again-method'), 
							e.target.getAttribute('data-kn-again-options')
						);

						if (response !== undefined) {
							responseFormatter(response);
						}
						setTimeout(() => {
							NProgress.done();
						}, 500);

					}
				}
			}
		});
	}
	
}

const sanitizeHTML = function (str) {
	var temp = document.createElement('div');
	temp.textContent = str;
	return temp.innerHTML;
};

function alertRemove() {
	const alerts = document.querySelectorAll('.kn-alert');
	if (alerts.length) {

		for (var i = alerts.length - 1; i >= 0; i--) {
			let element = alerts[i]
			setTimeout(() => {
				element.classList.add('out');
				setTimeout(() => {
					element.remove();
				}, 800);
			}, 5000);
			
		}
	}
}

function responseFormatter(response, dom = null) {

	if (dom === null) {
		dom = document;
	}

	if (response.alerts !== undefined) {
		const alertDom = document.createElement('div');
		alertDom.innerHTML = response.alerts;
		if (alertDom.querySelector('.kn-toast-alert')) { // if with parent 
			document.querySelector('.kn-toast-alert').innerHTML = 
				alertDom.querySelector('.kn-toast-alert').innerHTML;
		} else {
			document.querySelector('.kn-toast-alert').innerHTML = 
				alertDom.innerHTML;
		}
		alertRemove();
	}

	if (dom && response.form_reset !== undefined && response.form_reset) {
		dom.reset();
	}

	if (response.modal_close !== undefined && document.querySelector(response.modal_close)) {
		const modal = bootstrap.Modal.getOrCreateInstance(document.querySelector(response.modal_close));
		modal.hide();
	}

	if (response.modal_open !== undefined && document.querySelector(response.modal_open)) {
		const modal = bootstrap.Modal.getOrCreateInstance(document.querySelector(response.modal_open));
		modal.show();
	}

	if (response.reload !== undefined) {
		const timeOut = response.reload_timeout !== undefined ? response.reload_timeout : 1;
		setTimeout(() => {
			if (response.reload === true) {
				if (window.vanillaPjax) window.vanillaPjax.reload();
				else window.location.reload();
			} else {
				if (window.vanillaPjax) window.vanillaPjax.reload(response.reload);
				else window.location.href = response.reload;
			}
		}, timeOut);
	}

	if (response.table_reset !== undefined && window[response.table_reset] !== undefined) {
		window[response.table_reset].reset();
	}

	if (dom && response.manipulation !== undefined) {

		for (const [selector, data] of Object.entries(response.manipulation)) {
			
			if (dom.querySelector(selector)) {
				/**
				 * DOM manipulation for attributes. 
				 */
				if (data.attribute !== undefined && data.attribute) {
					for ([prop, value] of Object.entries(data.attribute)) {
						dom.querySelector(selector).setAttribute(prop, value);
					}
				}

				/**
				 * DOM manipulation for adding class. 
				 */
				if (data.class !== undefined && data.class.length) {
					for (var i = 0; i < data.class.length; i++) {
						dom.querySelector(selector).classList.add(data.class[i]);
					}
				}

				/**
				 * DOM manipulation for removing class. 
				 */
				if (data.remove_class !== undefined && data.remove_class.length) {
					for (var i = 0; i < data.remove_class.length; i++) {
						dom.querySelector(selector).classList.remove(data.remove_class[i]);
					}
				}

				/**
				 * DOM manipulation inner html. 
				 */
				if (data.html !== undefined && data.html) {
					dom.querySelector(selector).innerHTML = data.html;
				}
			}
		}
	}
}

NProgress.start();
(function() {

	window.vanillaPjax = new vPjax({selector: 'a:not([target="_blank"])', wrap: '#wrap', timeOut: 3000}).init() // .form('[data-vpjax]')
	document.addEventListener("vPjax:start", (e) => {
		NProgress.start();
	})
	document.addEventListener("vPjax:finish", (e) => {
		NProgress.done();
		init();
		kalipsoInit();
	})
	init();
	kalipsoInit(true);
	setTimeout(() => {
		NProgress.done()
	}, 500)

})();