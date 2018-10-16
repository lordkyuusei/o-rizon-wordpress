<?php
/**
 * Footer Main Manager
 *
 * @package Hestia
 */

/**
 * Class Hestia_Footer
 */
class Hestia_Footer extends Hestia_Abstract_Main {
	/**
	 * Footer sidebars array.
	 *
	 * @var array the array with footer sidebars slugs.
	 */
	private $footer_sidebars_array = array(
		'footer-one-widgets',
		'footer-two-widgets',
		'footer-three-widgets',
		'footer-four-widgets',
	);

	/**
	 * The number of footer sidebars.
	 *
	 * @var int the number of sidebars in footer [default = 3].
	 */
	private $number_of_sidebars;

	/**
	 * Initialization of the feature.
	 */
	public function init() {
		$this->loader->add_action( 'hestia_do_footer', $this, 'the_footer_content' );
		$this->loader->add_filter( 'wp_nav_menu_args', $this, 'modify_footer_menu_classes' );
	}

	/**
	 * Render the footer sidebars.
	 */
	private function render_footer_sidebars() {

		if ( ! $this->does_footer_have_widgets() ) {
			return;
		}

		$this->number_of_sidebars    = get_theme_mod( 'hestia_nr_footer_widgets', '3' );
		$this->footer_sidebars_array = array_slice( $this->footer_sidebars_array, 0, $this->number_of_sidebars );

		if ( empty( $this->footer_sidebars_array ) ) {
			return;
		} ?>
		<div class="content">
			<div class="row">
				<?php
				foreach ( $this->footer_sidebars_array as $footer_sidebar ) {
					if ( is_active_sidebar( $footer_sidebar ) ) {
						echo '<div class="' . esc_attr( $this->the_sidebars_class() ) . '">';
						dynamic_sidebar( $footer_sidebar );
						echo '</div>';
					}
				}
				?>
			</div>
		</div>
		<hr/>
		<?php
	}

	/**
	 * Function to display footer content.
	 *
	 * @since 1.1.24
	 * @access public
	 */
	public function the_footer_content() {
		hestia_before_footer_trigger();

		?>
		<footer class="footer <?php echo esc_attr( $this->the_footer_class() ); ?> footer-big">
			<?php hestia_before_footer_content_trigger(); ?>
			<div class="container">
				<?php hestia_before_footer_widgets_trigger(); ?>
				<?php $this->render_footer_sidebars(); ?>
				<?php hestia_after_footer_widgets_trigger(); ?>

				<div class="hestia-bottom-footer-content">
					<?php
					$this->wrapped_bottom_footer_content();
					?>
				</div>
			</div>
			<?php hestia_after_footer_content_trigger(); ?>
		</footer>
		<?php
		hestia_after_footer_trigger();
	}

	/**
	 * Filter footer menu classes to account for alignment.
	 *
	 * @param string $classes the footer classes.
	 *
	 * @return mixed
	 */
	public function modify_footer_menu_classes( $classes ) {
		if ( 'footer' !== $classes['theme_location'] ) {
			return $classes;
		}
		$classes['menu_class'] .= ' ' . $this->add_footer_menu_alignment_class();

		return $classes;
	}

	/**
	 * Function to display footer copyright and footer menu.
	 */
	private function wrapped_bottom_footer_content() {
		echo '<div class="hestia-bottom-footer-content">';
		$this->bottom_footer_content();
		echo '</div>';
	}

	/**
	 * Function to display footer copyright and footer menu.
	 * Also used as callback for selective refresh.
	 */
	public function bottom_footer_content() {
		$hestia_general_credits = get_theme_mod(
			'hestia_general_credits',
			sprintf(
				/* translators: %1$s is Theme Name, %2$s is WordPress */
				esc_html__( '%1$s | ' . date("Y") . ' - Contenu original', 'O-rizon' ),
				sprintf(
					/* translators: %s is Theme name */
					'<a href="/" rel="nofollow">%s</a>',
					esc_html__( 'O-rizon', 'O-rizon' )
				),
				/* translators: %s is WordPress */
				sprintf(
					'<a href="%1$s" rel="nofollow">%2$s</a>',
					esc_url( __( '/', 'O-rizon' ) ),
					esc_html__( 'O-rizon', 'O-rizon' )
				)
			)
		);

		wp_nav_menu(
			array(
				'theme_location' => 'footer',
				'depth'          => 1,
				'container'      => 'ul',
				'menu_class'     => 'footer-menu',
			)
		);
		?>
		<?php if ( ! empty( $hestia_general_credits ) || is_customize_preview() ) : ?>
			<div class="copyright <?php echo esc_attr( $this->add_footer_copyright_alignment_class() ); ?>">
				<?php echo wp_kses_post( $hestia_general_credits ); ?>
			</div>
			<?php
		endif;
	}

	/**
	 * Add the footer copyright alignment class.
	 *
	 * @return string
	 */
	private function add_footer_copyright_alignment_class() {
		$hestia_copyright_alignment = get_theme_mod( 'hestia_copyright_alignment', 'right' );
		if ( $hestia_copyright_alignment === 'left' ) {
			return 'pull-left';
		}
		if ( $hestia_copyright_alignment === 'center' ) {
			return 'hestia-center';
		}

		return 'pull-right';
	}

	/**
	 * Add the footer menu alignment class.
	 *
	 * @return string
	 */
	private function add_footer_menu_alignment_class() {
		$hestia_copyright_alignment = get_theme_mod( 'hestia_copyright_alignment', 'right' );
		if ( $hestia_copyright_alignment === 'left' ) {
			return 'pull-right';
		}
		if ( $hestia_copyright_alignment === 'center' ) {
			return 'hestia-center';
		}

		return 'pull-left';
	}

	/**
	 * Utility to get the footer class for color changes.
	 */
	private function the_footer_class() {
		$footer_style = get_theme_mod( 'hestia_alternative_footer_style', 'black_footer' );
		$class        = 'footer-black';

		if ( $footer_style === 'white_footer' ) {
			$class = '';
		}

		return $class;
	}

	/**
	 * Get the sidebars class.
	 *
	 * @return string the sidebar class
	 */
	private function the_sidebars_class() {
		if ( empty( $this->number_of_sidebars ) ) {
			return 'col-md-4';
		}

		$suffix = abs( 12 / $this->number_of_sidebars );
		$class  = 'col-md-' . $suffix;

		return $class;
	}

	/**
	 * Utility to check if any of the footer sidebars have widgets.
	 *
	 * @return bool
	 */
	private function does_footer_have_widgets() {
		if ( empty( $this->footer_sidebars_array ) ) {
			return false;
		}

		foreach ( $this->footer_sidebars_array as $footer_sidebar ) {
			$has_widgets = is_active_sidebar( $footer_sidebar );
			if ( $has_widgets ) {
				return true;
			}
		}

		return false;
	}

}
