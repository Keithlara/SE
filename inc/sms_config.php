<?php
/**
 * SMS configuration.
 *
 * Default: disabled (no credentials).
 * To enable Twilio SMS, set these constants to real values.
 */
if (!defined('SMS_PROVIDER')) {
    define('SMS_PROVIDER', 'twilio'); // currently supported: 'twilio'
}

// Twilio
if (!defined('TWILIO_ACCOUNT_SID')) {
    define('TWILIO_ACCOUNT_SID', ''); // e.g. ACxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
}
if (!defined('TWILIO_AUTH_TOKEN')) {
    define('TWILIO_AUTH_TOKEN', '');
}
if (!defined('TWILIO_FROM_NUMBER')) {
    define('TWILIO_FROM_NUMBER', ''); // e.g. +15551234567
}

