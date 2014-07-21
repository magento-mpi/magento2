<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Checkout\Service\V1\Data;

/**
 * @codeCoverageIgnore
 */
class ItemsBuilder extends \Magento\Framework\Service\Data\AbstractObjectBuilder
{
    /**
     * @param string $value
     * @return $this
     */
    public function setSku($value)
    {
        $this->_set(Items::SKU, $value);
        return $this;
    }

    /**
     * @param int $value
     * @return $this
     */
    public function setQty($value)
    {
        $this->_set(Items::QTY, $value);
        return $this;
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setName($value)
    {
        $this->_set(Items::NAME, $value);
        return $this;
    }

    /**
     * @param int $value
     * @return $this
     */
    public function setPrice($value)
    {
        $this->_set(Items::PRICE, $value);
        return $this;
    }

    /**
     * @param int $value
     * @return $this
     */
    public function setType($value)
    {
        $this->_set(Items::TYPE, $value);
        return $this;
    }

}