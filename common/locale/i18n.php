<?php

require_once('model/dao/i18ns.php');

class i18n
{
    public $value;
    public $is_image;
    public $pagelabel;
    public $category;
    public $name;
    public $labelId;
    
    public function get($label)
    {
         return i18ns::getInstance()->getTranslation($label); 
    }
}
?>