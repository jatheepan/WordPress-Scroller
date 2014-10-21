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
wp_register_script('tkbxsliderjs', plugins_url('/js/jquery.bxslider.min.js', __FILE__));
wp_register_script('tkscrollslider', plugins_url('/js/tkscrollslider.js', __FILE__));
wp_register_script('tkscrollslideradmin', plugins_url('/js/tkscrollslider.admin.js', __FILE__));

$current_location = $_SERVER['REQUEST_URI'];
if(!is_admin()) {
	wp_enqueue_style('tkbxslidercss');
	wp_enqueue_script('tkbxsliderjs', array('jquery'));
	wp_enqueue_script('tkscrollslider', array('jquery', 'tkbxsliderjs'));
}
if(basename($current_location) == 'widgets.php' && is_admin()) {
	wp_enqueue_style('tkbxslidercssadmin');
	wp_enqueue_script('tkscrollslideradmin');
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
		?>

		<?php
		echo $args['after_widget'];
	}

	public function form($inst) {
		$title = ($inst['title'] ? $inst['title'] : __('Scroller', 'tk'));
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
					
				</div>
			</p>
			<p><a href="javascript:void(0);" class="button button-secondary right" id="tk-scroll-insert-button">Insert</a></p>
			<div class="clearfix clear"></div>
			<p>
				<input type="hidden" class="widefat tk-scoller-image-ids" 
					name="<?php echo $this->get_field_name('pictures'); ?>"
					id="<?php echo $this->get_field_id('pictures'); ?>"
					value="<?php echo $pictures; ?>"
				/>
			</p>
		<?php
	}

	public function update($new_inst, $old_inst) {
		$inst = array();
		$inst['title'] = (!empty($new_inst['title']) ? strip_tags($new_inst['title']) : '');
		return $inst;
	}
}
function register_tk_scroll_slider_widget() {
	register_widget('TKScrollSliderWidget');
}
add_action('widgets_init', 'register_tk_scroll_slider_widget');