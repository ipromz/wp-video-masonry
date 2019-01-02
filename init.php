<?php 
/*
Plugin Name: Video Masonry

*/
//require_once "admin/init.php";

class VM_Main {

	function __construct() {
		add_action( 'wp_enqueue_scripts', array($this , "front_scripts") );
		add_action( 'admin_enqueue_scripts', array($this , "backend_scripts") );
		add_action( 'init', array($this , "register_post_type") );
		add_shortcode("video_masonry" , array($this , "shortcode"));
		add_image_size( 'vm-masonry-small', 320, 193 , true);
		add_image_size( 'vm-masonry-large', 640, 386 , true);
		add_action("wp_head" , array($this , "add_lobster_font") );
	}
	
	function front_scripts() {
	    wp_enqueue_style( 'vm-style', plugins_url( "/css/style.css", __FILE__ ) );
	    wp_enqueue_style( 'vm-font-hn', plugins_url( "/font/font.css", __FILE__ ) );
		wp_enqueue_script( 'masonry', plugins_url( "/js/masonry.pkgd.min.js", __FILE__ ) , array("jquery"), '1.0.0', true);
		wp_enqueue_script( 'vm-script', plugins_url( "/js/script.js", __FILE__ ) , array(), '1.0.0', true);

	}


	function backend_scripts() {
		//wp_enqueue_style
	}

	function register_post_type() {

		$labels = array(
			'name'                  => _x( 'Videos', 'Post Type General Name', 'text_domain' ),
			'singular_name'         => _x( 'Video', 'Post Type Singular Name', 'text_domain' ),
			'menu_name'             => __( 'Video', 'text_domain' ),
			'name_admin_bar'        => __( 'Video', 'text_domain' ),
			'archives'              => __( 'Video Archives', 'text_domain' ),
			'attributes'            => __( 'Video Attributes', 'text_domain' ),
			'parent_item_colon'     => __( 'Parent Video:', 'text_domain' ),
			'all_items'             => __( 'All Videos', 'text_domain' ),
			'add_new_item'          => __( 'Add New Video', 'text_domain' ),
			'add_new'               => __( 'Add New', 'text_domain' ),
			'new_item'              => __( 'New Item', 'text_domain' ),
			'edit_item'             => __( 'Edit Item', 'text_domain' ),
			'update_item'           => __( 'Update Item', 'text_domain' ),
			'view_item'             => __( 'View Item', 'text_domain' ),
			'view_items'            => __( 'View Items', 'text_domain' ),
			'search_items'          => __( 'Search Item', 'text_domain' ),
			'not_found'             => __( 'Not found', 'text_domain' ),
			'not_found_in_trash'    => __( 'Not found in Trash', 'text_domain' ),
			'featured_image'        => __( 'Featured Image', 'text_domain' ),
			'set_featured_image'    => __( 'Set featured image', 'text_domain' ),
			'remove_featured_image' => __( 'Remove featured image', 'text_domain' ),
			'use_featured_image'    => __( 'Use as featured image', 'text_domain' ),
			'insert_into_item'      => __( 'Insert into item', 'text_domain' ),
			'uploaded_to_this_item' => __( 'Uploaded to this item', 'text_domain' ),
			'items_list'            => __( 'Items list', 'text_domain' ),
			'items_list_navigation' => __( 'Items list navigation', 'text_domain' ),
			'filter_items_list'     => __( 'Filter items list', 'text_domain' ),
		);
		$args = array(
			'label'                 => __( 'Video', 'text_domain' ),
			'labels'                => $labels,
			'supports'              => array( 'title', 'editor', 'thumbnail', 'page-attributes', ),
			'hierarchical'          => false,
			'public'                => true,
			'show_ui'               => true,
			'show_in_menu'          => true,
			'menu_position'         => 5,
			'menu_icon'             => 'dashicons-video-alt2',
			'show_in_admin_bar'     => true,
			'show_in_nav_menus'     => true,
			'can_export'            => true,
			'has_archive'           => true,		
			'exclude_from_search'   => false,
			'publicly_queryable'    => true,
			'capability_type'       => 'page',
		);
		register_post_type( 'masonry_vid', $args );

	}

