<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1\Data\Eav;

/**
 * Class TextAttributeMetadata
 * @package Magento\Catalog\Service\V1\Data\Eav
 */
class TextAttributeMetadata extends AttributeMetadata
{
    /**
     * Retrieve frontend class of attribute
     *
     * @return string
     */
    public function getFrontendClass()
    {
        return $this->_get(AttributeMetadata::FRONTEND_CLASS);
    }

    /**
     * Whether the HTML tags are allowed on the frontend
     *
     * @return bool
     */
    public function isHtmlAllowedOnFront()
    {
        return (bool)$this->_get(AttributeMetadata::IS_HTML_ALLOWED_ON_FRONT);
    }

    /**
     * Whether it is used for sorting in product listing
     *
     * @return bool
     */
    public function isUsedForSortBy()
    {
        return (bool)$this->_get(AttributeMetadata::USED_FOR_SORT_BY);
    }
}
