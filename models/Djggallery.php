<?php

/**
* Djggallery Class
* 
* @license		MIT
* @author		Michał Uchnast
* @link			http://www.kreacjawww.pl
* @email		uchnast.michal@gmail.com
* 
* @file			DjggalleryClass.php
* @version		1.0
* @date			11/09/2010
* 
* Copyright (c) 2010
*/

class Djggallery {
	
    const NONE = 0;
    const ALBUM = 1;
    const GALLERY = 2;
	const CAROUSEL = 3;
	
	const PATH = 'public/djg_gallery/';
	const THUMBS_PATH = 'public/djg_gallery/thumbs/';
	
	private $pattern = "##";
	
	private $listhidden = false; //list of hidden category  
	private $root = 0;
	
	function Djggallery(){
		// constructor;
	}
	public static function felfTest($page)
	{
		use_helper('Pagination');
		echo $page->id();
		echo 'selfTest';
	}
	public static function executeSql($sql)
	{
		$PDO = Record::getConnection();
		$stmt = $PDO->prepare($sql);
		$stmt->execute();
		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}
	public static function translateTitle($parent)
	{
		if($_SESSION['lang']) $lang=$_SESSION['lang']; else $lang="pl";
		if ($parent->hasContent('titles')):
			$titles2 = $parent->content('titles');
			$res = preg_match('/'.$_SESSION['lang'].':\s*(.*)/i', $titles2, $results2);
			if($res == 1):
				$title2 = trim($results2[1]);
			else: 
				$title2 = $parent->title();
			endif;
		else:
			$title2 = $parent->title();
		endif;
		return $title2;
	}
	public static function translateContent($content)
	{
		return $content;
	}
	
