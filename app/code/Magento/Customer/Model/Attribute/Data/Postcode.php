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
namespace Magento\Customer\Model\Attribute\Data;

class Postcode extends \Magento\Eav\Model\Attribute\Data\Text
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
     * @var \Magento\Directory\Helper\Data
     */
    protected $_directoryData = null;

    /**
     * @param \Magento\Directory\Helper\Data $directoryData
     * @param \Magento\Core\Helper\String $coreString
     * @param array $arguments
     */
    public function __construct(
        \Magento\Directory\Helper\Data $directoryData,
        \Magento\Core\Helper\String $coreString,
        array $arguments = array()
    ) {
        $this->_directoryData = $directoryData;
        parent::__construct($coreString, $arguments);
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
