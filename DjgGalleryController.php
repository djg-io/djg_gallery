<?php
class DjgGalleryController extends PluginController {

	private $orgThumbName		= "big_";
	private $thumbThumbName		= "thumb_";
	private $path 				= "public/djg_gallery/";
	private $array_ext	 		= array('jpg','gif','png');
	private $errors				= null;

	protected $_image;
	protected $_image_root;
	
	const VIEW_FOLDER = "../../plugins/djg_gallery/views/";

	public function __construct() {
		if (defined('CMS_BACKEND')) {
			AuthUser::load();
			
			if ( !(AuthUser::isLoggedIn()) ) {
				redirect(get_url('login'));
			}

			if ( !AuthUser::hasPermission('admin_view') ) {
				redirect(URL_PUBLIC);
			}		
		
			$this->setLayout('backend');
			$this->assignToLayout('sidebar', new View('../../plugins/djg_gallery/views/backend/sidebar'));
			set_time_limit(180);
			ini_set('memory_limit','128M');
			ini_set('post_max_size','100M');
			ini_set('upload_max_filesize','100M');
			$this->path = Plugin::getSetting('path', 'djg_gallery');
			$src = Plugin::getSetting('path', 'image');
			if ($src !== '/')
				$src = '/'.$src;
			if (substr($src, strlen($src) - 1) !== '/')
				$src = $src . '/';
			$this->_image_root = CMS_ROOT . $src;	
		}else{
			$page = $this->findByUri();
			$this->setLayout('none');
		}
	}
	/**
	public function content($part=false, $inherit=false) {
		if (!$part)
			return $this->content;
		else
			return false;
	}
	
	public function querytest($item_id=NULL)
	{
		//get page name of item
		$sql_item_name = "SELECT pageId,title FROM ".TABLE_PREFIX."djg_gallery_items WHERE id='$item_id' LIMIT 1";
		$item = Djggallery::executeSql($sql_item_name);
		if (!$item):
			Flash::set('error',__('nie ma takiego elementu'));
			redirect(get_url('plugin/djg_gallery/items/'));
		endif;		
		$page_id = $item[0]['pageId'];
		$item_title = $item[0]['title'];
		echo "<pre>";
		print_r($item);
		echo "</pre>";
		
		//get item name
		$sql_page = "SELECT title FROM ".TABLE_PREFIX."page WHERE id='$page_id' LIMIT 1";
		$page = Djggallery::executeSql($sql_page);
		if (!$page):
			Flash::set('error',__('strona do którego item został przypisany nie istnieje'));
			redirect(get_url('plugin/djg_gallery/items/'));
		endif;
		$page_name = $page[0]['title'];
		
		// get files
		$sql_files = "SELECT * FROM ".TABLE_PREFIX."djg_gallery_pics WHERE itemId='$item_id' ORDER BY id ASC";
		$files = Djggallery::executeSql($sql_files);
		
		//display
		if (!$files): Flash::set('error',__('not pics')); Flash::init(); endif;
		$this->display('djg_gallery/views/backend/files', array('page_id' => $page_id, 'item_title'=>$item_title, 'files' => $files));
	}
	*/
	public function index() 
	{
		redirect(get_url('plugin/djg_gallery/documentation'));
    }
	
/********/
/* VIEW */
/********/	

	public function move_files($page=NULL) 
	{
		$fromId = (!empty($_POST['fromId'])) ? (int)$_POST['fromId'] : 0;
		$toId = (!empty($_POST['toId'])) ? (int)$_POST['toId'] : 0;
		if( ( $fromId != 0 ) && ( $toId != 0 )): 
			$result = Djggallery::moveFiles($_POST);
			if($result==1) Flash::set('success', __('All files moved'));
			else Flash::set('error', $result);
			redirect(get_url('plugin/djg_gallery/move_files'));
		else:
			$pages = Page::find(array('order'=>'page.created_on DESC', 'where'=>'page.djg_gallery > 1'));
			$this->display('djg_gallery/views/backend/move_files', array('pages' => $pages));
		endif;
	}

	public function settings($page=NULL) 
	{
		if($page):
			Djggallery::updateSettings($_POST);
			Flash::set('success', __('Your settings have been updated'));
			redirect(get_url('plugin/djg_gallery/settings'));
		else:
			$settings = Plugin::getAllSettings('djg_gallery');
			$this->display('djg_gallery/views/backend/settings', array('settings' => $settings));
		endif;
	}
	