	function shortcode($args) {
		//echo "the vidoe goes here";
		ob_start();
		echo "<div class='masonry_video'>";
		echo "<div class='masonry_video_main' style='display:none'>";

		$args = array(
					"post_type" => "masonry_vid",
					"posts_per_page" => 30
					);

		$qry = new WP_Query($args);
		$i = 0;
		while($qry->have_posts()) {
			$autoplay = "";
			$post_id = get_the_ID();
			$class = " not_started mv_vid_id_$post_id ";
			
			if($i == 0) {
				//$autoplay = " autoplay "; 
				//$state = " vid_playing ";
			}
			

			$qry->the_post();
			$url = get_field("video");
			$content = get_the_content();
			$poster = get_the_post_thumbnail_url(get_the_ID() , "vm-masonry-large"); 
			$button_text = get_field("button_text");
			$button_url = get_field("button_url");
			$show_overlay_after = get_field("show_overlay_after");
			$show_overlay_text_for = get_field("show_overlay_text_for");
			$play_next_video_after = get_field("play_next_video_after");
			$volume = get_field("volume");
			$size = get_field("size");
			$video_order = get_field("video_order");

			if($size == "2X") {
				$class .= " grid_twice_size ";
			}

			echo "<div class='masongry_grid $class' data-play-order='$video_order' data-overlay-after='$show_overlay_after' data-cta-after='$show_overlay_text_for' data-next-vid-duration='$play_next_video_after' volume='$volume' data-size='$size' >
					<div class='masonry_vid' >
						<video 	$autoplay data-poster='$poster'>
							<source src='{$url}'>
						</video>
					</div>
					<div class='masonry_text'>
						<div class='masonry_text_inner'>
							<div class='vid_p'>$content</div>
							<a href='$button_url' class='vid_cta'>$button_text</a>
						</div>
					</div>
				</div>";
			$i++;
		}
		echo "</div>"; 

		echo "<div class='masonry_vid_loader'>
				<div id='movingBallG'>
					<div class='movingBallLineG'></div>
					<div id='movingBallG_1' class='movingBallG'></div>
				</div>
			</div>";
			
		echo "</div>";
		$this->css();
		return ob_get_clean();
	}

	function css() {
		?>
		<style type="text/css">
			.masonry_vid_loader{min-height:90vh;padding-top:40vh}#movingBallG{position:relative;width:250px;height:19px;margin:auto}.movingBallG,.movingBallLineG{background-color:#fff;position:absolute;left:0}.movingBallLineG{top:8px;height:4px;width:250px}.movingBallG{top:0;width:19px;height:19px;border-radius:10px;-o-border-radius:10px;-ms-border-radius:10px;-webkit-border-radius:10px;-moz-border-radius:10px;animation-name:bounce_movingBallG;-o-animation-name:bounce_movingBallG;-ms-animation-name:bounce_movingBallG;-webkit-animation-name:bounce_movingBallG;-moz-animation-name:bounce_movingBallG;animation-duration:1.5s;-o-animation-duration:1.5s;-ms-animation-duration:1.5s;-webkit-animation-duration:1.5s;-moz-animation-duration:1.5s;animation-iteration-count:infinite;-o-animation-iteration-count:infinite;-ms-animation-iteration-count:infinite;-webkit-animation-iteration-count:infinite;-moz-animation-iteration-count:infinite;animation-direction:normal;-o-animation-direction:normal;-ms-animation-direction:normal;-webkit-animation-direction:normal;-moz-animation-direction:normal}@keyframes bounce_movingBallG{0%,100%{left:0}50%{left:230px}}@-o-keyframes bounce_movingBallG{0%,100%{left:0}50%{left:230px}}@-ms-keyframes bounce_movingBallG{0%,100%{left:0}50%{left:230px}}@-webkit-keyframes bounce_movingBallG{0%,100%{left:0}50%{left:230px}}@-moz-keyframes bounce_movingBallG{0%,100%{left:0}50%{left:230px}}
		</style>
		<?php 
	}


	function add_lobster_font() {
		echo '<link href="https://fonts.googleapis.com/css?family=Lobster" rel="stylesheet">';
	}
}

new VM_Main();