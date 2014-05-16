<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_TargetRule
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * TargetRule Products Item Block
 *
 * @category   Magento
 * @package    Magento_TargetRule
 *
 * @method \Magento\TargetRule\Block\Catalog\Product\Item setItem(\Magento\Catalog\Model\Product $item)
 * @method \Magento\Catalog\Model\Product getItem()
 */
namespace Magento\TargetRule\Block\Catalog\Product;

class Item extends \Magento\Catalog\Block\Product\AbstractProduct
{
    /**
     * Get cache key informative items with the position number to differentiate
     *
     * @return array
     */
    public function getCacheKeyInfo()
    {
        $cacheKeyInfo = parent::getCacheKeyInfo();

        $cacheKeyInfo[] = $this->getPosition();

        return $cacheKeyInfo;
    }
}
