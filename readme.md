djg_gallery
===========

Advanced gallery for WolfCMS.
version: 0.1

== FUNCTIONALITY ==

- page as gallery
- multiupload
- drag drop sortable files
- tumbnails (resize, crop, watermarks) 
- display albums, gallery, carousel
- download gallery as zip file

== HOW TO USE ==

1. Copy files to the wolf/plugins/djg_gallery
2. Insert in layout after echo Page::content();

&lt;?php
/** djg_gallery_fronted */
if (Plugin::isEnabled('djg_gallery')):
if ($this->djg_gallery == Djggallery::ALBUM) DjgGalleryRender::album($this);
if ($this->djg_gallery == Djggallery::GALLERY) DjgGalleryRender::gallery($this);
if ($this->djg_gallery == Djggallery::CAROUSEL) DjgGalleryRender::carousel($this);
if (($this->djg_gallery == Djggallery::GALLERY) && ($this->djg_gallery_download_allow == 1) ) DjgGalleryRender::download($this);
endif;
?&gt;

== ICON USED ==

Eightyshades by Victor Erixon: http://www.iconfinder.com/search/?q=iconset%3Aeightyshades
Bremen by pc.de: http://www.iconfinder.com/icondetails/59399/32/project_icon (modified by kreacjawww.pl)

== REQUIRES ==

PHP: BC Math
PHP GD lib
PHP 5.3.x