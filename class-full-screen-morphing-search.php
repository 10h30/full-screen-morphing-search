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

		// Require WordPress jQuery.
		wp_enqueue_script( 'jquery' );

		// Require jquery-ui-core and jquery-ui-autocomplete for autoco;pletition search !
		wp_enqueue_script( 'jquery-ui-core' );
		wp_enqueue_script( 'jquery-ui-autocomplete' );

		// Load Javascript.
		wp_enqueue_script( $this->plugin->name, $this->plugin->url . 'assets/js/full-screen-morphing-search.js', array( 'jquery' ), '1.0', true );
		$fsmsp_settings = array(
			'fsmsp_search_text' => get_option( 'fsmsp_options' )['fsmsp_search_form_text'],
		);
		wp_localize_script( 'full-screen-morphing-search', 'fsmsp_set', $fsmsp_settings );
	}

	/**
	 * Outputs the HTML markup for our Full Screen Morphing Search
	 * CSS hides this by default, and Javascript displays it when the user
	 * interacts with any WordPress search field
	 *
	 * @since 1.1
	 */
	public function full_screen_morphing_search_output_morphing_search() {
		// Setting the placeholder as a variable to output the option depending on the case.
		$fsmsp_options     = get_option( 'fsmsp_options' ); // Associative Array 'fsmsp_options'.
		$fsmsp_placeholder = $fsmsp_options['fsmsp_search_form_text']; // Selecting the key to get the value.
		if ( '' !== $fsmsp_placeholder ) {
			$fsmsp_placeholder = esc_attr( $fsmsp_placeholder );
		} else {
			$fsmsp_placeholder = esc_attr_x( 'Search &hellip;', 'placeholder', 'full-screen-morphing-search' );
		}
		?>
		<div id="morphsearch" class="morphsearch">
			<span class="morphsearch-close"></span>
			<form role="search" class="morphsearch-form" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
				<input required type="search" class="morphsearch-input" name="s"
				placeholder="<?php echo esc_attr( $fsmsp_placeholder ); ?>"
				value="" />
				<button class="morphsearch-submit" type="submit"><?php echo file_get_contents( dirname( __FILE__ ) . '/assets/img/magnifier.svg' ); ?></button> <!-- Using "inline" SVG - https://css-tricks.com/using-svg/#article-header-id-7 -->
			</form>

			<div class="morphsearch-content">
				<div class="dummy-column">
					<h2 class="fsmsp-rp">Recent Posts</h2>
					<?php
					$args    = array(
						'post_type'           => 'post',
						'posts_per_page'      => '5',
						'ignore_sticky_posts' => 1,
					);
					$fsmsprp = new WP_Query( $args );
					while ( $fsmsprp->have_posts() ) :
						$fsmsprp->the_post();
						?>
					<div class="dummy-media-object">
						<a href="<?php echo the_permalink(); ?>" title="<?php the_title_attribute(); ?>">
							<?php
							if ( has_post_thumbnail() ) {
								echo the_post_thumbnail( 'full-screen-morphing-search-plugin-thumb', array( 'class' => 'round' ) );
							} else {
								echo '<img src="' . esc_url( plugins_url( 'assets/img/article.png', __FILE__ ) ) . '" > ';
							}
							?>
						</a>                                     
						<h3><a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h3>
					</div>
						<?php
					endwhile;
					wp_reset_postdata();
					?>
				</div>
				<div class="dummy-column">
					<h2 class="fsmsp-tc">Top Categories</h2>
					<?php
					$fsmspcats = get_categories();
					if ( empty( $fsmspcats ) ) {
						return;
					}
					$fsmsptc_counts = array();
					$fsmspcat_links = array();
					foreach ( (array) $fsmspcats as $fsmspcat ) {
						$fsmsptc_counts[ $fsmspcat->name ] = $fsmspcat->count;
						$fsmspcat_links[ $fsmspcat->name ] = get_category_link( $fsmspcat->term_id );
					}
					asort( $fsmsptc_counts );
					$fsmsptc_counts = array_reverse( $fsmsptc_counts, true );
					$i              = 0;
					foreach ( $fsmsptc_counts as $fsmspcat => $fsmsptc_count ) {
						$i++;
						$fsmspcat_link = esc_url( $fsmspcat_links[ $fsmspcat ] );
						$fsmspcat      = str_replace( ' ', '&nbsp;', esc_html( $fsmspcat ) );
						if ( $i < 6 ) {
							?>
							<div class="dummy-media-object">
								<?php
								echo '<img src="' . esc_url( plugins_url( 'assets/img/category.png', __FILE__ ) ) . '" > ';
								print "<h3><a href='" . esc_url( $fsmspcat_link ) . "'>" . esc_html( $fsmspcat . ' (' . $fsmsptc_count . ')' ) . '</a></h3>';
								?>
							</div>
							<?php
						}
					}
					?>
				</div>
				<div class="dummy-column">
					<h2 class="fsmsp-tt">Top Tags</h2>
					<?php
					$fsmsptags = get_tags();
					if ( empty( $fsmsptags ) ) {
						return;
					}
					$fsmsptt_counts = array();
					$fsmsptag_links = array();
					foreach ( (array) $fsmsptags as $fsmsptag ) {
						$fsmsptt_counts[ $fsmsptag->name ] = $fsmsptag->count;
						$fsmsptag_links[ $fsmsptag->name ] = get_tag_link( $fsmsptag->term_id );
					}
					asort( $fsmsptt_counts );
					$fsmsptt_counts = array_reverse( $fsmsptt_counts, true );
					$i              = 0;
					foreach ( $fsmsptt_counts as $fsmsptag => $fsmsptt_count ) {
						$i++;
						$tag_link = esc_url( $fsmsptag_links[ $fsmsptag ] );
						$fsmsptag = str_replace( ' ', '&nbsp;', esc_html( $fsmsptag ) );
						if ( $i < 6 ) {
							?>
							<div class="dummy-media-object">
								<?php
								echo '<img src="' . esc_url( plugins_url( 'assets/img/tag.png', __FILE__ ) ) . '" > ';
								print "<h3><a href='" . esc_url( $tag_link ) . "'>" . esc_html( $fsmsptag . ' (' . $fsmsptt_count . ')' ) . '</a></h3>';
								?>
							</div>
							<?php
						}
					}
					?>

				</div>                            
			</div><!-- .morphsearch-content -->
		</div><!-- #morphsearch.morphsearch -->

		<?php
			$fsmsp_ac = array( // Autocomplete.
				'post_type'      => array( 'post', 'page' ),
				'post_status'    => 'publish',
				'posts_per_page' => -1, // all posts and pages.
			);
			$posts    = get_posts( $fsmsp_ac );

		if ( $posts ) :
			foreach ( $posts as $k => $post ) {
				$source[ $k ]['ID']        = $post->ID;
				$source[ $k ]['label']     = $post->post_title; // The name of the post.
				$source[ $k ]['permalink'] = get_permalink( $post->ID );
			}

			?>
				<script type="text/javascript">
					jQuery(document).ready(function($){
						var posts = <?php echo wp_json_encode( array_values( $source ) ); ?>;
						jQuery( 'input[name="s"]' ).autocomplete({
						source: posts,
						minLength: 2,
						select: function(event, ui) {
							var permalink = ui.item.permalink; // Get permalink from the datasource
							window.location.replace(permalink);
							}
						});
					});
				</script>
				<?php
		endif;
	}
}
$full_screen_morphing_search = new Full_Screen_Morphing_Search();