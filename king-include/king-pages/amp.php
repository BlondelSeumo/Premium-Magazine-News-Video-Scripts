<?php
/*

	File: king-include/king-page-question.php
	Description: Controller for question page (only viewing functionality here)


	This program is free software; you can redistribute it and/or
	modify it under the terms of the GNU General Public License
	as published by the Free Software Foundation; either version 2
	of the License, or (at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	More about this license: LICENCE.html
*/

	if (!defined('QA_VERSION')) { // don't allow this page to be requested directly from browser
		header('Location: ../');
		exit;
	}

	require_once QA_INCLUDE_DIR.'king-app/cookies.php';
	require_once QA_INCLUDE_DIR.'king-app/format.php';
	require_once QA_INCLUDE_DIR.'king-db/selects.php';
	require_once QA_INCLUDE_DIR.'king-util/sort.php';
	require_once QA_INCLUDE_DIR.'king-util/string.php';
	require_once QA_INCLUDE_DIR.'king-app/captcha.php';
	require_once QA_INCLUDE_DIR.'king-pages/question-view.php';
	require_once QA_INCLUDE_DIR.'king-app/updates.php';

	$postid=qa_request_part(0);
	$userid=qa_get_logged_in_userid();
	$cookieid=qa_cookie_get();


//	Get information about this question

	list($post, $childposts, $achildposts, $parentquestion, $closepost, $extravalue, $categories, $favorite)=qa_db_select_with_pending(
		qa_db_full_post_selectspec($userid, $postid),
		qa_db_full_child_posts_selectspec($userid, $postid),
		qa_db_full_a_child_posts_selectspec($userid, $postid),
		qa_db_post_parent_q_selectspec($postid),
		qa_db_post_close_post_selectspec($postid),
		qa_db_post_meta_selectspec($postid, 'qa_q_extra'),
		qa_db_category_nav_selectspec($postid, true, true, true),
		isset($userid) ? qa_db_is_favorite_selectspec($userid, QA_ENTITY_QUESTION, $postid) : null
	);

	if (isset($post['basetype']) && $post['basetype'] != 'Q') {
		$post=null;
	}

	if (isset($post)) {

		$post=$post+qa_page_q_post_rules($post, null, null, $childposts); // array union


	}
function king_parse_video_uri($text)
	{

		$types = array(
			'youtube'     => array(
				array(
					'https{0,1}:\/\/w{0,3}\.*youtube\.com\/watch\?\S*v=([A-Za-z0-9_-]+)[^< ]*',
					'https://www.youtube.com/embed/$1',
				),
				array(
					'https{0,1}:\/\/w{0,3}\.*youtu\.be\/([A-Za-z0-9_-]+)[^< ]*',
					'https://www.youtube.com/embed/$1"',
				),
			),
			'vimeo'       => array(
				array(
					'https{0,1}:\/\/w{0,3}\.*vimeo\.com\/([0-9]+)[^< ]*',
					'https://player.vimeo.com/video/$1?title=0&amp;byline=0&amp;portrait=0&amp;wmode=transparent',
				),
			),

			'instagram'   => array(
				array(
					'https{0,1}:\/\/w{0,3}\.*instagram\.com\/p\/([A-Za-z0-9_-]+)[^< ]*',
					'https://www.instagram.com/p/$1/embed/',
				),
			),

			'soundcloud'  => array(
				array(
					'https{0,1}:\/\/w{0,3}\.*soundcloud\.com\/([-\%_\/.a-zA-Z0-9]+\/[-\%_\/.a-zA-Z0-9]+)[^< ]*',
					'https://w.soundcloud.com/player/?url=https://soundcloud.com/$1&amp;auto_play=false&amp;hide_related=false&amp;show_comments=true&amp;show_user=true&amp;show_reposts=false&amp;visual=true',
				),
			),

			'spotify'  => array(
				array(
					'https{0,1}:\/\/w{0,3}\.*open.spotify\.com\/([-\%_\/.a-zA-Z0-9]+\/[-\%_\/.a-zA-Z0-9]+)[^< ]*',
					'https://open.spotify.com/embed/$1',
				),
			),


			'xhamster'    => array(
				array(
					'https{0,1}:\/\/w{0,3}\.*xhamster\.com\/movies\/([0-9]+)\/(.*?)[^< ]*',
					'http://xhamster.com/xembed.php?video=$1',
				),
			),



			'coub'        => array(
				array(
					'https{0,1}:\/\/w{0,3}\.*coub.com\/view\/([\-\_\/.a-zA-Z0-9]+)[^< ]*',
					'//coub.com/embed/$1?muted=true&autostart=true&originalSize=false&hideTopBar=false&startWithHD=false',
				),
			),

			'gfycat'      => array(
				array(
					'https{0,1}:\/\/w{0,3}\.*gfycat\.com\/([A-Z.a-z0-9_]+)[^< ]*',
					'https://gfycat.com/ifr/$1',
				),
			),

			'twitch'      => array(
				array(
					'https{0,1}:\/\/w{0,3}\.*twitch\.tv\/([A-Za-z0-9]+)[^< ]*',
					'https://player.twitch.tv/?channel=$1',
				),
			),

			'drive'       => array(
				array(
					'https{0,1}:\/\/w{0,3}\.*drive\.google\.com\/file\/d\/([\-\_\/.a-zA-Z0-9]+)\/view[^< ]*',
					'https://drive.google.com/file/d/$1/preview',
				),
			),


		);

		foreach ($types as $t => $ra) {
			foreach ($ra as $r) {

				$text = preg_replace('/<a[^>]+>' . $r[0] . '<\/a>/i', $r[1], $text);
				$text = preg_replace('/(?<![\'"=])' . $r[0] . '/i', $r[1], $text);
			}
		}
		return $text;
	}
