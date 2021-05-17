<?php
/*

File: king-include/king-app-blobs.php
Description: Application-level blob-management functions

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

if (!defined('QA_VERSION')) {
	// don't allow this page to be requested directly from browser
	header('Location: ../');
	exit;
}

function kingsource($videocontent)
{
	$parsed = parse_url($videocontent);
	return str_replace('www.', '', strtolower($parsed['host']));
}

function get_thumb($videocontent)
{
	$opts = array('http'=>array('header' => "User-Agent:MyAgent/1.0\r\n")); 
	$context = stream_context_create( $opts );
	$res = file_get_contents( $videocontent, false, $context );

	preg_match('/property="og:image" content="(.*?)"/', $res, $output);
	return ($output[1]) ? $output[1] : false;
}

function king_twitch($videocontent)
{
	$res = file_get_contents("$videocontent");
	preg_match('/content=\'(.*?)\' property=\'og:image\'/', $res, $matches);

	return ($matches[1]) ? $matches[1] : false;
}

function king_vk($videocontent)
{
	$page          = file_get_contents("$videocontent");
	$page_for_hash = preg_replace('/\\\/', '', $page);
	if (preg_match("@,\"jpg\":\"(.*?)\",@", $page_for_hash, $matches)) {
		$result = $matches[1];
		return $result;
	}
}

function king_mailru($videocontent)
{
	$page = file_get_contents("$videocontent");
	if (preg_match('/content="(.*?)" name="og:image"/', $page, $mailru)) {
		$king = $mailru[1];
		return $king;
	}
}

function king_facebook($content)
{
	$facebook_access_token = qa_opt('fb_user_token');
	$paths                 = explode("/", $content);
	$num                   = count($paths);
	for ($i = $num - 1; $i > 0; $i--) {
		if ($paths[$i] != "") {
			$video_id = $paths[$i];
			break;
		}
	}
	$data = file_get_contents('https://graph.facebook.com/' . $video_id . '/thumbnails?access_token=' . $facebook_access_token . '');
	if ($data !== false) {
		$result           = json_decode($data);
		return $thumbnail = $result->data[0]->uri;
	}
}

function king_youtube($url)
{
	$queryString = parse_url($url, PHP_URL_QUERY);
	parse_str($queryString, $params);
	if (isset($params['v'])) {
		return "https://i3.ytimg.com/vi/" . trim($params['v']) . "/hqdefault.jpg";
	}
	return true;
}

function king_soundcloud($videocontent)
{
	ini_set("user_agent", "SCPHP");

	function resolve_sc_track($url = '')
	{
		return json_decode(file_get_contents("https://api.soundcloud.com/resolve?client_id=KqmJoxaVYyE4XT0XQqFUUQ&format=json&url="
			. $url), true);
	}
	function get_artwork_url($track, $format = "t500x500")
	{
		return str_replace("large", $format, $track["artwork_url"]);
	}
	$track = resolve_sc_track("$videocontent");
	return get_artwork_url($track);
}

function king_xhamster($videocontent)
{
	$res = file_get_contents("$videocontent");
	preg_match('/name="twitter:image" property="og:image" content="(.*?)"/', $res, $output);
	return ($output[1]) ? $output[1] : false;
}

function king_okru($videocontent)
{
	$res = file_get_contents("$videocontent");
	preg_match('/rel="image_src" href="(.*?)"/', $res, $output);
	return ($output[1]) ? $output[1] : false;
}

function coub_thumb($videocontent)
{
	$page2 = file_get_contents("$videocontent");
	if (preg_match('/property="og:image" content="(.*?)"/', $page2, $coub)) {
		$cou = $coub[1];
		return $cou;
	}
}

function king_gfycat($videocontent)
{
	$res = file_get_contents("$videocontent");
	preg_match('/name="twitter:image" content="(.*?)"/', $res, $output);
	return ($output[1]) ? $output[1] : false;
}
function embed_replace($text)
	{

		$w = '800';

		$h = '450';

		$w2 = '100%';

		$h2 = 'auto';

		$types = array(
			'youtube'     => array(
				array(
					'https{0,1}:\/\/w{0,3}\.*youtube\.com\/watch\?\S*v=([A-Za-z0-9_-]+)[^< ]*',
					'<iframe width="' . $w . '" height="' . $h . '" src="https://www.youtube.com/embed/$1?wmode=transparent" frameborder="0" allowfullscreen></iframe>',
				),
				array(
					'https{0,1}:\/\/w{0,3}\.*youtu\.be\/([A-Za-z0-9_-]+)[^< ]*',
					'<iframe width="' . $w . '" height="' . $h . '" src="https://www.youtube.com/embed/$1?wmode=transparent" frameborder="0" allowfullscreen></iframe>',
				),
			),
			'vimeo'       => array(
				array(
					'https{0,1}:\/\/w{0,3}\.*vimeo\.com\/([0-9]+)[^< ]*',
					'<iframe src="https://player.vimeo.com/video/$1?title=0&amp;byline=0&amp;portrait=0&amp;wmode=transparent" width="' . $w . '" height="' . $h . '" frameborder="0"></iframe>'),
			),
			'metacafe'    => array(
				array(
					'https{0,1}:\/\/w{0,3}\.*metacafe\.com\/watch\/([0-9]+)\/([a-z0-9_]+)[^< ]*',
					'<embed flashVars="playerVars=showStats=no|autoPlay=no" src="https://www.metacafe.com/fplayer/$1/$2.swf" width="' . $w . '" height="' . $h . '" wmode="transparent" allowFullScreen="true" allowScriptAccess="always" name="Metacafe_$1" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash"></embed>',
				),
			),
			'vine'        => array(
				array(
					'https{0,1}:\/\/w{0,3}\.*vine\.co\/v\/([A-Za-z0-9_-]+)[^< ]*',
					'<iframe class="vine-embed" src="https://vine.co/v/$1/embed/simple?audio=1" width="' . $w . '" height="480px" frameborder="0"></iframe>',
				),
			),

			'instagram'   => array(
				array(
					'https{0,1}:\/\/w{0,3}\.*instagram\.com\/p\/([A-Za-z0-9_-]+)[^< ]*',
					'<iframe src="//instagram.com/p/$1/embed/" width="' . $w . '" height="' . $w . '" frameborder="0" scrolling="no" allowtransparency="true" class="instaframe"></iframe>',
				),
			),

			'twitter'   => array(
				array(
					'https{0,1}:\/\/w{0,3}\.*twitter\.com\/([A-Za-z0-9_-]+)\/status\/([A-Za-z0-9_-]+)[^< ]*',
					'<iframe id="twitter-widget-0" scrolling="no" frameborder="0" allowtransparency="true" allowfullscreen="true" src="https://platform.twitter.com/embed/Tweet.html?id=$2" data-tweet-id="$2" width="' . $w . '" height="' . $w . '" class="instaframe"></iframe>',
				),
			),


			'dailymotion' => array(
				array(
					'https{0,1}:\/\/w{0,3}\.*dailymotion\.com\/video\/([A-Za-z0-9]+)[^< ]*',
					'<iframe frameborder="0" width="' . $w . '" height="' . $h . '" src="https://www.dailymotion.com/embed/video/$1?wmode=transparent"></iframe>',
				),
			),

			'mailru'      => array(
				array(
					'https{0,1}:\/\/w{0,3}\.*my.mail.ru\/mail\/([\-\_\/.a-zA-Z0-9]+)[^< ]*',
					'<iframe src="https://videoapi.my.mail.ru/videos/embed/mail/$1" width="' . $w . '" height="' . $h . '" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>',
				),
			),

			'soundcloud'  => array(
				array(
					'https{0,1}:\/\/w{0,3}\.*soundcloud\.com\/([-\%_\/.a-zA-Z0-9]+\/[-\%_\/.a-zA-Z0-9]+)[^< ]*',
					'<iframe width="100%" height="450" scrolling="no" frameborder="no" src="https://w.soundcloud.com/player/?url=https://soundcloud.com/$1&amp;auto_play=false&amp;hide_related=false&amp;show_comments=true&amp;show_user=true&amp;show_reposts=false&amp;visual=true"></iframe>',
				),
			),

			'spotify'  => array(
				array(
					'https{0,1}:\/\/w{0,3}\.*open.spotify\.com\/([-\%_\/.a-zA-Z0-9]+\/[-\%_\/.a-zA-Z0-9]+)[^< ]*',
					'<iframe src="https://open.spotify.com/embed/$1" width="' . $w . '" height="' . $h . '" frameborder="0" allowtransparency="true" ></iframe>',
				),
			),

			'facebook'    => array(
				array(
					'https{0,1}:\/\/w{0,3}\.*facebook\.com\/video\.php\?\S*v=([A-Za-z0-9_-]+)[^< ]*',
					'<div class="fb-video" data-allowfullscreen="true" data-href="https://www.facebook.com/video.php?v=$1&type=1"></div>',
				),
				array(
					'https{0,1}:\/\/w{0,3}\.*facebook\.com\/([A-Z.a-z0-9_-]+)\/videos\/([A-Za-z0-9_-]+)[^< ]*',
					'<div class="fb-video" data-allowfullscreen="true"  data-href="/$1/videos/$2/?type=1"></div>',
				),
				array(
					'https{0,1}:\/\/w{0,3}\.*facebook\.com\/watch\/\?\S*v=([A-Za-z0-9]+)[^< ]*',
					'<iframe src="https://www.facebook.com/plugins/video.php?height=3144&href=https://www.facebook.com/watch/?v=$1&show_text=false&width=560" width="' . $w . '" height="' . $h . '" style="border:none;overflow:hidden" scrolling="no" frameborder="0" allow="autoplay; clipboard-write; encrypted-media; picture-in-picture; web-share" allowFullScreen="true"></iframe>',
				),
			),

			'image'       => array(
				array(
					'(https*:\/\/[-\[\]\{\}\(\)\%_\/.a-zA-Z0-9+]+\.(png|jpg|jpeg|gif|bmp))[^< ]*',
					'<img src="$1" style="max-width:' . $w2 . ';height:' . $h2 . ';display:block;" />',
				),
			),

			'xhamster'    => array(
				array(
					'https{0,1}:\/\/w{0,3}\.*xhamster\.com\/movies\/([0-9]+)\/(.*?)[^< ]*',
					'<iframe src="http://xhamster.com/xembed.php?video=$1" width="' . $w . '" height="' . $h . '" scrolling="no" allowfullscreen></iframe>',
				),
			),

			'okru'        => array(
				array(
					'https{0,1}:\/\/w{0,3}\.*ok\.ru\/video\/([A-Za-z0-9]+)[^< ]*',
					'<iframe width="' . $w . '" height="' . $h . '" src="http://ok.ru/videoembed/$1" frameborder="0" allowfullscreen></iframe>',
				),
			),

			'coub'        => array(
				array(
					'https{0,1}:\/\/w{0,3}\.*coub.com\/view\/([\-\_\/.a-zA-Z0-9]+)[^< ]*',
					'<iframe src="//coub.com/embed/$1?muted=true&autostart=true&originalSize=false&hideTopBar=false&startWithHD=false" allowfullscreen="true" frameborder="0" width="' . $w . '" height="' . $h . '"></iframe>',
				),
			),

			'vidme'       => array(
				array(
					'https{0,1}:\/\/w{0,3}\.*vid\.me\/([A-Za-z0-9_-]+)[^< ]*',
					'<iframe src="https://vid.me/e/$1" width="' . $w . '" height="' . $h . '" frameborder="0" allowfullscreen webkitallowfullscreen mozallowfullscreen scrolling="no"></iframe>',
				),
			),

			'gfycat'      => array(
				array(
					'https{0,1}:\/\/w{0,3}\.*gfycat\.com\/([A-Z.a-z0-9_]+)[^< ]*',
					'<iframe src="https://gfycat.com/ifr/$1" frameborder="0" scrolling="no" width="' . $w . '" height="' . $h . '" allowfullscreen></iframe>',
				),
			),

			'twitch'      => array(
				array(
					'https{0,1}:\/\/w{0,3}\.*twitch\.tv\/([A-Za-z0-9]+)[^< ]*',
					'<iframe src="https://player.twitch.tv/?channel=$1"  frameborder="0" allowfullscreen="true" scrolling="no" height="378" width="620"></iframe>',
				),
			),

			'drive'       => array(
				array(
					'https{0,1}:\/\/w{0,3}\.*drive\.google\.com\/file\/d\/([\-\_\/.a-zA-Z0-9]+)\/view[^< ]*',
					'<iframe src="https://drive.google.com/file/d/$1/preview" width="' . qa_html($w) . '" height="' . qa_html($h) . '"></iframe>',
				),
			),

			'rutube'      => array(
				array(
					'https{0,1}:\/\/w{0,3}\.*rutube\.ru\/video\/([A-Z.a-z0-9_-]+)\/\?\S*pl_type=source\&\S*pl_id=([A-Za-z0-9_-]+)[^< ]*',
					'<iframe width="' . $w . '" height="' . $h . '" src="//rutube.ru/pl/?pl_id=$2&pl_type=source&pl_video=$1" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowfullscreen></iframe>',
				),
			),
			'mp4'=>array(
				array(
					'(https*:\/\/[-\%_\/.a-zA-Z0-9+]+\.(mp4))[^< ]*',
					'<video id="my-video" class="video-js vjs-theme-forest" controls preload="auto"  width="960" height="540" data-setup="{}" poster="" ><source src="$1" type="video/mp4" /></video>'
				)
			),

			'mp3'         => array(
				array(
					'(https*:\/\/[-\%_\/.a-zA-Z0-9]+\.mp3)[^< ]*', qa_opt('embed_mp3_player_code'), 'mp3',
				),
			),
			'gmap'        => array(
				array(
					'(https*:\/\/maps.google.com\/?[^< ]+)',
					'<iframe width="' . qa_opt('embed_gmap_width') . '" height="' . qa_opt('embed_gmap_height') . '" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="$1&amp;ie=UTF8&amp;output=embed"></iframe><br /><small><a href="$1&amp;ie=UTF8&amp;output=embed" style="color:#0000FF;text-align:left">View Larger Map</a></small>', 'gmap',
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
function king_urlupload($image_url)
{

	$MaxSize              = 800;
	$Quality              = 90;
	$DestinationDirectory = QA_INCLUDE_DIR . 'uploads/';
	preg_match('/[^\?]+\.(jpg|JPG|jpe|JPE|jpeg|JPEG|gif|GIF|png|PNG|webp|image)/', $image_url, $matches);

	if ( ! empty($matches[1]) ) {
		$url_type     = $matches[1];
		$url_filename = rand() . '.' . $url_type;
	} else {
		$url_type     = 'image/jpeg';
		$url_filename = rand() . '.jpg';
	}
	$opts = array('http'=>array('header' => "User-Agent:MyAgent/1.0\r\n")); 
	$context = stream_context_create( $opts );
	$res = file_get_contents( $image_url, false, $context );

	$image_data   = file_get_contents($image_url, false, $context);
	$year_folder  = $DestinationDirectory . date("Y");
	$month_folder = $year_folder . '/' . date("m");
	!file_exists($year_folder) && mkdir($year_folder, 0777);
	!file_exists($month_folder) && mkdir($month_folder, 0777);
	$DestFolder = $month_folder . '/' . $url_filename;
	file_put_contents($DestFolder, $image_data);
	list($CurWidth, $CurHeight) = getimagesize($DestFolder);
	if ($CurWidth <= 0 || $CurHeight <= 0) {
		return false;
	}
	switch (strtolower($url_type)) {
		case 'png':
			$CreatedImage = imagecreatefrompng($DestFolder);
			break;
		case 'gif':
			$CreatedImage = imagecreatefromgif($DestFolder);
			break;
		case 'jpeg':
		case 'jpg':
			$CreatedImage = imagecreatefromjpeg($DestFolder);
			break;
		default:
			die('Unsupported File!');
	}
	$ImageScale   = min($MaxSize / $CurWidth, $MaxSize / $CurWidth);
	$NewWidth     = ceil($ImageScale * $CurWidth);
	$NewHeight    = ceil($ImageScale * $CurHeight);
	$NewCanves    = imagecreatetruecolor($NewWidth, $NewHeight);
	if (imagecopyresampled($NewCanves, $CreatedImage, 0, 0, 0, 0, $NewWidth, $NewHeight, $CurWidth, $CurHeight)) {
		imagejpeg($NewCanves, $DestFolder, $Quality);
		if (is_resource($NewCanves)) {
			imagedestroy($NewCanves);
		}
		$path   = 'uploads/' . date("Y") . '/' . date("m") . '/' . $url_filename;
		$output = king_insert_uploads( $path, $url_type, $NewWidth, $NewHeight );
		return $output;

	}
}
function king_uploadthumb($ImageName, $TempSrc, $ImageType)
{
	$DestinationDirectory = QA_INCLUDE_DIR . 'uploads/';
	$MaxSize              = 800;
	$Quality              = 90;
	$watermark_png_file   = QA_INCLUDE_DIR . 'watermark/watermark.png';
	$RandomNumber         = rand(0, 999999);

	$NewImageName =  $RandomNumber . '-' . basename( $ImageName );
	$year_folder  = $DestinationDirectory . date("Y");
	$month_folder = $year_folder . '/' . date("m");

	!file_exists($year_folder) && mkdir($year_folder, 0777);
	!file_exists($month_folder) && mkdir($month_folder, 0777);

	$DestFolder = $month_folder . '/' . $NewImageName;

	switch (strtolower($ImageType)) {
		case 'image/png':
			$CreatedImage = imagecreatefrompng($TempSrc);
			break;
		case 'image/gif':
			$CreatedImage = imagecreatefromgif($TempSrc);
			break;
		case 'image/webp':
			$CreatedImage = imagecreatefromwebp($TempSrc);
			break;
		case 'image/jpeg':
		case 'image/pjpeg':
			$CreatedImage = imagecreatefromjpeg($TempSrc);
			break;
		default:
			die('Unsupported File!');
	}
	list($CurWidth, $CurHeight) = getimagesize($TempSrc);

	if ($CurWidth <= 0 || $CurHeight <= 0) {
		return false;
	}
	$ImageScale = min($MaxSize / $CurWidth, $MaxSize / $CurWidth);
	$NewWidth   = ceil($ImageScale * $CurWidth);
	$NewHeight  = ceil($ImageScale * $CurHeight);
	$NewCanves  = imagecreatetruecolor($NewWidth, $NewHeight);
	// Resize Image
	if (imagecopyresampled($NewCanves, $CreatedImage, 0, 0, 0, 0, $NewWidth, $NewHeight, $CurWidth, $CurHeight)) {
		if ( qa_opt( 'watermark_default_show' ) ) {
			$watermark        = imagecreatefrompng($watermark_png_file); //watermark image
			$watermark_width  = imagesx($watermark);
			$watermark_height = imagesy($watermark);
			$wposition = qa_opt('watermark_position');
			switch (strtolower($wposition)) {
				case 'topleft':
					$watermark_left = 0;
					$watermark_top  = 0;
					break;
				case 'topright':
					$watermark_left = $NewWidth - $watermark_width;
					$watermark_top = 0;
					break;
				case 'center':
					$watermark_left = ( $NewWidth / 2 ) - ( $watermark_width / 2 );
					$watermark_top = ( $NewHeight / 2 ) - ( $watermark_height / 2 );
					break;
				case 'bottomleft':
					$watermark_left = 0;
					$watermark_top = $NewHeight - $watermark_height;
					break;
				case 'bottomright':
					$watermark_left = $NewWidth - $watermark_width;
					$watermark_top = $NewHeight - $watermark_height;
					break;
				case 'bottomcenter':
					$watermark_left = ( $NewWidth / 2 ) - ( $watermark_width / 2 );
					$watermark_top = $NewHeight - $watermark_height;
					break;
				default:
					$watermark_left   = 10;
					$watermark_top = ($NewHeight / 2 ) - ($watermark_height / 2);
			}
			
			imagecopy($NewCanves, $watermark, $watermark_left, $watermark_top, 0, 0, $watermark_width, $watermark_height); //merge image
		}
		//Or Save image to the folder
		imagejpeg($NewCanves, $DestFolder, $Quality);

		//Destroy image, frees memory
		if (is_resource($NewCanves)) {
			imagedestroy($NewCanves);
		}
		$output['path']   = 'uploads/' . date("Y") . '/' . date("m") . '/' . $NewImageName;
		$output['id'] = king_insert_uploads( $output['path'], $ImageType, $NewWidth, $NewHeight );
		return $output;
	}
}
