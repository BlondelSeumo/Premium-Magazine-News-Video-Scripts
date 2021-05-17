<?php
class qa_html_theme extends qa_html_theme_base
{

	public function html()
	{
		$this->output(
			'<html lang="en-US">',
			'<!-- Powered by KingMedia -->'
		);

		$this->head();
		$this->body();

		$this->output(
			'<!-- Powered by KingMedia -->',
			'</html>'
		);
	}

	public function head()
	{
		$this->output(
			'<head>',
			'<meta http-equiv="content-type" content="' . $this->content['content_type'] . '"/>'
		);
		$this->head_title();
		$this->head_metas();
		$this->head_css();
		$this->head_custom_css();
		$this->output('<meta name="viewport" content="width=device-width, initial-scale=1.0">');
		$this->head_links();
		if ($this->template == 'question') {
			if (strlen(@$this->content['description'])) {
				$pagetitle = strlen($this->request) ? strip_tags(@$this->content['title']) : '';
				$headtitle = (strlen($pagetitle) ? ($pagetitle . '') : '');
				$img       = king_get_uploads($this->content['description']);
				$this->output('<meta property="og:url" content="' . $this->content['canonical'] . '" />');
				$this->output('<meta property="og:type" content="article" />');
				$this->output('<meta property="og:title" content="' . $headtitle . '" />');
				$this->output('<meta property="og:description" content="Click To Watch" />');
				$this->output('<meta property="og:image" content="' . qa_html($img['furl']) . '"/>');
				$this->output('<meta name="twitter:card" content="summary_large_image">');
				$this->output('<meta name="twitter:title" content="' . $headtitle . '">');
				$this->output('<meta name="twitter:description" content="' . $headtitle . '">');
				$this->output('<meta name="twitter:image" content="' . qa_html($img['furl']) . '">');
				$this->output('<meta itemprop="description" content="click to watch">');
				$this->output('<meta itemprop="image" content="' . $this->content['description'] . '">');
				$this->output('<link rel="image_src" type="image/jpeg" href="' . qa_html($img['furl']) . '" />');
			}
		}
		$this->head_lines();


		$this->head_custom();
		$this->output('</head>');
	}

	public function body()
	{
		$this->output('<BODY');
		$this->body_tags();
		$this->output('>');
		$this->body_script();
		$this->body_content();
		$this->body_footer();
		$this->king_js();
		$this->output('</BODY>');
	}
	public function king_js()
	{

		$this->output('<script src="' . $this->rooturl . 'js/main.js"></script>');

		$this->output('<script src="' . $this->rooturl . 'js/bootstrap.min.js"></script>');
		if ($this->template == 'questions' || $this->template == 'hot' || $this->template == 'search' || $this->template == 'updates' || $this->template == 'user-questions' || $this->template == 'favorites' || $this->template == 'qa' || $this->template == 'tag' || $this->template == 'type') {
			$this->output('<script src="' . $this->rooturl . 'js/jquery-ias.min.js"></script>');
			$this->output('<script src="' . $this->rooturl . 'js/masonry.pkgd.min.js"></script>');
			$this->output('<script src="' . $this->rooturl . 'js/masonry.js"></script>');
		}
		if ($this->template !== 'questions') {
			$this->output('<script src="' . $this->rooturl . 'js/single.js"></script>');
		}
	}

	public function body_content()
	{
		$q_view = @$this->content['q_view'];
		$this->body_prefix();
		$this->notices();
		$this->body_header();
		$this->header();

		$this->output('<DIV CLASS="king-nav-sub">');
		$this->nav('sub');
		if (!empty($q_view)) {
			$this->viewtop();
		}

		$this->output('</DIV>');
		$this->output('<DIV id="king-body-wrapper">');
		$this->main();
		$this->page_links();
		$this->footer();
		$this->output('</DIV>');
		$this->widgets('full', 'low');
		$this->widgets('full', 'bottom');
		$this->body_suffix();

	}

	public function body_header()
	{

		if (isset($this->content['body_header'])) {
			$this->output('<DIV class="ads">');
			$this->output_raw($this->content['body_header']);
			$this->output('</DIV>');
		}

	}

	public function head_css()
	{
		$this->output('<LINK REL="stylesheet" TYPE="text/css" HREF="' . $this->rooturl . $this->css_name() . '"/>');
		$this->output('<link rel="stylesheet" href="' . $this->rooturl . 'font-awesome/css/all.min.css" type="text/css" media="all">');

		if (isset($this->content['css_src'])) {
			foreach ($this->content['css_src'] as $css_src) {
				$this->output('<LINK REL="stylesheet" TYPE="text/css" HREF="' . $css_src . '"/>');
			}
		}

		if (!empty($this->content['notices'])) {
			$this->output(
				'<STYLE type="text/css" ><!--',
				'.king-body-js-on .king-notice {display:none;}',
				'//--></STYLE>'
			);
		}

	}

	public function head_custom_css()
	{
		if (qa_opt('show_home_description')) {
			$this->output('<STYLE type="text/css"><!--');
			$this->output('' . qa_opt('home_description') . '');
			$this->output('//--></STYLE>');
		}
	}

