<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Framework\Pricing\Object;

/**
 * Interface SaleableInterface
 */
interface SaleableInterface
{
    /**
     * Returns PriceInfo container of saleable item
     *
     * @return \Magento\Framework\Pricing\PriceInfoInterface
     */
    public function getPriceInfo();

    /**
     * Returns type identifier of saleable item
     *
     * @return array|string
     */
    public function getTypeId();

    /**
     * Returns identifier of saleable item
     *
     * @return int
     */
    public function getId();

    /**
     * Returns quantity of saleable item
     *
     * @return float
     */
    public function getQty();
}
