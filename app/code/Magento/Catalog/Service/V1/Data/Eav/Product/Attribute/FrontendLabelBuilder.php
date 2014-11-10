<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1\Data\Eav\Product\Attribute;

/**
 * Class FrontendLabelBuilder
 *
 * @package Magento\Catalog\Service\V1\Data\Eav\Product\Attribute
 * @codeCoverageIgnore
 */
class FrontendLabelBuilder extends \Magento\Framework\Api\ExtensibleObjectBuilder
{
    /**
     * Set store id value
     *
     * @param  string $value
     * @return $this
     */
    public function setStoreId($value)
    {
        return $this->_set(FrontendLabel::STORE_ID, $value);
    }

    /**
     * Set label
     *
     * @param  string $label
     * @return $this
     */
    public function setLabel($label)
    {
        return $this->_set(FrontendLabel::LABEL, $label);
    }
}
