<!--Block menus example-->
<div id="ptsBlockMenuExl" class="ptsBlockMenu">
	<div class="ptsBlockMenuEl" data-menu="align">
		<div class="ptsBlockMenuElTitle ptsBlockMenuElAlignTitle">
			<?php _e('Content align', PTS_LANG_CODE)?>
		</div>
		<div class="ptsBlockMenuElAlignContent row">
			<div class="col-sm-4 ptsBlockMenuElElignBtn" data-align="left">
				<i class="tables-icon tables-icon-2x icon-aligne-left"></i>
			</div>
			<div class="col-sm-4 ptsBlockMenuElElignBtn" data-align="center">
				<i class="tables-icon tables-icon-2x icon-aligne-center"></i>
			</div>
			<div class="col-sm-4 ptsBlockMenuElElignBtn" data-align="right">
				<i class="tables-icon tables-icon-2x icon-aligne-right"></i>
			</div>
		</div>
		<?php echo htmlPts::hidden('params[align]')?>
	</div>
	<div class="ptsBlockMenuEl" data-menu="add_slide">
		<div class="ptsBlockMenuElAct">
			<i class="tables-icon tables-icon-lg icon-image ptsChangeImgBtnIcon"></i>
		</div>
		<div class="ptsBlockMenuElTitle">
			<?php _e('Add Slide', PTS_LANG_CODE)?>
		</div>
	</div>
	<div class="ptsBlockMenuEl" data-menu="add_gal_item">
		<div class="ptsBlockMenuElAct">
			<i class="tables-icon tables-icon-lg icon-image ptsChangeImgBtnIcon"></i>
		</div>
		<div class="ptsBlockMenuElTitle">
			<?php _e('Add Image', PTS_LANG_CODE)?>
		</div>
	</div>
	<div class="ptsBlockMenuEl" data-menu="add_menu_item">
		<div class="ptsBlockMenuElAct">
			<i class="tables-icon tables-icon-lg icon-plus-s"></i>
		</div>
		<div class="ptsBlockMenuElTitle">
			<?php _e('Add Menu Item', PTS_LANG_CODE)?>
		</div>
	</div>
	<div class="ptsBlockMenuEl" data-menu="edit_slides">
		<div class="ptsBlockMenuElAct">
			<i class="tables-icon tables-icon-lg icon-manage ptsChangeImgBtnIcon"></i>
		</div>
		<div class="ptsBlockMenuElTitle">
			<?php _e('Manage Slides', PTS_LANG_CODE)?>
		</div>
	</div>
	<div class="ptsBlockMenuEl" data-menu="fill_color">
		<div class="ptsBlockMenuElAct">
			<?php echo htmlPts::checkbox('params[fill_color_enb]')?>
		</div>
		<div class="ptsBlockMenuElTitle">
			<?php _e('Fill Color', PTS_LANG_CODE)?>
		</div>
<!--		<div class="ptsBlockMenuElRightAct">-->
<!--            --><?php //echo htmlPts::hidden('params[fill_color]', array(
//                'attrs' => 'class="ptsColorPickInput"'
//            ));?>
<!--            <div class="ptsTear ptsColorPickInputTear"></div>-->
<!--		</div>-->
	</div>
	<div class="ptsBlockMenuEl" data-menu="bg_img">
		<div class="ptsBlockMenuElAct">
			<?php echo htmlPts::checkbox('params[bg_img_enb]')?>
		</div>
		<div class="ptsBlockMenuElTitle">
			<?php _e('Background Image...', PTS_LANG_CODE)?>
		</div>
		<div class="ptsBlockMenuElRightAct">
			<i class="tables-icon tables-icon-lg icon-image"></i>
		</div>
	</div>
	<div class="ptsBlockMenuEl" data-menu="add_field">
		<div class="ptsBlockMenuElAct">
			<i class="tables-icon tables-icon-lg icon-plus-s"></i>
		</div>
		<div class="ptsBlockMenuElTitle">
			<?php _e('Add Field', PTS_LANG_CODE)?>
		</div>
	</div>
	<div class="ptsBlockMenuEl" data-menu="sub_settings">
		<div class="ptsBlockMenuElAct">
			<i class="glyphicon glyphicon-send"></i>
		</div>
		<div class="ptsBlockMenuElTitle">
			<?php _e('Subscribe Settings', PTS_LANG_CODE)?>
		</div>
	</div>
	<div class="ptsBlockMenuEl" data-menu="add_grid_item">
		<div class="ptsBlockMenuElAct">
			<i class="tables-icon tables-icon-lg icon-image"></i>
		</div>
		<div class="ptsBlockMenuElTitle">
			<?php _e('Add Column', PTS_LANG_CODE)?>
		</div>
	</div>