	public function main()
	{
		$content = $this->content;
		$q_view  = @$this->content['q_view'];
		$text2   = @$q_view['raw']['postformat'];

		$class = '';
		if ($this->template == 'question') {
			$class = ' post-page';
		} elseif ($this->template == 'questions' || $this->template == 'hot' || $this->template == 'search' || $this->template == 'updates' || $this->template == 'user-questions' || $this->template == 'favorites' || $this->template == 'qa' || $this->template == 'tag' || $this->template == 'type' || $this->template == 'users' || $this->template == 'user-following' || $this->template == 'user-follower') {
			$class = '';
		} else {
			$class = ' one-page';
		}

		$this->output('<DIV CLASS="king-main' . (@$this->content['hidden'] ? ' king-main-hidden' : '') . '' . $class . '">');
		$this->main_parts($content);
		$this->suggest_next();
		$this->output('</DIV> <!-- END king-main -->', '');

	}
	public function q_view($q_view)
	{
		$pid   = $q_view['raw']['postid'];
		$text2 = $q_view['raw']['postformat'];
		$nsfw  = $q_view['raw']['nsfw'];
		if (!empty($q_view)) {
			if ($nsfw !== null && !qa_is_logged_in()) {
				$this->output('<DIV CLASS="king-video">');
				$this->output('<span class="king-nsfw-post"><p><i class="fas fa-mask fa-2x"></i></p>' . qa_lang_html('misc/nsfw_post') . '</span>');
				$this->output('</DIV>');
			} else {
				if ($text2 == 'V') {
					$this->output('<DIV CLASS="king-video">');
					$this->q_view_main($q_view);
					$this->output('</DIV>');
				}
			}
			$this->output('<DIV CLASS="leftside">');
			$this->widgets('main', 'high');
			$this->widgets('main', 'top');
			$this->output('<DIV CLASS="king-q-view' . (@$q_view['hidden'] ? ' king-q-view-hidden' : '') . rtrim(' ' . @$q_view['classes']) . '"' . rtrim(' ' . @$q_view['tags']) . '>');
			$this->a_count($q_view);

			$this->q_view_clear();

			$this->output('<DIV CLASS="rightview">');
			$this->page_title_error();
			if ($text2 !== 'V') {
				$this->q_view_main($q_view);
			}
			$blockwordspreg = qa_get_block_words_preg();
			$this->output('<div class="post-content">' . qa_block_words_replace( $q_view['raw']['pcontent'], $blockwordspreg ) . '</div>');
			if ($text2 == 'poll') {
				$this->get_poll($pid);
			} elseif ($text2 == 'list') {
				$this->get_list($pid);
			}
			$this->post_tags($q_view, 'king-q-view');
			$this->view_count($q_view);
			$this->post_meta_when($q_view, 'meta');
			$this->output('<div class="prev-next">');
			$this->get_next_q();
			$this->get_prev_q();
			$this->output('</div>');
			$this->output('</DIV>');
			if (qa_opt('show_ad_post_below')) {
				$this->output('<div class="ad-below">');
				$this->output('' . qa_opt('ad_post_below') . '');
				$this->output('</div>');
			}
			$this->output('</DIV> <!-- END king-q-view -->', '');
			$this->socialshare();
			$this->kim($q_view);
			$this->widgets('main', 'low');
			$this->maincom();
			$this->widgets('main', 'bottom');
			$this->output('</DIV>');

			$this->output('<DIV CLASS="solyan">');
			$this->widgets('full', 'high');
			$this->sidepanel();
			$this->output('</DIV>');

		}
	}
	public function viewtop()
	{
		$q_view   = @$this->content['q_view'];
		$favorite = @$this->content['favorite'];

		if ($this->template == 'question') {
			$this->output('<DIV CLASS="share-bar">');

			if (isset($q_view['main_form_tags'])) {
				$this->output('<FORM ' . $q_view['main_form_tags'] . '>');
			}

			$this->voting($q_view);
			if (isset($q_view['main_form_tags'])) {
				$this->form_hidden_elements(@$q_view['voting_form_hidden']);
				$this->output('</FORM>');
			}
			if (isset($favorite)) {
				$this->output('<FORM ' . $favorite['form_tags'] . '>');
			}

			$this->favorite();
			if (isset($favorite)) {
				$this->form_hidden_elements(@$favorite['form_hidden']);
				$this->output('</FORM>');
			}

			$this->output('<div class="share-link" data-toggle="modal" data-target="#sharemodal" role="button" ><i data-toggle="tooltip" data-placement="top" class="fas fa-share" title="' . qa_lang_html('misc/king_share') . '"></i></div>');
			$this->q_view_buttons($q_view);

			$this->output('</DIV>');

		}

	}

	public function maincom()
	{

		$content = $this->content;

		/*if (isset($content['main_form_tags']))
		$this->output('<FORM '.$content['main_form_tags'].'>');*/

		if ($this->template == 'question') {
			$this->output('<DIV CLASS="maincom">');
			$this->output('<ul class="nav nav-tabs">');
			if (qa_opt('allow_close_questions')) {
				$this->output('<li class="active"><a href="#comments" data-toggle="tab"><i class="far fa-comment-alt"></i> ' . qa_lang_html('misc/postcomments') . '</a></li>');
				$active = '';
			} else {
				$active = 'active';
			}
			if (qa_opt('follow_on_as')) {
				$this->output('<li><a href="#fbcomments" data-toggle="tab"><i class="fab fa-facebook"></i> ' . qa_lang_html('misc/postcomments') . '</a></li>');
			}
			$this->output('</ul>');

			$this->output('<div class="tab-content">');
			if (qa_opt('allow_close_questions')) {
				$this->output('<div class="tab-pane active" id="comments">');
				$this->main_partsc($content);
				$this->output('</div>');
			}
			if (qa_opt('follow_on_as')) {
				$this->output('<div class="tab-pane ' . $active . '" id="fbcomments">');
				$this->fbyorum();
				$this->output('</div>');
			}
			$this->output('</div>');
			$this->output('</div>');
		}
		/*if (isset($content['main_form_tags']))
	$this->output('</FORM>');*/

	}
	public function main_partsc($content)
	{
		foreach ($content as $key => $part) {
			$this->main_partc($key, $part);
		}

	}

	public function main_partc($key, $part)
	{
		$partdiv = (
			(strpos($key, 'custom') === 0) ||
			(strpos($key, 'a_form') === 0) ||
			(strpos($key, 'a_list') === 0)

		);

		if ($partdiv) {
			$this->output('<div class="king-part-' . strtr($key, '_', '-') . '">');
		}
		// to help target CSS to page parts

		if (strpos($key, 'custom') === 0) {
			$this->output_raw($part);
		} elseif (strpos($key, 'a_list') === 0) {
			$this->a_list($part);
		} elseif (strpos($key, 'a_form') === 0) {
			$this->a_form($part);
		}

		if ($partdiv) {
			$this->output('</div>');
		}

	}

	public function main_part($key, $part)
	{
		$partdiv = (
			(strpos($key, 'custom') === 0) ||
			(strpos($key, 'form') === 0) ||
			(strpos($key, 'q_list') === 0) ||
			(strpos($key, 'q_view') === 0) ||
			(strpos($key, 'ranking') === 0) ||
			(strpos($key, 'message_list') === 0) ||
			(strpos($key, 'nav_list') === 0)
		);

		if ($partdiv) {
			$this->output('<div class="king-part-' . strtr($key, '_', '-') . '">');
		}
		// to help target CSS to page parts

		if (strpos($key, 'custom') === 0) {
			$this->output_raw($part);
		} elseif (strpos($key, 'form') === 0) {
			$this->form($part);
		} elseif (strpos($key, 'q_list') === 0) {
			$this->q_list_and_form($part);
		} elseif (strpos($key, 'q_view') === 0) {
			$this->q_view($part);
		} elseif (strpos($key, 'ranking') === 0) {
			$this->ranking($part);
		} elseif (strpos($key, 'message_list') === 0) {
			$this->message_list_and_form($part);
		} elseif (strpos($key, 'nav_list') === 0) {
			$this->part_title($part);
			$this->nav_list($part['nav'], $part['type'], 1);
		}

		if ($partdiv) {
			$this->output('</div>');
		}

	}

