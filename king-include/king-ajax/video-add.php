<?php
/*

File: king-include/king-ajax-click-wall.php
Description: Server-side response to Ajax single clicks on wall posts

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


require_once QA_INCLUDE_DIR . 'king-app/video.php';
$url    = qa_post_text('url');

$extraz = embed_replace( $url );
if ($extraz) {

	echo "QA_AJAX_RESPONSE\n1\n".$extraz;
}
