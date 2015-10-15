##Lyquix Banners##
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
	<div class="counter"><span class="current">1</span> of <span class="total">3</span></div>
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

  * The whole module is wrapped in `div.module.mod_lyquix_banners.banner123` where 123 is the module ID. Notice the use of custom attributes data-num and data-total to indicate the current banner and the total banners
  * Inside the main DIV there are 3 children DIVs:
    * __div.banners__ : contains the actual banners
    * __div.arrows__ : contains the previous/next arrows
    * __div.tabs__ : contains the tabs with thumbnails
    * __div.counter__ : contains the readable counters for current and total

###Recommended CSS###
At a minimum you should set the structure to achieve the desired effect of banners fading on the same position. Banners, arrows, tabs and counter should use position absolute.

```css
.mod_lyquix_banners {
  position: relative;  /* banners and other elements use absolute position */
  width: 100%;  /* responsive width */
  height: 0;  /* responsive height is achieved using padding bottom */
  padding-bottom: 56.25%;  /* this produces a box with 16:9 proportions */
  overflow: hidden;  
}
.mod_lyquix_banners .banners {
  position: relative;
}
.mod_lyquix_banners .banners .banner {
  position: absolute;
  width: 100%;
}
```

Use the padding-bottom trick to make the banner image responsive too:
```css
.mod_lyquix_banners .banners .banner .image {
  width: 100%;
  height: 0;
  padding-bottom: 56.25%;
  overflow: hidden;
}
.mod_lyquix_banners .banners .banner .image img {
  width: 100%;
}
```

The following provides some fancy arrows:
```css
.mod_lyquix_banners .arrows .prev,
.mod_lyquix_banners .arrows .next {
  position: absolute;
  top: 40%;
  padding: 0 10px;
  font-size: 4em;
  line-height: 0;
  cursor: pointer;
  color: #ffffff;
  text-shadow: 0.05em 0.05em 0.1em #222222;
  opacity: 0.50;
  -webkit-transition: all 0.2s ease;
  -moz-transition: all 0.2s ease;
  transition: all 0.2s ease;
}
.mod_lyquix_banners .arrows .prev:hover,
.mod_lyquix_banners .arrows .next:hover {
  opacity: 1;
}
.mod_lyquix_banners .arrows .prev {
  left: 0px;
}
.mod_lyquix_banners .arrows .prev:before {
  font-family: 'IonIcons';
  content: '\f3d2';
}
.mod_lyquix_banners .arrows .next {
  right: 0px;
}
.mod_lyquix_banners .arrows .next:before {
  font-family: 'IonIcons';
  content: '\f3d3';
}
```

Take advantage of the data-total attribute to produce styles based on the number of banners:
```css
.mod_lyquix_banners[data-total="1"] {
  /* your css here for the case when there is only 1 banner */
}
.mod_lyquix_banners[data-total="2"] {
  /* your css here for the case when there are 2 banners */
}
.mod_lyquix_banners[data-total="3"] {
  /* your css here for the case when there are 3 banners */
}
```
