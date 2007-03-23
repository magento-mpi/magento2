<?php
/**
 * Core abstract validation class
 * 
 * @package    Ecom
 * @subpackage Core
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
abstract class Mage_Core_Validate
{
    protected $_data;
    protected $_message;
    
    public function __construct($data) 
    {
        $this->_data = $data;
    }
    
    /**
     * Data validation
     */
    public function isValid() 
	{
		return false;
	}
    
    public function getMessage($format='string') 
    {
        switch ($format) {
        	case 'json':
        		$message = array('message'=>$this->_message);
        		return Zend_Json_Encoder::encode($message);
        		break;
        
        	default:
        	    return $this->_message;
        		break;
        }
	}
	
	protected function _getValidator($type, $class = '')
	{
	    if (empty($class)) {
	        $class = 'Zend_Validate_'.ucfirst(strtolower($type));
	    }
	    return  new $class;
	}
	
	protected function _prepareArray($arr, $keys)
	{
	    $arrRes = array();
	    foreach ($keys as $key) {
	        if (!isset($arr[$key])) {
	            $arrRes[$key] = null;
	        }
	        else {
	            $arrRes[$key] = $arr[$key];
	        }
	    }
	    return $arrRes;
	}
}
