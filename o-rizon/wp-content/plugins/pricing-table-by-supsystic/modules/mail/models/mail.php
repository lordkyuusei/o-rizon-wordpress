<?php
class mailModelPts extends modelPts {
	public function testEmail($email) {
		$email = trim($email);
		if(!empty($email)) {
			if($this->getModule()->send($email, 
				__('Test email functionslity', PTS_LANG_CODE), 
				sprintf(__('This is test email for testing email functionality on your site, %s.', PTS_LANG_CODE), PTS_SITE_URL))
			) {
				return true;
			} else {
				$this->pushError( $this->getModule()->getMailErrors() );
			}
		} else
			$this->pushError (__('Empty email address', PTS_LANG_CODE), 'test_email');
		return false;
	}
}