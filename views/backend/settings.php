<?php echo "<h1>".__('Settings') . "</h1>"; ?>
	<form method="post" action="<?php echo get_url('plugin/djg_gallery/settings/save'); ?>">
	<fieldset style="padding: 0.5em;">
		<legend style="padding: 0em 0.5em 0em 0.5em; font-weight: bold;"><?php echo __('Files'); ?></legend>
		<table class="fieldset" cellpadding="0" cellspacing="0" border="0">
			<tr>
				<td class="label"><?php echo URL_PUBLIC ?></td>
				<td class="field">
					<input type="text" class="textbox" name="path" value="<?php echo $settings['path']; ?>" />
				</td>
				<td class="help"><?php echo __('Path to djg_gallery plugin directory'); ?></strong></td>
			</tr>
			<tr>
				<td class="label"><?php echo __('<strong>Image</strong> allowed extensions'); ?></td>
				<td class="field">
					<input type="text" onkeyup="javascript:this.value=this.value.replace(/[^a-z][,]/g, '');" class="textbox" name="img_ext" value="<?php echo $settings['img_ext']; ?>" />
				</td>
				<td class="help"><?php echo __('For example: *.jpg;*.png;'); ?></strong></td>
			</tr>
			<tr>
				<td class="label"><?php echo __('Max <strong>image</strong> file size'); ?></td>
				<td class="field">
					<input type="number" min="1" max="100" step="1" class="textbox" name="img_max_size" value="<?php echo $settings['img_max_size']; ?>" />
				</td>
				<td class="help"><?php echo __('MB'); ?></strong></td>
			</tr>
			<tr>
				<td class="label"><?php echo __('Resize source file width after upload'); ?></td>
				<td class="field">
                    <input type="number" min="0" max="10000" step="10" class="textbox" name="resize_org_x" value="<?php echo $settings['resize_org_x']; ?>" />
				</td>
				<td class="help"><?php echo __('Defult 0 - no resize'); ?></strong></td>
			</tr>
			<tr>
				<td class="label"><?php echo __('Resize source file height after upload'); ?></td>
				<td class="field">
                    <input type="number" min="0" max="10000" step="10" class="textbox" name="resize_org_y" value="<?php echo $settings['resize_org_y']; ?>" />
				</td>
				<td class="help"><?php echo __('Defult 0 - no resize'); ?></strong></td>
			</tr>				
		</table>
	</fieldset>
	<fieldset style="padding: 0.5em;">
			<legend style="padding: 0em 0.5em 0em 0.5em; font-weight: bold;"><?php echo __('Other'); ?></legend>
			<table class="fieldset" cellpadding="0" cellspacing="0" border="0">
			<tr>
				<td class="label"><?php echo __('Confirm delete file.'); ?></td>
				<td class="field">
                    <select class="select" name="confirm_del_file" id="confirm_del_file">
                        <option value="1" <?php if ($settings['confirm_del_file'] == "1") echo 'selected = "";' ?>><?php echo __('Yes'); ?></option>
                        <option value="0" <?php if ($settings['confirm_del_file'] == "0") echo 'selected = "";' ?>><?php echo __('No'); ?></option>
                    </select>
				</td>
				<td class="help"><?php echo __('If you won\'t delete file or files without comfirm set no.'); ?></strong></td>
			</tr>
			<tr>
				<td class="label"><?php echo __('Debug'); ?></td>
				<td class="field">
                    <select class="select" name="debug" id="debug">
                        <option value="1" <?php if ($settings['debug'] == "1") echo 'selected = "";' ?>><?php echo __('Yes'); ?></option>
                        <option value="0" <?php if ($settings['debug'] == "0") echo 'selected = "";' ?>><?php echo __('No'); ?></option>
                    </select>
				</td>
				<td class="help"><?php echo __(''); ?></strong></td>
			</tr>
		</table>
	</fieldset>
    <p class="buttons">
        <input class="button" name="commit" type="submit" accesskey="s" value="<?php echo __('Save'); ?>" />
    </p>
	</form>