&laquo;<span id="ptsTableEditableLabelShell" title="<?php _e('Click to Edit', PTS_LANG_CODE)?>">
	<span id="ptsTableEditableLabel"><?php echo $this->table['label']?></span>
	<?php echo htmlPts::text('table_label', array(
		'attrs' => 'id="ptsTableEditableLabelTxt"'
	))?>
	<i id="ptsTableLabelEditMsg" class="fa fa-fw fa-pencil"></i>
</span>&raquo;&nbsp;
<span>
	<?php echo htmlPts::selectbox('shortcode_example', array('options' => array(
			'shortcode' => __('Shortcode', PTS_LANG_CODE),
			'php_code' => __('PHP code', PTS_LANG_CODE),
		), 'attrs' => 'class="chosen" style="width:100px;" id="ptsTableShortcodeExampleSel"',
	))?>:
	<span id="ptsTableShortcodeShell" style="display: none;">
		<?php echo htmlPts::text('ptsCopyTextCode', array(
			'value' => esc_html('['. PTS_SHORTCODE. ' id='. $this->table['id']. ']'),
			'attrs' => 'class="ptsCopyTextCode"'));?>
	</span>
	<span id="ptsTablePhpCodeShell" style="display: none;">
		<?php echo htmlPts::text('ptsCopyTextCode', array(
			'value' => esc_html('<?php echo do_shortcode("['. PTS_SHORTCODE. ' id=\''. $this->table['id']. '\']");?>'),
			'attrs' => 'class="ptsCopyTextCode"'));?>
	</span>
</span>
<span id="ptsTableMainControllsShell" style="float: right; padding-right: 15px;">
	<button class="button button-primary ptsTableSaveBtn" title="<?php _e('Save all changes', PTS_LANG_CODE)?>">
		<i class="fa fa-fw fa-save"></i>
		<?php _e('Save', PTS_LANG_CODE)?>
	</button>
	<button class="button button-primary ptsTablePreviewBtn">
		<i class="fa fa-fw fa-eye"></i>
		<?php _e('Preview', PTS_LANG_CODE)?>
	</button>
	<a href="<?php echo $this->ptsAddNewUrl. '&change_for='. $this->table['id']?>" class="button button-primary ptsTableChangeTplBtn">
		<i class="fa fa-fw fa-repeat"></i>
		<?php _e('Change Template', PTS_LANG_CODE)?>
	</a>
	<a href="#" class="button button-primary ptsTableEditCssBtn" title="<?php _e('Edit Css', PTS_LANG_CODE)?>">
		<i class="fa fa-fw fa-css3"></i>
		<?php _e('Edit Css', PTS_LANG_CODE)?>
	</a>
	<a href="#" class="button button-primary ptsTableCloneBtn" title="<?php _e('Clone to New Table', PTS_LANG_CODE)?>">
		<i class="fa fa-fw fa-files-o"></i>
		<?php _e('Clone', PTS_LANG_CODE)?>
	</a>
	<?php /*?><button class="button button-primary ptsPopupSwitchActive" data-txt-off="<?php _e('Turn Off', PTS_LANG_CODE)?>" data-txt-on="<?php _e('Turn On', PTS_LANG_CODE)?>">
		<i class="fa fa-fw"></i>
		<span></span>
	</button><?php */?>
	<button class="button button-primary ptsTableRemoveBtn">
		<i class="fa fa-fw fa-trash-o"></i>
		<?php _e('Delete', PTS_LANG_CODE)?>
	</button>
</span>
<div style="clear: both; height: 0;"></div>
<div id="ptsTableSaveAsCopyWnd" style="display: none;" title="<?php _e('Clone Table', PTS_LANG_CODE)?>">
	<form id="ptsTableSaveAsCopyForm">
		<label>
			<?php _e('New Name', PTS_LANG_CODE)?>:
			<?php echo htmlPts::text('copy_label', array('value' => $this->table['label']. ' '. __('Copy', PTS_LANG_CODE), 'required' => true))?>
		</label>
		<div id="ptsTableSaveAsCopyMsg"></div>
		<?php echo htmlPts::hidden('mod', array('value' => 'tables'))?>
		<?php echo htmlPts::hidden('action', array('value' => 'saveAsCopy'))?>
		<?php echo htmlPts::hidden('id', array('value' => $this->table['id']))?>
	</form>
</div>
<div id="ptsTableInitEditCssDlg" style="display: none;" title="<?php _e('Edit Css', PTS_LANG_CODE)?>">
	<?php echo htmlPts::textarea('css', array(
		'value' => $this->table['id'] ? $this->table['css'] : '',
		'attrs' => 'id="ptsBbCssInp" class="ptsCssBlockEditor"'
	))?>
</div>
