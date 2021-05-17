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

require_once QA_INCLUDE_DIR . 'king-app/users.php';
require_once QA_INCLUDE_DIR . 'king-app/limits.php';
require_once QA_INCLUDE_DIR . 'king-db/selects.php';

$pid    = qa_post_text('id');
$pollid = qa_post_text('pid');
if (qa_is_logged_in()) {
	$userid = qa_get_logged_in_userid();
} else {
	$userid = qa_remote_ip_address();
}
$query  = qa_db_read_one_value(qa_db_query_sub('SELECT answers FROM ^poll WHERE id=$ ', $pollid));
$result = unserialize($query);
if (is_array($result) && array_key_exists($userid, $result)) {

	echo "QA_AJAX_RESPONSE\n0\n";

} else {
	if (count($result) !== 0) {
		$king_voters = $result;
	}
	if (!is_array($king_voters)) {
		$king_voters = array();
	}
	if (!array_key_exists($userid, $king_voters)) {
		$king_voters[$userid] = $pid;
		$king_voters2         = serialize($king_voters);
	}
	qa_db_query_sub('UPDATE ^poll SET answers=$ WHERE id=#', $king_voters2, $pollid);


	echo "QA_AJAX_RESPONSE\n1\n";
}
