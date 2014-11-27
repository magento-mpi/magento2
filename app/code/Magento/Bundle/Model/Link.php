<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Bundle\Model;

use \Magento\Framework\Model\SimpleModelInterface;

class Link extends \Magento\Framework\Model\AbstractExtensibleModel implements
    \Magento\Bundle\Api\Data\LinkInterface,
    SimpleModelInterface
{
    /**
     * {@inheritdoc}
     */
    public function getSku()
    {
        return $this->getData('sku');
    }

    /**
     * {@inheritdoc}
     */
    public function getOptionId()
    {
        return $this->getData('option_id');
    }

    /**
     * {@inheritdoc}
     */
    public function getQty()
    {
        return $this->getData('qty');
    }

    /**
     * {@inheritdoc}
     */
    public function getPosition()
    {
        return $this->getData('position');
    }

    /**
     * {@inheritdoc}
     */
    public function getIsDefined()
    {
        return $this->getData('is_defined');
    }

    /**
     * {@inheritdoc}
     */
    public function getIsDefault()
    {
        return $this->getData('is_default');
    }

    /**
     * {@inheritdoc}
     */
    public function getPrice()
    {
        return $this->getData('price');
    }

    /**
     * {@inheritdoc}
     */
    public function getPriceType()
    {
        return $this->getData('price_type');
    }

    /**
     * {@inheritdoc}
     */
    public function getCanChangeQuantity()
    {
        return $this->getData('can_change_quantity');
    }

    /**
     * {@inheritdoc}
     */
    public function modelToArray()
    {
        return $this->toArray();
    }
}
