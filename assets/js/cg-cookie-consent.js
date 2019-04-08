/**
 * Cookie types
 */
const cookieTypes = new Set(['necessary', 'preferences', 'statistics'])

/**
 * Check if cookie type is allowed
 * @param {string} cType cookie type
 */
const allowCookie = (cType) => {
  // check if it's a proper type
  if (!cookieTypes.has(cType)) {
    return false
  }

  // get allowed cookie types
  const allowedCookies = getAllowedCookies()
  // check if type is allowed
  if (allowedCookies.has(cType)) {
    return true
  }
  return false
}

/**
 * Get types allowed by user
 */
const getAllowedCookies = () => {
  // return all types if user allowed all
  if (getCookie('cookies_all') === '1') {
    return cookieTypes
  }
  // search for individual types if user didn't allow all
  let allowedCookieTypes = new Set()
  for (let type of cookieTypes) {
    if (getCookie(`cookie_${type}`) === '1') {
      allowedCookieTypes.add(type)
    }
  }
  return allowedCookieTypes
}

/**
 * Get cookie value
 * @param {string} cName cookie name
 */
const getCookie = (cName) => {
  let cValue = document.cookie
  let cStart = cValue.indexOf(' ' + cName + '=')
  if (cStart === -1) {
    cStart = cValue.indexOf(cName + '=')
  }
  if (cStart === -1) {
    cValue = null
  } else {
    cStart = cValue.indexOf('=', cStart) + 1
    let cEnd = cValue.indexOf(';', cStart)
    if (cEnd === -1) {
      cEnd = cValue.length
    }
    cValue = unescape(cValue.substring(cStart, cEnd))
  }
  return cValue
}

/**
 * Set cookie
 * @param {string} cType cookie type
 * @param {string} cName cookie name
 * @param {any} value cookie value
 * @param {number} exdays expiry after n days
 */
const setCookie = (cType, cName, value, exdays) => {
  if (!allowCookie(cType)) {
    return
  }

  const exdate = new Date()
  exdate.setDate(exdate.getDate() + exdays)
  const cValue = escape(value) + ((exdays === null) ? '' : '; expires=' + exdate.toUTCString())
  document.cookie = cName + '=' + cValue
}

module.exports = {
  getCookie,
  setCookie
}
