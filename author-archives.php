<?php
/**
 * @package Author_Archives
 * @version 1.0
 */
/*
Plugin Name: Author Archives
Version: 1.0
Description: Displays author specific archives widget on author's single post page.
Author: Shwetuk
*/
class wpb_author_archives extends WP_Widget {

	function __construct() {
		parent::__construct(
		// Base ID
		'wpb_author_archives', 

		// Widget name will appear in UI
		__('Author Archives', 'wpb_author_archives_domain'), 

		// Widget description
		array( 'description' => __( 'A monthly archive of your author’s Posts which appears on single post page.', 'wpb_author_archives_domain' ), ) 
		);
	}
	public function form( $instance ) {
	if ( isset( $instance[ 'title' ] ) ) {
	$title = $instance[ 'title' ];
	}
	else {
	//$title = 'Archives';
	$title = __( 'Author Archives', 'wpb_author_archives_domain' );
	}
	// Widget admin form

	?>
	<p>
	<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
	<input id="<?php echo $this->get_field_id( 'title' ); ?>" class="widefat"  name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
	</p>
	<?php 
	}
		// Creating widget front-end
	public function widget( $args, $instance ) {
		$title = ( ! empty( $instance['title'] ) ) ? $instance['title'] : __( 'Author Archives' );
		$title = apply_filters( 'widget_title', $title );

		if(is_single()){
			global $post;
			$blogtime = date('Y');
			$prev_limit_year = $blogtime - 1;
			$prev_year = $prev_month = '';
			$current_user_id = $post->post_author;
			$postargs = array(
					 'posts_per_page' => 20,
					 'ignore_sticky_posts' => 1,
					 'author' => $current_user_id
			);
			$postsbymonth = new WP_Query($postargs);
			if($postsbymonth->have_posts()){
			echo $args['before_widget'];
			
			if ( ! empty( $title ) )
			$authorname = get_the_author_meta( 'display_name', $current_user_id );
			$title = str_replace('[authorname]', $authorname.'\'s', $title);
			echo $args['before_title'] . $title . $args['after_title'];
			while($postsbymonth->have_posts()) {
				$postsbymonth->the_post();
				if(get_the_time('F') != $prev_month || get_the_time('Y') != $prev_year && get_the_time('Y') == $prev_limit_year) {
	   ?>
				<ul class="author-archives">
				<li><a href="<?php echo get_site_url().'/?m='.get_the_time('Ym'); ?>&author=<?php echo $current_user_id;?>"> <?php echo get_the_time('F, Y'); ?></a></li>
				</ul>
	<?php
					}
				$prev_month = get_the_time('F');
				$prev_year = get_the_time('Y');
			}
			echo $args['after_widget'];
			}
			wp_reset_postdata();		
		
		}
	}	
		// Widget Backend 
		// Updating widget replacing old instances with new
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		return $instance;
		}
	} // Class wpb_author_archives ends here

		// Register and load the widget
	function wpb_load_widget() {
			register_widget( 'wpb_author_archives' );
	}
	add_action( 'widgets_init', 'wpb_load_widget' );
	/* Register the style sheet */

	function custom_stylesheet() {
					wp_register_style('custom_stylesheet', plugins_url('css/author-archives.css', __FILE__) );
					wp_enqueue_style('custom_stylesheet');
	}
	add_action('wp_enqueue_scripts', 'custom_stylesheet');
?>