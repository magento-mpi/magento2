<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Model\Metadata\Form;

use Magento\Customer\Model\Metadata\ElementFactory;
use Magento\Customer\Service\V1\Data\Eav\AttributeMetadata;
use Magento\Directory\Helper\Data as DirectoryHelper;
use Magento\Framework\Locale\ResolverInterface;
use Magento\Framework\Logger;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface as MagentoTimezone;

/**
 * Customer Address Postal/Zip Code Attribute Data Model
 */
class Postcode extends AbstractData
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
            $isAjax
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
        $attribute = $this->getAttribute();
        $label = __($attribute->getStoreLabel());

        $countryId = $this->getExtractedData('country_id');
        if ($this->directoryHelper->isZipCodeOptional($countryId)) {
            return true;
        }

        $errors = [];
        if (empty($value) && $value !== '0') {
            $errors[] = __('"%1" is a required value.', $label);
        }
        if (count($errors) == 0) {
            return true;
        }
        return $errors;
    }

    /**
     * {@inheritdoc}
     */
    public function extractValue(\Magento\Framework\App\RequestInterface $request)
    {
        return $this->_applyInputFilter($this->_getRequestValue($request));
    }

    /**
     * {@inheritdoc}
     */
    public function compactValue($value)
    {
        return $value;
    }

    /**
     * {@inheritdoc}
     */
    public function restoreValue($value)
    {
        return $this->compactValue($value);
    }

    /**
     * {@inheritdoc}
     */
    public function outputValue($format = ElementFactory::OUTPUT_FORMAT_TEXT)
    {
        return $this->_applyOutputFilter($this->_value);
    }
}
