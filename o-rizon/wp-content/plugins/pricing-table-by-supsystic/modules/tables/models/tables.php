<?php
class tablesModelPts extends modelPts {
	private $_linksReplacement = array();
	public function __construct() {
		$this->_setTbl('tables');
	}
	private function _getLinksReplacement() {
		if(empty($this->_linksReplacement)) {
			$this->_linksReplacement = array(
				'modUrl' => array('url' => $this->getModule()->getModPath(), 'key' => 'PTS_MOD_URL'),
				'siteUrl' => array('url' => PTS_SITE_URL, 'key' => 'PTS_SITE_URL'),
				'assetsUrl' => array('url' => $this->getModule()->getAssetsUrl(), 'key' => 'PTS_ASSETS_URL'),
				'oldAssets' => array('url' => $this->getModule()->getOldAssetsUrl(), 'key' => 'PTS_OLD_ASSETS_URL'),
			);
		}
		return $this->_linksReplacement;
	}
	public function createFromTpl($d = array()) {
		$d['label'] = isset($d['label']) ? trim($d['label']) : '';
		$d['original_id'] = isset($d['original_id']) ? (int) $d['original_id'] : 0;
		if(!empty($d['label'])) {
			if(!empty($d['original_id'])) {
				$original = $this->getById($d['original_id']);
				framePts::_()->getModule('supsystic_promo')->getModel()->saveUsageStat('create_from_tpl.'. strtolower(str_replace(' ', '-', $original['label'])));
				unset($original['id']);
				$original['label'] = $d['label'];
				$original['original_id'] = $d['original_id'];
				return $this->insertFromOriginal( $original );
			} else
				$this->pushError (__('Please select Table template from list below', PTS_LANG_CODE));
		} else
			$this->pushError (__('Please enter Name', PTS_LANG_CODE), 'label');
		return false;
	}
	public function insertFromOriginal($original) {
		$original = $this->_escTplData( $original );
		return $this->insert( $original );
	}
	public function remove($id) {
		$id = (int) $id;
		if($id) {
			if(framePts::_()->getTable( $this->_tbl )->delete(array('id' => $id))) {
				return true;
			} else
				$this->pushError (__('Database error detected', PTS_LANG_CODE));
		} else
			$this->pushError(__('Invalid ID', PTS_LANG_CODE));
		return false;
	}
	/**
	 * Exclude some data from list - to avoid memory overload
	 */
	public function getSimpleList($where = array(), $params = array()) {
		if($where)
			$this->setWhere ($where);
		return $this->setSelectFields('id, label, original_id, img, is_pro')->getFromTbl( $params );
	}
	/**
	 * Do not remove pre-set templates
	 */
	public function clear() {
		if(framePts::_()->getTable( $this->_tbl )->delete(array('additionalCondition' => 'original_id != 0'))) {
			return true;
		} else 
			$this->pushError (__('Database error detected', PTS_LANG_CODE));
		return false;
	}
	public function save($d = array()) {
		$res = $this->updateById($d);
		if($res) {
			dispatcherPts::doAction('afterTableUpdate', $d);
		}
		return $res;
	}
	protected function _afterGetFromTbl($row) {
		static $imgsPath = false;
		if(!$imgsPath) {
			$imgsPath = $this->getModule()->getAssetsUrl(). 'img/prev/';
		}
		$row['params'] = isset($row['params']) && !empty($row['params']) 
			? utilsPts::unserialize(base64_decode($row['params']), true)
			: array();
		$row['params'] = $this->_afterDbReplace($this->_afterDbParams( $row['params'] ));
		$row = $this->_afterDbReplace($row);
		$row['img_url'] = isset($row['img']) && !empty($row['img']) 
			? $imgsPath. $row['img'] 
			: $imgsPath. strtolower(str_replace(array(' ', '.'), '-', $row['label'])). '.jpg';
		$row['id'] = (int) $row['id'];
		$row['original_id'] = (int) $row['original_id'];
		$row['sort_order'] = isset($row['sort_order']) ? (int) $row['sort_order'] : 0;
		if(!isset($row['session_id'])) {
			$row['session_id'] = mt_rand(1, 999999);
		}
		if(!isset($row['view_id'])) {
			$row['view_id'] = 'ptsBlock_'. $row['session_id'];
		}
		// All blocks in this plugins have one category - price table
		$row['cat_code'] = 'price_table';
		return $row;
	}
	private function _afterDbParams($params) {
		if(empty($params)) return $params;
		if(is_array($params)) {
			foreach($params as $k => $v) {
				$params[ $k ] = $this->_afterDbParams($v);
			}
			return $params;
		} else
			return stripslashes ($params);
	}
	protected function _beforeDbReplace($data) {
		static $replaceFrom, $replaceTo;
		if(is_array($data)) {
			foreach($data as $k => $v) {
				$data[ $k ] = $this->_beforeDbReplace($v);
			}
		} else {
			if(!$replaceFrom) {
				$this->_getLinksReplacement();
				foreach($this->_linksReplacement as $k => $rData) {
					if($k == 'oldAssets') {	// Replace old assets urls - to new one
						$replaceFrom[] = $rData['url'];
						$replaceTo[] = '['. $this->_linksReplacement['assetsUrl']['key']. ']';
					} else {
						$replaceFrom[] = $rData['url'];
						$replaceTo[] = '['. $rData['key']. ']';
					}
				}
			}
			$data = str_replace($replaceFrom, $replaceTo, $data);
		}
		return $data;
	}
	protected function _afterDbReplace($data) {
		static $replaceFrom, $replaceTo;
		if(is_array($data)) {
			foreach($data as $k => $v) {
				$data[ $k ] = $this->_afterDbReplace($v);
			}
		} else {
			if(!$replaceFrom) {
				$this->_getLinksReplacement();
				/*Tmp fix - for quick replace all mode URL to assets URL*/
				$replaceFrom[] = '['. $this->_linksReplacement['modUrl']['key']. ']';
				$replaceTo[] = '['. $this->_linksReplacement['assetsUrl']['key']. ']';
				$replaceFrom[] = $this->_linksReplacement['oldAssets']['url'];
				$replaceTo[] = $this->_linksReplacement['assetsUrl']['url'];
				/*****/
				foreach($this->_linksReplacement as $k => $rData) {
					$replaceFrom[] = '['. $rData['key']. ']';
					$replaceTo[] = $rData['url'];
				}
			}
			$data = str_replace($replaceFrom, $replaceTo, $data);
		}
		return $data;
	}
	protected function _dataSave($data, $update = false) {
		$data = $this->_beforeDbReplace($data);
		if(isset($data['params'])) {
			if(isset($data['remove_old_html']) && $data['remove_old_html']) {
				unset( $data['remove_old_html'] );
				if(isset($data['params']['old_html'])) {
					unset( $data['params']['old_html'] );
				}
			}
			$data['params'] = base64_encode(utilsPts::serialize($data['params']));
		}
		return $data;
	}
	protected function _escTplData($data) {
		if(isset($data['label']))
			$data['label'] = dbPts::prepareHtmlIn($data['label']);
		if(isset($data['html']))
			$data['html'] = dbPts::escape($data['html']);
		if(isset($data['css']))
			$data['css'] = dbPts::escape($data['css']);
		return $data;
	}
	public function generateUniqueId() {
		$uid = utilsPts::getRandStr( 8 );
		if(framePts::_()->getTable($this->_tbl)->get('COUNT(*) AS total', array('unique_id' => $uid, 'original_id' => 0), '', 'one')) {
			return $this->generateUniqueId();
		}
		return $uid;
	}
	public function updateLabel($d = array()) {
		$d['id'] = isset($d['id']) ? (int) $d['id'] : 0;
		if(!empty($d['id'])) {
			$d['label'] = isset($d['label']) ? trim($d['label']) : '';
			if(!empty($d['label'])) {
				return $this->updateById(array(
					'label' => $d['label']
				), $d['id']);
			} else
				$this->pushError (__('Name can not be empty', PTS_LANG_CODE));
		} else
			$this->pushError (__('Invalid ID', PTS_LANG_CODE));
		return false;
	}
	public function getDifferences($popup, $original) {
		$difsFromOriginal = $this->_computeDifferences($popup, $original);
		$difsOfOriginal = $this->_computeDifferences($original, $popup);	// Some options may be present in original, but not present in current popup
		if(!empty($difsFromOriginal) && empty($difsOfOriginal)) {
			return $difsFromOriginal;
		} elseif(empty($difsFromOriginal) && !empty($difsOfOriginal)) {
			return $difsOfOriginal;
		} else {
			$difs = array_merge($difsFromOriginal, $difsOfOriginal);
			return array_unique($difs);
		}
	}
	private function _computeDifferences($popup, $original, $key = '', $keysImplode = array()) {
		$difs = array();
		if(is_array($popup)) {
			$excludeKey = array('id', 'unique_id', 'label', 'original_id', 'html', 'css', 'img', 'sort_order', 'is_base', 'img_preview',
				'date_created', 'img_preview_url', 'session_id', 'view_id');
			if(!empty($key))
				$keysImplode[] = $key;
			foreach($popup as $k => $v) {
				if(in_array($k, $excludeKey) && empty($key)) continue;
				if(!isset($original[ $k ])) {
					$difs[] = $this->_prepareDiffKeys($k, $keysImplode);
					continue;
				}
				$currDifs = $this->_computeDifferences($popup[ $k ], $original[ $k ], $k, $keysImplode);
				if(!empty($currDifs)) {
					$difs = array_merge($difs, $currDifs);
				}
			}
		} else {
			if($popup != $original) {
				$difs[] = $this->_prepareDiffKeys($key, $keysImplode);
			}
		}
		return $difs;
	}
	private function _prepareDiffKeys($key, $keysImplode) {
		return empty($keysImplode) ? $key : implode('.', $keysImplode). '.'. $key;
	}
	private function _assignKeyArr($from, &$to, $key) {
		$subKeys = explode('.', $key);	
		// Yeah, hardcode, I know.............
		switch(count($subKeys)) {
			case 4:
				if(isset( $from[ $subKeys[0] ][ $subKeys[1] ][ $subKeys[2] ][ $subKeys[3] ] ))
					$to[ $subKeys[0] ][ $subKeys[1] ][ $subKeys[2] ][ $subKeys[3] ] = $from[ $subKeys[0] ][ $subKeys[1] ][ $subKeys[2] ][ $subKeys[3] ];
				else
					unset($to[ $subKeys[0] ][ $subKeys[1] ][ $subKeys[2] ][ $subKeys[3] ]);
				break;
			case 3:
				if(isset( $from[ $subKeys[0] ][ $subKeys[1] ][ $subKeys[2] ] ))
					$to[ $subKeys[0] ][ $subKeys[1] ][ $subKeys[2] ] = $from[ $subKeys[0] ][ $subKeys[1] ][ $subKeys[2] ];
				else
					unset($to[ $subKeys[0] ][ $subKeys[1] ][ $subKeys[2] ]);
				break;
			case 2:
				if(isset( $from[ $subKeys[0] ][ $subKeys[1] ] ))
					$to[ $subKeys[0] ][ $subKeys[1] ] = $from[ $subKeys[0] ][ $subKeys[1] ];
				else
					unset($to[ $subKeys[0] ][ $subKeys[1] ]);
				break;
			case 1:
				if(isset( $from[ $subKeys[0] ] ))
					$to[ $subKeys[0] ] = $from[ $subKeys[0] ];
				else
					unset( $to[ $subKeys[0] ] );
				break;
		}
	}
	public function changeTpl($d = array()) {

		$d['id'] = isset($d['id']) ? (int) $d['id'] : 0;
		$d['new_tpl_id'] = isset($d['new_tpl_id']) ? (int) $d['new_tpl_id'] : 0;
		if($d['id'] && $d['new_tpl_id']) {
			$currentTable= $this->getById( $d['id'] );
			$newTpl = $this->getById( $d['new_tpl_id'] );

			// Copy toggle options parameters from old template to new
			if(!empty($currentTable['params']['option_name_input']['val'])){
                $newTpl['params']['option_name_input']['val'] = $currentTable['params']['option_name_input']['val'];
            }

			// For now - all parameters from new template will be moved to table
			/*$originalTable = $this->getById( $currentTable['original_id'] );
			$diffFromOriginal = $this->getDifferences($currentTable, $originalTable);

			if(!empty($diffFromOriginal)) {
				if(isset($newTpl['params'])) {
					foreach($diffFromOriginal as $k) {
						$this->_assignKeyArr($currentTable, $newTpl, $k);
					}
				}
			}*/
			framePts::_()->getModule('supsystic_promo')->getModel()->saveUsageStat('change_to_tpl.'. strtolower(str_replace(' ', '-', $newTpl['label'])));
			$newTpl['original_id'] = $newTpl['id'];	// It will be our new original
			$newTpl['id'] = $currentTable['id'];
			$newTpl['label'] = $currentTable['label'];
			$newTpl['params']['old_html']['val'] = $currentTable['html'];	// Save it to move all html changes into new template
			$newTpl = dispatcherPts::applyFilters('tableChangeTpl', $newTpl, $currentTable);
			$newTpl = $this->_escTplData( $newTpl );
			return $this->update( $newTpl, array('id' => $newTpl['id']) );
		} else
			$this->pushError (__('Provided data was corrupted', PTS_LANG_CODE));
		return false;
	}
	public function setSimpleGetFields() {
		$this->setSelectFields('id, label, date_created, sort_order, original_id');
		return parent::setSimpleGetFields();
	}
	public function saveAsCopy($d = array()) {
		$d['copy_label'] = isset($d['copy_label']) ? trim($d['copy_label']) : '';
		$d['id'] = isset($d['id']) ? (int) $d['id'] : 0;
		if(!empty($d['copy_label'])) {
			if(!empty($d['id'])) {
				$original = $this->getById($d['id']);
				unset($original['id']);
				$original['label'] = $d['copy_label'];
				return $this->insertFromOriginal( $original );
			} else
				$this->pushError (__('Where is ID?', PTS_LANG_CODE));
		} else
			$this->pushError (__('Please enter Name', PTS_LANG_CODE), 'copy_label');
		return false;
	}
	public function getFullByIdList($list) {
		return $this->setWhere('id in (' . implode(',', $list) . ')')->getFromTbl();
	}
}