	public static function menuTree($parent,$level=999,$count=999,$ids=array(),$noContentLink=false)
	{
	$i=1;
	$translate = false;
    $out = '';
    $childs = $parent->children();
    if (count($childs) > 0):
		if(($childs[0]->level()) <= $level):
			if( ($childs[0]->level()!=1) ) $out = '<ul>';
			foreach ($childs as $child)
					if(!in_array($child->parent()->id(), $ids)):
						if($childs[0]->level()==1):
							$span = '<span>00'.$i.'</span>';$i++;
						else:
							$span = '';
						endif;
						$out .= '<li>'.$child->link($child->title().$span, (url_start_with($child->url) ? ' class="current"': null)).self::menuTree($child,$level,$count,$ids,$noContentLink).'</li>';
					endif;
			if(($childs[0]->level()) !=1 )$out .= '</ul>';
		endif;
    endif;
	$out = str_replace("<ul></ul>", "", $out);
    return $out;
	}
	/**
	 * Translates a number to a short alhanumeric version
	 *
	 * Translated any number up to 9007199254740992
	 * to a shorter version in letters e.g.:
	 * 9007199254740989 --> PpQXn7COf
	 *
	 * specifiying the second argument true, it will
	 * translate back e.g.:
	 * PpQXn7COf --> 9007199254740989
	 *
	 * this function is based on any2dec && dec2any by
	 * fragmer[at]mail[dot]ru
	 * see: http://nl3.php.net/manual/en/function.base-convert.php#52450
	 *
	 * If you want the alphaID to be at least 3 letter long, use the
	 * $pad_up = 3 argument
	 *
	 * In most cases this is better than totally random ID generators
	 * because this can easily avoid duplicate ID's.
	 * For example if you correlate the alpha ID to an auto incrementing ID
	 * in your database, you're done.
	 *
	 * The reverse is done because it makes it slightly more cryptic,
	 * but it also makes it easier to spread lots of IDs in different
	 * directories on your filesystem. Example:
	 * $part1 = substr($alpha_id,0,1);
	 * $part2 = substr($alpha_id,1,1);
	 * $part3 = substr($alpha_id,2,strlen($alpha_id));
	 * $destindir = "/".$part1."/".$part2."/".$part3;
	 * // by reversing, directories are more evenly spread out. The
	 * // first 26 directories already occupy 26 main levels
	 *
	 * more info on limitation:
	 * - http://blade.nagaokaut.ac.jp/cgi-bin/scat.rb/ruby/ruby-talk/165372
	 *
	 * if you really need this for bigger numbers you probably have to look
	 * at things like: http://theserverpages.com/php/manual/en/ref.bc.php
	 * or: http://theserverpages.com/php/manual/en/ref.gmp.php
	 * but I haven't really dugg into this. If you have more info on those
	 * matters feel free to leave a comment.
	 *
	 * @author  Kevin van Zonneveld <kevin@vanzonneveld.net>
	 * @author  Simon Franz
	 * @author  Deadfish
	 * @copyright 2008 Kevin van Zonneveld (http://kevin.vanzonneveld.net)
	 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD Licence
	 * @version   SVN: Release: $Id: alphaID.inc.php 344 2009-06-10 17:43:59Z kevin $
	 * @link    http://kevin.vanzonneveld.net/
	 *
	 * @param mixed   $in    String or long input to translate
	 * @param boolean $to_num  Reverses translation when true
	 * @param mixed   $pad_up  Number or boolean padds the result up to a specified length
	 * @param string  $passKey Supplying a password makes it harder to calculate the original ID
	 *
	 * @return mixed string or long
	 * !	require PHP: BC Math
	 */
	public static function alphaID($in, $to_num = false, $pad_up = false, $passKey = null)
	{
	  $index = "abcdefghijklmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";
	  if ($passKey !== null) {
		// Although this function's purpose is to just make the
		// ID short - and not so much secure,
		// with this patch by Simon Franz (http://blog.snaky.org/)
		// you can optionally supply a password to make it harder
		// to calculate the corresponding numeric ID
		for ($n = 0; $n<strlen($index); $n++) {
		  $i[] = substr( $index,$n ,1);
		}
		$passhash = hash('sha256',$passKey);
		$passhash = (strlen($passhash) < strlen($index))
		  ? hash('sha512',$passKey)
		  : $passhash;
		for ($n=0; $n < strlen($index); $n++) {
		  $p[] =  substr($passhash, $n ,1);
		}
		array_multisort($p,  SORT_DESC, $i);
		$index = implode($i);
	  }
	  $base  = strlen($index);
	 
	  if ($to_num) {
		// Digital number  <<--  alphabet letter code
		$in  = strrev($in);
		$out = 0;
		$len = strlen($in) - 1;
		for ($t = 0; $t <= $len; $t++) {
		  $bcpow = bcpow($base, $len - $t);
		  $out   = $out + strpos($index, substr($in, $t, 1)) * $bcpow;
		} 
		if (is_numeric($pad_up)) {
		  $pad_up--;
		  if ($pad_up > 0) {
			$out -= pow($base, $pad_up);
		  }
		}
		$out = sprintf('%F', $out);
		$out = substr($out, 0, strpos($out, '.'));
	  } else {
		// Digital number  -->>  alphabet letter code
		if (is_numeric($pad_up)) {
		  $pad_up--;
		  if ($pad_up > 0) {
			$in += pow($base, $pad_up);
		  }
		}
		$out = "";	
		for ($t = floor(log($in, $base)); $t >= 0; $t--) {	
		  $bcp = bcpow($base, $t);
		  $a   = floor($in / $bcp) % $base;
		  $out = $out . substr($index, $a, 1);
		  $in  = $in - ($a * $bcp);
		  
		}
		$out = strrev($out); // reverse
	  }
	 
	  return $out;
	} // end base_encode()
	/*
	* uniqeId
	* method return uniqe string (6 chars)
	* parametrs: none;
	*/
	public static function uniqeId()
	{
	$u='';$i=0;
	do{
		usleep(5);
		$t = microtime(true);
		$t = (int)substr($t,0,strpos($t,'.')).substr($t,strpos($t,'.')+1,strlen($t));
		$u = (string)self::alphaID($t);
		$i++;
		//if ($i>10): return false; exit(); endif;
	} while (strlen($u) == 7);
	return $u;
	} 		

	/*
	BACKEND
	*/

	public static function getPages()
	{
		$query = "SELECT id,title,slug,parent_id FROM " . TABLE_PREFIX . "page WHERE 1=0 ";
		if(Plugin::getSetting('display_gallery_pages', 'djg_gallery')==1) $query.= " OR djg_gallery='1' "; 
		if(Plugin::getSetting('display_published_pages', 'djg_gallery')==1) $query.= " OR status_id='100' "; // published
		if(Plugin::getSetting('display_hidden_pages', 'djg_gallery')==1) $query.= " OR status_id='101' ";  // hidden
		if(Plugin::getSetting('display_preview_pages', 'djg_gallery')==1) $query.= " OR status_id='10' ";  // preview
		$query.= " ORDER BY parent_id ASC";
		return self::executeSql($query);
	}