</div>
<!--Image menu-->
<div id="ptsElMenuImgExl" class="ptsElMenu" style="min-width: 330px;">
	<div class="ptsElMenuContent">
		<div class="ptsElMenuMainPanel">
			<div class="ptsElMenuBtn ptsImgChangeBtn">
				<label>
					<?php echo htmlPts::radiobutton('type', array('value' => 'img'))?>
					<?php _e('Select image', PTS_LANG_CODE)?>
					<i class="glyphicon glyphicon-picture"></i>
				</label>
			</div>
			<div class="ptsElMenuBtnDelimiter"></div>
			<div class="ptsElMenuBtn ptsImgVideoSetBtn" data-sub-panel-show="video">
				<label>
					<?php echo htmlPts::radiobutton('type', array('value' => 'video'))?>
					<?php _e('Video', PTS_LANG_CODE)?>
					<i class="fa fa-video-camera ptsOptIconBtn"></i>
				</label>
			</div>
			<div class="ptsElMenuBtnDelimiter"></div>
			<div class="ptsElMenuBtn ptsLinkBtn" data-sub-panel-show="imglink">
				<label>
					<i class="glyphicon glyphicon-link"></i>
					<?php _e('Link', PTS_LANG_CODE); ?>
				</label>
			</div>
		</div>
		<div class="ptsElMenuSubPanel" data-sub-panel="video">
			<label class="ptsElMenuSubPanelRow">
				<span class="mce-input-name-txt"><?php _e('link', PTS_LANG_CODE)?></span>
				<?php echo htmlPts::text('video_link')?>
			</label>
		</div>
		<div class="ptsElMenuSubPanel" data-sub-panel="imglink">
			<label class="ptsElMenuSubPanelRow">
				<span class="mce-input-name-txt"><?php _e('link', PTS_LANG_CODE)?></span>
				<?php echo htmlPts::text('icon_item_link');?>
			</label>
			<div style="display: none;" class="ptsPostLinkDisabled" data-postlink-to=":parent label [name='icon_item_link']"></div>
			<label class="ptsElMenuSubPanelRow">
				<span class="mce-input-name-txt"><?php _e('title', PTS_LANG_CODE)?></span>
				<?php echo htmlPts::text('icon_item_title');?>
			</label>
			<label class="ptsElMenuSubPanelRow">
				<?php echo htmlPts::checkbox('icon_item_link_new_wnd')?>
				<span class="mce-input-name-txt mce-input-name-not-first"><?php _e('open link in a new window', PTS_LANG_CODE)?></span>
			</label>
			<label class="ptsElMenuSubPanelRow">
				<?php echo htmlPts::checkbox('icon_item_link_rel_nofollow')?>
				<span class="mce-input-name-txt mce-input-name-not-first"><?php _e('add nofollow attribute', PTS_LANG_CODE)?></span>
			</label>
		</div>
	</div>
</div>
<!--Standart Button menu-->
<div id="ptsElMenuBtnExl" class="ptsElMenu" style="min-width: 160px;">
	<div class="ptsElMenuContent">
		<div class="ptsElMenuMainPanel">
			<div class="ptsElMenuBtn ptsLinkBtn" data-sub-panel-show="link">
				<label>
					<i class="glyphicon glyphicon-link"></i>
					<?php _e('Link', PTS_LANG_CODE)?>
				</label>
			</div>
			<div class="ptsElMenuBtnDelimiter"></div>
			<div class="ptsElMenuBtn ptsColorBtn" data-sub-panel-show="color-pick-table-cell">
				<label>
					<?php _e('Color', PTS_LANG_CODE)?>
					<div class="ptsTear ptsColorPickInputTear"></div>
				</label>
			</div>
			<div class="ptsElMenuBtnDelimiter"></div>
			<!-- Select Type -->
			<div class="ptsElMenuBtn ptsTypeTxtBtn" style="padding-right: 5px;">
				<label>
					<?php echo htmlPts::radiobutton('type', array('value' => 'txt'))?>
					<?php _e('Text', PTS_LANG_CODE)?>
				</label>
			</div>
			<div class="ptsElMenuBtn ptsTypeImgBtn" style="padding-right: 5px;">
				<label>
					<?php echo htmlPts::radiobutton('type', array('value' => 'img'))?>
					<?php _e('Image / Video', PTS_LANG_CODE)?>
				</label>
			</div>
			<div class="ptsElMenuBtn ptsTypeIconBtn">
				<label>
					<?php echo htmlPts::radiobutton('type', array('value' => 'icon'))?>
					<?php _e('Icon', PTS_LANG_CODE)?>
				</label>
			</div>
			<div class="ptsElMenuBtnDelimiter"></div>
			<div class="ptsElMenuBtn ptsRemoveElBtn">
				<i class="fa fa-trash-o ptsOptIconBtn"></i>
			</div>
		</div>
		<div class="ptsElMenuSubPanel" data-sub-panel="link">
			<label class="ptsElMenuSubPanelRow">
				<span class="mce-input-name-txt"><?php _e('link', PTS_LANG_CODE)?></span>
				<?php echo htmlPts::text('btn_item_link')?>
			</label>
			<div style="display: none;" class="ptsPostLinkDisabled" data-postlink-to=":parent label [name='btn_item_link']"></div>
			<label class="ptsElMenuSubPanelRow">
				<span class="mce-input-name-txt"><?php _e('title', PTS_LANG_CODE)?></span>
				<?php echo htmlPts::text('btn_item_title')?>
			</label>
			<label class="ptsElMenuSubPanelRow">
				<?php echo htmlPts::checkbox('btn_item_link_new_wnd')?>
				<span class="mce-input-name-txt mce-input-name-not-first"><?php _e('open link in a new window', PTS_LANG_CODE)?></span>
			</label>
			<label class="ptsElMenuSubPanelRow">
				<?php echo htmlPts::checkbox('btn_item_link_rel_nofollow')?>
				<span class="mce-input-name-txt mce-input-name-not-first"><?php _e('add nofollow attribute', PTS_LANG_CODE)?></span>
			</label>
		</div>
		<div class="ptsElMenuSubPanel" data-sub-panel="color-pick-table-cell">
			<div class="ptsInlineColorPicker"></div>
		</div>
	</div>
