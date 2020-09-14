jQuery(document).ready(function ($) {

  var macsCookies = new function () {

    var self = this

    self.config = {
      'consentLifeDays': 90,
      'cookieGroups': [
        'necessary',
        'functional',
        'statistics',
        'targeting'
      ]
    }

    // Hide overlay
    $('body').click( function() {
      $('.cookieConsent__overlay.overlay-on').hide();
    } );

    // Local variable cache for user preferences
    self.allowedCookies = [];

    /**
     * Kicks it off
     */
    self.init = function () {

      /**
       * If popup is initialized, we setup GTM scripts only after user's action.
       * Otherwise we can do it based on current cookie preferences.
       */
      if ( ! self.popupInit() ){
        self.setupGTMscripts()
      }

      // PREFERENCES PAGE
      self.bindPreferencesActions()
      self.checkAllowedBoxes()

      // Block embeds
      self.blockEmbeds() // Block  embeds function

      self.trackUserConsent() // track user consent
    }

    /**
     * Cookie helpers
     */
    self.getCookie = function (name) {
      var re = new RegExp(name + '=([^;]+)')
      var value = re.exec(document.cookie)
      return (value != null) ? unescape(value[1]) : null
    }

    self.setCookie = function (name, value, days) {
      var t = new Date()
      t.setTime(t.getTime() + 24 * days * 60 * 60 * 1e3)
      var expires = 'expires=' + t.toUTCString()
      document.cookie = name + '=' + value + ';' + expires + ';path=/'
    }

    self.deleteCookie = function(name) {
      document.cookie = name +'=; Path=/; Expires=Thu, 01 Jan 1970 00:00:01 GMT;';
    }

    self.cookieExists = function(name) {
      return self.getCookie(name) !== null
    }

    /**
     * Clears local variable cache
     */
    self.resetAllowedCookies = function() {
      self.allowedCookies = []
    }

    /**
     * Rebuilds local variable cache for user preferences based on saved cookies
     */
    self.getAllowedCookies = function() {

      self.resetAllowedCookies()

      $( self.config.cookieGroups ).each( function(i,val) {
        var cookieName = 'macs_cookies_' + val + '_' + MACS_COOKIES.ID

        if ( self.cookieExists(cookieName) ) {
          self.allowedCookies.push( val )
        }

      } )
    }

    /**
     * Recreates GTM scripts based on plain text templates
     */
    self.setupGTMscripts = function() {
      self.getAllowedCookies()

      window.dataLayer = window.dataLayer || [];

      $( self.allowedCookies ).map(function(val){
        var gtmTemplate = $( '.macs_cookies_gtm_' + self.allowedCookies[val] )

        gtmTemplate.each( function(){
          if ( $(this).length && $(this).text() != '' ) {
            self.insertScriptElement( $(this).text() )
          }
        } )
      })
    }

    /**
     * Insets script element into DOM
     */
    self.insertScriptElement = function( scriptText ) {
      $el = $( '<script>', { 'text' : scriptText } )
      $el.appendTo($('body'))
    }

    /**
     * Refreshes general consent data by setting it up to current date and current policy version
     */
    self.refreshConsentData = function() {
      self.deleteCookie('macs_cookies_consent_' + MACS_COOKIES.ID);
      self.deleteCookie('macs_cookies_policy_v_' + MACS_COOKIES.ID);
      self.setCookie( 'macs_cookies_consent_' + MACS_COOKIES.ID, 1, self.config.consentLifeDays );
      self.setCookie( 'macs_cookies_policy_v_' + MACS_COOKIES.ID, MACS_COOKIES.coookiePolicyVersion, self.config.consentLifeDays );
    }

    // USER PREFERENCES METHODS

    /**
     * Binds actions to save button
     */
    self.bindPreferencesActions = function(){
      $('#macs_cookies_save_preferences').click(function(e){
        e.preventDefault()
        self.resetUserSettings();
        self.saveUserSettings();
        $('#popup-cookieConsent').hide();
        $('.cookieConsent__overlay.overlay-on').hide();
        $('.macs_cookies_saved').fadeIn(200, function(){
          setTimeout( function(){ $('.macs_cookies_saved').fadeOut(200) }, 2000 )
        })
      })
    }

    /**
     * Checks checkboxes on settings page based on existing cookies
     */
    self.checkAllowedBoxes = function() {
      self.getAllowedCookies();
      $( self.allowedCookies ).map(function(val){
        $('#accept_cookie_' + self.allowedCookies[val]).prop('checked', true)
      })
    }

    /**
     * Resets all user preferences (not the concent itself)
     */
    self.resetUserSettings = function() {
      $( self.config.cookieGroups ).each( function(i,val) {
        var cookieName = 'macs_cookies_' + val + '_' + MACS_COOKIES.ID
        self.deleteCookie(cookieName);
      })
    }

    /**
     * Saves user settings as cookies
     */
    self.saveUserSettings = function () {
      $('input[name="accept_cookies[]"]:checked').each(function(){
          self.setCookie( 'macs_cookies_' + $(this).val() + '_' + MACS_COOKIES.ID, 1, self.config.consentLifeDays )
      })

      // always set necessary when settings are saved (since the checkbox is required)
      self.setCookie( 'macs_cookies_necessary_' + MACS_COOKIES.ID, 1, self.config.consentLifeDays )

      // Set general consent cookies
      self.refreshConsentData()

      // Hide the popup
      $('#popup-cookieConsent').hide();
      $('.cookieConsent__overlay.overlay-on').hide();
      // Do the GTM switcheroo from plaintext to script.
      self.setupGTMscripts()
    }

    // POPUP

    /**
     * Initializes popup in following scenarios
     * - User did not accept cookies yet
     * - Cookie Policy version is changed since the user accepted policy
     *
     * NOTE: will also reset existing user settings in the cases described above.
     */
    self.popupInit = function() {
      if (
        self.cookieExists( 'macs_cookies_consent_' + MACS_COOKIES.ID ) &&
        self.cookieExists( 'macs_cookies_policy_v_' + MACS_COOKIES.ID ) &&
        self.getCookie( 'macs_cookies_policy_v_' + MACS_COOKIES.ID ) === MACS_COOKIES.coookiePolicyVersion )
      {
        return false;
      }

      // Reset user preferences, they're outdated anyway
      self.resetUserSettings()

      // Show popup
      $('#popup-cookieConsent').show();
      $('.cookieConsent__overlay.overlay-on').show();
      self.bindPopupActions()

      return true;
    }

    /**
     * Binds actions to user clicks
     */
    self.bindPopupActions = function() {
      $('.macs_cookies_accept_necessary').click(function(e) {
        e.preventDefault()

        // 1. Reset previous consent cookies if exist
        self.resetUserSettings()
        // 2. Set only necessary consent cookie
        self.setCookie( 'macs_cookies_necessary_' + MACS_COOKIES.ID, 1, self.config.consentLifeDays )
        // 3. Refresh consent date and cookie policy version to renew the consent lifespan
        self.refreshConsentData()
        // 4. Hide the popup
        $('#popup-cookieConsent').hide();
        $('.cookieConsent__overlay.overlay-on').hide()
        // 5. Do the GTM switcheroo from plaintext to script.
        self.setupGTMscripts()
      })

      $('.macs_cookies_accept_all').click(function(e) {
        e.preventDefault()

        // 1. Reset previous consent cookies if exist
        self.resetUserSettings()
        // 2. Set all consent cookies
        $( self.config.cookieGroups ).each( function(i,val) {
          var cookieName = 'macs_cookies_' + val + '_' + MACS_COOKIES.ID
          self.setCookie( cookieName, 1, self.config.consentLifeDays )
        })
        // 3. Refresh consent date and cookie policy version to renew the consent lifespan
        self.refreshConsentData()
        // 4. Hide the popup
        $('#popup-cookieConsent').hide();
        $('.cookieConsent__overlay.overlay-on').hide();
        // 5. Do the GTM switcheroo from plaintext to script.
        self.setupGTMscripts()
        // 6. Reflect data on settings page if it's currently viewed
        self.checkAllowedBoxes()
        // 7. Print embeds
        self.printEmbeds() // Print embeds function
      })

      // Hide overlay if cookie settings page
      var isCookieSettingsPage = document.getElementsByClassName('cookie_section--settings');
      if (isCookieSettingsPage.length > 0) {
        $('.cookieConsent__overlay.overlay-on').hide();
      }
    }


    // If cookies non exist, hide the iframe embeds
    self.blockEmbeds = function() {

      // If cookies non exist, hide the embeds
      if ( self.cookieExists( 'macs_cookies_statistics_' + MACS_COOKIES.ID ) ) {
        return true;
      }

      var frames = document.getElementsByTagName('iframe');

      if ( ! frames.length ) {
        return
      }

      //  check if are iframes
      $(frames).each(function() {
        $(this).addClass('iframe_withoutcookies')
      });

      $('.iframe_withoutcookies').each(function() {
       var src = $(this).attr('src');

       // Soundcloud iframe
      var exp = new RegExp(/(snd\.sc|soundcloud\.com)/);

        if ( exp.test(src) == true ){

          var sndbUrl = src;
          var sndbH = $(this).attr('height');
          var sndbW = $(this).attr('width');

          var fieldIdInput = $('<div />', {
            'class': 'embed_placeholder_soundcloud embed_placeholder',
            'data-url': sndbUrl,
            'data-height': sndbH,
            'data-width':sndbW,
          })

          fieldIdInput.text(MACS_COOKIES.embedCookiesSnd)
          $(this).replaceWith(fieldIdInput)

        }
      });

      $('.iframe_withoutcookies').each(function() {
        var src = $(this).attr('src');

        // Youtube iframe
        var exp = new RegExp(/(youtu\.be|youtube\.com)/);
 
        if( exp.test(src) ) {

          $(this).addClass('youtube')

            var ytbUrl = src;
            var ytbH = $(this).height();
            var ytbW = $(this).width();

            var fieldIdInput = $('<div />', {
              'class': 'embed_placeholder_youtube embed_placeholder',
              'data-url': ytbUrl,
              'data-height': ytbH,
              'data-width':ytbW,
            })

            fieldIdInput.text(MACS_COOKIES.embedCookiesYtb)
            $(this).replaceWith(fieldIdInput)

        }
      });

    }

    self.printEmbeds = function() {

      $('.embed_placeholder_youtube').each(function() {

        var url = $(this).attr('data-url');
            iframeH = $(this).attr('data-height');
            iframeW = $(this).attr('data-width');

        var fieldIdInput = $('<iframe />', {
          'class': 'embed_new_youtube',
          'src': url,
          'height': iframeH,
          'width':iframeW,
        })

        $(this).replaceWith(fieldIdInput)

      });

      $('.embed_placeholder_soundcloud').each(function() {

        var url = $(this).attr('data-url');
            iframeH = $(this).attr('data-height');
            iframeW = $(this).attr('data-width');

        var fieldIdInput = $('<iframe />', {
          'class': 'embed_new_soundcloud',
          'src': url,
          'height': iframeH,
          'width': iframeW,
        })

        $(this).replaceWith(fieldIdInput)

      });

    }

    self.trackUserConsent = function() {

      $('#macs_cookies_accept_all').click(function(e) {
        e.preventDefault()
        // add GTM to track user consent- user accepted all cookies
        if (typeof dataLayer !== 'undefined') {
          dataLayer.push({ 'event' : 'user_consent', 'consent_type': 'accept_all' })
        }
      })

      $('#macs_cookies_save_preferences').click(function(e) {
        e.preventDefault()

        $('.cookieConsent__checkbox_container').each(function() {
          if($(this).find('input').is(':checked')){
            var input = $(this).find('input').attr('id');

            if( input === 'accept_cookie_statistics' ){
              // add GTM to track user consent- user accepted statistics cookies
              if (typeof dataLayer !== 'undefined') {
                dataLayer.push({ 'event' : 'user_consent', 'consent_type': 'accept_statistics' })
              }
            }
          }
        })
      })

    }

  }
    macsCookies.init()
})