    function save() 
	{
        if (isset($_POST['settings'])):
            $settings = $_POST['settings'];
            foreach ($settings as $key => $value) $settings[$key] = mysql_escape_string($value);
            
            $ret = Plugin::setAllSettings($settings, 'djg_gallery');

            if ($ret):
                Flash::set('success', __('The settings have been saved.'));
            else:
                Flash::set('error', __('An error occured trying to save the settings.'));
            endif;
        else:
            Flash::set('error', __('Could not save settings, no settings found.'));
        endif;

        redirect(get_url('plugin/djg_gallery/settings'));
    }
	
	public function documentation() 
	{
		$this->display('djg_gallery/views/backend/documentation');
	}

   public function thumbnails()
   {
	$watermarks = glob(CMS_ROOT.DS.$this->path.'watermarks'.DS.'*.*');
	$sql ="SELECT * FROM " . TABLE_PREFIX . "djg_gallery_thumbnails ORDER BY id ASC";
		if(isset($_POST['djg_gallery'])):
			$i=0;
			foreach ($_POST['djg_gallery'] as $key => $value):($value=='')?$i++:''; endforeach;	
			if($i==0):
				// save new thumbnail
				if(!is_numeric($_POST['djg_gallery']['width'])):
					Flash::set('error', __('Width is not numeric.')); Flash::init();
					$this->display('djg_gallery/views/backend/thumbnail', array('djg_gallery' => $_POST['djg_gallery'], 'thumbnails' => Djggallery::executeSql($sql), 'watermarks' => $watermarks));
				elseif(!is_numeric($_POST['djg_gallery']['height'])):
					Flash::set('error', __('Height is not numeric.')); Flash::init();
					$this->display('djg_gallery/views/backend/thumbnail', array('djg_gallery' => $_POST['djg_gallery'], 'thumbnails' => Djggallery::executeSql($sql), 'watermarks' => $watermarks));
				else:
					$d_g = $_POST['djg_gallery'];
					if(!empty($d_g['watermark']['filename'])):
						$wArray = implode('|',$d_g['watermark']);
					else:
						$wArray = null;
					endif;
					//$wArray = implode('|',$d_g['watermark']);
					$sql_add = "INSERT INTO ".TABLE_PREFIX."djg_gallery_thumbnails (thumbname,thumbhash,quality,crop,cropposition,width,height,watermark) VALUES ('".preg_replace('/\s+/', '_', $d_g['thumbname'])."','".$d_g['thumbhash']."','".$d_g['quality']."','".$d_g['crop']."','".$d_g['cropposition']."','".$d_g['width']."','".$d_g['height']."','".$wArray."')";
					Djggallery::executeSql($sql_add);
					Flash::set('success', __('Thumbnail added.'));
					redirect(get_url('plugin/djg_gallery/thumbnails'));
				endif;
			else:
				// isset empty fields
				Flash::set('error', __('Complete the :i field(s).',array(':i'=>$i))); Flash::init();
				$this->display('djg_gallery/views/backend/thumbnail', array('djg_gallery' => $_POST['djg_gallery'], 'thumbnails' => Djggallery::executeSql($sql), 'watermarks' => $watermarks));
			endif;		
		else:
			// no post, empty form
			$this->display('djg_gallery/views/backend/thumbnail', array('djg_gallery' => null, 'thumbnails' => Djggallery::executeSql($sql), 'watermarks' => $watermarks));
		endif;
	}
	public function edit_thumbnail($at1=NULL)
	{
		if($at1 == NULL) redirect(get_url('plugin/djg_gallery/thumbnails'));
		$watermarks = glob(CMS_ROOT.DS.$this->path.'watermarks'.DS.'*.*');
		$sql_thumbnail = "SELECT * FROM ".TABLE_PREFIX."djg_gallery_thumbnails WHERE id='$at1' LIMIT 1";
		$items = Djggallery::executeSql($sql_thumbnail);
		$post = (isset($_POST['djg_gallery'])) ? $_POST['djg_gallery'] : $items[0];
		
		if( (!is_array($post['watermark'])) && (!empty($post['watermark'])) ): 
			$post['watermark'] 							= explode('|',$post['watermark']);
			$post['watermark']['filename'] 				= $post['watermark'][0];
			$post['watermark']['watermark_position'] 	= $post['watermark'][1];
			$post['watermark']['opacity'] 				= $post['watermark'][2];
			$post['watermark']['horizontal_margin'] 	= $post['watermark'][3];
			$post['watermark']['vertical_margin'] 		= $post['watermark'][4];
		endif;

		$i=0;
		foreach ($post as $key => $value):($value=='')?$i++:''; endforeach;	
		if(isset($_POST['djg_gallery']) && ($i==0)):
				if(!is_numeric($post['width'])):
					Flash::set('error', __('Width is not numeric.')); Flash::init();
					$this->display('djg_gallery/views/backend/edit_thumbnail', array('djg_gallery' => $post, 'watermarks' => $watermarks, 'at1' => $at1));
				elseif(!is_numeric($post['height'])):
					Flash::set('error', __('Height is not numeric.')); Flash::init();
					$this->display('djg_gallery/views/backend/edit_thumbnail', array('djg_gallery' => $post, 'watermarks' => $watermarks, 'at1' => $at1));
				else:
					if(!empty($post['watermark']['filename'])):
						$wArray = implode('|',$post['watermark']);
					else:
						$wArray = null;
					endif;
		
					
					
					
					//print_r($post['watermark']); exit();
					$sql_change = "UPDATE ".TABLE_PREFIX."djg_gallery_thumbnails SET thumbname = '".preg_replace('/\s+/', '_',$post['thumbname'])."', quality = ".$post['quality'].", cropposition = '".$post['cropposition']."', crop = ".(int)$post['crop'].", width = ".$post['width'].", height = ".$post['height'].", watermark = '".$wArray."' WHERE id ='$at1' LIMIT 1";
					Djggallery::executeSql($sql_change);
					Flash::set('success', __('Thumbnail changed.'));
					redirect(get_url('plugin/djg_gallery/edit_thumbnail/'.$at1));
				endif;
		else:
			if(isset($_POST['djg_gallery'])) Flash::set('error', __('Complete the :i field(s).',array(':i'=>$i))); Flash::init();
			$this->display('djg_gallery/views/backend/edit_thumbnail', array('djg_gallery' => $post, 'watermarks' => $watermarks, 'at1' => $at1));
		endif;
	}
	public function regenerate_thumbnail($at1=NULL)
	{
		if($at1 == NULL) redirect(get_url('plugin/djg_gallery/thumbnails'));
		$this->display('djg_gallery/views/backend/regenerate_thumbnail', array('at1' => $at1));
	}
	/**
	public static function tt($thumbName, $patch, $file)
	{
		exit($patch.DS.'thumbs'.DS.$thumbName.'_'.$file);
		if (file_exists($patch.DS.'thumbs'.DS.$thumbName.'_'.$file)):
			return true;
		else:
			$w = $patch.'watermark.png';
			$parametr = "fltr=wmi|".$w.";zc='C';w=200;h=200";
			$parametrs = explode(';',$parametr);			
			$phpThumb = new phpThumb();
			$phpThumb->setSourceFilename($patch.$file);
			print_r($parametrs);
			//exit();
			foreach($parametrs as &$value):
				$v = explode('=' , $value);
				$phpThumb->setParameter($v[0],$v[1]);
			endforeach;

			if (!$phpThumb->GenerateThumbnail()) { exit('cannot generate thumbnail');}
			if (!$phpThumb->RenderToFile($patch.DS.'thumbs'.DS.$thumbName.'_'.$file)) {  exit('cannot save thumbnail');}
			return true;
		endif;
	}
	*/
	
