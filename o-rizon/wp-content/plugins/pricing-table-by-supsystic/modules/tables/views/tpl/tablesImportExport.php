<section>
	<div class="supsystic-item supsystic-panel import-export-panel">
		<div class="supsystic-imex-tab-header">
			<div class="supsystic-imex-tab-header-item imex-page-nav"  data-to-page=".supsystic-panel.import-export-panel .supsystic-imex-import-page">
				<?php _e('Import', PTS_LANG_CODE)?>
			</div>
			<div class="supsystic-imex-tab-header-item imex-page-nav active" data-to-page=".supsystic-panel.import-export-panel .supsystic-imex-export-page">
				<?php _e('Export', PTS_LANG_CODE)?>
			</div>
		</div>
		<div class="supsystic-imex-tab-content">
			<div class="supsystic-imex-export-page supsystic-imex-page">
				<ul id="ptsPagesTblNavBtnsShell" class="supsystic-bar-controls">
					<li title="<?php _e('Search', PTS_LANG_CODE)?>">
						<input id="ptsPagesTblSearchTxt" type="text" name="tbl_search" placeholder="<?php _e('Search', PTS_LANG_CODE)?>">
					</li>
				</ul>
				<div id="ptsPagesTblNavShell" class="supsystic-tbl-pagination-shell"></div>
				<div style="clear: both;"></div>
				<hr />
				<div id="ptsPagesTblNav"></div>
				<div class="imex-description">
					In order to export tables - choose the table, that you want to export and click Export button in the right bottom corner. Copy the code. Then go to another site to Pricing table plugin at Tables Import/Export tab. Paste the code on Import tab. Click Import button in the right bottom corner.
				</div>
				<table id="ptsPagesTbl"></table>	
				<div class="bottom-nav">
					<button id="imex-export"><?php _e('Export', PTS_LANG_CODE)?></button>
				</div>
			</div>
			<div class="supsystic-imex-export-json-page supsystic-imex-page">
				<div class="imex-description">
					In order to export tables - choose the table, that you want to export and click Export button in the right bottom corner. Copy the code. Then go to another site to Pricing table plugin at Tables Import/Export tab. Paste the code on Import tab. Click Import button in the right bottom corner.
				</div>
				<textarea id="imex-export-json"></textarea>
			</div>
			<div class="supsystic-imex-import-page supsystic-imex-page">
				<div class="imex-description">
					In order to import table - paste the code of the table, which you copied on Export tab from another site. Click Import button in the right bottom corner and wait for a "Success" message.
				</div>
				<div class="ptsUpdateConfigPanel">
					<label for="ptsUpdateWithSameId">
						<input id="ptsUpdateWithSameId" type="checkbox" value="ptsUpdateWithSameId"/>
						Update tables with the same ID
					</label>
				</div>
				<div class="wp-messages">
					<div class="message error errorFormat"><?php _e('Incorrect data!', PTS_LANG_CODE)?></div>
					<div class="message success successAddedTable"><?php _e('Success', PTS_LANG_CODE)?></div>
				</div>
				<textarea id="imex-import-json"></textarea>
				<div class="bottom-nav">
					<button id="imex-import"><?php _e('Import', PTS_LANG_CODE)?></button>
				</div>
			</div>
		</div>
	</div>
</section>