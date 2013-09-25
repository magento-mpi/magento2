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
class Magento_Directory_Model_Resource_Country extends Magento_Core_Model_Resource_Db_Abstract
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
     * @param Magento_Directory_Model_Country $country
     * @param string $code
     * @return Magento_Directory_Model_Resource_Country
     * @throws Magento_Core_Exception
     */
    public function loadByCode(Magento_Directory_Model_Country $country, $code)
    {
        switch (strlen($code)) {
            case 2:
                $field = 'iso2_code';
                break;

            case 3:
                $field = 'iso3_code';
                break;

            default:
                throw new Magento_Core_Exception(__('Please correct the country code: %1.', $code));
        }

        return $this->load($country, $code, $field);
    }
}
