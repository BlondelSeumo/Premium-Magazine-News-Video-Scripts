<?php
/*

File: king-include/king-page-categories.php
Description: Controller for page listing categories

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

$categoryslugs = qa_request_parts(1);
$countslugs    = count($categoryslugs);
$userid = qa_get_logged_in_userid();

list($categories, $categoryid, $favoritecats) = qa_db_select_with_pending(
	qa_db_category_nav_selectspec($categoryslugs, false, false, true),
	$countslugs ? qa_db_slugs_to_category_id_selectspec($categoryslugs) : null,
	isset($userid) ? qa_db_user_favorite_categories_selectspec($userid) : null
);

if ($countslugs && !isset($categoryid)) {
	return include QA_INCLUDE_DIR . 'king-page-not-found.php';
}

//    Function for recursive display of categories
function qa_subcategory_nav_to_browse(&$navigation, $categories, $categoryid, $favoritemap)
{
	$html = '<div class="king-subbrowse-cat">';
	foreach ($navigation as $key => $navlink) {
		$category = $categories[$navlink['categoryid']];
		$html .= '<a class="king-subbrowse-cat-item" href="'.qa_path_html('' . implode('/', array_reverse(explode('/', $category['backpath'])))).'" >'.$category['title'].'</a> ';
	}
	$html .= '</div>';
	return $html;
}		

function qa_category_nav_to_browse(&$navigation, $categories, $categoryid, $favoritemap)
{
	$html = '<div class="king-browse-cat-list">';
	foreach ($navigation as $key => $navlink) {
		$category = $categories[$navlink['categoryid']];
		$html .= '<div class="king-browse-cat-item" '. ( ($category['color']) ? 'style="background-color: '.$category['color'].';"' : '' ) .'>';
		$html .= '<span class="king-cat-icon">' . ( ($category['icon']) ? $category['icon'] : '' ) . '</span>';
		$html .= '<a href="'.qa_path_html('' . implode('/', array_reverse(explode('/', $category['backpath'])))).'" >'.$category['title'].'</a>';
		$html .= '<span class="king-cat-num">'.( ($category['qcount']==1) ? qa_lang_html_sub('main/1_question', '1', '1') : qa_lang_html_sub('main/x_questions', number_format($category['qcount']))).'</span>';
		$html .= '<span class="king-cat-desc">'.qa_html( $category['content'] ).'</span>';
		if (isset($navlink['subnav'])) {
			$html .= qa_subcategory_nav_to_browse($navigation[$key]['subnav'], $categories, $categoryid, $favoritemap);
		}
		$html .= '</div>';

	}
	$html .= '</div>';
	return $html;
}

//    Prepare content for theme

$qa_content = qa_content_prepare(false, array_keys(qa_category_path($categories, $categoryid)));

$qa_content['title'] = qa_lang_html('misc/browse_categories');

if (count($categories)) {
	$navigation = qa_category_navigation($categories, $categoryid, 'categories/', false);

	unset($navigation['all']);

	$favoritemap = array();
	if (isset($favoritecats)) {
		foreach ($favoritecats as $category) {
			$favoritemap[$category['categoryid']] = true;
		}
	}

	$qa_content['custom']  = qa_category_nav_to_browse($navigation, $categories, $categoryid, $favoritemap);



} else {
	$qa_content['title']        = qa_lang_html('main/no_categories_found');
	$qa_content['suggest_next'] = qa_html_suggest_qs_tags(qa_using_tags());
}

return $qa_content;

/*
Omit PHP closing tag to help avoid accidental output
 */
