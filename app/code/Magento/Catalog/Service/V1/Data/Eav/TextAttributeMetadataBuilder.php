<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1\Data\Eav;

use Magento\Eav\Model\Entity\Attribute\AbstractAttribute;

/**
 * Class TextAttributeMetadataBuilder
 * @package Magento\Catalog\Service\V1\Data\Eav
 */
class TextAttributeMetadataBuilder extends AttributeMetadataBuilder
{
    /**
     * Set frontend class for attribute
     *
     * @param  string $frontendClass
     * @return $this|bool
     */
    public function setFrontendClass($frontendClass)
    {
        return (bool)$this->_set(TextAttributeMetadata::FRONTEND_CLASS, $frontendClass);
    }

    /**
     * Set whether the HTML tags are allowed on the frontend
     *
     * @param  bool $isHtmlAllowedOnFront
     * @return $this
     */
    public function setIsHtmlAllowedOnFront($isHtmlAllowedOnFront)
    {
        return (bool)$this->_set(TextAttributeMetadata::IS_HTML_ALLOWED_ON_FRONT, $isHtmlAllowedOnFront);
    }

    /**
     * Set whether it is used for sorting in product listing
     *
     * @param  bool $usedForSortBy
     * @return $this
     */
    public function setUsedForSortBy($usedForSortBy)
    {
        return (bool)$this->_set(TextAttributeMetadata::USED_FOR_SORT_BY, $usedForSortBy);
    }
}
