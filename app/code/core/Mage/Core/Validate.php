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
     * Get valid data
     *
     * @return array
     */
    public function getData()
    {
        return $this->_data;
    }
    
    /**
     * Data validation
     */
    public function isValid() 
	{
		return false;
	}
    
	/**
	 * Get validation result message
	 *
	 * @param  string $format
	 * @return string
	 */
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
	
	/**
	 * Get validation object
	 *
	 * @param  string $type
	 * @param  string $class
	 * @return Zend_Validate
	 */
	protected function _getValidator($type, $class = '')
	{
	    if (empty($class)) {
	        $class = 'Zend_Validate_'.ucfirst(strtolower($type));
	    }
	    return new $class;
	}
	
	/**
	 * Prepare array keys
	 *
	 * @param  array $arr
	 * @param  array $keys
	 * @return array
	 */
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
