<?php
/**
 * Full Screen Morphing Search Class
 *
 * Morphs any WordPress search input into a fullscreen overlay.
 *
 * @package WordPress
 * @since 1.1
 */

/**
 * Class Full_Screen_Morphing_Search
 */
class Full_Screen_Morphing_Search {

	/**
	 * Stores information about the Plugin
	 *
	 * @since 1.1
	 * @var plugin
	 */
	public $plugin = '';

	/**
	 * Plugin constructor.
	 *
	 * Adds necessary action and filter hooks for the plugin.
	 *
	 * @since 1.0
	 * @access public
	 */
	public function __construct() {

		// Setup Plugin Details.
		$this->plugin         = new stdClass();
		$this->plugin->name   = 'full-screen-morphing-search';
		$this->plugin->folder = plugin_dir_path( __FILE__ );
		$this->plugin->url    = plugin_dir_url( __FILE__ );

		// Add actions if we're not in the WordPress Administration to load CSS, JS and the Morphing Search HTML.
		if ( ! is_admin() ) {
			add_action( 'wp_head', array( $this, 'full_screen_morphing_search_enqueue_css_js' ) );
			add_action( 'wp_footer', array( $this, 'full_screen_morphing_search_output_morphing_search' ) );
		}

	}

	/**
	 * Loads the CSS and JS used for this plugin.
	 *
	 * @since 1.1
	 */
	public function full_screen_morphing_search_enqueue_css_js() {

		// Load CSS.
		wp_enqueue_style( $this->plugin->name, $this->plugin->url . 'assets/css/full-screen-morphing-search.css', array(), '1.0', false );

		// Load Javascript.
		wp_enqueue_script( $this->plugin->name, $this->plugin->url . 'assets/js/full-screen-morphing-search.js', array( 'jquery' ), '1.0', true );

	}

	/**
	 * Outputs the HTML markup for our Full Screen Morphing Search
	 * CSS hides this by default, and Javascript displays it when the user
	 * interacts with any WordPress search field
	 *
	 * @since 1.1
	 */
	public function full_screen_morphing_search_output_morphing_search() {

		?>

			<div id="morphsearch" class="morphsearch">
				<span class="morphsearch-close"></span>
				<form role="search" class="morphsearch-form" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
					<input required id="morphsearch-input" type="search" class="morphsearch-input" name="s" placeholder="Tìm sản phẩm..." value=""/>
					<button id="morphsearch-submit" class="morphsearch-submit" type="submit">
                    <?php
						$response = wp_remote_get( 'https://plugins.svn.wordpress.org/full-screen-morphing-search/trunk/assets/img/magnifier.svg' );
						if ( is_array( $response ) && ! is_wp_error( $response ) ) {
							echo wp_kses( $response['body'], 'full_screen_morphing_search_add_svg_tags' ); // use the content.
						}
						?>
					</button>
				</form>

			</div><!-- #morphsearch.morphsearch -->

		<?php
		

	}

}
$full_screen_morphing_search = new Full_Screen_Morphing_Search();
