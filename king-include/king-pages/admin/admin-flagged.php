<?php
/*

	File: king-include/king-page-admin-flagged.php
	Description: Controller for admin page showing posts with the most flags


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

	require_once QA_INCLUDE_DIR.'king-app/admin.php';
	require_once QA_INCLUDE_DIR.'king-db/selects.php';
	require_once QA_INCLUDE_DIR.'king-app/format.php';


//	Find most flagged questions, answers, comments

	$userid=qa_get_logged_in_userid();

	$questions=qa_db_select_with_pending(
		qa_db_flagged_post_qs_selectspec($userid, 0, true)
	);


//	Check admin privileges (do late to allow one DB query)

	if (qa_user_maximum_permit_error('permit_hide_show')) {
		$qa_content=qa_content_prepare();
		$qa_content['error']=qa_lang_html('users/no_permission');
		return $qa_content;
	}


//	Check to see if any were cleared or hidden here

	$pageerror=qa_admin_check_clicks();


//	Remove questions the user has no permission to hide/show

	if (qa_user_permit_error('permit_hide_show')) // if user not allowed to show/hide all posts
		foreach ($questions as $index => $question)
			if (qa_user_post_permit_error('permit_hide_show', $question))
				unset($questions[$index]);


//	Get information for users

	$usershtml=qa_userids_handles_html(qa_any_get_userids_handles($questions));


//	Prepare content for theme

	$qa_content=qa_content_prepare();

	$qa_content['title']=qa_lang_html('admin/most_flagged_title');
	$qa_content['error']=isset($pageerror) ? $pageerror : qa_admin_page_error();

	$html = '<form method="post" action="'.qa_self_html().'">';
	
	$html .= '<table class="editusers-table">';


	if (count($questions)) {
		foreach ($questions as $question) {
			$postid=qa_html(isset($question['opostid']) ? $question['opostid'] : $question['postid']);
			$elementid='p'.$postid;

			$htmloptions=qa_post_html_options($question);
			$htmloptions['voteview']=false;
			$htmloptions['tagsview']=($question['obasetype']=='Q');
			$htmloptions['answersview']=false;
			$htmloptions['viewsview']=false;
			$htmloptions['contentview']=false;
			$htmloptions['flagsview']=true;
			$htmloptions['elementid']=$elementid;

			$htmlfields=qa_any_to_q_html_fields($question, $userid, qa_cookie_get(), $usershtml, null, $htmloptions);

			if (isset($htmlfields['what_url'])) {
				$htmlfields['url'] = $htmlfields['what_url'];
			}
			$acontent = isset($htmlfields['raw']['ocontent']) ? $htmlfields['raw']['ocontent'] : '';
			$type     = isset($htmlfields['raw']['obasetype']) ? $htmlfields['raw']['obasetype'] : $htmlfields['raw']['basetype'];
			$author   = isset($htmlfields['who']['data']) ? $htmlfields['who']['data'] : $htmlfields['who']['handle'];

			switch ($type) {
				case 'A':
					$typet = qa_lang_html('misc/m_comment');
					break;
				case 'C':
					$typet = qa_lang_html('misc/m_reply');
					break;
				default:
					$typet = qa_lang_html('misc/m_post');
					break;
			}
			$html .= '<tr class="kingeditli" id="p' . $postid . '">';
			$html .= '<td><strong>' . qa_html($typet) . '</strong></td>';
			$html .= '<td>' . qa_sanitize_html($author) . '</td>';
			$html .= '<td><strong>' . qa_html($question['title']) . '</strong><div>' . qa_html($acontent) . '</div></td>';
			$html .= '<td><a href="' . qa_html($htmlfields['url']) . '" class="king-edit-button" target="_blank"><i class="fas fa-external-link-alt"></i></a></td>';
			$html .= '<td><input name="admin_' . $postid . '_clearflags" onclick="return qa_admin_click(this);" value="' . qa_lang_html('question/clear_flags_button') . '" type="submit" class="king-edit-button"></td>';
			$html .= '<td><input name="admin_' . $postid . '_hide" onclick="return qa_admin_click(this);" value="' . qa_lang_html('question/hide_button') . '" type="submit" class="king-edit-button"></td>';
			$html .= '</tr>';

		}

	} else {
		$qa_content['title']=qa_lang_html('admin/no_flagged_found');
	}

	$html .= '<input type="hidden" name="code" value="'.qa_get_form_security_code('admin/click').'">';
	$html .= '</table>';
	$html .= '</form>';

	$qa_content['custom'] = $html;
	$qa_content['navigation']['sub']=qa_admin_sub_navigation();
	$qa_content['script_rel'][]='king-content/king-admin.js?'.QA_VERSION;


	return $qa_content;


/*
	Omit PHP closing tag to help avoid accidental output
*/