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

class VO implements Iterator
{
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
		    return $this->properties;
		}

}


