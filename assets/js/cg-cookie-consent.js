function getCookie (name) {
  var re = new RegExp(name + '=([^;]+)')
  var value = re.exec(document.cookie)
  return (value != null) ? unescape(value[1]) : null
}

function setCookie (e, o, r) {
  var t = new Date()
  t.setTime(t.getTime() + 24 * r * 60 * 60 * 1e3)
  var i = null
  r !== 'session' && (i = 'expires=' + t.toUTCString()), document.cookie = e + '=' + o + ';' + i + ';path=/'
}

function cookieExists (name) {
  return getCookie(name) !== null
}

function runCookiesPlugin () {

var cookiePopupTemplate = window.lodash.template(
              '<div id="cookiePopup" class="section__cookies" tabindex="-1"> \
                <div class="section__cookies__container dialog" role="dialog" aria-labelledby="dialog-title" aria-describedby="dialog-description"> \
                  <h2 id="dialog-title" class="section__title col-12"><%- title %></h2> \
                  <div id="dialog-description" class="section__cookies__text"><%= text %></div> \
                    <div class="section__cookies__checkbox"> \
                      <form> \
                      <div> \
                        <input type="checkbox" name="cookie_necessary" value="cookie_necessary" id="cookie_necessary" onclick="unsetNecessary()" checked> \
                        <label for="cookie_necessary"><span></span> <%- cookie_necessary %></label> \
                      </div> \
                      <div> \
                        <input type="checkbox" name="cookie_preferences" value="cookie_preferences" id="cookie_preferences" onclick="setNecessary()" checked> \
                        <label for="cookie_preferences"><span></span> <%- cookie_preferences %></label> \
                      </div> \
                      <div> \
                        <input type="checkbox" name="cookie_statistics" value="cookie_statistics" id="cookie_statistics" onclick="setNecessary()" checked> \
                        <label for="cookie_statistics"><span></span> <%- cookie_statistics %></label> \
                      </div> \
                    </form> \
                  </div> \
                  <div class="section__cookies__buttons"> \
                    <button id="decline" class="section__button--cookies" onclick="cookiesDecline()"> \
                      <p><%- decline %></p> \
                      <span class="sr-only"><%- decline_cookie_info %></span> \
                    </button> \
                    <button id="accept" class="section__button--cookies section__button section__button--transparent" onclick="cookiesAccept()"> \
                      <p><%- accept %></p> \
                      <span class="sr-only"><%- accept_cookie_info %></span> \
                    </button> \
                  </div> \
                </div> \
              </div>'
            )
  var cookiePopupEscaped  = cookiePopupTemplate( {
                              'title': cookie_script_vars.title,
                              'text': cookie_script_vars.text,
                              'cookie_necessary': cookie_script_vars.cookie_necessary,
                              'cookie_preferences': cookie_script_vars.cookie_preferences,
                              'cookie_statistics': cookie_script_vars.cookie_statistics,
                              'decline': cookie_script_vars.decline,
                              'decline_cookie_info': cookie_script_vars.decline_cookie_info,
                              'accept': cookie_script_vars.accept,
                              'accept_cookie_info': cookie_script_vars.accept_cookie_info
                            } )

  var newDiv = document.createElement('div')
  newDiv.innerHTML = cookiePopupEscaped;

  document.body.appendChild(newDiv)

  var cookiesNames = ['cookies_temp', 'cookies_all', 'cookie_necessary', 'cookie_preferences', 'cookie_statistics']
  if (!cookiesNames.some(cookieExists)) {
    var cookiePopup = document.getElementById('cookiePopup')
    cookiePopup.style.visibility = 'visible'
    cookiePopup.style.opacity = '1'
  } else {
    for (var i = 1; i < cookiesNames.length; i++) {
      if (getCookie(cookiesNames[i]) !== null) {
        if (cookiesNames[i] === 'cookies_all') {
          document.querySelectorAll("script[data-name='cookie_necessary']").forEach(function (script) {
            script.setAttribute('data-cookies', 'accepted')
          })
          document.querySelectorAll("script[data-name='cookie_preferences']").forEach(function (script) {
            script.setAttribute('data-cookies', 'accepted')
          })
          document.querySelectorAll("script[data-name='cookie_statistics']").forEach(function (script) {
            script.setAttribute('data-cookies', 'accepted')
          })
        } else {
          document.querySelectorAll('script[data-name=' + cookiesNames[i] + ']').forEach(function (script) {
            script.setAttribute('data-cookies', 'accepted')
          })
        }
      }
    }
    runScripts()
  }
}

// setting declined for all
function cookiesSettingsClear () {
  document.querySelectorAll('.section__cookies__checkbox input').forEach(function (checkbox) {
    document.querySelectorAll('script[data-name=' + checkbox.name + ']').forEach(function (script) {
      script.setAttribute('data-cookies', 'declined')
    })
  })
}

// setting accepted
function cookiesAccept () {
  cookiesSettingsClear()
  var cookiesAccepted = []
  document.querySelectorAll('.section__cookies__checkbox input:checked').forEach(function (checkbox) {
    document.querySelectorAll('script[data-name=' + checkbox.name + ']').forEach(function (script) {
      script.setAttribute('data-cookies', 'accepted')
    })
    cookiesAccepted.push(checkbox.name)
  })
  var cookiesTemp = false
  if (cookiesAccepted.length === 3) {
    setCookie('cookies_all', '1', 9999)
  } else if (cookiesAccepted.length === 0) {
    cookiesTemp = true
  } else {
    for (var i = 0; i < cookiesAccepted.length; i++) {
      setCookie(cookiesAccepted[i], '1', 9999)
    }
  }
  runScripts()
  cookiesPopupClose(cookiesTemp)
}

function cookiesDecline () {
  cookiesSettingsClear()
  cookiesPopupClose(true)
}

function runScripts () {
  const scripts = document.querySelectorAll('script[type="text/plain"]')
  let customDataLayer = {}
  for (let script of scripts) {
    if (script.getAttribute('data-cookies') === 'accepted') {
      const oScript = document.createElement('script')
      const oScriptText = document.createTextNode(script.text)
      oScript.appendChild(oScriptText)
      document.body.appendChild(oScript)
      customDataLayer = _.merge( customDataLayer, window.dataLayerItems[script.getAttribute('data-name')])
    }
  }

  // push custom layers to GTM
  window.dataLayer.push(customDataLayer)
}

function cookiesPopupClose (cookiesTemp) {
  var cookiePopup = document.getElementById('cookiePopup')
  cookiePopup.style.visibility = 'hidden'
  cookiePopup.style.opacity = '0'
  if (cookiesTemp) {
    setCookie('cookies_temp', '1', 'session')
  }
}

// necessary must be checked if any other is checked
function setNecessary (e) {
  if (event.target.checked) {
    if (!document.getElementById('cookie_necessary').checked) {
      document.getElementById('cookie_necessary').checked = true
    }
  }
}

function unsetNecessary (e) {
  if (!event.target.checked) {
    if (document.getElementById('cookie_preferences').checked) {
      document.getElementById('cookie_preferences').checked = false
    }
    if (document.getElementById('cookie_statistics').checked) {
      document.getElementById('cookie_statistics').checked = false
    }
  }
}
