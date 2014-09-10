<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Bundle\Service\V1\Data\Product;

use Magento\Framework\Service\Data\AbstractExtensibleObjectBuilder;

/**
 * @codeCoverageIgnore
 */
class OptionBuilder extends AbstractExtensibleObjectBuilder
{
    /**
     * Set option id
     *
     * @param int $value
     * @return $this
     */
    public function setId($value)
    {
        return $this->_set(Option::ID, $value);
    }

    /**
     * Set option title
     *
     * @param string $value
     * @return $this
     */
    public function setTitle($value)
    {
        return $this->_set(Option::TITLE, $value);
    }

    /**
     * Set is required option
     *
     * @param bool $value
     * @return $this
     */
    public function setRequired($value)
    {
        return $this->_set(Option::REQUIRED, $value);
    }

    /**
     * Set input type
     *
     * @param string $value
     * @return $this
     */
    public function setType($value)
    {
        return $this->_set(Option::TYPE, $value);
    }

    /**
     * Set option position
     *
     * @param int $value
     * @return $this
     */
    public function setPosition($value)
    {
        return $this->_set(Option::POSITION, $value);
    }

    /**
     * Set product sku
     *
     * @param string $value
     * @return $this
     */
    public function setSku($value)
    {
        return $this->_set(Option::SKU, $value);
    }

    /**
     * Set product links
     *
     * @param \Magento\Bundle\Service\V1\Data\Product\Link[] $value
     * @return $this
     */
    public function setProductLinks($value)
    {
        return $this->_set(Option::PRODUCT_LINKS, $value);
    }
}
