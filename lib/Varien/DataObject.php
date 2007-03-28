<?php

/**
 * Varien Data Object
 *
 * @package    Varien
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Varien_DataObject
{
    protected $_data;
    
    public function __construct($data = array())
    {
        $this->_data = $data;
    }

    public function setData($key, $value)
    {
        $this->_data[$key] = $value;
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
                $key = strtolower(substr($method,3));
                if (isset($this->_data[$key])) {
                    return $this->_data[$key];
                } else {
                    return null;
                }
                break;

            case 'set' :
                $key = strtolower(substr($method,3));
                if (isset($args[0])) {
                    $this->_data[$key] = $args[0];
                }
                return $this;
                break;
        }
    }
}