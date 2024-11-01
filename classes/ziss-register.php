<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ziss_cls_registerhook {
	public static function ziss_activation() {
	
		global $wpdb;

		add_option('zoom-image-simple-script', "1.0");

		$charset_collate = '';
		$charset_collate = $wpdb->get_charset_collate();
	
		$ziss_default_tables = "CREATE TABLE {$wpdb->prefix}zoom_image_simples (
										ziss_id INT unsigned NOT NULL AUTO_INCREMENT,
										ziss_title VARCHAR(1024) NOT NULL default '',
										ziss_img_sm VARCHAR(1024) NOT NULL default '',
										ziss_img_bg VARCHAR(1024) NOT NULL default '',
										ziss_width int(11) NOT NULL default '0',
										ziss_height int(11) NOT NULL default '0',
										ziss_fade int(11) NOT NULL default '0',
										ziss_scale int(11) NOT NULL default '0',
										ziss_position VARCHAR(10) NOT NULL default '',
										ziss_group VARCHAR(10) NOT NULL default 'Group1',
										ziss_status VARCHAR(3) NOT NULL default 'Yes',
										ziss_updated TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, 
										PRIMARY KEY (ziss_id)
										) $charset_collate;";
	
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $ziss_default_tables );
		
		$ziss_default_tablesname = array( 'zoom_image_simples' );
	
		$ziss_errors = false;
		$ziss_missing_tables = array();
		foreach($ziss_default_tablesname as $table_name) {
			if(strtoupper($wpdb->get_var("SHOW TABLES like  '". $wpdb->prefix.$table_name . "'")) != strtoupper($wpdb->prefix.$table_name)) {
				$ziss_missing_tables[] = $wpdb->prefix.$table_name;
			}
		}
		
		if($ziss_missing_tables) {
			$errors[] = __( 'These tables could not be created on installation ' . implode(', ',$ziss_missing_tables), 'zoom-image-simple-script' );
			$ziss_errors = true;
		}
		
		if($ziss_errors) {
			wp_die( __( $errors[0] , 'zoom-image-simple-script' ) );
			return false;
		} 
		else {
			ziss_cls_dbquery::ziss_default();
		}
		
		if ( ! is_network_admin() && ! isset( $_GET['activate-multi'] ) ) {
			//set_transient( '_ziss_activation_redirect', 1, 30 );
		}
			
		return true;
	}

	public static function ziss_deactivation() {
		// do not generate any output here
	}

	public static function ziss_adminoptions() {
	
		global $wpdb;
		$current_page = isset($_GET['ac']) ? $_GET['ac'] : '';
		
		switch($current_page) {
			case 'edit':
				require_once(ZISSD_DIR . 'pages' . DIRECTORY_SEPARATOR . 'image-management-edit.php');
				break;
			case 'add':
				require_once(ZISSD_DIR . 'pages' . DIRECTORY_SEPARATOR . 'image-management-add.php');
				break;
			default:
				require_once(ZISSD_DIR . 'pages' . DIRECTORY_SEPARATOR . 'image-management-show.php');
				break;
		}
	}
	
	public static function ziss_frontscripts() {
		if (!is_admin()) {
			wp_enqueue_script('jquery');
			wp_enqueue_script( 'zoom-image-simple-script', plugin_dir_url( __DIR__ ) . '/inc/zoom-image-simple-script.js');
			wp_enqueue_style( 'zoom-image-simple-script', plugin_dir_url( __DIR__ ) . '/inc/zoom-image-simple-script.css');
		}	
	}

	public static function ziss_addtomenu() {
	
		if (is_admin()) {
			add_options_page( __('Zoom image', 'zoom-image-simple-script'), 
								__('Zoom image', 'zoom-image-simple-script'), 'manage_options', 
									'zoom-image-simple-script', array( 'ziss_cls_registerhook', 'ziss_adminoptions' ) );
		}
	}
	
	public static function ziss_adminscripts() {
	
		if(!empty($_GET['page'])) {
			switch ($_GET['page']) {
				case 'zoom-image-simple-script':
					wp_register_script( 'marquee-image-adminscripts', plugin_dir_url( __DIR__ ) . '/pages/setting.js', '', '', true );
					wp_enqueue_script( 'marquee-image-adminscripts' );
					$ziss_select_params = array(
						'ziss_title'  		=> __( 'Please enter the image title.', 'ziss-select', 'zoom-image-simple-script' ),
						'ziss_image'  		=> __( 'Please enter the image path.', 'ziss-select', 'zoom-image-simple-script' ),
						'ziss_group'  		=> __( 'Please enter the image group.', 'ziss-select', 'zoom-image-simple-script' ),
						'ziss_width'  		=> __( 'Please enter image display width.', 'ziss-select', 'zoom-image-simple-script' ),
						'ziss_height'  		=> __( 'Please enter image display height.', 'ziss-select', 'zoom-image-simple-script' ),
						'ziss_width'  		=> __( 'Please enter image display height.', 'ziss-select', 'zoom-image-simple-script' ),				
						'ziss_width_num'  	=> __( 'Please enter image width. only numbers.', 'ziss-select', 'zoom-image-simple-script' ),
						'ziss_numletters'  	=> __( 'Please input numeric and letters only.', 'ziss-select', 'zoom-image-simple-script' ),
						'ziss_delete'  		=> __( 'Do you want to delete this record?', 'ziss-select', 'zoom-image-simple-script' ),
					);
					wp_localize_script( 'marquee-image-adminscripts', 'ziss_adminscripts', $ziss_select_params );
					break;
			}
		}
	}
	
	public static function ziss_widgetloading() {
		register_widget( 'ziss_widget_register' );
	}
}