	/**
	$thumbnail array()
	$filename
	$ext
	*/
	public function save_thumbnail($thumbnail, $filename, $ext)
	{
		$phpThumb = new phpThumb();
		$phpThumb->setSourceFilename(CMS_ROOT.DS.$this->path.$filename.'.'.$ext);
		$phpThumb->setParameter('w', $thumbnail['width']);
		$phpThumb->setParameter('h', $thumbnail['height']);
		$phpThumb->setParameter('zc', ($thumbnail['crop']==0)?false:'C');
		$phpThumb->setParameter('q', $thumbnail['quality']);
		if(!empty($thumbnail['watermark'])):
			$wArray = explode('|',$thumbnail['watermark']);
			if(file_exists(CMS_ROOT.DS.'public'.DS.'djg_gallery'.DS.'watermarks'.DS.$wArray[0])):
				$phpThumb->setParameter('fltr', 'wmi|'.CMS_ROOT.DS.'public'.DS.'djg_gallery'.DS.'watermarks'.DS.$wArray[0]);
			endif;
		endif;
		if (!$phpThumb->GenerateThumbnail()) { exit('cannot generate thumbnail');}
		if (!$phpThumb->RenderToFile(CMS_ROOT.DS.$this->path.'thumbs'.DS.$thumbnail['thumbname'].$filename.'.'.$ext)) {  exit('cannot save thumbnail');}

		return true;
	}

/********/
/* AJAX */
/********/

