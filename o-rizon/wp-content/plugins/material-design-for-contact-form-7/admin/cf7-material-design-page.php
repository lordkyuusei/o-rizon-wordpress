<?php

// don't load directly
if (!defined('ABSPATH')) die('-1');

class CF7_Material_Design_Admin_Page {

	private $customize_url;
    private $new_form_url;
    private $plugin_url;
    private $upgrade_url;
    private $upgrade_cost;
    private $fs;
    
    function __construct() {
		
		// Enqueue
        add_action( 'admin_enqueue_scripts', array( $this, 'add_scripts_and_styles' ) );

        // Other actions
        add_action( 'admin_menu', array( $this, 'add_menu_page' ) );

        // Set members
        $this->customize_url = admin_url( '/customize.php?autofocus[section]=cf7md_options' );
        $this->new_form_url = admin_url( '/admin.php?page=wpcf7-new' );
        $this->plugin_url = plugin_dir_url( __DIR__ );
        global $cf7md_fs;
        $this->fs = $cf7md_fs;
        $this->upgrade_url = $cf7md_fs->get_upgrade_url( 'lifetime' );
        $this->upgrade_cost = CF7MD_UPGRADE_COST;
        $this->live_preview_url = esc_url( network_admin_url( 'plugin-install.php?tab=plugin-information&plugin=' . CF7MD_LIVE_PREVIEW_PLUGIN_SLUG ) );

	}


    /**
     * Enqueue scripts and styles
     */
    public function add_scripts_and_styles( $hook ) {
        
        // Register the admin scripts and styles
        wp_register_script( 'cf7md-slick', plugins_url( '../assets/js/lib/slick.min.js', __FILE__ ), array( 'jquery' ), '1.0', true );
        wp_register_script( 'cf7-material-design-admin', plugins_url( '../assets/js/cf7-material-design-admin.js', __FILE__ ), array( 'jquery', 'cf7md-slick' ), '1.0', true );

        wp_register_style( 'cf7-material-design-admin', plugins_url( '../assets/css/cf7-material-design-admin.css', __FILE__ ) );

        // Load only on ?page=cf7md
        if( strpos( $hook, 'cf7md' ) !== false ) {
            wp_enqueue_script( 'cf7md-slick' );
            wp_enqueue_script( 'cf7-material-design-admin' );
            wp_enqueue_style( 'cf7-material-design-admin' );
        }
    
    }


    /**
     * Add menu page
     */
    public function add_menu_page() {
        add_submenu_page( 'wpcf7', 'Material Design', 'Material Design', 'edit_theme_options', 'cf7md', array( $this, 'get_menu_page' ) );
    }