class ziss_widget_register extends WP_Widget 
{
	function __construct() {
		$widget_ops = array('classname' => 'widget_text zoom-image-widget', 'description' => __('Zoom image', 'zoom-image-simple-script'), 'zoom-image-simple-script');
		parent::__construct('zoom-image-simple-script', __('Zoom image', 'zoom-image-simple-script'), $widget_ops);
	}
	
	function widget( $args, $instance ) {
		extract( $args, EXTR_SKIP );
		
		$ziss_title 	= apply_filters( 'widget_title', empty( $instance['ziss_title'] ) ? '' : $instance['ziss_title'], $instance, $this->id_base );
		$ziss_group		= $instance['ziss_group'];
		$ziss_id		= $instance['ziss_id'];
	
		echo $args['before_widget'];
		if (!empty($ziss_title)) {
			echo $args['before_title'] . $ziss_title . $args['after_title'];
		}
		
		$data = array(
			'group' => $ziss_group,
			'id' 	=> $ziss_id
		);
		
		ziss_cls_shortcode::ziss_render($data);
		
		echo $args['after_widget'];
	}
	
	function update( $new_instance, $old_instance ) {		
		$instance 				= $old_instance;
		$instance['ziss_title'] = ( ! empty( $new_instance['ziss_title'] ) ) ? strip_tags( $new_instance['ziss_title'] ) : '';
		$instance['ziss_group'] = ( ! empty( $new_instance['ziss_group'] ) ) ? strip_tags( $new_instance['ziss_group'] ) : '';
		$instance['ziss_id'] 	= ( ! empty( $new_instance['ziss_id'] ) ) ? strip_tags( $new_instance['ziss_id'] ) : '';
		return $instance;
	}
	
