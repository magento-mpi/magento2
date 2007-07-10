<?php
/**
 * Abstract message model
 *
 * @package     Mage
 * @subpackage  Core
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
abstract class Mage_Core_Model_Message_Abstract
{
    protected $_type;
    protected $_code;
    protected $_class;
    protected $_method;
    
    public function __construct($type, $code='')
    {
        $this->_type = $type;
        $this->_code = $code;
    }

    public function getCode()
    {
        return $this->_code;
    }
    
    public function getText()
    {
        return __($this->getCode());
    }
    
    public function getType()
    {
        return $this->_type;
    }

    public function setClass($class)
    {
        $this->_class = $class;
    }
    
    public function setMethod($method)
    {
        $this->_method = $method;
    }

    public function toString()
    {
        $out = $this->getType().': '.$this->getText();
        return $out;
    }
}