<?php
class zipfile
{
	var $datasec = array();	var $files = array();var $dirs = array();var $ctrl_dir = array();var $eof_ctrl_dir = "\x50\x4b\x05\x06\x00\x00\x00\x00";var $old_offset = 0;var $basedir = ".";
	function create_dir($name){
		$name = str_replace("\\", "/", $name);
		$fr = "\x50\x4b\x03\x04";$fr .= "\x0a\x00";	$fr .= "\x00\x00";$fr .= "\x00\x00";$fr .= "\x00\x00\x00\x00";
		$fr .= pack("V",0);$fr .= pack("V",0);$fr .= pack("V",0);$fr .= pack("v",strlen($name));$fr .= pack("v", 0);$fr .= $name;
		$fr .= pack("V",0);$fr .= pack("V",0);$fr .= pack("V",0); 
		$this->datasec[] = $fr;
		$new_offset = strlen(implode("", $this->datasec));
		$cdrec = "\x50\x4b\x01\x02";$cdrec .="\x00\x00";$cdrec .="\x0a\x00";$cdrec .="\x00\x00";$cdrec .="\x00\x00";$cdrec .="\x00\x00\x00\x00";$cdrec .= pack("V",0);$cdrec .= pack("V",0);$cdrec .= pack("V",0);$cdrec .= pack("v", strlen($name) );	$cdrec .= pack("v", 0 );$cdrec .= pack("v", 0 );$cdrec .= pack("v", 0 );$cdrec .= pack("v", 0 );$cdrec .= pack("V", 16 );
		$cdrec .= pack("V", $this->old_offset);$this->old_offset = $new_offset;
		$cdrec .= $name;
		$this->ctrl_dir[] = $cdrec;
		$this->dirs[] = $name;
	}
	function create_file($data, $name){
		$name = str_replace("\\", "/", $name);
		$fr = "\x50\x4b\x03\x04";$fr .= "\x14\x00";$fr .= "\x00\x00";$fr .= "\x08\x00";$fr .= "\x00\x00\x00\x00";$unc_len = strlen($data);$crc = crc32($data);$zdata = gzcompress($data);$zdata = substr($zdata, 2, -4);$c_len = strlen($zdata);$fr .= pack("V",$crc);
		$fr .= pack("V",$c_len);$fr .= pack("V",$unc_len);$fr .= pack("v", strlen($name) );
		$fr .= pack("v", 0 );$fr .= $name;$fr .= $zdata;
		$fr .= pack("V",$crc);$fr .= pack("V",$c_len); $fr .= pack("V",$unc_len);
		$this->datasec[] = $fr;	$new_offset = strlen(implode("", $this->datasec));$cdrec = "\x50\x4b\x01\x02";
		$cdrec .="\x00\x00";$cdrec .="\x14\x00";$cdrec .="\x00\x00";$cdrec .="\x08\x00";
		$cdrec .="\x00\x00\x00\x00";$cdrec .= pack("V",$crc); $cdrec .= pack("V",$c_len); $cdrec .= pack("V",$unc_len);
		$cdrec .= pack("v", strlen($name) );$cdrec .= pack("v", 0 );$cdrec .= pack("v", 0 );$cdrec .= pack("v", 0 );
		$cdrec .= pack("v", 0 );$cdrec .= pack("V", 32 );$cdrec .= pack("V", $this->old_offset);	$this->old_offset = $new_offset;
		$cdrec .= $name;$this->ctrl_dir[] = $cdrec;
	}
	function read_zip($name){
		$this->datasec = array();$this->name = $name;$this->mtime = filemtime($name);$this->size = filesize($name);
		$fh = fopen($name, "rb");
		$filedata = fread($fh, $this->size);
		fclose($fh);
		$filesecta = explode("\x50\x4b\x05\x06", $filedata);
		$unpackeda = unpack('x16/v1length', $filesecta[1]);
		$this->comment = substr($filesecta[1], 18, $unpackeda['length']);
		$this->comment = str_replace(array("\r\n", "\r"), "\n", $this->comment); // CR + LF and CR -> LF
		$filesecta = explode("\x50\x4b\x01\x02", $filedata);
		$filesecta = explode("\x50\x4b\x03\x04", $filesecta[0]);
		array_shift($filesecta); // Removes empty entry/signature
		foreach($filesecta as $filedata){
			$entrya = array();
			$entrya['error'] = "";
			$unpackeda = unpack("v1version/v1general_purpose/v1compress_method/v1file_time/v1file_date/V1crc/V1size_compressed/V1size_uncompressed/v1filename_length", $filedata);
			$isencrypted = (($unpackeda['general_purpose'] & 0x0001) ? true : false);
			if($unpackeda['general_purpose'] & 0x0008){
				$unpackeda2 = unpack("V1crc/V1size_compressed/V1size_uncompressed", substr($filedata, -12));
				$unpackeda['crc'] = $unpackeda2['crc'];
				$unpackeda['size_compressed'] = $unpackeda2['size_uncompressed'];
				$unpackeda['size_uncompressed'] = $unpackeda2['size_uncompressed'];
				unset($unpackeda2);
			}
			$entrya['name'] = substr($filedata, 26, $unpackeda['filename_length']);
			if(substr($entrya['name'], -1) == "/"){
				continue;
			}
			$entrya['dir'] = dirname($entrya['name']);
			$entrya['dir'] = ($entrya['dir'] == "." ? "" : $entrya['dir']);
			$entrya['name'] = basename($entrya['name']);
			$filedata = substr($filedata, 26 + $unpackeda['filename_length']);
			if(strlen($filedata) != $unpackeda['size_compressed']){
				$entrya['error'] = "Compressed size is not equal to the value given in header.";
			}
			if($isencrypted){
				$entrya['error'] = "Encryption is not supported.";
			}
			else{
				switch($unpackeda['compress_method']){
					case 0:
					break;
					case 8:
						$filedata = gzinflate($filedata);
					break;
					case 12:
						if(!extension_loaded("bz2")){
							@dl((strtolower(substr(PHP_OS, 0, 3)) == "win") ? "php_bz2.dll" : "bz2.so");
						}
						if(extension_loaded("bz2")){
							$filedata = bzdecompress($filedata);
						}
						else{
							$entrya['error'] = "Required BZIP2 Extension not available.";
						}
					break;
					default:
						$entrya['error'] = "Compression method ({$unpackeda['compress_method']}) not supported.";
				}
				if(!$entrya['error']){
					if($filedata === false){
						$entrya['error'] = "Decompression failed.";
					}
					elseif(strlen($filedata) != $unpackeda['size_uncompressed']){
						$entrya['error'] = "File size is not equal to the value given in header.";
					}
					elseif(crc32($filedata) != $unpackeda['crc']){
						$entrya['error'] = "CRC32 checksum is not equal to the value given in header.";
					}
				}
				$entrya['filemtime'] = mktime(($unpackeda['file_time']  & 0xf800) >> 11,($unpackeda['file_time']  & 0x07e0) >>  5, ($unpackeda['file_time']  & 0x001f) <<  1, ($unpackeda['file_date']  & 0x01e0) >>  5, ($unpackeda['file_date']  & 0x001f), (($unpackeda['file_date'] & 0xfe00) >>  9) + 1980);
				$entrya['data'] = $filedata;
			}
			$this->files[] = $entrya;
		}
		return $this->files;
	}
	function add_file($file, $dir = ".", $file_blacklist = array(), $ext_blacklist = array()){
		$file = str_replace("\\", "/", $file);
		$dir = str_replace("\\", "/", $dir);
		if(strpos($file, "/") !== false){
			$dira = explode("/", "{$dir}/{$file}");
			$file = array_shift($dira);
			$dir = implode("/", $dira);
			unset($dira);
		}
		while(substr($dir, 0, 2) == "./"){
			$dir = substr($dir, 2);
		}
		while(substr($file, 0, 2) == "./"){
			$file = substr($file, 2);
		}
		if(!in_array($dir, $this->dirs)){
			if($dir == ".")
			{
				$this->create_dir("./");
			}
			$this->dirs[] = $dir;
		}
		if(in_array($file, $file_blacklist)){
			return true;
		}
		foreach($ext_blacklist as $ext){
			if(substr($file, -1 - strlen($ext)) == ".{$ext}"){
				return true;
			}
		}
		$filepath = (($dir && $dir != ".") ? "{$dir}/" : "").$file;
		if(is_dir("{$this->basedir}/{$filepath}")){
			$dh = opendir("{$this->basedir}/{$filepath}");
			while(($subfile = readdir($dh)) !== false){
				if($subfile != "." && $subfile != ".."){
					$this->add_file($subfile, $filepath, $file_blacklist, $ext_blacklist);
				}
			}
			closedir($dh);
		}
		else{
			$this->create_file(implode("", file("{$this->basedir}/{$filepath}")), $filepath);
		}
		return true;
	}
	function zipped_file(){
		$data = implode("", $this->datasec);
		$ctrldir = implode("", $this->ctrl_dir);
		return $data.
				$ctrldir.
				$this->eof_ctrl_dir.
				pack("v", sizeof($this->ctrl_dir)). // total number of entries "on this disk"
				pack("v", sizeof($this->ctrl_dir)). // total number of entries overall
				pack("V", strlen($ctrldir)). // size of central dir
				pack("V", strlen($data)). // offset to start of central dir
				"\x00\x00"; // .zip file comment length
	}
      function enable_modify(){
            foreach($this->files as $fileinzip){
                  if($fileinzip['dir']!="")
                        $fileinzip['dir'].="/"         ;
                  $filename =$fileinzip['dir'].$fileinzip['name'];
                  $data =$fileinzip['data'];
                  $this->create_file($data,$filename);
            }
      }
      function load_zip($file){
          $this->read_zip($file);
          $this->enable_modify();
      }
}
?>