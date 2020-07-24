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
      self.blockEmbeds()
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
        // 7. Change soundcloud src
        var isIframeExist = document.getElementsByClassName('soundcloud');
        if (isIframeExist.length > 0) {
          $('.soundcloud').each( function() {
            var newUrl = $(this).data('url');
            $(this).attr('src', newUrl );
            $(this).siblings( '.embed_placeholder' ).css('display', 'none');
          })

        }
      })

      // Hide overlay if cookie settings page
      var isCookieSettingsPage = document.getElementsByClassName('cookie_section--settings');
      if (isCookieSettingsPage.length > 0) {
        $('.cookieConsent__overlay.overlay-on').hide();
      }
    }

    // If cookies non exist, hide the embeds
    self.blockEmbeds = function() {

      // If cookies non exist, hide the embeds
      if ( self.cookieExists( 'macs_cookies_consent_' + MACS_COOKIES.ID ) ) {
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
        if(src.indexOf('https://w.soundcloud.com/player/?url=') != -1 && src.indexOf('visual=true') != -1) {

          var url_embed = $(this).attr('src');

          $(this).attr('data-url', url_embed);
          $(this).attr('src', 'about:blank');
          $(this).addClass('soundcloud')
          $( '.soundcloud' ).before( "<div class='embed_placeholder'>Please allow statistical cookies to see this embed.</div>" );
        }
      });

      $('.iframe_withoutcookies').each(function() {
        var src = $(this).attr('src');

        // Youtube iframe
        if(src.indexOf('https://www.youtube.com/embed/') != -1 ) {

          $(this).addClass('youtube')

          var regExp = /^.*(youtu\.be\/|v\/|u\/\w\/|embed\/|watch\?v=|\&v=)([^#\&\?]*).*/;
          var url = $(this).attr('src');
          var match = url.match(regExp);
          var match2 = match[2].length;

          if (match && match2 == 11) {

            var ytbID = match[2];
            url = `https://www.youtube-nocookie.com/embed/${ytbID}?autoplay=0&rel=0`

            $(this).attr('src',url); 

            return match[2];
          }
        }
      });

    }
  }

  macsCookies.init()
})
