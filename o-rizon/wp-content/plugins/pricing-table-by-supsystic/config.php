<?php
    global $wpdb;
    if (!defined('WPLANG') || WPLANG == '') {
        define('PTS_WPLANG', 'en_GB');
    } else {
        define('PTS_WPLANG', WPLANG);
    }
    if(!defined('DS')) define('DS', DIRECTORY_SEPARATOR);

    define('PTS_PLUG_NAME', basename(dirname(__FILE__)));
    define('PTS_DIR', WP_PLUGIN_DIR. DS. PTS_PLUG_NAME. DS);
    define('PTS_TPL_DIR', PTS_DIR. 'tpl'. DS);
    define('PTS_CLASSES_DIR', PTS_DIR. 'classes'. DS);
    define('PTS_TABLES_DIR', PTS_CLASSES_DIR. 'tables'. DS);
	define('PTS_HELPERS_DIR', PTS_CLASSES_DIR. 'helpers'. DS);
    define('PTS_LANG_DIR', PTS_DIR. 'lang'. DS);
    define('PTS_IMG_DIR', PTS_DIR. 'img'. DS);
    define('PTS_TEMPLATES_DIR', PTS_DIR. 'templates'. DS);
    define('PTS_MODULES_DIR', PTS_DIR. 'modules'. DS);
    define('PTS_FILES_DIR', PTS_DIR. 'files'. DS);
	define('PTS_JS_DIR', PTS_DIR. 'js'. DS);
    define('PTS_ADMIN_DIR', ABSPATH. 'wp-admin'. DS);

    define('PTS_SITE_URL', get_bloginfo('wpurl'). '/');
    define('PTS_JS_PATH', plugins_url().'/'.basename(dirname(__FILE__)).'/js/');
    define('PTS_CSS_PATH', plugins_url().'/'.basename(dirname(__FILE__)).'/css/');
    define('PTS_IMG_PATH', plugins_url().'/'.basename(dirname(__FILE__)).'/img/');
    define('PTS_MODULES_PATH', plugins_url().'/'.basename(dirname(__FILE__)).'/modules/');
    define('PTS_TEMPLATES_PATH', plugins_url().'/'.basename(dirname(__FILE__)).'/templates/');
    
    define('PTS_URL', PTS_SITE_URL);

    define('PTS_LOADER_IMG', PTS_IMG_PATH. 'loading.gif');
	define('PTS_TIME_FORMAT', 'H:i:s');
    define('PTS_DATE_DL', '/');
    define('PTS_DATE_FORMAT', 'm/d/Y');
    define('PTS_DATE_FORMAT_HIS', 'm/d/Y ('. PTS_TIME_FORMAT. ')');
    define('PTS_DATE_FORMAT_JS', 'mm/dd/yy');
    define('PTS_DATE_FORMAT_CONVERT', '%m/%d/%Y');
    define('PTS_WPDB_PREF', $wpdb->prefix);
    define('PTS_DB_PREF', 'pts_');
    define('PTS_MAIN_FILE', 'pts.php');

    define('PTS_DEFAULT', 'default');
    define('PTS_CURRENT', 'current');
	
	define('PTS_EOL', "\n");    
    
    define('PTS_PLUGIN_INSTALLED', true);
    define('PTS_VERSION', '1.5.5');
    define('PTS_USER', 'user');
    
    define('PTS_CLASS_PREFIX', 'ptsc');     
    define('PTS_FREE_VERSION', false);
	define('PTS_TEST_MODE', true);
    
    define('PTS_SUCCESS', 'Success');
    define('PTS_FAILED', 'Failed');
	define('PTS_ERRORS', 'ptsErrors');
	
	define('PTS_ADMIN',	'admin');
	define('PTS_LOGGED','logged');
	define('PTS_GUEST',	'guest');
	
	define('PTS_ALL',		'all');
	
	define('PTS_METHODS',		'methods');
	define('PTS_USERLEVELS',	'userlevels');
	/**
	 * Framework instance code, unused for now
	 */
	define('PTS_CODE', 'pts');

	define('PTS_LANG_CODE', 'pts_lng');
	/**
	 * Plugin name
	 */
	define('PTS_WP_PLUGIN_NAME', 'Pricing Table by Supsystic');
	/**
	 * Custom defined for plugin
	 */
	define('PTS_COMMON', 'common');
	define('PTS_FB_LIKE', 'fb_like');
	define('PTS_VIDEO', 'video');
	
	define('PTS_HOME_PAGE_ID', 0);
	/**
	 * Our product name
	 */
	define('PTS_OUR_NAME', 'Pricing Table');
	/**
	 * Shortcode name
	 */
	define('PTS_SHORTCODE', 'supsystic-price-table');
