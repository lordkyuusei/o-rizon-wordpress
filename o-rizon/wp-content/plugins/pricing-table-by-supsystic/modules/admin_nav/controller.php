<?php
class admin_navControllerPts extends controllerPts {
	public function getPermissions() {
		return array(
			PTS_USERLEVELS => array(
				PTS_ADMIN => array()
			),
		);
	}
}