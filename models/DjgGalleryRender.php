<?php

/**
* DjgGalleryRender Class
**/

class Djggalleryrender {
    const AlbumThumbName = "145_";
    const GalleryThumbName = "145_";
	const GalleryLinkClassName = "link_colorbox";
	const ItemsPerPage = 20;
	const DisplayEmptyGallery = true;
	
	private $path 				= "public/djg_gallery/";
	//private $path_tmp			= "public/djg_gallery/tmp/";
	function Djggalleryrender(){
		$this->path = Plugin::getSetting('path', 'djg_gallery');
		//$this->path_tmp = Plugin::getSetting('path', 'djg_gallery').'/tmp';	
		// constructor;
	}
	/**
	* donwnload gallery as zip file
	* $page: object or int(page id)
	*/
	public static function download($page)
	{
		$pageId = (is_object($page)) ? $page->id() : $page;
		$pageSlug =  (is_object($page)) ? $page->slug() : Page::findById($page)->slug();
		echo '<a href="' . URL_PUBLIC . 'djg_gallery/download/' . $pageId . '/' . $pageSlug . '">'.__('Download gallery').'</a>';
	}
	/**
	* carousel
	*/
	public static function carousel($page)
	{
		$pageId = (is_object($page)) ? $page->id() : $page;
		self::dispalyThemeGallery(self::getPicsArray($pageId,"djg_gallery"),"jcarousel-skin-tango","carousel");
	}
	/**
	* gallery
	*/
	public static function gallery($page,$pager=false)
	{
		$pageId = (is_object($page)) ? $page->id() : $page;
		if($pager==true):
			use_helper('Pager');
			$pager = new Pager(array('style' => 'punbb','items_per_page' => self::ItemsPerPage,'total_items' => count(self::getPicsArray($pageId))));
			self::dispalyThemeGallery(self::getPicsArray(array('pageId'=>$pageId,'limit' => $pager->items_per_page, 'offset' => $pager->sql_offset)),"djg_gallery_g");
			echo $pager;
		else:
			self::dispalyThemeGallery(self::getPicsArray($pageId,"djg_gallery"),"djg_gallery_g");
		endif;
		
	}
	/**
	* album
	*/
	public static function album($page,$pager=false)
	{
		$path = "public/djg_gallery/";
		$pageId = (is_object($page)) ? $page->id() : $page;
		foreach(Page::findById($pageId)->children() as $menu):
			if(self::countPics($menu->id())):
				$val = self::getPicByPageId($menu->id());				
				echo "<li>";
				echo '<a href="' . $menu->url() . '">';
				echo '<img src="' . URL_PUBLIC . Djggallery::THUMBS_PATH . self::GalleryThumbName . $val[0]['filename'] . '.' . $val[0]['fileext'] . '" alt="'.$val[0]['title'].'" />';
				echo '<span class="album_title">' . $menu->slug() . ' (' . self::countPics($menu->id()) . ')</span>';
				echo '</a>';
				echo "</li>";

			endif;	
		endforeach;
	}
	/* 
	* method: getPicsArray
	* parametr: items:Array(itemId, limit, offset)
	* itemId (string or array)
	*/
	public static function getPicByPageId($pageId){
		$sql = "SELECT * FROM ".TABLE_PREFIX."djg_gallery_pics WHERE pageId = $pageId ORDER BY sort_order ASC, id DESC LIMIT 1";
		return Djggallery::executeSql($sql);	
	}
	/* 
	* method: getPicsArray
	* parametr: items:Array(itemId, limit, offset)
	* itemId (string or array)
	*/
	public static function getPicsArray($i)
	{
		if(is_array($i)):
			if(!isset($i['pageId'])) exit('no page id');
			$pageId = (is_array($i['pageId'])) ? implode(',',$i['pageId']) : $i['pageId'];
			if( (isset($i['limit'])) && (isset($i['offset'])) ):
				$offset = $i['offset'];
				$limit = $i['limit'];
				$iLimit=" LIMIT $offset,$limit "; 
			else: 
				$iLimit=""; 
			endif;
		else:
			$pageId = $i;
			$iLimit="";
		endif;
		$sql = "SELECT * FROM ".TABLE_PREFIX."djg_gallery_pics WHERE pageId IN ($pageId)";
		$sql .= " ORDER BY sort_order ASC, id DESC $iLimit ";
		return Djggallery::executeSql($sql);
	} //end getPicsArray	
	
