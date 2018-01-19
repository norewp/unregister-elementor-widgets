<?php
namespace UnregisterElementor\Modules\Unregister;

use Elementor;
use ElementorUtils;
use Elementor\Elementor_Base;
use Elementor\Controls_Manager;
use Elementor\Element_Base;
use Elementor\Widget_Base;
use UnregisterElementor\Base\Module_Base;
use UnregisterElementor\Plugin;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Module extends Module_Base {

	public function __construct() {
		parent::__construct();
		
		$this->add_actions();
	}

	public function get_name() {
		return 'unregister';
	}
	
	protected function add_actions() {	
		add_action( 'elementor/widgets/widgets_registered', [ $this, 'norewp_hide_elementor_modules' ], 20 );
		add_action( 'elementor/widgets/widgets_registered', [ $this, 'norewp_hide_wordpress_widgets' ], 99 );
	}
	
	function norewp_hide_elementor_modules( $widgets_manager ) {
		if ( !current_user_can( 'update_core' ) && is_user_logged_in() ) {
			$widgets_manager->unregister_widget_type( 'heading' );
		}
	}
	
	function norewp_hide_wordpress_widgets( $widgets_manager ) {
		if ( !current_user_can( 'update_core' ) && is_user_logged_in() ) {
			global $wp_widget_factory;
			foreach ($wp_widget_factory->widgets as $value) {
				$widgets_manager->unregister_widget_type( 'wp-widget-'. $value->id_base );
			}
		}
	}
	
}