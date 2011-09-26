<?php 
if(!$_SESSION['login']){ 
	header("HTTP/1.0 404 Not Found");
}


switch($_GET['action']) {
	case 'getfolders':
		echo getfolders();
		break;
	case 'getfiles':
		echo getfiles();
		break;
	case 'deleteFolder':
		deleteFolder();
		break;
	case 'deleteFile':
		deleteFile();
		break;
	default:
		header("HTTP/1.0 404 Not Found");
		break;
}


function getfolders() {
	$inode = $_GET['file'];
//get files
	$files = new Files();
	$basedir = Prefs::getParam('basedir');$filter = Prefs::getParam('filter');$recurse = Prefs::getParam('recurse');$full = Prefs::getParam('full');$exclude = Prefs::getParam('exclude');$excludefilter = Prefs::getParam('excludefilter');$deletefilter = Prefs::getParam('deletefilter');
	if($_SESSION['parent']){$path = $_SESSION['parent'];}
	else {$path='';}
	$files->items($basedir,$path, $filter, $recurse, $full, $exclude, $excludefilter,$deletefilter);
	$file = $files->getFile($inode);
	//folder path
	$path = $file->folder;
	if($file->folder != '/')$path .= '/';	
	$path .= $file->filename;
	$_SESSION['path'] = $path;
	$_SESSION['files'] = $files->dumpFiles(); 
	$folders = $files->getFolders($path); 
	$return = "<?xml version=\"1.0\" encoding=\"utf8\" ?>";
		$return .= "<folders>";
		if($path != '/'){$return .= '<folder id=\''.$files->getParent($file).'\'>../</folder>';}
 		if(count($folders)) {
    		foreach ($folders as $folder) {
                $return .="<folder id='".$folder->inode."'>".$folder->filename."</folder>";
			}
      }
      $return .= "</folders>";
      $return .= "<path>".$_SESSION['path']."</path>";
	return $return;
}
function getfiles() {
	$foldername = $_GET['file'];
	$path = $_SESSION['path'];
	$files = new Files();
	$files->loadFiles($_SESSION['files']);
	$folders = $files->getFiles($path);
	$return = "<?xml version=\"1.0\" encoding=\"utf8\" ?>";
		$return .= "<files>";
 		if(count($folders)) {
    		foreach ($folders as $folder) {
                $return .="<file id='".$folder->inode."'>".$folder->filename."</file>";
			}
      }
      $return .= "</files>";
      $return .= "<path>".$_SESSION['path']."</path>";
	return $return;
}
function deleteFolder() {
	$inode = $_GET['inode'];
	$basedir = Prefs::getParam('basedir');$filter = Prefs::getParam('filter');$recurse = Prefs::getParam('recurse');$full = Prefs::getParam('full');$exclude = Prefs::getParam('exclude');$excludefilter = Prefs::getParam('excludefilter');$deletefilter = Prefs::getParam('deletefilter');
	$path='';
	$files = new Files();
	$files->items($basedir,$path, $filter, $recurse, $full, $exclude, $excludefilter,$deletefilter);
	$file = $files->getFile($inode);
	$folderpath = $file->path.'/'.$file->filename;
	if(rmdir($folderpath)){
		echo "success";
	} else {
		echo "failed";
	}
}
function deleteFile() {
	$inode = $_GET['inode'];
	$basedir = Prefs::getParam('basedir');$filter = Prefs::getParam('filter');$recurse = Prefs::getParam('recurse');$full = Prefs::getParam('full');$exclude = Prefs::getParam('exclude');$excludefilter = Prefs::getParam('excludefilter');$deletefilter = Prefs::getParam('deletefilter');
	$path='';
	$files = new Files();
	$files->items($basedir,$path, $filter, $recurse, $full, $exclude, $excludefilter,$deletefilter);
	$file = $files->getFile($inode);
	$folderpath = $file->path.'/'.$file->filename;
	if(unlink($folderpath)){
		echo "success";
	} else {
		echo "failed";
	}
}
?>