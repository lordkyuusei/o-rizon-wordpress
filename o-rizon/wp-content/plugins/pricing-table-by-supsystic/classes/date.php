<?php
class datePts {
	static public function _($time = NULL) {
		if(is_null($time)) {
			$time = time();
		}
		return date(PTS_DATE_FORMAT_HIS, $time);
	}
}