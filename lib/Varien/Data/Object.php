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
    protected $_data;
    
    public function __construct($data = array())
    {
        $this->_data = $data;
    }

    public function setData($key, $value='')
    {
        if(is_array($key)) {
            foreach($key as $index=>$value) {
                $this->_data[$index] = $value;
            }
        } else {
            $this->_data[$key] = $value;
        }
    }
    
    public function getData($key='')
    {
        if (''===$key) {
            return $this->_data;
        } elseif (isset($this->_data[$key])) {
            return $this->_data[$key];
        }
        return false;
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
                //$key = strtolower(substr($method,3));
                $key = $this->_underscore(substr($method,3));
                if (isset($this->_data[$key])) {
                    return $this->_data[$key];
                } else {
                    return null;
                }
                break;

            case 'set' :
                //$key = strtolower(substr($method,3));
                $key = $this->_underscore(substr($method,3));
                if (isset($args[0])) {
                    $this->_data[$key] = $args[0];
                }
                return $this;
                break;
        }
        throw new Exception("Invalid method ".get_class($this)."::".$method."(".print_r($args,1).")");
    }
    
    public function __get($var)
    {
        $var = $this->_underscore($var);
        return isset($this->_data[$var]) ? $this->_data[$var] : null;
    }
    
    public function __set($var, $value)
    {
        $var = $this->_underscore($var);
        $this->_data[$var] = $value;
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
}