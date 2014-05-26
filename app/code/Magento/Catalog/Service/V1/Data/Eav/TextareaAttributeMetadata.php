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
class TextareaAttributeMetadata extends AttributeMetadata
{
    /**
     * Enable WYSIWYG flag
     *
     * @return bool
     */
    public function getIsWysiwygEnabled()
    {
        return (bool)$this->_get(AttributeMetadata::IS_WYSIWYG_ENABLED);
    }

    /**
     * Whether the HTML tags are allowed on the frontend
     *
     * @return bool
     */
    public function getIsHtmlAllowedOnFront()
    {
        return (bool)$this->_get(AttributeMetadata::IS_HTML_ALLOWED_ON_FRONT);
    }
}