</div>
<!--Image menu-->
<div id="ptsElMenuImgExl" class="ptsElMenu" style="min-width: 260px;">
	<div class="ptsElMenuContent">
		<div class="ptsElMenuMainPanel">
			<div class="ptsElMenuBtn ptsImgChangeBtn">
				<label>
					<?php echo htmlPts::radiobutton('type', array('value' => 'img'))?>
					<?php _e('Select image', PTS_LANG_CODE)?>
					<i class="glyphicon glyphicon-picture"></i>
				</label>
			</div>
			<div class="ptsElMenuBtnDelimiter"></div>
			<div class="ptsElMenuBtn ptsImgVideoSetBtn" data-sub-panel-show="video">
				<label>
					<?php echo htmlPts::radiobutton('type', array('value' => 'video'))?>
					<?php _e('Video', PTS_LANG_CODE)?>
					<i class="fa fa-video-camera ptsOptIconBtn"></i>
				</label>
			</div>
			<div class="ptsElMenuBtnDelimiter"></div>
			<div class="ptsElMenuBtn ptsRemoveElBtn">
				<i class="fa fa-trash-o ptsOptIconBtn"></i>
			</div>
		</div>
		<div class="ptsElMenuSubPanel" data-sub-panel="video">
			<label class="ptsElMenuSubPanelRow">
				<span class="mce-input-name-txt"><?php _e('link', PTS_LANG_CODE)?></span>
				<?php echo htmlPts::text('video_link')?>
			</label>
		</div>
	</div>
</div>
<!--Grid Column menu-->
<div id="ptsElMenuGridColExl" class="ptsElMenu" style="min-width: 370px;">
	<div class="ptsElMenuContent">
		<div class="ptsElMenuMainPanel">
			<div class="ptsElMenuBtn">
				<?php echo htmlPts::checkbox('enb_fill_color')?>
			</div>
			<div class="ptsElMenuBtn ptsColorBtn" data-sub-panel-show="color-pick-table-cell">
				<label>
					<?php _e('Fill Color', PTS_LANG_CODE)?>
					<div class="ptsTear ptsColorPickInputTear"></div>
				</label>
			</div>
			<div class="ptsElMenuBtnDelimiter"></div>
			<div class="ptsElMenuBtn">
				<?php echo htmlPts::checkbox('enb_bg_img')?>
			</div>
			<div class="ptsElMenuBtn ptsImgChangeBtn">
				<label>
					<?php _e('Background Image', PTS_LANG_CODE)?>
					<i class="glyphicon glyphicon-picture"></i>
				</label>
			</div>
			<div class="ptsElMenuBtnDelimiter"></div>
			<div class="ptsElMenuBtn ptsRemoveElBtn">
				<i class="fa fa-trash-o ptsOptIconBtn"></i>
			</div>
		</div>
		<div class="ptsElMenuSubPanel" data-sub-panel="color-pick-table-cell">
			<div class="ptsInlineColorPicker"></div>
		</div>
	</div>
</div>
<!--Table Column menu-->
<div id="ptsElMenuTableColExl" class="ptsElMenu" style="min-width: 200px;">
	<div class="ptsElMenuContent">
		<div class="ptsElMenuMainPanel">