	public function nav_user_search()
	{
		$this->search();
	}

	public function nav_main_sub()
	{
		$this->output('<DIV CLASS="king-nav-main">');
		$this->nav('main');
		$this->navuser();
		$this->output('</DIV>');
	}

	public function navuser()
	{
		$this->output('<ul>');
		if (qa_is_logged_in()) {

			$this->output('<li class="king-nav-main-item king-nav-acc">');
			$this->output('<a href="' . qa_path_html('account') . '" class="king-nav-main-link" ><i class="fas fa-user-circle"></i>' . qa_lang_html('main/nav_account') . '</a>');
			$this->output('</li>');
			$this->output('<li class="king-nav-main-item king-nav-fav">');
			$this->output('<a href="' . qa_path_html('favorites') . '" class="king-nav-main-link" ><i class="fas fa-heart"></i>' . qa_lang_html('main/nav_updates') . '</a>');
			$this->output('</li>');
			$this->output('<li class="king-nav-main-item">');
			$this->output('<a class="king-nav-main-link" href="' . qa_path_html('logout') . '"><i class="fas fa-sign-out-alt"></i>' . qa_lang_html('main/nav_logout') . '</a>');
			$this->output('</li>');
		} else {
			$this->output('<li class="king-nav-main-item">');
			$this->output('<a class="king-nav-main-link" href="' . qa_path_html('login') . '"><i class="fas fa-sign-in-alt"></i>' . qa_lang_html('main/nav_login') . '</a>');
			$this->output('</li>');
			$this->output('<li class="king-nav-main-item">');
			$this->output('<a class="king-nav-main-link" href="' . qa_path_html('register') . '"><i class="fas fa-user-plus"></i>' . qa_lang_html('main/nav_register') . '</a>');
			$this->output('</li>');
		}
		$this->output('</ul>');

	}

	public function header()
	{

		$this->output('<header CLASS="king-headerf">');
		$this->output('<DIV CLASS="king-header">');
		$this->output('<div class="king-left-toggle" data-toggle="dropdown" data-target=".leftmenu" aria-expanded="false" role="button"><span class="left-toggle-line"></span></div>');
		$this->logo();

		$this->header_right();

		$this->output('</DIV>');

		$this->nav_user_search();
		if (qa_using_categories()) {
			$this->king_cats();
		}
		$this->output('</header>');
		if ($this->template == 'hot' || $this->template == 'updates' || $this->template == 'tags' || $this->template == 'users' || $this->template == 'admin' || $this->template == 'search' || $this->template == 'categories' || $this->template == 'ask' || $this->template == 'video' || $this->template == 'news' || $this->template == 'poll' || $this->template == 'list' || $this->template == 'qa') {
			$this->output('<div class="head-title">');
			$this->title();
			$this->output('</div>');
		}
		$this->output('<div class="leftmenu">');
		$this->output('<button type="button" class="king-left-close" data-dismiss="modal" aria-label="Close"><i class="far fa-times-circle"></i></button>');
		$this->nav_main_sub();
		$this->output('</div>');

		$this->widgets('full', 'top');
		if ($this->template == 'user-following' || $this->template == 'user-follower' || $this->template == 'user' || $this->template == 'user-wall' || $this->template == 'user-questions' || $this->template == 'account' || $this->template == 'favorites') {
			$this->profile_page();
		}

		if (isset($this->content['error'])) {
			$this->error(@$this->content['error']);
		}

	}
	public function header_right()
	{
		$this->output('<DIV CLASS="header-right">');
		$this->output('<ul>');
		if (!qa_is_logged_in()) {
			$this->output('<li>');
			$this->output('<a class="reglink" href="' . qa_path_html('register') . '">' . qa_lang_html('main/nav_register') . '</a>');
			$this->output('</li>');
			$this->output('<li>');
			$this->output('<div class="reglink" data-toggle="modal" data-target="#loginmodal" role="button" title="' . qa_lang_html('main/nav_login') . '">' . qa_lang_html('main/nav_login') . '</div>');
			$this->output('</li>');
		} else {
			$this->userpanel();
		}

		if ((qa_user_maximum_permit_error('permit_post_q') != 'level')) {
			$this->kingsubmit();
		}
		$this->output('<li class="search-button"><span data-toggle="dropdown" data-target=".king-search" aria-expanded="false" class="search-toggle"><i class="fas fa-search fa-lg"></i></span></li>');
		if (qa_using_categories()) {
			$this->output('<li class="cats-button"><span data-toggle="dropdown" data-target=".king-cat-main" aria-expanded="false" class="cats-toggle"><i class="fas fa-ellipsis-h"></i></span></li>');
		}
		$this->output('</ul>');
		$this->output('</DIV>');
	}
	public function king_cats()
	{
		$this->output('<div class="king-cat-main">');
		$this->output('<a href="' . qa_path_html('categories') . '" class="king-cat-link">' . qa_lang_html('main/nav_categories') . '</a>');
		$this->output('<div class="king-cat">');
		$categories                         = qa_db_single_select(qa_db_category_nav_selectspec(null, true));
		$this->content['navigation']['cat'] = qa_category_navigation($categories);
		$this->nav('cat', 4);
		$this->output('</div>');
		$this->output('</div>');
	}

	public function profile_page()
	{
		$handle = qa_request_part(1);
		if (!strlen($handle)) {
			$handle = qa_get_logged_in_handle();
		}

		$user = qa_db_select_with_pending(
			qa_db_user_account_selectspec($handle, false)
		);

		$this->output(get_user_html($user, '600', 'king-profile'));

	}

	public function favorite2()
	{
		$favorite = isset($this->content['favorite']) ? $this->content['favorite'] : null;
		if (isset($favorite)) {
			$favoritetags = isset($favorite['favorite_tags']) ? $favorite['favorite_tags'] : '';
			$this->output('<span class="king-following" ' . $favoritetags . '>');
			$this->favorite_inner_html2($favorite);
			$this->output('</span>');
		}
	}

	public function title()
	{
		if (isset($this->content['title'])) {
			$this->output($this->content['title']);
		}

	}

	public function favorite_inner_html2($favorite)
	{
		$this->favorite_button(@$favorite['favorite_add_tags'], 'king-favorite');
		$this->favorite_button(@$favorite['favorite_remove_tags'], 'king-unfavorite');
	}

	public function favorite_button($tags, $class)
	{
		if (isset($tags)) {
			if ($class == 'king-favorite') {
				$follow = qa_lang_html('main/nav_follow');
			} else {
				$follow = qa_lang_html('main/nav_unfollow');
			}
			$this->output('<button ' . $tags . ' type="submit" value="' . $follow . '" class="' . $class . '-button"><i class="far fa-heart"></i></button>');
		}
	}

