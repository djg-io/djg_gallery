<?php
Plugin::setInfos(array(
	'id'			=>	'djg_gallery',
	'title'			=>	__('[djg] Gallery'),
	'description'   =>	__('Galleria and portfolio in one.'),
	'license'		=>	'MIT',
	'author'		=>	'Michał Uchnast',
	'website'		=>	'http://www.kreacjawww.pl/',
	'version'		=>	'0.1',
	'require_wolf_version' => '0.7.3',
    'type'			=>	'both'
));
Plugin::addController('djg_gallery', __('[djg] Gallery'), 'file_manager_view', true);
Plugin::addJavascript('djg_gallery', 'js/swfobject.js');
Plugin::addJavascript('djg_gallery', 'js/jquery.uploadify.v2.1.4.min.js');
Plugin::addJavascript('djg_gallery', 'js/jquery.jeditable.js');
Plugin::addJavascript('djg_gallery', 'js/jquery.cookie.js');
Plugin::addJavascript('djg_gallery', 'assets/colorbox/jquery.colorbox-min.js');

Dispatcher::addRoute(array(
	/* download gallery */
	'/djg_gallery/download/:num/:any' => '/plugin/djg_gallery/download/$1/$2', // pageId, file name (slug)
	/* thumbphp on fly*/
	'/djg_gallery/image/:any' => '/plugin/djg_gallery/thumbphp/$1',
	/* dispaly on fly */
	//'/djg_gallery/image/:any/:any/:any' => '/plugin/djg_gallery/display_on_fly/$1/$2/$3', // img hash, thumb hash, custom filename.jpg
	/* backend */
	'/djg_gallery/ajax_jeditable.php' => '/plugin/djg_gallery/ajax_jeditable', //ajax
	'/djg_gallery/ajax_global.php' => '/plugin/djg_gallery/ajax_global', //ajax
	'/djg_gallery/del_thumbnail.php' => '/plugin/djg_gallery/ajax_del_thumbnail', //ajax
	'/djg_gallery/ajax_del_file.php' => '/plugin/djg_gallery/ajax_del_file', //ajax
	'/djg_gallery/del_all_files.php' => '/plugin/djg_gallery/ajax_del_all_files', //ajax
	'/djg_gallery/after_upload.php' => '/plugin/djg_gallery/ajax_after_upload', //ajax
	'/djg_gallery/uploadify_upload.php' => '/plugin/djg_gallery/ajax_uploadify_upload', //ajax
	'/djg_gallery/sort_items.php' => '/plugin/djg_gallery/ajax_sort_items', //ajax
	'/djg_gallery/sort_files.php' => '/plugin/djg_gallery/ajax_sort_files', //ajax
	'/djg_gallery/uploadify_check.php'    => '/plugin/djg_gallery/uploadify_check' //ajax
));
include_once('models'.DS.'Djggallery.php');
include_once('models'.DS.'DjgGalleryRender.php');
include_once('lib'.DS.'phpthumb.class.php');
include_once('lib'.DS.'class.upload.php');
include_once('lib'.DS.'zip.class.php');


$tPage = TABLE_PREFIX.'page';
$tItems = TABLE_PREFIX.'djggallery_items';
$tPics = TABLE_PREFIX.'djggallery_pics';

Observer::observe('view_page_edit_plugins', 'djg_gallery_checkbox');
Observer::observe('view_page_edit_plugins', 'djg_gallery_download_allow_checkbox');
Observer::observe('page_delete', 'djg_gallery_delete_files');
Observer::observe('view_page_edit_tab_links', 'DjgGalleryController::Callback_view_page_edit_tab_links');
Observer::observe('view_page_edit_tabs', 'DjgGalleryController::Callback_view_page_edit_tabs');

function djg_gallery_checkbox(&$page)
{
    echo '<p><label for="djg_gallery_checkbox">';?>
	<img src="<?php echo URL_PUBLIC . 'wolf/plugins/djg_gallery/images/16_gal_page.png'; ?>" alt="<?php echo __('Gallery option'); ?>" title="<?php echo __('Gallery option'); ?>" />
	<?php
	echo '</label><select id="djg_gallery_checkbox" name="page[djg_gallery]">';
	echo '<option value="'.Djggallery::NONE.'"'.($page->djg_gallery == Djggallery::NONE ? ' selected="selected"': '').'>&#8212; '.__('None').' &#8212;</option>';
    echo '<option value="'.Djggallery::ALBUM.'"'.($page->djg_gallery == Djggallery::ALBUM ? ' selected="selected"': '').'>'.__('Album').'</option>';
    echo '<option value="'.Djggallery::GALLERY.'"'.($page->djg_gallery == Djggallery::GALLERY ? ' selected="selected"': '').'>'.__('Gallery').'</option>';
	echo '<option value="'.Djggallery::CAROUSEL.'"'.($page->djg_gallery == Djggallery::CAROUSEL ? ' selected="selected"': '').'>'.__('Carousel').'</option>';
    echo '</select></p>';
}

function djg_gallery_download_allow_checkbox(&$page)
{
    echo '<p><label for="djg_gallery_download_allow_checkbox">';?>
	<img src="<?php echo URL_PUBLIC . 'wolf/plugins/djg_gallery/images/16_gal_page.png'; ?>" alt="<?php echo __('djg_galley'); ?>" title="<?php echo __('djg_galley'); ?>" />
	<?php
	echo '</label><select id="djg_gallery_download_allow_checkbox" name="page[djg_gallery_download_allow]">';
	echo '<option value="0"'.($page->djg_gallery_download_allow == '0' ? ' selected="selected"': '').'>'.__('No').'</option>';
    echo '<option value="1"'.($page->djg_gallery_download_allow == '1' ? ' selected="selected"': '').'>'.__('Yes').'</option>';
    echo '</select> [' . $page->djg_gallery_download_counter . ']</p>';
}

function djg_gallery_delete_files($page)
{
	DjgGalleryController::after_del_page($page->id());
}
?>