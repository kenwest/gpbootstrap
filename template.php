<?php
/**
 * Prior to HTML processing
 */
function gpbootstrap_preprocess_html(&$variables, $hook) {
  /*
   * Add a P3P header so IE respects us when a page is embedded in an IFRAME
   * See http://labs.fundbox.com/third-party-cookies-with-ie-at-2am/
   */
  drupal_add_http_header('P3P', 'CP="POTATO"');
}

/**
 * Add placeholders to input elements
 */
function gpbootstrap_update_civicrm_event_registration_page($rendered) {
  /*
   * Only update CiviCRM pages.
   */
  if (stripos($rendered, 'crm-event-register-form-block') === false) {
    return $rendered;
  }

  /*
   * Add placeholders to inputs
   */
  $inputs = array (
    'first_name' => 'First name *',
    'last_name' => 'Last name *',
    'email-6' => 'Email address *',
    'phone-6-2' => 'Mobile *',
    'custom_44' => 'Additional information, eg names of guests, dietary requirements',
    'street_address-5' => 'Street address *',
  	'supplemental_address_1-5' => 'Additional address, eg level, unit, company name',
    'city-5' => 'Suburb *',
    'postal_code-5' => 'Postcode *',
  );
  $patterns = $replacements = array();
  foreach ($inputs as $from => $to) {
    $patterns[] = '/<input [^>]* name="' . $from . '"/i';
    $replacements[] = '$0 placeholder="' . $to . '"';
  }
  $updated = preg_replace($patterns, $replacements, $rendered);

  /*
   * Prepend output with a DIV which requests permission to use cookies.
   * Solves a problem where Safari only allows third-party cookies in an
   * IFRAME when that site has been previously visited (not in an IFRAME)
   * and has set cookies.
   *
   * See http://labs.fundbox.com/third-party-cookies-with-ie-at-2am/ for
   * more information.
   */
  $cookiesPermission = '
<div class="gpb-cookie-permissions gpb-cookie-hidden" id="gpb-cookie-permissions">
  <div class="gpb-cookie-description">
    Registering for this event requires the use of cookies. Please click here to accept the use of cookies.
  </div>
  <div class="gpb-cookie-button-area">
    <span class="gpb-cookie-button" id="gpb-cookie-button">
      <i class="fa-check"></i>
      <span class="gpb-cookie-accept">Accept</span>
    </span>
  </div>
  <div class="gpb-cookie-description">
    If you have technical issues with this form, you can register at the ' .
    '<a style="display: inline-block;" href="https://citybibleforum.org' .
    request_uri() .
    '"><strong>City Bible Forum</strong></a> site.
  </div>
</div>

<script type="text/javascript">' . "
CRM.$(function($) {
  function setGpbCookie(cname, cvalue) {
    document.cookie = cname + '=' + cvalue;
  }

  function getGpbCookie(cname) {
    var name = cname + '=';
    var ca = document.cookie.split(';');
    for (var i = 0; i < ca.length; i++) {
      var c = ca[i];
      while (c.charAt(0) == ' ') {
        c = c.substring(1);
      }
      if (c.indexOf(name) == 0) {
        return c.substring(name.length, c.length);
      }
    }
    return '';
  }

  test_cookie = setGpbCookie('gpb_test_cookie', 'test');
  if (getGpbCookie('gpb_test_cookie') == '') {
    $('#gpb-cookie-permissions').removeClass('gpb-cookie-hidden');

    $('#gpb-cookie-button').on('mousedown',function(e){
      $('#gpb-cookie-button').addClass('gpb-cookie-loading');
      e.preventDefault();
      window.open(
        '/sites/gpb/themes/gpbootstrap/set-cookie-then-close.html',
        '_blank',
        'width=20,height=20,left=' + screen.width + ',top=' + screen.height
      );
    });
  }
});
" . '
</script>';

  if (isset($updated)) {
    return $cookiesPermission . $updated;
  }
  else {
    return $cookiesPermission . $rendered;
  }
}
