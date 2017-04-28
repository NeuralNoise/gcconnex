<?php

elgg_register_event_handler('init','system','test_init');

function test_init() {
	elgg_register_plugin_hook_handler('register','menu:owner_block','test_owner_block_menu');
	elgg_register_page_handler('test', 'test_page_handler');
}


function test_owner_block_menu($hook, $type, $return, $params) {
	$entity = elgg_extract('entity', $params);
	$item = new ElggMenuItem('forums', "Forums", "forums/group/{$entity->guid}");
	$return[] = $item;
	return $return;
}

function test_page_handler($page) {

	$page_type = $page[0];
	switch ($page_type) {

		case 'edit':
			break;

		case 'view':
			$params = test_params1();
			break;

		case 'group':

			$group = get_entity($page[1]);
			if (!elgg_instanceof($group, 'group')) {
				forward('','404');
			}

			$params = test_params($group);			
			break;

		default:
			return false;
	}

	$body = elgg_view_layout('content', $params);
	echo elgg_view_page($params['title'], $body);
}

/// renders the first page of the forums
function test_params($group) {

	elgg_group_gatekeeper();

	// breadcrumb: push the group entity in first, then the forums (without link)
	elgg_push_breadcrumb($group->name, $group->getURL());
	elgg_push_breadcrumb('Group Forums');

	elgg_load_css('gcforums-css');
	$categories = elgg_get_entities(array(
		'types' => 'object',
		'subtypes' => 'hjforumcategory',
		'limit' => false,
		'container_guid' => $group->getGUID()
	));

	$content = "<div class='gcforums-container'>";


	foreach ($categories as $category) {

		// get the forums that are filed under the category
		$forums = elgg_get_entities_from_relationship(array(
			'relationship' => 'filed_in',
			'relationship_guid' => $category->getGUID(),
			'container_guid' => $group->getGUID(),
			'inverse_relationship' => true,
			'limit' => false
		));

		$content .= "<div class='gcforums-category-container'>";
		$content .= "	<div class='gcforums-category'> <h1> {$category->title}</h1> </div>";
		$content .= "	<div class='gcforums-options'> placeholder:category-options </div>";
		$content .= "</div>";

		$content .= "<hr>";


		foreach ($forums as $forum) {
			$hyperlink = elgg_get_site_url()."gcforums/group/{$group_guid}/{$forum->guid}";

			$content .= "<div class='gcforums-forum-container'>";
			$content .= "	<div class='gcforums-forum'> <strong> <a href='{$hyperlink}'> {$forum->title} </a> </strong> </div>";
			$content .= "	<div class='gcforums-forum-stats'> 000 </div>";
			$content .= "	<div class='gcforums-forum-stats'> 000 </div>";
			$content .= "	<div class='gcforums-forum-stats'> 000 </div>";
			$content .= "	<div class='gcforums-options'> ph:options:forum-edit </div>";
			$content .= "</div>";

			$content .= "<hr>";

		}
	}

	$content .= "</div>";


	return array(
		'content' => $content,
		'title' => "{$group->name}'s Forum",
		'filter' => '',
	);

}

/// renders everything after the first forums (displays nested forums)
function test_params1() {
	// forums will always remain as content within a group
	elgg_set_page_owner_guid(334);
	$return = array();
	$return['filter'] = '';
	$return['title'] = "title of the content";
	$return['content'] = "body of the content";

	return $return;
}
