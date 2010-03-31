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
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Mage_XmlConnect_Block_Abstract extends Mage_Core_Block_Template
{
    const PRODUCT_IMAGE_RESIZE_PARAM = 80;
    const CATEGORY_IMAGE_RESIZE_PARAM = 80;
    const PRODUCT_BIG_IMAGE_RESIZE_PARAM = 130;

    /**
     * @var array Product attributes for xml
     */
    protected $_productAttributes = array('entity_id', 'name', 'in_stock', 'rating_summary', 
                                          'reviews_count', 'icon', 'big_icon', 'price');

    public function productToXml(Mage_Catalog_Model_Product $product, array $arrAttributes = array(),
        $rootName = 'item', $addOpenTag = false, $addCdata = false, $safeAdditionalEntities = false,
        $additionalAtrributes = '', $withAdditionalData = false
    ) {
        $arrAttributes = array_merge($this->_productAttributes, $arrAttributes);
        if ($product->getId()) {
            if ($withAdditionalData) {
                $this->_addProductAdditionalData($product);
            }
            $this->_formProductPrice($product, $arrAttributes);
            $this->_formProductIcon($product);
            $product->in_stock = (int)$product->isInStock();
            $product->rating_summary = round((int)$product->rating_summary / 10);
            $xml = $product->toXml($arrAttributes, $rootName, $addOpenTag, $addCdata);
            if (strlen($additionalAtrributes)) {
                $xml = substr_replace($xml, $additionalAtrributes, strrpos($xml, "</$rootName>"), 0);
            }
            return $xml;
        }
        return "<$rootName></$rootName>";
    }

    /**
     * @param Mage_Catalog_Model_Product $product
     * @return void
     */
    protected function _addProductAdditionalData(Mage_Catalog_Model_Product $product) {
        $rating = Mage::getModel('rating/rating')->getEntitySummary($product->getId());
        if ($rating->count > 0) {
            $product->rating_summary = round($rating->sum / $rating->count);
            $product->reviews_count = $rating->count;
        }
    }

    protected function _formProductPrice(Mage_Catalog_Model_Product $product, &$attributes = array())
    {
        if ('Mage_Bundle_Model_Product_Price' == get_class($product->getPriceModel())
            && !strlen($product->min_price) && !strlen($product->max_price)
        ) {
            $product->price = '0';
            $product->min_price = $product->getPriceModel()->getMinimalPrice($product);
            $product->max_price = $product->getPriceModel()->getMaximalPrice($product);
        }

        if (strlen($product->special_price)) {
            $attributes[] = 'old_price';
            $product->old_price = strip_tags(Mage::app()->getStore()->formatPrice($product->price));
            $product->price = strip_tags(Mage::app()->getStore()->formatPrice($product->special_price));
        }
        else if (strlen($product->min_price) && strlen($product->max_price)
                 && $product->min_price !== $product->max_price && strlen($product->price)
        ) {
            $product->min_price = strip_tags(Mage::app()->getStore()->formatPrice($product->min_price));
            $product->max_price = strip_tags(Mage::app()->getStore()->formatPrice($product->max_price));
            $product->price = Mage::helper('catalog/product')->__('From:')
                            .' '. $product->min_price. "\n". Mage::helper('catalog/product')->__('To:').' '
                            . $product->max_price;
        }
        else if (strlen($product->min_price) && 0 == strlen($product->price)) {
            $product->min_price = strip_tags(Mage::app()->getStore()->formatPrice($product->min_price));
            $product->price = Mage::helper('catalog/product')->__('Starting at:') . ' ' . $product->min_price;
        }
        else if (is_scalar($product->tier_price) && strlen($product->tier_price)) {
            $attributes[] = 'aslowas_price';
            $product->tier_price = strip_tags(Mage::app()->getStore()->formatPrice($product->tier_price));
            $product->aslowas_price = Mage::helper('catalog/product')->__('As low as:') . ' ' . $product->tier_price;
            $product->price = strip_tags(Mage::app()->getStore()->formatPrice($product->price));
        }
        else {
            $product->price = strip_tags(Mage::app()->getStore()->formatPrice($product->price));
        }
        return $product;
    }

    protected function _formProductIcon(Mage_Catalog_Model_Product $product)
    {
        $product->icon = clone Mage::helper('catalog/image')->init($product, 'image')
            ->resize(self::PRODUCT_IMAGE_RESIZE_PARAM);
        $product->big_icon = Mage::helper('catalog/image')->init($product, 'image')
            ->resize(self::PRODUCT_BIG_IMAGE_RESIZE_PARAM);
    }

    public function productCollectionToXml(Varien_Data_Collection_Db $collection,
        $rootName = 'product', $addOpenTag = true, $addCdata=false, $safeAdditionalEntities = false,
        $additionalAtrributes = ''
    ) {
        $collection->load();

        $xml = '';
        if ($addOpenTag) {
            $xml .= '<?xml version="1.0" encoding="UTF-8"?>';
        }
        if (strlen($rootName)) {
            $xml .= "<$rootName>";
        }
        if (count($collection) > 0) {
            $xml .= '<items>';
        }

        foreach ($collection as $item) {
            $xml .= $this->productToXml($item);
        }

        if (count($collection) > 0) {
            $xml .= '</items>';
        }

        $xmlModel = new Varien_Simplexml_Element('<node></node>');

        $xml .= $this->_renderAdditionalAttributes($additionalAtrributes, $safeAdditionalEntities);
        
        if (strlen($rootName)) {
            $xml .= "</$rootName>";
        }
        return $xml;
    }

    /**
     * @param Mage_XmlConnect_Model_Resource_Mysql4_Product_Collection $collection
     * @param Zend_Controller_Request_Abstract $reguest
     * @param  $category
     * @param string $prefix
     * @return Mage_XmlConnect_Model_Resource_Mysql4_Product_Collection
     */
    protected function _addFiltersToProductCollection(Varien_Data_Collection_Db $collection,
        Zend_Controller_Request_Abstract $reguest, $category, $prefix = 'filter_'
    ) {
        $layer = Mage::getSingleton('catalog/layer');
        $layer->setData('current_category', $category);
        $attributes = array();
        foreach ($layer->getFilterableAttributes() as $attributeModel ) {
            $attributes[$attributeModel->getAttributeCode()] = $attributeModel;
        }

        foreach ($reguest->getParams() as $key => $value) {
            if (0 === strpos($key, $prefix)) {
                $key = str_replace($prefix, '', $key);
                if (isset($attributes[$key])) {
                    $filter = $this->_getFilterByKey($key)
                        ->setAttributeModel($attributes[$key])
                        ->setRequestVar($prefix.$key)
                        ->apply($reguest, null);
                }
            }
        }
        $layer->prepareProductCollection($collection);
        return $collection;
    }

    protected function _getFilterByKey($key)
    {
        $filterModelName = 'catalog/layer_filter_attribute';
        switch ($key) {
            case 'price':
                $filterModelName = 'catalog/layer_filter_price';
                break;
            case 'decimal':
                $filterModelName = 'catalog/layer_filter_decimal';
                break;
            default:
                $filterModelName = 'catalog/layer_filter_attribute';
                break;
        }
        return Mage::getModel($filterModelName);
    }

    /**
     * @param Zend_Controller_Request_Abstract $reguest
     * @param string $prefix
     * @return Mage_XmlConnect_Model_Resource_Mysql4_Product_Collection
     */
    protected function _addOrdersToProductCollection(Varien_Data_Collection_Db $collection,
        Zend_Controller_Request_Abstract $reguest, $prefix = 'order_'
    ) {
        foreach ($reguest->getParams() as $key => $value) {
            if (0 === strpos($key, $prefix)) {
                $key = str_replace($prefix, '', $key);
                if ($value != 'desc') {
                    $value = 'asc';
                }
                $collection->addAttributeToSort($key, $value);
            }
        }
        return $collection;
    }

    /**
     * @param array $array
     * @param string|null $nodeName
     * @param bool $useItems
     * @return string
     */
    protected function _arrayToXml(array $array, $nodeName = null, $itemTag = null)
    {
        $xml = '';
        $xmlModel = new Varien_Simplexml_Element('<node></node>');
        if (!is_null($nodeName)) {
            $xml = '<'.$nodeName.'>';
        }

        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $value = $this->_arrayToXml($value, null, true);
            }
            else {
                $value = $xmlModel->xmlentities($value);
            }
            if (!is_null($itemTag)) {
                $xml .= "<$itemTag><code>{$xmlModel->xmlentities($key)}</code><name>$value</name></$itemTag>";
            }
            else {
                $xml .= "<{$key}>$value</{$key}>";
            }
        }

        if (!is_null($nodeName)) {
            $xml .= '</'.$nodeName.'>';
        }
        return $xml;
    }

    public function filterCollectionToXml(Mage_XmlConnect_Model_Filter_Collection $collection,
        $rootName = 'product', $addOpenTag = true, $addCdata=false, $safeAdditionalEntities = false,
        $additionalAtrributes = '')
    {
        $xml = '';
        if ($addOpenTag) {
            $xml .= '<?xml version="1.0" encoding="UTF-8"?>';
        }
        if (strlen($rootName)) {
            $xml .= "<{$rootName}>";
        }
        $xml .= '<filters>';

        foreach ($collection as $item) {
            $xml .= $this->_filterItemToXml($item);
        }
        $xml .= '</filters>';

        $xml .= $this->_renderAdditionalAttributes($additionalAtrributes, $safeAdditionalEntities);

        if (strlen($rootName)) {
            $xml .= "</$rootName>";
        }
        return $xml;
    }

    protected function _filterItemToXml($item)
    {
        $xmlModel = new Varien_Simplexml_Element('<node></node>');
        $xml = '<item>';
        $xml .= "<name>{$xmlModel->xmlentities($item->getName())}</name>";
        $xml .= "<code>{$xmlModel->xmlentities($item->getCode())}</code>";
        $valuesXml = '';
        foreach ($item->getValues() as $value) {
            $valuesXml .= "<value>
                                <id>{$xmlModel->xmlentities($value->getValueString())}</id>
                                <label>{$xmlModel->xmlentities(strip_tags($value->getLabel()))}</label>
                          </value>";
        }
        $xml .= "<values>$valuesXml</values>";
        $xml .= '</item>';
        return $xml;
    }

    /**
     * @param array|string $additionalAttributes
     * @return string Xml string
     */
    protected function _renderAdditionalAttributes($additionalAttributes, $safeAdditionalEntities = true)
    {
        $xml = '';
        $xmlModel = new Varien_Simplexml_Element('<node></node>');
        if (is_array($additionalAttributes)) {
            foreach ($additionalAttributes as $attrKey => $value) {
                $value = $safeAdditionalEntities ? $xmlModel->xmlentities($value) : $value;
                $xml .= "<{$attrKey}>$value</{$attrKey}>";
            }
        } else {
            $xml .= $safeAdditionalEntities ? $xmlModel->xmlentities($additionalAttributes) : $additionalAttributes;
        }
        return $xml;
    }

    public function categoryCollectionToXml(Varien_Data_Collection_Db $collection,
        $rootName = 'category', $addOpenTag = true, $addCdata=false, $safeAdditionalEntities = false,
        $additionalAtrributes = ''
    ) {
        $collection->load();

        $xml = '';
        if ($addOpenTag) {
            $xml .= '<?xml version="1.0" encoding="UTF-8"?>';
        }
        if (strlen($rootName)) {
            $xml .= "<$rootName>";
        }
        $xml .= '<items>';

        foreach ($collection as $item)
        {
            $attributes = array('label', 'background', 'entity_id', 'content_type', 'icon');
            if (strlen($item->image) < 1) {
                $item->image = 'no_selection';
            }
            $item->icon = Mage::helper('catalog/category_image')->init($item, 'image')
                ->resize(self::CATEGORY_IMAGE_RESIZE_PARAM);

            if ($collection->showParentId) {
                $attributes[] = 'parent_id';
            }
            $item->label = $item->name;
            $item->content_type = $item->hasChildren() ? 'categories' : 'products' ;

            /* Hardcode */
            $item->background = 'http://kd.varien.com/dev/yuriy.sorokolat/current/media/catalog/category/background_img.png';

            $xml .= $item->toXml($attributes, 'item', false, false);
        }
        $xml .= '</items>';
        $xml .= $this->_renderAdditionalAttributes($additionalAtrributes, $safeAdditionalEntities);
        $xml .= "</$rootName>";
        return $xml;
    }
}
