<?php

define( 'VTOURS_MAX_LOCATIONS', 50 ); // Maximum number of locations allowed per tour (prevents performance bottle necks)

/**
 *
 * @copyright Copyright (c), Ryan Hellyer
 * @author Ryan Hellyer <ryanhellyer@gmail.com>
 * @package Virtual Tours
 * @since Virtual Tours 1.0
 */
class Virtual_Tours_Setup {

	/**
	 * Constructor
	 * Add methods to appropriate hooks and filters
	 */
	public function __construct() {
		add_action( 'after_setup_theme',  array( $this, 'theme_setup' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'stylesheet' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'scripts' ) );
		add_action( 'init',               array( $this, 'post_types' ) );
		add_action( 'init',               array( $this, 'taxonomies' ) );
		add_action( 'admin_menu',         array( $this, 'remove_menus' ) );
		add_action( 'admin_head',         array( $this, 'add_menu_icons_styles' ) );
	}

	/*
	 * Add menu icons
	 */ 
	public function add_menu_icons_styles() {
		echo '<style>';
		echo '#menu-posts-tour .wp-menu-image:before {content: "\f307";}';
		echo '</style>';
	}

	/**
	 * Sets up theme defaults and registers support for various WordPress features.
	 */
	public function theme_setup() {
	
		// Make theme available for translation
		load_theme_textdomain( 'vtours', get_template_directory() . '/languages' );

		// Add support for featured images
		add_theme_support( 'post-thumbnails' );
		add_theme_support( 'excerpt' );

	}

	/*
	 * Simplify admin panel by removing menu items
	 */
	public function remove_menus() {

		remove_menu_page( 'index.php' );         // Dashboard
		remove_menu_page( 'edit.php' );          // Posts
		remove_menu_page( 'edit-comments.php' ); // Comments
		remove_menu_page( 'tools.php' );         // Tools
	}

	/**
	 * Register post types
	 */
	public function post_types() {

		// Register location post type
		$args = array(
			'label'              => __( 'Location', 'vtour' ),
			'public'             => false,
			'publicly_queryable' => false,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'query_var'          => false,
			'hierarchical'       => false,
			'menu_position'      => 4,
			//'menu_icon'          => get_template_directory_uri() . "/img/admin/menu-icon.png",
			'supports'           => array( 'title', 'editor', 'thumbnail', 'excerpt', 'page-attributes' ),
		);
		register_post_type( 'location', $args );

		// Register tours post type
		$args = array(
			'label'              => __( 'Tours', 'vtour' ),
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'query_var'          => false,
			'hierarchical'       => false,
			'menu_position'      => 5,
			'menu_icon'          => '',
			'supports'           => array( 'title', 'editor', 'thumbnail', 'excerpt' ),
		);
		register_post_type( 'tour', $args );

	}

	/**
	 * Register taxonomies
	 */
	public function taxonomies() {

		// Countries taxonomy
		$labels = array(
			'name'              => __( 'Countries', 'vtours' ),
			'singular_name'     => __( 'Country', 'vtours' ),
			'search_items'      => __( 'Search countries', 'vtours' ),
			'all_items'         => __( 'All countries', 'vtours' ),
			'edit_item'         => __( 'Edit country', 'vtours' ),
			'update_item'       => __( 'Update country', 'vtours' ),
			'add_new_item'      => __( 'Add country', 'vtours' ),
			'new_item_name'     => __( 'New country', 'vtours' ),
			'menu_name'         => __( 'Country', 'vtours' ),
		);
		$args = array(
			'hierarchical'      => false,
			'labels'            => $labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => false,
			'single_value'      => true, // Support for this added via Single Value Taxonomy UI plugin (http://wordpress.org/plugins/single-value-taxonomy-ui/)
		);
		register_taxonomy( 'country', array( 'location' ), $args );

		// Area taxonomy
		$labels = array(
			'name'              => __( 'Areas', 'vtours' ),
			'singular_name'     => __( 'Area', 'vtours' ),
			'search_items'      => __( 'Search areas', 'vtours' ),
			'all_items'         => __( 'All areas', 'vtours' ),
			'edit_item'         => __( 'Edit area', 'vtours' ),
			'update_item'       => __( 'Update area', 'vtours' ),
			'add_new_item'      => __( 'Add area', 'vtours' ),
			'new_item_name'     => __( 'New area', 'vtours' ),
			'menu_name'         => __( 'Area', 'vtours' ),
		);
		$args = array(
			'hierarchical'      => false,
			'labels'            => $labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => false,
			'single_value'      => true, // Support for this added via Single Value Taxonomy UI plugin (http://wordpress.org/plugins/single-value-taxonomy-ui/)
		);
		register_taxonomy( 'area', array( 'location' ), $args );

	}

	/**
	 * Load stylesheet
	 */
	public function stylesheet() {
		if ( ! is_admin() ) {
			wp_enqueue_style( 'style', get_stylesheet_directory_uri() . '/style.css', array(), '1.0' );
		}
	}

