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
 * @package     Mage_Catalog
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Catalog product
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Model_Product extends Mage_Catalog_Model_Product
{
    
    const IMAGE_RESIZE_PARAM = 80;

    /**
     * @var array Attributes for xml
     */
    protected $_xmlAttributes = array('entity_id', 'name', 'in_stock', 'rating_summary', 'reviews_count', 'icon', 'price');

    /**
     * @param Mage_Catalog_Model_Product $product
     * @param array $attributes
     * @return void
     */
    public function formPriceForXml(&$attributes = array())
    {
        if (strlen($this->special_price)) {
            $attributes[] = 'old_price';
            $this->old_price = strip_tags(Mage::app()->getStore()->formatPrice($this->price));
            $this->price = strip_tags(Mage::app()->getStore()->formatPrice($this->special_price));
        }
        else if (strlen($this->min_price) && strlen($this->max_price)
                 && $this->min_price !== $this->max_price && strlen($this->price)
        ) {
            $this->min_price = strip_tags(Mage::app()->getStore()->formatPrice($this->min_price));
            $this->max_price = strip_tags(Mage::app()->getStore()->formatPrice($this->max_price));
            $this->price = Mage::helper('catalog/product')->__('From:')
                            .' '. $this->min_price. "\n". Mage::helper('catalog/product')->__('To:').' '
                            . $this->max_price;
        }
        else if (strlen($this->min_price) && 0 == strlen($this->price)) {
            $this->min_price = strip_tags(Mage::app()->getStore()->formatPrice($this->min_price));
            $this->price = Mage::helper('catalog/product')->__('Starting at:') . ' ' . $this->min_price;
        }
        else if (is_scalar($this->tier_price) && strlen($this->tier_price)) {
            $attributes[] = 'aslowas_price';
            $this->tier_price = strip_tags(Mage::app()->getStore()->formatPrice($this->tier_price));
            $this->aslowas_price = Mage::helper('catalog/product')->__('As low as:') . ' ' . $this->tier_price;
            $this->price = strip_tags(Mage::app()->getStore()->formatPrice($this->price));
        }
        else {
            $this->price = strip_tags(Mage::app()->getStore()->formatPrice($this->price));
        }
        return $this;
    }

    public function formIconForXml()
    {
        $this->icon = Mage::helper('catalog/image')->init($this, 'image')->resize(self::IMAGE_RESIZE_PARAM);
    }

    /**
     * @param array $arrAttributes
     * @param string $rootName
     * @param bool $addOpenTag
     * @param bool $addCdata
     * @return string
     */
    public function toXml(array $arrAttributes = array(),
        $rootName = 'item', $addOpenTag=false, $addCdata=true, $withRelated = false
    ) {
        $arrAttributes = array_merge($this->_xmlAttributes, $arrAttributes);
        if ($this->getId()) {
            $this->formPriceForXml($arrAttributes);
            $this->formIconForXml();
            $this->in_stock = (int)$this->isInStock();
            $this->rating_summary = round((int)$this->rating_summary / 10);
            $xml = parent::toXml($arrAttributes, $rootName, $addOpenTag, $addCdata);
            if ($withRelated) {
                $collection = $this->getRelatedProductCollection();
                $layer = Mage::getSingleton('catalog/layer')->prepareProductCollection($collection);
                $xmlconnectCollection = Mage::getResourceModel('xmlconnect/product_collection');
                $relatedXml = $xmlconnectCollection->toXml(array(),'item', false, 'relatedProducts', $collection);
                $xml = substr_replace($xml, $relatedXml, strrpos($xml, "</$rootName>"), 0);
            }
            return $xml;
        }
        return "<$rootName></$rootName>";
    }

    /**
     * Load object data
     *
     * @param   integer $id
     * @return  Mage_Core_Model_Abstract
     */
    public function load($id, $field=null)
    {
        return parent::load($id, $field=null);
    }
}