	function form( $instance ) {
		$defaults = array(
			'ziss_title' => '',
		    'ziss_group' => '',
			'ziss_id' 	 => ''
        );
		
		$instance 	= wp_parse_args( (array) $instance, $defaults);
		$ziss_title = $instance['ziss_title'];
        $ziss_group = $instance['ziss_group'];
		$ziss_id 	= $instance['ziss_id'];
		
		?>
		<p>
			<label for="<?php echo $this->get_field_id('ziss_title'); ?>"><?php _e('Title', 'zoom-image-simple-script'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('ziss_title'); ?>" name="<?php echo $this->get_field_name('ziss_title'); ?>" type="text" value="<?php echo $ziss_title; ?>" />
        </p>
		
		<p>
			<label for="<?php echo $this->get_field_id('ziss_group'); ?>"><?php _e('Image Group', 'zoom-image-simple-script'); ?></label>
			<select class="widefat" id="<?php echo $this->get_field_id('ziss_group'); ?>" name="<?php echo $this->get_field_name('ziss_group'); ?>">
			<option value="">Select (Use Image Id)</option>
			<?php
			$groups = array();
			$groups = ziss_cls_dbquery::ziss_group();
			if(count($groups) > 0) {
				foreach ($groups as $group) {
					?>
					<option value="<?php echo $group['ziss_group']; ?>" <?php $this->ziss_selected($group['ziss_group'] == $ziss_group); ?>>
					<?php echo $group['ziss_group']; ?>
					</option>
					<?php
				}
			}
			?>
			</select>
        </p>
			
		<p>
			<label for="<?php echo $this->get_field_id('ziss_id'); ?>"><?php _e('Image ID', 'zoom-image-simple-script'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('ziss_id'); ?>" name="<?php echo $this->get_field_name('ziss_id'); ?>" type="text" value="<?php echo $ziss_id; ?>" />
        </p>
		<?php
	}
	
	function ziss_selected($var) {
		if ($var==1 || $var==true) {
			echo 'selected="selected"';
		}
	}
}

class ziss_cls_shortcode {
	public function __construct() {
	}
	
	public static function ziss_shortcode( $atts ) {
		ob_start();
		if (!is_array($atts)) {
			return '';
		}
		
		//[ziss-zoom-image group="Group1"]
		//[ziss-zoom-image id="1"]
		$atts = shortcode_atts( array(
				'group'	=> '',
				'id'	=> ''
			), $atts, 'zoom-image-simple-script' );

		$group 	= isset($atts['group']) ? $atts['group'] : '';
		$id 	= isset($atts['id']) ? $atts['id'] : '';
		
		$data = array(
			'group' => $group,
			'id' 	=> $id
		);
		
		self::ziss_render( $data );

		return ob_get_clean();
	}
	
	public static function ziss_render( $input = array() ) {	
		
		$ziss = "";
		$datas = array();
		$files	= array();
		
		if(count($input) == 0) {
			return $ziss;
		}
		
		$group 	= sanitize_text_field($input['group']);
		$id		= intval($input['id']);
		
		$data =ziss_cls_dbquery::ziss_select_shortcode($id, $group);
	
		if(count($data) > 0 ) {
			$ziss_img_sm = $data['ziss_img_sm'];
			$ziss_img_bg = $data['ziss_img_bg'];
			$ziss_width = intval($data['ziss_width']);
			$ziss_height = intval($data['ziss_height']);
			$ziss_fade = intval($data['ziss_fade']);
			$ziss_scale = intval($data['ziss_scale']);
			$ziss_position = $data['ziss_position'];
			$ziss_group = $data['ziss_group'];
			
			if($ziss_fade < 500) {
				$ziss_fade = 500;
			}
			
			$ziss .= '<script>';
			$ziss .= 'jQuery(function($){';
				$ziss .= "jQuery('.sampleimage').zoomio({";
					if($ziss_width > 0) {
						$ziss .= "w: '" . $ziss_width . "%',";
					}
					if($ziss_height > 0) {
						$ziss .= "h: '" . $ziss_height . "px',";
					}
					if($ziss_scale > 0) {
						$ziss .= 'scale: ' . $ziss_scale . ',';
					}
					if($ziss_fade > 0) {
						$ziss .= 'fadeduration: ' . $ziss_fade . '';
					}
				$ziss .= '})';
			$ziss .= '})';
			$ziss .= '</script>';
			$ziss .= '<img class="sampleimage" src="'. $ziss_img_sm .'" data-largesrc="'. $ziss_img_bg .'" />';

		}
		
		echo $ziss;
	}
}
?>