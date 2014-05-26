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
    /**#@+
     * Constants used as keys into $_data
     */
    const IS_WYSIWYG_ENABLED = 'is_wysiwyg_enabled';

    const IS_HTML_ALLOWED_ON_FRONT = 'is_html_allowed_on_front';
    /**#@-*/

    /**
     * Enable WYSIWYG flag
     *
     * @return bool
     */
    public function getIsWysiwygEnabled()
    {
        return (bool)$this->_get(self::IS_WYSIWYG_ENABLED);
    }

    /**
     * Whether the HTML tags are allowed on the frontend
     *
     * @return bool
     */
    public function getIsHtmlAllowedOnFront()
    {
        return (bool)$this->_get(self::IS_HTML_ALLOWED_ON_FRONT);
    }
}
