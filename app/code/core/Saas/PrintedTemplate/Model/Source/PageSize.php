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
 * Page size source model
 *
 * @category   Saas
 * @package    Saas_PrintedTemplate
 * @subpackage Models
 */
class Saas_PrintedTemplate_Model_Source_PageSize
{
    /**
     * List of sizes from config
     *
     * @var array  Array
     * (
     *     [a4] => Array
     *         (
     *             [label] => A4
     *             [height] => 279
     *             [width] => 210
     *             [unit] => millimeters
     *         )
     */
    protected $_source;

    /**
     * Initializes source
     */
    public function __construct()
    {
        $this->_source = Mage::getModel('Saas_PrintedTemplate_Model_Config')->getConfigSectionArray('page_size');
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
            $options[$key] = Mage::helper('Saas_PrintedTemplate_Helper_Data')->__($item['label']);
        }

        return $options;
    }

    /**
     * Creates size model from array
     *
     * @param string $name Name of format
     * @param array $sizeInfo
     *     [a4] => Array
     *         (
     *             [label] => A4
     *             [unit]  => inch
     *             [height] => 8.5
     *             [width] => 11
     *         )
     * @return Saas_PrintedTemplate_Model_PageSize
     */
    protected function _createSize($name, array $sizeInfo)
    {
        $config = array('name' => $name);
        $unit = isset($sizeInfo['unit'])
            ? strtoupper($sizeInfo['unit'])
            : Zend_Measure_Length::MILLIMETER;

        if (isset($sizeInfo['width'])) {
            $config['width'] = new Zend_Measure_Length($sizeInfo['width'], $unit, 'en_US');
        }
        if (isset($sizeInfo['height'])) {
            $config['height'] = new Zend_Measure_Length($sizeInfo['height'], $unit, 'en_US');
        }

        return Mage::getModel('Saas_PrintedTemplate_Model_PageSize', array('sizeInfo' => $config));
    }

    /**
     * Returns size by name
     *
     * @param string $name
     * @return Saas_PrintedTemplate_Model_PageSize
     * @throws InvalidArgumentException
     */
    public function getSizeByName($name)
    {
        if (!isset($this->_source[$name])) {
            throw new InvalidArgumentException('Incorrect size code.');
        }

        return $this->_createSize($name, $this->_source[$name]);
    }
}