//	Deal with question not found or not viewable, otherwise report the view event

	if (!isset($post))
		return include QA_INCLUDE_DIR.'king-page-not-found.php';

	if (!$post['viewable']) {

		if ($post['queued'])
			$error=qa_lang_html('question/q_waiting_approval');
		elseif ($post['flagcount'] && !isset($post['lastuserid']))
			$error=qa_lang_html('question/q_hidden_flagged');
		elseif ($post['authorlast'])
			$error=qa_lang_html('question/q_hidden_author');
		else
			$error=qa_lang_html('question/q_hidden_other');


	}

	$permiterror=qa_user_post_permit_error('permit_view_q_page', $post, null, false);

	if ( $permiterror && (qa_is_human_probably() || !qa_opt('allow_view_q_bots')) ) {

		$topage=qa_q_request($postid, $post['title']);

		switch ($permiterror) {
			case 'login':
				$error=qa_insert_login_links(qa_lang_html('main/view_q_must_login'), $topage);
				break;

			case 'confirm':
				$error=qa_insert_login_links(qa_lang_html('main/view_q_must_confirm'), $topage);
				break;

			case 'approve':
				$error=qa_lang_html('main/view_q_must_be_approved');
				break;

			default:
				$error=qa_lang_html('users/no_permission');
				break;
		}


	}
$shareurl  = qa_path_html(qa_q_request($post['postid'], $post['title']), null, qa_opt('site_url'));
$featured = king_get_uploads($post['content']);
$head = '';
$alist = '';
if ( $post['postformat'] == 'I' ) {
	$lists = @unserialize($extravalue);
	if ($lists) {
		$head = '<div class="amp-king-image"><amp-carousel width="600" height="400" layout="responsive" type="slides">';
		foreach ($lists as $list) {
			$featured = king_get_uploads($list);
			$head .= '<amp-img src="'.qa_html($featured['furl']).'" width="'.qa_html($featured['width']).'" height="'.qa_html($featured['height']).'" layout="responsive"></amp-img>';
		}
		$head .= '</amp-carousel></div>';
	}
} elseif( $post['postformat'] == 'V' ) {
	$head = '<div class="amp-video">';
	if (is_numeric($extravalue)) {
		$vidurl = king_get_uploads($extravalue);
		$head .= '<amp-video controls width="800" height="400" layout="responsive"
		poster="'.qa_html($featured['furl']).'">
			<source src="'.qa_html($vidurl['furl']).'" type="video/mp4" />
			<div fallback>
				<p>This browser does not support the video element.</p>
			</div>
		</amp-video>';
	} else {
		$vid = king_parse_video_uri($extravalue);
		$head .= '<amp-iframe width="800" height="400" layout="responsive" sandbox="allow-scripts allow-same-origin" src="'.$vid.'">
		<amp-img layout="fill" src="'.qa_html($featured['furl']).'" placeholder></amp-img>
		</amp-iframe>';
	}
	$head .= '</div>';
} elseif( $post['postformat'] == 'poll' ) {

	$alist = '<a href="'.$shareurl.'" class="take-poll">'.qa_lang_html('misc/take_poll').'</a>';

} elseif( $post['postformat'] == 'list' ) {
	$poll= get_poll($post['postid']);
	$lists = @unserialize($poll['content']);
	if ($lists) {
		$alist = '<div class="amp-list">';
		foreach ($lists as $list) {
			$alist .= '<span class="list-title"><h2><span class="list-id">#' . qa_html($list['id']) . ' </span>' . qa_html($list['choices']) . '</h2></span>';
			if ( $list['img'] ) {
				$imgs = king_get_uploads($list['img']);
				$alist .= '<amp-img src="'.qa_html($imgs['furl']).'" alt="" height="'.qa_html($imgs['height']).'" width="'.qa_html($imgs['width']).'" layout="responsive"></amp-img>';
			} elseif ( $list['video'] ) {
				
				$vid = king_parse_video_uri($list['video']);
				$alist .= '<span class="list-video">';
				
				$alist .= '<amp-iframe width="800" height="400" layout="responsive" sandbox="allow-scripts allow-same-origin" src="'.$vid.'">
				<amp-img layout="fill" src="'.qa_html($featured['furl']).'" placeholder></amp-img>
				</amp-iframe>';
				$alist .= '</span>';
			}
			$alist .= '<span class="amp-listcontent">' . $list['desc'] . '</span>';
		}
		$alist .= '</div>';
	}


}

