<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Checkout\Service\V1\Data\Cart;

/**
 * @codeCoverageIgnore
 */
class ItemBuilder extends \Magento\Framework\Service\Data\AbstractObjectBuilder
{
    /**
     * @param string $value
     * @return $this
     */
    public function setSku($value)
    {
        $this->_set(Item::SKU, $value);
        return $this;
    }

    /**
     * @param int $value
     * @return $this
     */
    public function setQty($value)
    {
        $this->_set(Item::QTY, $value);
        return $this;
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setName($value)
    {
        $this->_set(Item::NAME, $value);
        return $this;
    }

    /**
     * @param float $value
     * @return $this
     */
    public function setPrice($value)
    {
        $this->_set(Item::PRICE, $value);
        return $this;
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setType($value)
    {
        $this->_set(Item::TYPE, $value);
        return $this;
    }

}