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
		var cookies_names = ['cookies_temp', 'cookies_all', 'cookie_necessary', 'cookie_preferences', 'cookie_statistics'];
		if (!cookies_names.some(cookieExists)) {
			var cookiePopup = document.getElementById('cookiePopup')
			cookiePopup.style.visibility = "visible"
			cookiePopup.style.opacity = "1"
		} else {
			for (var i=1; i<cookies_names.length; i++) {
				if (getCookie(cookies_names[i]) !== null) {
					if (cookies_names[i] == 'cookies_all') {
						document.querySelectorAll("script[data-name='cookie_necessary']").forEach(function(script) {
							script.setAttribute("data-cookies", "accepted");
						})
						document.querySelectorAll("script[data-name='cookie_preferences']").forEach(function(script) {
							script.setAttribute("data-cookies", "accepted");
						})
						document.querySelectorAll("script[data-name='cookie_statistics']").forEach(function(script) {
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
		const scripts = document.querySelectorAll('script[type="text/plain"]')
		for (let script of scripts) {
			if (script.getAttribute('data-cookies') === 'accepted') {
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

	// necessary must be checked if any other is checked
	function setNecessary(e) {
		if (event.target.checked) {
			if (! document.getElementById('cookie_necessary').checked) {
				document.getElementById('cookie_necessary').checked = true;
			}
		}
	}

	function unsetNecessary(e) {
		if (! event.target.checked) {
			if (document.getElementById('cookie_preferences').checked) {
				document.getElementById('cookie_preferences').checked = false;
			}
			if (document.getElementById('cookie_statistics').checked) {
				document.getElementById('cookie_statistics').checked = false;
			}
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
		$cookie_necessary   = __( 'Necessary', 'capgemini' );
		$cookie_preferences  = __( 'Preferences', 'capgemini' );
		$cookie_statistics   = __( 'Statistics', 'capgemini' );
		$decline             = __( 'Decline', 'capgemini' );
		$decline_cookie_info = __( 'Decline cookie information', 'capgemini' );
		$accept              = __( 'Accept', 'capgemini' );
		$accept_cookie_info  = __( 'Accept cookie information', 'capgemini' );

		echo <<<SCRIPT
		var html = '<div id="cookiePopup" class="section__cookies" tabindex="-1">' +
									'<div class="section__cookies__container dialog" role="dialog" aria-labelledby="dialog-title" aria-describedby="dialog-description">' +
										'<h2 id="dialog-title" class="section__title col-12">This website uses cookies</h2>' +
										'<div id="dialog-description" class="section__cookies__text">' +
											'We use cookies to personalise content and ads, to provide social media features and to analyse our traffic. We also share information about your use of our site with our social media, advertising and analytics partners who may combine it with other information that you’ve provided to them or that they’ve collected from your use of their services.' +
										'</div>' +
										'<div class="section__cookies__checkbox">' +
											'<form>' +
											  '<div>' +
													'<input type="checkbox" name="cookie_necessary" value="cookie_necessary" id="cookie_necessary" onclick="unsetNecessary()">' +
													'<label for="cookie_necessary"> {$cookie_necessary}</label>' +
											  '</div>' +
												'<div>' +
													'<input type="checkbox" name="cookie_preferences" value="cookie_preferences" id="cookie_preferences" onclick="setNecessary()">' +
													'<label for="cookie_preferences"> {$cookie_preferences}</label>' +
												'</div>' +
												'<div>' +
													'<input type="checkbox" name="cookie_statistics" value="cookie_statistics" id="cookie_statistics" onclick="setNecessary()">' +
													'<label for="cookie_statistics"> {$cookie_statistics}</label>' +
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

