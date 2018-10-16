<?php
class supsystic_promoPts extends modulePts {
	private $_mainLink = '';
	private $_specSymbols = array(
		'from'	=> array('?', '&'),
		'to'	=> array('%', '^'),
	);
	private $_minDataInStatToSend = 20;	// At least 20 points in table shuld be present before send stats
	public function __construct($d) {
		parent::__construct($d);
		$this->getMainLink();
	}
	public function init() {
		parent::init();
		add_action('admin_footer', array($this, 'displayAdminFooter'), 9);
		if(is_admin()) {
			$this->checkStatisticStatus();
		}
		$this->weLoveYou();
		dispatcherPts::addFilter('mainAdminTabs', array($this, 'addAdminTab'));
		dispatcherPts::addAction('beforeSaveOpts', array($this, 'checkSaveOpts'));
		dispatcherPts::addAction('tableEnd', array($this, 'checkWeLoveYou'));
		dispatcherPts::addFilter('showTplsList', array($this, 'checkProTpls'));
		add_action('admin_notices', array($this, 'checkAdminPromoNotices'));
	}
	public function checkAdminPromoNotices() {
		if(!framePts::_()->isAdminPlugOptsPage())	// Our notices - only for our plugin pages for now
			return;
		$notices = array();
		// Start usage
		$startUsage = (int) framePts::_()->getModule('options')->get('start_usage');
		$currTime = time();
		$day = 24 * 3600;
		if($startUsage) {	// Already saved
			$rateMsg = sprintf(__("<h3>Hey, I noticed you just use %s over a week – that’s awesome!</h3><p>Could you please do me a BIG favor and give it a 5-star rating on WordPress? Just to help us spread the word and boost our motivation.</p>", PTS_LANG_CODE), PTS_WP_PLUGIN_NAME);
			$rateMsg .= '<p><a href="https://wordpress.org/support/view/plugin-reviews/pricing-table-by-supsystic?rate=5#postform" target="_blank" class="button button-primary" data-statistic-code="done">'. __('Ok, you deserve it', PTS_LANG_CODE). '</a>
			<a href="#" class="button" data-statistic-code="later">'. __('Nope, maybe later', PTS_LANG_CODE). '</a>
			<a href="#" class="button" data-statistic-code="hide">'. __('I already did', PTS_LANG_CODE). '</a></p>';
			$enbPromoLinkMsg = sprintf(__("<h3>More then eleven days with our %s plugin - Congratulations!</h3>", PTS_LANG_CODE), PTS_WP_PLUGIN_NAME);;
			$enbPromoLinkMsg .= __('<p>On behalf of the entire <a href="https://supsystic.com/" target="_blank">supsystic.com</a> company I would like to thank you for been with us, and I really hope that our software helped you.</p>', PTS_LANG_CODE);
			$enbPromoLinkMsg .= __('<p>And today, if you want, - you can help us. This is really simple - you can just add small promo link to our site under your tables. This is small step for you, but a big help for us! Sure, if you don\'t want - just skip this and continue enjoy our software!</p>', PTS_LANG_CODE);
			$enbPromoLinkMsg .= '<p><a href="#" class="button button-primary" data-statistic-code="done">'. __('Ok, you deserve it', PTS_LANG_CODE). '</a>
			<a href="#" class="button" data-statistic-code="later">'. __('Nope, maybe later', PTS_LANG_CODE). '</a>
			<a href="#" class="button" data-statistic-code="hide">'. __('Skip', PTS_LANG_CODE). '</a></p>';
			$checkOtherPlugins = '<p>'
				. sprintf(__('Check out <a href="%s" target="_blank" class="button button-primary" data-statistic-code="hide">our other Plugins</a>! Years of experience in WordPress plugins developers made those list unbreakable!', PTS_LANG_CODE), framePts::_()->getModule('options')->getTabUrl('featured-plugins'))
			. '</p>';
			$notices = array(
				'rate_msg' => array('html' => $rateMsg, 'show_after' => 7 * $day),
				'enb_promo_link_msg' => array('html' => $enbPromoLinkMsg, 'show_after' => 11 * $day),
				'check_other_plugs_msg' => array('html' => $checkOtherPlugins, 'show_after' => 1 * $day),
			);
			foreach($notices as $nKey => $n) {
				if($currTime - $startUsage <= $n['show_after']) {
					unset($notices[ $nKey ]);
					continue;
				}
				$done = (int) framePts::_()->getModule('options')->get('done_'. $nKey);
				if($done) {
					unset($notices[ $nKey ]);
					continue;
				}
				$hide = (int) framePts::_()->getModule('options')->get('hide_'. $nKey);
				if($hide) {
					unset($notices[ $nKey ]);
					continue;
				}
				$later = (int) framePts::_()->getModule('options')->get('later_'. $nKey);
				if($later && ($currTime - $later) <= 2 * $day) {	// remember each 2 days
					unset($notices[ $nKey ]);
					continue;
				}
				if($nKey == 'enb_promo_link_msg' && (int)framePts::_()->getModule('options')->get('add_love_link')) {
					unset($notices[ $nKey ]);
					continue;
				}
			}
		} else {
			framePts::_()->getModule('options')->getModel()->save('start_usage', $currTime);
		}
		if(!empty($notices)) {
			if(isset($notices['rate_msg']) && isset($notices['enb_promo_link_msg']) && !empty($notices['enb_promo_link_msg'])) {
				unset($notices['rate_msg']);	// Show only one from those messages
			}
			$html = '';
			foreach($notices as $nKey => $n) {
				$this->getModel()->saveUsageStat($nKey. '.'. 'show', true);
				$html .= '<div class="updated notice is-dismissible supsystic-admin-notice" data-code="'. $nKey. '">'. $n['html']. '</div>';
			}
			echo $html;
		}
	}
	public function addAdminTab($tabs) {
		$tabs['overview'] = array(
			'label' => __('Overview', PTS_LANG_CODE), 'callback' => array($this, 'getOverviewTabContent'), 'fa_icon' => 'fa-info', 'sort_order' => 5,
		);
		$tabs['featured-plugins'] = array(
			'label' => __('Featured Plugins', PTS_LANG_CODE), 'callback' => array($this, 'showFeaturedPluginsPage'), 'fa_icon' => 'fa-heart', 'sort_order' => 99,
		);
		return $tabs;
	}
	public function getOverviewTabContent() {
		return $this->getView()->getOverviewTabContent();
	}
	// We used such methods - _encodeSlug() and _decodeSlug() - as in slug wp don't understand urlencode() functions
	private function _encodeSlug($slug) {
		return str_replace($this->_specSymbols['from'], $this->_specSymbols['to'], $slug);
	}
	private function _decodeSlug($slug) {
		return str_replace($this->_specSymbols['to'], $this->_specSymbols['from'], $slug);
	}
	public function decodeSlug($slug) {
		return $this->_decodeSlug($slug);
	}
	public function modifyMainAdminSlug($mainSlug) {
		$firstTimeLookedToPlugin = !installerPts::isUsed();
		if($firstTimeLookedToPlugin) {
			$mainSlug = $this->_getNewAdminMenuSlug($mainSlug);
		}
		return $mainSlug;
	}
	private function _getWelcomMessageMenuData($option, $modifySlug = true) {
		return array_merge($option, array(
			'page_title'	=> __('Welcome to Supsystic', PTS_LANG_CODE),
			'menu_slug'		=> ($modifySlug ? $this->_getNewAdminMenuSlug( $option['menu_slug'] ) : $option['menu_slug'] ),
			'function'		=> array($this, 'showWelcomePage'),
		));
	}
	public function addWelcomePageToMenus($options) {
		$firstTimeLookedToPlugin = !installerPts::isUsed();
		if($firstTimeLookedToPlugin) {
			foreach($options as $i => $opt) {
				$options[$i] = $this->_getWelcomMessageMenuData( $options[$i] );
			}
		}
		return $options;
	}
	private function _getNewAdminMenuSlug($menuSlug) {
		// We can't use "&" symbol in slug - so we used "|" symbol
		$newSlug = $this->_encodeSlug(str_replace('admin.php?page=', '', $menuSlug));
		return 'welcome-to-'. framePts::_()->getModule('adminmenu')->getMainSlug(). '|return='. $newSlug;
	}
	public function addWelcomePageToMainMenu($option) {
		$firstTimeLookedToPlugin = !installerPts::isUsed();
		if($firstTimeLookedToPlugin) {
			$option = $this->_getWelcomMessageMenuData($option, false);
		}
		return $option;
	}
	public function showWelcomePage() {
		$this->getView()->showWelcomePage();
	}
	public function displayAdminFooter() {
		if(framePts::_()->isAdminPlugPage()) {
			$this->getView()->displayAdminFooter();
		}
	}
	private function _preparePromoLink($link, $ref = '') {
		if(empty($ref))
			$ref = 'user';
		return $link;
	}
	public function weLoveYou() {
		if(!framePts::_()->getModule(implode('', array('l','ic','e','ns','e')))) {
			// Nothing for now
		}
	}
	public function showAdditionalmainAdminShowOnOptions($popup) {
		$this->getView()->showAdditionalmainAdminShowOnOptions($popup);
	}
	/**
	 * Public shell for private method
	 */
	public function preparePromoLink($link, $ref = '') {
		return $this->_preparePromoLink($link, $ref);
	}
	public function checkStatisticStatus(){
		$canSend = (int) framePts::_()->getModule('options')->get('send_stats');
		if($canSend) {
			$this->getModel()->checkAndSend();
		}
	}
	public function getMinStatSend() {
		return $this->_minDataInStatToSend;
	}
	public function getMainLink() {
		if(empty($this->_mainLink)) {
			$this->_mainLink = 'https://supsystic.com/plugins/pricing-table/';
		}
		return $this->_mainLink ;
	}
	public function getContactFormFields() {
		$fields = array(
            'name' => array('label' => __('Your name', PTS_LANG_CODE), 'valid' => 'notEmpty', 'html' => 'text'),
			'email' => array('label' => __('Your email', PTS_LANG_CODE), 'html' => 'email', 'valid' => array('notEmpty', 'email'), 'placeholder' => 'example@mail.com', 'def' => get_bloginfo('admin_email')),
			'website' => array('label' => __('Website', PTS_LANG_CODE), 'html' => 'text', 'placeholder' => 'http://example.com', 'def' => get_bloginfo('url')),
			'subject' => array('label' => __('Subject', PTS_LANG_CODE), 'valid' => 'notEmpty', 'html' => 'text'),
            'category' => array('label' => __('Topic', PTS_LANG_CODE), 'valid' => 'notEmpty', 'html' => 'selectbox', 'options' => array(
				'plugins_options' => __('Plugin options', PTS_LANG_CODE),
				'bug' => __('Report a bug', PTS_LANG_CODE),
				'functionality_request' => __('Require a new functionallity', PTS_LANG_CODE),
				'other' => __('Other', PTS_LANG_CODE),
			)),
			'message' => array('label' => __('Message', PTS_LANG_CODE), 'valid' => 'notEmpty', 'html' => 'textarea', 'placeholder' => __('Hello Supsystic Team!', PTS_LANG_CODE)),
        );
		foreach($fields as $k => $v) {
			if(isset($fields[ $k ]['valid']) && !is_array($fields[ $k ]['valid']))
				$fields[ $k ]['valid'] = array( $fields[ $k ]['valid'] );
		}
		return $fields;
	}
	public function isPro() {
		return framePts::_()->getModule('license') ? true : false;
	}
	public function generateMainLink($params = '') {
		$mainLink = $this->getMainLink();
		if(!empty($params)) {
			return $mainLink. (strpos($mainLink , '?') ? '&' : '?'). $params;
		}
		return $mainLink;
	}
	public function getLoveLink() {
		$title = 'WordPress Pricing Table Plugin';
		return '<a title="'. $title. '" style="border: none; color: #26bfc1 !important; font-size: 9px; display: block; float: right; padding-right: 10px;" href="'. $this->generateMainLink('utm_source=plugin&utm_medium=love_link&utm_campaign=pricing_table'). '" target="_blank">'
			. $title
			. '</a>'
			. '<div style="clear: both;"></div>';
	}
	public function checkSaveOpts($newValues) {
		$loveLinkEnb = (int) framePts::_()->getModule('options')->get('add_love_link');
		$loveLinkEnbNew = isset($newValues['opt_values']['add_love_link']) ? (int) $newValues['opt_values']['add_love_link'] : 0;
		if($loveLinkEnb != $loveLinkEnbNew) {
			$this->getModel()->saveUsageStat('love_link.'. ($loveLinkEnbNew ? 'enb' : 'dslb'));
		}
	}
	public function checkWeLoveYou() {
		if(framePts::_()->getModule('options')->get('add_love_link')) {
			echo $this->getLoveLink();
		}
	}
	public function checkProTpls($list) {
		if(!$this->isPro()) {
			$imgsPath = framePts::_()->getModule('tables')->getAssetsUrl(). 'img/prev/';
			$promoList = array(
				array('label' => 'Izar', 'img' => 'imagine.jpg'),
				array('label' => 'Keid', 'img' => 'iconic.jpg'),
				array('label' => 'Wezen', 'img' => 'winner.jpg'),
				array('label' => 'Extended Table', 'img' => 'extended-table.jpg'),
				array('label' => 'Arrakis', 'img' => 'big-brother.jpg'),
				array('label' => 'Alcor', 'img' => 'ati.jpg'),
				array('label' => 'Toliman', 'img' => 'triangle-header.jpg'),
				array('label' => 'Matas', 'img' => 'plans.jpg'),
				array('label' => 'Toliman', 'img' => 'triangle-header.jpg'),
				array('label' => 'Coxa', 'img' => 'clean.jpg'),
				array('label' => 'Atik', 'img' => 'veggy.jpg'),
				array('label' => 'Hihal', 'img' => 'product-compare.jpg'),
				array('label' => 'Deneb', 'img' => 'easy-columns.jpg'),
				array('label' => 'Exponential', 'img' => 'exponential.jpg'),
				array('label' => 'Vote Classic', 'img' => 'vote-classic.jpg'),
				array('label' => 'Team 1', 'img' => 'team-1.jpg'),
				array('label' => 'Team 2', 'img' => 'team-1.jpg'),
				array('label' => 'Comfort', 'img' => 'comfort.jpg'),
				array('label' => 'Dr House', 'img' => 'dr-house.jpg'),
				array('label' => 'Low Price', 'img' => 'low-price.jpg'),
				array('label' => 'Pizza 1', 'img' => 'pizza1.jpg'),
				array('label' => 'Pizza 2', 'img' => 'pizza2.jpg'),
				array('label' => 'Model Agency', 'img' => 'model-agency.jpg'),
				array('label' => 'Mini', 'img' => 'mini.jpg'),
				array('label' => 'Hosting Tools', 'img' => 'hosting-tools.jpg'),
				array('label' => 'Startup', 'img' => 'startup.jpg'),
				array('label' => 'Try', 'img' => 'try.jpg'),
				array('label' => 'Express', 'img' => 'express.jpg'),
				array('label' => 'Retro', 'img' => 'retro.jpg'),
				array('label' => 'Taxi', 'img' => 'taxi.jpg'),
				array('label' => 'Prezzo', 'img' => 'prezzo.jpg'),
				array('label' => 'Server Hosting', 'img' => 'server-hosting.jpg'),
				array('label' => 'Horizontal', 'img' => 'horisontal-template.png'),
				array('label' => 'Swans', 'img' => 'swans.jpg'),
				array('label' => 'Danskin', 'img' => 'danskin.jpg'),
				array('label' => 'Carmen', 'img' => 'carmen.jpg'),
				array('label' => 'Cinnamon', 'img' => 'cinnamon.jpg'),
				array('label' => 'Ancer', 'img' => 'ancer.jpg')

			);
			foreach($promoList as $i => $t) {
				$promoList[ $i ]['img_url'] = $imgsPath. $promoList[ $i ]['img'];
				$promoList[ $i ]['promo'] = strtolower(str_replace(array(' ', '!'), '', $t['label']));
				$promoList[ $i ]['promo_link'] = $this->generateMainLink('utm_source=plugin&utm_medium='. $promoList[ $i ]['promo']. '&utm_campaign=pricing_table');
			}
			foreach($list as $i => $t) {
				if(isset($t['is_pro']) && (int)$t['is_pro']) {
					unset($list[ $i ]);
				}
			}
			$list = array_merge($list, $promoList);
		}
		return $list;
	}
	public function showFeaturedPluginsPage() {
		return $this->getView()->showFeaturedPluginsPage();
	}
}