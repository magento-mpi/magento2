<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Shopping cart cross sell items xml renderer
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Block_Cart_Crosssell extends Mage_Checkout_Block_Cart_Crosssell
{
    /**
     * Render cross sell items xml
     *
     * @return string
     */
    protected function _toHtml()
    {
        if (is_object(Mage::getConfig()->getNode('modules/Enterprise_TargetRule'))) {
            $blockRenderer = 'Enterprise_TargetRule_Block_Checkout_Cart_Crosssell';
            $blockName = 'targetrule.checkout.cart.crosssell';
            $this->getLayout()->createBlock($blockRenderer, $blockName);
            $this->setItems($this->getLayout()->getBlock($blockName)->getItemCollection());
        }

        $crossSellXmlObj = Mage::getModel('Mage_XmlConnect_Model_Simplexml_Element',
            array('data' => '<crosssell></crosssell>'));
        if (!$this->getItemCount()) {
            return $crossSellXmlObj->asNiceXml();
        }

        /** @var $product Mage_Catalog_Model_Product */
        foreach ($this->getItems() as $product) {
            $itemXmlObj = $crossSellXmlObj->addChild('item');
            $itemXmlObj->addChild('name', $crossSellXmlObj->escapeXml($product->getName()));
            $icon = $this->helper('Mage_Catalog_Helper_Image')->init($product, 'thumbnail')
                ->resize(Mage::helper('Mage_XmlConnect_Helper_Image')->getImageSizeForContent('product_small'));

            $iconXml = $itemXmlObj->addChild('icon', $icon);

            $file = Mage::helper('Mage_XmlConnect_Helper_Data')->urlToPath($icon);
            $iconXml->addAttribute('modification_time', filemtime($file));

            $itemXmlObj->addChild('entity_id', $product->getId());
            $itemXmlObj->addChild('entity_type', $product->getTypeId());

            /**
             * If product type is grouped than it has options as its grouped items
             */
            if ($product->getTypeId() == Mage_Catalog_Model_Product_Type_Grouped::TYPE_CODE
                || $product->getTypeId() == Mage_Catalog_Model_Product_Type_Configurable::TYPE_CODE) {
                $product->setHasOptions(true);
            }

            $itemXmlObj->addChild('has_options', (int)$product->getHasOptions());
            $itemXmlObj->addChild('in_stock', (int)$product->getStockItem()->getIsInStock());
            if ($product->getTypeId() == Mage_Downloadable_Model_Product_Type::TYPE_DOWNLOADABLE) {
                $itemXmlObj->addChild('is_salable', 0);
            } else {
                $itemXmlObj->addChild('is_salable', (int)$product->isSalable());
            }

            if ($this->getChildBlock('product_price')) {
                $this->getChildBlock('product_price')->setProduct($product)->setProductXmlObj($itemXmlObj)
                    ->collectProductPrices();
            }

            if (!$product->getRatingSummary()) {
                Mage::getModel('Mage_Review_Model_Review')->getEntitySummary($product, Mage::app()->getStore()->getId());
            }

            $itemXmlObj->addChild('rating_summary', round((int)$product->getRatingSummary()->getRatingSummary() / 10));
            $itemXmlObj->addChild('reviews_count', $product->getRatingSummary()->getReviewsCount());
        }
        return $crossSellXmlObj->asNiceXml();
    }
}