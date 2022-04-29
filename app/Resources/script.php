<?php

use App\Helpers\KN;

if (defined('INLINE_JS')) {
	?>
	<script>
	<?php
}	?>
/* Init */
NProgress.start();
(function() {

	window.vanillaPjax = new vPjax({selector: 'a:not([target="_blank"])', wrap: '#wrap', timeOut: 3000}).init() // .form('[data-vpjax]')
	document.addEventListener("vPjax:start", (e) => {
		NProgress.start()
	})
	document.addEventListener("vPjax:finish", (e) => {
		NProgress.done()
		init()
	})
	init()
	setTimeout(() => {
		NProgress.done()
	}, 500)

})()

/* Helpers */
function trimAny(str, chars) {
    var start = 0, 
        end = str.length

    while(start < end && chars.indexOf(str[start]) >= 0)
        ++start

    while(end > start && chars.indexOf(str[end - 1]) >= 0)
        --end

    return (start > 0 || end < str.length) ? str.substring(start, end) : str
}

/* /Helpers */

/* Basicplate Defaults */

function init() {

	// Click actions
	document.querySelectorAll("[data-action]").forEach(function(actionBtn) {
		actionBtn.addEventListener("click", async (e, i) => {
			
			NProgress.start()

			e.target.classList.add('disabled')
			e.target.setAttribute('disabled', true)

			const url = location.origin + '/' + trimAny(e.target.getAttribute('data-action'), '/')
			const formData = new FormData();

			let abortController = new AbortController()
			const request = await fetch(url, {
				method: 'POST',
				mode: 'cors',
				cache: 'no-cache',
				credentials: 'same-origin',
				headers: {
					'X-Requested-With': 'fetch',
				},
				redirect: 'follow',
				referrerPolicy: 'same-origin',
				signal: abortController.signal,
				body: formData
			}).then(function (response) {

				return response.ok ? response.json() : false

			}).then(function (dom) {

				return dom

			}).catch(function (err) {

				return false
				throw err

			})

			if (request) {
				asyncResponse(request)
			} else {
				alert("<?php echo KN::lang('alert.a_problem_occurred'); ?>")
			}
			
			setTimeout(() => {
				NProgress.done()
				e.target.classList.remove('disabled')
				e.target.setAttribute('disabled', false)
			}, 500)
			
			e.preventDefault()

		})
	});

	
}

async function formSender(e, url) {

	// Preparing URL and Form Data
	let formId = '#' + e.target.id
	document.querySelector(formId).classList.add('form-section-active')
	NProgress.start()
	url = location.origin + '/form/' + trimAny(url, '/')

	const formData = new FormData(e.target)

	// Preparing Fetch API
	let abortController = new AbortController()

	// Fetching
	const request = await fetch(url, {
		method: 'POST',
		mode: 'cors',
		cache: 'no-cache',
		credentials: 'same-origin',
		headers: {
			'X-Requested-With': 'fetch',
		},
		redirect: 'follow',
		referrerPolicy: 'same-origin',
		signal: abortController.signal,
		body: formData
	}).then(function (response) {

		return response.ok ? response.json() : false

	}).then(function (dom) {

		return dom

	}).catch(function (err) {

		return false
		throw err

	})

	if (request) {
		asyncResponse(request, formId)
	} else {
		alert("<?php echo KN::lang('alert.a_problem_occurred'); ?>")
	}
	
	setTimeout(() => {
		NProgress.done()
		document.querySelector(formId).classList.remove('form-section-active')
	}, 500)
	
	e.preventDefault()
}

function asyncResponse(response, selector = null) {

	// message output
	if (response.alert_type !== undefined) {
		switch (response.alert_type) {
			case 'toast': 

				let wrapper = document.querySelector('#toastPlacement')

				if (wrapper) {

					// calculating reading time
					const words = (response.title + ' ' + response.message).trim().split(/\s+/).length;
					const time = (words * 0.50) * 1000;

					// creating toast element
					let toastId = "toast_" + (Math.random() + 1).toString(36).substring(7)
					const toast = document.createElement('div');
					toast.classList.add('toast', 'toast-' + response.status)
					toast.setAttribute("role", "alert")
					toast.setAttribute("id", toastId)
					toast.setAttribute("aria-live", "assertive")
					toast.setAttribute("aria-atomic", "true")
					toast.setAttribute("data-bs-delay", time)
					toast.innerHTML = `<div class="toast-header">` + 
					`	<strong class="me-auto">` + response.title + `</strong>` + 
					`	<button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>` + 
					`</div>` + 
					`<div class="toast-body">` + 
					response.message +
					`</div>`

					// adding dom and starting
					wrapper.appendChild(toast)

					const toastEl = document.querySelector('#' + toastId)
					const toastIns = new bootstrap.Toast(toastEl)
					toastIns.show()

				} else {
					alert(response.title + ' ' + response.message)
				}

			break
			case 'card': 

				if (selector && document.querySelector(selector)) {
					// Creating response div
					let el = document.querySelector(selector)
					if (! el.querySelector('.response-message')) {

						const wrap = document.createElement('div');
						wrap.classList.add('response-message')
						el.insertBefore(wrap, el.firstChild)

					}

					el.querySelector('.response-message').innerHTML = `<div class="alert alert-` + response.status + ` alert-dismissible fade show" role="alert">` +
					`<strong>' + response.title + '</strong> ` + response.message +
					`<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>` +
					`</div>`

				} else {
					alert(response.title + ' ' + response.message)
				}

			break
			default: 
				alert(response.title + ' ' + response.message)
		}
	}

	// reload
	if (response.reload !== undefined) {

		setTimeout(() => {
			window.vanillaPjax.reload(response.reload[0])
		}, (1000 * response.reload[1]))
	}

	// form reset
	if (response.form_reset !== undefined && response.form_reset) {

		document.querySelector(selector).reset()

	}
}
<?php
if (defined('INLINE_JS')) {
	?>
	</script>
	<?php
}	?>