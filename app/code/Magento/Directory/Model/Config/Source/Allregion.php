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

class Allregion implements \Magento\Core\Model\Option\ArrayInterface
{
    /**
     * @var array
     */
    protected $_countries;

    /**
     * @var array
     */
    protected $_options;

    /**
     * @var \Magento\Directory\Model\Resource\Country\CollectionFactory
     */
    protected $_countryCollectionFactory;

    /**
     * @var \Magento\Directory\Model\Resource\Region\CollectionFactory
     */
    protected $_regionCollectionFactory;

    /**
     * @param \Magento\Directory\Model\Resource\Country\CollectionFactory $countryCollectionFactory
     * @param \Magento\Directory\Model\Resource\Region\CollectionFactory $regionCollectionFactory
     */
    public function __construct(
        \Magento\Directory\Model\Resource\Country\CollectionFactory $countryCollectionFactory,
        \Magento\Directory\Model\Resource\Region\CollectionFactory $regionCollectionFactory
    ) {
        $this->_countryCollectionFactory = $countryCollectionFactory;
        $this->_regionCollectionFactory = $regionCollectionFactory;
    }

    /**
     * @param bool $isMultiselect
     * @return array
     */
    public function toOptionArray($isMultiselect = false)
    {
        if (!$this->_options) {
            $countriesArray = $this->_countryCollectionFactory->create()->load()
                ->toOptionArray(false);
            $this->_countries = array();
            foreach ($countriesArray as $a) {
                $this->_countries[$a['value']] = $a['label'];
            }

            $countryRegions = array();
            $regionsCollection = $this->_regionCollectionFactory->create()->load();
            foreach ($regionsCollection as $region) {
                $countryRegions[$region->getCountryId()][$region->getId()] = $region->getDefaultName();
            }
            uksort($countryRegions, array($this, 'sortRegionCountries'));

            $this->_options = array();
            foreach ($countryRegions as $countryId => $regions) {
                $regionOptions = array();
                foreach ($regions as $regionId => $regionName) {
                    $regionOptions[] = array('label' => $regionName, 'value' => $regionId);
                }
                $this->_options[] = array('label' => $this->_countries[$countryId], 'value' => $regionOptions);
            }
        }
        $options = $this->_options;
        if (!$isMultiselect) {
            array_unshift($options, array('value' => '', 'label' => ''));
        }

        return $options;
    }

    /**
     * @param string $a
     * @param string $b
     * @return int
     */
    public function sortRegionCountries($a, $b)
    {
        return strcmp($this->_countries[$a], $this->_countries[$b]);
    }
}
