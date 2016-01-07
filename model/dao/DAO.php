<?php

require_once("settings/db.php");

/**
 * @uses _Array
 * @abstract
 */
abstract class DAO
{
	/**
	 * @access private
	 * @var $map
	 */
	private $map;
	
	/**
	 * @access private
	 * @var $class
	 */
	private $class;
	
	/**
	 * @access private
	 * @var $connection
	 */
	private $connection;
	
	/**
	 *
	 */
	private $is_my_connection;
	
	/**
 	 * CONSTRUCT
 	 */
 	protected function __construct($class,$map = array(),$connection = NULL,$server = DBServer,$user = DBUser,$pass = DBPassword,$db = DBName)
	{
		$this->map = array();
		
		$this->class = $class;
		$obj = new $this->class();
		$class_vars = get_object_vars($obj);
		if(in_array("id",array_keys($class_vars))){ $this->map[lcfirst($this->class) . "Id"] = "id"; unset($class_vars["id"]); }
		foreach($class_vars as $key => $value) { $this->map[$key] = $key; }
		
		if(DAO::is_assoc($map)) { foreach($map as $key => $value){ $this->map[$key] = $value; } }
		
		if($connection == NULL)
		{
			$this->is_my_connection = true;
			$this->connection = mysql_connect($server,$user,$pass,true);
			if(!$this->connection) { throw new Exception("no connection to database server!!!".mysql_error()); }
			
			$this->setCharsetUTF8();
			if($db !== NULL) { $this->selectDB($db); }
		}
		else
		{
			$this->is_my_connection = false;
			$this->connection = $connection;
		}
	}
 	
	/**
	 * @access private
	 * @final
	 * @return void
	 */
 	final private function setCharsetUTF8()
 	{
 		mysql_set_charset('utf8',$this->connection);
 		$this->getResult('set character set utf8;');
 	}
 	
	/**
	 * selectDB
	 *
	 * @param String $db
	 * @return Flag
	 */
	protected function selectDB($db)
	{
		if(!mysql_select_db($db,$this->connection))
		{
			throw new Exception("cannot change to database[$db]!!!");
		}
	}
	
	/**
	 * returns customer Object
	 *
	 * @param String $tableName
	 * @return boolean
	 */
	protected function tableExists($tableName,$database = NULL)
	{
		$database = !empty($database) ? "`{$database}`." : "";
		return !($result = mysql_query("SELECT 1 FROM {$database}`$tableName`;",$this->connection)) ? false : true;
	}
	
	/**
	 * @param string $name
	 * @return bool
	 */
	protected function dbExists($name)
	{
		$rows = $this->getRows("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '$name'");
		return count($rows) > 0;
	}
	
	/**
	 * returns customer Object
	 *
	 * @param String $query
	 * @return $obj
	 */
	protected function getObj($query)
	{
		$objs = $this->getObjs($query);
		return (count($objs) > 0) ? $objs[0] : NULL;
	}
	
	/**
	 * returns array of customer Objects
	 *
	 * @param String $query
	 * @return $objs[]
	 */
	protected function getObjs($query)
	{
		$objs = array();
		$rows = $this->getRows($query);
		
		if(count($rows) == 0) return $objs;
		
		foreach($rows as $row)
			$objs[] = $this->toObj($row);
		return $objs;
	}
	
	/**
	 * @param mixed $row
	 * @return $obj
	 */
	protected function toObj($row)
	{
		$obj = new $this->class();
		foreach($this->map as $key => $value) { $obj->$value = isset($row[$key]) ? $row[$key] : NULL; }
		return $obj;
	}
	
	/**
	 * returns first row of a query
	 *
	 * @param String $query
	 * @return $row[0]
	 */
	protected function getRow($query)
	{
		$rows = $this->getRows($query);
		return (count($rows) > 0) ? $rows[0] : NULL;
	}
	
	/**
	 * returns assiciative array from query
	 *
	 * @param String $query
	 * @return $rows
	 */
	protected function getRows($query)
	{
		$result = $this->getResult($query);
		if(mysql_num_rows($result) == 0){ return array(); }
		
		$rows = array();
		while($row = mysql_fetch_array($result,MYSQL_ASSOC)){ $rows[] = $row; }
		
		mysql_free_result($result);
		
		return $rows;
	}
	
	/**
	 * returns mysql_affected_rows
	 *
	 * @param String $query
	 * @return mysql_affected_rows
	 */
	protected function set($query)
	{
		$result = $this->getResult($query);
		return mysql_insert_id($this->connection);
	}
	
	/**
	 * returns result of mysql_query
	 *
	 * @access private
	 * @final
	 * @param String $query
	 * @return $result
	 */
	final private function getResult($query)
	{
		$result = mysql_query($query,$this->connection);
		if(!$result) { throw new Exception("[".mysql_errno($this->connection)."][".mysql_error($this->connection)."][$query]"); }

		return $result;
	}
	
	/**
	 * @access protected
	 * @param mixed $obj
	 * @return mixed $obj
	 */
	protected function clean($obj)
	{
		if(is_object($obj))
		{
			foreach ($obj as $name => $value) { $obj->$name = $this->clean($value); }
		}
		else if(DAO::is_assoc($obj))
		{
			$result = array();
			foreach($obj as $key => $value) { $result[$this->clean($key)] = $this->clean($value); }
			$obj = $result;
		}
		else if(is_array($obj))
		{
			foreach($obj as &$value) { $value = $this->clean($value); }
		}
		else if(is_string($obj))
		{
			$obj = mysql_real_escape_string($obj,$this->connection);
		}
		return $obj;
	}
	
	/**
	 * @param mixed $var
	 * @return Bool $is_assoc
	 */
	private static function is_assoc($var)
	{
		return is_array($var) && array_diff_key($var,array_keys(array_keys($var)));
	}

	protected function between($field, $from, $to)
	{
	    if(empty($from) || empty($to))
	    {
	        return;
	    }
	    
	    $from = date('Y-m-d', strtotime($from));
	    $to = date('Y-m-d', strtotime($to));
	    
	    $where  = " AND {$field} between '{$from}' and '{$to}'";
	    
	    return $where;
	}
	
	/**
	 * @access paublic
	 * @throws Exception
	 */
	public function __destruct()
	{
		if($this->is_my_connection == true && isset($this->connection))
		{
			if(!mysql_close($this->connection)) { throw new Exception("could not close connection to DB Server"); }
		}
	}

}

?>
