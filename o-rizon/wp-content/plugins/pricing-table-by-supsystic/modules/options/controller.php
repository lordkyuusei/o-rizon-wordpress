<?php
class optionsControllerPts extends controllerPts {
	public function saveGroup() {
		$res = new responsePts();
		if($this->getModel()->saveGroup(reqPts::get('post'))) {
			$res->addMessage(__('Done', PTS_LANG_CODE));
		} else
			$res->pushError ($this->getModel('options')->getErrors());
		return $res->ajaxExec();
	}
	public function getPermissions() {
		return array(
			PTS_USERLEVELS => array(
				PTS_ADMIN => array('saveGroup')
			),
		);
	}
}

