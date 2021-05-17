<?php
/*
	Plugin Name: Google reCAPTCHA
	Plugin URI:
	Plugin Description: Provides support for Google reCAPTCHA
	Plugin Version: 0.1
	Plugin Date: 2014-12-05
	Plugin Author: kingthemes.net
	Plugin Author URI: http://kingthemes.net
	Plugin License: GPLv2
	Plugin Minimum kingmedia Version: 1.5
	Plugin Update Check URI:
*/


	if (!defined('QA_VERSION')) { // don't allow this page to be requested directly from browser
		header('Location: ../../');
		exit;
	}


	qa_register_plugin_module('captcha', 'king-google-recaptcha.php', 'qa_google_recaptcha', 'Google reCAPTCHA');


/*
	Omit PHP closing tag to help avoid accidental output
*/