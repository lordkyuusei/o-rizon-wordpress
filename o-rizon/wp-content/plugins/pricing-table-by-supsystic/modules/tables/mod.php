<?php
class tablesPts extends modulePts {
	private $_assetsUrl = '';
	private $_oldAssetsUrl = 'https://supsystic.com/_assets/tables/';
	
	public function init() {
		dispatcherPts::addFilter('mainAdminTabs', array($this, 'addAdminTab'));
		add_filter('wp_footer', array($this, 'assignRenderedTables'));
		add_shortcode(PTS_SHORTCODE, array($this, 'showPriceTable'));
		// Add to admin bar new item
		add_action('admin_bar_menu', array($this, 'addAdminBarNewItem'), 300);

		//redefine yoast seo plugin method. We don't need parse pricing shortcode.
		//this fix increases the speed of loading backend pages
		add_action( 'wp_ajax_wpseo_filter_shortcodes', array( $this, 'do_filter' ) );
	}

	public function addAdminTab($tabs) {
		$tabs[ $this->getCode(). '_add_new' ] = array(
			'label' => __('Add New Table', PTS_LANG_CODE), 'callback' => array($this, 'getAddNewTabContent'), 'fa_icon' => 'fa-plus-circle', 'sort_order' => 10, 'add_bread' => $this->getCode(),
		);
		$tabs[ $this->getCode(). '_edit' ] = array(
			'label' => __('Edit', PTS_LANG_CODE), 'callback' => array($this, 'getEditTabContent'), 'sort_order' => 20, 'child_of' => $this->getCode(), 'hidden' => 1, 'add_bread' => $this->getCode(),
		);
		$tabs[ $this->getCode() ] = array(
			'label' => __('Show All Tables', PTS_LANG_CODE), 'callback' => array($this, 'getTabContent'), 'fa_icon' => 'fa-list', 'sort_order' => 20, //'is_main' => true,
		);
		$tabs[ $this->getCode() . '_import_export' ] = array(
			'label' => __('Tables Import / Export', PTS_LANG_CODE), 'callback' => array($this, 'getImportExportTab'), 'fa_icon' => 'fa-upload', 'sort_order' => 30, //'is_main' => true,
		);
		return $tabs;
	}
	public function getImportExportTab() {
		return $this->getView()->getImportExportTab();
	}
	public function getTabContent() {
		return $this->getView()->getTabContent();
	}
	public function getAddNewTabContent() {
		return $this->getView()->getAddNewTabContent();
	}
	public function getEditTabContent() {
		$id = (int) reqPts::getVar('id', 'get');
		return $this->getView()->getEditTabContent( $id );
	}
	public function getEditLink($id) {
		$link = framePts::_()->getModule('options')->getTabUrl( $this->getCode(). '_edit' );
		$link .= '&id='. $id;
		return $link;
	}
	public function getAssetsUrl() {
		if(empty($this->_assetsUrl)) {
			$this->_assetsUrl = framePts::_()->getModule('templates')->getCdnUrl(). '_assets/tables/';
		}
		return $this->_assetsUrl;
	}
	public function getOldAssetsUrl() {
		return $this->_oldAssetsUrl;
	}
	public function assignRenderedTables() {
		$tables = $this->getView()->getRenderedTables();
		if(!empty($tables)) {
			framePts::_()->addJSVar('frontend.tables', 'ptsTables', $tables);
		}
	}
	public function showPriceTable($params) {
		return do_shortcode($this->getView()->showTable($params));
	}
	public function addAdminBarNewItem( $wp_admin_bar ) {
		$mainCap = framePts::_()->getModule('adminmenu')->getMainCap();
		if(!current_user_can( $mainCap) || !$wp_admin_bar || !is_object($wp_admin_bar)) {
			return;
		}
		$wp_admin_bar->add_menu(array(
			'parent'    => 'new-content',
			'id'        => PTS_CODE. '-admin-bar-new-item',
			'title'     => __('Pricing Table', PTS_LANG_CODE),
			'href'      => framePts::_()->getModule('options')->getTabUrl( $this->getCode(). '_add_new' ),
		));
	}
	public function do_filter() {
		check_ajax_referer( 'wpseo-filter-shortcodes', 'nonce' );

		$shortcodes = filter_input( INPUT_POST, 'data', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );

		$parsed_shortcodes = array();

		foreach ( $shortcodes as $shortcode ) {

			if (!strpos($shortcode, PTS_SHORTCODE) !== false) {
				$parsed_shortcodes[] = array(
					'shortcode' => $shortcode,
					'output'    => do_shortcode( $shortcode ),
				);
			}
		}

		wp_die( wp_json_encode( $parsed_shortcodes ) );
	}
}

