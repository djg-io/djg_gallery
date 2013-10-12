<h1><?php echo __('Thumbnails'); ?></h1>

<table id="djg_table">
	<thead>
			<td><?php echo __('Id'); ?></td>
			<td><?php echo __('Name'); ?></td>
			<td><?php echo __('Hash'); ?></td>
			<td><?php echo __('Quality'); ?></td>
			<td><?php echo __('Crop'); ?></td>
			<td><?php echo __('Width'); ?></td>
			<td><?php echo __('Height'); ?></td>
			<td><?php echo __('Watermark'); ?></td>
			<td><?php echo __('Action'); ?></td>
	</thead>
	<tbody>
		<?php foreach($thumbnails as $thumbnail): ?>
		<?php echo '<tr class="' . even_odd() . '" thumbnail_id="' . $thumbnail['id'] . '">'; ?>
			<td><?php echo $thumbnail['id']; ?></td>
			<td><?php echo $thumbnail['thumbname']; ?></td>
			<td><?php echo $thumbnail['thumbhash']; ?></td>
			<td><?php echo $thumbnail['quality']; ?></td>
			<td>
				<?php if($thumbnail['crop'] == 1): ?>
					<img src="<?php echo ICONS_URI;?>action-approve-16.png" title="<?php echo __('Crop is enable'); ?>" alt="<?php echo __('Crop is enable'); ?>" />
					<?php echo $thumbnail['cropposition']; ?>
				<?php else: ?>
					<img src="<?php echo ICONS_URI;?>action-approve-disabled-16.png" title="<?php echo __('Crop is disable'); ?>" alt="<?php echo __('Crop is disable'); ?>" />
				<?php endif ?>
			</td>
			<td><?php echo $thumbnail['width']; ?></td>
			<td><?php echo $thumbnail['height']; ?></td>
			<td><?php echo $thumbnail['watermark']; ?></td>
			<td class="actions_wrapper">
				<?php if (($thumbnail['id']==1)): ?>
					<img src="<?php echo PLUGINS_URI;?>djg_gallery/images/action-regenerate-disabled-16.png" title="<?php echo __('Regenerate thumbnail'); ?>" alt="<?php echo __('Regenerate thumbnail'); ?>" />
					<img src="<?php echo ICONS_URI;?>action-rename-disabled-16.png" title="<?php echo __('Edit thumbnail'); ?>" alt="<?php echo __('Edit thumbnail'); ?>" />
					<img src="<?php echo ICONS_URI;?>action-delete-disabled-16.png" title="<?php echo __('Delete thumbnail'); ?>" alt="<?php echo __('Delete thumbnail'); ?>" />
				<?php else: ?>
					<a href="<?php echo get_url('plugin/djg_gallery/regenerate_thumbnail/'.$thumbnail['id']); ?>" class="regenerate_thumbnail"><img src="<?php echo PLUGINS_URI;?>djg_gallery/images/action-regenerate-16.png" title="<?php echo __('Regenerate thumbnail'); ?>" alt="<?php echo __('Regenerate thumbnail'); ?>" /></a>
					<a href="<?php echo get_url('plugin/djg_gallery/edit_thumbnail/'.$thumbnail['id']); ?>" class="edit_thumbnail"><img src="<?php echo ICONS_URI;?>action-rename-16.png" title="<?php echo __('Edit thumbnail'); ?>" alt="<?php echo __('Edit thumbnail'); ?>" /></a>
					<a href="#" class="del_thumbnail"><img src="<?php echo ICONS_URI;?>action-delete-16.png" title="<?php echo __('Delete thumbnail'); ?>" alt="<?php echo __('Delete thumbnail'); ?>" /></a>
				<?php endif; ?>
			</td>
		</tr>
		<?php endforeach; ?>
	</tbody>
</table>
<h2><?php echo __('Add new thumbnail'); ?></h2>

