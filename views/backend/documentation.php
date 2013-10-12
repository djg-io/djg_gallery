<h1><?php echo __('Documentation'); ?></h1>
<?php
	$description = file_get_contents(PLUGINS_ROOT.DS.'djg_gallery'.DS.'readme.md'); 
	$description = preg_replace("/\r\n|\r|\n/",'<br/>',$description);
	echo '<p>'.$description.'</p>';
/**
mkdir(CMS_ROOT.DS.'public'.DS.'djg_gallery',0777,true);
    mkdir(CMS_ROOT.DS.'public'.DS.'djg_gallery'.DS.'cache',0777,true);
	mkdir(CMS_ROOT.DS.'public'.DS.'djg_gallery'.DS.'thumbs',0777,true);
	mkdir(CMS_ROOT.DS.'public'.DS.'djg_gallery'.DS.'watermarks',0777,true);	
$files = glob(CMS_ROOT.DS.'public'.DS.'djg_gallery'.DS.'thumbs'.DS.'big2_*');
print_r($files);
array_walk($files, function ($file) {
unlink($file);
});
*/
?>