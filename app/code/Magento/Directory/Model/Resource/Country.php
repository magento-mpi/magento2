<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Directory
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * \Directory Country Resource Model
 *
 * @category    Magento
 * @package     Magento_Directory
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Directory\Model\Resource;

class Country extends \Magento\Core\Model\Resource\Db\AbstractDb
{
    /**
     * Resource initialization
     *
     */
    protected function _construct()
    {
        $this->_init('directory_country', 'country_id');
    }

    /**
     * Load country by ISO code
     *
     * @param \Magento\Directory\Model\Country $country
     * @param string $code
     *
     * @throws \Magento\Core\Exception
     * 
     * @return \Magento\Directory\Model\Resource\Country
     */
    public function loadByCode(\Magento\Directory\Model\Country $country, $code)
    {
        switch (strlen($code)) {
            case 2:
                $field = 'iso2_code';
                break;

            case 3:
                $field = 'iso3_code';
                break;

            default:
                \Mage::throwException(__('Please correct the country code: %1.', $code));
        }

        return $this->load($country, $code, $field);
    }
}