<form action="<?php echo get_url('plugin/djg_gallery/thumbnails'); ?>" method="post">
		<table class="fieldset" cellpadding="0" cellspacing="0" border="0">
			<tr>
				<td class="label"><?php echo __('Name'); ?></td>
				<td class="field">
					<input type="text" class="textbox"  maxlength="32" name="djg_gallery[thumbname]" value="<?php echo (isset($djg_gallery['thumbname']))?$djg_gallery['thumbname']:''; ?>" />
				</td>
				<td class="help"><?php echo __('Thumbnail name is a prefix for file name, for example: thumb_4JS9S.jpg'); ?></strong></td>
			</tr>
			<tr>
				<td class="label"><?php echo __('Hash'); ?></td>
				<td class="field">
					<input type="test" class="textbox" maxlength="32" name="djg_gallery[thumbhash]" value="<?php echo DjgGallery::uniqeId(); ?>" readonly />
				</td>
				<td class="help"><?php echo __('Read only'); ?></strong></td>
			</tr>
			<tr>
				<td class="label"><?php echo __('Quality'); ?></td>
				<td class="field">
					<input type="number" min="20" max="100" step="10" class="textbox" name="djg_gallery[quality]" value="<?php echo (isset($djg_gallery['quality']))?$djg_gallery['quality']:'80'; ?>"  />
				</td>
				<td class="help"><?php echo __('Set quality of file (20-100)'); ?></strong></td>
			</tr>
			<tr>
				<td class="label"><?php echo __('Crop'); ?></td>
				<td class="field">
					<select id="crop" name="djg_gallery[crop]">
						<option value="0" <?php if( isset($djg_gallery['crop']) and ($djg_gallery['crop']== "0")) echo 'selected="selected"' ?>><?php echo __('No'); ?></option>
						<option value="1" <?php if( isset($djg_gallery['crop']) and ($djg_gallery['crop']== "1")) echo 'selected="selected"' ?>><?php echo __('Yes'); ?></option>
					</select>
				</td>
				<td class="help"></strong></td>
			</tr>
			<tr>
				<td class="label"><?php echo __('Crop position'); ?></td>
				<td class="field">
					<?php if(empty($djg_gallery['cropposition'])) $djg_gallery['cropposition'] = 'C'; ?>
					<input disabled="disabled" type="radio" name="djg_gallery[cropposition]">
					<input type="radio" name="djg_gallery[cropposition]" value="T" <?php if ($djg_gallery['cropposition']=='T') echo 'checked'; ?>>
					<input disabled="disabled" type="radio" name="djg_gallery[cropposition]">					
					<br>
					<input type="radio" name="djg_gallery[cropposition]" value="L" <?php if ($djg_gallery['cropposition']=='L') echo 'checked'; ?>>
					<input type="radio" name="djg_gallery[cropposition]" value="C" <?php if ($djg_gallery['cropposition']=='C') echo 'checked'; ?>>
					<input type="radio" name="djg_gallery[cropposition]" value="R" <?php if ($djg_gallery['cropposition']=='R') echo 'checked'; ?>>						
					<br>	
					<input disabled="disabled" type="radio" name="djg_gallery[cropposition]">
					<input type="radio" name="djg_gallery[cropposition]" value="B" <?php if ($djg_gallery['cropposition']=='B') echo 'checked'; ?>>
					<input disabled="disabled" type="radio" name="djg_gallery[cropposition]">										
				</td>
				<td class="help"><?php echo __('Set orientation of crop'); ?></strong></td>
			</tr>
			<tr>
				<td class="label"><?php echo __('Thumbnail width'); ?></td>
				<td class="field">
					<input type="text" class="textbox"  maxlength="4" name="djg_gallery[width]" value="<?php echo (isset($djg_gallery['width']))?$djg_gallery['width']:''; ?>" />
				</td>
				<td class="help"><?php echo __(''); ?></strong></td>
			</tr>
			<tr>
				<td class="label"><?php echo __('Thumbnail height'); ?></td>
				<td class="field">
					<input type="text" class="textbox"  maxlength="4" name="djg_gallery[height]" value="<?php echo (isset($djg_gallery['height']))?$djg_gallery['height']:''; ?>" />
				</td>
				<td class="help"><?php echo __(''); ?></strong></td>
			</tr>		
			<tr>
				<td class="label"><?php echo __('Watermark'); ?></td>
				<td class="field">
					<?php if(empty($djg_gallery['watermark']['filename'])) $djg_gallery['watermark']['filename'] = '0'; ?>
					<div style="border-bottom: 1px solid #ccc;" >
					<input type="radio" name="djg_gallery[watermark][filename]" value="" <?php if ($djg_gallery['watermark']['filename']=='0') echo 'checked'; ?> > <?php echo __('No watermark'); ?>
					</div>
					<?php foreach($watermarks as $watermark_file):?>
					<div style="border-bottom: 1px solid #ccc;" >
					<input type="radio" name="djg_gallery[watermark][filename]" value="<?php echo basename($watermark_file); ?>" <?php if ($djg_gallery['watermark']['filename']==basename($watermark_file)) echo 'checked'; ?> >
					<img src="<?php echo URL_PUBLIC.'public/djg_gallery/watermarks/'.basename($watermark_file); ?>" height="80" alt="<?php echo basename($watermark_file); ?>" />
					<span style="float: right;"><?php echo basename($watermark_file); ?></span>
					</div>
					<? endforeach; ?>
				</td>
				<td class="help"></strong></td>
			</tr>
			<tr>
				<?php if(empty($djg_gallery['watermark']['watermark_position'])) $djg_gallery['watermark']['watermark_position'] = 'MC'; ?>
				<td class="label"><?php echo __('Watermark alignment'); ?></td>
				<td class="field">
					<input type="radio" name="djg_gallery[watermark][watermark_position]" value="TL" <?php if ($djg_gallery['watermark']['watermark_position']=='TL') echo 'checked'; ?>>
					<input type="radio" name="djg_gallery[watermark][watermark_position]" value="TC" <?php if ($djg_gallery['watermark']['watermark_position']=='TC') echo 'checked'; ?>>
					<input type="radio" name="djg_gallery[watermark][watermark_position]" value="TR" <?php if ($djg_gallery['watermark']['watermark_position']=='TR') echo 'checked'; ?>>						
					<br>
					<input type="radio" name="djg_gallery[watermark][watermark_position]" value="ML" <?php if ($djg_gallery['watermark']['watermark_position']=='ML') echo 'checked'; ?>>
					<input type="radio" name="djg_gallery[watermark][watermark_position]" value="MC" <?php if ($djg_gallery['watermark']['watermark_position']=='MC') echo 'checked'; ?>>
					<input type="radio" name="djg_gallery[watermark][watermark_position]" value="MR" <?php if ($djg_gallery['watermark']['watermark_position']=='MR') echo 'checked'; ?>>						
					<br>	
					<input type="radio" name="djg_gallery[watermark][watermark_position]" value="BL" <?php if ($djg_gallery['watermark']['watermark_position']=='BL') echo 'checked'; ?>>
					<input type="radio" name="djg_gallery[watermark][watermark_position]" value="BC" <?php if ($djg_gallery['watermark']['watermark_position']=='BC') echo 'checked'; ?>>
					<input type="radio" name="djg_gallery[watermark][watermark_position]" value="BR" <?php if ($djg_gallery['watermark']['watermark_position']=='BR') echo 'checked'; ?>>											
				</td>
				<td class="help"><?php echo __('Set position of watermark on picture'); ?></strong></td>
			</tr>
			<tr>
				<?php if(empty($djg_gallery['watermark']['opacity'])) $djg_gallery['watermark']['opacity'] = '90'; ?>
				<td class="label"><?php echo __('Watermark opacity in %'); ?></td>
				<td class="field">
					<input type="number" min="10" max="100" step="10" class="textbox" maxlength="3" name="djg_gallery[watermark][opacity]" value="<?php echo (isset($djg_gallery['watermark']['opacity']))?$djg_gallery['watermark']['opacity']:'100'; ?>"  />
				</td>
				<td class="help"><?php echo __(''); ?></strong></td>
			</tr>
			<tr>
				<?php if(empty($djg_gallery['watermark']['horizontal_margin'])) $djg_gallery['watermark']['horizontal_margin'] = '0'; ?>
				<td class="label"><?php echo __('Watermark horizontal margin'); ?></td>
				<td class="field">
					<input type="number" min="0" max="1000" step="1" class="textbox"  maxlength="3" name="djg_gallery[watermark][horizontal_margin]" value="<?php echo (isset($djg_gallery['watermark']['horizontal_margin']))?$djg_gallery['watermark']['horizontal_margin']:'0'; ?>" />
				</td>
				<td class="help"><?php echo __(''); ?></strong></td>
			</tr>
			<tr>
				<?php if(empty($djg_gallery['watermark']['vertical_margin'])) $djg_gallery['watermark']['vertical_margin'] = '0'; ?>
				<td class="label"><?php echo __('Watermark vertical margin'); ?></td>
				<td class="field">
					<input type="number" min="0" max="1000" step="1" class="textbox"  maxlength="3" name="djg_gallery[watermark][vertical_margin]" value="<?php echo (isset($djg_gallery['watermark']['vertical_margin']))?$djg_gallery['watermark']['vertical_margin']:'0'; ?>" />
				</td>
				<td class="help"><?php echo __(''); ?></strong></td>
			</tr>
		</table>
	<p class="buttons">
        <input class="button" name="commit" type="submit" accesskey="s" value="<?php echo __('Add new thumbnail'); ?>" />
    </p>
