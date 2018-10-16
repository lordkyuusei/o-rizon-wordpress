<?php
class tablesViewPts extends viewPts {
	protected $_twig;
	private $_renderedTables = array();

	public function getImportExportTab() {
		framePts::_()->getModule('templates')->loadJqGrid();
		framePts::_()->addStyle('admin.imex', $this->getModule()->getModPath(). 'css/admin.imex.css');
		framePts::_()->addScript('admin.imex', $this->getModule()->getModPath(). 'js/admin.imex.js');
		framePts::_()->addScript('admin.tables.list', $this->getModule()->getModPath(). 'js/admin.tables.list.js');
		framePts::_()->addJSVar('admin.tables.list', 'ptsTblDataUrl', uriPts::mod('tables', 'getListForTbl', array('reqType' => 'ajax')));

		return parent::getContent('tablesImportExport');
	}
	public function getTabContent() {
		framePts::_()->getModule('templates')->loadJqGrid();

		framePts::_()->addStyle('admin.tables', $this->getModule()->getModPath(). 'css/admin.tables.css');
		framePts::_()->addScript('admin.tables', $this->getModule()->getModPath(). 'js/admin.tables.js');
		framePts::_()->addScript('admin.tables.list', $this->getModule()->getModPath(). 'js/admin.tables.list.js');
		framePts::_()->addJSVar('admin.tables.list', 'ptsTblDataUrl', uriPts::mod('tables', 'getListForTbl', array('reqType' => 'ajax')));
		
		$this->assign('addNewLink', framePts::_()->getModule('options')->getTabUrl('tables_add_new'));
		return parent::getContent('tablesAdmin');
	}
	public function getAddNewTabContent() {
		framePts::_()->getModule('templates')->loadJqueryUi();
		framePts::_()->addStyle('admin.tables', $this->getModule()->getModPath(). 'css/admin.tables.css');
		framePts::_()->addScript('admin.tables', $this->getModule()->getModPath(). 'js/admin.tables.js');
		framePts::_()->getModule('templates')->loadMagicAnims();
		framePts::_()->getModule('templates')->loadBootstrapPartialOnlyCss();
		
		$changeFor = (int) reqPts::getVar('change_for', 'get');
		if($changeFor) {
			$originalTable = $this->getModel()->getById( $changeFor );
			$editLink = $this->getModule()->getEditLink( $changeFor );
			$this->assign('originalTable', $originalTable);
			$this->assign('editLink', $editLink);
			framePts::_()->addJSVar('admin.tables', 'ptsOriginalTable', $originalTable);
			dispatcherPts::addFilter('mainBreadcrumbs', array($this, 'modifyBreadcrumbsForChangeTpl'));
		}
		$this->assign('list', dispatcherPts::applyFilters('showTplsList', $this->getModel()->getSimpleList(array('original_id' => 0))));
		$this->assign('changeFor', $changeFor);
		
		return parent::getContent('tablesAddNewAdmin');
	}
	public function modifyBreadcrumbsForChangeTpl($crumbs) {
		$crumbs[ count($crumbs) - 1 ]['label'] = __('Modify Table Template', PTS_LANG_CODE);
		return $crumbs;
	}
	public function changeMainBreadCrumbsClb($crumbs) {
		return array( $crumbs[ count($crumbs) - 1 ] );	// Get rid of all other breadcrumbs - leave space on this page for other important things (buttons, etc.)
	}
	public function getEditTabContent($id) {
		$table = $this->getModel()->getById($id);
		if(empty($table)) {
			return __('Cannot find required Table', PTS_LANG_CODE);
		}

		$this->assign('table', $table);
		$this->assign('renderedTable', $this->renderTable($table, true));
		$this->assign('editorFooter', $this->getEditorFooter());
		
		dispatcherPts::addAction('afterAdminBreadcrumbs', array($this, 'showEditTableFormControls'));
		dispatcherPts::addFilter('mainBreadcrumbs', array($this, 'changeMainBreadCrumbsClb'));

		$this->connectFrontendAssets( $table );
		$this->connectEditorAssets( $table );
		
		framePts::_()->getModule('templates')->loadJqueryUi();
		
		$ptsAddNewUrl = framePts::_()->getModule('options')->getTabUrl('tables_add_new');
		$this->assign('ptsAddNewUrl', $ptsAddNewUrl);
		
		framePts::_()->getModule('templates')->loadCodemirror();

		framePts::_()->addStyle('codemirror-style', $this->getModule()->getModPath(). 'css/codemirror.css');
		framePts::_()->addStyle('admin.tables', $this->getModule()->getModPath(). 'css/admin.tables.css');
		framePts::_()->addScript('admin.tables.edit', $this->getModule()->getModPath(). 'js/admin.tables.edit.js');
		framePts::_()->addJSVar('admin.tables.edit', 'ptsAddNewUrl', $ptsAddNewUrl);
		
		return parent::getContent('tablesEditAdmin');
	}
	public function showTable($params) {
		$id = isset($params['id']) ? (int) $params['id'] : 0;
		$table = $id ? $this->getModel()->getById($id) : false;
		if(empty($table)) {
			return __('Cannot find required Table', PTS_LANG_CODE);
		}
		
		framePts::_()->getModule('templates')->loadCoreJs();
		$this->connectFrontendAssets( $table );
		
		$this->assign('renderedTable', $this->renderTable($table, false));
		return parent::getContent('tablesShowTable');
	}
	public function showEditTableFormControls() {
		parent::display('tablesEditFormControls');
	}
	public function renderTable($table, $isEditMode = false) {
		if(is_numeric($table)) {
			$table = $this->getModel()->getById($table);
		}
		if(!isset($this->table) || $this->table != $table) {
			// if params enb_desc_col disabled - cut description column
			if($isEditMode === false) {
				if(isset($table['params']['enb_desc_col']['val']) && $table['params']['enb_desc_col']['val'] == 0) {
					$pattern = '`(' . '<div class="[-_\.\w\d\ ]*ptsTableDescCol[\W\w]*'
						. ')<div class="[-_\w\d\.\ ]*ptsCol-1' . '`ui';
					// regex find descriptionColumn
					if(preg_match_all($pattern, $table['html'], $matches)) {
						if(isset($matches[1][0])) {
							$table['html'] = str_replace($matches[1][0], '', $table['html']);
							$table['html'] = preg_replace("`\{\% if \(table\.params\.enb_desc_col\.val \=\= 1 or isEditMode\) \%\}`", '', $table['html']);
						}
					}
				}
			}
			$this->assign('table', $table);
		}
		$this->pushRenderedTable( $table );
		$content = parent::getContent('tablesRender');
		$this->_initTwig();
		return $this->_twig->render($content, array(
			'table' => $table, 
			'isEditMode' => $isEditMode,
		));
	}
	public function pushRenderedTable($table) {
		$this->_renderedTables[] = $table;
	}
	public function getRenderedTables() {
		return $this->_renderedTables;
	}
	public function renderForPost($pid, $params = array()) {
		//framePts::_()->setStylesInitialized(false);
		//framePts::_()->setScriptsInitialized(false);
		$isEditMode = isset($params['isEditMode']) ? $params['isEditMode'] : false;
		$post = isset($params['post']) ? $params['post'] : get_post($pid);
		$tables = $this->getModel()->getForPost($pid);
		if($isEditMode) {
			$this->loadWpAdminAssets();
		}
		framePts::_()->getModule('templates')->loadCoreJs();
		framePts::_()->getModule('templates')->loadBootstrap();
		framePts::_()->getModule('templates')->loadCustomBootstrapColorpicker();
		
		if($isEditMode) {
			$originalBlocksByCategories = $this->getModel('tables_blocks')->getOriginalBlocksByCategories();
			$this->assign('originalBlocksByCategories', $originalBlocksByCategories);
			
			$this->assign('allPagesUrl', framePts::_()->getModule('options')->getTabUrl('tables'));
			$this->assign('previewPageUrl', get_permalink($post));
		}
		$this->_preparePtsoForRender( $tables, $isEditMode );
		
		$this->assign('tables', $tables);
		$this->assign('pid', $pid);
		$this->assign('isEditMode', $isEditMode);
		$this->assign('post', $post);
		$this->assign('stylesScriptsHtml', $this->generateStylesScriptsHtml());
		// Render this part - at final step
		$this->assign('commonFooter', $this->getCommonFooter());
		if($isEditMode) {
			$this->assign('editorFooter', $this->getEditorFooter());
		} else {
			$this->assign('footer', $this->getFooter());
		}
		parent::display('tablesRenderForPost');
	}
	public function getEditorFooter() {
		return parent::getContent('tablesEditorFooter');
	}
	public function getFooter() {
		return parent::getContent('tablesFooter');
	}
	// Footer parts that need to be in frontend and in editor too
	public function getCommonFooter() {
		return parent::getContent('tablesCommonFooter');
	}
	private function _preparePtsoForRender(&$tables, $isEditMode = false) {
		if(!empty($tables['blocks'])) {
			foreach($tables['blocks'] as $i => $block) {
				$tables['blocks'][ $i ]['rendered_html'] = $this->renderBlock( $tables['blocks'][ $i ], $isEditMode );
			}
		}
	}
	public function renderBlock($block = array(), $isEditMode = false) {
		$this->assign('block', $block);
		$this->assign('isEditMode', $isEditMode);
		$content = parent::getInlineContent('tablesRenderBlock');
		$this->_initTwig();
		return $this->_twig->render($content, array('block' => $block));
	}
	public function connectFrontendAssets( $tables = array(), $isEditMode = false ) {
		$isDebbug = (bool) reqPts::getVar('is_debbug', 'get');
		// TODO: remove this debug and minimize all scripts before release
        $isDebbug = true;
		$isPro = framePts::_()->getModule('supsystic_promo')->isPro();
		framePts::_()->addStyle('animate', $this->getModule()->getModPath(). 'css/animate.css');
		if ($isDebbug) {
			framePts::_()->addStyle('frontend.tables', $this->getModule()->getModPath() . 'css/frontend.tables.css');
		} else {
			framePts::_()->addStyle('frontend.tables', $this->getModule()->getModPath() . 'css/frontend.tables.min.css');
		}

		framePts::_()->getModule('templates')->loadFontAwesome();
		framePts::_()->getModule('templates')->loadTooltipster();
		
		if ($isDebbug) {
			framePts::_()->addScript('pts.js.responsive.text', PTS_JS_PATH . 'responsiveText.js');
			framePts::_()->addScript('frontend.tables.editor.blocks_fabric.base', $this->getModule()->getModPath(). 'js/frontend.tables.editor.blocks_fabric.base.js');
			framePts::_()->addScript('frontend.tables.editor.blocks.base', $this->getModule()->getModPath(). 'js/frontend.tables.editor.blocks.base.js');
			framePts::_()->addScript('frontend.tables.editor.elements.base', $this->getModule()->getModPath(). 'js/frontend.tables.editor.elements.base.js');

		} else {
			framePts::_()->addScript('table.min', PTS_JS_PATH . 'table.min.js');
		}

		framePts::_()->addScript('frontend.tablesModal', $this->getModule()->getModPath(). 'js/modal.js', array('jquery'), false, true);
			if($isPro && framePts::_()->getModule('tablepro') && is_admin()) {
				//admin area code
				framePts::_()->addScript('admin.tablesPro', framePts::_()->getModule('tablepro')->getModPath(). 'js/admin.pro.tables.js', array('jquery'), false, true);
			} elseif ($isPro && framePts::_()->getModule('tablepro')) {
				//frontend code
				framePts::_()->addScript('frontend.tablesPro', framePts::_()->getModule('tablepro')->getModPath(). 'js/frontend.pro.tables.js', array('jquery'), false, true);
			}

		framePts::_()->addScript('frontend.tables', $this->getModule()->getModPath(). 'js/frontend.tables.js', array('jquery'), false, true);
		framePts::_()->addJSVar('frontend.tables', 'ptsBuildConst', array(
			'standardFonts' => utilsPts::getStandardFontsList(),
		));
	}
	public function connectEditorAssets( $tables = array() ) {
		$this->assign('adminEmail', get_bloginfo('admin_email'));
		$this->connectEditorJs( $tables );
		$this->connectEditorCss( $tables );
		
		$isPro = framePts::_()->getModule('supsystic_promo')->isPro();
		if($isPro && framePts::_()->getModule('tablepro')) {
			framePts::_()->addScript('admin.tablesPro', framePts::_()->getModule('tablepro')->getModPath(). 'js/admin.pro.tables.js', array('jquery'), false, true);
			framePts::_()->getModule('templates')->loadDatePicker();
		}
		
		framePts::_()->getModule('templates')->loadBootstrapPartial();

		$this->getModule()->assignRenderedTables();
	}
	public function connectEditorJs( $tables = array() ) {
		framePts::_()->addScript('jquery-ui-core');
		framePts::_()->addScript('jquery-ui-widget');
		framePts::_()->addScript('jquery-ui-mouse');
		
		framePts::_()->addScript('jquery-ui-draggable');
		framePts::_()->addScript('jquery-ui-sortable');
		//framePts::_()->addScript('jquery-ui-dialog');
		
		framePts::_()->getModule('templates')->loadMediaScripts();
		framePts::_()->getModule('templates')->loadCustomBootstrapColorpicker();
		framePts::_()->getModule('templates')->loadTinyMce();
		//framePts::_()->getModule('templates')->loadContextMenu();
		//framePts::_()->getModule('templates')->loadCustomColorpicker();
		framePts::_()->getModule('templates')->loadSlimscroll();
		
		framePts::_()->addScript('twig', PTS_JS_PATH. 'twig.min.js');
		framePts::_()->addScript('icheck', PTS_JS_PATH. 'icheck.min.js');
		//framePts::_()->addScript('frontend.tables.editor.menus', $this->getModule()->getModPath(). 'js/frontend.tables.editor.menus.js');
		framePts::_()->addScript('wp.tabs', PTS_JS_PATH. 'wp.tabs.js');

		framePts::_()->addScript('frontend.tables.editor.utils', $this->getModule()->getModPath(). 'js/frontend.tables.editor.utils.js');
		framePts::_()->addScript('frontend.tables.editor.blocks_fabric', $this->getModule()->getModPath(). 'js/frontend.tables.editor.blocks_fabric.js');
		framePts::_()->addScript('frontend.tables.editor.elements', $this->getModule()->getModPath(). 'js/frontend.tables.editor.elements.js');
		framePts::_()->addScript('frontend.tables.editor.elements.menu', $this->getModule()->getModPath(). 'js/frontend.tables.editor.elements.menu.js');
		framePts::_()->addScript('frontend.tables.editor.blocks', $this->getModule()->getModPath(). 'js/frontend.tables.editor.blocks.js');
		framePts::_()->addScript('frontend.tables.editor', $this->getModule()->getModPath(). 'js/frontend.tables.editor.js');

		$ptsEditor = array();
		$ptsEditor['posts'] = array();

		global $wpdb;
		$postTypesForPostsList = array('page', 'post', 'product', 'blog');
		$allPosts = dbPts::get("SELECT ID, post_title FROM $wpdb->posts WHERE post_type IN ('". implode("','", $postTypesForPostsList). "') AND post_status IN ('publish','draft') ORDER BY post_title");

		if ($allPosts) {
			foreach ($allPosts as $post) {
				$ptsEditor['posts'][] = array(
					'url' => get_permalink($post['ID']),
					'title' => $post['post_title'],
				);
			}
		}

		framePts::_()->addJSVar('frontend.tables.editor', 'ptsEditor', $ptsEditor);
	}
	public function connectEditorCss( $tables = array() ) {
		// We will use other instance of this lib here - to use prev. one in admin area
		framePts::_()->addStyle('tables.icheck', $this->getModule()->getModPath(). 'css/jquery.icheck.css');
		framePts::_()->addStyle('frontend.tables.editor', $this->getModule()->getModPath(). 'css/frontend.tables.editor.css');
		framePts::_()->addStyle('frontend.tables.editor.tinymce', $this->getModule()->getModPath(). 'css/frontend.tables.editor.tinymce.css');
		framePts::_()->addStyle('frontend.tables.fonts', $this->getModule()->getModPath(). 'css/frontend.tables.fonts.css');
	}
	protected function _initTwig() {
		if(!$this->_twig) {
			if(!class_exists('Twig_Autoloader')) {
				require_once(PTS_CLASSES_DIR. 'Twig'. DS. 'Autoloader.php');
			}
			Twig_Autoloader::register();
			$this->_twig = new Twig_Environment(new Twig_Loader_String(), array('debug' => 0));
			$this->_twig->addFunction(
				new Twig_SimpleFunction('adjBs'	/*adjustBrightness*/, array(
						$this,
						'adjustBrightness'
					)
				)
			);
		}
	}
	public function adjustBrightness($hex, $steps) {
		static $converted = array();
		if(isset($converted[ $hex ]) && isset($converted[ $hex ][ $steps ])) {
			return $converted[ $hex ][ $steps ];
		}
		$isRgb = (strpos($hex, 'rgb') !== false);
		if($isRgb) {
			$rgbArr = utilsPts::rgbToArray($hex);
			$isRgba = count($rgbArr) == 4;
			$hex = utilsPts::rgbToHex($rgbArr);
		}
		 // Steps should be between -255 and 255. Negative = darker, positive = lighter
		$steps = max(-255, min(255, $steps));

		// Normalize into a six character long hex string
		$hex = str_replace('#', '', $hex);
		if (strlen($hex) == 3) {
			$hex = str_repeat(substr($hex, 0, 1), 2). str_repeat(substr($hex, 1, 1), 2). str_repeat(substr($hex, 2, 1), 2);
		}

		// Split into three parts: R, G and B
		$color_parts = str_split($hex, 2);
		$return = '#';

		foreach ($color_parts as $color) {
			$color   = hexdec($color); // Convert to decimal
			$color   = max(0, min(255, $color + $steps)); // Adjust color
			$return .= str_pad(dechex($color), 2, '0', STR_PAD_LEFT); // Make two char hex code
		}
		
		if($isRgb) {
			$return = utilsPts::hexToRgb( $return );
			if($isRgba) {	// Don't forget about alpha chanel
				$return[] = $rgbArr[ 3 ];
			}
			$return = ($isRgba ? 'rgba' : 'rgb'). '('. implode(',', $return). ')';
		}
		if(!isset($converted[ $hex ]))
			$converted[ $hex ] = array();
		$converted[ $hex ][ $steps ] = $return;
		return $return;
	}
}
