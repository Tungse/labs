<?php

ini_set("display_errors","on");
ini_set("memory_limit", "256M");

date_default_timezone_set('Europe/Berlin');
set_time_limit(-1);

require_once('settings/app.php');
require_once('settings/settings.php');
require_once('controller/icontroller.php');

final class controllers extends icontroller
{
	public function __construct()
	{
		switch(parent::getVar('ctl'))
		{
			case 'instagram' : require_once('controller/instagram/index.php'); break;
			default          : require_once('controller/instagram/index.php'); break;
		}
		
		new controller();
	}
}

new controllers();

?>