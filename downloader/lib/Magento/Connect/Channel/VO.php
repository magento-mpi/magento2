<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Connect
 * @copyright   {copyright}
 * @license     {license_link}
 */


class \Magento\Connect\Channel\VO implements Iterator
{

    private $_validator = null;

    protected $properties = array(
        'name' => '',
        'uri' => '',
        'summary' => '',
    );

    public function rewind() {
        reset($this->properties);
    }

    public function valid() {
        return current($this->properties) !== false;
    }

    public function key() {
        return key($this->properties);
    }

    public function current() {
        return current($this->properties);
    }

    public function next() {
        next($this->properties);
    }

    public function __get($var)
    {
        if (isset($this->properties[$var])) {
            return $this->properties[$var];
        }
        return null;
    }

    public function __set($var, $value)
    {
        if (is_string($value)) {
            $value = trim($value);
        }
        if (isset($this->properties[$var])) {
            if ($value === null) {
                $value = '';
            }
            $this->properties[$var] = $value;
        }
    }

    public function toArray()
    {
        return array('channel' => $this->properties);
    }

    public function fromArray(array $arr)
    {
        foreach($arr as $k=>$v) {
            $this->$k = $v;
        }
    }


    private function validator()
    {
        if(is_null($this->_validator)) {
            $this->_validator = new \Magento\Connect\Validator();
        }
        return $this->_validator;
    }

    /**
     Stub for validation result
     */
    public function validate()
    {
        $v = $this->validator();
        if(!$v->validatePackageName($this->name)) {
            return false;
        }
        return true;
    }

}
