<?php
class optionsPts extends modulePts {
	private $_tabs = array();
	private $_options = array();
	private $_optionsToCategoires = array();	// For faster search
	
	public function init() {
		dispatcherPts::addAction('afterModulesInit', array($this, 'initAllOptValues'));
		dispatcherPts::addFilter('mainAdminTabs', array($this, 'addAdminTab'));
	}
	public function initAllOptValues() {
		// Just to make sure - that we loaded all default options values
		$this->getAll();
	}
    /**
     * This method provides fast access to options model method get
     * @see optionsModel::get($d)
     */
    public function get($code) {
        return $this->getModel()->get($code);
    }
	/**
     * This method provides fast access to options model method get
     * @see optionsModel::get($d)
     */
	public function isEmpty($code) {
		return $this->getModel()->isEmpty($code);
	}
	public function getAllowedPublicOptions() {
		$allowKeys = array('add_love_link', 'disable_autosave');
		$res = array();
		foreach($allowKeys as $k) {
			$res[ $k ] = $this->get($k);
		}
		return $res;
	}
	public function getAdminPage() {
		if(installerPts::isUsed()) {
			return $this->getView()->getAdminPage();
		} else {
			return framePts::_()->getModule('supsystic_promo')->showWelcomePage();
		}
	}
	public function addAdminTab($tabs) {
		$tabs['settings'] = array(
			'label' => __('Settings', PTS_LANG_CODE), 'callback' => array($this, 'getSettingsTabContent'), 'fa_icon' => 'fa-gear', 'sort_order' => 30,
		);
		return $tabs;
	}
	public function getSettingsTabContent() {
		return $this->getView()->getSettingsTabContent();
	}
	public function getTabs() {
		if(empty($this->_tabs)) {
			$this->_tabs = dispatcherPts::applyFilters('mainAdminTabs', array(
				//'main_page' => array('label' => __('Main Page', PTS_LANG_CODE), 'callback' => array($this, 'getTabContent'), 'wp_icon' => 'dashicons-admin-home', 'sort_order' => 0), 
			));
			foreach($this->_tabs as $tabKey => $tab) {
				if(!isset($this->_tabs[ $tabKey ]['url'])) {
					$this->_tabs[ $tabKey ]['url'] = $this->getTabUrl( $tabKey );
				}
			}
			uasort($this->_tabs, array($this, 'sortTabsClb'));
		}
		return $this->_tabs;
	}
	public function sortTabsClb($a, $b) {
		if(isset($a['sort_order']) && isset($b['sort_order'])) {
			if($a['sort_order'] > $b['sort_order'])
				return 1;
			if($a['sort_order'] < $b['sort_order'])
				return -1;
		}
		return 0;
	}
	public function getTab($tabKey) {
		$this->getTabs();
		return isset($this->_tabs[ $tabKey ]) ? $this->_tabs[ $tabKey ] : false;
	}
	public function getTabContent() {
		return $this->getView()->getTabContent();
	}
	public function getActiveTab() {
		$reqTab = reqPts::getVar('tab');
		return empty($reqTab) ? 'tables' : $reqTab;
	}
	public function getTabUrl($tab = '') {
		static $mainUrl;
		if(empty($mainUrl)) {
			$mainUrl = framePts::_()->getModule('adminmenu')->getMainLink();
		}
		return empty($tab) ? $mainUrl : $mainUrl. '&tab='. $tab;
	}
	public function getRolesList() {
		if(!function_exists('get_editable_roles')) {
			require_once( ABSPATH . '/wp-admin/includes/user.php' );
		}
		return get_editable_roles();
	}
	public function getAvailableUserRolesSelect() {
		$rolesList = $this->getRolesList();
		$rolesListForSelect = array();
		foreach($rolesList as $rKey => $rData) {
			$rolesListForSelect[ $rKey ] = $rData['name'];
		}
		return $rolesListForSelect;
	}
	public function getAll() {
		if(empty($this->_options)) {
			$uploadsDir = wp_upload_dir( null, false );
			$this->_options = dispatcherPts::applyFilters('optionsDefine', array(
				'general' => array(
					'label' => __('General', PTS_LANG_CODE),
					'opts' => array(
						'send_stats' => array('label' => __('Send usage statistics', PTS_LANG_CODE), 'desc' => __('Send information about what plugin options you prefer to use, this will help us to make our solution better for You.', PTS_LANG_CODE), 'def' => '0', 'html' => 'checkboxHiddenVal'),
						'add_love_link' => array('label' => __('Enable promo link', PTS_LANG_CODE), 'desc' => __('We are trying to make our plugin better for you, and you can help us with this. Just check this option - and small promotion link will be added in the bottom of your Pricing Table. This is easy for you - but very helpful for us!', PTS_LANG_CODE), 'def' => '0', 'html' => 'checkboxHiddenVal'),
						'access_roles' => array('label' => __('User with such role can use plugin', PTS_LANG_CODE), 'desc' => __('User with the next roles will have access to the whole plugin from admin area.', PTS_LANG_CODE), 'def' => 'administrator', 'html' => 'selectlist', 'options' => array($this, 'getAvailableUserRolesSelect'), 'pro' => ''),
						'disable_autosave' => array('label' => __('Disable autosave in Pricing Table', PTS_LANG_CODE), 'desc' => __('By default our plugin will make autosave of all your changes that you do in Pricing Table edit screen, but you can disable this feature here. Just don\'t forget to save your Pricing Table each time you make any changes in it.', PTS_LANG_CODE), 'def' => '0', 'html' => 'checkboxHiddenVal'),
						'use_local_cdn' => array('label' => __('Disable CDN usage', PTS_LANG_CODE), 'desc' => esc_html(sprintf(__('By default our plugin is using CDN server to store there part of it\'s files - images, javascript and CSS libraries. This was designed in that way to reduce plugin size, make it lighter and easier for usage. But if you need to store all files - on your server - you can disable this option here, then upload plugin CDN files from <a href="%s" target="_blank">here</a> to your own site to %s folder.', PTS_LANG_CODE), 'https://supsystic.com/wp-content/uploads/2016/07/pts.zip', $uploadsDir['baseurl']. '/'. PTS_CODE. '/')), 'def' => '0', 'html' => 'checkboxHiddenVal'),
					),
				),
			));
			$isPro = framePts::_()->getModule('supsystic_promo')->isPro();
			if(!$isPro) {
				$mainLink = framePts::_()->getModule('supsystic_promo')->getMainLink();
			}
			foreach($this->_options as $catKey => $cData) {
				foreach($cData['opts'] as $optKey => $opt) {
					$this->_optionsToCategoires[ $optKey ] = $catKey;
					if(isset($opt['pro']) && !$isPro) {
						$this->_options[ $catKey ]['opts'][ $optKey ]['pro'] = $mainLink. '?utm_source=plugin&utm_medium='. $optKey. '&utm_campaign=pricing_table';
					}
				}
			}
			$this->getModel()->fillInValues( $this->_options );
		}
		return $this->_options;
	}
	public function getFullCat($cat) {
		$this->getAll();
		return isset($this->_options[ $cat ]) ? $this->_options[ $cat ] : false;
	}
	public function getCatOpts($cat) {
		$opts = $this->getFullCat($cat);
		return $opts ? $opts['opts'] : false;
	}
}

