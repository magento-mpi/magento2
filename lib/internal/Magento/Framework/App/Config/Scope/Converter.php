<?php
/**
 * Configuration data converter. Converts associative array to tree array
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Framework\App\Config\Scope;

class Converter implements \Magento\Framework\Config\ConverterInterface
{
    /**
     * Convert config data
     *
     * @param array $source
     * @return array
     */
    public function convert($source)
    {
        $output = [];
        foreach ($source as $key => $value) {
            $this->_setArrayValue($output, $key, $value);
        }
        return $output;
    }

    /**
     * Set array value by path
     *
     * @param array &$container
     * @param string $path
     * @param string $value
     * @return void
     */
    protected function _setArrayValue(array &$container, $path, $value)
    {
        $segments = explode('/', $path);
        $currentPointer = & $container;
        foreach ($segments as $segment) {
            if (!isset($currentPointer[$segment])) {
                $currentPointer[$segment] = [];
            }
            $currentPointer = & $currentPointer[$segment];
        }
        $currentPointer = $value;
    }
}
