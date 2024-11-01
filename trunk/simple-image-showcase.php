<?php
/*
Plugin Name: Simple Image Showcase
Plugin URI: http://www.glaskugel.hu/
Description: Simple image showcase widget. The widget can find and show the last uploaded image. The plugin only works with the Widget API. 
						 == Installation ==
						 1. Download.
						 2. Unzip.
						 3. Upload to the plugins directory (wp-content/plugins).
						 4. Activate the plugin.
						 5. Use the "Simple Image Showcase Widget" on a dynamic sidebar
Version: 1.1
Author: Zsolt Takács
Author URI: http://glaskugel.hu/

== CHANGELOG ==

1.1: Added modal box selection.
     Random image option.
		 Width settings for frontend sidebar.
		 Show the image preview (but the image width is fixed when user's on admin page).
1.0: Get the last uploaded image from the the database.

*/

if(class_exists('WP_Widget')){

class SimpleImageShowcase extends WP_Widget {

	function SimpleImageShowcase() {
		$widget_ops = array('classname' => 'SimpleImageShowcase', 'description' => 'The widget can find and show the last uploaded image.' );
		$this->WP_Widget('image_showcase', 'Simple Image Showcase', $widget_ops);
	}
 
	function widget($args, $instance) {
		extract($args, EXTR_SKIP);
 
		echo $before_widget;
		
		$title = empty($instance['title']) ? '&nbsp;' : apply_filters('widget_title', $instance['title']);
		$width = empty($instance['width']) ? '&nbsp;' : apply_filters('widget_width', $instance['width']);
		$modal = empty($instance['modal']) ? '&nbsp;' : apply_filters('widget_modal', $instance['modal']);
		$random = empty($instance['random']) ? '&nbsp;' : apply_filters('widget_random', $instance['random']);
 
		if ( !empty( $title ) ) { echo $before_title . $title . $after_title; };
		
		/* GET IMAGE FROM GALLERY */
				
  	echo $this->get_dat_image($instance);
		
		/* ---------------------- */
		
		echo $after_widget;

	}
 
	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['width'] = strip_tags($new_instance['width']);
		$instance['modal'] = strip_tags($new_instance['modal']);
		$instance['random'] = strip_tags($new_instance['random']);
  		
		return $instance;

	}
 
	function form($instance) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => '', 'width' => '', 'modal' => '', 'random' => '' ) );
		$title = strip_tags($instance['title']);
		$width = strip_tags($instance['width']);
		$modal = strip_tags($instance['modal']);
		$random = strip_tags($instance['random']);
		
		$modals = array(''=>'',
										'fancybox'=>'FancyBox',
										'fancyzoom'=>'FancyZoom',
										'floatbox'=>'FloatBox',
										'greybox'=>'GrayBox',
										'lightbox'=>'Lightbox',
										'lightview'=>'LightView',
										'lightwindow'=>'LightWindow',
										'lytebox'=>'LyteBox',
										'shadowbox'=>'Shadowbox',
										'slimbox'=>'Slimbox',
										'thickbox'=>'Thickbox');
		
		$is_checked = ($random ? 'checked="checked"' : '');
?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>">Title: <input style="width:191px;" class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo attribute_escape($title); ?>" /></label></p>
		<p><label for="<?php echo $this->get_field_id('width'); ?>">Sidebar width: <input class="widefat" style="width:40px;" id="<?php echo $this->get_field_id('width'); ?>" name="<?php echo $this->get_field_name('width'); ?>" type="text" value="<?php echo attribute_escape($width); ?>" />&nbsp;px</label></p>
		<p><label for="<?php echo $this->get_field_id('random'); ?>">Random image: <input class="widefat" style="width:20px;" id="<?php echo $this->get_field_id('random'); ?>" name="<?php echo $this->get_field_name('random'); ?>" type="checkbox" value="1" <?=$is_checked?> /></label></p>
    <p><label for="<?php echo $this->get_field_id('modal'); ?>">Modal type: 
    <select style="width:148px;" class="widefat" id="<?php echo $this->get_field_id('modal'); ?>" name="<?php echo $this->get_field_name('modal'); ?>">
    <?php 
		foreach($modals as $modid => $modname){
		?>
    <option value="<?=$modid?>" <?=($modid == $modal ? 'selected="selected"' : '')?> ><?= attribute_escape($modname); ?></option>
    <?php
		}
		?>
    </select>
		</label></p>
    	
<?php

		echo $this->get_dat_image($instance);
			
	}
	
	function get_dat_image($instance){
		
	global $wpdb;
		
	$orderby = ( $instance['random']=='1' ? 'RAND()' : 'ID DESC' );
	$has_modal = ( $instance['modal'] ? 'rel="'.$instance['modal'].'"' : '' );
	$has_width = ( is_numeric($instance['width']) ? 'style="width:'.$instance['width'].'px;"' : '' );
	$has_width = ( is_admin() ? 'style="width:226px;"' : $has_width );
		
	$imagerow = $wpdb->get_row("SELECT * FROM ".$wpdb->posts." WHERE post_type='attachment' AND post_mime_type LIKE '%image%' ORDER BY ".$orderby." LIMIT 0,1");
	
	$image = wp_get_attachment_image_src($imagerow->ID, $size='medium', $icon = true);
		
	return '<div id="imagewidget" class="wp-caption"><a href="'.wp_get_attachment_url($imagerow->ID).'" '.$has_modal.'><img src="'.$image[0].'" alt="" '.$has_width.' /></a></div>' .
	
	do_shortcode('[caption id="attachment_'.$imagerow->ID.'" align="aligncenter" caption="'.$imagerow->post_excerpt.'"]');
			
	}
	
}

function register_the_new_widget(){
	register_widget('SimpleImageShowcase');
}

add_action('init', 'register_the_new_widget', 1);

} else {

echo '<script type="text/javascript">alert(\'Warning! Your Wordpess version is less than 2.8! Update your Wordpress engine!\');</script>';

}
?>