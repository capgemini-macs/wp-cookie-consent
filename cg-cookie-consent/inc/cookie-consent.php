<?php
/**
 * Allows to set an individual group of cookies, all groups or don't allow at all
 *
 * @package CapGemini
 */

namespace CG\Cookie_Consent;

function cookies_consent_js() { 
	?>

	<script type="text/javascript" id="cookie-consent-js"> 
	<?php
	echo <<<SCRIPT
	function getCookie(name) {
		var re = new RegExp(name + "=([^;]+)");
		var value = re.exec(document.cookie);
		return (value != null) ? unescape(value[1]) : null;
	}

	function setCookie(e, o, r) {
    var t = new Date;
    t.setTime(t.getTime() + 24 * r * 60 * 60 * 1e3);
    var i = null;
    "session" !== r && (i = "expires=" + t.toUTCString()), document.cookie = e + "=" + o + ";" + i + ";path=/"
	}

	function cookieExists(name) {
		return getCookie(name) !== null;
	}

	window.onload = function() {
		var cookies_names = ['cookies_temp', 'cookies_all', 'cookies1', 'cookies2', 'cookies3'];
		if (!cookies_names.some(cookieExists)) {
			var cookiePopup = document.getElementById('cookiePopup')
			cookiePopup.style.visibility = "visible"
			cookiePopup.style.opacity = "1"
		} else {
			for (var i=1; i<cookies_names.length; i++) {
				if (getCookie(cookies_names[i]) !== null) {
					if (cookies_names[i] == 'cookies_all') {
						document.querySelectorAll("script[data-name='cookies1']").forEach(function(script) {
							script.setAttribute("data-cookies", "accepted");
						})
						document.querySelectorAll("script[data-name='cookies2']").forEach(function(script) {
							script.setAttribute("data-cookies", "accepted");
						})
						document.querySelectorAll("script[data-name='cookies3']").forEach(function(script) {
							script.setAttribute("data-cookies", "accepted");
						})
					} else {
						document.querySelectorAll("script[data-name="+cookies_names[i]+"]").forEach(function(script) {
							script.setAttribute("data-cookies", "accepted");
						})
					}
				}
			}
			run_scipts()
		}
	};

	// setting declined for all 
	function cookies_settings_clear() {
		document.querySelectorAll(".section__cookies__checkbox input").forEach(function(checkbox) {
			document.querySelectorAll("script[data-name="+checkbox.name+"]").forEach(function(script) {
			script.setAttribute("data-cookies", "declined");
			})
		})
	}

	// setting accepted
	function cookies_accept() {
		cookies_settings_clear()
		var cookies_accepted = [];
		document.querySelectorAll(".section__cookies__checkbox input:checked").forEach(function(checkbox) {
			document.querySelectorAll("script[data-name="+checkbox.name+"]").forEach(function(script) {
				script.setAttribute("data-cookies", "accepted");
				cookies_accepted.push(script.getAttribute("data-name"));
			})
		})
		var cookies_temp = false;
		if (cookies_accepted.length == 3) {
			setCookie("cookies_all", "1", 9999)
		} else if (cookies_accepted.length == 0) {
			cookies_temp = true
		} else {
			for (var i=0; i<cookies_accepted.length; i++) {
				setCookie(cookies_accepted[i], "1", 9999)
			}
		}
		run_scipts()
		cookies_popup_close(cookies_temp)
	}

	function cookies_decline() {
		cookies_settings_clear()
		cookies_popup_close(true)
	}

	function run_scipts() {
		const scripts = document.getElementsByTagName('script')
		for (let script of scripts) {
			if (script.getAttribute('type') === 'text/plain' && script.getAttribute('data-cookies') === 'accepted') {
				const oScript = document.createElement('script')
				const oScriptText = document.createTextNode(script.text)
				oScript.appendChild(oScriptText)
				document.body.appendChild(oScript)
			}
		}
	}

	function cookies_popup_close(cookies_temp) {
		var cookiePopup = document.getElementById('cookiePopup')
		cookiePopup.style.visibility = "hidden"
		cookiePopup.style.opacity = "0"
		if (cookies_temp) {
			setCookie("cookies_temp", "1", "session")
		}
	}
SCRIPT;
	?>
	</script>

	<?php
}

function cookies_consent_html() {
	?>

	<script type="text/javascript" id="cookie-consent-html"> 
	<?php
		$cookies1 = __( 'Check if you agree with cookies1!', 'capgemini' );
		$cookies2 = __( 'Check if you agree with cookies2!', 'capgemini' );
		$cookies3 = __( 'Check if you agree with cookies3!', 'capgemini' );
		$decline = __( 'Decline', 'capgemini' );
		$decline_cookie_info = __( 'Decline cookie information', 'capgemini' );
		$accept = __( 'Accept', 'capgemini' );
		$accept_cookie_info = __( 'Accept cookie information', 'capgemini' );

		echo <<<SCRIPT
		var html = '<div id="cookiePopup" class="section__cookies" tabindex="-1">' +
									'<div class="section__cookies__container dialog" role="dialog" aria-labelledby="dialog-title" aria-describedby="dialog-description">' +
									'<h2 id="dialog-title" class="section__title col-12">Cookie consent</h2>' +
									'<div id="dialog-description" class="section__cookies__text"></div>' +
										'<div class="section__cookies__checkbox">' +
											'<form>' +
												'<div>' +
													'<input type="checkbox" name="cookies1" value="cookies1" id="cookies1">' +
													'<label for="cookies1">{$cookies1}</label>' +
												'</div>' +
												'<div>' +
													'<input type="checkbox" name="cookies2" value="cookies2" id="cookies2">' +
													'<label for="cookies2">{$cookies2}</label>' +
												'</div>' +
												'<div>' +
													'<input type="checkbox" name="cookies3" value="cookies3" id="cookies3">' +
													'<label for="cookies3">{$cookies3}</label>' +
												'</div>' +
											'</form>' +
										'</div>' +	
										'<div class="section__cookies__buttons">' +
											'<button id="decline" class="section__button--cookies" onclick="cookies_decline()">' +
												'<p>{$decline}</p>' +
												'<span class="sr-only">{$decline_cookie_info}</span>' +
											'</button>' +	
											'<button id="accept" class="section__button--cookies section__button section__button--transparent" onclick="cookies_accept()">' +
												'<p>{$accept}</p>' +
												'<span class="sr-only">{$accept_cookie_info}</span>' +
											'</button>' +		
										'</div>' +	
									'</div>' +
							 '</div>'

		document.write(html)
SCRIPT;
	?>
	</script>

	<?php
}


function cookies_consent_css() {
	?>
	<link rel="stylesheet" type="text/css" media="all" href="<?php echo plugin_dir_url( __FILE__ ); ?>../assets/css/cg-cookie-consent.css">
	<?php
}

add_action( 'wp_head', 'CG\Cookie_Consent\cookies_consent_js' );
add_action( 'wp_head', 'CG\Cookie_Consent\cookies_consent_css' );
add_action( 'wp_footer', 'CG\Cookie_Consent\cookies_consent_html' );

