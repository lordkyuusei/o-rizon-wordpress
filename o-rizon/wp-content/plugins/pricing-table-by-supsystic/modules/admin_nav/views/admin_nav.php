<?php
class admin_navViewPts extends viewPts {
	public function getBreadcrumbs() {
		$this->assign('breadcrumbsList', dispatcherPts::applyFilters('mainBreadcrumbs', $this->getModule()->getBreadcrumbsList()));
		return parent::getContent('adminNavBreadcrumbs');
	}
}
