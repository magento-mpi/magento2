<?php
/**
 * DB configuration data converter. Converts associative array to tree array
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Core_Model_Config_Section_Converter implements Magento_Config_ConverterInterface
{
    /**
     * Convert config data
     *
     * @param array $source
     * @return array
     */
    public function convert($source)
    {
        $output = array();
        foreach ($source as $key => $value) {
            $this->_setArrayValue($output, $key, $value);
        }
        return $output;

    }

    /**
     * Set array value by path
     *
     * @param array $container
     * @param string $path
     * @param string $value
     */
    protected function _setArrayValue(array &$container, $path, $value)
    {
        $segments = explode('/', $path);
        $currentPointer = &$container;
        foreach ($segments as $segment) {
            if (!isset($currentPointer[$segment])) {
                $currentPointer[$segment] = array();
            }
            $currentPointer = &$currentPointer[$segment];
        }
        $currentPointer = $value;
    }
}
