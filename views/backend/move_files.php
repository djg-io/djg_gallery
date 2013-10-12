<h1><?php echo __('Move files to another page'); ?></h1>
<form method="post" action="<?php echo get_url('plugin/djg_gallery/move_files'); ?>">
<table class="fieldset" cellpadding="0" cellspacing="0" border="0">
		<tr>
			<td class="label"><?php echo __('Move from'); ?></td>
			<td class="field">
				<SELECT name="fromId">
				<option value=0><?php echo __('- chose page -'); ?></option>
				<?php
				foreach($pages as $row) echo '<option value="'.$row->id().'">'.$row->id().' : '.$row->title().' </option>';
				?>
				</SELECT>
			</td>
			<td class="help"><?php echo __('Source page'); ?></td>
		</tr>
		<tr>
			<td class="label"><?php echo __('Move to'); ?></td>
			<td class="field">
				<SELECT name="toId">
				<option value=0><?php echo __('- chose page -'); ?></option>
				<?php
				foreach($pages as $row) echo '<option value="'.$row->id().'">'.$row->id().' : '.$row->title().' </option>';
				?>
				</SELECT>
			</td>
			<td class="help"><?php echo __('Dest page'); ?></td>
		</tr>
		<tr>
			<td></td>
			<td colspan="2"></td>
		</tr>
</table>
<p class="buttons">
<input class="button" name="commit" type="submit" accesskey="s" value="<?php echo __('Move files'); ?>" />
</p>
</form>