</form>
<script type="text/javascript">
// <![CDATA[
	function setConfirmUnload(on, msg) {
		window.onbeforeunload = (on) ? unloadMessage : null;
		return true;
	}
	function unloadMessage() {
		return '<?php echo __('You have modified this page.  If you navigate away from this page without first saving your data, the changes will be lost.'); ?>';
	}
	$(document).ready(function() {
		$('.del_thumbnail').live('click', function() {
			var action = confirm('<?php echo __('Do you want to delete thumbnail with files?'); ?>');
			var b = $(this);
			var id = b.parent().parent().attr('thumbnail_id');
			if(action){
				$.ajax({ 
					type: "GET", 
					data: {'id':id},
					dataType: "json", cache: true,
					url: '<?php echo rtrim(URL_PUBLIC,'/').(USE_MOD_REWRITE ? '/': '/?/'); ?>/djg_gallery/del_thumbnail.php',
					contentType: "application/json; charset=utf-8", 
					beforeSend: function() {b.hide(0);},
					error: function() {showAlert('<?php echo __('ajax error'); ?>','error'); b.show(0);}, 
					success: function(data) {
						if(data.error!=0){}else{b.parent().parent().remove(); showAlert('<?php echo __('Thumbnails deleted.'); ?>','ok'); return false;}
					},
					complete: function() {}
				});
			}
		});
	});
// ]]>
</script>