<?php
	$settings = Plugin::getAllSettings('djg_gallery');
	$path = URI_PUBLIC.$settings['path'];
	$pageId = $page_id;
	$sql_files = "SELECT * FROM ".TABLE_PREFIX."djg_gallery_pics WHERE pageId='$page_id' ORDER BY sort_order ASC, id DESC";
	$files = Djggallery::executeSql($sql_files);
?>
<div id="djg_gallery_tabcontents" class="page">
	<div id="djg_gallery_editpage">
	<table style="width:100%;">
		<tr>
			<td width="60%">
				<span title="<?php echo __('Switch view'); ?>" class="switchView"></span>
				<?php echo '<a class="del_all_files" href="#"><img src="'.URL_PUBLIC . 'wolf/plugins/djg_gallery/images/32_del_picture.png" title="'.__('Click to delete all files from this album.').'" alt="'.__('Click to delete all files from this album.').'" /></a>'; ?>
				<?php echo '<a class="upload" href="#" pageId="'.$pageId.'"><img src="'.URL_PUBLIC . 'wolf/plugins/djg_gallery/images/32_add_picture.png" title="'.__('Click to upload files.').'" alt="'.__('Click to upload files.').'" /></a>'; ?>
				<span class="file_counter"><?php echo __('please wait while files are processed ... remained: '); ?> <span>0s</span></span>
				<span class="cut_past_pics"></span>
			</td>
			<td width="40%">
				<label><?php echo __('Title'); ?> </label><input class="djg_gallery_global_title" />
				<img class="global_title_save_button" src="<?php echo ICONS_URI;?>action-approve-16.png" title="<?php echo __('Title'); ?>" alt="<?php echo __('Title'); ?>" />
				<label><?php echo __('Description'); ?> </label><input  class="djg_gallery_global_description"  />
				<img class="global_description_save_button" src="<?php echo ICONS_URI;?>action-approve-16.png" title="<?php echo __('Description'); ?>" alt="<?php echo __('Description'); ?>" />
			</td>
		</tr>
		<tr>
			<td colspan="2">		
				<ul id="djg_gallery_files" class="view">
				<?php if($files): foreach($files as $key=>$val) {	?>
					<?php echo '<li id="filesArray_'.$val["id"].'" file_id="'.$val["id"].'" class="pic shadow">'; ?>
						<div class="icons">
							<span class="del_pic" alt="Del" title="Del"></span>
							<a class="link_colorbox" title="<?php echo $val['title']; ?>" href="<?php echo $path.'/'.$val['filename'].'.'.$val['fileext']; ?>"><span title="<?php echo __('Zoom'); ?>" class="zoom"></span></a>
						</div>
						<div class="img">
							<img src="<?php echo $path.'thumbs'.'/thumb_'.$val['filename'].'.'.$val['fileext'];?>" alt="thumb" />
						</div>
						<div class="desc">
							<p class="djg_gallery_pic_title"><?php echo $val['title']; ?></p>
							<p class="djg_gallery_pic_description"><?php echo $val['description']; ?></p>
						</div>
						<div style="clear: left;"></div>
					</li>
					<?php
					}
				endif;
				?>
				</ul>
			</td>			
		</tr>
	</table>
	<div class="upload_window">
		<div id="custom-demo" class="demo"> 
			<div id="status-message"><?php echo __('Select some files to upload:'); ?></div>
			<div id="custom-queue"></div>
			<span><input type="file" name="uploadify" id="uploadify" /></span>
			<span><a href="javascript:$('#status-message').text('<?php echo __('select some files to upload:'); ?>'),$('#uploadify').uploadifyClearQueue()">
			<img src="<?php echo URL_PUBLIC . 'wolf/plugins/djg_gallery/images/32_stop.png'; ?>" alt="<?php echo __('Cancel all uploads.'); ?>" title="<?php echo __('Cancel all uploads.'); ?>" /></a></span>
			<span><a href="javascript:$('#uploadify').uploadifyUpload()">
			<img src="<?php echo URL_PUBLIC . 'wolf/plugins/djg_gallery/images/32_start.png'; ?>" alt="<?php echo __('Start upload files.'); ?>" title="<?php echo __('Start upload files.'); ?>"/></a></span>
		</div>
	</div>	
	</div>	
</div>

