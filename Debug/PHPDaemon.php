<?PHP
//Define the LIB & SCRIPT path within your webroot, so that you can debug this script in your broswer
define('__LIB__', '/webroot/Daemon/Lib/');
define('__SCRIPT__', '/webroot/Daemon/Script/');

//Rewrite setLog function
function setLog($content) {
	echo $content;
}

require_once(__SCRIPT__.'/PHPDaemon.php');