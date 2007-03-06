<?php

abstract class Mage_Core_Resource_Entity_Abstract
{
    protected $_name = null;
    protected $_data = array();
    
    public function __construct(array $data=array())
    {
        if (!empty($data) && is_array($data)) {
            foreach ($data as $k=>$v) {
                $this->setData($k, $v);
            }
        }
    }
    
    public function setData($key, $value)
    {
        $this->_data[$key] = $value;
    }
    
    public function getData($key='')
    {
        if (''===$key) {
            return $this->_data;
        } elseif (isset($this->_data[$key])) {
            return $this->_data[$key];
        } else {
            return false;
        }
    }    
}