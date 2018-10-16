<?php
class supsystic_promoViewPts extends viewPts {
    public function displayAdminFooter() {
        parent::display('adminFooter');
    }
	public function showWelcomePage() {
		$this->assign('askOptions', array(
			1 => array('label' => 'Google'),
			2 => array('label' => 'Worptsess.org'),
			3 => array('label' => 'Refer a friend'),
			4 => array('label' => 'Find on the web'),
			5 => array('label' => 'Other way...'),
		));
		$this->assign('originalPage', uriPts::getFullUrl());
		parent::display('welcomePage');
	}
	public function showAdditionalmainAdminShowOnOptions($popup) {
		$this->assign('promoLink', $this->getModule()->getMainLink(). '?utm_source=plugin&utm_medium=onexit&utm_campaign=pricing_table');
		parent::display('additionalmainAdminShowOnOptions');
	}
	public function getOverviewTabContent() {
		framePts::_()->getModule('templates')->loadJqueryUi();
		framePts::_()->getModule('templates')->loadSlimscroll();
		framePts::_()->addScript('admin.overview', $this->getModule()->getModPath(). 'js/admin.overview.js');
		framePts::_()->addStyle('admin.overview', $this->getModule()->getModPath(). 'css/admin.overview.css');
		$this->assign('mainLink', $this->getModule()->getMainLink());
		$this->assign('faqList', $this->getFaqList());
		$this->assign('serverSettings', $this->getServerSettings());
		$this->assign('news', $this->getNewsContent());
		$this->assign('contactFields', $this->getModule()->getContactFormFields());
		return parent::getContent('overviewTabContent');
	}
	public function getFaqList() {
		return array();
	}
	public function getNewsContent() {
		return '';	// For now only
	}
	public function getServerSettings() {
		global $wpdb;
		return array(
			'Operating System' => array('value' => PHP_OS),
            'PHP Version' => array('value' => PHP_VERSION),
            'Server Software' => array('value' => $_SERVER['SERVER_SOFTWARE']),
			'MySQL' => array('value' =>  $wpdb->db_version()),
            'PHP Allow URL Fopen' => array('value' => ini_get('allow_url_fopen') ? __('Yes', PTS_LANG_CODE) : __('No', PTS_LANG_CODE)),
            'PHP Memory Limit' => array('value' => ini_get('memory_limit')),
            'PHP Max Post Size' => array('value' => ini_get('post_max_size')),
            'PHP Max Upload Filesize' => array('value' => ini_get('upload_max_filesize')),
            'PHP Max Script Execute Time' => array('value' => ini_get('max_execution_time')),
            'PHP EXIF Support' => array('value' => extension_loaded('exif') ? __('Yes', PTS_LANG_CODE) : __('No', PTS_LANG_CODE)),
            'PHP EXIF Version' => array('value' => phpversion('exif')),
            'PHP XML Support' => array('value' => extension_loaded('libxml') ? __('Yes', PTS_LANG_CODE) : __('No', PTS_LANG_CODE), 'error' => !extension_loaded('libxml')),
            'PHP CURL Support' => array('value' => extension_loaded('curl') ? __('Yes', PTS_LANG_CODE) : __('No', PTS_LANG_CODE), 'error' => !extension_loaded('curl')),
		);
	}
	public function showFeaturedPluginsPage() {
		framePts::_()->getModule('templates')->loadBootstrapSimple();
		framePts::_()->addStyle('admin.featured-plugins', $this->getModule()->getModPath(). 'css/admin.featured-plugins.css');
		framePts::_()->getModule('templates')->loadGoogleFont('Montserrat');
		$siteUrl = 'https://supsystic.com/';
		$pluginsUrl = $siteUrl. 'plugins/';
		$uploadsUrl = $siteUrl. 'wp-content/uploads/';
		$downloadsUrl = 'https://downloads.wordpress.org/plugin/';
		$promoCampaign = 'pricing_table';
		$this->assign('pluginsList', array(
			array('label' => __('Popup Plugin', PTS_LANG_CODE), 'url' => $pluginsUrl. 'popup-plugin/', 'img' => $uploadsUrl. '2016/07/Popup_256.png', 'desc' => __('The Best WordPress PopUp option plugin to help you gain more subscribers, social followers or advertisement. Responsive pop-ups with friendly options.', PTS_LANG_CODE), 'download' => $downloadsUrl. 'popup-by-supsystic.zip'),
			array('label' => __('Photo Gallery Plugin', PTS_LANG_CODE), 'url' => $pluginsUrl. 'photo-gallery/', 'img' => $uploadsUrl. '2016/07/Gallery_256.png', 'desc' => __('Photo Gallery Plugin with a great number of layouts will help you to create quality respectable portfolios and image galleries.', PTS_LANG_CODE), 'download' => $downloadsUrl. 'gallery-by-supsystic.zip'),
			array('label' => __('Contact Form Plugin', PTS_LANG_CODE), 'url' => $pluginsUrl. 'contact-form-plugin/', 'img' => $uploadsUrl. '2016/07/Contact_Form_256.png', 'desc' => __('One of the best plugin for creating Contact Forms on your WordPress site. Changeable fonts, backgrounds, an option for adding fields etc.', PTS_LANG_CODE), 'download' => $downloadsUrl. 'contact-form-by-supsystic.zip'),
			array('label' => __('Newsletter Plugin', PTS_LANG_CODE), 'url' => $pluginsUrl. 'newsletter-plugin/', 'img' => $uploadsUrl. '2016/08/icon-256x256.png', 'desc' => __('Supsystic Newsletter plugin for automatic mailing of your letters. You will have no need to control it or send them manually. No coding, hard skills or long hours of customizing are required.', PTS_LANG_CODE), 'download' => $downloadsUrl. 'newsletter-by-supsystic.zip'),
			array('label' => __('Membership by Supsystic', PTS_LANG_CODE), 'url' => $pluginsUrl. 'membership-plugin/', 'img' => $uploadsUrl. '2016/09/256.png', 'desc' => __('Create online membership community with custom user profiles, roles, FrontEnd registration and login. Members Directory, activity, groups, messages.', PTS_LANG_CODE), 'download' => $downloadsUrl. 'membership-by-supsystic.zip'),
			array('label' => __('Data Tables Generator', PTS_LANG_CODE), 'url' => $pluginsUrl. 'data-tables-generator-plugin/', 'img' => $uploadsUrl. '2016/07/Data_Tables_256.png', 'desc' => __('Create and manage beautiful data tables with custom design. No HTML knowledge is required.', PTS_LANG_CODE), 'download' => $downloadsUrl. 'data-tables-generator-by-supsystic.zip'),
			array('label' => __('Slider Plugin', PTS_LANG_CODE), 'url' => $pluginsUrl. 'slider/', 'img' => $uploadsUrl. '2016/07/Slider_256.png', 'desc' => __('Creating slideshows with Slider plugin is fast and easy. Simply select images from your WordPress Media Library, Flickr, Instagram or Facebook, set slide captions, links and SEO fields all from one page.', PTS_LANG_CODE), 'download' => $downloadsUrl. 'slider-by-supsystic.zip'),
			array('label' => __('Social Share Buttons', PTS_LANG_CODE), 'url' => $pluginsUrl. 'social-share-plugin/', 'img' => $uploadsUrl. '2016/07/Social_Buttons_256.png', 'desc' => __('Social share buttons to increase social traffic and popularity. Social sharing to Facebook, Twitter and other social networks.', PTS_LANG_CODE), 'download' => $downloadsUrl. 'social-share-buttons-by-supsystic.zip'),
			array('label' => __('Live Chat Plugin', PTS_LANG_CODE), 'url' => $pluginsUrl. 'live-chat/', 'img' => $uploadsUrl. '2016/07/Live_Chat_256.png', 'desc' => __('Be closer to your visitors and customers with Live Chat Support by Supsystic. Help you visitors, support them in real-time with exceptional Live Chat WordPress plugin by Supsystic.', PTS_LANG_CODE), 'download' => $downloadsUrl. 'live-chat-by-supsystic.zip'),
			array('label' => __('Pricing Table', PTS_LANG_CODE), 'url' => $pluginsUrl. 'pricing-table/', 'img' => $uploadsUrl. '2016/07/Pricing_Table_256.png', 'desc' => __('It’s never been so easy to create and manage pricing and comparison tables with table builder. Any element of the table can be customise with mouse click.', PTS_LANG_CODE), 'download' => $downloadsUrl. 'pricing-table-by-supsystic.zip'),
			array('label' => __('Coming Soon Plugin', PTS_LANG_CODE), 'url' => $pluginsUrl. 'coming-soon-plugin/', 'img' => $uploadsUrl. '2016/07/Coming_Soon_256.png', 'desc' => __('Coming soon page with drag-and-drop builder or under construction | maintenance mode to notify visitors and collects emails.', PTS_LANG_CODE), 'download' => $downloadsUrl. 'coming-soon-by-supsystic.zip'),
			array('label' => __('Backup Plugin', PTS_LANG_CODE), 'url' => $pluginsUrl. 'backup-plugin/', 'img' => $uploadsUrl. '2016/07/Backup_256.png', 'desc' => __('Backup and Restore WordPress Plugin by Supsystic provides quick and unhitched DropBox, FTP, Amazon S3, Google Drive backup for your WordPress website.', PTS_LANG_CODE), 'download' => $downloadsUrl. 'backup-by-supsystic.zip'),
			array('label' => __('Google Maps Easy', PTS_LANG_CODE), 'url' => $pluginsUrl. 'google-maps-plugin/', 'img' => $uploadsUrl. '2016/07/Google_Maps_256.png', 'desc' => __('Display custom Google Maps. Set markers and locations with text, images, categories and links. Customize google map in a simple and intuitive way.', PTS_LANG_CODE), 'download' => $downloadsUrl. 'google-maps-easy.zip'),
			array('label' => __('Digital Publication Plugin', PTS_LANG_CODE), 'url' => $pluginsUrl. 'digital-publication-plugin/', 'img' => $uploadsUrl. '2016/07/Digital_Publication_256.png', 'desc' => __('Digital Publication WordPress Plugin by Supsystic for Magazines, Catalogs, Portfolios. Convert images, posts, PDF to the page flip book.', PTS_LANG_CODE), 'download' => $downloadsUrl. 'digital-publications-by-supsystic.zip'),
		));
		foreach($this->pluginsList as $i => $p) {
			$this->pluginsList[ $i ]['url'] = $this->pluginsList[ $i ]['url']. '?utm_source=plugin&utm_medium=featured_plugins&utm_campaign='. $promoCampaign;
		}
		$this->assign('bundleUrl', $siteUrl. 'product/plugins-bundle/'. '?utm_source=plugin&utm_medium=featured_plugins&utm_campaign='. $promoCampaign);
		return parent::getContent('featuredPlugins');
	}
}
