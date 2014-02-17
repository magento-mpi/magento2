<?php
/**
 * \Reflection based plugin method list. Uses reflection to retrieve list of interception methods defined in plugin.
 * Should be only used in development mode, because it reads method list on every request which is expensive.
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Interception\Definition;

use Magento\Interception\Definition;

class Runtime implements Definition
{
    /**
     * @var array
     */
    protected $_typesByPrefixes = array(
        'befor' => 0, 'aroun' => 1, 'after' => 2
    );

    /**
     * Plugin method service prefix lengths
     *
     * @var array
     */
    protected $prefixLengths = array(6, 6, 5);

    /**
     * Retrieve list of methods
     *
     * @param string $type
     * @return string[]
     */
    public function getMethodList($type)
    {
        $methods = array();
        foreach(get_class_methods($type) as $method) {
            $prefix = substr($method, 0, 5);
            if (isset($this->_typesByPrefixes[$prefix])) {
                $methods[substr($method, $this->prefixLengths[$this->_typesByPrefixes[$prefix]])]
                    = $this->_typesByPrefixes[$prefix];
            }
        }
        return $methods;
    }
}