<!--			<div class="ptsElMenuMoveHandlerPlace"></div>-->
<!--			<div class="ptsElMenuBtnDelimiter"></div>-->
			<div class="ptsElMenuBtn">
				<?php echo htmlPts::checkbox('enb_fill_color')?>
			</div>
			<div class="ptsElMenuBtn ptsColorBtn" data-sub-panel-show="color-pick-table-cell">
				<label>
					<?php _e('Fill Color', PTS_LANG_CODE)?>
					<?php echo htmlPts::hidden('color', array(
						'attrs' => 'class="ptsColorPickInput"'
					));?>
					<div class="ptsTear ptsColorPickInputTear"></div>
				</label>
			</div>
			<div class="ptsElMenuBtnDelimiter"></div>
			<div class="ptsElMenuBtn">
				<?php echo htmlPts::checkbox('enb_badge_col')?>
			</div>
			<div class="ptsElMenuBtn ptsColBadgeBtn">
				<?php _e('Badge for Column', PTS_LANG_CODE)?>
			</div>
			<div class="ptsElMenuBtnDelimiter"></div>
			<div class="ptsElMenuBtn ptsScheduleColBtn" title="<?php _e('Schedule column', PTS_LANG_CODE)?>" data-sub-panel-show="schedule">
				<i class="fa fa-calendar ptsOptIconBtn"></i>
			</div>

			<div class="ptsElMenuBtnDelimiter"></div>
			<div class="ptsElMenuBtn ptsRemoveElBtn">
				<i class="fa fa-trash-o ptsOptIconBtn"></i>
			</div>
		</div>
		<div class="ptsElMenuSubPanel" data-sub-panel="color-pick-table-cell">
			<div class="ptsInlineColorPicker"></div>
		</div>
		<div class="ptsElMenuSubPanel" data-sub-panel="schedule">
			<label style="display: inline-block; margin-right: 5px;">
				<?php echo htmlPts::checkbox('enb_schedule_col')?>
				<?php _e('Schedule', PTS_LANG_CODE)?>
			</label>
			<label style="display: inline-block;">
				<?php _e('From', PTS_LANG_CODE)?>: <?php echo htmlPts::text('schedule_date_from', array(
					'attrs' => 'style="width: auto;"'
				))?>
			</label>
			<label style="display: inline-block;">
				<?php _e('To', PTS_LANG_CODE)?>: <?php echo htmlPts::text('schedule_date_to', array(
					'attrs' => 'style="width: auto;"'
				))?>
			</label>
			<br style="clear: both;" />
		</div>
	</div>
</div>
<!--Table Cell menu-->
<div id="ptsElMenuTableCellExl" class="ptsElMenu" style="min-width: 270px;">
	<div class="ptsElMenuContent">
		<div class="ptsElMenuMainPanel">
			<div class="ptsElMenuBtn ptsTypeTxtBtn" style="padding-right: 5px;">
				<label>
					<?php echo htmlPts::radiobutton('type', array('value' => 'txt'))?>
					<?php _e('Text', PTS_LANG_CODE)?>
				</label>
			</div>
			<div class="ptsElMenuBtn ptsTypeImgBtn" style="padding-right: 5px;">
				<label>
					<?php echo htmlPts::radiobutton('type', array('value' => 'img'))?>
					<?php _e('Image / Video', PTS_LANG_CODE)?>
				</label>
			</div>
			<div class="ptsElMenuBtn ptsTypeIconBtn">
				<label>
					<?php echo htmlPts::radiobutton('type', array('value' => 'icon'))?>
					<?php _e('Icon', PTS_LANG_CODE)?>
				</label>
			</div>
			<div class="ptsElMenuBtn ptsTypeButtonBtn">
				<label>
					<?php echo htmlPts::radiobutton('type', array('value' => 'btn'))?>
					<?php _e('Button', PTS_LANG_CODE)?>
				</label>
			</div>
			<div class="ptsElMenuBtnDelimiter"></div>
			<div class="ptsElMenuBtn ptsRemoveElBtn">
				<i class="fa fa-trash-o ptsOptIconBtn"></i>
			</div>
		</div>
	</div>
</div>
<!-- Icon menu -->
<div id="ptsElMenuIconExl" class="ptsElMenu" style="min-width: 290px;">
	<div class="ptsElMenuContent">
		<div class="ptsElMenuMainPanel">
			<div class="ptsElMenuBtn ptsIconLibBtn" data-sub-panel-show="link">
				<i class="fa fa-lg fa-pencil ptsOptIconBtn"></i>
				<?php _e('Change Icon', PTS_LANG_CODE)?>
			</div>
			<div class="ptsElMenuBtnDelimiter"></div>
			<div class="ptsElMenuBtn ptsColorBtn" data-sub-panel-show="color-pick-table-cell">
				<?php _e('Color', PTS_LANG_CODE)?>
				<div class="ptsTear ptsColorPickInputTear"></div>
			</div>
			<div class="ptsElMenuBtnDelimiter"></div>
			<div class="ptsElMenuBtn ptsLinkBtn" data-sub-panel-show="iconlink">
				<i class="glyphicon glyphicon-link"></i>
				<?php _e('Link', PTS_LANG_CODE)?>
			</div>
			<div class="ptsElMenuBtnDelimiter"></div>
			<div class="ptsElMenuBtn ptsRemoveElBtn">
				<i class="fa fa-trash-o ptsOptIconBtn"></i>
			</div>
		</div>
		<div class="ptsElMenuSubPanel" data-sub-panel="iconlink">
			<label class="ptsElMenuSubPanelRow">
				<span class="mce-input-name-txt"><?php _e('link', PTS_LANG_CODE)?></span>
				<?php echo htmlPts::text('icon_item_link');?>
			</label>
			<div style="display: none;" class="ptsPostLinkDisabled" data-postlink-to=":parent label [name='icon_item_link']"></div>
			<label class="ptsElMenuSubPanelRow">
				<span class="mce-input-name-txt"><?php _e('title', PTS_LANG_CODE)?></span>
				<?php echo htmlPts::text('icon_item_title');?>
			</label>
			<label class="ptsElMenuSubPanelRow">
				<?php echo htmlPts::checkbox('icon_item_link_new_wnd')?>
				<span class="mce-input-name-txt mce-input-name-not-first"><?php _e('open link in a new window', PTS_LANG_CODE)?></span>
			</label>
			<label class="ptsElMenuSubPanelRow">
				<?php echo htmlPts::checkbox('icon_item_link_rel_nofollow')?>
				<span class="mce-input-name-txt mce-input-name-not-first"><?php _e('add nofollow attribute', PTS_LANG_CODE)?></span>
			</label>
		</div>
		<div class="ptsElMenuSubPanel" data-sub-panel="color-pick-table-cell">
			<div class="ptsInlineColorPicker"></div>
		</div>
	</div>
