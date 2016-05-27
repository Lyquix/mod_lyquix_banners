<?php
/**
 * @license GNU/GPL v2
 * @copyright  Copyright (c) Lyquix. All rights reserved.
 */
defined('_JEXEC') or die('Restricted access');

// display only banners if any available
if (count($banners)) {

	// load required FLEXIcontent libraries
	require_once (JPATH_ADMINISTRATOR . DS . 'components/com_flexicontent/defineconstants.php');
	JTable::addIncludePath(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_flexicontent' . DS . 'tables');
	require_once ("components/com_flexicontent/classes/flexicontent.fields.php");
	require_once ("components/com_flexicontent/classes/flexicontent.helper.php");
	require_once ("components/com_flexicontent/helpers/permission.php");
	require_once("components/com_flexicontent/helpers/route.php");	
	require_once ("components/com_flexicontent/models/" . FLEXI_ITEMVIEW . ".php");
	$itemobjects = array();
	//Check to see if banners are repeated on the current page so that they can be loaded more than once
	foreach ($banners as $id) {
		if (array_key_exists($id, $itemobjects)) {
			continue;
		} else {
			$itemmodel = new FlexicontentModelItem();
			$item = $itemmodel -> getItem($id, false);
			$itemslist = array($item);
			$itemobjects[$id] = $item;
		}
	}
	// banner module wrapper and module pre-text
	echo '<div class="module mod_lyquix_banners ' . $params -> get('moduleclass_sfx') . ' banner' . $module -> id . '">';
	echo $params -> get('modpretxt');

	// banner counter, starts at 1
	$count = 1;
	$tabs_html = '';
	$itemmodel = new FlexicontentModelItem();

	// banners wrapper
	echo '<div class="banners" data-num="1" data-total="' . count($banners) . '">';

	foreach ($banners as $banner_item) {
		// process item
		$item = $itemobjects[$banner_item];
		$items = array($item);
		FlexicontentFields::getFields($items);
		// banner wrapper
		echo '<div class="banner ' . $params -> get('banner_class', '');

		// add class from style field
		if ($params -> get('style_field') != '') {
			FlexicontentFields::getFieldDisplay($item, $params -> get('style_field'));
			if (array_key_exists($params -> get('style_field'), $item -> fields)) {
				if (array_key_exists($item -> fields[$params -> get('style_field')] -> id, $item -> fieldvalues)) {
					if ($item -> fieldvalues[$item -> fields[$params -> get('style_field')] -> id]) {
						echo ' ' . $item -> fields[$params -> get('style_field')] -> display;
					}
				}
			}
		}

		// only the first banner is visible on load
		echo '"' . ($count > 1 ? ' style="display:none"' : '');

		// attributes for keep track of current and total banners
		echo ' data-num="' . $count . '" data-total="' . count($banners) . '">';

		// add pre-text for banner
		echo $params -> get('itempretxt');

		// render main image
		if ($params -> get('image_display', 1) && $params -> get('image_field') != '') {
			FlexicontentFields::getFieldDisplay($item, $params -> get('image_field'));
			if ($item -> fieldvalues[$item -> fields[$params -> get('image_field')] -> id]) {

				// Unserialize value's properties and check for empty original name property
				$value = unserialize($item -> fieldvalues[$item -> fields[$params -> get('image_field')] -> id][0]);
				$image_name = trim(@$value['originalname']);

				if (strlen($image_name)) {

					$field = $item -> fields[$params -> get('image_field')];
					$field -> parameters = json_decode($field -> attribs, true);
					$image_source = $field -> parameters['image_source'];
					$dir_url = str_replace('\\', '/', $field -> parameters['dir']);
					$multiple_image_usages = !$image_source && $field -> parameters['list_all_media_files'] && $field -> parameters['unique_thumb_method'] == 0;
					$extra_prefix = $multiple_image_usages ? 'fld' . $field -> id . '_' : '';
					$of_usage = $field -> untranslatable ? 1 : $field -> parameters['of_usage'];
					$u_item_id = ($of_usage && $item -> lang_parent_id && $item -> lang_parent_id != $item -> id) ? $item -> lang_parent_id : $item -> id;
					$extra_folder = '/item_' . $u_item_id . '_field_' . $field -> id;

					if ($params -> get('image_size') == 'custom') {

						// get the original image file path
						$image_file = JURI::root(true) . '/';

						// supports only db mode and item-field folder mode
						if ($image_source == 0) {
							// db mode
							$cparams = JComponentHelper::getParams('com_flexicontent');
							$image_file .= str_replace('\\', '/', $cparams -> get('file_path', 'components/com_flexicontent/uploads'));
						} else if ($image_source == 1) {
							// item+field specific folder
							$image_file .= $dir_url . $extra_folder . '/original';
						}

						// custom size image
						$image_file .= '/' . $image_name;

						$conf = '&amp;w=' . $params -> get('image_width', 960) . '&amp;h=' . $params -> get('image_height', 540) . '&amp;aoe=1&amp;q=95';
						$conf .= $params -> get('image_resize', 1) ? '&amp;zc=1' : '';
						$ext = strtolower(pathinfo($image_file, PATHINFO_EXTENSION));
						$conf .= in_array($ext, array('png', 'ico', 'gif')) ? '&amp;f=' . $ext : '';

						$src = JURI::root(true) . '/components/com_flexicontent/librairies/phpthumb/phpThumb.php?src=' . urlencode($image_file) . $conf;

					} else {

						// Create thumbs URL path
						$src = JURI::root(true) . '/' . $dir_url;

						// Extra thumbnails sub-folder
						if ($image_source == 1) {
							// item+field specific folder
							$src .= $extra_folder;
						}

						$src .= '/' . $params -> get('image_size', 's') . '_' . $extra_prefix . $image_name;

					}

					$bannerSelector = 'banner-' . $module -> id;

					// open image div
					echo '<div class="image ' . $params -> get('image_class') . '">';

					$video_html ='';
					//if the item has main video set
					if ($items[0] -> fieldvalues [$params -> get('sharedmediaid')][0]){
					    $video_path = unserialize($items[0] -> fieldvalues [$params -> get('sharedmediaid')][0])['url'];
					    $youtube_id = '';
					    if (preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $video_path, $match)) {
					            $youtube_id = $match[1];
				        } else if (preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|w(?:atch)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $video_path, $match)) {
				            $youtube_id = $match[1];
				        }

					    if($youtube_id != ''){
					        $video_html .='<div id="' . $bannerSelector . '"><a class="video-icon" data-featherlight="iframe" href="https://www.youtube.com/embed/'.$youtube_id. '?rel=0&amp;autoplay=1" title="'.$item->title.'"></a></div>';

					        $video_html .="<script>
					                    //call the jquery to initialize featherlight gallery
					                    jQuery(document).ready(function(){
					                        jQuery('#" . $bannerSelector ."').featherlight();
					                    });
					                </script>";
					    }
					}

					echo $video_html;

					// image clickable?
					if ($params -> get('link_display', 1)) {
						// construct the link

						// if item has custom link field, use the custom field value
						if ($params -> get('link_url') == 'custom' && $params -> get('link_field') != '') {
							// get the item fields and its values
							FlexicontentFields::getFieldDisplay($item, $params -> get('link_field'));

							// check to intercept condition where the link value is in 2 different places, either in the fields, or the fieldvalues.
							// if the value is in the fields 
							if ($item -> fields[$params -> get('link_field')] -> value[0])
								$link = $item -> fields[$params -> get('link_field')] -> value[0];
							// if not, get the fieldvalues
							else 
								$link = $item -> fieldvalues[$item -> fields[$params -> get('link_field')] -> id][0];
						// if the item has no custom field as links, construct the link from JRoute	
						} else {
							$link = JRoute::_(FlexicontentHelperRoute::getItemRoute($item -> slug, $item -> categoryslug));
						}


						if ($link != '')
							echo '<a href="' . $link . '">';
					}

					echo '<img src="' . $src . '" alt="' . htmlspecialchars(@$value['alt'], ENT_COMPAT, 'UTF-8') . '" />';

					if ($link != '')
						echo '</a>';

					// close image div
					echo '</div>';
				}

			}
		}

		// render title and description
		if ($params -> get('title_display', 1) || $params -> get('description_display', 1)) {

			// open text div
			echo '<div class="text ' . $params -> get('text_class') . '">';

			// render title
			if ($params -> get('title_display', 1)) {
				$item_title = $item -> title;
				if ($params -> get('title_field', '') != '0') {
					FlexicontentFields::getFieldDisplay($item, $params -> get('title_field'));
					if ($item -> fields[$params -> get('title_field')]) {
						FlexicontentFields::getFieldDisplay($item, $params -> get('title_field'), $values = null, $method = 'display');
						$item_title = $item -> fields[$params -> get('title_field')] -> display;
					}
				}
				echo '<h' . $params -> get('title_heading_level', 3) . ' class="title">' . $item_title . '</h' . $params -> get('title_heading_level', 3) . '>';
			}

			// render description
			if ($params -> get('description_display', 1)) {
				if ($params -> get('description_field', '') != '' && $params -> get('description_field', '') != '0') {
					FlexicontentFields::getFieldDisplay($item, $params -> get('description_field'));
					if ($item -> fields[$params -> get('description_field')]) {
						FlexicontentFields::getFieldDisplay($item, $params -> get('description_field'), $values = null, $method = 'display');
						$item_description = $item -> fields[$params -> get('description_field')] -> display;
					}
				} else {
					$item_description = $item -> text;
				}
				if ($params -> get('description_striptags', 0)) {
					$item_description = strip_tags($item_description);
				}
				if ($item_description)
					echo '<div class="description">' . $item_description . '</div>';
			}

			// render read more link
			if ($params -> get('readmore_display', 1)) {
				if ($params -> get('readmore_url') == 'custom' && $params -> get('readmore_field') != '') {
					FlexicontentFields::getFieldDisplay($item, $params -> get('link_field'));
					if (array_key_exists(0, $item -> fields[$params -> get('readmore_field')] -> value)) {
						$link = $item -> fields[$params -> get('readmore_field')] -> value[0];
					}
				} else {
					$link = JRoute::_(FlexicontentHelperRoute::getItemRoute($item -> slug, $item -> categoryslug));
				}
				if ($link != '')
					echo '<a class="' . $params -> get('readmore_css', 'readmore') . '" href="' . $link . '">' . $params -> get('readmore_label', 'Read more') . '</a>';
			}

			// close text div
			echo '</div>';

		}

		// render caption
		if ($params -> get('caption_display', 1)) {
			FlexicontentFields::getFieldDisplay($item, $params -> get('caption_field'));
			if ($item -> fields[$params -> get('caption_field')]) {
				FlexicontentFields::getFieldDisplay($item, $params -> get('caption_field'), $values = null, $method = 'display');
				$item_caption = $item -> fields[$params -> get('caption_field')] -> display;
				if ($item_caption != '')
					echo '<div class="caption ' . $params -> get('caption_class') . '">' . $item_caption . '</div>';
			}
		}

		// banner post-text and close wrapper
		echo $params -> get('itempostxt');
		echo '</div>';

		// banner tab: append to $tabs_html for rendering later
		$tabs_html .= '<div class="tab ' . $params -> get('tab_class', '') . ($count == 1 ? ' on' : '') . '" data-num="' . $count . '" data-total="' . count($banners) . '">';

		// add tab thumnail image
		if ($params -> get('thumb_display', 0) && $params -> get('thumb_field') != '') {
			FlexicontentFields::getFieldDisplay($item, $params -> get('thumb_field'));
			if ($item -> fieldvalues[$item -> fields[$params -> get('thumb_field')] -> id]) {

				// Unserialize value's properties and check for empty original name property
				$value = unserialize($item -> fieldvalues[$item -> fields[$params -> get('thumb_field')] -> id][0]);
				$image_name = trim(@$value['originalname']);

				if (strlen($image_name)) {

					$field = $item -> fields[$params -> get('thumb_field')];
					$field -> parameters = json_decode($field -> attribs, true);
					$image_source = $field -> parameters['image_source'];
					$dir_url = str_replace('\\', '/', $field -> parameters['dir']);
					$multiple_image_usages = !$image_source && $field -> parameters['list_all_media_files'] && $field -> parameters['unique_thumb_method'] == 0;
					$extra_prefix = $multiple_image_usages ? 'fld' . $field -> id . '_' : '';
					$of_usage = $field -> untranslatable ? 1 : $field -> parameters['of_usage'];
					$u_item_id = ($of_usage && $item -> lang_parent_id && $item -> lang_parent_id != $item -> id) ? $item -> lang_parent_id : $item -> id;
					$extra_folder = '/item_' . $u_item_id . '_field_' . $field -> id;

					if ($params -> get('thumb_size') == 'custom') {

						// get the original image file path
						$image_file = JURI::root(true) . '/';

						// supports only db mode and item-field folder mode
						if ($image_source == 0) {
							// db mode
							$cparams = JComponentHelper::getParams('com_flexicontent');
							$image_file .= str_replace('\\', '/', $cparams -> get('file_path', 'components/com_flexicontent/uploads'));
						} else if ($image_source == 1) {
							// item+field specific folder
							$image_file .= $dir_url . $extra_folder . '/original';
						}

						// custom size image
						$image_file .= '/' . $image_name;

						$conf = '&amp;w=' . $params -> get('thumb_width', 960) . '&amp;h=' . $params -> get('thumb_height', 540) . '&amp;aoe=1&amp;q=95';
						$conf .= $params -> get('thumb_resize', 1) ? '&amp;zc=1' : '';
						$ext = strtolower(pathinfo($image_file, PATHINFO_EXTENSION));
						$conf .= in_array($ext, array('png', 'ico', 'gif')) ? '&amp;f=' . $ext : '';

						$src = JURI::root(true) . '/components/com_flexicontent/librairies/phpthumb/phpThumb.php?src=' . urlencode($image_file) . $conf;

					} else {

						// Create thumbs URL path
						$src = JURI::root(true) . '/' . $dir_url;

						// Extra thumbnails sub-folder
						if ($image_source == 1) {
							// item+field specific folder
							$src .= $extra_folder;
						}

						$src .= '/' . $params -> get('thumb_size', 's') . '_' . $extra_prefix . $image_name;

					}

					// render thumbnail div
					echo '<div class="thumb ' . $params -> get('thumb_class') . '">';
					echo '<img src="' . $src . '" alt="' . htmlspecialchars(@$value['alt'], ENT_COMPAT, 'UTF-8') . '" />';
					echo '</div>';
				}
			}
		}

		// add tab thumbnail title
		if ($params -> get('thumb_title_display', 0)) {
			$item_title = $item -> title;
			if ($params -> get('thumb_title_field', '') != '0') {
				FlexicontentFields::getFieldDisplay($item, $params -> get('thumb_title_field'));
				if ($item -> fields[$params -> get('thumb_title_field')]) {
					FlexicontentFields::getFieldDisplay($item, $params -> get('thumb_title_field'), $values = null, $method = 'display');
					$item_title = $item -> fields[$params -> get('thumb_title_field')] -> display;
				}
			}
			$tabs_html .= '<span>' . $item_title . '</span>';
		}

		// close banner tab
		$tabs_html .= '</div>';

		// increase banner counter
		$count++;
	}

	// close banners wrapper
	echo '</div>';

	// render arrows divs
	echo '<div class="arrows"><div class="prev ' . $params -> get('arrow_prev_class') . '"></div><div class="next ' . $params -> get('arrow_next_class') . '"></div></div>';

	// render tabs
	echo '<div class="tabs" data-num="1" data-total="' . count($banners) . '">' . $tabs_html . '</div>';

	// render counter div
	echo '<div class="counter"><span class="current ' . $params -> get('counter_current_class') . '">1</span> of <span class="total ' . $params -> get('counter_total_class') . '">' . count($banners) . '</span></div>';

	// module post-text
	echo $params -> get('modpostxt');

	// close module wrapper
	echo '</div>';

	// generate js array containing delay and speed parameters
	$js_vars = '{delay: ' . ($params -> get('delay_time', 5) * 1000) . ", banner: '.banner', tab: '.tab', speed: " . $params -> get('fade_time', 400) . '}';

	// render the necessary javascript code
	if ($params -> get('load_js', 1)) {
		$document = JFactory::getDocument();
		$document -> addScript(JURI::root() . 'modules/mod_lyquix_banners/assets/banner.js');
		echo '<script>jQuery(document).ready(function(){var myBanner' . $module -> id . ' = new jQuery("div.banner' . $module -> id . '").Banner(' . $js_vars . ');});</script>';
	} else {
		echo '<script>var myBanner' . $module -> id . ' = ' . $js_vars . ';</script>';
	}
}
