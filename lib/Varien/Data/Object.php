<?php

/**
 * Varien Data Object
 *
 * @package    Varien
 * @subpackage Data
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @author     Andrey Korolyov <andrey@varien.com>
 * @author     Moshe Gurvich <moshe@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */

class Varien_Data_Object
{
    protected $_data = array();
    protected $_isChanged = false;
    
    public function __construct($data = array())
    {
        $this->_data = $data;
    }
    
    public function resetChanged($changed=false)
    {
        $this->_isChanged = $changed;
        return $this;
    }
    
    public function isChanged()
    {
        return $this->_isChanged;
    }
    
    public function addData($arr)
    {
        foreach($arr as $index=>$value) {
            $this->_data[$index] = $value;
        }
        return $this;
    }

    public function setData($key, $value='', $isChanged=true)
    {
        if ($isChanged) {
            $this->resetChanged(true);
        }
        
        if(is_array($key)) {
            /*
            foreach($key as $index=>$value) {
                $this->_data[$index] = $value;
            }
            */
            $this->_data = $key;
        } else {
            $this->_data[$key] = $value;
        }
        return $this;
    }
    
    public function getData($key='', $index=false)
    {
        if (''===$key) {
            return $this->_data;
        } elseif (isset($this->_data[$key])) {
            if ($index) {
                return (is_array($this->_data[$key]) && !empty($this->_data[$key][$index])) ? $this->_data[$key][$index] : null;
            }
            return $this->_data[$key];
        }
        return null;
    }
    
    public function hasData($key='')
    {
        return isset($this->_data[$key]);
    }

    /**
     * Convert object attributes to array
     * 
     * @param  array $arrAttributes array of required attributes
     * @return array
     */
    public function __toArray($arrAttributes = array())
    {
        if (empty($arrAttributes)) {
            return $this->_data;
        }
        
        $arrRes = array();
        foreach ($arrAttributes as $attribute) {
            if (isset($this->_data[$attribute])) {
                $arrRes[$attribute] = $this->_data[$attribute];
            }
            else {
                $arrRes[$attribute] = null;
            }
        }
        return $arrRes;
    }

    /**
     * Convert object attributes to XML
     *
     * @param  array $arrAttributes array of required attributes
     * @return string
     */
    public function __toXml($arrAttributes = array(), $rootName = 'item')
    {
        $xml = '<'.$rootName.'>';
        $arrData = $this->__toArray($arrAttributes);
        foreach ($arrData as $fieldName => $fieldValue) {
            $xml.= "<$fieldName><![CDATA[$fieldValue]]></$fieldName>";
        }
        $xml.= '</'.$rootName.'>';
        return $xml;
    }
    
    /**
     * Convert object attributes to JSON
     *
     * @param  array $arrAttributes array of required attributes
     * @return string
     */
    public function __toJson($arrAttributes = array())
    {
        $arrData = $this->__toArray($arrAttributes);
        $json = Zend_Json::encode($arrData);
        return $json;
    }

    /**
     * Convert object attributes to string
     * 
     * @param  array  $arrAttributes array of required attributes
     * @param  string $valueSeparator
     * @return string
     */
    public function __toString($arrAttributes = array(), $valueSeparator=',')
    {
        $arrData = $this->__toArray($arrAttributes);
        return implode($valueSeparator, $arrData);
    }
    
    /**
     * Set/Get attribute wrapper
     *
     * @param   string $method
     * @param   array $args
     * @return  mixed
     */
    public function __call($method, $args) 
    {
        switch (substr($method, 0, 3)) {
            case 'get' :
                $key = $this->_underscore(substr($method,3));
                array_unshift($args, $key);
                return call_user_func_array(array($this, 'getData'), $args);
                break;

            case 'set' :
                $key = $this->_underscore(substr($method,3));
                array_unshift($args, $key);
                return call_user_func_array(array($this, 'setData'), $args);
                return $this;
                break;
                
            case 'has' :
                $key = $this->_underscore(substr($method,3));
                return isset($this->_data[$key]);
                break;
        }
        throw new Varien_Exception("Invalid method ".get_class($this)."::".$method."(".print_r($args,1).")");
    }
    
    public function __get($var)
    {
        $var = $this->_underscore($var);
        return $this->getData($var);
    }
    
    public function __set($var, $value)
    {
        $this->resetChanged(true);
        $var = $this->_underscore($var);
        $this->setData($var, $value);
    }
    
    public function isEmpty()
    {
        if(empty($this->_data)) {
            return true;
        }
        return false;
    }
    
    protected function _underscore($name)
    {
        return strtolower(preg_replace('/([a-z])([A-Z])/', "$1_$2", $name));
    }
    
    public function __sleep()
    {
       return array_keys( (array)$this );
    }

    public function __wakeup()
    {
        $this->_isChanged = false;
    }
}
