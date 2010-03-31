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
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Rss
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Review form block
 *
 * @category   Mage
 * @package    Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_XmlConnect_Block_Category extends Mage_XmlConnect_Block_Abstract
{

    protected function _toHtml()
    {
        $additionalAttributes = $this->getChildHtml();
        if ($categoryId = $this->getRequest()->getParam('category_id', null))
        {
            $categoryModel = Mage::getModel('xmlconnect/category')->load($categoryId);
            if (!$categoryModel->hasChildren())
            {
                $productCollection = Mage::getResourceModel('xmlconnect/product_collection');
                $categoryModel->setProductCollection($productCollection);
                $productCollection->setStoreId($categoryModel->getStoreId())
                    ->addCategoryFilter($categoryModel)
                    ->addLimit($this->getRequest()->getParam('offset', 0), $this->getRequest()->getParam('count', 0));
                $this->_addFiltersToProductCollection($productCollection, $this->getRequest(), $categoryModel);
                $this->_addOrdersToProductCollection($productCollection, $this->getRequest());
                return $this->productCollectionToXml($productCollection, 'product', true, false, false, $additionalAttributes);
            }
        }
        $categoryCollection = Mage::getResourceModel('xmlconnect/category_collection');
        $categoryCollection->addImageToResult()
            ->setStoreId($categoryCollection->getDefaultStoreId())
            ->addParentIdFilter($categoryId)
            ->addLimit($this->getRequest()->getParam('offset', 0), $this->getRequest()->getParam('count', 0));
        $xml = $this->categoryCollectionToXml($categoryCollection, 'category', true, false, false, $additionalAttributes);
        return $xml;
    }

}
