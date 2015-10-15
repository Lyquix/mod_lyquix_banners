##Lyquix Banners mod_lyquix_banners##
Joomla module used to generate a banner system using content from FLEXIcontent

###Is this for you?###
This module is intended for advanced developers that are looking for a powerful solution that can be fully customized for your project, without intrusive CSS and JavaScript overhead. After installation you will need to go over all the configuration options and you will need to add your styling to make it look as you need.

###Features###
**Scope**

Options to select category, content type, maximum number of banners to load, ordering method (date, title, category order, and random), and language filter.

**Display Options**

Each banner may contain the following elements
  * Banner Image
  * Text: title, description and read more link
  * Caption: a separate text field
  * Tabs and thumbnails: can be used as progress indicator with optional thumbnails and title

The administrator can control the timing settings: time displaying each slide, and time for transition.

Users can click on previous/next arrows, and progress tabs to move the banners.

**Advanced Options**
  * Load default Javascript to animate banner system
  * Additional CSS classes for all HTML elements
  * Module pre- and post-text
  * Banner items pre- and post-text

###Installation and Updates###
Download the latest release from https://github.com/Lyquix/mod_lyquix_banners/releases and install it using Joomla extension manager.

###Configuration###
  1. Scope
    * Select the category and type where your banner items are to be found
    * Select the ordering method: date, title, category order, or random
    * Select the maximum number of banners to fetch
    * Language filtering
  2. Display settings
    * Image
      * Display banner image (yes/no) - you don't need to have an image, you can use this banner system for text only banners (e.g. client testimonials)
      * Select image field
      * Select thumbnail size or custom size
      * Make image clickable (yes/no)
      * Select link destination: banner item or value of URL field
    * Text
      * Display title (yes/no)
      * Field to use for title
      * Display text (yes/no)
      * Field to use for text
      * Strip tags (yes/no)
      * Add a red more link (yes/no)
      * Select link destination: banner item or value of URL field (independent from image link)
      * Read more link label and CSS class
    * Caption (an optional separate text field)
    * Thumbnails (option to add title and image to the progress indicator tabs)
      * Display thumbnails (yes/no)
      * Image field to use for thumbnails
      * Select thumbnail size or custom size
      * Display title (yes/no)
      * Field to use for title
    * Timing options
      * Slide swap time
      * Fade time
    * Styling
      * Custom field for adding CSS class to individual banners

###HTML structure###

```xml
<div class="module mod_lyquix_banners banner123">
	<div class="banners" data-num="1" data-total="3">
		<div class="banner image" data-num="1" data-total="3" style="display: block;">
			<div class="image">
				<a href=""><img src="" alt=""></a>
			</div>
			<div class="text">
				<h3>Title</h3>
				<div class="description">Text</div>
				<a class="readmore" href="">Read more</a>
			</div>
		</div>
		<div class="banner image" data-num="2" data-total="3" style="display: block;">
			<div class="image">
				<a href=""><img src="" alt=""></a>
			</div>
			<div class="text">
				<h3>Title</h3>
				<div class="description">Text</div>
				<a class="readmore" href="">Read more</a>
			</div>
		</div>
		<div class="banner image" data-num="3" data-total="3" style="display: block;">
			<div class="image">
				<a href=""><img src="" alt=""></a>
			</div>
			<div class="text">
				<h3>Title</h3>
				<div class="description">Text</div>
				<a class="readmore" href="">Read more</a>
			</div>
		</div>
	</div>
	<div class="arrows">
		<div class="prev"></div><div class="next"></div>
	</div>
	<div class="tabs" data-num="1" data-total="3">
		<div class="tab on" data-num="1" data-total="3">
			<span>Title</span>
			<div class="thumb"><img src="" alt="" /></div>
		</div>
		<div class="tab" data-num="2" data-total="3">
			<span>Title</span>
			<div class="thumb"><img src="" alt="" /></div>
		</div>
		<div class="tab" data-num="3" data-total="3">
			<span>Title</span>
			<div class="thumb"><img src="" alt="" /></div>
		</div>
	</div>
	<script type="text/javascript">
		var myBanner123 = new jQuery('div.banner123').Banner({
			delay : 10000,
			banner : '.banner',
			tab : '.tab',
			speed : 400
		});
	</script>
</div>
```
