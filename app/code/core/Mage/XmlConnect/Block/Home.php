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
 * @package     Mage_XmlConnect
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Home categories list renderer
 *
 * @category   Mage
 * @package    Mage_XmlConnect
 * @author     Magento Core Team <core@magentocommerce.com>
 */

class Mage_XmlConnect_Block_Home extends Mage_XmlConnect_Block_Catalog
{

    /**
     * Category list limitation
     */
    const HOME_PAGE_CATEGORIES_COUNT = 6;

    /**
     * Render home category list xml
     *
     * @return string
     */
    protected function _toHtml()
    {
        $homeXmlObj = new Mage_XmlConnect_Model_Simplexml_Element('<home></home>');

        $categoryCollection = Mage::getResourceModel('xmlconnect/category_collection');
        $categoryCollection->setStoreId($categoryCollection->getDefaultStoreId())
            ->addParentIdFilter(null)
            ->setLimit(0, self::HOME_PAGE_CATEGORIES_COUNT);

        if (sizeof($categoryCollection)) {
            $itemsXmlObj = $homeXmlObj->addChild('categories');
        }

        foreach ($categoryCollection->getItems() as $item){
            $itemXmlObj = $itemsXmlObj->addChild('item');
            $itemXmlObj->addChild('label', $homeXmlObj->xmlentities(strip_tags($item->getName())));
            $itemXmlObj->addChild('entity_id', $item->getEntityId());
            $itemXmlObj->addChild('content_type', $item->hasChildren() ? 'categories' : 'products');
            $icon = Mage::helper('xmlconnect/catalog_category_image')->initialize($item, 'thumbnail')
                ->resize(Mage_XmlConnect_Block_Catalog_Category::CATEGORY_IMAGE_RESIZE_PARAM);
            $itemXmlObj->addChild('icon', $icon);
        }

        $homeXmlObj->addChild('home_banner', '/current/media/catalog/category/banner_home.png');

        return $homeXmlObj->asNiceXml();
    }

}
