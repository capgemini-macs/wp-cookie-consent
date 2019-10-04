/**
 * Embed YT player with or without cookies if user allowed 'statistics' cookies
 * 
 * Usage: 
 * add HTML element with data-yt="{video id}" e.g.: <div class="iframe-wraper" data-yt="123456"></div>
 * import module and execute embedYTPlayers() function
 * 
 * @author Remigiusz Loginow <remigiusz.loginow@capgemini.com>
 */

import { allowCookie } from './cg-cookie-methods'

const embedYTPlayers = () => {
  const allow = allowCookie('statistics')
  ready(() => {
    const players = document.querySelectorAll('[data-yt]')
    for (const player of players) {
      const id = player.getAttribute('data-yt')
      let url
      if (allow) {
        url = `https://www.youtube.com/embed/${id}?autoplay=0&rel=0`
      } else {
        url = `https://www.youtube-nocookie.com/embed/${id}?autoplay=0&rel=0`
      }
      player.innerHTML = `<iframe class="video_yt" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" type="text/html" src="${url}" frameborder="0" allowfullscreen></iframe>`
    }
  })
}

const ready = (fn) => {
  if (document.attachEvent ? document.readyState === "complete" : document.readyState !== "loading"){
    fn()
  } else {
    document.addEventListener('DOMContentLoaded', fn)
  }
}

module.exports = {
  embedYTPlayers
}