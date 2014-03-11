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
     * @param \Magento\Stdlib\DateTime\TimezoneInterface $localeDate
     * @param \Magento\Logger $logger
     * @param \Magento\Locale\ResolverInterface $localeResolver
     * @param \Magento\Stdlib\String $stringHelper
     * @param \Magento\Directory\Helper\Data $directoryData
     */
    public function __construct(
        \Magento\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Logger $logger,
        \Magento\Locale\ResolverInterface $localeResolver,
        \Magento\Stdlib\String $stringHelper,
        \Magento\Directory\Helper\Data $directoryData
    ) {
        $this->_directoryData = $directoryData;
        parent::__construct($localeDate, $logger, $localeResolver, $stringHelper);
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
