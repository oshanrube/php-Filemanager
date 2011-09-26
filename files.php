<?php
session_start();
date_default_timezone_set('Asia/Calcutta');
//include libraries
include("files/prefs.php");
include "files/files.class.php";
//load the template
if($_GET['tmpl'] == 'ajax'){
	include "files/ajax.php";
	exit();	
} else if($_GET['tmpl'] == 'process'){
	include "files/process.php";
	exit();	
}
$_SESSION['path']='';

$basedir = Prefs::getParam('basedir');
$path = Prefs::getParam('path');
$filter = Prefs::getParam('filter');
$recurse = Prefs::getParam('recurse');
$full = Prefs::getParam('full');
$exclude = Prefs::getParam('exclude');
$excludefilter = Prefs::getParam('excludefilter');
$deletefilter = Prefs::getParam('deletefilter');

$files = new files();
$files->items($basedir,$path, $filter, $recurse, $full, $exclude, $excludefilter,$deletefilter);


$version = "1.1";

$server = $_SERVER['HTTP_HOST'];
$thisDir = '.';//dirname($_SERVER['PHP_SELF']); 
//echo $thisDir;
$pathToHere = "http://$server$thisDir";
$dir=isset($_GET['dir'])?$_GET['dir']:'';if(strstr($dir,'..'))$dir='';

echo $dir;
if($dir != ""){
	$titlePath = "http://$server/?dir=$dir";
	$path = $dir;	
}else{
	$titlePath = "http://$server$thisDir";
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"  >
	<head>
		<script type="text/javascript" src="files/js/jquery-1.6.js"></script>
		<meta charset="utf-8">
		<!-- stylesheets -->
		<link rel="stylesheet" href="files/css/bootstrap.min.css" type="text/css" media="all" />
		<link rel="stylesheet" href="files/css/fonts.css" type="text/css" media="all">
		<link rel="stylesheet" href="files/css/reset.css" type="text/css" media="all">
		<link rel="stylesheet" href="files/css/layout.css" type="text/css" media="all">
		<link rel="stylesheet" href="files/css/style.css" type="text/css" media="all">
		<link rel="stylesheet" href="files/css/template.css" type="text/css" media="all">
		<link rel="stylesheet" href="files/css/style.css" type="text/css" media="screen" />
	  	<link rel="stylesheet" href="files/css/slide.css" type="text/css" media="screen" />
	  	<link rel="stylesheet" href="files/css/general.css" type="text/css" media="screen" />
	  	<link rel="stylesheet" href="files/css/menu.css" type="text/css" media="screen" />
	  	<link rel="stylesheet" href="files/css/jquery.mCustomScrollbar.css" type="text/css" media="screen" />
	  	<link rel="stylesheet" href="files/css/com_dbmanager.css" type="text/css" media="all" />
	  	
	  	<!-- javascript -->

		<script type="text/javascript" src="files/js/jquery.jqtransform.js"></script>
		<script type="text/javascript" src="files/js/menu.js"></script>
		<script type="text/javascript" src="files/js/ajax_content.js"></script>
		<script type="text/javascript" src="files/js/jquery-ui.min.js"></script>
		<script type="text/javascript" src="files/js/jquery.easing.1.3.js"></script>
		<script type="text/javascript" src="files/js/jquery.mousewheel.min.js"></script>
		<script type="text/javascript" src="files/js/bootstrap-modal.js"></script>
		

		<!-- styles needed by jScrollPane -->
		<link type="text/css" href="files/css/jquery.jscrollpane.css" rel="stylesheet" media="all" />
		<!-- the mousewheel plugin - optional to provide mousewheel support -->
		<script type="text/javascript" src="files/js/jquery.mousewheel.js"></script>
		<!-- the jScrollPane script -->
		<script type="text/javascript" src="files/js/jquery.jscrollpane.min.js"></script>		
		
		<!-- PNG FIX for IE6 -->
	  	<!-- http://24ways.org/2007/supersleight-transparent-png-in-ie6 -->
		<!--[if lte IE 6]>
			<script type="text/javascript" src="files/js/pngfix/supersleight-min.js"></script>
		<![endif]-->
		<!-- Sliding effect -->
		<script src="files/js/slide.js" type="text/javascript"></script>
		 <!--[if lt IE 9]>
			<script type="text/javascript" src="files/js/html5.js"></script>
			<style type="text/css">
				.button1 {behavior: url(files/js/PIE.htc)}
			</style>
		<![endif]-->
		<!--[if lt IE 7]>
			<div style=' clear: both; text-align:center; position: relative;'>
				<a href="http://www.microsoft.com/windows/internet-explorer/default.aspx?ocid=ie6_countdown_bannercode"><img src="http://www.theie6countdown.com/images/upgrade.jpg" border="0" alt="" /></a>
			</div>
		<![endif]-->
</head>
<body id="page1">
<?php 
if(!$_SESSION['login']){ ?>
	<form id="login" action="files.php?tmpl=process&action=login" method="post">
		<?php if($_SESSION['message']){ ?>
			<span><?php echo $_SESSION['message'];unset($_SESSION['message']);?></span>
		<?php }?>
		<label>Username</label>
		<input type="text" name="username" >
		<label>Password</label>
		<input type="password" name="password">
		<button type="submit">Login</button>	
	</form>
<?php } else { ?>
<span id="message"></span>
<div id="database">
<div class="header">
	<label>File Manager
	<form  id="logout" action="files.php?tmpl=process&action=logout" method="post">
		<button id="logout_button" type="submit">Logout</button>
	</form>
	</label>
</div>
<div class="left" id="host">
	<div class="header"><label>Folders</label></div>
	<div id="folder_loading"><img src="files/ajax_preloader.gif" alt="loading" > </div>
	<ul class="tables" id="folders">
		<?php foreach($files->getFolders() as $folder){
		echo '<li id="'.$folder->inode.'">'.$folder->filename.'</li><button id="'.$folder->inode.'" class="btn danger delete">X</button>'."\n";}	?>
	</ul>
</div>
<div class="right">
	<div class="header"><label>Files</label></div>
	<ul class="tables_files" id="files">
		<?php
		$count = 0;
		foreach($files->getFiles() as $file){
			echo '<li id="row'.$file->inode.'">
					<input type="checkbox" id="'.$file->inode.'" name="file" value="'.$file->inode.'" >
					<label id="lbl'.$file->inode.'" for="'.$file->inode.'">'.$file->filename.'</label>
					<span class="actions">
						<button id="btn'.$file->inode.'" class="delete">X</button>
					</span>
					</li>'."\n";
			$count++;
		}?>
	</ul>
</div>
<div class="action">
<button class="delete">X</button>
</div>
<div class="stats"></div>
</div>
<script type="text/javascript" >
$(document).ready(function() {
	$('.tables').jScrollPane();
	$('.tables_files').jScrollPane();
});
</script>
<script src="files/js/jquery.mCustomScrollbar.js" type="text/javascript"></script>
<script src="files/js/script.js" type="text/javascript"></script>
<?php } ?>
</body>
	<div id="modal-from-dom" class="modal hide fade in">
     	<div class="modal-header">
         <a href="#" class="close">×</a>
      	<h3>Modal Heading</h3>
      </div>
      <div class="modal-body">
      	<p></p>
      </div>
      <div class="modal-footer">
         <button class="btn primary confirm">Confirm</button>
			<button class="btn secondary cancel">Cancel</button>
		</div>
	</div>
</html>
