<?php
class mailControllerPts extends controllerPts {
	public function testEmail() {
		$res = new responsePts();
		$email = reqPts::getVar('test_email', 'post');
		if($this->getModel()->testEmail($email)) {
			$res->addMessage(__('Now check your email inbox / spam folders for test mail.'));
		} else 
			$res->pushError ($this->getModel()->getErrors());
		$res->ajaxExec();
	}
	public function saveMailTestRes() {
		$res = new responsePts();
		$result = (int) reqPts::getVar('result', 'post');
		framePts::_()->getModule('options')->getModel()->save('mail_function_work', $result);
		$res->ajaxExec();
	}
	public function saveOptions() {
		$res = new responsePts();
		$optsModel = framePts::_()->getModule('options')->getModel();
		$submitData = reqPts::get('post');
		if($optsModel->saveGroup($submitData)) {
			$res->addMessage(__('Done', PTS_LANG_CODE));
		} else
			$res->pushError ($optsModel->getErrors());
		$res->ajaxExec();
	}
	public function getPermissions() {
		return array(
			PTS_USERLEVELS => array(
				PTS_ADMIN => array('testEmail', 'saveMailTestRes', 'saveOptions')
			),
		);
	}
}
