<?php
	if (!defined('QA_VERSION')) { // don't allow this page to be requested directly from browser
		header('Location: ../');
		exit;
	}


	class qa_google_recaptcha {

		private static $_signupUrl = "https://www.google.com/recaptcha/admin";

		var $directory;

		function load_module($directory, $urltoroot)
		{
			$this->directory=$directory;
		}

		function option_default($option)
		{
			if ($option=='recaptcha_theme')
				return 'light';
			elseif ($option=='recaptcha_type')
				return 'image';
		}

		function admin_form()
		{
			$saved=false;

			if (qa_clicked('recaptcha_save_button')) {
				qa_opt('recaptcha_site_key', qa_post_text('recaptcha_site_key_field'));
				qa_opt('recaptcha_secret_key', qa_post_text('recaptcha_secret_key_field'));
				qa_opt('recaptcha_theme', qa_post_text('recaptcha_theme_field'));
				qa_opt('recaptcha_type', qa_post_text('recaptcha_type_field'));

				$saved=true;
			}

			$form=array(
				'ok' => $saved ? 'Google reCAPTCHA settings saved' : null,

				'fields' => array(
					'public' => array(
						'label' => 'Site key:',
						'value' => qa_opt('recaptcha_site_key'),
						'tags' => 'name="recaptcha_site_key_field"',
					),

					'private' => array(
						'label' => 'Secret key:',
						'value' => qa_opt('recaptcha_secret_key'),
						'tags' => 'name="recaptcha_secret_key_field"',
						'error' => $this->recaptcha_error_html(),
					),

					'theme' => array(
						'type' => 'select',
						'label' => 'Theme:',
						'tags' => 'name="recaptcha_theme_field"',
						'options' => array('dark'=>'dark', 'light'=>'light'),
						'value' => qa_opt('recaptcha_theme'),
					),

					'type' => array(
						'type' => 'select',
						'label' => 'Type:',
						'tags' => 'name="recaptcha_type_field"',
						'options' => array('audio'=>'audio', 'image'=>'image'),
						'value' => qa_opt('recaptcha_type'),
					),
				),

				'buttons' => array(
					array(
						'label' => 'Save Changes',
						'tags' => 'name="recaptcha_save_button"',
					),
				),
			);

			return $form;
		}


		function recaptcha_error_html()
		{
			if ( (!strlen(trim(qa_opt('recaptcha_site_key')))) || (!strlen(trim(qa_opt('recaptcha_secret_key')))) ) {
				require_once $this->directory.'recaptchalib.php';

				$url = self::$_signupUrl;

				return 'To use reCAPTCHA, you must <a href="'.qa_html($url).'">sign up</a> to get these keys.';
			}

			return null;
		}


		function allow_captcha()
		{
			return strlen(trim(qa_opt('recaptcha_site_key'))) && strlen(trim(qa_opt('recaptcha_secret_key')));
		}


		function form_html(&$qa_content, $error)
		{
			require_once $this->directory.'recaptchalib.php';

			$language=qa_opt('site_language');
			if (strpos('|ar|bg|ca|zh-CN|zh-TW|hr|cs|da|nl|ex-GB|en|fil|fi|fr|fr-CA|de|de-AT|de-CH|el|iw|hi|hu|id|it|ja|jo|lv|lt|no|fa|pl|pt|pt-BR|pt-PT|ro|ru|sr|sk|sl|es|es-419|sv|th|tr|uk|vi|', '|'.$language.'|')===false) // supported as of 12/2014
				$language='en';

			$qa_content['script_src'][] = 'https://www.google.com/recaptcha/api.js?hl='.$language;

			$html = '<center><div class="g-recaptcha" data-sitekey="'.qa_opt('recaptcha_site_key').'" data-theme="'.qa_opt('recaptcha_theme').'" data-type="'.qa_opt('recaptcha_type').'"></div></center>';
			return $html;
		}


		function validate_post(&$error)
		{
			if(!empty($_POST['g-recaptcha-response'])) {
				require_once $this->directory.'recaptchalib.php';

				$reCaptcha = new ReCaptcha(qa_opt('recaptcha_secret_key'));
				$resp = $reCaptcha->verifyResponse(
					$_SERVER["REMOTE_ADDR"],
					$_POST["g-recaptcha-response"]
				);
				if($resp != null && $resp->success)
					return true;

				$error=@$answer->errorCodes;
			}

			return false;
		}

	}


/*
	Omit PHP closing tag to help avoid accidental output
*/