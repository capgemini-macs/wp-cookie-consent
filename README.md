# WordPress GDPR compatible Cookie Consent plugin

This plugin allows a user to choose which types of cookies does he accept. There are three types: 'necessary', 'preferences' and 'statistics'. User can decline all, allow all, or allow individual type. 'Necessary' cookies are required if 'preferences' or 'statistics' marked.

## Requirements
* WordPress 5.0+ 
* Fieldmanager plugin installed and activated (http://fieldmanager.org/)

## Usage

1. Enable plugin :)
2. Configure plugin settings in Settings / Cookie

You can control those cookies in two ways:

### HTML tags

Add your JS scripts as `type="text/plain"`. The plugin would enable it if the user allowed cookies of that type.

```html
<script src="/my/script-with-cookie.js" type="text/plain" data-name='cookie_necessary'></script>
```

### Helper functions

ES6 module `assets/js/cg-cookie-methods.js` has two helper functions:

* `getCookie(cName)` - returns cookie value or null if cookie is missing
* `setCookie(cType, cName, value, exdays)` - save cookie if user allows cookie of `cType` type.

## Dependencies

* Fieldmanager plugin (http://fieldmanager.org/)
* lodash.js (https://lodash.com/) - registered in WordPress core since 5.0

## TODOs

* remove Fieldmanager dependency for options screen
