#!/usr/local/php/bin/php
 
<?php
define('__ROOT__', '/webroot/sms.xwg.cc/');
define('__SCRIPT__', __ROOT__.'/Script/');
define('__LIB__', __ROOT__.'/Lib/');
define('__LOG__', __ROOT__.'/Log/');

function setLog($content)
{
	global $module;
	$log = __LOG__.'/'.$module.'.'.date('Ymd').'.log';
	file_put_contents($log, "(".date('Y-m-d H:i:s').") - ".$content."\n", FILE_APPEND);
}
 
/**
 * Method for displaying the help and default variables.
 **/
function displayUsage(){
    global $log;
 
    echo "\n";
    echo "Process for demonstrating a PHP daemon.\n";
    echo "\n";
    echo "Usage:\n";
    echo "\tDaemon.php [options]\n";
    echo "\n";
    echo "\toptions:\n";
    echo "\t\t--help display this help message\n";
	echo "\t\t--module={module} (router|sms|ios|android)\n";
	echo "\t\t--process={num} (Default:1)\n";
    echo "\n";
}//end displayUsage()
 
//configure command line arguments
$process_num = 1;
if($argc > 0){
    foreach($argv as $arg){
        $args = explode('=',$arg);
        switch($args[0]){
            case '--help':
                return displayUsage();
                break;
			case '--module':
                $module = $args[1];
				setLog($module.' Module Init.');
				break;
			case '--process':
				$process_num = intval($args[1]);
				setLog('Total: '.$process_num.' Processes');
				break;
        }//end switch
    }//end foreach
}//end if

if (!$module)
{
	die('No Module Appointed!');
}

$pid_file = '/var/run/'.$module.'.pid';

//multi process
for($i = 0; $i < $process_num; $i++)
{
	$pid[$i] = pcntl_fork();
	if($pid[$i] == -1)
	{
		setLog("Error: could not daemonize process.");
		return 1; //error
	}
	else if($pid[$i])
	{
		setLog('Process: '.$i.' PID: '.$pid[$i]);
		if ($i > 0)
		{
			$pid_str = file_get_contents($pid_file).' '.$pid[$i];
		}
		else
		{
			$pid_str = $pid[$i];
		}
		file_put_contents($pid_file, $pid_str);
	}
	else
	{
		//the main process
		while(true)
		{
			require(__SCRIPT__.'/'.$module.'.php');
			sleep(1);
		}
	}
}