	public function kingsubmit()
	{
		if (!qa_opt('disable_image') || !qa_opt('disable_video') || !qa_opt('disable_news') || !qa_opt('disable_poll') || !qa_opt('disable_list')) {
			$this->output('<li>');
			$this->output('<div class="king-submit">');

			$this->output('<span class="kingadd" data-toggle="dropdown" data-target=".king-submit" aria-expanded="false" role="button"><i class="fa fa-plus fa-lg" aria-hidden="true"></i></span>');
			$this->output('<div class="king-dropdown2">');
			$this->output('<div class="arrow"></div>');
			if (!qa_opt('disable_image')) {
				$this->output('<a href="' . qa_path_html('ask') . '" class="kingaddimg"><i class="fas fa-image"></i> ' . qa_lang_html('main/home_image') . '</a>');
			}
			if (!qa_opt('disable_video')) {
				$this->output('<a href="' . qa_path_html('video') . '" class="kingaddvideo"><i class="fas fa-video"></i> ' . qa_lang_html('main/home_video') . '</a>');
			}
			if (!qa_opt('disable_news')) {
				$this->output('<a href="' . qa_path_html('news') . '" class="kingaddnews"><i class="fas fa-newspaper"></i> ' . qa_lang_html('main/home_news') . '</a>');
			}
			if (!qa_opt('disable_poll')) {
				$this->output('<a href="' . qa_path_html('poll') . '" class="kingaddpoll"><i class="fas fa-align-left"></i> ' . qa_lang_html('misc/king_poll') . '</a>');
			}
			if (!qa_opt('disable_list')) {
				$this->output('<a href="' . qa_path_html('list') . '" class="kingaddlist"><i class="fas fa-bars"></i> ' . qa_lang_html('misc/king_list') . '</a>');
			}
			$this->output('</div>');
			$this->output('</div>');
			$this->output('</li>');
		}
	}

	public function userpanel()
	{
		$handle = qa_get_logged_in_handle();
		$user   = qa_db_select_with_pending(
			qa_db_user_account_selectspec($handle, false)
		);

		$this->output('<li>');
		$this->output('<div class="king-havatar" data-toggle="dropdown" data-target=".king-dropdown" aria-expanded="false" >');
		if ($user['avatarblobid']) {
			$this->output('<img src="' . get_avatar($user['avatarblobid'], 60) . '" alt=""/>');
		}
		$this->output('</div>');
		$this->output('<div class="king-dropdown">');
		$this->output('<div class="arrow"></div>');
		$this->useravatar();
		$this->nav('user');
		$this->output('</div>');
		$this->output('</li>');

	}

