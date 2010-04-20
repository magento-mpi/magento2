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
 * Product data xml renderer
 *
 * @category   Mage
 * @package    Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_XmlConnect_Block_Catalog_Product extends Mage_XmlConnect_Block_Catalog
{

    /**
     * Product view small image size
     */
    const PRODUCT_IMAGE_SMALL_RESIZE_PARAM  = 80;

    /**
     * Product view big image size
     */
    const PRODUCT_IMAGE_BIG_RESIZE_PARAM    = 130;

    /**
     * Retrieve product attributes as xml object
     *
     * @param Mage_Catalog_Model_Product $product
     * @param string $itemNodeName
     *
     * @return Varien_Simplexml_Element
     */
    public function productToXmlObject(Mage_Catalog_Model_Product $product, $itemNodeName = 'item')
    {
        $item = new Varien_Simplexml_Element('<' . $itemNodeName . '></' . $itemNodeName . '>');
        if ($product->getId()) {
            $item->addChild('entity_id', $product->getId());
            $item->addChild('name', $item->xmlentities(strip_tags($product->getName())));
            $item->addChild('entity_type', $product->getTypeId());
            $item->addChild('description', $item->xmlentities($product->getDescription()));

            $icon = clone Mage::helper('catalog/image')->init($product, 'image')
                ->resize($itemNodeName == 'item' ? self::PRODUCT_IMAGE_SMALL_RESIZE_PARAM : self::PRODUCT_IMAGE_BIG_RESIZE_PARAM);
            $item->addChild('icon', $icon);
            $item->addChild('in_strock', (int)$product->isInStock());
            /**
             * By default all products has gallery (because of collection not load gallery attribute)
             */
            $hasGallery = 1;
            if ($product->getMediaGalleryImages()) {
                $hasGallery = sizeof($product->getMediaGalleryImages()) > 0 ? 1 : 0;
            }
            $item->addChild('has_gallery', $hasGallery);
            /**
             * If product type is grouped than it has options as its grouped items
             */
            if ($product->getTypeId() == Mage_Catalog_Model_Product_Type_Grouped::TYPE_CODE) {
                $product->setHasOptions(true);
            }
            $item->addChild('has_options', (int)$product->getHasOptions());
            $item->addChild('is_salable', (int)$product->isSaleable());

            if (!$product->getRatingSummary()) {
                Mage::getModel('review/review')
                   ->getEntitySummary($product, Mage::app()->getStore()->getId());
            }

            $item->addChild('rating_summary', $product->getRatingSummary()->getRatingSummary());
            $item->addChild('reviews_count', $product->getRatingSummary()->getReviewsCount());

            $this->_collectProductPrices($product, $item);
        }

        return $item;
    }


    /**
     * Renders text price for product
     *
     * @param Mage_Catalog_Model_Product $product
     * @param Varien_Simplexml_Element $item
     */
    protected function _collectProductPrices(Mage_Catalog_Model_Product $product, Varien_Simplexml_Element $item)
    {
        $store = Mage::app()->getStore($product->getStoreId());
        /* TODO: leak of data for grouped products price render if product loaded by load() method.
           Everything works good when using collection to load products */
        if ($product->getPriceModel() instanceof Mage_Bundle_Model_Product_Price
            && !strlen($product->getMinPrice()) && !strlen($product->getMaxPrice())
        ) {
            $minPrice =  $store->formatPrice($product->getPriceModel()->getMinimalPrice($product), false);
            $item->addChild('aslowas_price', $this->__('As low as:') . ' ' . $minPrice);
            $item->addChild('min_price', $minPrice);
            $item->addChild('max_price', $store->formatPrice($product->getPriceModel()->getMaximalPrice($product), false));
        }

        if (strlen($product->getSpecialPrice())) {
            $item->addChild('price', $store->formatPrice($product->getSpecialPrice(), false));
            $item->addChild('old_price', $store->formatPrice($product->getPrice(), false));
        }
        else if (strlen($product->getMinPrice()) && strlen($product->getMaxPrice())
                 && $product->getMinPrice() !== $product->getMaxPrice() && strlen($product->getPrice())
        ) {
            $item->addChild('min_price', $store->formatPrice($product->getMinPrice(), false));
            $item->addChild('max_price', $store->formatPrice($product->getMaxPrice(), false));
            $item->addChild('price', $this->__('From:') . ' ' . $store->formatPrice($product->getMinPrice(), false) . "\n" .
                $this->__('To:') . ' ' . $store->formatPrice($product->getMaxPrice(), false)
            );
        }
        else if (strlen($product->getMinPrice()) && 0 == strlen($product->getPrice())) {
            $item->addChild('min_price', $store->formatPrice($product->getMinPrice(), false));
            $item->addChild('price', $this->__('Starting at:') . ' ' . $store->formatPrice($product->getMinPrice(), false));
        }
        else if (is_scalar($product->getTierPrice()) && strlen($product->getTierPrice())) {
            $item->addChild('tier_price', $store->formatPrice($product->getTierPrice(), false));
            $item->addChild('aslowas_price', $this->__('As low as:') . ' ' . $store->formatPrice($product->getTierPrice(), false));
            $item->addChild('price', $store->formatPrice($product->getPrice(), false));
        }
        elseif ($product->getPrice()) {
            $item->addChild('price', $store->formatPrice($product->getPrice(), false));
        }
    }

    /**
     * Render product info xml
     *
     * @return string
     */
    protected function _toHtml()
    {
        $product = Mage::getModel('catalog/product')
            ->setStoreId(Mage::app()->getStore()->getId())
            ->load($this->getRequest()->getParam('id', 0));

        $this->setProduct($product);
        $productXmlObj = $this->productToXmlObject($product, 'product');

        $relatedProductsBlock = $this->getChild('related_products');
        if ($relatedProductsBlock) {
            $relatedXmlObj = $relatedProductsBlock->getRelatedProductsXmlObj();
            $productXmlObj->appendChild($relatedXmlObj);
        }
        return $productXmlObj->asNiceXml();
    }

}
