<?php
/**
 * Preview image on wp.org
 *
 * @package Hestia
 */

/**
 * Class Hestia_Wp_Preview
 */
class Hestia_Wp_Preview extends Hestia_Abstract_Main {

	/**
	 * Init function
	 */
	public function init() {
		if ( $this->is_wp_preview() ) {
			$this->loader->add_filter( 'post_thumbnail_html', $this, 'post_thumbnail' );
		}
	}

	/**
	 * Filter thumbnail image
	 *
	 * @param string $input Post thumbnail.
	 */
	public function post_thumbnail( $input ) {
		if ( empty( $input ) ) {
			$placeholder = $this->get_preview_img_src();
			return '<img width="360" height="240" src="' . esc_url( $placeholder ) . '" class="attachment-hestia-blog size-hestia-blog wp-post-image">';
		}
		return $input;
	}

	/**
	 * Get a random image from demo content
	 * Can be recursive if a specific img size is not found
	 *
	 * @param int $i Maximum number of recalls.
	 *
	 * @return mixed
	 */
	private function get_preview_img_src( $i = 0 ) {
		// prevent infinite loop
		if ( 10 == $i ) {
			return '';
		}

		$path = get_template_directory() . '/inc/admin/wp-preview/images/';

		// Build or re-build the global dem img array
		if ( ! isset( $GLOBALS['prevdem_img'] ) || empty( $GLOBALS['prevdem_img'] ) ) {
			$imgs       = array( '1.jpg', '2.jpg', '3.jpg', '4.jpg', '5.jpg', '6.jpg', '7.jpg', '8.jpg', '9.jpg', '10.jpg' );
			$candidates = array();

			foreach ( $imgs as $img ) {
				$candidates[] = $img;
			}
			$GLOBALS['prevdem_img'] = $candidates;
		}
		$candidates = $GLOBALS['prevdem_img'];
		// get a random image name
		$rand_key = array_rand( $candidates );
		$img_name = $candidates[ $rand_key ];

		// if file does not exists, reset the global and recursively call it again
		if ( ! file_exists( $path . $img_name ) ) {
			unset( $GLOBALS['prevdem_img'] );
			$i++;
			return $this->get_preview_img_src( $i );
		}

		// unset all sizes of the img found and update the global
		$new_candidates = $candidates;
		foreach ( $candidates as $_key => $_img ) {
			if ( substr( $_img, 0, strlen( "{$img_name}" ) ) === "{$img_name}" ) {
				unset( $new_candidates[ $_key ] );
			}
		}
		$GLOBALS['prevdem_img'] = $new_candidates;
		return get_template_directory_uri() . '/inc/admin/wp-preview/images/' . $img_name;
	}

	/**
	 * Check if it is demo preview
	 *
	 * @return bool
	 */
	private function is_wp_preview() {

		$ti_theme     = wp_get_theme();
		$theme_name   = $ti_theme->get( 'TextDomain' );
		$active_theme = $this->get_raw_option( 'template' );

		if ( is_child_theme() ) {
			$theme_name      = get_option( 'stylesheet' );
			$template_name   = $ti_theme->get( 'Template' );
			$stylesheet_name = $this->get_raw_option( 'stylesheet' );

			return apply_filters( 'hestia_isprevdem', ( ( $active_theme != strtolower( $theme_name ) ) && ( $template_name == $stylesheet_name ) ) );
		}

		return apply_filters( 'hestia_isprevdem', $active_theme != strtolower( $theme_name ) );

	}

	/**
	 * All options or a single option val
	 *
	 * @param string $opt_name Option name.
	 *
	 * @return bool|mixed
	 */
	private function get_raw_option( $opt_name ) {
		$alloptions = wp_cache_get( 'alloptions', 'options' );
		$alloptions = maybe_unserialize( $alloptions );
		return isset( $alloptions[ $opt_name ] ) ? maybe_unserialize( $alloptions[ $opt_name ] ) : false;
	}

}
