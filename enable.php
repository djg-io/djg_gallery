<?php
	/** Security measure */
	if (!defined('IN_CMS')) { exit(); }
	
	/** create direcotrys */
	if (!file_exists(CMS_ROOT.DS.'public'.DS.'djg_gallery')) mkdir(CMS_ROOT.DS.'public'.DS.'djg_gallery',0777,true);  
	if (!file_exists(CMS_ROOT.DS.'public'.DS.'djg_gallery'.DS.'cache')) mkdir(CMS_ROOT.DS.'public'.DS.'djg_gallery'.DS.'cache',0777,true);      
	if (!file_exists(CMS_ROOT.DS.'public'.DS.'djg_gallery'.DS.'thumbs')) mkdir(CMS_ROOT.DS.'public'.DS.'djg_gallery'.DS.'thumbs',0777,true); 
	if (!file_exists(CMS_ROOT.DS.'public'.DS.'djg_gallery'.DS.'watermarks')) mkdir(CMS_ROOT.DS.'public'.DS.'djg_gallery'.DS.'watermarks',0777,true); 

	/** sql guery */
	$PDO = Record::getConnection();
	$settings = array(
		'ver' => '0.1',
		'debug' => '1',
		'confirm_del_file' => '1',
		'path' => 'public/djg_gallery/', 
		'img_ext' => '*.jpg;*.jpeg;*.gif;*.png',
		'img_max_size' => '5',
		'resize_org_x' => '1200',
		'resize_org_y' => '0'
	);
	Plugin::setAllSettings($settings, 'djg_gallery');

	/** create tables */
	$createPicsTable = "
	CREATE TABLE IF NOT EXISTS `".TABLE_PREFIX."djg_gallery_pics` (
		`id` int(10) NOT NULL AUTO_INCREMENT,
		`sort_order` smallint(4) unsigned NOT NULL,
		`pageId` int(10) NOT NULL,
		`filename` varchar(8) COLLATE utf8_unicode_ci NOT NULL,
		`filehash` varchar(8) COLLATE utf8_unicode_ci NOT NULL,
		`fileext` varchar(4) COLLATE utf8_unicode_ci NOT NULL,
		`title` text COLLATE utf8_unicode_ci NOT NULL,
		`description` text COLLATE utf8_unicode_ci NOT NULL,
		PRIMARY KEY (`id`)
	) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
    $stmt2 = $PDO->prepare($createPicsTable);
    $stmt2->execute();
	$createThumbnailTable = "		
	CREATE TABLE IF NOT EXISTS `".TABLE_PREFIX."djg_gallery_thumbnails` (
	`id` INT(10) NOT NULL AUTO_INCREMENT,
	`thumbname` varchar(32) NOT NULL ,
	`thumbhash` varchar(8) COLLATE utf8_unicode_ci NOT NULL,
	`quality` INT(3) NOT NULL ,
	`crop` TINYINT(1) NOT NULL ,
	`cropposition` varchar(2) NOT NULL DEFAULT 'C',
	`width` INT(4) NOT NULL ,
	`height` INT(4) NOT NULL ,
	`watermark` varchar(256),
	PRIMARY KEY (`id`)
	) ENGINE = MYISAM CHARACTER SET utf8 COLLATE utf8_unicode_ci;";
    $stmt3 = $PDO->prepare($createThumbnailTable);
    $stmt3->execute();
	
	/** defult data */
	$PDO->exec("ALTER TABLE ".TABLE_PREFIX."page ADD djg_gallery integer NOT NULL DEFAULT 0");
	$PDO->exec("ALTER TABLE ".TABLE_PREFIX."page ADD djg_gallery_download_allow integer NOT NULL DEFAULT 0");
	$PDO->exec("ALTER TABLE ".TABLE_PREFIX."page ADD djg_gallery_download_counter integer NOT NULL DEFAULT 0");
	$PDO->exec("INSERT INTO ".TABLE_PREFIX."djg_gallery_thumbnails (id, thumbname, thumbhash, quality, crop, cropposition, width, height) VALUES ('1', 'thumb_', '".substr(str_shuffle(str_repeat("0123456789ABCDEFGHIJKLMNOPRSTUWXYZabcdefghijklmnopqrstuvwxyz", 8)), 0, 8)."', '60', '1', 'C', '80', '80')");
	$PDO->exec("INSERT INTO ".TABLE_PREFIX."djg_gallery_thumbnails (id, thumbname, thumbhash, quality, crop, cropposition, width, height) VALUES ('2', '145_', '".substr(str_shuffle(str_repeat("0123456789ABCDEFGHIJKLMNOPRSTUWXYZabcdefghijklmnopqrstuvwxyz", 8)), 0, 8)."', '85', '1', 'C', '145', '145')");

	/** result */
	Flash::set('success', __('Successfully installed plugin.'));	
exit();