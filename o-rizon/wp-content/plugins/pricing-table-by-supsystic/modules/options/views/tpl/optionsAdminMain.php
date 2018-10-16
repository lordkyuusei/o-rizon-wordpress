<style type="text/css">
	.ptsAdminMainLeftSide {
		width: 56%;
		float: left;
	}
	.ptsAdminMainRightSide {
		width: <?php echo (empty($this->optsDisplayOnMainPage) ? 100 : 40)?>%;
		float: left;
		text-align: center;
	}
	#ptsMainOccupancy {
		box-shadow: none !important;
	}
</style>
<section>
	<div class="supsystic-item supsystic-panel">
		<div id="containerWrapper">
			<?php _e('Main page Go here!!!!', PTS_LANG_CODE)?>
		</div>
		<div style="clear: both;"></div>
	</div>
</section>