    /**
     * Get menu page
     */
    public function get_menu_page() {
        
        ?>
        <div id="cf7md-page" class="cf7md-page">
            <div class="cf7md-hero">
                <h1 class="cf7md-hero--title">Material Design</h1>
                <p class="cf7md-hero--subtitle">For Contact Form 7</p>
            </div>
            <div class="cf7md-main">

                <div class="cf7md-content">
                    <h2>Thanks for installing!</h2>
                    <p>Material Design for Contact Form 7 provides a way to bring your forms in line with Google's <a href="https://material.io/guidelines/material-design/introduction.html" target="_blank">Material Design Guidelines</a>.</p>
                    <h3>Getting Started</h3>
                    <p>This plugin provides a bunch of shortcodes that are used to wrap your Contact Form 7 form tags in the form editor. When you wrap a form tag in one of these shortcodes, it's output will be changed to be Material Design compliant.</p>
                    <div class="mdc-card" style="margin-bottom: 20px;">
                        <div class="mdc-card__primary">
                            <h4 class="mdc-card__title">Quick Start</h4>
                            <p class="mdc-card__subtitle">Fastest track to a working form</p>
                        </div>
                        <div class="mdc-card__supporting-text cf7md-card-content">
                            <ol>
                                <li><a href="<?php echo $this->new_form_url; ?>" target="_blank">Add a new form</a>.</li>
                                <li>Delete everything from the form editor, and instead copy the example form code from the "Material Design" meta-box into the editor.</li>
                                <li>Save.</li>
                                <li>Copy your form shortcode (under the form title) and paste it into a page (or post, widget, etc).</li>
                                <li>Save and preview the page.</li>
                            </ol>
                        </div>
                    </div>
                    <p>After that, you can customize your form to your liking. Click the "Material Design" button alongside the CF7 form tag buttons to automatically generate shortcodes for the different field types.</p>
                    <p>We also recommend installing the <a href="<?php echo esc_attr( $this->live_preview_url ); ?>" target="_blank">Contact Form 7 Live Preview</a> plugin for easier form development.</p>
                    <h3>Documentation</h3>
                    <p>All shortcodes are documented in the help tab (top right of the screen) on the form editor page.</p>
                    <h3>Support</h3>
                    <p>Confused? Something doesn't look right? Have a specific question? Make a post in the <a href="https://wordpress.org/support/plugin/material-design-for-contact-form-7" target="_blank">support forum</a> and I'll help you out.</p>
                    <h3>Custom colours? fonts?</h3>
                    <p>Customizing colours and fonts is a pro feature, but you can <a href="<?php echo $this->customize_url; ?>">try it out for free in the customizer</a>. Your changes just won't be saved unless you're on the pro version.</p>
                    <h3>Like this plugin? Rate it!</h3>
                    <p><a class="mdc-button mdc-button--primary mdc-button--raised" href="https://wordpress.org/support/plugin/material-design-for-contact-form-7/reviews/?rate=5#new-post" target="_blank">Leave a 5 star review</a></p>
                </div>

                <div class="cf7md-aside">
                    <?php if( $this->fs->is_free_plan() ) : ?>
                        <div class="mdc-card" style="margin-bottom: 32px;">
                            <div class="mdc-card__primary">
                                <h2 class="mdc-card__title mdc-card__title--large">Upgrade to Pro for <?php echo $this->upgrade_cost; ?></h2>
                                <p class="mdc-card__subtitle">And unlock all these extra features</p>
                            </div>
                            <div class="cf7md-card--slideshow">
                                <div class="cf7md-card--slide">
                                    <div class="mdc-card__media mdc-card__media--img">
                                        <img src="<?php echo $this->plugin_url; ?>assets/images/features-slideshow-styles.png" alt="Custom styles">
                                    </div>
                                    <div class="mdc-card__supporting-text cf7md-card-content">
                                        <p>Customise the colours and fonts to suit your theme.</p>
                                    </div>
                                </div>
                                <div class="cf7md-card--slide">
                                    <div class="mdc-card__media mdc-card__media--img">
                                        <img src="<?php echo $this->plugin_url; ?>assets/images/features-slideshow-switches.png" alt="Switches">
                                    </div>
                                    <div class="mdc-card__supporting-text cf7md-card-content">
                                        <p>Turn checkboxes and radios into <a href="https://material.io/guidelines/components/selection-controls.html#selection-controls-switch" target="_blank">switches</a> using <code>[md-switch]</code>.</p>
                                    </div>
                                </div>
                                <div class="cf7md-card--slide">
                                    <div class="mdc-card__media mdc-card__media--img">
                                        <img src="<?php echo $this->plugin_url; ?>assets/images/features-slideshow-layout.png" alt="Custom layouts">
                                    </div>
                                    <div class="mdc-card__supporting-text cf7md-card-content">
                                        <p>Easily organize your fields into columns.</p>
                                    </div>
                                </div>
                                <div class="cf7md-card--slide">
                                    <div class="mdc-card__media mdc-card__media--img">
                                        <img src="<?php echo $this->plugin_url; ?>assets/images/features-slideshow-cards.png" alt="Separate sections with cards">
                                    </div>
                                    <div class="mdc-card__supporting-text cf7md-card-content">
                                        <p>Group fields into sections with the <code>[md-card]</code> shortcode.</p>
                                    </div>
                                </div>
                            </div>
                            <section class="mdc-card__actions">
                                <a href="<?php echo $this->upgrade_url; ?>" class="cf7md-button">Upgrade now for <?php echo $this->upgrade_cost; ?></a>
                            </section>
                        </div>
                        <h3>All Pro Benefits</h3>
                        <ul>
                            <li>Customize the colours and fonts to suit your theme.</li>
                            <li>Turn checkboxes and radios into <a href="https://material.io/guidelines/components/selection-controls.html#selection-controls-switch" target="_blank">switches</a> using <code>[md-switch]</code>.</li>
                            <li>Easily organize your fields into columns.</li>
                            <li>Group fields into sections with the <code>[md-card]</code> shortcode.</li>
                            <li>Faster support, directly through email.</li>
                        </ul>
                        <p><a class="mdc-button mdc-button--primary mdc-button--raised" href="<?php echo $this->upgrade_url; ?>" target="_blank">Upgrade now for <?php echo $this->upgrade_cost; ?></a></p>
                    <?php else: ?>
                        <div class="mdc-card" style="margin-bottom: 32px;">
                            <div class="mdc-card__primary">
                                <h2 class="mdc-card__title mdc-card__title--large">You have the pro version</h2>
                                <p class="mdc-card__subtitle">Start using pro features now!</p>
                            </div>
                            <div class="mdc-card__supporting-text cf7md-card-content">
                                <ul>
                                    <li><a href="<?php echo $this->customize_url; ?>">Customize the colours and fonts</a></li>
                                    <li>Turn checkboxes and radios into <a href="https://material.io/guidelines/components/selection-controls.html#selection-controls-switch" target="_blank">switches</a> using <code>[md-switch]</code></li>
                                    <li>Use the <code>desktopwidth</code>, <code>tabletwidth</code> and <code>mobilewidth</code> attributes on your shortcodes to arrange them into columns<sup>*</sup></li>
                                    <li>Use the <code>[md-card]</code> shortcode to group your fields into sections<sup>*</sup></li>
                                    <li><a href="mailto:cf7materialdesign@gmail.com" target="_blank">Email me</a> (Angus) directly for support</li>
                                </ul>
                                <p><small>* See documentation on form editor screen</small></p>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php

    }
    
    
}

// Finally initialize code
$cf7_material_design_admin_page = new CF7_Material_Design_Admin_Page();
