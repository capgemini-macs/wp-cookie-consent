"use strict";

/**
 * Cookie get/set helper functions with user consent check
 *
 * @author Remigiusz Loginow <remigiusz.loginow@capgemini.com>
 */

/**
 * Cookie types
 */
var cookieTypes = new Set(["necessary", "preferences", "statistics"]);
/**
 * Check if cookie type is allowed
 * @param {string} cType cookie type
 */

var allowCookie = function allowCookie(cType) {
  // check if it's a proper type
  if (!cookieTypes.has(cType)) {
    return false;
  } // get allowed cookie types

  var allowedCookies = getAllowedCookies(); // check if type is allowed

  if (allowedCookies.has(cType)) {
    return true;
  }

  return false;
};
/**
 * Get types allowed by user
 */

var getAllowedCookies = function getAllowedCookies() {
  // return all types if user allowed all
  if (getCookie("cookies_all") === "1") {
    return cookieTypes;
  } // search for individual types if user didn't allow all

  var allowedCookieTypes = new Set();
  var _iteratorNormalCompletion = true;
  var _didIteratorError = false;
  var _iteratorError = undefined;

  try {
    for (
      var _iterator = cookieTypes[Symbol.iterator](), _step;
      !(_iteratorNormalCompletion = (_step = _iterator.next()).done);
      _iteratorNormalCompletion = true
    ) {
      var type = _step.value;

      if (getCookie("cookie_".concat(type)) === "1") {
        allowedCookieTypes.add(type);
      }
    }
  } catch (err) {
    _didIteratorError = true;
    _iteratorError = err;
  } finally {
    try {
      if (!_iteratorNormalCompletion && _iterator.return != null) {
        _iterator.return();
      }
    } finally {
      if (_didIteratorError) {
        throw _iteratorError;
      }
    }
  }

  return allowedCookieTypes;
};
/**
 * Get cookie value
 * @param {string} cName cookie name
 */

var getCookie = function getCookie(cName) {
  var cValue = document.cookie;
  var cStart = cValue.indexOf(" " + cName + "=");

  if (cStart === -1) {
    cStart = cValue.indexOf(cName + "=");
  }

  if (cStart === -1) {
    cValue = null;
  } else {
    cStart = cValue.indexOf("=", cStart) + 1;
    var cEnd = cValue.indexOf(";", cStart);

    if (cEnd === -1) {
      cEnd = cValue.length;
    }

    cValue = unescape(cValue.substring(cStart, cEnd));
  }

  return cValue;
};
/**
 * Set cookie
 * @param {string} cType cookie type
 * @param {string} cName cookie name
 * @param {any} value cookie value
 * @param {number} exdays expiry after n days
 */

var setCookie = function setCookie(cType, cName, value, exdays) {
  if (!allowCookie(cType)) {
    return;
  }

  var exdate = new Date();
  exdate.setDate(exdate.getDate() + exdays);
  var cValue =
    escape(value) +
    (exdays === null ? "" : "; expires=" + exdate.toUTCString());
  document.cookie = cName + "=" + cValue;
};

module.exports = {
  allowCookie: allowCookie,
  getCookie: getCookie,
  setCookie: setCookie
};
