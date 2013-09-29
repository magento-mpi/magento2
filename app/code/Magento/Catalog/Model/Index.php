<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Catalog Category/Product Index
 *
 * @category   Magento
 * @package    Magento_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Catalog\Model;

class Index
{
    /**
     * Rebuild indexes
     *
     * @return \Magento\Catalog\Model\Index
     */
    public function rebuild()
    {
        \Mage::getResourceSingleton('Magento\Catalog\Model\Resource\Category')
            ->refreshProductIndex();
        foreach (\Mage::app()->getStores() as $store) {
            \Mage::getResourceSingleton('Magento\Catalog\Model\Resource\Product')
                ->refreshEnabledIndex($store);
        }
        return $this;
    }
}