</div>
<!-- Table Cell Icon menu -->
<div id="ptsElMenuTableCellIconExl" class="ptsElMenu" style="min-width: 250px;">
	<div class="ptsElMenuContent">
		<div class="ptsElMenuMainPanel">
			<div class="ptsElMenuBtn ptsIconLibBtn" data-sub-panel-show="link">
				<i class="fa fa-lg fa-pencil ptsOptIconBtn"></i>
				<?php _e('Change Icon', PTS_LANG_CODE)?>
			</div>
			<div class="ptsElMenuBtnDelimiter"></div>
			<div class="ptsElMenuBtn"  data-sub-panel-show="size">
				<i class="glyphicon glyphicons-resize-small"></i>
				<?php _e('Icon Size', PTS_LANG_CODE)?>
			</div>
			<div class="ptsElMenuBtnDelimiter"></div>
			<div class="ptsElMenuBtn ptsColorBtn" data-sub-panel-show="color-pick-table-cell">
				<?php _e('Color', PTS_LANG_CODE)?>
				<div class="ptsTear ptsColorPickInputTear"></div>
			</div>
			<div class="ptsElMenuBtnDelimiter"></div>
			<div class="ptsElMenuBtn ptsLinkBtn" data-sub-panel-show="iconlink">
				<label>
					<i class="glyphicon glyphicon-link"></i>
					<?php _e('Link', PTS_LANG_CODE)?>
				</label>
			</div>
			<div class="ptsElMenuBtnDelimiter"></div>
			<div class="ptsElMenuBtn ptsTypeTxtBtn" style="padding-right: 5px;">
				<label>
					<?php echo htmlPts::radiobutton('type', array('value' => 'txt'))?>
					<?php _e('Text', PTS_LANG_CODE)?>
				</label>
			</div>
			<div class="ptsElMenuBtn ptsTypeImgBtn" style="padding-right: 5px;">
				<label>
					<?php echo htmlPts::radiobutton('type', array('value' => 'img'))?>
					<?php _e('Image / Video', PTS_LANG_CODE)?>
				</label>
			</div>
			<div class="ptsElMenuBtn ptsTypeButtonBtn">
				<label>
					<?php echo htmlPts::radiobutton('type', array('value' => 'btn'))?>
					<?php _e('Button', PTS_LANG_CODE)?>
				</label>
			</div>
			<div class="ptsElMenuBtnDelimiter"></div>
			<div class="ptsElMenuBtn ptsRemoveElBtn">
				<i class="fa fa-trash-o ptsOptIconBtn"></i>
			</div>
		</div>
		<div class="ptsElMenuSubPanel ptsElMenuSubPanelIconSize" data-sub-panel="size">
			<span data-size="fa-lg">lg</span>
			<span data-size="fa-2x">2x</span>
			<span data-size="fa-3x">3x</span>
			<span data-size="fa-4x">4x</span>
			<span data-size="fa-5x">5x</span>
		</div>
		<div class="ptsElMenuSubPanel" data-sub-panel="iconlink">
			<label class="ptsElMenuSubPanelRow">
				<span class="mce-input-name-txt"><?php _e('link', PTS_LANG_CODE)?></span>
				<?php echo htmlPts::text('icon_item_link')?>
			</label>
			<div style="display: none;" class="ptsPostLinkDisabled" data-postlink-to=":parent label [name='icon_item_link']"></div>
			<label class="ptsElMenuSubPanelRow">
				<span class="mce-input-name-txt"><?php _e('title', PTS_LANG_CODE)?></span>
				<?php echo htmlPts::text('icon_item_title')?>
			</label>
			<label class="ptsElMenuSubPanelRow">
				<?php echo htmlPts::checkbox('icon_item_link_new_wnd')?>
				<span class="mce-input-name-txt mce-input-name-not-first"><?php _e('open link in a new window', PTS_LANG_CODE)?></span>
			</label>
			<label class="ptsElMenuSubPanelRow">
				<?php echo htmlPts::checkbox('icon_item_link_rel_nofollow')?>
				<span class="mce-input-name-txt mce-input-name-not-first"><?php _e('add nofollow attribute', PTS_LANG_CODE)?></span>
			</label>
		</div>
		<div class="ptsElMenuSubPanel"></div>
		<div class="ptsElMenuSubPanel" data-sub-panel="color-pick-table-cell">
			<div class="ptsInlineColorPicker"></div>
		</div>
	</div>
