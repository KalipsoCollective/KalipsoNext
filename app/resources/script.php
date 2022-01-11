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

	window.vanillaPjax = new vPjax('a:not([target="_blank"])', '#wrap').form('[data-vpjax]').init()
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

	let tableVariables = {

		usersTable: {
			selector: "#usersTable",
			language: "en",
			source: [
				{
					"id": 1,
					"username": "test1",
					"email": "test1@example.com",
					"birthday": "07.07.2021",
					"status": "active",
					"action": "action"
				},
				{
					"id": 5,
					"username": "test5",
					"email": "test5@example.com",
					"birthday": "01.01.2021",
					"status": "passive",
					"action": "action"
				},
				{
					"id": 4,
					"username": "test4",
					"email": "test4@example.com",
					"birthday": "05.05.2021",
					"status": "deleted",
					"action": "action"
				},
				{
					"id": 2,
					"username": "test2",
					"email": "test2@example.com",
					"birthday": "09.12.2021",
					"status": "active",
					"action": "action"
				},
				{
					"id": 3,
					"username": "<div>test3</div>",
					"email": "test3@example.com",
					"birthday": "10.12.2021",
					"status": "passive",
					"action": "action"
				}
			],
			columns: [ 
				{
					"searchable": {
						"type": "number",
						"min": 1,
						"max": 999
					},
					"orderable": true,
					"title": "#",
					"key": "id"
				},
				{
					"searchable": {
						"type": "text",
						"maxlenght": 50
					},
					"orderable": true,
					"title": "<?php echo KN::lang('def.username'); ?>",
					"key": "username"
				},
				{
					"searchable": {
						"type": "text",
						"maxlenght": 50
					},
					"orderable": true,
					"title": "<?php echo KN::lang('def.email'); ?>",
					"key": "email"
				},
				{
					"searchable": {
						"type": "text",
						"maxlenght": 50
					},
					"orderable": true,
					"title": "<?php echo KN::lang('def.birth_date'); ?>",
					"key": "birthday"
				},
				{
					"searchable": {
						"type": "select",
						"datas": [
							{"value": 'active', "name": "Active"},
							{"value": 'passive', "name": "Passive"},
							{"value": 'deleted', "name": "Deleted"}
						],
					},
					"orderable": true,
					"title": "<?php echo KN::lang('def.status'); ?>",
					"key": "status"
				},
				{
					"searchable": false,
					"orderable": false,
					"title": "<?php echo KN::lang('def.action'); ?>",
					"key": "action"
				}
			],
			lengthOptions: [
				{
					"name": "10",
					"value": 10,
				},
				{
					"name": "1",
					"value": 1,
				},
				{
					"name": "50",
					"value": 50,
				},
				{
					"name": "100",
					"value": 100,
					"default": true
				},
				{
					"name": "<?php echo KN::lang('def.all'); ?>",
					"value": 0,
				}
			],
			customize: {
				tableWrapClass: "table-responsive",
				tableClass: "table table-bordered",
				tableHeadClass: "",
				tableBodyClass: "",
				tableFooterClass: "",
				inputClass: "form-control form-control-sm",
				selectClass: "form-control form-control-sm",
				paginationUlClass: null,
                paginationLiClass: null,
                paginationAClass: null
			},
			tableHeader: {
				"searchBar": true
			},
			tableFooter: {
				"visible": true,
				"searchBar": true
			}
		}

	}

	for(const [key, value] of Object.entries(tableVariables)) {
		window[key] = new KalipsoTable(value);
	}
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