	public function userpanel2()
	{
		if (!qa_is_logged_in()) {
			$login = @$this->content['navigation']['user']['login'];
			$this->output('<div id="loginmodal" class="king-modal-login">');
			$this->output('<div class="king-modal-content">');
			$this->output('<button type="button" class="king-modal-close" data-dismiss="modal" aria-label="Close"><i class="icon fa fa-fw fa-times"></i></button>');
			$this->output('<div class="king-modal-header"><h4 class="modal-title">Login</h4></div>');
			$this->output('<div class="king-modal-form">');
			$this->output('<form action="' . qa_path_html('login') . '" method="post">
				<input type="text" id="king-userid" name="emailhandle" placeholder="' . trim(qa_lang_html('users/email_handle_label'), ':') . '" />
				<input type="password" id="king-password" name="password" placeholder="' . trim(qa_lang_html('users/password_label'), ':') . '" />
				<div id="king-rememberbox"><input type="checkbox" name="remember" id="king-rememberme" value="1"/>
				<label for="king-rememberme" id="king-remember">' . qa_lang_html('users/remember') . '</label></div>
				<input type="hidden" name="code" value="' . qa_html(qa_get_form_security_code('login')) . '"/>
				<input type="submit" value="Sign in" id="king-login" name="dologin" />
				</form>');
			$this->output('</div>');
			$this->output('<div class="king-modal-footer">');
			$this->nav('user');
			$this->output('</div>');
			$this->output('</div>');
			$this->output('</div>');
		}

	}

	public function form_text_multi_row($field, $style)
	{
		$this->output('<TEXTAREA ' . @$field['tags'] . ' ROWS="5" COLS="40" CLASS="king-form-' . $style . '-text">' . @$field['value'] . '</TEXTAREA>');
	}

	public function q_list_and_form($q_list)
	{
		if (!empty($q_list)) {
			$this->part_title($q_list);
			$this->q_list($q_list);

		}
	}

	public function king_cat()
	{
		if (qa_using_categories()) {
			$this->output('<div class="king-cat-main">');
			$this->output('<ul><li>');
			$this->output('<a href="' . qa_path_html('categories') . '" class="king-cat-link">' . qa_lang_html('main/nav_categories') . '</a>');
			$this->output('<div class="king-cat">');
			$categories                         = qa_db_single_select(qa_db_category_nav_selectspec(null, true));
			$this->content['navigation']['cat'] = qa_category_navigation($categories);
			$this->nav('cat', 4);
			$this->output('</div>');
			$this->output('</li></ul>');
			$this->output('</div>');
		}
	}

	public function q_list($q_list)
	{
		if (isset($q_list['qs'])) {

			$this->q_list_items($q_list['qs']);

		}
	}

	public function q_list_items($q_items)
	{
		if ($this->template == 'question') {
			foreach ($q_items as $q_item) {
				$this->king_related($q_item);
			}
		} else {
			$this->output('<div class="container">');
			$this->output('<div class="grid-sizer"></div>');
			foreach ($q_items as $q_item) {
				$this->q_list_item($q_item);
			}
			$this->output('</div>');
		}
	}

	public function king_related($q_item)
	{
		$this->output('<div class="king-related">');
		$this->q_item_content($q_item);
		$this->q_item_title($q_item);
		$this->output('</div>');
	}

	public function q_list_item($q_item)
	{
		$text2      = $q_item['raw']['postformat'];
		$postformat = '';
		$postc      = '';
		if ($text2 == 'V') {
			$postformat = '<a class="king-post-format" href="' . qa_path_html('type') . '" data-toggle="tooltip" data-placement="right" title="' . qa_lang_html('main/home_video') . '"><i class="fas fa-video"></i></a>';
			$postc      = ' king-class-video';
		} elseif ($text2 == 'I') {
			$postformat = '<a class="king-post-format" href="' . qa_path_html('type', array('by' => 'images')) . '" data-toggle="tooltip" data-placement="right" title="' . qa_lang_html('main/home_image') . '"><i class="fas fa-image"></i></a>';
			$postc      = ' king-class-image';
		} elseif ($text2 == 'N') {
			$postformat = '<a class="king-post-format" href="' . qa_path_html('type', array('by' => 'news')) . '" data-toggle="tooltip" data-placement="right" title="' . qa_lang_html('main/home_news') . '"><i class="fas fa-newspaper"></i></a>';
			$postc      = ' king-class-news';
		} elseif ($text2 == 'poll') {
			$postformat = '<a class="king-post-format" href="' . qa_path_html('type', array('by' => 'poll')) . '" data-toggle="tooltip" data-placement="right" title="' . qa_lang_html('misc/king_poll') . '"><i class="fas fa-align-left"></i></a>';
		} elseif ($text2 == 'list') {
			$postformat = '<a class="king-post-format" href="' . qa_path_html('type', array('by' => 'list')) . '" data-toggle="tooltip" data-placement="right" title="' . qa_lang_html('misc/king_list') . '"><i class="fas fa-bars"></i></a>';
		}

		$this->output('<div class="box king-q-list-item' . rtrim(' ' . @$q_item['classes']) . '' . $postc . '" ' . @$q_item['tags'] . '>');
		$this->output('<div class="king-post-upbtn">');
		$this->output('' . $postformat . '');
		$this->output('<a href="' . $q_item['url'] . '" class="ajax-popup-link magnefic-button" data-toggle="tooltip" data-placement="right" title="' . qa_lang_html('misc/king_qview') . '"><i class="fas fa-chevron-up"></i></a>');
		$this->output('<a href="' . $q_item['url'] . '" class="ajax-popup-share magnefic-button" data-toggle="tooltip" data-placement="right" title="' . qa_lang_html('misc/king_share') . '"><i class="fas fa-share-alt"></i></a>');
		$this->output('</div>');
		$this->q_item_main($q_item);
		$this->output('</div>');
	}

	public function q_item_stats($q_item)
	{
		$this->output('<DIV CLASS="king-q-item-stats">');

		$this->voting($q_item);

		$this->output('</DIV>');
	}

	public function q_item_main($q_item)
	{
		$this->output('<div class="king-q-item-main">');
		$this->q_item_content($q_item);
		$this->output('<DIV CLASS="yoriz">');
		$this->q_item_title($q_item);
		$this->output('<DIV CLASS="yorizbottom">');
		$this->a_count($q_item);
		$this->view_count($q_item);
		$this->voting2($q_item);
		$this->q_item_buttons($q_item);
		$this->output('</DIV>');
		$this->output('</DIV>');
		$this->output('</div>');
	}

	public function voting2($post)
	{
		if (isset($post['vote_view'])) {
			$this->vote_count($post);
		}
	}

	public function q_item_content($q_item)
	{

		$text = $q_item['raw']['content'];
		$nsfw = $q_item['raw']['nsfw'];
		if ($nsfw !== null && !qa_is_logged_in()) {
			$this->output('<a href="' . $q_item['url'] . '" class="item-a"><span class="king-nsfw-post"><p><i class="fas fa-mask fa-2x"></i></p>' . qa_lang_html('misc/nsfw_post') . '</span></a>');
		} elseif (!empty($text)) {
			$text2 = king_get_uploads($text);
			$this->output('<A class="item-a" HREF="' . $q_item['url'] . '">');
			if ($text2) {
				$this->output_raw('<span class="post-featured-img"><img class="item-img" width="' . $text2['width'] . '" height="' . $text2['height'] . '" src="' . $text2['furl'] . '" alt=""/></span>');
			} else {
				$this->output_raw('<span class="post-featured-img"><img class="item-img" src="' . $text . '" alt=""/></span>');
			}

			$this->output('</A>');
		} else {
			$this->output('<a href="' . $q_item['url'] . '" class="king-nothumb"></a>');
		}

	}

	public function q_item_title($q_item)
	{

		$this->output('<DIV CLASS="king-q-item-title">');
		$this->post_meta_where($q_item, 'metah');
		$this->output('<A HREF="' . $q_item['url'] . '"><h3>' . $q_item['title'] . '</h3></A>');
		if (isset($q_item['avatar'])) {
			$this->output('<div class="king-p-who">');
			$this->output('' . $q_item['avatar'] . $q_item['who']['data'] . '');
			$this->output('</div>');
		}
		$this->output('</DIV>');

	}

	public function sidepanel()
	{
		$this->output('<div class="king-sidepanel">');
		$this->widgets('side', 'top');
		$this->sidebar();
		$this->widgets('side', 'high');
		$this->nav('cat', 1);
		$this->widgets('side', 'low');
		$this->output_raw(@$this->content['sidepanel']);
		$this->feed();
		$this->widgets('side', 'bottom');
		$this->output('</div>', '');
	}

	public function nav($navtype, $level = null)
	{
		$navigation = @$this->content['navigation'][$navtype];

		if (($navtype == 'user') || isset($navigation)) {

			if ($navtype == 'user')

			// reverse order of 'opposite' items since they float right
			{
				foreach (array_reverse($navigation, true) as $key => $navlink) {
					if (@$navlink['opposite']) {
						unset($navigation[$key]);
						$navigation[$key] = $navlink;
					}
				}
			}

			$this->set_context('nav_type', $navtype);
			$this->nav_list($navigation, 'nav-' . $navtype, $level);
			$this->nav_clear($navtype);
			$this->clear_context('nav_type');

		}
	}

	public function useravatar()
	{
		$handle = qa_get_logged_in_handle();
		$user   = qa_db_select_with_pending(
			qa_db_user_account_selectspec($handle, false)
		);
		$this->output('<DIV CLASS="usrname">');

		$this->logged_in();
		$this->output('</DIV>');
	}

	public function logged_in() // adds points count after logged in username

	{
		qa_html_theme_base::logged_in();

		if (qa_is_logged_in()) {
			$userpoints = qa_get_logged_in_points();

			$pointshtml = ($userpoints == 1)
			? qa_lang_html_sub('main/1_point', '1', '1')
			: qa_lang_html_sub('main/x_points', qa_html(number_format($userpoints)));

			$this->output(
				'<SPAN CLASS="king-logged-in-points">',
				'' . $pointshtml . '',
				'</SPAN>'
			);
		}
	}

	public function q_view_main($q_view)
	{

		$this->q_view_extra($q_view);

	}

	public function q_view_extra($q_view)
	{
		if (!empty($q_view['extra'])) {
			require_once QA_INCLUDE_DIR . 'king-app/video.php';
			$extraz = $q_view['extra']['content'];
			$extras = @unserialize($extraz);
			if ($extras) {

				foreach ($extras as $extra) {
					$text2 = king_get_uploads($extra);
					$this->output('<img class="item-img" width="' . $text2['width'] . '" height="' . $text2['height'] . '" src="' . $text2['furl'] . '" alt=""/>');
				}

			} elseif (is_numeric($extraz)) {
				$vidurl = king_get_uploads($extraz);
				$thumb  = $this->content['description'];
				$poster = king_get_uploads($thumb);

				$this->output('<video id="my-video" class="video-js vjs-theme-forest" controls preload="auto"  width="960" height="540" data-setup="{}" poster="' . $poster['furl'] . '" >');
				$this->output('<source src="' . $vidurl['furl'] . '" type="video/mp4" />');
				$this->output('</video>');

			} else {
				if (!empty($q_view['extra'])) {
					$this->output_raw($extraz = embed_replace($extraz));
				}
			}
		}
	}

	public function fbyorum()
	{
		$this->output('<DIV CLASS="fbyorum">');
		$this->output('<div class="fb-comments" data-href="' . qa_path_html(qa_q_request($this->content['q_view']['raw']['postid'], $this->content['q_view']['raw']['title']), null, qa_opt('site_url')) . '" data-numposts="14" data-width="100%" ></div>');
		$this->output('</DIV>');
	}

	public function page_title_error()
	{
		$this->output('<DIV CLASS="baslik">');
		$this->output('<H1>');
		$this->title();
		$this->output('</H1>');
		$this->output('</DIV>');
	}

	public function q_view_buttons($q_view)
	{
		if (isset($q_view['main_form_tags'])) {
			$this->output('<DIV CLASS="king-q-view-buttons">');
			$this->output('<FORM ' . $q_view['main_form_tags'] . '>');
		}
		if (!empty($q_view['form'])) {
			$this->form($q_view['form']);
		}
		if (isset($q_view['main_form_tags'])) {
			$this->form_hidden_elements(@$q_view['buttons_form_hidden']);
			$this->output('</FORM>');
			$this->output('</DIV>');
		}
	}

	public function kim($q_view)
	{
		$this->output('<DIV CLASS="kim">');
		$user = qa_db_select_with_pending(
			qa_db_user_account_selectspec($q_view['raw']['userid'], true)
		);

		$this->output(get_user_html($user, '600'));

		$this->output('</DIV>');
	}

	public function socialshare()
	{
		$pagetitle = strlen($this->request) ? strip_tags(@$this->content['title']) : '';
		$headtitle = (strlen($pagetitle) ? ($pagetitle) : '');
		$shareurl  = qa_path_html(qa_q_request($this->content['q_view']['raw']['postid'], $this->content['q_view']['raw']['title']), null, qa_opt('site_url'));
		$text2     = king_get_uploads($this->content['q_view']['raw']['content']);
		$this->output('<div id="sharemodal" class="king-modal-login">');
		$this->output('<div class="king-modal-content">');
		$this->output('<div class="social-share">');
		$this->output('<h3>' . qa_lang_html('misc/king_share') . '</h3>');
		$this->output('<a class="post-share share-fb" data-toggle="tooltip" data-placement="top" title="Facebook" href="#" target="_blank" rel="nofollow" onclick="window.open(\'https://www.facebook.com/sharer/sharer.php?u=' . $shareurl . '\',\'facebook-share-dialog\',\'width=626,height=436\');return false;"><i class="fab fa-facebook-square"></i></i></a>');
		$this->output('<a class="social-icon share-tw" href="#" data-toggle="tooltip" data-placement="top" title="Twitter" rel="nofollow" target="_blank" onclick="window.open(\'http://twitter.com/share?text=' . $headtitle . '&amp;url=' . $shareurl . '\',\'twitter-share-dialog\',\'width=626,height=436\');return false;"><i class="fab fa-twitter"></i></a>');
		$this->output('<a class="social-icon share-pin" href="#" data-toggle="tooltip" data-placement="top" title="Pin this" rel="nofollow" target="_blank" onclick="window.open(\'//pinterest.com/pin/create/button/?url=' . $shareurl . '&amp;description=' . $headtitle . '\',\'pin-share-dialog\',\'width=626,height=436\');return false;"><i class="fab fa-pinterest-square"></i></a>');
		$this->output('<a class="social-icon share-em" href="mailto:?subject=' . $headtitle . '&amp;body=' . $shareurl . '" data-toggle="tooltip" data-placement="top" title="Email this"><i class="fas fa-envelope"></i></a>');
		$this->output('<a class="social-icon share-tb" href="#" data-toggle="tooltip" data-placement="top" title="Tumblr" rel="nofollow" target="_blank" onclick="window.open( \'http://www.tumblr.com/share/link?url=' . $shareurl . '&amp;name=' . $headtitle . '\',\'tumblr-share-dialog\',\'width=626,height=436\' );return false;"><i class="fab fa-tumblr-square"></i></a>');
		$this->output('<a class="social-icon share-linkedin" href="#" data-toggle="tooltip" data-placement="top" title="LinkedIn" rel="nofollow" target="_blank" onclick="window.open( \'http://www.linkedin.com/shareArticle?mini=true&amp;url=' . $shareurl . '&amp;title=' . $headtitle . '&amp;source=' . $headtitle . '\',\'linkedin-share-dialog\',\'width=626,height=436\');return false;"><i class="fab fa-linkedin"></i></a>');
		$this->output('<a class="social-icon share-vk" href="#" data-toggle="tooltip" data-placement="top" title="Vk" rel="nofollow" target="_blank" onclick="window.open(\'http://vkontakte.ru/share.php?url=' . $shareurl . '\',\'vk-share-dialog\',\'width=626,height=436\');return false;"><i class="fab fa-vk"></i></a>');
		$this->output('<a class="social-icon share-wapp" href="whatsapp://send?text=' . $shareurl . '" data-action="share/whatsapp/share" data-toggle="tooltip" data-placement="top" title="whatsapp"><i class="fab fa-whatsapp-square"></i></a>');
		$this->output('<h3>' . qa_lang_html('misc/copy_link') . '</h3>');
		$this->output('<input type="text" id="modal-url" value="' . $shareurl . '">');
		$this->output('<span class="copied" style="display: none;">' . qa_lang_html('misc/copied') . '</span>');
		$this->output('</div>');
		$this->output('</div>');
		$this->output('</div>');
	}

	public function a_list_item($a_item)
	{
		$extraclass = @$a_item['classes'] . ($a_item['hidden'] ? ' king-a-list-item-hidden' : ($a_item['selected'] ? ' king-a-list-item-selected' : ''));

		$this->output('<DIV CLASS="king-a-list-item ' . $extraclass . '" ' . @$a_item['tags'] . '>');

		$this->a_item_main($a_item);
		$this->a_item_clear();

		$this->output('</DIV> <!-- END king-a-list-item -->', '');
	}

	public function a_item_main($a_item)
	{
		$this->output('<div class="king-a-item-main">');

		$this->output('<DIV CLASS="commentmain">');

		if ($a_item['hidden']) {
			$this->output('<DIV CLASS="king-a-item-hidden">');
		} elseif ($a_item['selected']) {
			$this->output('<DIV CLASS="king-a-item-selected">');
		}

		$this->error(@$a_item['error']);
		$this->output('<DIV CLASS="a-top">');
		$this->post_avatar_meta($a_item, 'king-a-item');

		$this->post_meta_who($a_item, 'meta');
		$this->a_item_content($a_item);
		$this->output('</DIV>');

		$this->output('<DIV CLASS="a-alt">');
		$this->a_selection($a_item);
		if (isset($a_item['main_form_tags'])) {
			$this->output('<form ' . $a_item['main_form_tags'] . '>');
		}
		// form for voting buttons

		$this->voting($a_item);

		if (isset($a_item['main_form_tags'])) {
			$this->form_hidden_elements(@$a_item['voting_form_hidden']);
			$this->output('</form>');
		}
		if (isset($a_item['main_form_tags'])) {
			$this->output('<form ' . $a_item['main_form_tags'] . '>');
		}
		// form for buttons on answer

		$this->a_item_buttons($a_item);
		if (isset($a_item['main_form_tags'])) {
			$this->form_hidden_elements(@$a_item['buttons_form_hidden']);
			$this->output('</FORM>');
		}
		$this->post_meta_when($a_item, 'meta');
		$this->output('</DIV>');

		$this->output('</DIV>');

		if ($a_item['hidden'] || $a_item['selected']) {
			$this->output('</DIV>');
		}

		if (isset($a_item['main_form_tags'])) {
			$this->output('<FORM ' . $a_item['main_form_tags'] . '>');
		}
		// form for buttons on answer
		$this->c_list(@$a_item['c_list'], 'king-a-item');
		if (isset($a_item['main_form_tags'])) {
			$this->form_hidden_elements(@$a_item['buttons_form_hidden']);
			$this->output('</FORM>');
		}
		$this->c_form(@$a_item['c_form']);

		$this->output('</DIV> <!-- END king-a-item-main -->');
	}

	public function a_item_buttons($a_item)
	{

		if (!empty($a_item['form'])) {
			$this->output('<DIV CLASS="king-a-item-buttons">');
			$this->form($a_item['form']);
			$this->output('</DIV>');
		}
	}

	public function post_avatar_meta($post, $class, $avatarprefix = null, $metaprefix = null, $metaseparator = '<br/>')
	{
		$this->output('<span class="' . $class . '-avatar-meta">');
		$this->post_avatar($post, $class, $avatarprefix);
		$this->output('</span>');
	}

	public function post_meta($post, $class, $prefix = null, $separator = '<BR/>')
	{
		$this->output('<SPAN CLASS="' . $class . '-meta">');

		if (isset($prefix)) {
			$this->output($prefix);
		}

		$order = explode('^', @$post['meta_order']);

		foreach ($order as $element) {
			switch ($element) {
				case 'who':
					$this->post_meta_who($post, $class);
					break;

				case 'when':
					$this->post_meta_when($post, $class);
					break;

			}
		}

		$this->post_meta_flags($post, $class);

		$this->output('</SPAN>');
	}

	public function post_meta_who($post, $class)
	{
		if (isset($post['who'])) {
			$this->output('<SPAN CLASS="' . $class . '-who">');

			if (strlen(@$post['who']['prefix'])) {
				$this->output('<SPAN CLASS="' . $class . '-who-pad">' . $post['who']['prefix'] . '</SPAN>');
			}

			if (isset($post['who']['data'])) {
				$this->output('<SPAN CLASS="' . $class . '-who-data">' . $post['who']['data'] . '</SPAN>');
			}

			if (isset($post['who']['title'])) {
				$this->output('<SPAN CLASS="' . $class . '-who-title">' . $post['who']['title'] . '</SPAN>');
			}

			// You can also use $post['level'] to get the author's privilege level (as a string)

			if (isset($post['who']['points'])) {
				$post['who']['points']['prefix'] = '' . $post['who']['points']['prefix'];
				$post['who']['points']['suffix'] .= '';
				$this->output_split($post['who']['points'], $class . '-who-points');
			}

			if (strlen(@$post['who']['suffix'])) {
				$this->output('<SPAN CLASS="' . $class . '-who-pad">' . $post['who']['suffix'] . '</SPAN>');
			}

			$this->output('</SPAN>');
		}
	}
	public function post_meta_when($post, $class)
	{
		$this->output_split(@$post['when'], $class . '-when');
	}

	public function c_item_main($c_item)
	{
		$this->error(@$c_item['error']);
		$this->post_avatar_meta($c_item, 'king-c-item');
		$this->post_meta_who($c_item, 'meta');
		if (isset($c_item['expand_tags'])) {
			$this->c_item_expand($c_item);
		} elseif (isset($c_item['url'])) {
			$this->c_item_link($c_item);
		} else {
			$this->c_item_content($c_item);
		}

		$this->output('<DIV CLASS="king-c-item-footer">');
		$this->c_item_buttons($c_item);
		$this->post_meta_when($c_item, 'meta');
		$this->output('</DIV>');
	}

	public function voting_inner_html($post)
	{
		$this->vote_buttonsup($post);
		$this->vote_count($post);
		$this->vote_buttonsdown($post);
	}

	public function vote_buttonsup($post)
	{
		$this->output('<DIV CLASS="' . (($post['vote_view'] == 'updown') ? 'king-vote-buttons-updown' : 'king-vote-buttons-netup') . '">');

		switch (@$post['vote_state']) {
			case 'voted_up':
				$this->post_hover_button($post, 'vote_up_tags', '+', 'king-vote-one-button king-voted-up');
				break;

			case 'voted_up_disabled':
				$this->post_disabled_button($post, 'vote_up_tags', '+', 'king-vote-one-button king-vote-up');
				break;

			case 'up_only':
				$this->post_hover_button($post, 'vote_up_tags', '+', 'king-vote-first-button king-vote-up');

				break;

			case 'enabled':
				$this->post_hover_button($post, 'vote_up_tags', '+', 'king-vote-first-button king-vote-up');

				break;

			default:
				$this->post_disabled_button($post, 'vote_up_tags', '', 'king-vote-first-button king-vote-up');

				break;
		}

		$this->output('</DIV>');
	}

	public function vote_buttonsdown($post)
	{
		$this->output('<DIV CLASS="' . (($post['vote_view'] == 'updown') ? 'king-vote-buttons-updown' : 'king-vote-buttons-netdown') . '">');

		switch (@$post['vote_state']) {

			case 'voted_down':
				$this->post_hover_button($post, 'vote_down_tags', '&ndash;', 'king-vote-one-button king-voted-down');
				break;

			case 'voted_down_disabled':
				$this->post_disabled_button($post, 'vote_down_tags', '&ndash;', 'king-vote-one-button king-vote-down');
				break;

			case 'up_only':

				$this->post_disabled_button($post, 'vote_down_tags', '', 'king-vote-second-button king-vote-down');
				break;

			case 'enabled':

				$this->post_hover_button($post, 'vote_down_tags', '&ndash;', 'king-vote-second-button king-vote-down');
				break;

			default:

				$this->post_disabled_button($post, 'vote_down_tags', '', 'king-vote-second-button king-vote-down');
				break;
		}

		$this->output('</DIV>');
	}
	public function post_hover_button($post, $element, $value, $class)
	{
		if (isset($post[$element])) {
			$this->output('<button ' . $post[$element] . ' type="submit" value="' . $value . '" class="' . $class . '-button"></button>');
		}

	}

	public function post_disabled_button($post, $element, $value, $class)
	{
		if (isset($post[$element])) {
			$this->output('<button ' . $post[$element] . ' type="submit" value="' . $value . '" class="' . $class . '-disabled" disabled="disabled"></button>');
		}

	}
	public function footer()
	{
		$this->output('<div class="king-footer">');
		$this->output('<ul class="socialicons">');
		if (qa_opt('footer_fb')) {
			$this->output('<li class="facebook"><a href="' . qa_opt('footer_fb') . '" target="_blank" data-toggle="tooltip" data-placement="top"  title="' . qa_lang_html('misc/footer_fb') . '"><i class="fab fa-facebook-f"></i></a></li>');
		}
		if (qa_opt('footer_twi')) {
			$this->output('<li class="twitter"><a href="' . qa_opt('footer_twi') . '" target="_blank" data-toggle="tooltip" data-placement="top"  title="' . qa_lang_html('misc/footer_twi') . '"><i class="fab fa-twitter"></i></a></li>');
		}
		if (qa_opt('footer_google')) {
			$this->output('<li class="instagram"><a href="' . qa_opt('footer_google') . '" target="_blank" data-toggle="tooltip" data-placement="top"  title="' . qa_lang_html('misc/footer_insta') . '"><i class="fab fa-instagram"></i></a></li>');
		}
		if (qa_opt('footer_ytube')) {
			$this->output('<li class="youtube"><a href="' . qa_opt('footer_ytube') . '" target="_blank" data-toggle="tooltip" data-placement="top"  title="' . qa_lang_html('misc/footer_ytube') . '"><i class="fab fa-youtube"></i></a></li>');
		}
		if (qa_opt('footer_pin')) {
			$this->output('<li class="pinterest"><a href="' . qa_opt('footer_pin') . '" target="_blank" data-toggle="tooltip" data-placement="top"  title="' . qa_lang_html('misc/footer_pin') . '"><i class="fab fa-pinterest-p"></i></a></li>');
		}
		$this->output('</ul>');
		$this->nav('footer');
		$this->attribution();
		$this->footer_clear();

		$this->output('</div> <!-- END king-footer -->', '');
		$this->userpanel2();
	}

	public function feed()
	{
		$feed = @$this->content['feed'];

		if (!empty($feed)) {

		}
	}

	public function get_prev_q()
	{

		$myurl       = $this->request;
		$myurlpieces = explode("/", $myurl);
		$myurl       = $myurlpieces[0];

		$query_p = "SELECT *
				FROM ^posts
				WHERE postid < $myurl
				AND type='Q'
				ORDER BY postid DESC
				LIMIT 1";

		$prev_q = qa_db_query_sub($query_p);

		while ($prev_link = qa_db_read_one_assoc($prev_q, true)) {

			$title = $prev_link['title'];
			$pid   = $prev_link['postid'];

			$this->output('<A HREF="' . qa_q_path_html($pid, $title) . '" CLASS="king-prev-q">' . $title . ' <i class="fas fa-angle-right"></i></A>');
		}

	}

	public function get_next_q()
	{

		$myurl       = $this->request;
		$myurlpieces = explode("/", $myurl);
		$myurl       = $myurlpieces[0];

		$query_n = "SELECT *
				FROM ^posts
				WHERE postid > $myurl
				AND type='Q'
				ORDER BY postid ASC
				LIMIT 1";

		$next_q = qa_db_query_sub($query_n);

		while ($next_link = qa_db_read_one_assoc($next_q, true)) {

			$title = $next_link['title'];
			$pid   = $next_link['postid'];

			$this->output('<A HREF="' . qa_q_path_html($pid, $title) . '" CLASS="king-next-q"><i class="fas fa-angle-left"></i> ' . $title . '</A>');
		}

	}

	public function message_item($message)
	{
		$this->output('<div class="king-message-item" ' . @$message['tags'] . '>');
		$this->post_avatar_meta($message, 'king-message');
		$this->message_content($message);
		$this->message_buttons($message);
		$this->output('</div> <!-- END king-message-item -->', '');
	}

	public function nav_link($navlink, $class)
	{
		if (isset($navlink['url'])) {
			$this->output(
				'<a href="' . $navlink['url'] . '" class="king-' . $class . '-link' .
				(@$navlink['selected'] ? (' king-' . $class . '-selected') : '') .
				(@$navlink['favorited'] ? (' king-' . $class . '-favorited') : '') .
				'"' . (strlen(@$navlink['popup']) ? (' title="' . $navlink['popup'] . '"') : '') .
				(isset($navlink['target']) ? (' target="' . $navlink['target'] . '"') : '') . '>' . $navlink['label'] .
				'</a>'
			);
		} else {
			$this->output(
				'<span class="king-' . $class . '-nolink' . (@$navlink['selected'] ? (' king-' . $class . '-selected') : '') .
				(@$navlink['favorited'] ? (' king-' . $class . '-favorited') : '') . '"' .
				(strlen(@$navlink['popup']) ? (' title="' . $navlink['popup'] . '"') : '') .
				'>' . $navlink['label'] . '</span>'
			);
		}

		if (strlen(@$navlink['note'])) {
			$this->output('<span class="king-' . $class . '-note">' . $navlink['note'] . '</span>');
		}

	}

	public function attribution()
	{

		$this->output(
			'<DIV CLASS="king-attribution">',
			'2021 ©  <A HREF="/">' . $this->content['site_title'] . '</A> | All rights reserved',
			'</DIV>'
		);
	}

}
