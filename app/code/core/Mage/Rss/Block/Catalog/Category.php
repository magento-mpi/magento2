<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Rss
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Review form block
 *
 * @category   Mage
 * @package    Mage_Rss
 * @author     Lindy Kyaw <lindy@varien.com>
 */
class Mage_Rss_Block_Catalog_Category extends Mage_Rss_Block_Abstract
{
    protected function _construct()
    {
        /*
        * setting cache to save the rss for 10 minutes
        */
        $this->setCacheKey('rss_catalog_category_'.$this->getRequest()->getParam('name'));
        $this->setCacheLifetime(600);
    }

    protected function _toHtml()
    {
echo "category";
        $categoryId = $this->getRequest()->getParam('cid');
        $storeId = $this->_getStoreId();
        if ($categoryId) {
            $category = Mage::getModel('catalog/category')->load($categoryId);
print_r($category->getData());
            if ($category && $category->getId()) {
                $category->getProductCollection()->setStoreId($storeId);
                $layer = Mage::getSingleton('catalog/layer')->setStore($storeId);
                $collection = $layer->setCurrentCategory($category)
                    ->getProductCollection()
                    ->addAttributeToSort('created_at','desc')
                ;
echo "<hr>".$collection->getSelect();
            }

        }


    }
}