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
class TextareaAttributeMetadataBuilder extends AttributeMetadataBuilder
{
    /**
     * Set whether WYSIWYG enabled or not
     *
     * @param  bool $isWysiwygEnabled
     * @return $this
     */
    public function setIsWysiwygEnabled($isWysiwygEnabled)
    {
        return (bool)$this->_set(TextareaAttributeMetadata::IS_WYSIWYG_ENABLED, $isWysiwygEnabled);
    }

    /**
     * Set whether the HTML tags are allowed on the frontend
     *
     * @param  bool $isHtmlAllowedOnFront
     * @return $this
     */
    public function setIsHtmlAllowedOnFront($isHtmlAllowedOnFront)
    {
        return (bool)$this->_set(TextareaAttributeMetadata::IS_HTML_ALLOWED_ON_FRONT, $isHtmlAllowedOnFront);
    }
}
