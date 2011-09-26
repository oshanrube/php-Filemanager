<?php

class Prefs {

	var $params;
	public function __construct() {
	}
	
	public static function getParam($field) {
		//username 
		$params['username'] = 'root';
		//password
		$params['password'] = 'oshan1991';
		$params['basedir'] = str_replace('fileNice','',dirname(__FILE__));
		$params['path'] = '';
		$params['exclude'] = array('.svn', 'CVS','.DS_Store','__MACOSX');
		$params['excludefilter'] = array('^\..*');
		$params['deletefilter'] = array('~$');
		$params['recurse'] = true;
		$params['full'] = true;
		return $params[$field];
	}
}

?>