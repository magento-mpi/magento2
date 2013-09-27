<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Customer
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Customer Address Postal/Zip Code Attribute Data Model
 * This Data Model Has to Be Set Up in additional EAV attribute table
 *
 * @category    Magento
 * @package     Magento_Customer
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Customer_Model_Attribute_Data_Postcode extends Magento_Eav_Model_Attribute_Data_Text
{
    /**
     * Validate postal/zip code
     * Return true and skip validation if country zip code is optional
     *
     * @param array|string $value
     * @return boolean|array
     */
    /**
     * Directory data
     *
     * @var Magento_Directory_Helper_Data
     */
    protected $_directoryData = null;

    /**
     * @param Magento_Core_Model_LocaleInterface $locale
     * @param Magento_Core_Model_Logger $logger
     * @param Magento_Core_Helper_String $stringHelper
     * @param Magento_Directory_Helper_Data $directoryData
     */
    public function __construct(
        Magento_Core_Model_LocaleInterface $locale,
        Magento_Core_Model_Logger $logger,
        Magento_Core_Helper_String $stringHelper,
        Magento_Directory_Helper_Data $directoryData
    ) {
        $this->_directoryData = $directoryData;
        parent::__construct($locale, $logger, $stringHelper);
    }

    public function validateValue($value)
    {
        $countryId      = $this->getExtractedData('country_id');
        $optionalZip    = $this->_directoryData->getCountriesWithOptionalZip();
        if (!in_array($countryId, $optionalZip)) {
            return parent::validateValue($value);
        }
        return true;
    }
}