	/**
	 * Load scripts
	 */
	public function scripts() {

		if ( ! is_admin() ) {
			wp_enqueue_script( 'leaflet', get_stylesheet_directory_uri() . '/js/leaflet.js', array(), '1.0' );
			wp_enqueue_script( 'swipe', get_stylesheet_directory_uri() . '/js/swipe.js', array(), '1.0', true );
			wp_enqueue_script( 'vtours-init', get_stylesheet_directory_uri() . '/js/vtours-init.js', array(), '1.0', true );
			wp_localize_script( 'vtours-init', 'vtours_template_uri', get_template_directory_uri() );
		}

	}

}
new Virtual_Tours_Setup;





/**
 * Add Meta Box to "post" post type
 *
 * @copyright Copyright (c), Ryan Hellyer
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @author Ryan Hellyer <ryanhellyer@gmail.com>
 * @since 1.0
 */
class Virtual_Tours_Metaboxes {

	/*
	 * Class constructor
	 */
	public function __construct() {
		add_action( 'add_meta_boxes', array( $this, 'add_metabox' ) );
		add_action( 'save_post',      array( $this, 'meta_boxes_save' ), 10, 2 );
	}

	/**
	 * Add admin metabox for thumbnail chooser
	 */
	public function add_metabox() {

		add_meta_box(
			'geo-coordinates', // ID
			__( 'Geo-coordinates', 'vtours' ), // Title
			array(
				$this,
				'meta_box_coordinates', // Callback to method to display HTML
			),
			'location', // Post type
			'side', // Context, choose between 'normal', 'advanced', or 'side'
			'high'  // Position, choose between 'high', 'core', 'default' or 'low'
		);

		add_meta_box(
			'tour', // ID
			__( 'Tour ID', 'vtours' ), // Title
			array(
				$this,
				'meta_box_tour_id', // Callback to method to display HTML
			),
			'location', // Post type
			'side', // Context, choose between 'normal', 'advanced', or 'side'
			'high'  // Position, choose between 'high', 'core', 'default' or 'low'
		);

	}

	/**
	 * Output the geo-coordinates meta box
	 * Also outputs the nonce
	 */
	public function meta_box_coordinates() {

		?>

		<p>
			<label for="_vtours_latitude"><strong><?php _e( 'Latitude', 'vtours' ); ?></strong></label>
			<br />
			<input type="text" name="_vtours_latitude" id="_vtours_latitude" value="<?php echo get_post_meta( get_the_ID(), '_vtours_latitude', true ); ?>" />
		</p>

		<p>
			<label for="_vtours_latitude"><strong><?php _e( 'Longitude', 'vtours' ); ?></strong></label>
			<br />
			<input type="text" name="_vtours_longitude" id="_vtours_longitude" value="<?php echo get_post_meta( get_the_ID(), '_vtours_longitude', true ); ?>" />
		</p>

		<input type="hidden" id="vtours_nonce" name="vtours_nonce" value="<?php echo wp_create_nonce( __FILE__ ); ?>">

		<?php
	}

	/**
	 * Output the tour ID meta box
	 */
	public function meta_box_tour_id() {

		$current_tour_id = get_post_meta( get_the_ID(), '_vtours_tourid', true );
		?>
		<p>
			<label for="_vtours_tourid"><?php _e( 'Tour', 'vtours' ); ?></label>
			<br />
			<?php
			$args = array( 'posts_per_page' => 10, 'post_type' => 'tour' );
			$tours = get_posts( $args );
			?>
			<select name="_vtours_tourid" id="_vtours_tourid"><?php
			foreach ( $tours as $tour ) {
				if ( $current_tour_id == $tour->ID ) {
					$selected = 'selected="selected" ';
				} else {
					$selected = '';
				}
				?>
				<option <?php echo $selected; ?>value="<?php echo absint( $tour->ID ); ?>"><?php echo esc_html( $tour->post_title ); ?></option><?php
			}
			?>
			</select>
		</p>

		<?php
	}

	/**
	 * Save opening times meta box data
	 */
	public function meta_boxes_save( $post_id, $post ) {

		// Bail out now if post data not sent
		if ( ! isset( $_POST['_vtours_latitude'] ) ) {
			return;
		}

		// Do nonce security check
		if ( ! wp_verify_nonce( $_POST['vtours_nonce'], __FILE__ ) ) {
			return;
		}

		// Sanitizing and storing data
		if ( isset( $_POST['_vtours_latitude'] ) ) {
			if ( is_numeric( $_POST['_vtours_latitude'] ) ) {
				$_vtours_latitude = $_POST['_vtours_latitude'];
			} else {
				$_vtours_latitude = 0;
			}
			update_post_meta( $post_id, '_vtours_latitude', $_vtours_latitude ); // Store the data
		}
		if ( isset( $_POST['_vtours_longitude'] ) ) {
			if ( is_numeric( $_POST['_vtours_longitude'] ) ) {
				$_vtours_longitude = $_POST['_vtours_longitude'];
			} else {
				$_vtours_longitude = 0;
			}
			update_post_meta( $post_id, '_vtours_longitude', $_vtours_longitude ); // Store the data
		}
		if ( isset( $_POST['_vtours_tourid'] ) ) {
			$_vtours_tourid = absint( $_POST['_vtours_tourid'] );
			update_post_meta( $post_id, '_vtours_tourid', $_vtours_tourid ); // Store the data
		}

	}

}
new Virtual_Tours_Metaboxes;