</div>
<!--Table Cell Image menu-->
<div id="ptsElMenuTableCellImgExl" class="ptsElMenu" style="min-width: 360px;">
	<div class="ptsElMenuContent">
		<div class="ptsElMenuMainPanel">
			<div class="ptsElMenuBtn ptsImgChangeBtn">
				<label>
					<?php echo htmlPts::radiobutton('type', array('value' => 'img'))?>
					<?php _e('Select image', PTS_LANG_CODE)?>
					<i class="glyphicon glyphicon-picture"></i>
				</label>
			</div>
			<div class="ptsElMenuBtnDelimiter"></div>
			<div class="ptsElMenuBtn ptsLinkBtn" data-sub-panel-show="imagelink">
				<label>
					<i class="glyphicon glyphicon-link"></i>
					<?php _e('Link', PTS_LANG_CODE)?>
				</label>
			</div>
			<div class="ptsElMenuBtnDelimiter"></div>
			<div class="ptsElMenuBtn ptsImgVideoSetBtn" data-sub-panel-show="video">
				<label>
					<?php echo htmlPts::radiobutton('type', array('value' => 'video'))?>
					<?php _e('Video', PTS_LANG_CODE)?>
					<i class="fa fa-video-camera ptsOptIconBtn"></i>
				</label>
			</div>
			<div class="ptsElMenuBtnDelimiter"></div>
			<div class="ptsElMenuBtn ptsTypeTxtBtn" style="padding-right: 5px;">
				<label>
					<?php echo htmlPts::radiobutton('type', array('value' => 'txt'))?>
					<?php _e('Text', PTS_LANG_CODE)?>
				</label>
			</div>
			<div class="ptsElMenuBtn ptsTypeIconBtn">
				<label>
					<?php echo htmlPts::radiobutton('type', array('value' => 'icon'))?>
					<?php _e('Icon', PTS_LANG_CODE)?>
				</label>
			</div>
			<div class="ptsElMenuBtn ptsTypeButtonBtn">
				<label>
					<?php echo htmlPts::radiobutton('type', array('value' => 'btn'))?>
					<?php _e('Button', PTS_LANG_CODE)?>
				</label>
			</div>
			<div class="ptsElMenuBtnDelimiter"></div>
			<div class="ptsElMenuBtn ptsRemoveElBtn">
				<i class="fa fa-trash-o ptsOptIconBtn"></i>
			</div>
		</div>
		<div class="ptsElMenuSubPanel" data-sub-panel="video">
			<label class="ptsElMenuSubPanelRow">
				<span class="mce-input-name-txt"><?php _e('link', PTS_LANG_CODE)?></span>
				<?php echo htmlPts::text('video_link')?>
			</label>
		</div>
		<div class="ptsElMenuSubPanel" data-sub-panel="imagelink">
			<label class="ptsElMenuSubPanelRow">
				<span class="mce-input-name-txt"><?php _e('link', PTS_LANG_CODE)?></span>
				<?php echo htmlPts::text('image_item_link')?>
			</label>
			<div style="display: none;" class="ptsPostLinkDisabled" data-postlink-to=":parent label [name='image_item_link']"></div>
			<label class="ptsElMenuSubPanelRow">
				<span class="mce-input-name-txt"><?php _e('title', PTS_LANG_CODE)?></span>
				<?php echo htmlPts::text('image_item_title')?>
			</label>
			<label class="ptsElMenuSubPanelRow">
				<?php echo htmlPts::checkbox('image_item_link_new_wnd')?>
				<span class="mce-input-name-txt mce-input-name-not-first"><?php _e('open link in a new window', PTS_LANG_CODE)?></span>
			</label>
			<label class="ptsElMenuSubPanelRow">
				<?php echo htmlPts::checkbox('image_item_link_rel_nofollow')?>
				<span class="mce-input-name-txt mce-input-name-not-first"><?php _e('add nofollow attribute', PTS_LANG_CODE)?></span>
			</label>
		</div>
	</div>
</div>
<!--Icons library wnd-->
<div class="modal fade" id="ptsIconsLibWnd" tabindex="-1" role="dialog" aria-labelledby="ptsIconsLibWndLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="button close" data-dismiss="modal" aria-label="Close">
					<i class="fa fa-close" aria-hidden="true"></i>
				</button>
				<h4 class="modal-title"><?php _e('Icons Library', PTS_LANG_CODE)?></h4>
			</div>
			<div class="modal-body ptsElMenuSubPanel">
				<div id="ptsSubSettingsWndTabs">
					<?php echo htmlPts::text('icon_search', array(
						'attrs' => 'class="ptsIconsLibSearchTxt" placeholder="'. esc_html(__('Search, for example - pencil, music, ...', PTS_LANG_CODE)). '"',
					))?>
					<div class="ptsIconsLibList row"></div>
					<div class="ptsIconsLibEmptySearch alert alert-info" style="display: none;"><?php _e('Nothing found for <span class="ptsNothingFoundKeys"></span>, maybe try to search something else?', PTS_LANG_CODE)?></div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="button-primary ptsIconsLibSaveBtn"><?php _e('Close', PTS_LANG_CODE)?></button>
			</div>
		</div>
	</div>