<script type="text/javascript">
// <![CDATA[
var picsArray = new Array();
var value = null;
var inProgressResize = 0;
var inProgressUpload = 0;
function sendNames() {
	inProgressResize = 1;
	value = picsArray.shift();
	$.ajax({ 
		type: "GET", 
		data: {'filename':value.name,'ext':value.ext,'pageId':value.pageId},
		dataType: "json", 
		cache: true,
		url: '<?php echo rtrim(URL_PUBLIC,'/').(USE_MOD_REWRITE ? '/': '/?/'); ?>/djg_gallery/after_upload.php?ssid=<?php echo time(); ?>',
		contentType: "application/json; charset=utf-8", 
		beforeSend: function() {$('.file_counter').show();$('.file_counter span').html(picsArray.length+1);},
		error: function(request, status, error) { alert('error '+request.responseText); $('.file_counter').hide();}, 
		success: function(data) {if(data.error!=0){alert('ajax error');$('.file_counter').hide();}},
		complete: function() {
			inProgressResize = 0;
			$('.file_counter span').html(picsArray.length);
			if (picsArray.length > 0) {sendNames();
			} else {
				$('.file_counter').hide(); 
				if(inProgressUpload == 0){location.reload();}
			}
		}
		
	});
	return true;
};

function cutPastPics() {
	$.cookie("djg_gallery_page_id", 20);
	if(typeof($.cookie("djg_gallery_page_id"))!='undefined'){
		$('.cut_past_pics').html('<?php echo $pageId; ?>');
	}else{
		$('.cut_past_pics').html('cut');
	};
	$('.cut_past_pics').click(function(){
		$('.cut_past_pics').html('20');
		cutPastPics();
	});
}

