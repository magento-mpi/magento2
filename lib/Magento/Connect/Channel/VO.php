<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Connect
 * @copyright   {copyright}
 * @license     {license_link}
 */


namespace Magento\Connect\Channel;

use Magento\Connect\Validator;

class VO implements \Iterator
{
    /**
     * @var Validator
     */
    private $_validator = null;

    /**
     * @var array
     */
    protected $properties = array(
        'name' => '',
        'uri' => '',
        'summary' => '',
    );

    /**
     * @return void
     */
    public function rewind() {
        reset($this->properties);
    }

    /**
     * @return bool
     */
    public function valid() {
        return current($this->properties) !== false;
    }

    /**
     * @return mixed
     */
    public function key() {
        return key($this->properties);
    }

    /**
     * @return mixed
     */
    public function current() {
        return current($this->properties);
    }

    /**
     * @return void
     */
    public function next() {
        next($this->properties);
    }

    /**
     * @param string $var
     * @return null|string
     */
    public function __get($var)
    {
        if (isset($this->properties[$var])) {
            return $this->properties[$var];
        }
        return null;
    }

    /**
     * @param string $var
     * @param string|null $value
     * @return void
     */
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

    /**
     * @return array
     */
    public function toArray()
    {
        return array('channel' => $this->properties);
    }

    /**
     * @param array $arr
     * @return void
     */
    public function fromArray(array $arr)
    {
        foreach($arr as $k=>$v) {
            $this->$k = $v;
        }
    }

    /**
     * @return Validator
     */
    private function validator()
    { 
        if(is_null($this->_validator)) {
            $this->_validator = new Validator();
        }
        return $this->_validator;
    }
    
    /**
     * Stub for validation result
     *
     * @return bool
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
