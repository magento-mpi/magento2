<?php

/**
 * Varien Object
 *
 * @package    Varien
 * @subpackage Data
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @author     Andrey Korolyov <andrey@varien.com>
 * @author     Moshe Gurvich <moshe@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */

class Varien_Object
{
    /**
     * Object attributes
     *
     * @var array
     */
    protected $_data = array();
    
    /**
     * Caching flag
     *
     * @var boolean
     */
    protected $_isChanged = false;
    
    /**
     * Deleting flag
     *
     * @var boolean
     */
    protected $_isDeleted = false;
    
    /**
     * Seter/Geter underscore transformation cache
     *
     * @var array
     */
    protected static $_underscoreCache = array();
    
    public function __construct()
    {
        $args = func_get_args();
        if (empty($args[0])) {
            $args[0] = array();
        }
        $this->_data = $args[0];
    }
    
    public function setIsChanged($changed=false)
    {
        $this->_isChanged = $changed;
        return $this;
    }
    
    public function isChanged()
    {
        return $this->_isChanged;
    }
    
    public function setIsDeleted($deleted=false)
    {
        $this->_isDeleted = $deleted;
        return $this;
    }
    
    public function isDeleted()
    {
        return $this->_isDeleted;
    }
    
    public function addData($arr)
    {
        foreach($arr as $index=>$value) {
            $this->setData($index, $value);
        }
        return $this;
    }

    public function setData($key, $value='', $isChanged=true)
    {
        if ($isChanged) {
            $this->setIsChanged(true);
        }
        
        if(is_array($key)) {
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
                $value = $this->_data[$key];
                if (is_array($value)) {
                    return (!empty($value[$index])) ? $value[$index] : false;
                } elseif (is_string($value)) {
                    $arr = explode("\n", $value);
                    return (!empty($arr[$index])) ? $arr[$index] : false;
                } elseif ($value instanceof Varien_Object) {
                    return $value->getData($index);
                }
                return null;
            }
            return $this->_data[$key];
        }
        return null;
    }
    
    public function hasData($key='')
    {
        if (empty($key) || !is_string($key)) {
            return false;
        }
        return isset($this->_data[$key]);
    }

    /**
     * Convert object attributes to array
     * 
     * @param  array $arrAttributes array of required attributes
     * @return array
     */
    protected function __toArray($arrAttributes = array())
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

    public function toArray($arrAttributes = array())
    {
        return $this->__toArray($arrAttributes);
    }
    
    /**
     * Set required array elements
     *
     * @param   array $arr
     * @param   array $elements
     * @return  array
     */
    protected function _prepareArray(&$arr, $elements=array())
    {
        foreach ($elements as $element) {
            if (!isset($arr[$element])) {
                $arr[$element] = null;
            }
        }
        return $arr;
    }

    /**
     * Convert object attributes to XML
     *
     * @param  array $arrAttributes array of required attributes
     * @return string
     */
    protected function __toXml($arrAttributes = array(), $rootName = 'item')
    {
        $xml = '<'.$rootName.'>';
        $arrData = $this->toArray($arrAttributes);
        foreach ($arrData as $fieldName => $fieldValue) {
            $xml.= "<$fieldName><![CDATA[$fieldValue]]></$fieldName>";
        }
        $xml.= '</'.$rootName.'>';
        return $xml;
    }

    public function toXml($arrAttributes = array(), $rootName = 'item')
    {
        return $this->__toXml($arrAttributes, $rootName);
    }
    
    /**
     * Convert object attributes to JSON
     *
     * @param  array $arrAttributes array of required attributes
     * @return string
     */
    protected function __toJson($arrAttributes = array())
    {
        $arrData = $this->toArray($arrAttributes);
        $json = Zend_Json::encode($arrData);
        return $json;
    }

    public function toJson($arrAttributes = array())
    {
        return $this->__toJson($arrAttributes);
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
        $arrData = $this->toArray($arrAttributes);
        return implode($valueSeparator, $arrData);
    }

    public function toString($format='')
    {
        if (empty($format)) {
            $str = implode(', ', $this->getData());
        } else {
            preg_match_all('/\{\{([a-z0-9_]+)\}\}/is', $format, $matches);
            foreach ($matches[1] as $var) {
                $format = str_replace('{{'.$var.'}}', $this->getData($var), $format);
            }
            $str = $format;
        }
        return $str;
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
        $this->_isChanged = true;
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
        if (isset(self::$_underscoreCache[$name])) {
            return self::$_underscoreCache[$name];
        }
        $result = strtolower(preg_replace('/(.)([A-Z])/', "$1_$2", $name));
        self::$_underscoreCache[$name] = $result;
        
        return $result;
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
