/* NProgress, (c) 2013, 2014 Rico Sta. Cruz - http://ricostacruz.com/nprogress
 * @license MIT */
 !function(e,n){"function"==typeof define&&define.amd?define(n):"object"==typeof exports?module.exports=n():e.NProgress=n()}(this,function(){var e,n,t={version:"0.2.0"},r=t.settings={minimum:.08,easing:"ease",positionUsing:"",speed:200,trickle:!0,trickleRate:.02,trickleSpeed:800,showSpinner:!0,barSelector:'[role="bar"]',spinnerSelector:'[role="spinner"]',parent:"body",template:'<div class="bar" role="bar"><div class="peg"></div></div><div class="spinner" role="spinner"><div class="spinner-icon"></div></div>'};function i(e,n,t){return e<n?n:e>t?t:e}function s(e){return 100*(-1+e)}t.configure=function(e){var n,t;for(n in e)void 0!==(t=e[n])&&e.hasOwnProperty(n)&&(r[n]=t);return this},t.status=null,t.set=function(e){var n=t.isStarted();e=i(e,r.minimum,1),t.status=1===e?null:e;var u=t.render(!n),c=u.querySelector(r.barSelector),l=r.speed,f=r.easing;return u.offsetWidth,o(function(n){""===r.positionUsing&&(r.positionUsing=t.getPositioningCSS()),a(c,function(e,n,t){var i;i="translate3d"===r.positionUsing?{transform:"translate3d("+s(e)+"%,0,0)"}:"translate"===r.positionUsing?{transform:"translate("+s(e)+"%,0)"}:{"margin-left":s(e)+"%"};return i.transition="all "+n+"ms "+t,i}(e,l,f)),1===e?(a(u,{transition:"none",opacity:1}),u.offsetWidth,setTimeout(function(){a(u,{transition:"all "+l+"ms linear",opacity:0}),setTimeout(function(){t.remove(),n()},l)},l)):setTimeout(n,l)}),this},t.isStarted=function(){return"number"==typeof t.status},t.start=function(){t.status||t.set(0);var e=function(){setTimeout(function(){t.status&&(t.trickle(),e())},r.trickleSpeed)};return r.trickle&&e(),this},t.done=function(e){return e||t.status?t.inc(.3+.5*Math.random()).set(1):this},t.inc=function(e){var n=t.status;return n?("number"!=typeof e&&(e=(1-n)*i(Math.random()*n,.1,.95)),n=i(n+e,0,.994),t.set(n)):t.start()},t.trickle=function(){return t.inc(Math.random()*r.trickleRate)},e=0,n=0,t.promise=function(r){return r&&"resolved"!==r.state()?(0===n&&t.start(),e++,n++,r.always(function(){0==--n?(e=0,t.done()):t.set((e-n)/e)}),this):this},t.render=function(e){if(t.isRendered())return document.getElementById("nprogress");c(document.documentElement,"nprogress-busy");var n=document.createElement("div");n.id="nprogress",n.innerHTML=r.template;var i,o=n.querySelector(r.barSelector),u=e?"-100":s(t.status||0),l=document.querySelector(r.parent);return a(o,{transition:"all 0 linear",transform:"translate3d("+u+"%,0,0)"}),r.showSpinner||(i=n.querySelector(r.spinnerSelector))&&d(i),l!=document.body&&c(l,"nprogress-custom-parent"),l.appendChild(n),n},t.remove=function(){l(document.documentElement,"nprogress-busy"),l(document.querySelector(r.parent),"nprogress-custom-parent");var e=document.getElementById("nprogress");e&&d(e)},t.isRendered=function(){return!!document.getElementById("nprogress")},t.getPositioningCSS=function(){var e=document.body.style,n="WebkitTransform"in e?"Webkit":"MozTransform"in e?"Moz":"msTransform"in e?"ms":"OTransform"in e?"O":"";return n+"Perspective"in e?"translate3d":n+"Transform"in e?"translate":"margin"};var o=function(){var e=[];function n(){var t=e.shift();t&&t(n)}return function(t){e.push(t),1==e.length&&n()}}(),a=function(){var e=["Webkit","O","Moz","ms"],n={};function t(t){return t=t.replace(/^-ms-/,"ms-").replace(/-([\da-z])/gi,function(e,n){return n.toUpperCase()}),n[t]||(n[t]=function(n){var t=document.body.style;if(n in t)return n;for(var r,i=e.length,s=n.charAt(0).toUpperCase()+n.slice(1);i--;)if((r=e[i]+s)in t)return r;return n}(t))}function r(e,n,r){n=t(n),e.style[n]=r}return function(e,n){var t,i,s=arguments;if(2==s.length)for(t in n)void 0!==(i=n[t])&&n.hasOwnProperty(t)&&r(e,t,i);else r(e,s[1],s[2])}}();function u(e,n){return("string"==typeof e?e:f(e)).indexOf(" "+n+" ")>=0}function c(e,n){var t=f(e),r=t+n;u(t,n)||(e.className=r.substring(1))}function l(e,n){var t,r=f(e);u(e,n)&&(t=r.replace(" "+n+" "," "),e.className=t.substring(1,t.length-1))}function f(e){return(" "+(e.className||"")+" ").replace(/\s+/gi," ")}function d(e){e&&e.parentNode&&e.parentNode.removeChild(e)}return t});