	function ajax_jeditable(){
		$sql = "UPDATE ".TABLE_PREFIX."djg_gallery_pics SET ".$_POST['names']." = '" . $_POST['value']. "' WHERE id = " . $_POST['id'];
		Djggallery::executeSql($sql);
		print $_POST['value'];
	}
	
	function ajax_global(){
		$sql = "UPDATE ".TABLE_PREFIX."djg_gallery_pics SET ".$_POST['names']." = '" . $_POST['value']. "' WHERE pageId = " . $_POST['pageId'];
		return Djggallery::executeSql($sql);
	}
	
	function ajax_after_upload()
	{
		$pageId = $_GET['pageId'];
		$ext = $_GET['ext'];
		$filename = $json2['alert'] = $_GET['filename'];
		$json2['error'] = 0;
		$sql="SELECT * FROM ".TABLE_PREFIX."djg_gallery_thumbnails";
		$thumbnails = Djggallery::executeSql($sql);
		// resize original file after upload
		if( (Plugin::getSetting('resize_org_x', 'djg_gallery')!=0) || (Plugin::getSetting('resize_org_y', 'djg_gallery')!=0) ):
			$phpThumb = new phpThumb();
			$phpThumb->setSourceFilename(CMS_ROOT.DS.$this->path.$filename.'.'.$ext);
			if(Plugin::getSetting('resize_org_x', 'djg_gallery')!=0) $phpThumb->setParameter('w', Plugin::getSetting('resize_org_x', 'djg_gallery'));
			if(Plugin::getSetting('resize_org_y', 'djg_gallery')!=0) $phpThumb->setParameter('h', Plugin::getSetting('resize_org_y', 'djg_gallery'));
			$phpThumb->setParameter('q', '100');
			if (!$phpThumb->GenerateThumbnail()) { exit('cannot generate thumbnail');}
			if (!$phpThumb->RenderToFile(CMS_ROOT.DS.$this->path.$filename.'.'.$ext)) {  exit('cannot save thumbnail');}
	endif;
		// generate thumbs
		foreach($thumbnails as $thumbnail):
				if(!self::save_thumbnail($thumbnail,$filename,$ext)) $json2['error']++;
		endforeach;

		echo json_encode($json2);		
	}
	
	/**
	AJAX DEL THUMBNAIL
	*/
	public function ajax_del_thumbnail() 
	{
		$return['error'] = 0;
		$id = $_GET['id'];
		$sql = "SELECT thumbname FROM ".TABLE_PREFIX."djg_gallery_thumbnails WHERE id='$id' LIMIT 1";
		$name = Djggallery::executeSql($sql);
		$thumb_name = $name[0]['thumbname'];
		$files = glob(CMS_ROOT.DS.'public'.DS.'djg_gallery'.DS.'thumbs'.DS.$thumb_name.'????????.*');
		array_walk($files, function ($file) {unlink($file);});
		$sql_del = "DELETE FROM ".TABLE_PREFIX."djg_gallery_thumbnails WHERE id='$id' LIMIT 1";
		Djggallery::executeSql($sql_del);
		echo json_encode($return);
		exit();
	}
	/*
	usuwa zdjęcie z bazy i fizycznie katalog
	*/
	public static function del_file($file_id){
		$sql = "SELECT id,filename,fileext FROM ".TABLE_PREFIX."djg_gallery_pics WHERE id='$file_id' LIMIT 1";
		$filename = Djggallery::executeSql($sql);
		$files = glob(CMS_ROOT.DS.'public'.DS.'djg_gallery'.DS.'thumbs'.DS.'*'.$filename[0]["filename"].'.*');
		if( (array_walk($files, function ($file) {unlink($file);})) and (unlink(CMS_ROOT.DS.'public'.DS.'djg_gallery'.DS.$filename[0]["filename"].'.'.$filename[0]["fileext"])) ):
			$sql2 = "DELETE FROM ".TABLE_PREFIX."djg_gallery_pics WHERE id='$file_id' LIMIT 1";
			Djggallery::executeSql($sql2);
			return true;
		else:
			return false;
		endif;
		return true;
	}
	
