<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Directory
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Magento_Directory_Model_Config_Source_Country implements Magento_Core_Model_Option_ArrayInterface
{
    /**
     * Countries
     *
     * @var Magento_Directory_Model_Resource_Country_Collection
     */
    protected $_countryCollection;

    /**
     * Directory Helper
     *
     * @var Magento_Directory_Helper_Data
     */
    protected $_directoryHelper;

    /**
     * @param Magento_Directory_Model_Resource_Country_Collection $countryCollection
     * @param Magento_Directory_Helper_Data $directoryHelper
     */
    public function __construct(
        Magento_Directory_Model_Resource_Country_Collection $countryCollection,
        Magento_Directory_Helper_Data $directoryHelper
    ) {
        $this->_countryCollection = $countryCollection;
        $this->_directoryHelper = $directoryHelper;
    }

    /**
     * Options array
     *
     * @var type
     */
    protected $_options;

    /**
     * Return options array
     *
     * @param boolean $isMultiselect
     * @param string|array $foregroundCountries
     * @return array
     */
    public function toOptionArray($isMultiselect = false, $foregroundCountries = '')
    {
        if (!$this->_options) {
            $this->_options = $this->_countryCollection
                ->loadData()
                ->setForegroundCountries($foregroundCountries)
                ->toOptionArray(false);
        }

        $options = $this->_options;
        if (!$isMultiselect) {
            array_unshift($options, array(
                'value' => '',
                'label' => $this->_directoryHelper->__('--Please Select--'),
            ));
        }

        return $options;
    }
}
