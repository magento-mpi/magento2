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
     * List of available measurements from config
     *
     * @var array  Array
     * (
     *     [mellimeter] => Array
     *         (
     *             [label] => Millimeter
     *             [value] => 0
     *         )
     */
    protected $_source;

    /**
     * Initializes source
     */
    public function __construct()
    {
        $this->_source = Mage::getModel('Saas_PrintedTemplate_Model_Config')->getConfigSectionArray('measurements');
    }

    /**
     * Returns all available options with titles
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = array();
        foreach ($this->_source as $key => $item) {
            $options[strtoupper($key)] = Mage::helper('Saas_PrintedTemplate_Helper_Data')->__($item['label']);
        }

        return $options;
    }
}
