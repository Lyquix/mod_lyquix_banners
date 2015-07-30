<?php
defined( '_JEXEC' ) or die( 'Restricted access' );

class modLyquixBannersHelper {
	static function getItems(&$params) {

		// Load libraries
		require_once("components/com_flexicontent/classes/flexicontent.helper.php");
		require_once("components/com_flexicontent/helpers/permission.php");
		
		// Initialize
		global $mainframe;
		$mainframe = JFactory::getApplication();
		$db = JFactory::getDBO();
		$user = JFactory::getUser();
				
		// get null date and now
		$nullDate	= $db->getNullDate();
		$date = JFactory::getDate();
		$now = $date->toSql();

		// build query
		$query = "SELECT c.id FROM #__content AS c, #__flexicontent_cats_item_relations as fcir, #__flexicontent_items_ext as fie WHERE c.id = fcir.itemid AND c.id = fie.item_id AND c.state = 1 AND c.publish_up < '".$now."' AND (c.publish_down = '0000-00-00 00:00:00' OR c.publish_down > '".$now."')";
		
		// category
		if($params->get('catid')) {
			$query .= " AND fcir.catid = ".$params->get('catid');
		}
		
		// type
		if($params->get('typid')) {
			$query .= " AND fie.type_id = ".$params->get('typeid');
		}
		
		// language scope
		if($params->get('lang_scope',1)) {
			$lang = flexicontent_html::getUserCurrentLang();
			$query .= " AND (fie.language = '*' OR fie.language LIKE '".$lang."%')";
		}
		
		// remove unauthorized items
		$gid = !FLEXI_J16GE ? (int)$user->get('aid') : max($user->getAuthorisedViewLevels());
		if(FLEXI_ACCESS && class_exists('FAccess')) {
			$readperms = FAccess::checkUserElementsAccess($user->gmid, 'read');
			if (isset($readperms['item']) && count($readperms['item']) ) {
				$query .= " AND (c.access <= ".$gid." OR c.id IN (".implode(",", $readperms['item'])."))";
			} 
			else {
				$query .= " AND c.access <= ".$gid;
			}
		} 
		else {
			$query .= " AND c.access <= ".$gid;
		}
		
		// add ordering
		$query .= " ORDER BY ".$params->get('ordering', 'fcir.ordering');

		// Execute query
		$db->setQuery($query, 0);
		$items = $db->loadColumn();
		return array_slice($items, 0, $params->get('count',5), true);
	}
}