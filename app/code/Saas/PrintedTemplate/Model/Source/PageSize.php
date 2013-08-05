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
     * Returns all available options with titles
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = array();
        $source = $this->_getSource();

        foreach ($source as $key => $item) {
            $options[$key] = __($item['label']);
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

        return $this->_getPageSizeModel($config);
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
        $source = $this->_getSource();

        if (!isset($source[$name])) {
            throw new InvalidArgumentException('Incorrect size code.');
        }

        return $this->_createSize($name, $source[$name]);
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
     * Returns PageSize model
     *
     * @return  Saas_PrintedTemplate_Model_PageSize
     */
    protected function _getPageSizeModel($config)
    {
        return Mage::getModel('Saas_PrintedTemplate_Model_PageSize', array('sizeInfo' => $config));
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

    /**
     * Returns $this->_source with page sizes
     *
     * @return  $this->_source
     */
    protected function _getSource()
    {
        if (!$this->_source) {
            $this->_source = $this->_getConfigModel()->getConfigSectionArray('page_size');
        }

        return $this->_source;
    }
}