</div>
<!--Badges library wnd-->
<div class="modal fade" id="ptsBadgesLibWnd" tabindex="-1" role="dialog" aria-labelledby="ptsIconsLibWndLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="button close" data-dismiss="modal" aria-label="Close">
					<i class="fa fa-close" aria-hidden="true"></i>
				</button>
				<h4 class="modal-title"><?php _e('Badges Library', PTS_LANG_CODE)?></h4>
			</div>
			<div class="modal-body ptsElMenuSubPanel">
				<form id="ptsBadgesLibForm">
					<div class="row">
						<div class="col-sm-6">
							<div class="ptsTableSetting row col-sm-12">
								<label>
									<?php _e('Badge Label', PTS_LANG_CODE)?>:
									<?php echo htmlPts::text('badge_name', array('value' => __('Sale!', PTS_LANG_CODE)))?>
								</label>
							</div>
							<?php /*?><div class="ptsTableSetting row col-sm-12">
								<?php _e('Badge Type', PTS_LANG_CODE)?>:
								<div>
									<label>
										<?php echo htmlPts::radiobutton('badge_type', array('value' => 'corner', 'checked' => true))?>
										<?php _e('Corner', PTS_LANG_CODE)?>
									</label>
									<label>
										<?php echo htmlPts::radiobutton('badge_type', array('value' => 'corner_cut'))?>
										<?php _e('Corner Cut', PTS_LANG_CODE)?>
									</label>
									<label>
										<?php echo htmlPts::radiobutton('badge_type', array('value' => 'rect'))?>
										<?php _e('Rectangle', PTS_LANG_CODE)?>
									</label>
									<label>
										<?php echo htmlPts::radiobutton('badge_type', array('value' => 'circle'))?>
										<?php _e('Circle', PTS_LANG_CODE)?>
									</label>
								</div>
							</div><?php */?>
							<div class="ptsTableSetting row col-sm-12">
								<?php _e('Badge Background Color', PTS_LANG_CODE)?>:
								<?php echo htmlPts::hidden('badge_bg_color', array(
									'attrs' => 'class="ptsColorPickInput"',
								));?>
								<div class="ptsTear ptsColorPickInputTear"></div>
							</div>
							<div class="ptsTableSetting row col-sm-12">
								<?php _e('Badge Text Color', PTS_LANG_CODE)?>:
								<?php echo htmlPts::hidden('badge_txt_color', array(
									'attrs' => 'class="ptsColorPickInput"',
								));?>
								<div class="ptsTear ptsColorPickInputTear"></div>
							</div>
							<div class="ptsTableSetting row ptsTableBadgePositionRow col-sm-12">
								<?php _e('Select position for Badge', PTS_LANG_CODE)?>
								<?php echo htmlPts::hidden('badge_pos', array('value' => 'left'))?>
								<div class="ptsTableBadgePositionsShell">
									<div class="ptsTableBadgePosition active" data-pos="left"></div>
									<div class="ptsTableBadgePosition" data-pos="left-top"></div>
									<div class="ptsTableBadgePosition" data-pos="top"></div>
									<div class="ptsTableBadgePosition" data-pos="right-top"></div>
									<div class="ptsTableBadgePosition" data-pos="right"></div>
								</div>
							</div>
						</div>
						<div class="col-sm-6">
							<div id="ptsTableBadgeColTest">
								<div id="ptsTableBadgePrev" class="ptsColBadge" style="">
									<div class="ptsColBadgeContent"></div>
								</div>
							</div>
						</div>
					</div>
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="button-primary ptsBadgesLibSaveBtn"><?php _e('Save', PTS_LANG_CODE)?></button>
			</div>
		</div>
	</div>
</div>
<!--Movable handler-->
<div id="ptsMoveHandlerExl" class="ptsMoveHandler ptsShowSmooth">
	<i class="fa fa-arrows ptsOptIconBtn"></i>
</div>
<!--Cell move btn-->
<div id="ptsMoveCellBtnExl" class="ptsMoveCellBtn ptsElMenuBtn ptsAddCellEditBtn" title="<?php _e('Move cell', PTS_LANG_CODE)?>">
	<i class="fa fa-arrows-v ptsOptIconBtn"></i>
</div>
<!--Add row after btn-->
<div id="ptsAddRowAfterBtnExl" class="ptsAddRowAfterBtn ptsElMenuBtn ptsAddCellEditBtn" title="<?php _e('Add row after', PTS_LANG_CODE)?>">
	<i class="fa fa-plus ptsOptIconBtn" style="font-size: 10px; position: absolute; bottom: 1px; left: 4px;"></i>
	<i class="fa fa-arrow-down ptsOptIconBtn"></i>
