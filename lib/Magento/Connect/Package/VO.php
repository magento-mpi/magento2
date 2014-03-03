<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Connect
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Connect\Package;

class VO implements \Iterator
{
    /**
     * @var array
     */
    protected $properties = array(
        'name' => '',
        'package_type_vesrion' => '',
        'cahnnel' => '',
        'extends' => '',
        'summary' => '',
        'description' => '',
        'authors' => '',
        'date' => '',
        'time' => '',
        'version' => '',
        'stability' => 'dev',
        'license' => '',
        'license_uri' => '',
        'contents' => '',
        'compatible' => '',
        'hotfix' => ''
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
     * @return string
     */
    public function key() {
        return key($this->properties);
    }

    /**
     * @return string
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
     * @param null|string $value
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
        return $this->properties;
    }

}


