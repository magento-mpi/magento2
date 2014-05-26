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
    /**#@+
     * Constants used as keys into $_data
     */
    const FRONTEND_CLASS = 'frontend_class';

    const IS_HTML_ALLOWED_ON_FRONT = 'is_html_allowed_on_front';

    const USED_FOR_SORT_BY = 'used_for_sort_by';
    /**#@-*/

    /**
     * Retrieve frontend class of attribute
     *
     * @return bool
     */
    public function getFrontendClass()
    {
        return (bool)$this->_get(self::FRONTEND_CLASS);
    }

    /**
     * Whether the HTML tags are allowed on the frontend
     *
     * @return bool
     */
    public function isHtmlAllowedOnFront()
    {
        return (bool)$this->_get(self::IS_HTML_ALLOWED_ON_FRONT);
    }

    /**
     * Whether it is used for sorting in product listing
     *
     * @return bool
     */
    public function isUsedForSortBy()
    {
        return (bool)$this->_get(self::USED_FOR_SORT_BY);
    }
}
