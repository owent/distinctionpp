<?php
/**
 * distinctionpp functions and definitions
 *
 * Sets up the theme and provides some helper functions. Some helper functions
 * are used in the theme as custom template tags. Others are attached to action and
 * filter hooks in WordPress to change core functionality.
 *
 * The first function, wp_distinctionpp_setup(), sets up the theme by registering support
 * for various features in WordPress, such as post thumbnails, post formats, navigation menus, and the like.
 *
 * When using a child theme (see http://codex.wordpress.org/Theme_Development and
 * http://codex.wordpress.org/Child_Themes), you can override certain functions
 * (those wrapped in a function_exists() call) by defining them first in your child theme's
 * functions.php file. The child theme's functions.php file is included before the parent
 * theme's file, so the child theme functions would be used.
 *
 * Functions that are not pluggable (not wrapped in function_exists()) are instead attached
 * to a filter or action hook. The hook can be removed by using remove_action() or
 * remove_filter() and you can attach your own function to the hook.
 *
 * We can remove the parent theme's hook only after it is attached, which means we need to
 * wait until setting up the child theme:
 *
 * <code>
 * add_action( 'after_setup_theme', 'my_child_theme_setup' );
 * function my_child_theme_setup() {
 *     // We are providing our own filter for excerpt_length (or using the unfiltered value)
 *     remove_filter( 'excerpt_length', 'distinction_excerpt_length' );
 *     ...
 * }
 * </code>
 *
 * For more information on hooks, actions, and filters, see http://codex.wordpress.org/Plugin_API.
 *
 * @package WordPress
 * @subpackage distinctionpp
 * @since distinctionpp 1.2
 */

if ( ! function_exists( 'wp_distinctionpp_setup' ) ):
/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which runs
 * before the init hook. The init hook is too late for some features, such as indicating
 * support post thumbnails.
 *
 * To override distinction_setup() in a child theme, add your own distinction_setup to your child theme's
 * functions.php file.
 *
 * @uses add_theme_support() To add support for post thumbnails and automatic feed links.
 * @uses register_nav_menus() To add support for navigation menus.
 * @uses add_theme_support( 'custom-background', $args )  To add support for a custom background.
 * @uses add_editor_style() To style the visual editor.
 * @uses load_theme_textdomain() For translation/localization support.
 * @uses add_custom_image_header() To add support for a custom header.
 * @uses register_default_headers() To register the default custom header images provided with the theme.
 * @uses set_post_thumbnail_size() To set a custom post thumbnail size.
 *
 * @since distinctionpp 1.0
 */

/** Tell WordPress to run distinction_setup() when the 'after_setup_theme' hook is run. */
add_action( 'after_setup_theme', 'wp_distinctionpp_setup' );

function wp_distinctionpp_setup() {

    add_theme_support( 'title-tag' );

	// This theme styles the visual editor with editor-style.css to match the theme style.
	// add time() to the end of editor-style.css so that you'll always see the latest version
	add_editor_style('editor-style.css?' . time());

	// This theme uses post thumbnails
	add_theme_support( 'post-thumbnails' );

	// Add default posts and comments RSS feed links to head
	add_theme_support( 'automatic-feed-links' );

	// Add Post Format capabilities
	add_theme_support( 'post-formats', array( 'aside', 'gallery', 'link', 'image', 'quote', 'status', 'video', 'audio', 'chat' ) );

	// Make theme available for translation
	// Translations can be filed in the /languages/ directory
	load_theme_textdomain( 'wp_distinctionpp', get_template_directory() . '/languages' );

	$locale = get_locale();
	$locale_file = get_template_directory() . "/languages/$locale.php";
	if ( is_readable( $locale_file ) )
		require_once( $locale_file );

	// This theme uses wp_nav_menu() in one location.
	register_nav_menus( array(
		'primary' => __( 'Primary Navigation', 'wp_distinctionpp' ),
	) );

	// This theme allows users to set a custom background
	add_theme_support('custom-background');

    add_theme_support( "custom-header");

}
endif;

/**
 * Set the content width based on the theme's design and stylesheet.
 *
 * Used to set the width of images and content. Should be equal to the width the theme
 * is designed for, generally via the style.css stylesheet.
 */
if ( ! isset( $content_width ) )
	$content_width = 383;

