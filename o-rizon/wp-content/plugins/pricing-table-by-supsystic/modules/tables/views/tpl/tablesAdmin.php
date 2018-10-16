<section>
	<div class="supsystic-item supsystic-panel">
		<div id="containerWrapper">
			<ul id="ptsPagesTblNavBtnsShell" class="supsystic-bar-controls">
				<li title="<?php _e('Delete selected', PTS_LANG_CODE)?>">
					<button class="button" id="ptsPagesRemoveGroupBtn" disabled data-toolbar-button>
						<i class="fa fa-fw fa-trash-o"></i>
						<?php _e('Delete selected', PTS_LANG_CODE)?>
					</button>
				</li>
				<li title="<?php _e('Search', PTS_LANG_CODE)?>">
					<input id="ptsPagesTblSearchTxt" type="text" name="tbl_search" placeholder="<?php _e('Search', PTS_LANG_CODE)?>">
				</li>
			</ul>
			<div id="ptsPagesTblNavShell" class="supsystic-tbl-pagination-shell"></div>
			<div style="clear: both;"></div>
			<hr />
			<table id="ptsPagesTbl"></table>
			<div id="ptsPagesTblNav"></div>
			<div id="ptsPagesTblEmptyMsg" style="display: none;">
				<h3><?php printf(__('You have no Tables for now. <a href="%s" style="font-style: italic;">Create</a> your First Table!', PTS_LANG_CODE), $this->addNewLink)?></h3>
			</div>
		</div>
		<div style="clear: both;"></div>
	</div>
</section>