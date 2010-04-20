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
 * Category list xml renderer
 *
 * @category   Mage
 * @package    Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_XmlConnect_Block_Catalog_Category extends Mage_XmlConnect_Block_Catalog
{

    /**
     * Category list image size
     */
    const CATEGORY_IMAGE_RESIZE_PARAM = 80;

    protected function _toHtml()
    {
        $request = $this->getRequest();
        $offset = (int)$request->getParam('offset', 0);
        $count  = (int)$request->getParam('count', 0);
        $count  = $count <= 0 ? 1 : $count;
        $categoryXmlObj = new Varien_Simplexml_Element('<category></category>');
        $categoryId     = (int)$this->getRequest()->getParam('id');
        $categoryModel  = Mage::getModel('catalog/category')->load($categoryId);
        if ($categoryModel->getId()) {
            $infoBlock = $this->getChild('category_info');
            if ($infoBlock) {
                $categoryInfoXmlObj = $infoBlock->setCategory($categoryModel)
                    ->getCategoryInfoXmlObject();
                $categoryXmlObj->appendChild($categoryInfoXmlObj);
            }
            /**
             * Return products list if there are no child categories
             */
            if (!$categoryModel->hasChildren()){
                $productListBlock = $this->getChild('product_list');
                if ($productListBlock) {
                    $layer = Mage::getSingleton('catalog/layer');
                    $productsXmlObj = $productListBlock->setCategory($categoryModel)
                        ->setLayer($layer)
                        ->getProductsXmlObject();
                    $categoryXmlObj->appendChild($productsXmlObj);
                }
            }
        }

        $categoryCollection = Mage::getResourceModel('xmlconnect/category_collection');
        $categoryCollection->setStoreId($categoryCollection->getDefaultStoreId())
            ->addParentIdFilter($categoryId)
            ->setLimit($offset, $count);

        if (sizeof($categoryCollection)) {
            $itemsXmlObj = $categoryXmlObj->addChild('items');
        }

        foreach ($categoryCollection->getItems() as $item){
            $itemXmlObj = $itemsXmlObj->addChild('item');
            $itemXmlObj->addChild('label', $categoryXmlObj->xmlentities(strip_tags($item->getName())));
            $itemXmlObj->addChild('entity_id', $item->getEntityId());
            $itemXmlObj->addChild('content_type', $item->hasChildren() ? 'categories' : 'products');
            if (!is_null($categoryId)) {
                $itemXmlObj->addChild('parent_id', $item->getParentId());
            }
            $icon = Mage::helper('catalog/category_image')->init($item, 'image')
                ->resize(self::CATEGORY_IMAGE_RESIZE_PARAM);
            $itemXmlObj->addChild('icon', $icon);
        }

        return $categoryXmlObj->asNiceXml();
    }
}
