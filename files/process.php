<?php 
switch($_GET['action']) {
	case 'login':
		if(login($_POST['username'],$_POST['password'])) {
			$_SESSION['message'] = 'Login Success';
		} else {
			$_SESSION['message'] = 'Login Unsuccess';
		}
		break;
	case 'logout':
		if(logout()){			
			$_SESSION['message'] = 'Logout Success';
		} else {
			$_SESSION['message'] = 'Logout Unsuccess';
		}
		break;
	default:
		header("HTTP/1.0 404 Not Found");
		break;
}
$server = $_SERVER['HTTP_HOST']; 
$thisDir = $_SERVER['PHP_SELF']; 
$pathToHere = "http://$server$thisDir";
header('Location: '.$pathToHere);


function login($unm,$pwd) {
	if(Prefs::getParam('username') != $unm) {
		$_SESSION['login'] = false;
		return false;
	} elseif(Prefs::getParam('password') != $pwd) {
		$_SESSION['login'] = false;
		return false;
	} else {
		$_SESSION['login'] = true;
		return true;
	}
}
function logout() {
	$_SESSION['login'] = false;
	return true;
}

?>