$(document).ready(function() {

	var counter = 0;
	
	/** cut and past */
	
	cutPastPics();
	
	/** upload dialog open */

	$(".upload").click(function() {$( ".upload_window" ).dialog( "open" ); return false;});

	/** upload dialog */

	$(".upload_window").dialog({ autoOpen: false, title: "<?php echo __('Upload files'); ?>", width: 440});

	/** switch view */
	
	$(".switchView").click(function() {
		if($('#djg_gallery_files').hasClass('view')){
			$('#djg_gallery_files').removeClass('view').addClass('grid');
			$('#djg_gallery_editpage').find('.switchView').css('background-position', '0 32px');
		}else{
			$('#djg_gallery_files').removeClass('grid').addClass('view');
			$('#djg_gallery_editpage').find('.switchView').css('background-position', '0 0');
		}
		return false;
	});
	
	/** pic hover */
	
	$(".pic").hover(function(){
		$(this).find('.icons').show();
	},function(){
	   $(this).find('.icons').hide();
	});
	
	/** colorbox */

	$(".link_colorbox").colorbox({rel:'link_colorbox', transition:"none"});

	/** ajax edit title */
	
	$(".djg_gallery_pic_title").editable("<?php echo rtrim(URL_PUBLIC,'/').(USE_MOD_REWRITE ? '/': '/?/'); ?>djg_gallery/ajax_jeditable.php", { 
		indicator : '<img src="<?php echo rtrim(URL_PUBLIC,'/').(USE_MOD_REWRITE ? '/': '/?/'); ?>wolf/admin/images/spinner.gif">',
		style  : "inherit",
		submitdata : function() {
			var file = $(this).parent().parent();
			return {id : file.attr('file_id'), names : 'title'};
		}
	});
	
	/** ajax edit description */
	
	$(".djg_gallery_pic_description").editable("<?php echo rtrim(URL_PUBLIC,'/').(USE_MOD_REWRITE ? '/': '/?/'); ?>djg_gallery/ajax_jeditable.php", { 
		indicator : '<img src="<?php echo rtrim(URL_PUBLIC,'/').(USE_MOD_REWRITE ? '/': '/?/'); ?>wolf/admin/images/spinner.gif">',
		style  : "inherit",
		submitdata : function() {
			var file = $(this).parent().parent();
			return {id : file.attr('file_id'), names : 'description'};
		}
	});
	
	/** ajax edit title global */
	
	$(".global_title_save_button").click(function() {
		var action = confirm('<?php echo __('Do you want to change title of all files?'); ?>');
		if(action){
			var new_value = $(".djg_gallery_global_title").val();
			$.post('<?php echo rtrim(URL_PUBLIC,'/').(USE_MOD_REWRITE ? '/': '/?/'); ?>/djg_gallery/ajax_global.php', {'value':new_value,'names':'title','pageId':'<?php echo $pageId; ?>'}, function(data) {
				//alert("Data Loaded: " + data);
				$('#djg_gallery_files').find('.djg_gallery_pic_title').html(new_value);
			});
		};
		return false;
	});
	
	/** ajax edit description global */
	
	$(".global_description_save_button").click(function() {
		var action = confirm('<?php echo __('Do you want to change description of all files?'); ?>');
		if(action){
			var new_value = $(".djg_gallery_global_description").val();
			$.post('<?php echo rtrim(URL_PUBLIC,'/').(USE_MOD_REWRITE ? '/': '/?/'); ?>/djg_gallery/ajax_global.php', {'value':new_value,'names':'description ','pageId':'<?php echo $pageId; ?>'}, function(data) {
				//alert("Data Loaded: " + data);
				$('#djg_gallery_files').find('.djg_gallery_pic_description').html(new_value);
			});
		};
		return false;
	});
	
	/** ajax sort files */
	
	$("#djg_gallery_files").sortable({ 
		handle : 'img',
		update : function () { 
		var order = $(this).sortable("serialize") + '&action=updateRecordsListings';
			$.post("<?php echo URL_PUBLIC.'djg_gallery/sort_files.php';?>", order, function(theResponse){
				$("#ajax_debug").html("<?php echo __('Updated file list'); ?>"+theResponse);
			});
		} 
	}); 
	$("#djg_gallery_files").disableSelection();

	/** ajax del file */
	
	$(".del_pic").click(function(){
		var action = confirm('<?php echo __('Do you want to delete the item?'); ?>');
		if(action){
			var file = $(this).parent().parent();
			$.ajax({ 
					type: "GET", 
					data: {'file_id':file.attr('file_id')},
					dataType: "json", cache: true,
					url: '<?php echo rtrim(URL_PUBLIC,'/').(USE_MOD_REWRITE ? '/': '/?/'); ?>/djg_gallery/ajax_del_file.php',
					contentType: "application/json; charset=utf-8", 
					beforeSend: function() {},
					error: function() {alert('<?php echo __('ajax error'); ?>');}, 
					success: function(data) {
						if(data.error!=0)
						{
							file.animate({ backgroundColor: "#fbc7c7" }, "fast");
						}else{
							file.hide();
						}
					},
					complete: function() {}
				});
		};
		return false;
	});

	/** ajax del all files */

	$(".del_all_files").click(function() {
		var action = confirm('<?php echo __('Do you want to delete all files?'); ?>');
		if(action){
			$.ajax({ 
				type: "GET", 
				data: {'pageId':<?php echo $pageId; ?>},
				dataType: "json", cache: true,
				url: '<?php echo rtrim(URL_PUBLIC,'/').(USE_MOD_REWRITE ? '/': '/?/'); ?>/djg_gallery/del_all_files.php',
				contentType: "application/json; charset=utf-8", 
				beforeSend: function() {},
				//error: function() {alert('<?php echo __('ajax error'); ?>');},
				error: function(request, status, error) { alert('error '+request.responseText);}, 				
				success: function(data) {$('#djg_gallery_files').find('li').remove();},
				complete: function() {}
			});	
		};
	});
	
	/** uploadify */
	
	$("#uploadify").uploadify({
		'uploader': '<?php echo PLUGINS_URI; ?>djg_gallery/images/swf/uploadify.allglyphs.swf',
		'cancelImg': '<?php echo PLUGINS_URI; ?>djg_gallery/images/icons/cancel.png',
		'script': '<?php echo rtrim(URL_PUBLIC,'/').(USE_MOD_REWRITE ? '/': '/?/'); ?>djg_gallery/uploadify_upload.php',
		'folder': '<?php echo rtrim(URL_PUBLIC,'/').(USE_MOD_REWRITE ? '/': '/?/'); ?>djg_gallery/tmp/',
		'fileDesc': '<?php echo __('image files'); ?>',
		'fileExt': '<?php echo Plugin::getSetting('img_ext', 'djg_gallery'); ?>',
		'buttonText': '<?php echo __('Select files'); ?>',
		'queueID' : 'custom-queue',
		'auto' : false,
		'sizeLimit': 1024*1000*<?php echo Plugin::getSetting('img_max_size', 'djg_gallery'); ?>,
		'multi' : true,
		'buttonImage' : '<?php echo PLUGINS_URI; ?>djg_gallery/images/icons/cancel.png',
		'removeCompleted' : true,
		'onSelectOnce'  : function(event,data) {
			$('#uploadify').uploadifySettings("scriptData", {'pageId': <?php echo $pageId;?>});
			$('#status-message').text(data.filesSelected + ' <?php echo __('files have been added to the queue.'); ?>');
		},
		'onAllComplete'  : function(event,data) {
			$('#status-message').text(data.filesUploaded + ' <?php echo __('files uploaded, error:'); ?> ' + data.errors);
			
			$('#uploadify').uploadifyClearQueue();
			if(picsArray.length > 0){
			$('#status-message').text('s');
				$('.upload_window' ).dialog('close');
				$('.upload').hide(1000);
				if(inProgressResize == 0){sendNames();}
			}
			inProgressUpload = 0;
			return false;
		},
		'onOpen' : function(event,ID,fileObj) {
			inProgressUpload = 1;
			$('#status-message').text('<?php echo __('The upload is beginning for'); ?>' + fileObj.name);
		},
		'onComplete' : function(event, queueID, fileObj, response, data) {
			if($.parseJSON(response).error!=false){
				alert($.parseJSON(response).error);
			}else{
				picsArray.push($.parseJSON(response));
				if(inProgressResize == 0){sendNames();}
			}			
			return false;
		}
	});
});
// ]]>
</script>