	public static function after_del_page($page_id){
		$sql = "SELECT id FROM ".TABLE_PREFIX."djg_gallery_pics WHERE pageId='$page_id'";
		foreach(Djggallery::executeSql($sql) as $key=>$val) self::del_file($val["id"]);
	}
	
	public function ajax_del_file() 
	{
		$return['error'] = !self::del_file($_GET['file_id']);
		echo json_encode($return);
		exit();
	}
	
	public function ajax_del_all_files() 
	{
		$return['error'] = 0;
		$pageId = $_GET['pageId'];
		$sql = "SELECT id FROM ".TABLE_PREFIX."djg_gallery_pics WHERE pageId='$pageId'";
		foreach(Djggallery::executeSql($sql) as $key=>$val) $return['error'] += !self::del_file($val["id"]);
		echo json_encode($return);
		exit();
	}
	
	/**
	AJAX UPLOAD FILES
	*/
	function ajax_uploadify_upload()
    {
		if (!empty($_FILES)) 	
		{				
			switch ($_FILES['Filedata']['error'])
			{
				 case 0:
						$return['error'] = false;
						break;
				 case 1:
						$return['error'] = "The file is bigger than this PHP installation allows";
						break;
				  case 2:
						$return['error'] = "The file is bigger than this form allows";
						break;
				   case 3:
						$return['error'] = "Only part of the file was uploaded";
						break;
				   case 4:
						$return['error'] = "No file was uploaded";
						break;
				   case 6:
						$return['error'] = "Missing a temporary folder";
						break;
				   case 7:
						$return['error'] = "Failed to write file to disk";
						break;
				   case 8:
						$return['error'] = "File upload stopped by extension";
						break;
				   default:
						$return['error'] = "unknown error ".$_FILES['Filedata']['error'];
						break;
			}
			
			$tempFile = $_FILES['Filedata']['tmp_name'];
			$ext = pathinfo($_FILES['Filedata']['name'], PATHINFO_EXTENSION);
			$ext = strtolower((string)$ext);
			$newName = Djggallery::uniqeId();

			if($newName == false):
				$return['error']='uniqeId error';
			else:
				$targetPath = CMS_ROOT.DS.'public'.DS.'djg_gallery'.DS;
				if(move_uploaded_file($tempFile,$targetPath.$newName.'.'.$ext)) Djggallery::addPic($_POST['pageId'],$newName,$ext);
				 chmod($targetPath . $newName . '.' . $ext, 0777);
			endif;
			
			$return['pageId'] = $_POST['pageId'];
			$return['patch'] = $targetPath;
			$return['name'] = $newName;
			$return['ext'] = $ext;
		}
		else
		{
			$return['error'] = "UPLOAD error";
		}
		echo json_encode($return);
		exit();
	} // end function
		
	function ajax_sort_files()
	{
		$action 				= $_POST['action'];
		$updateRecordsArray 	= $_POST['filesArray'];
		if ($action == "updateRecordsListings"):
			$listingCounter = 0;
			foreach ($updateRecordsArray as $recordIDValue):
				$sql = "UPDATE ".TABLE_PREFIX."djg_gallery_pics SET sort_order = " . $listingCounter . " WHERE id = " . $recordIDValue;
				Djggallery::executeSql($sql);
				$listingCounter = $listingCounter + 1;
			endforeach;
			echo '<pre>';print_r($updateRecordsArray);echo '</pre>';
		endif;
	}  // end function
	