</div>
<!--Add row before btn-->
<div id="ptsAddRowBeforeBtnExl" class="ptsAddRowBeforeBtn ptsElMenuBtn ptsAddCellEditBtn" title="<?php _e('Add row before', PTS_LANG_CODE)?>">
	<i class="fa fa-arrow-up ptsOptIconBtn"></i>
	<i class="fa fa-plus ptsOptIconBtn" style="font-size: 10px; position: absolute; top: 2px; left: 4px;"></i>
</div>

<!-- Combining with the previous row btn-->
<div id="ptsCombiningPrevBtnExl" class="ptsCombiningPrevBtnExl ptsElMenuBtn ptsAddCellEditBtn" title="<?php _e('Combining with the previous row', PTS_LANG_CODE)?>">
	<i class="fa fa-level-up ptsOptIconBtn"></i>
</div>
<!-- Combining with the next row row btn-->
<div id="ptsCombiningNextBtnExl" class="ptsCombiningNextBtnExl ptsElMenuBtn ptsAddCellEditBtn" title="<?php _e('Combining with the next row', PTS_LANG_CODE)?>">
	<i class="fa fa-level-down ptsOptIconBtn"></i>
</div>

<!-- Add new one cell in this column -->
<div id="ptsAddOneCellInColumn" class="ptsAddOneCellInColumn ptsElMenuBtn ptsAddCellEditBtn" title="<?php _e('Add One Cell', PTS_LANG_CODE)?>">
	<i class="fa fa-plus ptsOptIconBtn"></i>
</div>

<!-- Text align in column -->
<div id="ptsTextAlignColumn" class="ptsTextAlignColumn ptsElMenuBtn">
	<span class="ptsTextAlignSwitch" data-align="left">
		<i class="fa fa-align-left ptsOptIconBtn"></i>
	</span>
	<span class="ptsTextAlignSwitch" data-align="center">
		<i class="fa fa-align-center ptsOptIconBtn"></i>
	</span>
	<span class="ptsTextAlignSwitch" data-align="right">
		<i class="fa fa-align-right ptsOptIconBtn"></i>
	</span>
</div>

<!--Edit Tooltip cell btn-->
<div id="ptsTooltipEditBtnShellExl" class="ptsTooltipEditBtnShell">
	<div class="ptsTooltipEditBtn ptsElMenuBtn ptsAddCellEditBtn" title="<?php _e('Edit Tooltip for Cell', PTS_LANG_CODE)?>">
		<i class="fa fa-info ptsOptIconBtn"></i>
	</div>
	<div class="ptsTooltipEditWnd ptsShowSmooth">
		<textarea name="tooltip"></textarea>
	</div>
</div>
<!--Remove row btn-->
<div id="ptsRemoveRowBtnExl" class="ptsRemoveRowBtn ptsElMenuBtn ptsAddCellEditBtn" title="<?php _e('Remove Row', PTS_LANG_CODE)?>">
	<i class="fa fa-trash-o ptsOptIconBtn"></i>
</div>
<div id="ptsElementButtonDefaultTemplate" class="ptsActBtn ptsEl ptsElInput" data-el="btn">
	<a target="_blank" href="https://supsystic.com/" class="ptsEditArea ptsInputShell">Text</a>
</div>


<div id="ptsAddHtmlAttribute" class="ptsAddHtmlAttribute" data-sub-panel-show="add_attr">
	<label>
		<i class="mce-ico mce-i-fullpage"></i>
		Attributes
	</label>
</div>

<div id="ptsSubMenyAddHtmlAttr" class="ptsElMenuSubPanel" data-sub-panel="add_attr"></div>

<label id="ptsRowAddHtmlAttr" class="ptsElMenuSubPanelRow">
	<span class="mce-input-name-txt"></span>
	<input type="text" name="btn_item_link" value="">
</label>

<div id="ptsMceSubMenyAddHtmlAttr">
	<div class="mce-not-inline mce-menu-item mce-menu-item-normal mce-first mce-stack-layout-item mce-link-row">
		<label class="ptsElMenuSubPanelRow" data-id="id">
			<span class="mce-input-name-txt">id</span>
			<input type="text" name="id" value="">
		</label>
	</div>
	<div class="mce-not-inline mce-menu-item mce-menu-item-normal mce-first mce-stack-layout-item mce-link-row">
		<label class="ptsElMenuSubPanelRow" data-id="style">
			<span class="mce-input-name-txt">style</span>
			<input type="text" name="style" value="">
		</label>
	</div>
	<div class="mce-not-inline mce-menu-item mce-menu-item-normal mce-first mce-stack-layout-item mce-link-row">
		<label class="ptsElMenuSubPanelRow" data-id="class">
			<span class="mce-input-name-txt">class</span>
			<input type="text" name="class" value="">
		</label>
	</div>
</div>