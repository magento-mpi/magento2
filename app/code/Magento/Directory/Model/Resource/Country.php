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
 * Directory Country Resource Model
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
     * @return \Magento\Directory\Model\Resource\Country
     * @throws \Magento\Core\Exception
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
                throw new \Magento\Core\Exception(__('Please correct the country code: %1.', $code));
        }

        return $this->load($country, $code, $field);
    }
}