	public static function addPic($pageId,$fileName,$fileExt) 
	{
		// step1 get album description
		$sql = "SELECT id,title FROM ".TABLE_PREFIX."page WHERE id='$pageId' LIMIT 1";
		$item = self::executeSql($sql);
		// step2 - save do db
		$sql = "INSERT INTO ".TABLE_PREFIX."djg_gallery_pics
					(`sort_order`,`pageId`,`filename`,`fileext`,`filehash`,`title`,`description`)
				VALUES
					(0,'".$pageId."', '".$fileName."', '".$fileExt."', '".self::uniqeId()."','".$item[0]['title']."', '".$item[0]['title']."');";
		return (self::executeSql($sql))?true:false;
		
	}
	public static function getItem($id) {
		$sql = "SELECT * FROM ".TABLE_PREFIX."djg_gallery_items WHERE id='$id' LIMIT 1";
		return self::executeSql($sql);
	}
	/*
	public static function editItem($_POST,$id) {
		$sql = "UPDATE ".TABLE_PREFIX."djg_gallery_items
				SET pageId=$_POST[pageId], 
				title='$_POST[title]',
				description='$_POST[description]' 
				WHERE id='$id'";
		self::executeSql($sql);
	}
	*/
	//items
/*
	public static function getPageId($itemId)
	{
		$sql = "SELECT pageId,title FROM ".TABLE_PREFIX."djg_gallery_items WHERE id=$itemId LIMIT 1";
		// return pageId
		return self::executeSql($sql);
	}
	*/
	public static function lastAdded($limit=null)
	{
		$sql = "SELECT * FROM ".TABLE_PREFIX."djg_gallery_pics ORDER BY id DESC";
		if(limit!=null) $sql .= " LIMIT 0,$limit"; else $sql .= " LIMIT 0,4";
		return self::executeSql($sql);
	}
	public static function displayAlbums($itemsArray,$showEmpty=true,$limit=0)
	{
		$settings = Plugin::getAllSettings('djg_gallery');
		// persowanie wyników z bazy
		echo "<div id='djg_gallery'>";
		echo "<ul>";
		foreach($itemsArray as $key=>$val)
		{
			echo '<li>';
			if( ($showEmpty) and (count($val['pics'])==0) ) 
			{
					echo '<p class="img">Brak zdjęć</p>';
					echo '<h3>' . $val["title"] . '</h3>';
					echo '<p>' . $val["description"] . '</p>';
					echo 'Przejdź do galerii: ' . Page::linkById((int)$val['pageId']);
			}
			else
			{
				foreach($val['pics'] as $key=>$picVal) 
				{
					echo '<img src="' . URL_PUBLIC . $settings['patch'] . '/' . $picVal['filename'] . '/thumb_' . $picVal['filename'] . '.' . $picVal['fileext'] . '" />';
					echo '<h3>' . $val["title"] . '</h3>';
					echo '<p>' . $val["description"] . '</p>';
					echo 'Przejdź do galerii: ' . Page::linkById((int)$val['pageId']);
					
				}
			} // end if
			echo '</li>';
		} // end foreach
		echo "</ul>";
		echo "</div>";
	} // end function

	
	public static function updateSettings($settings) 
	{
		foreach($settings as $key=>$value) 
		{
			$sql = "UPDATE ".TABLE_PREFIX."plugin_settings
					SET value='$value'
					WHERE plugin_id='djg_gallery'
					AND name = '$key'";
			self::executeSql($sql);
		}
	}
	
    /** 2013 */
	public static function moveFiles($move) {
		if( ((int)$move['fromId']==0) or ((int)$move['toId']==0)): 
			return __('Chose page');
		else: 
			$sql = "UPDATE ".TABLE_PREFIX."djg_gallery_pics SET pageId=$move[toId] WHERE pageId='$move[fromId]'";
			self::executeSql($sql);
			return 1;
		endif;
	}
	
    /** 2013 */
	public static function recursive_remove_directory($directory, $empty=FALSE)
	{
		if(substr($directory,-1) == '/')
		{
			$directory = substr($directory,0,-1);
		}
		if(!file_exists($directory) || !is_dir($directory))
		{
			return FALSE;
		}elseif(is_readable($directory))
		{
			$handle = opendir($directory);
			while (FALSE !== ($item = readdir($handle)))
			{
				if($item != '.' && $item != '..')
				{
					$path = $directory.'/'.$item;
					if(is_dir($path)) 
					{
						self::recursive_remove_directory($path);
					}else{
						unlink($path);
					}
				}
			}
			closedir($handle);
			if($empty == FALSE)
			{
				if(!rmdir($directory))
				{
					return FALSE;
				}
			}
		}
		return TRUE;
	}

}