<?php
class mailViewPts extends viewPts {
	public function getTabContent() {
		framePts::_()->getModule('templates')->loadJqueryUi();
		framePts::_()->addScript('admin.'. $this->getCode(), $this->getModule()->getModPath(). 'js/admin.'. $this->getCode(). '.js');
		
		$this->assign('options', framePts::_()->getModule('options')->getCatOpts( $this->getCode() ));
		$this->assign('testEmail', framePts::_()->getModule('options')->get('notify_email'));
		return parent::getContent('mailAdmin');
	}
}
