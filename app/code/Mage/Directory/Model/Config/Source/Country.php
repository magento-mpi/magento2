<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Directory
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Mage_Directory_Model_Config_Source_Country implements Mage_Core_Model_Option_ArrayInterface
{
    /**
     * Countries
     *
     * @var Mage_Directory_Model_Resource_Country_Collection
     */
    protected $_countryCollection;

    /**
     * @param Mage_Directory_Model_Resource_Country_Collection $countryCollection
     */
    public function __construct(
        Mage_Directory_Model_Resource_Country_Collection $countryCollection
    ) {
        $this->_countryCollection = $countryCollection;
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
                'label' => __('--Please Select--'),
            ));
        }

        return $options;
    }
}
