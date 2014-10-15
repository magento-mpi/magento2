<?php
/**
 * Form Element Postcode Data Model
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Model\Metadata\Form;

class Postcode extends Text
{
    protected $_directoryData = null;

    /**
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
     * @param \Magento\Framework\Logger $logger
     * @param \Magento\Customer\Service\V1\Data\Eav\AttributeMetadata $attribute
     * @param \Magento\Framework\Locale\ResolverInterface $localeResolver
     * @param string $value
     * @param string $entityTypeCode
     * @param bool $isAjax
     * @param \Magento\Framework\Stdlib\String $stringHelper
     * @param \Magento\Directory\Helper\Data $directoryData
     */
    public function __construct(
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Framework\Logger $logger,
        \Magento\Customer\Service\V1\Data\Eav\AttributeMetadata $attribute,
        \Magento\Framework\Locale\ResolverInterface $localeResolver,
        $value,
        $entityTypeCode,
        $isAjax,
        \Magento\Framework\Stdlib\String $stringHelper,
        \Magento\Directory\Helper\Data $directoryData
    ) {
        $this->_directoryData = $directoryData;
        parent::__construct($localeDate, $logger, $attribute, $localeResolver, $value, $entityTypeCode, $isAjax, $stringHelper);
    }

    /**
     * @param string $value
     * @return true|string[]
     */
    public function validateValue($value)
    {
        $countryId = $this->getExtractedData('country_id');
        $optionalZip = $this->_directoryData->getCountriesWithOptionalZip();
        if (!in_array($countryId, $optionalZip)) {
            return parent::validateValue($value);
        }
        return true;
    }
}