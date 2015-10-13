<?php
defined('_JEXEC') or die('Restricted access');

// display banners if any available
if (count($banners)) {
    // load required libraries
    require_once(JPATH_ADMINISTRATOR . DS . 'components/com_flexicontent/defineconstants.php');
    JTable::addIncludePath(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_flexicontent' . DS . 'tables');
    require_once("components/com_flexicontent/models/" . FLEXI_ITEMVIEW . ".php");
    require_once("components/com_flexicontent/classes/flexicontent.fields.php");
    require_once("components/com_flexicontent/classes/flexicontent.helper.php");
	
    echo '<div class="module mod_lyquix_banners ' . $params->get('moduleclass_sfx') . ' banner' . $module->id . '">';
    echo $params->get('modpretxt');
    $count = 1;
	$tabs_html = '';
    $itemmodel = new FlexicontentModelItem();
	
    echo '<div class="banners" data-num="1" data-total="' . count($banners) . '">';
	
    foreach ($banners as $banner_item) {
        $item  = $itemmodel->getItem($banner_item, false);
        $items = array(
            $item
        );
        FlexicontentFields::getFields($items);
        echo '<div class="banner ' . $params->get('banner_class', '');
        if ($params->get('style_field') != '') {
            FlexicontentFields::getFieldDisplay($item, $params->get('style_field'));
            if (array_key_exists($params->get('style_field'), $item->fields)) {
                if (array_key_exists($item->fields[$params->get('style_field')]->id, $item->fieldvalues)) {
                    if ($item->fieldvalues[$item->fields[$params->get('style_field')]->id]) {
                        echo ' ' . $item->fields[$params->get('style_field')]->display;
                    }
                }
            }
        }
        echo '"' . ($count > 1 ? ' style="display:none"' : '');
        echo ' data-num="' . $count . '" data-total="' . count($banners) . '">';
        echo $params->get('itempretxt');
        if ($params->get('image_display', 1) && $params->get('image_field') != '') {
            FlexicontentFields::getFieldDisplay($item, $params->get('image_field'));
            if ($item->fieldvalues[$item->fields[$params->get('image_field')]->id]) {
                if ($params->get('image_size', 's') == 'custom') {
                    // custom size image
                    $image_field = unserialize($item->fields[$params->get('image_field')]->value[0]);
                    $image_file  = JPATH_SITE . DS . 'images' . DS . 'stories' . DS . 'flexicontent' . DS . 'l_' . $image_field['originalname'];
                    $conf        = '&amp;w=' . $params->get('image_width') . '&amp;h=' . $params->get('image_height') . '&amp;aoe=1&amp;q=95';
                    $conf .= $params->get('image_resize') ? '&amp;zc=' . $params->get('image_resize') : '';
                    $ext = pathinfo($image_file, PATHINFO_EXTENSION);
                    $conf .= in_array($ext, array(
                        'png',
                        'ico',
                        'gif'
                    )) ? '&amp;f=' . $ext : '';
                    $image_src  = JURI::base() . 'components/com_flexicontent/librairies/phpthumb/phpThumb.php?src=' . $image_file . $conf;
                    $item_image = '<img src="' . $image_src . '" alt="' . $image_field['alt'] . '" title="' . $image_field['title'] . '" />';
                } else {
                    // standard thumbnail
                    $image_field = unserialize($item->fields[$params->get('image_field')]->value[0]);
                    $image_src   = JURI::base() . 'images/stories/flexicontent/' . $params->get('image_size', 's') . '_' . $image_field['originalname'];
                    $item_image  = '<img src="' . $image_src . '" alt="' . $image_field['alt'] . '" title="' . $image_field['alt'] . '" />';
                }
                echo '<div class="image ' . $params->get('image_class') . '">';
                // Link
                if ($params->get('link_display', 1)) {
                    if ($params->get('link_url') == 'custom' && $params->get('link_field') != '') {
                        FlexicontentFields::getFieldDisplay($item, $params->get('link_field'));
                        if (array_key_exists(0, $item->fields[$params->get('link_field')]->value)) {
                            $link = $item->fields[$params->get('link_field')]->value[0];
                        }
                    } else {
                        $link = JRoute::_(FlexicontentHelperRoute::getItemRoute($item->slug, $item->categoryslug));
                    }
                    if ($link != '')
                        echo '<a href="' . $link . '">';
                }
                echo $item_image;
                if ($link != '')
                    echo '</a>';
                echo '</div>';
            }
        }
        if ($params->get('title_display', 1) || $params->get('description_display', 1)) {
            echo '<div class="text ' . $params->get('text_class') . '">';
            // Title
            if ($params->get('title_display', 1)) {
                $item_title = $item->title;
                if ($params->get('title_field', '') != '0') {
                    FlexicontentFields::getFieldDisplay($item, $params->get('title_field'));
                    if ($item->fields[$params->get('title_field')]) {
                        FlexicontentFields::getFieldDisplay($item, $params->get('title_field'), $values = null, $method = 'display');
                        $item_title = $item->fields[$params->get('title_field')]->display;
                    }
                }
                echo '<h' . $params->get('title_heading_level', 3) . ' class="title">' . $item_title . '</h' . $params->get('title_heading_level', 3) . '>';
            }
            // Description Text
            if ($params->get('description_display', 1)) {
                if ($params->get('description_field', '') != '' && $params->get('description_field', '') != '0') {
                    FlexicontentFields::getFieldDisplay($item, $params->get('description_field'));
                    if ($item->fields[$params->get('description_field')]) {
                        FlexicontentFields::getFieldDisplay($item, $params->get('description_field'), $values = null, $method = 'display');
                        $item_description = $item->fields[$params->get('description_field')]->display;
                    }
                } else {
                    $item_description = $item->text;
                }
                if ($params->get('description_striptags', 0)) {
                    $item_description = strip_tags($item_description);
                }
                if ($item_description)
                    echo '<div class="description">' . $item_description . '</div>';
            }
            // Read more link
            if ($params->get('readmore_display', 1)) {
                if ($params->get('readmore_url') == 'custom' && $params->get('readmore_field') != '') {
                    FlexicontentFields::getFieldDisplay($item, $params->get('link_field'));
                    if (array_key_exists(0, $item->fields[$params->get('readmore_field')]->value)) {
                        $link = $item->fields[$params->get('readmore_field')]->value[0];
                    }
                } else {
                    $link = JRoute::_(FlexicontentHelperRoute::getItemRoute($item->slug, $item->categoryslug));
                }
                if ($link != '')
                    echo '<a class="' . $params->get('readmore_css', 'readmore') . '" href="' . $link . '">' . $params->get('readmore_label', 'Read more') . '</a>';
            }
            echo '</div>';
        }
        // Caption
        if ($params->get('caption_display', 1)) {
            FlexicontentFields::getFieldDisplay($item, $params->get('caption_field'));
            if ($item->fields[$params->get('caption_field')]) {
                FlexicontentFields::getFieldDisplay($item, $params->get('caption_field'), $values = null, $method = 'display');
                $item_caption = $item->fields[$params->get('caption_field')]->display;
                if ($item_caption != '')
                    echo '<div class="caption ' . $params->get('caption_class') . '">' . $item_caption . '</div>';
            }
        }
        echo $params->get('itempostxt');
        echo '</div>';
        // Tab
        $tabs_html .= '<div class="tab ' . $params->get('tab_class', '') . ($count == 1 ? ' on' : '') . '" data-num="' . $count . '" data-total="' . count($banners) . '">';
        // Tab thumbnail title
        if ($params->get('thumb_title_display', 0)) {
            $item_title = $item->title;
            if ($params->get('thumb_title_field', '') != '0') {
                FlexicontentFields::getFieldDisplay($item, $params->get('thumb_title_field'));
                if ($item->fields[$params->get('thumb_title_field')]) {
                    FlexicontentFields::getFieldDisplay($item, $params->get('thumb_title_field'), $values = null, $method = 'display');
                    $item_title = $item->fields[$params->get('thumb_title_field')]->display;
                }
            }
            $tabs_html .= '<span>' . $item_title . '</span>';
        }
        // Tab thumnail image
        if ($params->get('thumb_display', 0) && $params->get('thumb_field') != '') {
            FlexicontentFields::getFieldDisplay($item, $params->get('thumb_field'));
            if ($item->fieldvalues[$item->fields[$params->get('thumb_field')]->id]) {
                if ($params->get('thumb_size', 's') == 'custom') {
                    // custom size image
                    $image_field = unserialize($item->fields[$params->get('thumb_field')]->value[0]);
                    $image_file  = JPATH_SITE . DS . 'images' . DS . 'stories' . DS . 'flexicontent' . DS . 'l_' . $image_field['originalname'];
                    $conf        = '&amp;w=' . $params->get('thumb_width') . '&amp;h=' . $params->get('thumb_height') . '&amp;aoe=1&amp;q=95';
                    $conf .= $params->get('thumb_resize') ? '&amp;zc=' . $params->get('thumb_resize') : '';
                    $ext = pathinfo($image_file, PATHINFO_EXTENSION);
                    $conf .= in_array($ext, array(
                        'png',
                        'ico',
                        'gif'
                    )) ? '&amp;f=' . $ext : '';
                    $image_src  = JURI::base() . 'components/com_flexicontent/librairies/phpthumb/phpThumb.php?src=' . $image_file . $conf;
                    $item_image = '<img src="' . $image_src . '" alt="' . $image_field['alt'] . '" title="' . $image_field['title'] . '" />';
                } else {
                    // standard thumbnail
                    $image_field = unserialize($item->fields[$params->get('thumb_field')]->value[0]);
                    $image_src   = JURI::base() . 'images/stories/flexicontent/' . $params->get('thumb_size', 's') . '_' . $image_field['originalname'];
                    $item_image  = '<img src="' . $image_src . '" alt="' . $image_field['alt'] . '" title="' . $image_field['alt'] . '" />';
                }
                $tabs_html .= '<div class="thumb ' . $params->get('thumb_class') . '">' . $item_image . '</div>';
            }
        }
        $tabs_html .= '</div>';
        $count++;
    }
    echo '</div>';
    echo '<div class="arrows"><div class="prev ' . $params->get('arrow_prev_class') . '"></div><div class="next ' . $params->get('arrow_next_class') . '"></div></div>';
    if ($params->get('thumb_display', 0)) {
        echo '<div class="tabs" data-num="1" data-total="' . count($banners) . '">' . $tabs_html . '</div>';
    }
	echo '<div class="counter"><span class="current ' . $params->get('counter_current_class') . '">1</span> of <span class="total ' . $params->get('counter_total_class') . '">' . count($banners) . '</span></div>';
    
?>
<script>var myBanner<?php echo $module -> id; ?> = new jQuery('div.banner<?php echo $module -> id; ?>
	').Banner({
	delay: 
 <?php echo $params -> get('delay_time', 5) * 1000; ?>
	, banner: '.banner',
	tab: '.tab',
	speed: 
 <?php echo $params -> get('fade_time', 400); ?>});</script>
<?php
echo $params -> get('modpostxt');
echo '</div>';
if ($params -> get('load_js', 1)) {
	$document = JFactory::getDocument();
	$document -> addScript(JURI::root() . 'modules/mod_lyquix_banners/assets/banner.js');
}
}
