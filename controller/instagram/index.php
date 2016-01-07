<?php

require_once('common/filter.php');

class controller extends icontroller 
{
    public function __construct() 
    {
        parent::__construct(__DIR__, __FILE__);

        method_exists($this, $this->action) ? $this->{$this->action}() : $this->index();
    }
    
    private function index()
    {
		require_once('view/instagram/index.php');
    }
    
    private function tags()
    {
    	$tags       = parent::getVar('tags');
		$pagination = (isset($tags['pagination']['next_url'])) ? $tags['pagination']['next_url'] : NULL;
		$data       = (isset($tags['data'])) ? $tags['data'] : array();
		$error      = (empty($data)) ? 'no result' : NULL;
		$error      = (isset($tags['meta']['error_message'])) ? $tags['meta']['error_message'] : $error;
    	$instagrams = array();
    	
    	try 
    	{
			foreach($data as $id => $value)
			{
				$instagram            = new stdClass();
				$instagram->image     = (isset($value['images']['thumbnail']['url'])) ? $value['images']['thumbnail']['url'] : NULL;
				$instagram->url       = (isset($value['link'])) ? $value['link'] : NULL;
				$instagram->userName  = (isset($value['user']['username'])) ? $value['user']['username'] : NULL;
				$instagram->userImage = (isset($value['user']['profile_picture'])) ? $value['user']['profile_picture'] : NULL;
				$instagram->datetime  = (isset($value['caption']['created_time'])) ? $this->datetime(intval($value['caption']['created_time'])) : NULL;
				
				$instagrams[] = $instagram;
			}
    	}
    	catch(Exception $e) {}

		require_once('view/instagram/index.tags.php');
    }
    
	private function datetime($datetime)
    {
    	if(empty($datetime)) return;
    	
    	$seconds = time() - $datetime;
    	
    	if(empty($seconds)) return;
	    if($seconds < 60)   return (int)$seconds.'s';
	    
	    $minutes = floor($seconds/60);	    
	    if($minutes < 60) return $minutes.'m';
	    
	    $hours = floor($minutes / 60);
	    if($hours < 24) return $hours.'h';
		
	    return date('j M', $datetime);
    }
    
    private function cutString($string)
    {
    	$string = (strlen($string) > 18) ? substr($string,0,18).'..' : $string;
    	
    	return $string;
    }
    
    private function filter()
    {
    	$input = Filter::run(strip_tags(parent::getVar('input')));  	
    	$find  = array(' ', '#', '?', '&', '.', '!', '"', '§', '$', '%', '(', ')', '=', '*', '+', '~', '-', ':', ';', '>');
    	$input = str_replace($find, '', $input);
    	
    	echo $input;
    }
}

?>
