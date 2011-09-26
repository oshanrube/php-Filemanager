<?php



class Files{
	var $tobedeleted = array();
	var $files = array();
	var $folders = array();
	public function __construct() {
	}
	//getFile from inode
	public function getFile($inode) { 
		foreach($this->files as $file){
			if($file->inode == $inode){
				return $file;
			}
		}
	}
	//get parent 
	public function getParent($file) {
		return dechex(fileinode($file->path));
	}
	//get the folders only
	public function getFolders($path = '/') {
		foreach($this->files as $file){
			if($file->isDir && $file->folder == $path){
				$this->folders[] = $file;
			}
		}
		sort($this->folders);
		return $this->folders;
	}
	//get the files only
	public function getFiles($path = '/') {
		foreach($this->files as $file){
			if(!$file->isDir && $file->folder == $path){
				$this->filesOnly[] = $file;
			}
		}
		sort($this->filesOnly);
		return $this->filesOnly;
	}
	//dump files
	public function dumpFiles() {
		return $this->files;
	}
	//load files 
	public function loadFiles($files) {
		$this->files = $files;
	}
	//list the files
	function items($BASEDIR,$path, $filter, $recurse, $full, $exclude, $excludefilter,$deletefilter,$depth = 1) {
		// Compute the excludefilter string
		if(count($excludefilter))
			$excludefilter_string = '/('. implode('|', $excludefilter) .')/';
		else
			$excludefilter_string = '';
		// Compute the excludefilter string
		if(count($deletefilter))
			$deletefilter_string = '/('. implode('|', $deletefilter) .')/';
		else
			$deletefilter_string = '';
		// Initialise variables.
		$arr = array();
		if(!is_dir($BASEDIR.$path)) die('path does not exsist:'.$BASEDIR.$path);
		// read the source directory
			$handle = opendir($BASEDIR.$path);
			while (($file = readdir($handle)) !== false)
			{
				if ($file != '.' && $file != '..' && !in_array($file, $exclude) && (empty($excludefilter_string) || !preg_match($excludefilter_string, $file)))
				{
					// Compute the fullpath and absolutepath
					$fullpath = $path .'/'. $file;
					$absolutePath = $BASEDIR.$fullpath;
					// Compute the isDir flag
					$isDir = is_dir($absolutePath);
					//check wether it matches delete patern
					if(preg_match($deletefilter_string, $file)) {
						//addto tobedeleted
						$this->tobedeleted[] = $fullpath;
					}else {
						$arrfile = null;
						// store file details
						$arrfile->filename 	= $file;
						$arrfile->path 		= dirname($absolutePath);
						$arrfile->folder 	= dirname($fullpath);
						$arrfile->isDir 		= $isDir;
						$arrfile->filesize 	= dechex(filesize($absolutePath));
						$arrfile->filemtime = filemtime($absolutePath);
						$arrfile->inode = dechex(fileinode($absolutePath));
						$arr[] = $arrfile;
					}
					
					//if directory
					if ($isDir && $recurse)
					{
						// Search recursively
						if ($recurse){
							$arr = array_merge($arr, $this->items($BASEDIR,$fullpath, $filter, $recurse, $full, $exclude, $excludefilter,$deletefilter,$depth+1));
						}
					}
				}
			}
			
			closedir($handle);
			$this->files = $arr;
			return $arr;
	}
	function getFilesToBeDeleted() {
		return $this->tobedeleted;
	}
		// Sort the files
	//	asort($arr);
	//	var_dump( file('.htaccess'));
		//$filename = '.htaccess';
		//echo "$filename was last changed: " . date("F d Y H:i:s.", filemtime($filename))."\n";
		//echo  hexdec(dechex(filesize($filename)));
		//echo var_dump( $arr);
		//var_dump( array_values($tobedeleted));
	//---------------------------------------------------------------------------------
	function writeToFile($arr) {
		//lines per file
		$chunk = 5000;
		//initialize the filename
		$filename = 'filedata-1.txt';
		if(count($arr) > $chunk) {
			$files = array_chunk($arr,$chunk,true);
		} else {
			$files[] = $arr;
		}
		foreach($files as $file){
			while(file_exists($filename)) {
				//if the file is already there create a new one
				$filename = 'filedata-'.(preg_replace('/.*-([0-9]+)\.txt/','$1',$filename)+1).'.txt';
			}
			if (!$handle = fopen($filename, 'a+')) {
				echo "Cannot open file ($filename)";
			   exit;
			}
			foreach($file as $item){
				$string = serialize($item)."\n";
				if (fwrite($handle, $string) === FALSE) {
				  echo "Cannot write to file ($filename)";
				  exit;
				}
			}
			fclose($handle);	
		}
	}
	
	//---------------------------------------------------------------------------------------
	//initialize the array to store the file objects
	function readFromFile() {
		$arr = array();
		$filename = 'filedata-1.txt';
		while(file_exists($filename)) {
			$chunksize = 1 * (1024 * 1); // how many bytes per chunk 
			$handle = fopen($filename, 'rb'); 
			$buffer = ''; 
			while (($buffer = fgets($handle, $chunksize)) !== false) {
				$arr[] = $buffer;
				ob_flush(); 
				flush();
			}
			fclose($handle);
			$filename = 'filedata-'.(preg_replace('/.*-([0-9]+)\.txt/','$1',$filename)+1).'.txt';
		}
	return $arr;
	}
	public static function makesafe($file) {
		return $file;
	}
}

?>