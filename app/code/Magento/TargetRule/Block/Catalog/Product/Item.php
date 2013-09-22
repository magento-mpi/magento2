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

        foreach ($this->_layout->getXpath('//action[@method="addPriceBlockType"]') as $element) {
            if (!empty($element->type)) {
                $prefix = 'price_block_type_' . (string)$element->type;
                $cacheKeyInfo[$prefix . '_block'] = empty($element->block) ? '' : (string)$element->block;
                $cacheKeyInfo[$prefix . '_template'] = empty($element->template) ? '' : (string)$element->template;
            }
        }

        $cacheKeyInfo[] = $this->getPosition();

        return $cacheKeyInfo;
    }
}
