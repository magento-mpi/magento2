<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Directory
 * @copyright   {copyright}
 * @license     {license_link}
 */


namespace Magento\Directory\Model\Config\Source;

class Country implements \Magento\Core\Model\Option\ArrayInterface
{
    /**
     * Countries
     *
     * @var \Magento\Directory\Model\Resource\Country\Collection
     */
    protected $_countryCollection;

    /**
     * @param \Magento\Directory\Model\Resource\Country\Collection $countryCollection
     */
    public function __construct(\Magento\Directory\Model\Resource\Country\Collection $countryCollection)
    {
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
