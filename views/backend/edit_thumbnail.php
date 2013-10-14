<h1><?php echo __('Edit thumbnail'); ?></h1>
<form action="<?php echo get_url('plugin/djg_gallery/edit_thumbnail/'.$at1); ?>" method="post">
		<table class="fieldset" cellpadding="0" cellspacing="0" border="0">
			<tr>
				<td class="label"><?php echo __('Name'); ?></td>
				<td class="field">
					<input type="text" class="textbox"  maxlength="32" name="djg_gallery[thumbname]" value="<?php echo (isset($djg_gallery['thumbname']))?$djg_gallery['thumbname']:''; ?>" />
				</td>
				<td class="help"><?php echo __('Thumbnail name is a prefix for file name, for example: thumb_4JS9S.jpg'); ?></strong></td>
			</tr>
			<tr>
				<td class="label"><?php echo __('Quality'); ?></td>
				<td class="field">
					<input type="number" min="20" max="100" step="5" class="textbox" name="djg_gallery[quality]" value="<?php echo (isset($djg_gallery['quality']))?$djg_gallery['quality']:'80'; ?>"  />
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
					<?php if(empty($djg_gallery['watermark'][0])) $djg_gallery['watermark']['filename'] = '0'; ?>
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
				<?php if(empty($djg_gallery['watermark'][1])) $djg_gallery['watermark'][1] = 'MC'; ?>
				<td class="label"><?php echo __('Watermark alignment'); ?></td>
				<td class="field">
					<input type="radio" name="djg_gallery[watermark][1]" value="TL" <?php if ($djg_gallery['watermark'][1]=='TL') echo 'checked'; ?>>
					<input type="radio" name="djg_gallery[watermark][1]" value="TC" <?php if ($djg_gallery['watermark'][1]=='TC') echo 'checked'; ?>>
					<input type="radio" name="djg_gallery[watermark][1]" value="TR" <?php if ($djg_gallery['watermark'][1]=='TR') echo 'checked'; ?>>						
					<br>
					<input type="radio" name="djg_gallery[watermark][1]" value="ML" <?php if ($djg_gallery['watermark'][1]=='ML') echo 'checked'; ?>>
					<input type="radio" name="djg_gallery[watermark][1]" value="MC" <?php if ($djg_gallery['watermark'][1]=='MC') echo 'checked'; ?>>
					<input type="radio" name="djg_gallery[watermark][1]" value="MR" <?php if ($djg_gallery['watermark'][1]=='MR') echo 'checked'; ?>>						
					<br>	
					<input type="radio" name="djg_gallery[watermark][1]" value="BL" <?php if ($djg_gallery['watermark'][1]=='BL') echo 'checked'; ?>>
					<input type="radio" name="djg_gallery[watermark][1]" value="BC" <?php if ($djg_gallery['watermark'][1]=='BC') echo 'checked'; ?>>
					<input type="radio" name="djg_gallery[watermark][1]" value="BR" <?php if ($djg_gallery['watermark'][1]=='BR') echo 'checked'; ?>>											
				</td>
				<td class="help"><?php echo __('Set position of watermark on picture'); ?></strong></td>
			</tr>
			<tr>
				<?php if(empty($djg_gallery['watermark']['opacity'])) $djg_gallery['watermark'][2] = '90'; ?>
				<td class="label"><?php echo __('Watermark opacity in %'); ?></td>
				<td class="field">
					<input type="number" min="10" max="100" step="5" class="textbox" maxlength="3" name="djg_gallery[watermark][2]" value="<?php echo (isset($djg_gallery['watermark'][2]))?$djg_gallery['watermark'][2]:'100'; ?>"  />
				</td>
				<td class="help"><?php echo __(''); ?></strong></td>
			</tr>
			<tr>
				<?php if(empty($djg_gallery['watermark'][3])) $djg_gallery['watermark'][3] = '0'; ?>
				<td class="label"><?php echo __('Watermark horizontal margin'); ?></td>
				<td class="field">
					<input type="number" min="0" max="1000" step="1" class="textbox"  maxlength="3" name="djg_gallery[watermark][3]" value="<?php echo (isset($djg_gallery['watermark'][3]))?$djg_gallery['watermark'][3]:'0'; ?>" />
				</td>
				<td class="help"><?php echo __(''); ?></strong></td>
			</tr>
			<tr>
				<?php if(empty($djg_gallery['watermark'][4])) $djg_gallery['watermark'][4] = '0'; ?>
				<td class="label"><?php echo __('Watermark vertical margin'); ?></td>
				<td class="field">
					<input type="number" min="0" max="1000" step="1" class="textbox"  maxlength="3" name="djg_gallery[watermark][4]" value="<?php echo (isset($djg_gallery['watermark'][4]))?$djg_gallery['watermark'][4]:'0'; ?>" />
				</td>
				<td class="help"><?php echo __(''); ?></strong></td>
			</tr>
		</table>
	<p class="buttons">
        <input class="button" name="commit" type="submit" accesskey="s" value="<?php echo __('Save changes'); ?>" />
		<?php echo __('or'); ?> <a href="<?php echo get_url('plugin/djg_gallery/thumbnails'); ?>"><?php echo __('Back to thumbnails'); ?></a>
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
// ]]>
</script>