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
 */
class LabelBuilder extends \Magento\Framework\Service\Data\AbstractObjectBuilder
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
     * Get option order
     *
     * @param int $value
     * @return $this
     */
    public function setStoreId($value)
    {
        return $this->_set(Label::STORE_ID, $value);
    }
}
