<?php
class tablesControllerPts extends controllerPts {
	public function createFromTpl() {
		$res = new responsePts();
		if(($id = $this->getModel()->createFromTpl(reqPts::get('post'))) != false) {
			$res->addMessage(__('Done', PTS_LANG_CODE));
			$res->addData('edit_link', $this->getModule()->getEditLink( $id ));
		} else
			$res->pushError ($this->getModel()->getErrors());
		return $res->ajaxExec();
	}
	protected function _prepareListForTbl($data) {
		if(!empty($data)) {
			foreach($data as $i => $v) {
				$data[ $i ]['label'] = '<a class="" href="'. $this->getModule()->getEditLink($data[ $i ]['id']). '">'. $data[ $i ]['label']. '&nbsp;<i class="fa fa-fw fa-pencil" style="margin-top: 2px;"></i></a>';
			}
		}
		return $data;
	}
	protected function _prepareModelBeforeListSelect($model) {
		$where = 'original_id != 0';
		$model->addWhere( $where );
		return $model;
	}
	protected function _prepareTextLikeSearch($val) {
		$query = '(label LIKE "%'. $val. '%"';
		if(is_numeric($val)) {
			$query .= ' OR id LIKE "%'. (int) $val. '%"';
		}
		$query .= ')';
		return $query;
	}
	public function importJSONTable() {
		$res = new responsePts();
		$data = reqPts::getVar('data');
		$updateWithSameId = (int) reqPts::getVar('update_with_same_id');
		$tables = array();
		$requiredFields = array(
			'css', 'html', 'img', 'img_url', 'is_base', 'is_pro', 'original_id', 'params', 'label'
		);

		if (! count($data)) {
			$res->pushError('List is empty');
		} else {
			foreach ($data as $table) {
				$issetRequiredField = true;

				foreach ($requiredFields as $field) {
					if($field === 'css' && isset($table[$field])){
						$table[$field] = stripcslashes($table[$field]);
					}
					if (! isset($table[$field])) {
						$issetRequiredField = false;

						break;
					}
				}

				if (! $issetRequiredField) continue;

				if(!$updateWithSameId) {
					if (isset($table['id'])) unset($table['id']);
				}

				$tables[] = $table;
			}

			if (! count($tables)) {
				$res->pushError('List of invalid');
			} else {
				foreach ($tables as $table) {
					if($updateWithSameId
						&& isset($table['id'])
						&& $this->getModel()->getById($table['id']) !== false
					) {
						$this->getModel()->update($table, array('id' => $table['id']));
					} else {
						$this->getModel()->insert($table);
					}
				}

				$res->addData('success', true);
			}
		}

		$res->ajaxExec();
	}
	public function getJSONExportTable() {
		$res = new responsePts();
		$tableIDList = reqPts::getVar('tables');

		if (! count($tableIDList)) {
			$res->pushError('List is empty');
		} else {
			$tables = array();

			foreach ($tableIDList as $value) {
				$id = (int) $value;

				if ($id) $tables[] = $id;
			}

			if (! count($tables)) {
				$res->pushError('List of invalid');
			} else {
				$tableData = $this->getModel()->getFullByIdList($tables);

				$res->addData('exportData', $tableData);
			}
		}

		$res->ajaxExec();
	}
	public function remove() {
		$res = new responsePts();
		if($this->getModel()->remove(reqPts::getVar('id', 'post'))) {
			$res->addMessage(__('Done', PTS_LANG_CODE));
		} else
			$res->pushError($this->getModel()->getErrors());
		$res->ajaxExec();
	}
	public function save() {
		$res = new responsePts();
		$data = reqPts::getVar('data', 'post');
		if($this->getModel()->save( $data )) {
			$res->addMessage(__('Done', PTS_LANG_CODE));
		} else
			$res->pushError($this->getModel()->getErrors());
		$res->ajaxExec();
	}
	public function changeTpl() {
		$res = new responsePts();
		if($this->getModel()->changeTpl(reqPts::get('post'))) {
			$res->addMessage(__('Done', PTS_LANG_CODE));
			$id = (int) reqPts::getVar('id', 'post');
			$res->addData('edit_link', $this->getModule()->getEditLink( $id ));
		} else
			$res->pushError ($this->getModel()->getErrors());
		return $res->ajaxExec();
	}
	public function exportForDb() {
		$forPro = (int) reqPts::getVar('for_pro', 'get');
		$tblsCols = array(
			'@__tables' => array('unique_id','label','original_id','params','html','css','img','sort_order','is_base','date_created','is_pro'),
		);
		if($forPro) {
			echo 'db_install=>';
			foreach($tblsCols as $tbl => $cols) {
				echo $this->_makeExportQueriesLogicForPro($tbl, $cols);
			}
		} else {
			foreach($tblsCols as $tbl => $cols) {
				echo $this->_makeExportQueriesLogic($tbl, $cols);
			}
		}
		exit();
	}
	private function _makeExportQueriesLogicForPro($table, $cols) {
		global $wpdb;
		$octoList = $this->_getExportData($table, $cols, true);
		$res = array();
        $link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

		foreach($octoList as $octo) {
			$uId = '';
			$rowData = array();
			foreach($octo as $k => $v) {
				if(!in_array($k, $cols)) continue;
				if($k === 'css'){
					$temp = stripcslashes($v);
					$val = mysqli_real_escape_string($link, $temp);
				}else{
					$val = mysqli_real_escape_string($link, $v);
				}
				if($k == 'unique_id') $uId = $val;
				$rowData[ $k ] = $val;

			}
			$res[ $uId ] = $rowData;
		}
		echo str_replace(array('@__'), '', $table). '|'. base64_encode( utilsPts::serialize($res) );
	}
	private function _getExportData($table, $cols, $forPro = false) {
		return dbPts::get('SELECT '. implode(',', $cols). ' FROM '. $table. ' WHERE original_id = 0 and is_base = 1 and is_pro = '. ($forPro ? '1' : '0'));
	}
	/**
	 * new usage
	 */
	private function _makeExportQueriesLogic($table, $cols) {
		global $wpdb;

		$eol = "\r\n";
		$octoList = $this->_getExportData($table, $cols);

		$valuesArr = array();
		$allKeys = array();
		$uidIndx = 0;
		$i = 0;
        $link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

        foreach($octoList as $octo) {
		    $arr = array();
			$addToKeys = empty($allKeys);
			$i = 0;
			foreach($octo as $k => $v) {
				if(!in_array($k, $cols)) continue;
				if($addToKeys) {
					$allKeys[] = $k;
					if($k == 'unique_id') {
						$uidIndx = $i;
					}
				}
				if($k === 'css'){
					$temp = stripcslashes($v);
					$arr[] = ''. mysqli_real_escape_string($link, $temp) . '';
				}else{
					$arr[] = ''. mysqli_real_escape_string($link, $v) . '';
				}

				$i++;
			}
			$valuesArr[] = $arr;

		}
		$out = '';
		//$out .= "\$cols = array('". implode("','", $allKeys). "');". $eol;
		$out .= "\$data = array(". $eol;
		foreach($valuesArr as $row) {
			$uid = str_replace(array('"'), '', $row[ $uidIndx ]);
			$installData = array();
			foreach($row as $i => $v) {
				$installData[] = "'{$allKeys[ $i ]}' => '{$v}'";
			}
			$out .= "'$uid' => array(". implode(',', $installData). "),". $eol;
		}
		$out .= ");". $eol;

		return $out;
	}
	public function saveAsCopy() {
		$res = new responsePts();
		if(($id = $this->getModel()->saveAsCopy(reqPts::get('post'))) != false) {
			$res->addMessage(__('Done, redirecting to new Table...', PTS_LANG_CODE));
			$res->addData('edit_link', $this->getModule()->getEditLink( $id ));
		} else
			$res->pushError ($this->getModel()->getErrors());
		return $res->ajaxExec();
	}
	public function updateLabel() {
		$res = new responsePts();
		if($this->getModel()->updateLabel(reqPts::get('post'))) {
			$res->addMessage(__('Done', PTS_LANG_CODE));
		} else
			$res->pushError ($this->getModel()->getErrors());
		return $res->ajaxExec();
	}
	public function getPermissions() {
		return array(
			PTS_USERLEVELS => array(
				PTS_ADMIN => array('getListForTbl', 'remove', 'removeGroup', 'clear', 
					'save', 'exportForDb', 'updateLabel', 'changeTpl', 'saveAsCopy')
			),
		);
	}
}

