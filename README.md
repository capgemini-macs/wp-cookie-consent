# Wordpress GDPR compatible Cookie Consent plugin

This plugin allows a user to choose which types of cookies does he accept. There are three types: 'necessary', 'preferences' and 'statistics'. User can decline all, allow all, or allow individual type. 'Necessary' cookies are required if 'preferences' or 'statistics' marked.

## Usage

Enable plugin :)

You can control those cookies in two ways:

### HTML tags

Add your JS scripts as `type="text/plain"`. The plugin would enable it if the user allowed cookies of that type.

```html
<script src="/my/script-with-cookie.js" type="text/plain" data-name='cookie_necessary'></script>
```

### Helper functions

ES6 module `assets/js/cg-cookie-methods.js` have two helper functions:

* `getCookie(cName)` - returns cookie value or null if cookie is missing
* `setCookie(cType, cName, value, exdays)` - save cookie if user allows cookie of `cType` type.

## Embedding YT player with/without cookies

Add HTML element with `data-yt="{video id}"` e.g.: 

```html
<div class="iframe-wraper" data-yt="123456"></div>
```

import ES6 module `assets/js/cg-cookie-yt.js` and run `embedYTPlayers()`