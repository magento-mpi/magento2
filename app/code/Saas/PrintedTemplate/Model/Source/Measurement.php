<?php
/**
 * {license_notice}
 *
 * @category    Saas
 * @package     Saas_PrintedTemplate
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Measurements source model
 *
 * @category   Saas
 * @package    Saas_PrintedTemplate
 * @subpackage Models
 */
class Saas_PrintedTemplate_Model_Source_Measurement
{
    /**
     * Returns all available options with titles
     * List of available measurements from config
     *
     * @var array  Array
     * (
     *     [mellimeter] => Array
     *         (
     *             [label] => Millimeter
     *             [value] => 0
     *         )
     * @return array
     */
    public function toOptionArray()
    {
        $options = array();
        $measurement = $this->_getConfigModel()->getConfigSectionArray('measurements');

        foreach ($measurement as $key => $item) {
            $options[strtoupper($key)] = __($item['label']);
        }

        return $options;
    }

    /**
     * Returns Config model
     *
     * @return  Saas_PrintedTemplate_Model_Config
     */
    protected function _getConfigModel()
    {
        return Mage::getModel('Saas_PrintedTemplate_Model_Config');
    }

    /**
     * Returns Data helper
     *
     * @return  Saas_PrintedTemplate_Helper_Data
     */
    protected function _getHelper()
    {
        return Mage::helper('Saas_PrintedTemplate_Helper_Data');
    }
}