/*!
 * Copyright 2021, Halil Ibrahim Ercelik
 * Released under the MIT License
 * {@link https://github.com/halillusion/vpjax GitHub}
 * Inspired by defunkt's jQuery-Pjax
 */
class vPjax{constructor(t,e=null){return this.version="0.8.1",this.options={selector:null,wrap:null,formSelector:null,url:null,cacheExpire:300,timeOut:1e3},null!==e&&(this.options.wrap=e),"object"!=typeof t?(this.options.selector=t,null===e&&console.error("Wrapper is not defined!")):this.mergeObject(this.options,t),this.fetch=null,this.method="GET",this.formData=null,window.vPjax={},window.onpopstate=(t=>this.getBack(t)),this}init(){let t=document.querySelectorAll(this.options.selector);for(let e=0;e<t.length;e++)t[e].addEventListener("click",o=>{this.handler(o,t[e])});if(this.options.formSelector){let t=document.querySelectorAll(this.options.formSelector);for(let e=0;e<t.length;e++)t[e].addEventListener("submit",o=>{this.formHandler(o,t[e])})}return this}mergeObject(t,e,o=null){if(null!==t&&null!==e){const o=Object.keys(e);let i=null;for(let n=0;n<o.length;n++)i=o[n],t.hasOwnProperty(i)&&"object"==typeof e[i]?t[i]=this.mergeObject(t[i],e[i],i):t[i]=e[i]}else t=e;return t}handler(t,e){if("#"===e.getAttribute("href"))return;let o=this.urlCheck(e.getAttribute("href"));if(null===o)return void console.error("File protocol doesn't supported!");if(!1===o)return;const i=new URL(o);if(t.ctrlKey||t.shiftKey||t.altKey||t.metaKey||t.which>1)return;if(location.protocol!==i.protocol||location.hostname!==i.hostname)return void(location.href=i);if(i.href.indexOf("#")>-1&&this.stripHash(i)===this.stripHash(location))return;const n=new CustomEvent("vPjax:click",{detail:{options:this.options}});return document.dispatchEvent(n),this.formData=null,this.method="GET",this.get(i),t.preventDefault(),this}urlCheck(t){const e=/(?:^|\s)((https?:\/\/)?(?:localhost|[\w-]+(?:\.[\w-]+)+)(:\d+)?(\/\S*)?)/g;return e.exec(t)?t:"file:"==window.location.protocol?null:(t=window.location.origin+t,!!e.exec(t)&&t)}reload(t=null){this.method="GET",this.formData=null,null===t&&(t=location.href),this.get(t)}formHandler(t,e){if("#"===e.getAttribute("action"))return;let o=e.getAttribute("action");if(-1===o.indexOf(location.origin)&&(o=location.origin+("/"===o.substring(0,1)?"":"/")+o),this.formData=new FormData(e),"GET"===e.getAttribute("method").toUpperCase())return;this.method="POST";const i=new CustomEvent("vPjax:submit",{detail:{options:this.options}});return document.dispatchEvent(i),this.get(o),t.preventDefault(),this}async get(t){const e=new CustomEvent("vPjax:beforeSend",{detail:{options:this.options,url:t}});document.dispatchEvent(e);const o=new AbortController;let i;this.options.url=t,this.options.timeOut&&(i=setTimeout(()=>{if(!this.fetch){const t=new CustomEvent("vPjax:timeout",{detail:{options:this.options,fetch:o}});document.dispatchEvent(t),o.abort(),location.href=this.options.url,clearTimeout(i)}},this.options.timeOut));const n=new CustomEvent("vPjax:start",{detail:{options:this.options,abort:o}});document.dispatchEvent(n);let r={method:this.method,mode:"cors",cache:"no-cache",credentials:"same-origin",headers:{"X-VPJAX":!0},redirect:"follow",referrerPolicy:"same-origin",signal:o.signal};if(this.formData&&(r.body=this.formData),this.fetch=await fetch(this.options.url,r).then(function(t){if(t.headers.get("refresh")){let e=t.headers.get("refresh").replace(" ","");window.vPjax.refresh=e.split(";url=")}return t.headers.get("location")&&(window.vPjax.location=t.headers.get("location")),!!t.ok&&t.text()}).then(function(t){const e=new CustomEvent("vPjax:success",{detail:{dom:t.dom}});return document.dispatchEvent(e),t}).catch(function(t){const e=new CustomEvent("vPjax:error",{detail:{error:t}});throw document.dispatchEvent(e),t}),window.vPjax.location){let t=this.urlCheck(window.vPjax.location);return this.get(t),window.vPjax.location=null,this}if(window.vPjax.refresh){let t=this.urlCheck(window.vPjax.refresh[1]);setTimeout(()=>{this.get(t)},1e3*parseInt(window.vPjax.refresh[0])),window.vPjax.refresh=null}return this.fetch&&(this.loadContent(this.fetch),this.options.timeOut&&clearTimeout(i),this.fetch=null),this}loadContent(t,e=!1){let o=(new DOMParser).parseFromString(t,"text/html"),i=o.querySelector(this.options.wrap);if(!i)throw location.href=this.options.url,"Server response is not correct! -> "+t;{let t=document.querySelector(this.options.wrap);if(!t)throw location.href=this.options.url,"The element specified as selector does not exist!";{const n=new CustomEvent("vPjax:beforeExtract",{detail:{options:this.options,dom:o}});document.dispatchEvent(n);let r=i.innerHTML,s=document.querySelector("title").textContent;t.innerHTML=r,(s=o.querySelector("title").textContent)&&(document.querySelector("title").textContent=s);let l=new URL(this.options.url);e?window.history.back(-1):window.history.pushState({},"",l);const h=new CustomEvent("vPjax:finish",{detail:{options:this.options,url:l}});document.dispatchEvent(h),this.init()}}return this}stripHash(t){return t.href.replace(/#.*/,"")}form(t){return this.options.formSelector=t,this}getBack(t){const e=new CustomEvent("vPjax:popstate",{detail:{options:this.options,url:document.location}});document.dispatchEvent(e),this.get(document.location)}}

/* Kalipso Next Basic Script */
function kalipsoInit() {
	// Stored alert remove action
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
	/* Async. Form Submit */
	const forms = document.querySelectorAll('form[data-kn-form]');
	for (let i = 0; i < forms.length; i++) {
		forms[i].addEventListener("submit", async function(e) {
			e = e || window.event;
			e.preventDefault();
			alert("submit");

			let dom = e.target;

			// Append Datas
			let form = {};
			let data = new FormData(dom);
			for (var [key, value] of data) {
				let arrayType = false;
				if (key.indexOf('[]') !== -1) {
					arrayType = true;
					key = key.replace('[]', '');
				}

				if (arrayType) {
					if (form[key] === undefined) {
						form[key] = [];
					}
					form[key].push(value);
				} else {
					form[key] = value;
				}
			}

			let url = dom.getAttribute('action') ?? window.location.href;
			let method = dom.getAttribute('method') ?? 'post';
			method = method.toUpperCase();

			// Fetch
			const response = await fetch(url, {
				method: method, // or 'PUT'
				mode: 'no-cors',
				cache: 'no-cache',
				credentials: 'same-origin',
				redirect: 'follow',
				body: data
			})
			.then((response) => {
				if (response.status >= 200 && response.status < 300) {
	                return response.json();
	            } else {
	            	throw new Error(response.statusText);
	            }
			})
			.then(data => {
				console.log('Success:', data);
			})
			.catch((error) => {
				console.error('Error:', error);
			});
		});
	}
}

NProgress.start();
(function() {

	window.vanillaPjax = new vPjax({selector: 'a:not([target="_blank"])', wrap: '#wrap', timeOut: 3000}).init() // .form('[data-vpjax]')
	document.addEventListener("vPjax:start", (e) => {
		NProgress.start()
	})
	document.addEventListener("vPjax:finish", (e) => {
		NProgress.done()
		init();
		kalipsoInit();
	})
	init();
	kalipsoInit();
	setTimeout(() => {
		NProgress.done()
	}, 500)

})();