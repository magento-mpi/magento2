<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_TargetRule
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * TargetRule Products Item Block
 *
 * @category   Enterprise
 * @package    Enterprise_TargetRule
 *
 * @method Enterprise_TargetRule_Block_Catalog_Product_Item setItem(Mage_Catalog_Model_Product $item)
 * @method Mage_Catalog_Model_Product getItem()
 */
class Enterprise_TargetRule_Block_Catalog_Product_Item extends Mage_Catalog_Block_Product_Abstract
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