/*
	$poll= get_poll($post['postid']);
	echo ' title: ' . $post['title'] . ' content: ' . $post['content'] . ' extra: ' . $extravalue . ' format: ' . $post['postformat']. $poll['content'];
*/
$pattern = '/<img/i';
$replacement = '<amp-img width="800" height="600" layout="responsive"';
$pcontent = preg_replace($pattern, $replacement, $post['pcontent']);
$q_time= qa_when_to_html($post['created'], 7);
$when=@$q_time['prefix'] . ' ' . @$q_time['data'] . ' ' . @$q_time['suffix'];
$logourl = qa_opt('logo_url');
$logo = qa_html(is_numeric(strpos($logourl, '://')) ? $logourl : qa_path_to_root().$logourl);
$useraccount=qa_db_select_with_pending(qa_db_user_account_selectspec($post['userid'], true));
$amp = '<!doctype html>
<html amp lang="en">
  <head>
	<meta charset="utf-8">
	<script async src="https://cdn.ampproject.org/v0.js"></script>
	<title>'.qa_html($post['title']).'</title>
	<link rel="canonical" href="https://amp.dev/documentation/guides-and-tutorials/start/create/basic_markup/">
	<meta name="viewport" content="width=device-width">
	<script type="application/ld+json">
	  {
		"@context": "http://schema.org",
		"@type": "NewsArticle",
		"headline": "'.qa_opt('site_title').'",
		"datePublished": "2015-10-07T12:02:41Z",
		"author": {
			"@type": "Person",
			"name": "'.qa_html(qa_userid_to_handle($post['userid'])).'"
		},
		"publisher": {
				"@type": "Organization",
				"name": "'.qa_opt('site_title').'",
				"logo": {
					"@type": "ImageObject",
					"url": "'.$logo.'",
					"width": 150,
					"height": 150
				}
		},
		"image": [
		  "logo.jpg"
		]
	  }
	</script>
	<style amp-boilerplate>body{-webkit-animation:-amp-start 8s steps(1,end) 0s 1 normal both;-moz-animation:-amp-start 8s steps(1,end) 0s 1 normal both;-ms-animation:-amp-start 8s steps(1,end) 0s 1 normal both;animation:-amp-start 8s steps(1,end) 0s 1 normal both}@-webkit-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@-moz-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@-ms-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@-o-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}</style>
	<noscript>
		<style amp-boilerplate>body{-webkit-animation:none;-moz-animation:none;-ms-animation:none;animation:none}</style>
	</noscript>
	<style amp-custom>
	body{background-color:#e8eaee;font-family:\'Open Sans\',sans-serif;line-height:28px;color:#949fa9}a{color:#D63E3D;text-decoration:none}a:hover{color:#000000;text-decoration:none}.amp-king-header{display:block;background-color:#ffffff;text-align:center;position:sticky;width:100%;height:60px;z-index:12;top:0;-webkit-box-shadow:0 2px 14px 0 rgb(0 0 0 / 5%);box-shadow:0 2px 14px 0 rgb(0 0 0 / 5%)}.amp-king-logo amp-img{max-height:60px;max-width:200px}.amp-king-logo amp-img img{object-fit:cover}.amp-featured{box-sizing:border-box;display:block}.featured-img{max-height:300px;width:100%;border-radius:0}.featured-img img{object-fit:cover}.amp-king-container{max-width:980px;display:block;margin:0 auto}.amp-main{background-color:#fff;padding:10px 20px;box-sizing:border-box;border-radius:12px;margin:-70px 20px 10px;z-index:2;position:relative}.amp-content,.amp-listcontent{margin:15px 0;display:block}amp-img{border-radius:12px}h1,h2{color:#747386;line-height:38px;margin:20px 0 15px}.amp-meta{font-size:12px}.amp-meta amp-img{border-radius:32px}.amp-meta a{font-weight:bold;font-size:14px}.amp-meta-name{display:inline-block;vertical-align:top;margin-left:7px}.amp-meta-name a{display:block;line-height:15px;color:#a2a1a7}.amp-king-image{background-color:#f0f1f6;border-radius:12px}.amp-video{background-color:#f0f1f6;border-radius:12px;padding:8px;display:block;margin-top:10px}.list-video{background-color:#f0f1f6;border-radius:12px;padding:8px;display:block}.amp-video amp-iframe,.list-video amp-iframe{border-radius:12px}.amp-video amp-video{border-radius:12px}.amp-video amp-video video{outline:0}.amp-share{background-color:#f0f1f6;margin-top:10px;border-radius:12px;padding:10px 10px 0}.amp-share amp-social-share{outline:0;min-width:70px;max-height:38px;display:inline-block;border-radius:6px;background-color:#f0f1f6;background-size:28px}.amp-share amp-social-share:hover{background-color:#333}.amp-share .amp-social-share-sms{background-size:22px;background-position:center 10px}.take-poll,.see-full{display:block;background-color:#d8dae3;color:#fff;margin-bottom:10px;padding:5px 10px;border-radius:8px;text-align:center}.see-full{margin:20px;padding:8px;background-color:#fff;color:#d8dae3}.king-nsfw-post{background-color:#2a2a2a;display:block;text-align:center;padding:20px;border-radius:8px}
	</style>
	<script async custom-element="amp-carousel" src="https://cdn.ampproject.org/v0/amp-carousel-0.1.js"></script>
	<script async custom-element="amp-sidebar" src="https://cdn.ampproject.org/v0/amp-sidebar-0.1.js"></script>	
	<script async custom-element="amp-video" src="https://cdn.ampproject.org/v0/amp-video-0.1.js"></script>
	<script async custom-element="amp-iframe" src="https://cdn.ampproject.org/v0/amp-iframe-0.1.js"></script>
	<script async custom-element="amp-social-share" src="https://cdn.ampproject.org/v0/amp-social-share-0.1.js"></script>
  </head>';
$amp .= '<body>
<header class="amp-king-header">
	<div class="amp-king-logo">
			<a href="'.qa_opt('site_url').'" class="amp-king-logo-img">
				<amp-img src="'.$logo.'" width="200" height="60" responsive></amp-img>
			</a>
	</div>
	<div on="tap:sidebar.toggle" class="ampstart-btn caps m2" role="button" tabindex="0"><span></span></div>
</header>
	<div class="amp-featured">
		<amp-img src="'.qa_html($featured['furl']).'" alt="" height="'.qa_html($featured['height']).'" width="'.qa_html($featured['width']).'" class="featured-img" layout="responsive"></amp-img>
	</div>
<div class="amp-king-container">
	<div class="amp-main">
<div class="amp-share">
			<amp-social-share type="facebook" height="38" data-param-app_id="'.qa_opt('fb_user_token').'" data-param-href="'.$shareurl.'"></amp-social-share>
			<amp-social-share type="twitter" aria-label="Share on Twitter" data-param-url="'.$shareurl.'"></amp-social-share>
			<amp-social-share type="whatsapp" height="38" data-param-text="'.$shareurl.'"></amp-social-share>
			<amp-social-share type="sms" height="38" data-param-body="'.$shareurl.'"></amp-social-share>
			<amp-social-share type="email" height="38" data-param-body="'.$shareurl.'"></amp-social-share>
		</div>
		'.$head.'
		<h1>'.qa_html($post['title']).'</h1>
		<div class="amp-meta">
			<a href="' . qa_path_html('user/'.qa_userid_to_handle($post['userid'])) . '">
				<amp-img src="' . get_avatar($useraccount['avatarblobid'], 40) . '" width="40" height="40"></amp-img>
			</a>
			<span class="amp-meta-name">
				<a href="' . qa_path_html('user/'.qa_userid_to_handle($post['userid'])) . '">'.qa_html(qa_userid_to_handle($post['userid'])).'</a>
				'.qa_html($when).'
			</span>
			</div>';
		$amp2 = '<div class="amp-content">'.$pcontent.''.$alist.'</div>
		
	</div>
	<a href="'.$shareurl.'" class="see-full">'.qa_lang_html('misc/see_full').'</a>
</div>
  </body>
</html>';

if ($post['nsfw'] !== null && !qa_is_logged_in()) {
	$amp2 = '<div class="amp-content"><span class="king-nsfw-post">' . qa_lang_html('misc/nsfw_post') . '</span></div>
		
	</div>
	<a href="'.$shareurl.'" class="see-full">'.qa_lang_html('misc/see_full').'</a>
</div>
  </body>
</html>';
} elseif (!empty($error)) {

	$amp2 = '<div class="amp-content"><span class="king-nsfw-post">' . $error . '</span></div>
		
	</div>
	<a href="'.$shareurl.'" class="see-full">'.qa_lang_html('misc/see_full').'</a>
</div>
  </body>
</html>';
}



echo $amp.$amp2;
