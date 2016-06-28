<?php

/**
 * Add placeholders to input elements
 */
function gpbootstrap_add_placeholders_to_inputs($rendered) {
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
  	'supplemental_address_1-5' => 'Additional address',
    'city-5' => 'Suburb *',
    'postal_code-5' => 'Postcode *',
  );
  $patterns = $replacements = array();
  foreach ($inputs as $from => $to) {
    $patterns[] = '/<input [^>]* name="' . $from . '"/i';
    $replacements[] = '$0 placeholder="' . $to . '"';
  }
  $updated = preg_replace($patterns, $replacements, $rendered);

  if (isset($updated)) {
    return $updated;
  }
  else {
    return $rendered;
  }
}