	/*******/
	/* TMP */
	/*******/
	/**

	function chache_file($filename)
	{
		$file=CMS_ROOT.DS.$this->path.$f[0]['filename'].DS.$t[0]['name'].$f[0]['filename'].'.'.$f[0]['fileext'];
		return true;
	}	
	
	function thumbphp($filename)
	{
		//authenticate(); # authenticate and authorize, redirect/exit if failed
		
		$file_name = CMS_ROOT.DS.$this->path.'thumbs'.DS.'thumb_'.$filename.'.jpg';
		//$file_name = determine_file();
		if (file_exists($file_name)) {
			$info = getimagesize($file_name);
			$ext = preg_replace('/^.*\.([^.]+)$/D', '$1', $file_name);
			
			$new_name = 'przedszkole';
			$mime = $info['mime'];
			header("X-Sendfile: $file_name");
			header('Content-type: '.$mime);
			header("Content-Disposition: inline; filename=".$new_name.".".$ext);

			readfile($file_name);
			exit(0);
		} else {
			exit('Error: Could not load image !');
		}
	}

	function display_on_fly($thumbname,$filename)
	{
		$f = Djggallery::executeSql("SELECT * FROM ".TABLE_PREFIX."djg_gallery_pics WHERE filename='".$filename."' LIMIT 1");
		$t = Djggallery::executeSql("SELECT * FROM ".TABLE_PREFIX."djg_gallery_thumbnails WHERE thumbname='".$thumbname."' LIMIT 1");
        if( (sizeof($f)==0) || (sizeof($t)==0) ):
			header('HTTP/1.0 404 Not Found');
            die('paramert error');
        endif;
		$file = CMS_ROOT.DS.$this->path.$f[0]['filename'].DS.$t[0]['name'].$f[0]['filename'].'.'.$f[0]['fileext'];
		
		if(!is_file($file)):
			header('HTTP/1.0 404 Not Found');
            die('The file does not exist');
        endif;

		$imagepath=$file;
		$image=imagecreatefromjpeg($imagepath);
		$imgheight=imagesy($image);
		header('Content-Type: image/jpeg');
		imagejpeg($image);

         if(preg_match("/jpg$|jpeg$/i", $f[0]['fileext'])):
             header('Content-type: image/jpeg');
         elseif(preg_match("/gif$/i", $f[0]['fileext'])):
             header('Content-type: image/gif');
         elseif(preg_match("/png$/i", $f[0]['fileext'])):
             header('Content-type: image/png');
         endif;
         header('Content-Description: File Transfer');
		//header('Content-Type: application/octet-stream');
         header('Content-Disposition: inline; filename="'.$name.'"');
         header('Content-Transfer-Encoding: binary');
         header('Connection: Keep-Alive');
         header('Expires: 0');
         header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
         header('Pragma: public');
         header('Content-Length: ' . filesize($file));
         readfile($file);

	}
	*/

	public static function get_watermarks()
	{
		return glob(CMS_ROOT.DS.'public'.DS.'djg_gallery'.DS.'watermark\*.*');
	}

	public static function Callback_view_page_edit_tab_links($page) {
		if(!empty($page->id)) echo '<li class="tab"><a href="#djg_gallery_tabcontents">' . __('Gallery') . '</a></li>';
    }
	
    public static function Callback_view_page_edit_tabs($page) {
        if(!empty($page->id)) echo new View(self::VIEW_FOLDER . 'editpage/files', array('page_id' => $page->id));
    }
	
	/**
	* generate zip file of all gallery's files
	* $pageId (int)
	*/
	public static function download($pageId,$slug=null){
		$zipfile = new zipfile;
		$filename = ($slug)?$filename:'fotosy';
		$location = CMS_ROOT.DS.'public'.DS.'djg_gallery';
		$tmp_location=str_replace("./","",$location);
		$tmp_location=$tmp_location."/";
		$sql = "SELECT * FROM ".TABLE_PREFIX."djg_gallery_pics WHERE pageId='$pageId'";
		$i=0;
		foreach(Djggallery::executeSql($sql) as $key=>$val) {
			$files = $val["filename"].'.'.$val["fileext"];
			$zipfile->create_file(file_get_contents($location."/".$files), $files);
			$i++;
		} // end foreach
		if($i!=0):
			//increment download counter
			$sql2 = "UPDATE ".TABLE_PREFIX."page SET djg_gallery_download=djg_gallery_download+1 WHERE id = " . $pageId;
			Djggallery::executeSql($sql2);
		endif;
		header("Content-type: application/zip");
		header("Content-disposition: attachment;filename=\"".$filename."\"");
		echo $zipfile->zipped_file();
	}

}