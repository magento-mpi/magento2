<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Pricing\Price;

use \Magento\ObjectManager;
use \Magento\Pricing\Object\SaleableInterface;

/**
 * Class Collection
 */
class Collection implements \Iterator
{
    /**
     * @var Pool
     */
    protected $pool;

    /**
     * @var \Magento\Pricing\Object\SaleableInterface
     */
    protected $saleableItem;

    /**
     * @var \Magento\Pricing\Price\Factory
     */
    protected $priceFactory;

    /**
     * @var float
     */
    protected $quantity;

    /**
     * @var array
     */
    protected $contains;

    /**
     * @var array
     */
    protected $excludes;

    public function __construct(
        SaleableInterface $saleableItem,
        Factory $priceFactory,
        Pool $pool,
        $quantity
    ) {
        $this->saleableItem = $saleableItem;
        $this->priceFactory = $priceFactory;
        $this->pool = $pool;
        $this->quantity = $quantity;
    }

    /**
     * Reset the Collection to the first element
     *
     * @return mixed|void
     */
    public function rewind()
    {
        return $this->pool->rewind();
    }

    /**
     * Return the current element
     *
     * @return \Magento\Pricing\Price\PriceInterface
     */
    public function current()
    {
        return $this->get($this->key());
    }

    /**
     * Return the key of the current element
     *
     * @return string
     */
    public function key()
    {
        return $this->pool->key();
    }

    /**
     * Move forward to next element
     *
     * @return mixed|void
     */
    public function next()
    {
        return $this->pool->next();
    }

    /**
     * Checks if current position is valid
     *
     * @return bool
     */
    public function valid()
    {
        return $this->pool->valid();
    }

    /**
     * Returns price model by code
     *
     * @param $code
     * @return PriceInterface
     */
    public function get($code)
    {
          return $this->priceFactory->create(
            $this->saleableItem,
            $this->pool[$code],
            $this->quantity
        );
    }
}
