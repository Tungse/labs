<?php

	class BO
	{
		protected static $dao = "";
		
		public function __set($property,$value)
		{
			$property = $this->parseProperty($property);
			$this->$property = $value;
		}
		
		public function __get($property)
		{
			$property = $this->parseProperty($property);
			return $this->$property;
		}
		
		public function parseProperty($property)
		{
			$property = strtolower($property);
			$class_vars = get_class_vars(get_class($this));
			
			foreach ($class_vars as $name => $value)
				if($property === strtolower($name)) return $name;
			throw new Exception("property [$property] belongs not to the class ".get_class($this));
		}
		
		public static function __callstatic($name, $arguments)
		{
			$dao = !empty(static::$dao) ? static::$dao : get_called_class() . "s";
			
			if($name == "connection"){ $dao::getInstance($arguments[0]); return; }
			
			$dao = $dao::getInstance();
			return call_user_func_array(array($dao, $name),$arguments);
		}
	}
	
?>