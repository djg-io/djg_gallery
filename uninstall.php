<?php
if (!defined('IN_CMS')) { exit(); }

if (Plugin::deleteAllSettings('djg_gallery') === false)
{
    Flash::set('error', __('Unable to delete plugin settings.'));
    redirect(get_url('setting'));
}
$PDO = Record::getConnection();
$PDO->exec('DROP TABLE IF EXISTS '.TABLE_PREFIX.'djg_gallery_pics');
$PDO->exec('DROP TABLE IF EXISTS '.TABLE_PREFIX.'djg_gallery_thumbnails');

Flash::set('success', __('Successfully uninstalled plugin.'));

exit();