// Shortcodes & Settings
require_once (get_template_directory() . '/functions/shortcodes.php');
require( get_template_directory() . '/functions/options.php' );

/**
 * Get our wp_nav_menu() fallback, wp_page_menu(), to show a home link.
 *
 * To override this in a child theme, remove the filter and optionally add
 * your own function tied to the wp_page_menu_args filter hook.
 *
 * @since distinctionpp 1.0
 */
function wp_distinctionpp_page_menu_args( $args ) {
	$args['show_home'] = true;
	return $args;
}
add_filter( 'wp_page_menu_args', 'wp_distinctionpp_page_menu_args' );

/**
 * Filters wp_title to print a neat <title> tag based on what is being viewed.
 *
 * @param string $title Default title text for current view.
 * @param string $sep   Optional separator.
 * @return string The filtered title.
 */
function wp_distinctionpp_title_format($title, $sep) {
    /*
	 * Print the <title> tag based on what is being viewed.
	 */
	global $page, $paged;
	// Add the blog name
    $title .= get_bloginfo( 'name', 'display' );

	// Add the blog description for the home/front page.
	$site_description = get_bloginfo( 'description', 'display' );
	if ( $site_description && ( is_home() || is_front_page() ) )
		$title .= " $sep $site_description";

	// Add a page number if necessary:
	if ( $paged >= 2 || $page >= 2 )
		$title .= " $sep " . sprintf( __( 'Page %s', '_s' ), max( $paged, $page ) );

    return $title;
}
add_filter( 'wp_title', 'wp_distinctionpp_title_format', 10, 2 );

function wp_distinctionpp_init_method() {
	if(!is_admin()) {
		wp_enqueue_script( 'jquery' );
		// wp_enqueue_script( 'es5-shim', get_template_directory_uri() . '/js/es5-shim/es5-shim.min.js?ver=4.5.6' );
		// wp_enqueue_script( 'less', get_template_directory_uri() . '/js/less.min.js?ver=2.1.0' , array('es5-shim') );
		// wp_enqueue_script( 'bootstrap', get_template_directory_uri() . '/js/bootstrap/js/bootstrap.min.js?ver=3.3.6' , array('jquery') );
	    wp_enqueue_script( 'simplemetro-plugin', get_template_directory_uri() . '/js/SimpleMetro.plugin.min.js?ver=0.8.0', array('jquery') );
		wp_enqueue_script( 'distinctionpp-js', get_template_directory_uri() . '/js/distinctionpp.min.js', array('jquery', 'simplemetro-plugin') );
	}
}

add_action('init', 'wp_distinctionpp_init_method');


/**
 * Sets the post excerpt length to 40 characters.
 *
 * To override this length in a child theme, remove the filter and add your own
 * function tied to the excerpt_length filter hook.
 *
 * @since distinctionpp 1.0
 * @return int
 */
function wp_distinctionpp_excerpt_length( $length ) {
	return 40;
}
add_filter( 'excerpt_length', 'wp_distinctionpp_excerpt_length' );

/**
 * Returns a "Continue Reading" link for excerpts
 *
 * @since distinctionpp 1.0
 * @return string "Continue Reading" link
 */
function wp_distinctionpp_continue_reading_link() {
	return ' <a href="'. get_permalink() . '">' . __( 'Continue reading <span class="meta-nav">&rarr;</span>', 'wp_distinctionpp' ) . '</a>';
}

/**
 * Replaces "[...]" (appended to automatically generated excerpts) with an ellipsis and distinction_continue_reading_link().
 *
 * To override this in a child theme, remove the filter and add your own
 * function tied to the excerpt_more filter hook.
 *
 * @since distinctionpp 1.0
 * @return string An ellipsis
 */
function wp_distinctionpp_auto_excerpt_more( $more ) {
	return ' &hellip;' . wp_distinctionpp_continue_reading_link();
}
add_filter( 'excerpt_more', 'wp_distinctionpp_auto_excerpt_more' );

/**
 * Adds a pretty "Continue Reading" link to custom post excerpts.
 *
 * To override this link in a child theme, remove the filter and add your own
 * function tied to the get_the_excerpt filter hook.
 *
 * @since distinctionpp 1.0
 * @return string Excerpt with a pretty "Continue Reading" link
 */
