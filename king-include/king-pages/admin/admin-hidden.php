<?php
/*

	File: king-include/king-page-admin-hidden.php
	Description: Controller for admin page showing hidden questions, answers and comments


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
	require_once QA_INCLUDE_DIR.'king-db/admin.php';
	require_once QA_INCLUDE_DIR.'king-db/selects.php';
	require_once QA_INCLUDE_DIR.'king-app/format.php';


//	Find recently hidden questions, answers, comments

	$userid=qa_get_logged_in_userid();

	list($hiddenquestions, $hiddenanswers, $hiddencomments)=qa_db_select_with_pending(
		qa_db_qs_selectspec($userid, 'created', 0, null, null, 'Q_HIDDEN', true),
		qa_db_recent_a_qs_selectspec($userid, 0, null, null, 'A_HIDDEN', true),
		qa_db_recent_c_qs_selectspec($userid, 0, null, null, 'C_HIDDEN', true)
	);


//	Check admin privileges (do late to allow one DB query)

	if (qa_user_maximum_permit_error('permit_hide_show') && qa_user_maximum_permit_error('permit_delete_hidden')) {
		$qa_content=qa_content_prepare();
		$qa_content['error']=qa_lang_html('users/no_permission');
		return $qa_content;
	}


//	Check to see if any have been reshown or deleted

	$pageerror=qa_admin_check_clicks();


//	Combine sets of questions and remove those this user has no permissions for

	$questions=qa_any_sort_by_date(array_merge($hiddenquestions, $hiddenanswers, $hiddencomments));

	if (qa_user_permit_error('permit_hide_show') && qa_user_permit_error('permit_delete_hidden')) // not allowed to see all hidden posts
		foreach ($questions as $index => $question)
			if (qa_user_post_permit_error('permit_hide_show', $question) && qa_user_post_permit_error('permit_delete_hidden', $question))
				unset($questions[$index]);


//	Get information for users

	$usershtml=qa_userids_handles_html(qa_any_get_userids_handles($questions));


//	Create list of actual hidden postids and see which ones have dependents

	$qhiddenpostid=array();
	foreach ($questions as $key => $question)
		$qhiddenpostid[$key]=isset($question['opostid']) ? $question['opostid'] : $question['postid'];

	$dependcounts=qa_db_postids_count_dependents($qhiddenpostid);


//	Prepare content for theme

	$qa_content=qa_content_prepare();

	$qa_content['title']=qa_lang_html('admin/recent_hidden_title');
	$qa_content['error']=isset($pageerror) ? $pageerror : qa_admin_page_error();

	$html = '<form method="post" action="'.qa_self_html().'">';
	
	$html .= '<table class="editusers-table">';

	if (count($questions)) {
		foreach ($questions as $key => $question) {
			$elementid='p'.$qhiddenpostid[$key];

			$htmloptions=qa_post_html_options($question);
			$htmloptions['voteview']=false;
			$htmloptions['tagsview']=!isset($question['opostid']);
			$htmloptions['answersview']=false;
			$htmloptions['viewsview']=false;
			$htmloptions['updateview']=false;
			$htmloptions['contentview']=true;
			$htmloptions['flagsview']=true;
			$htmloptions['elementid']=$elementid;

			$htmlfields=qa_any_to_q_html_fields($question, $userid, qa_cookie_get(), $usershtml, null, $htmloptions);

			if (isset($htmlfields['what_url'])) {
				$htmlfields['url'] = $htmlfields['what_url'];
			}
			$acontent = isset($htmlfields['raw']['ocontent']) ? $htmlfields['raw']['ocontent'] : '';
			$type     = isset($htmlfields['raw']['obasetype']) ? $htmlfields['raw']['obasetype'] : $htmlfields['raw']['basetype'];
			$author   = isset($htmlfields['who']['data']) ? $htmlfields['who']['data'] : $htmlfields['who']['handle'];
			$posttype = qa_strtolower(isset($question['obasetype']) ? $question['obasetype'] : $question['basetype']);
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
			$html .= '<tr class="kingeditli" id="p' . $qhiddenpostid[$key] . '">';
			$html .= '<td><strong>' . qa_html($typet) . '</strong></td>';
			$html .= '<td>' . qa_sanitize_html($author) . '</td>';
			$html .= '<td><strong>' . qa_html($question['title']) . '</strong><div>' . qa_html($acontent) . '</div></td>';
			$html .= '<td><a href="' . qa_html($htmlfields['url']) . '" class="king-edit-button" target="_blank"><i class="fas fa-external-link-alt"></i></a></td>';
			if (!qa_user_post_permit_error('permit_hide_show', $question)) {
				$html .= '<td><input name="admin_' . qa_html($qhiddenpostid[$key]) . '_reshow" onclick="return qa_admin_click(this);" value="' . qa_lang_html('question/reshow_button') . '" title="[question/approve_q_popup]" type="submit" class="king-edit-button"></td>';
			}
			if ((!qa_user_post_permit_error('permit_delete_hidden', $question)) && !$dependcounts[$qhiddenpostid[$key]]) {
				$html .= '<td><input name="admin_' . qa_html($qhiddenpostid[$key]) . '_delete" onclick="return qa_admin_click(this);" value="' . qa_lang_html('question/delete_button') . '" type="submit" class="king-edit-button"></td>';
			}
			$html .= '</tr>';

			

		}

	} else {
		$qa_content['title']=qa_lang_html('admin/no_hidden_found');
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