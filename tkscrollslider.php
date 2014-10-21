<?php
/*
	Plugin Name: Scroll Slider
	Plugin URI: http://www.cutedrops.com/
	Description: A simple Scorller widget
	Version: 1.0.0
	Author: Theepan Kanthavel
	Author URI: http://www.cutedrops.com/
	Text Domain: tk
*/

wp_register_style('tkbxslidercss', plugins_url('/css/jquery.bxslider.css', __FILE__));
wp_register_style('tkbxslidercssadmin', plugins_url('/css/jquery.bxslider.admin.css', __FILE__));
wp_register_script('tkbxsliderjs', plugins_url('/js/jquery.bxslider.min.js', __FILE__), array('jquery'));
wp_register_script('tkscrollslider', plugins_url('/js/tkscrollslider.js', __FILE__));
wp_register_script('tkscrollslideradmin', plugins_url('/js/tkscrollslider.admin.js', __FILE__));

// Localize Script
$localize_array = array(
	'admin_ajax' => admin_url() . '/admin-ajax.php'
);
wp_localize_script('tkscrollslideradmin', 'tk_scroller_ajax', $localize_array);

$current_location = $_SERVER['REQUEST_URI'];
if(!is_admin()) {
	wp_enqueue_style('tkbxslidercss');
	wp_enqueue_script('tkbxsliderjs');
	wp_enqueue_script('tkscrollslider', array('jquery', 'tkbxsliderjs'));
}
if(basename($current_location) == 'widgets.php' && is_admin()) {
	wp_enqueue_style('tkbxslidercssadmin');
	add_action('admin_print_scripts', 'tk_admin_scripts');
}

function tk_admin_scripts() {
	wp_enqueue_script('tkscrollslideradmin');
	if(function_exists('wp_enqueue_media')) {
		wp_enqueue_media();
	} else {
		wp_enqueue_style('thickbox');
		wp_enqueue_script('media-upload');
		wp_enqueue_script('thickbox');
	}
}

class TKScrollSliderWidget extends WP_Widget {
	function __construct() {
		parent::__construct('tkscollsliderwidget', __('TK Scroll Slider', 'tk'),
			array(
				'description' => __('A simple Scorller widget', 'tk')
			)
		);
	}

	public function widget($args, $inst) {
		echo $args['before_widget'];
		echo $args['before_title'] . $inst['title'] . $args['after_title'];
		$image_ids = ($inst['pictures'] ? explode(',', $inst['pictures']) : array());
		if(count($image_ids) > 0){
		?>
		<ul class="bxslider">
			<?php
				foreach($image_ids as $image_id) {
					$img_src = wp_get_attachment_image_src($image_id, array(120, 100));
			?>		
				<li>
					<img src="<?php echo $img_src[0]; ?>" />
				</li>
			<?php
				}
			?>
		</ul>
		<?php
		}
		echo $args['after_widget'];
	}

	public function form($inst) {
		$title = ($inst['title'] ? $inst['title'] : __('Scroller', 'tk'));
		$image_ids = ($inst['pictures'] ? explode(',', $inst['pictures']) : array());
		?>
			<p>
				<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e( 'Title:' ); ?></label>
				<input type="text" class="widefat" 
					name="<?php echo $this->get_field_name('title'); ?>" 
					id="<?php echo $this->get_field_name('title'); ?>" 
					value="<?php echo esc_attr($title); ?>"
				/>
			</p>
			<h4>Pictures</h4>
			<p>
				<div class="tkscorllslider-pictures">
					<ul>
						<?php 
							if(count($image_ids) > 0) {
								foreach($image_ids as $image_id) {
									$img_src = wp_get_attachment_image_src($image_id, array(120, 100));
									echo "<li data-image-id='{$image_id}'>";
									echo "<img src='{$img_src[0]}' />";
									echo "</li>";
								}
							}
						?>
					</ul>
				</div>
			</p>
			<p><a href="javascript:void(0);" class="button button-secondary right insert-pictures">Insert</a></p>
			<div class="clearfix clear"></div>
			<p>
				<input type="hidden" class="widefat tk-scroller-image-ids" 
					name="<?php echo $this->get_field_name('pictures'); ?>"
					id="<?php echo $this->get_field_id('pictures'); ?>"
					value="<?php echo $inst['pictures']; ?>"
				/>
			</p>
		<?php
	}

	public function update($new_inst, $old_inst) {
		$inst = array();
		$inst['title'] = (!empty($new_inst['title']) ? strip_tags($new_inst['title']) : '');
		$inst['pictures'] =  (!empty($new_inst['pictures']) ? $new_inst['pictures'] : '');
		return $inst;
	}
}
function register_tk_scroll_slider_widget() {
	register_widget('TKScrollSliderWidget');
}
add_action('widgets_init', 'register_tk_scroll_slider_widget');
add_action('wp_ajax_tk_scroller', 'tk_scroller_render_images');

function tk_scroller_render_images() {
	$image_ids = explode(',', $_POST['image_ids']);
	$content ='';
	foreach($image_ids as $image_id) {
		$img_src = wp_get_attachment_image_src($image_id, array(120, 100));
		$content .= "<li data-image-id='{$image_id}'><img src='" . $img_src[0] . "' /></li>";
	}
	wp_send_json($content);
}