	public static function dispalyThemeGallery($files=array(),$className=null,$idName=null)
	{
		$group = time();
		$path = "public/djg_gallery/";
		$id = ($idName==null)?'':'id="'.$idName.'"';
		$class = ($className==null)?'':'class="'.$className.'"';
		echo '<ul '.$id.' '.$class.'>';
		foreach($files as $key=>$val):	
				echo "<li>";
				echo '<a class="'.self::GalleryLinkClassName.'" data-lightbox="{\'group\':\''.$group.'\'}" href="' . URL_PUBLIC . $path . $val['filename'] . '.' . $val['fileext'] . '" title="' . $val['title'] . '">';
				echo '<span></span><img src="' . URL_PUBLIC . $path . 'thumbs/' . self::GalleryThumbName . $val['filename'] . '.' . $val['fileext'] . '" alt="'.$val['title'].'" /></a>';
				echo "</li>";
		endforeach;
		echo '</ul>';
	}
	/* 
	* method: getPicsArray
	* parametr: items:Array(itemId, limit, offset)
	* itemId (string or array)
	*/
	public static function getAlbumsArray($i)
	{
		if(is_array($i)):
			if(!isset($i['pageId'])) exit('no page id');
			$pageId = (is_array($i['pageId'])) ? implode(',',$i['pageId']) : $i['pageId'];
			if( (isset($i['limit'])) && (isset($i['offset'])) ):
				$offset = $i['offset'];
				$limit = $i['limit'];
				$iLimit=" LIMIT $offset,$limit "; 
			else: 
				$iLimit=""; 
			endif;
		else:
			$pageId = $i;
			$iLimit="";
		endif;
		$sql = "SELECT * FROM ".TABLE_PREFIX."djg_gallery_pics WHERE pageId IN ($pageId)";
		$sql .= " ORDER BY sort_order ASC, id DESC $iLimit ";
		return Djggallery::executeSql($sql);
	} //end getPicsArray	
	public static function dispalyThemeAlbums($files=array(),$className=null,$idName=null)
	{
		$path = "public/djg_gallery/";
		$id = ($idName==null)?'':'id="'.$idName.'"';
		$class = ($className==null)?'':'class="'.$className.'"';
		echo '<ul '.$id.' '.$class.'>';
		foreach($files as $key=>$val):	
				echo "<li>";
				echo '<a href="' . URL_PUBLIC . $path . $val['filename'] . '.' . $val['fileext'] . '" title="' . $val['title'] . '">';
				echo '<img src="' . URL_PUBLIC . $path . 'thumbs' . '/thumb_' .$val['filename'] . '.' . $val['fileext'] . '" alt="'.$val['title'].'" /></a>';
				//echo '<img src="' . URL_PUBLIC. 'djg_gallery/image/'.$val['filename'].'" alt="'.$val['title'].'" />';
				echo "</li>";
		endforeach;
		echo '</ul>';
	}

	/** helpers */
	
	
	public static function countPics($pageId){
		$sql = "SELECT id FROM ".TABLE_PREFIX."djg_gallery_pics WHERE pageId = $pageId";
		return count(Djggallery::executeSql($sql));
	}
	
	function newestPic($pageId){
		$sql = "SELECT * FROM ".TABLE_PREFIX."djg_gallery_pics WHERE pageId = $pageId LIMIT 1";
		return Djggallery::executeSql($sql);
	}
	

	
	
	
	
	
	
	
	
} // end class