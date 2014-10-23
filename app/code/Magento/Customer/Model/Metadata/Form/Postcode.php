<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Model\Metadata\Form;

use Magento\Customer\Service\V1\Data\Eav\AttributeMetadata;
use Magento\Directory\Helper\Data as DirectoryHelper;
use Magento\Framework\Locale\ResolverInterface;
use Magento\Framework\Logger;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface as MagentoTimezone;
use Magento\Framework\Stdlib\String as MagentoString;

/**
 * Customer Address Postal/Zip Code Attribute Data Model
 */
class Postcode extends Text
{
    /**
     * @var DirectoryHelper
     */
    protected $directoryHelper;

    /**
     * @param MagentoTimezone $localeDate
     * @param Logger $logger
     * @param AttributeMetadata $attribute
     * @param ResolverInterface $localeResolver
     * @param string $value
     * @param string $entityTypeCode
     * @param bool $isAjax
     * @param MagentoString $stringHelper
     * @param DirectoryHelper $directoryHelper
     */
    public function __construct(
        MagentoTimezone $localeDate,
        Logger $logger,
        AttributeMetadata $attribute,
        ResolverInterface $localeResolver,
        $value,
        $entityTypeCode,
        $isAjax,
        MagentoString $stringHelper,
        DirectoryHelper $directoryHelper
    ) {
        $this->directoryHelper = $directoryHelper;
        parent::__construct(
            $localeDate,
            $logger,
            $attribute,
            $localeResolver,
            $value,
            $entityTypeCode,
            $isAjax,
            $stringHelper
        );
    }

    /**
     * Validate postal/zip code
     * Return true and skip validation if country zip code is optional
     *
     * @param array|null|string $value
     * @return array|bool
     */
    public function validateValue($value)
    {
        $countryId = $this->getExtractedData('country_id');
        if ($this->directoryHelper->isZipCodeOptional($countryId)) {
            return true;
        }
        return parent::validateValue($value);
    }
}
