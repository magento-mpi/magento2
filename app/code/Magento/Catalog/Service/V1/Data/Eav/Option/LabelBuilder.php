<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1\Data\Eav\Option;

/**
 * Class LabelBuilder
 *
 * @codeCoverageIgnore
 */
class LabelBuilder extends \Magento\Framework\Api\AbstractExtensibleObjectBuilder
{
    /**
     * Set option label
     *
     * @param  string $label
     * @return $this
     */
    public function setLabel($label)
    {
        return $this->_set(Label::LABEL, $label);
    }

    /**
     * Set store id
     *
     * @param int $value
     * @return $this
     */
    public function setStoreId($value)
    {
        return $this->_set(Label::STORE_ID, $value);
    }
}
