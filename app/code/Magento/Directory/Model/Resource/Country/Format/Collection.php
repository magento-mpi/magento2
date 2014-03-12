<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Directory
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Directory\Model\Resource\Country\Format;

/**
 * \Directory country format resource model
 *
 * @category    Magento
 * @package     Magento_Directory
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Collection extends \Magento\Core\Model\Resource\Db\Collection\AbstractCollection
{
    /**
     * Define main table
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magento\Directory\Model\Country\Format', 'Magento\Directory\Model\Resource\Country\Format');
    }

    /**
     * Set country filter
     *
     * @param string|\Magento\Directory\Model\Country $country
     * @return \Magento\Directory\Model\Resource\Country\Format\Collection
     */
    public function setCountryFilter($country)
    {
        if ($country instanceof \Magento\Directory\Model\Country) {
            $countryId = $country->getId();
        } else {
            $countryId = $country;
        }

        return $this->addFieldToFilter('country_id', $countryId);
    }
}
