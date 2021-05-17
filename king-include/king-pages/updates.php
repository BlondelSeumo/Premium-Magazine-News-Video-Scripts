<?php
/*

File: king-include/king-page-updates.php
Description: Controller for page listing recent updates for a user

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

require_once QA_INCLUDE_DIR . 'king-db/selects.php';
require_once QA_INCLUDE_DIR . 'king-app/format.php';
require_once QA_INCLUDE_DIR . 'king-app/users.php';
require_once QA_INCLUDE_DIR . 'king-app/q-list.php';

//    Check that we're logged in

$userid = qa_get_logged_in_userid();

if (!isset($userid)) {
    qa_redirect('login');
}

$users = qa_db_select_with_pending(
    qa_db_user_favorite_users_selectspec($userid, '16')
);
//    Find out which updates to show

$forfavorites = qa_get('show') != 'content';
$forcontent   = qa_get('show') != 'favorites';
$html         = '';
if (count($users)) {
    $html .= '<div class="discover-users">';
    foreach ($users as $user) {
        $html .= '<a href="' . qa_path_html('user/' . $user['handle']) . '" data-toggle="tooltip" data-placement="top" title="'.qa_html($user['handle']).'"><img src="' . get_avatar($user['avatarblobid'], 100) . '" /></a>';
    }
    $html .= '</div>';
}

//    Get lists of recent updates for this user
$start = qa_get_start();
$questions = qa_db_select_with_pending(
    qa_db_user_updates_selectspec($userid, 20, $start)
);

if ($forfavorites) {

    $sometitle = qa_lang_html('misc/recent_updates_favorites') . $html;
    $nonetitle = qa_lang_html('misc/no_updates_favorites') . $html;

} else {
    $sometitle = qa_lang_html('misc/recent_updates_content');
    $nonetitle = qa_lang_html('misc/no_updates_content');
}

//    Prepare and return content for theme

$qa_content = qa_q_list_page_content(
    $questions,
    20, // questions per page
    $start, // start offset
    qa_opt('cache_qcount'), // total count (null to hide page links)
    $sometitle, // title if some questions
    $nonetitle, // title if no questions
    null, // categories for navigation
    null, // selected category id
    null, // show question counts in category navigation
    null, // prefix for links in category navigation
    null, // prefix for RSS feed paths (null to hide)
    qa_lang_html('main/suggest_follow')// suggest what to do next
);

return $qa_content;

/*
Omit PHP closing tag to help avoid accidental output
 */
