<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Model\Attribute\Data;

/**
 * Customer Address Postal/Zip Code Attribute Data Model
 * This Data Model Has to Be Set Up in additional EAV attribute table
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
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
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
     * @param \Magento\Framework\Logger $logger
     * @param \Magento\Framework\Locale\ResolverInterface $localeResolver
     * @param \Magento\Framework\Stdlib\String $stringHelper
     * @param \Magento\Directory\Helper\Data $directoryData
     */
    public function __construct(
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Framework\Logger $logger,
        \Magento\Framework\Locale\ResolverInterface $localeResolver,
        \Magento\Framework\Stdlib\String $stringHelper,
        \Magento\Directory\Helper\Data $directoryData,
        \Magento\Customer\Service\V1\Data\Eav\AttributeMetadata $attribute
    ) {
        $this->_attribute = $attribute;
        $this->_directoryData = $directoryData;
        parent::__construct($localeDate, $logger, $localeResolver, $stringHelper);
    }

    /**
     * @param string $value
     * @return true|string[]
     */
    public function validateValue($value)
    {
        if (empty($value)) {
            $errors = [];
            $attribute = $this->getAttribute();
            $label = __($attribute->getStoreLabel());
            $countryId = $this->getExtractedData('country_id');
            $optionalZip = $this->_directoryData->getCountriesWithOptionalZip();
            if (!in_array($countryId, $optionalZip)) {
                $errors[] = __('"%1" is a required value.', $label);
                return $errors;
            }
        }
        return true;
    }

    /**
     * @param string $value
     * @return string
     */
    public function compactValue($value)
    {
        return $value;
    }
}
