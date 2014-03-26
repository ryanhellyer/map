<?php

/**
 * The main template file.
 *
 * @package Virtual Tours
 * @since Virtual Tours 1.0
 */

?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>" />
<meta name="viewport" content="width=device-width" />
<title><?php wp_title( '|', true, 'right' ); ?></title>
<link rel="profile" href="http://gmpg.org/xfn/11" />
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
<!--[if lt IE 9]>
<script src="<?php echo get_template_directory_uri(); ?>/js/html5.js" type="text/javascript"></script>
<![endif]-->
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>

<!-- The main menu - left hand side -->
<nav id="main-menu">
	<h1>Main menu</h1>
	<div id="main-menu-inner">
		<ul><?php
			$args = array( 'posts_per_page' => 10, 'post_type' => 'tour' );
			$tours = get_posts( $args );
			$args = array( 'posts_per_page' => 10, 'post_type' => 'page' );
			$pages = get_posts( $args );
			$menu = array_merge( $tours, $pages );

			foreach ( $menu as $post ) {
				setup_postdata( $post );
				?>
				<li>
					<a href="<?php the_permalink(); ?>">
						<div>
							<h2><?php the_title(); ?></h1>
							<?php the_excerpt(); ?>
						</div>
						<?php

						// The post thumbnail
						if ( has_post_thumbnail() ) {
							the_post_thumbnail( 'small' );
						}
						?>
					</a>
				</li>
				<?php
			}
			?>
		</ul>
	</div>
</nav>

<!-- The map - right hand side-->
<nav id="map-wrapper">
	<div id="map-inner">

		<p>
			Origin latitude : <input type="text" id="origin_latitude" /> 
			Origin longitude: <input type="text" id="origin_longitude" /> 
			<br />
			Destination latitude: <input type="text" id="destination_latitude" value="51.510177" /> 
			Destination longitude: <input type="text" id="destination_longitude" value="13.4688059" /> 
		</p>

		<div id="map" style="border:1px solid lime;width: 100%; height: 400px"></div>
		<button id="bla" value="action">Action</button>

	</div>
</nav>

<!-- The main content area -->
<div id="main-content">
	<nav id="quick-menu">
		<ul>
			<li id="show-menu"><a href="#">Menu</a></li>
			<li id="play-current-audio">Play</li>
			<li id="show-map"><a href="#">Map</a></li>
		</ul>
	</nav>


	<div id="location-slider">
		<div id="location-slider-wrap">
<?php
// Load main loop
if ( have_posts() ) {

	// Start of the Loop
	while ( have_posts() ) {
		the_post();
		$tour_id = get_the_ID();

		?>
			<div class="location-slide">

				<h2><?php the_title(); ?></h2><?php

				the_content();

				// Display list of locations
				$args = array(
					'post_type'      => 'location',
					'numberposts'    => VTOURS_MAX_LOCATIONS, // Limit number of locations for performance reasons
					// ******************************** NEED TO ENSURE QUERY ONLY DOES REQUIRED META VALUES
				);

				$count = 0;
				$locations = get_posts( $args );
				foreach( $locations as $location ) {

					if ( $tour_id != get_post_meta( $location->ID, '_vtours_tourid', true ) ) { // NOT NEEDED IF QUERY RUN CORRECTLY ABOVE
						unset( $locations[$count] );
					}

					$count++;
				}

				if ( ! empty( $locations ) ) :
				?>
				<h2><?php _e( 'Locations', 'vtours' ); ?></h2>
				<ul id="positions">
					<li id="button-0">Home</li><?php
				$count = 1;
				foreach( $locations as $location ) {

					?>
					<li id="button-<?php echo absint( $count ); ?>">
						<?php echo esc_html( $location->post_title ); ?>
					</li><?php
					$count++;
				}
				endif;
				?></ul>

			</div><?php
	}
}

// Output individual location slides
foreach( $locations as $location ) {
	if ( $tour_id == get_post_meta( $location->ID, '_vtours_tourid', true ) ) { // NOT NEEDED IF QUERY RUN CORRECTLY ABOVE
		?>
			<div class="location-slide">
				<h2><?php echo esc_html( $location->post_title ); ?></h2>
				<?php echo apply_filters( 'the_content', $location->post_content ); ?>
			</div><?php
	}
}
?>
		</div>
	</div>

	<button id="prev">prev</button>
	<button id="next">next</button>

</div><!-- #main-content -->


<?php wp_footer(); ?>
</body>
</html>