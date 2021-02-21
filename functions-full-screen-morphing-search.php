<?php
/**
 * Full Screen Morphing Search Functions
 *
 * @package WordPress
 */


if ( ! function_exists( 'full_screen_morphing_search_add_svg_tags' ) ) {
	/**
	 * Function to add svg tags to wp_kses_allowed_html
	 *
	 * @see http://themelovin.com/add-allowed-html-tags-wordpress/
	 *
	 * To secure the output, to correctly escape it !
	 * @see https://wordpress.stackexchange.com/a/316943
	 *
	 * @param array $svg_tags The SVG Tags.
	 *
	 * @since 2.4
	 */
	function full_screen_morphing_search_add_svg_tags( $svg_tags ) {
		$svg_tags['svg']  = array(
			'version'     => true,
			'id'          => true,
			'xmlns'       => true,
			'xmlns:xlink' => true,
			'x'           => true,
			'y'           => true,
			'viewbox'     => true, // <= Must be lower case !
			'style'       => true,
			'xml:space'   => true,
		);
		$svg_tags['path'] = array(
			'fill' => true,
			'd'    => true,
		);
		return $svg_tags;
	}
	add_filter( 'wp_kses_allowed_html', 'full_screen_morphing_search_add_svg_tags' );
}
