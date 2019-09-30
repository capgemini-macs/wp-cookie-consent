"use strict";

var _cgCookieMethods = require("./cg-cookie-methods");

/**
 * Embed YT player with or without cookies if user allowed 'statistics' cookies
 *
 * Usage:
 * add HTML element with data-yt="{video id}" e.g.: <div class="iframe-wraper" data-yt="123456"></div>
 * import module and execute embedYTPlayers() function
 *
 * @author Remigiusz Loginow <remigiusz.loginow@capgemini.com>
 */
var embedYTPlayers = function embedYTPlayers() {
  var allow = (0, _cgCookieMethods.allowCookie)("statistics");
  ready(function() {
    var players = document.querySelectorAll("[data-yt]");
    var _iteratorNormalCompletion = true;
    var _didIteratorError = false;
    var _iteratorError = undefined;

    try {
      for (
        var _iterator = players[Symbol.iterator](), _step;
        !(_iteratorNormalCompletion = (_step = _iterator.next()).done);
        _iteratorNormalCompletion = true
      ) {
        var player = _step.value;
        var id = player.getAttribute("data-yt");
        var url = void 0;

        if (allow) {
          url = "https://www.youtube.com/embed/".concat(
            id,
            "?autoplay=0&rel=0"
          );
        } else {
          url = "https://www.youtube-nocookie.com/embed/".concat(
            id,
            "?autoplay=0&rel=0"
          );
        }

        player.innerHTML = '<iframe class="video_yt" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" type="text/html" src="'.concat(
          url,
          '" frameborder="0" allowfullscreen></iframe>'
        );
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
  });
};

var ready = function ready(fn) {
  if (
    document.attachEvent
      ? document.readyState === "complete"
      : document.readyState !== "loading"
  ) {
    fn();
  } else {
    document.addEventListener("DOMContentLoaded", fn);
  }
};

module.exports = {
  embedYTPlayers: embedYTPlayers
};
