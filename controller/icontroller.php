<?php

require_once('model/sao/facebook/Facebook.php');

abstract class icontroller
{
    protected $action;
    protected $facebook;
    
    public function __construct() 
    {
        $this->action = self::getVar('atn', 'index');
    }  
    
    protected function getVar($key, $default = NULL, $scope = 'REQUEST')
    {
        $scope = strtoupper($scope);

        switch($scope) 
        {
            case 'POST'   : $value = isset($_POST[$key])    ? $_POST[$key]    : $default; break;
            case 'GET'    : $value = isset($_GET[$key])     ? $_GET[$key]     : $default; break;
            case 'SESSION': $value = isset($_SESSION[$key]) ? $_SESSION[$key] : $default; break;
            case 'FILES'  : $value = isset($_FILES[$key])   ? $_FILES[$key]   : $default; break;
            default       : $value = isset($_REQUEST[$key]) ? $_REQUEST[$key] : $default; break;
        }

        return $value;
    }
    
	protected function getJson($url)
	{
		$i    = 0;
		$data = new stdClass();
		
		do
		{
			if(($data = @file_get_contents($url)) !== false) 
			{ 
				$data = json_decode($data);
				break;	
			}
			sleep(1);
		} 
		while(++$i <= 10);

		return $data;
	}
	
	protected function json($data)
	{
		header('Content-Type: Application/json');
		print json_encode($data);
	}
	
	protected function connect($db)
	{
		if(!($connection = mysql_connect(DBServer,DBUser,DBPassword,true))) { throw new Exception("error: no datebase connection"); }
		if(!mysql_set_charset('utf8',$connection))                          { throw new Exception("error: set character set utf8 [".mysql_errno($connection)."][".mysql_error($connection)."]\n"); }
		if(!mysql_query("set character set utf8;",$connection))             { throw new Exception("error: set character set utf8 [".mysql_errno($connection)."][".mysql_error($connection)."]\n"); }
		if(!mysql_select_db($db,$connection))                               { throw new Exception("error: cannot change to database [$db] [".mysql_errno($connection)."][".mysql_error($connection)."]\n"); }
		
		return $connection;
	}
    
    protected function error()
    {
        echo 'o_O';
    }
}

?>