function wp_distinctionpp_custom_excerpt_more( $output ) {
	if ( has_excerpt() && ! is_attachment() ) {
		$output .= wp_distinctionpp_continue_reading_link();
	}
	return $output;
}
add_filter( 'get_the_excerpt', 'wp_distinctionpp_custom_excerpt_more' );

/**
 * Remove inline styles printed when the gallery shortcode is used.
 *
 * Galleries are styled by the theme in distinctionpp's style.css.
 *
 * @since distinctionpp 1.0
 * @return string The gallery style filter, with the styles themselves removed.
 */
function wp_distinctionpp_remove_gallery_css( $css ) {
	return preg_replace( "#<style type='text/css'>(.*?)</style>#s", '', $css );
}
add_filter( 'gallery_style', 'wp_distinctionpp_remove_gallery_css' );

if ( ! function_exists( 'wp_distinctionpp_comment' ) ) :
/**
 * Template for comments and pingbacks.
 *
 * To override this walker in a child theme without modifying the comments template
 * simply create your own distinction_comment(), and that function will be used instead.
 *
 * Used as a callback by wp_list_comments() for displaying the comments.
 *
 * @since distinctionpp 1.0
 */
function wp_distinctionpp_comment( $comment, $args, $depth ) {
	$GLOBALS['comment'] = $comment;
	switch ( $comment->comment_type ) :
		case '' :
	?>
	<li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">
		<div id="comment-<?php comment_ID(); ?>">
		<div class="comment-author vcard">
			<?php echo get_avatar( $comment, 40 ); ?>
			<?php printf( __( '%s <span class="says">says:</span>', 'wp_distinctionpp' ), sprintf( '<cite class="fn">%s</cite>', get_comment_author_link() ) ); ?>
		</div><!-- .comment-author .vcard -->
		<?php if ( $comment->comment_approved == '0' ) : ?>
			<em><?php _e( 'Your comment is awaiting moderation.', 'wp_distinctionpp' ); ?></em>
			<br />
		<?php endif; ?>

		<div class="comment-meta commentmetadata"><a href="<?php echo esc_url( get_comment_link( $comment->comment_ID ) ); ?>">
			<?php
				/* translators: 1: date, 2: time */
				printf( __( '%1$s at %2$s', 'wp_distinctionpp' ), get_comment_date(),  get_comment_time() ); ?></a><?php edit_comment_link( __( '(Edit)', 'wp_distinctionpp' ), ' ' );
			?>
		</div><!-- .comment-meta .commentmetadata -->

		<div class="comment-body"><?php comment_text(); ?></div>

		<div class="reply">
			<?php comment_reply_link( array_merge( $args, array( 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?>
		</div><!-- .reply -->
	</div><!-- #comment-##  -->

	<?php
			break;
		case 'pingback'  :
		case 'trackback' :
	?>
	<li class="post pingback">
		<p><?php _e( 'Pingback:', 'wp_distinctionpp' ); ?> <?php comment_author_link(); ?><?php edit_comment_link( __('(Edit)', 'wp_distinctionpp'), ' ' ); ?></p>
	<?php
			break;
	endswitch;
}
endif;

/**
 * Register widgetized areas, including two sidebars and four widget-ready columns in the footer.
 *
 * To override distinction_widgets_init() in a child theme, remove the action hook and add your own
 * function tied to the init hook.
 *
 * @since distinctionpp 1.0
 * @uses register_sidebar
 */
function wp_distinctionpp_widgets_init() {
	// Area 1, located at the top of the sidebar.
	register_sidebar( array(
		'name' => __( 'Primary Widget Area', 'distinctionpp' ),
		'id' => 'primary-widget-area',
		'description' => __( 'The primary widget area', 'distinctionpp' ),
		'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
		'after_widget' => '</li>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );

	// Area 2, located below the Primary Widget Area in the sidebar. Empty by default.
	register_sidebar( array(
		'name' => __( 'Secondary Widget Area', 'distinctionpp' ),
		'id' => 'secondary-widget-area',
		'description' => __( 'The secondary widget area', 'distinctionpp' ),
		'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
		'after_widget' => '</li>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );

	// Area 3, located in the footer. Empty by default.
	register_sidebar( array(
		'name' => __( 'First Footer Widget Area', 'distinctionpp' ),
		'id' => 'first-footer-widget-area',
		'description' => __( 'The first footer widget area', 'distinctionpp' ),
		'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
		'after_widget' => '</li>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );

	// Area 4, located in the footer. Empty by default.
	register_sidebar( array(
		'name' => __( 'Second Footer Widget Area', 'distinctionpp' ),
		'id' => 'second-footer-widget-area',
		'description' => __( 'The second footer widget area', 'distinctionpp' ),
		'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
		'after_widget' => '</li>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );

	// Area 5, located in the footer. Empty by default.
	register_sidebar( array(
		'name' => __( 'Third Footer Widget Area', 'distinctionpp' ),
		'id' => 'third-footer-widget-area',
		'description' => __( 'The third footer widget area', 'distinctionpp' ),
		'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
		'after_widget' => '</li>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );

	// Area 6, located in the footer. Empty by default.
	register_sidebar( array(
		'name' => __( 'Fourth Footer Widget Area', 'distinctionpp' ),
		'id' => 'fourth-footer-widget-area',
		'description' => __( 'The fourth footer widget area', 'distinctionpp' ),
		'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
		'after_widget' => '</li>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );
}
/** Register sidebars by running distinction_widgets_init() on the widgets_init hook. */
add_action( 'widgets_init', 'wp_distinctionpp_widgets_init' );

/**
 * Removes the default styles that are packaged with the Recent Comments widget.
 *
 * To override this in a child theme, remove the filter and optionally add your own
 * function tied to the widgets_init action hook.
 *
 * @since distinctionpp 1.0
 */
function wp_distinctionpp_remove_recent_comments_style() {
	global $wp_widget_factory;
	remove_action( 'wp_head', array( $wp_widget_factory->widgets['WP_Widget_Recent_Comments'], 'recent_comments_style' ) );
}
add_action( 'widgets_init', 'wp_distinctionpp_remove_recent_comments_style' );

if ( ! function_exists( 'wp_distinctionpp_posted_on' ) ) :
/**
 * Prints HTML with meta information for the current post—date/time and author.
 *
 * @since distinctionpp 1.0
 */
function wp_distinctionpp_posted_on() {
	printf( __( '%2$s', 'wp_distinctionpp' ),
		'meta-prep meta-prep-author',
		sprintf( '<a href="%1$s" title="%2$s" rel="bookmark"><span class="utility-date">%3$s</span></a>',
			get_permalink(),
			esc_attr( get_the_time() ),
			get_the_date()
		)
	);
}
endif;

if ( ! function_exists( 'wp_distinctionpp_posted_in' ) ) :
/**
 * Prints HTML with meta information for the current post (category, tags and permalink).
 *
 * @since distinctionpp 1.0
 */
function wp_distinctionpp_posted_in() {
	// Retrieves tag list of current post, separated by commas.
	$tag_list = get_the_tag_list( '', ', ' );
	if ( $tag_list ) {
		$posted_in = __( 'This entry was posted in %1$s and tagged %2$s. Bookmark the <a href="%3$s" title="Permalink to %4$s" rel="bookmark">permalink</a>.', 'wp_distinctionpp' );
	} elseif ( is_object_in_taxonomy( get_post_type(), 'category' ) ) {
		$posted_in = __( 'This entry was posted in %1$s. Bookmark the <a href="%3$s" title="Permalink to %4$s" rel="bookmark">permalink</a>.', 'wp_distinctionpp' );
	} else {
		$posted_in = __( 'Bookmark the <a href="%3$s" title="Permalink to %4$s" rel="bookmark">permalink</a>.', 'wp_distinctionpp' );
	}
	// Prints the string, replacing the placeholders.
	printf(
		$posted_in,
		get_the_category_list( ', ' ),
		$tag_list,
		get_permalink(),
		the_title_attribute( 'echo=0' )
	);
}
endif;


function wp_distinctionpp_embed_defaults($embed_size){
    if( is_single() ){ // If displaying a single post
        $embed_size['width'] = 560; // Adjust values to your needs
        //$embed_size['height'] = 500;
    }
	if( is_page() ){ // If displaying a single post
        $embed_size['width'] = 560; // Adjust values to your needs
        //$embed_size['height'] = 500;
    }
	if( is_page_template( 'onecolumn-page.php' ) ){ // If displaying a single post
        $embed_size['width'] = 855; // Adjust values to your needs
        //$embed_size['height'] = 500;
    }

    return $embed_size; // Return new size
}

add_filter('embed_defaults', 'wp_distinctionpp_embed_defaults');

if ( ! function_exists( 'wp_distinctionpp_pagination' ) ) :
/**
 * Prints HTML for pagination of posts on index and archive pages.
 *
 * @since distinctionpp 1.0
 */
function wp_distinctionpp_pagination(){
	global $wp_query, $wp_rewrite;
	$wp_query->query_vars['paged'] > 1 ? $current = $wp_query->query_vars['paged'] : $current = 1;
	$pagination = array(
		'base' => @add_query_arg('page','%#%'),
		'format' => '',
		'total' => $wp_query->max_num_pages,
		'current' => $current,
		'prev_next'    => True,
		'prev_text'    => __('&lsaquo;'),
		'next_text'    => __('&rsaquo;'),
	);
	if( $wp_rewrite->using_permalinks() )
	$pagination['base'] = user_trailingslashit( trailingslashit( remove_query_arg( 's', get_pagenum_link( 1 ) ) ) . 'page/%#%/', 'paged' );

	if( !empty($wp_query->query_vars['s']) )
	$pagination['add_args'] = array( 's' => get_query_var( 's' ) );

	echo '<div class="pagination">'.paginate_links( $pagination ).'</div><!-- .pagination --> ';

}
endif;

if ( ! function_exists( 'wp_distinctionpp_comment_pagination' ) ) :
/**
 * Prints HTML for pagination of comments.
 *
 * @since distinctionpp 1.0
 */
function wp_distinctionpp_comment_pagination(){
	global $wp_query, $wp_rewrite;
	$wp_query->query_vars['paged'] > 1 ? $current = $wp_query->query_vars['paged'] : $current = 1;
	$pagination = array(
		'base' => @add_query_arg('cpage','%#%'),
		'format' => '',
		'total' => get_comment_pages_count(),
		'current' => $current,
		'echo' => true,
		'prev_next'    => True,
		'prev_text'    => __('&lsaquo;'),
		'next_text'    => __('&rsaquo;'),
	);

	echo '<div class="pagination">';
	paginate_comments_links( $pagination );
	echo '</div><!-- .pagination --> ';

}
endif;

function wp_distinctionpp_footer_credit() { ?>
<div id="site-generator">

	<?php do_action( 'wp_distinctionpp_credits' ); ?>
    <a class="wp" href="<?php echo esc_url( __('http://wordpress.org/', 'wp_distinctionpp') ); ?>"
            title="<?php esc_attr_e('Semantic Personal Publishing Platform', 'wp_distinctionpp'); ?>" rel="generator">
        <?php printf( __('Powered by %s.', 'wp_distinctionpp'), 'WordPress' ); ?>
    </a>
    <a class="wptheme" href="<?php echo esc_url( __('http://www.owent.net', 'wp_distinctionpp') ); ?>"
            title="<?php esc_attr_e('OWenT', 'wp_distinctionpp'); ?>" rel="generator">
        <?php printf( __('Built by %s.', 'wp_distinctionpp'), 'OWenT' ); ?>
    </a>

</div><!-- #site-generator -->
<?php }

function wp_distinctionpp_analytics() {
	global $wp_distinctionpp_options;
	echo $wp_distinctionpp_options['analytics_code'];
}
add_action('wp_footer', 'wp_distinctionpp_analytics', 100);

function wp_distinctionpp_sidebar() {
	global $wp_distinctionpp_options;
	if ( 'left' == $wp_distinctionpp_options['sidebar_position'] ) { ?>
	<style type="text/css">
		#container {float: right;margin: 0 0 0 -270px;}
		#content {margin: 0 20px 0 280px;}
		#primary, #secondary {float: left;overflow: hidden;}
		#secondary {clear: left;}
		#main .widget-area ul {margin-right: 0;padding: 0 0 0 20px;
	</style>
	<?php }
}
add_action('wp_print_styles', 'wp_distinctionpp_sidebar');

// 解决gravatar被墙的问题
function get_ssl_avatar($avatar) {
   $avatar = preg_replace('/.*\/avatar\/(.*)\?s=([\d]+)&.*/','<img src="https://secure.gravatar.com/avatar/$1?s=$2" class="avatar avatar-$2" height="$2" width="$2">',$avatar);
   return $avatar;
}
add_filter('get_avatar', 'get_ssl_avatar');
