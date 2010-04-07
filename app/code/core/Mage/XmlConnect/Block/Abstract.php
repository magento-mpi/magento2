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

    const PRODUCT_GALLERY_BIG_IMAGE_SIZE_PARAM = 280;
    const PRODUCT_GALLERY_SMALL_IMAGE_SIZE_PARAM = 40;

    const REVIEW_DETAIL_TRUNCATE_LEN = 200;

    /**
     * @var array Product attributes for xml
     */
    protected $_productAttributes = array('entity_id', 'name', 'in_stock', 'rating_summary',
                                          'reviews_count', 'icon', 'big_icon', 'price');

    /**
     * @var array Review attributes for xml
     */
    protected $_reviewAttributes = array('review_id', 'created_at', 'title', 'detail', 'nickname',
                                          'rating_votes');

    /**
     * Renders xml document start data
     *
     * @param Varien_Data_Collection $collection
     * @param  $rootName
     * @param bool $addOpenTag
     * @param string $itemsName
     * @return string
     */
    protected function _getCollectionXmlStart(Varien_Data_Collection $collection,
        $rootName, $addOpenTag = true, $itemsName = 'items')
    {
        $collection->load();
        $xml = '';
        if ($addOpenTag) {
            $xml .= '<?xml version="1.0" encoding="UTF-8"?>';
        }
        if (strlen($rootName)) {
            $xml .= "<$rootName>";
        }
        if (count($collection) && strlen($itemsName)) {
            $xml .= "<$itemsName>";
        }
        return $xml;
    }

    /**
     * Renders xml document end data
     *
     * @param Varien_Data_Collection $collection
     * @param  $rootName
     * @param bool $safeAdditionalEntities
     * @param string $additionalAtrributes
     * @param string $itemsName
     * @return string
     */
    protected function _getCollectionXmlEnd(Varien_Data_Collection $collection,
        $rootName, $safeAdditionalEntities = false, $additionalAtrributes = '', $itemsName = 'items'
    ) {
        $xml = '';
        if (count($collection) && strlen($itemsName)) {
            $xml .= "</$itemsName>";
        }
        $xml .= $this->_renderAdditionalAttributes($additionalAtrributes, $safeAdditionalEntities);
        if (strlen($rootName)) {
            $xml .= "</$rootName>";
        }
        return $xml;
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

    /**
     * Renders text price for product
     *
     * @param Mage_Catalog_Model_Product $product
     * @param array $attributes
     * @return Mage_Catalog_Model_Product
     */
    protected function _formProductPrice(Mage_Catalog_Model_Product $product, &$attributes = array())
    {
        /* TODO: leak of data for grouped products price render if product loaded by load() method.
           Everything works good when using collection to load products */
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

    /**
     * Adds resized icons or placeholders to product params
     *
     * @param Mage_Catalog_Model_Product $product
     * @return void
     */
    protected function _formProductIcon(Mage_Catalog_Model_Product $product)
    {
        $product->icon = clone Mage::helper('catalog/image')->init($product, 'image')
            ->resize(self::PRODUCT_IMAGE_RESIZE_PARAM);
        $product->big_icon = Mage::helper('catalog/image')->init($product, 'image')
            ->resize(self::PRODUCT_BIG_IMAGE_RESIZE_PARAM);
    }

    /**
     * Converts additional attributes (string or array) to xml
     *
     * @param array|string $additionalAttributes
     * @param bool $safeAdditionalEntities
     * @return string Xml string
     */
    protected function _renderAdditionalAttributes($additionalAttributes, $safeAdditionalEntities = true)
    {
        $xml = '';
        $xmlModel = new Varien_Simplexml_Element('<node></node>');
        if (is_array($additionalAttributes)) {
            $xml .= $this->_arrayToXml($additionalAttributes);
        } else {
            $xml .= $safeAdditionalEntities ? $xmlModel->xmlentities($additionalAttributes) : $additionalAttributes;
        }
        return $xml;
    }

    /**
     * Converts product object to Xml
     *
     * @param Mage_Catalog_Model_Product $product
     * @param array $arrAttributes
     * @param string $rootName
     * @param bool $addOpenTag
     * @param bool $addCdata
     * @param bool $safeAdditionalEntities
     * @param string $additionalAtrributes
     * @param bool $withAdditionalData
     * @return string
     */
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
     * Converts product collection object to Xml
     *
     * @param Varien_Data_Collection_Db $collection
     * @param string $rootName
     * @param bool $addOpenTag
     * @param bool $addCdata
     * @param bool $safeAdditionalEntities
     * @param string $additionalAtrributes
     * @return string Xml
     */
    public function productCollectionToXml(Varien_Data_Collection_Db $collection,
        $rootName = 'product', $addOpenTag = true, $addCdata=false, $safeAdditionalEntities = false,
        $additionalAtrributes = ''
    ) {
        $xml = $this->_getCollectionXmlStart($collection, $rootName, $addOpenTag);

        foreach ($collection as $item) {
            $xml .= $this->productToXml($item);
        }

        $xml .= $this->_getCollectionXmlEnd($collection, $rootName, $safeAdditionalEntities, $additionalAtrributes);
        return $xml;
    }

    /**
     * Adds filter params from request to collection
     *
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

    /**
     * Creates filter object by key
     *
     * @param string $key
     * @return Mage_Catalog_Model_Layer_Filter_Abstract
     */
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
     * Adds sort params from request to collection
     *
     * @param Varien_Data_Collection_Db $collection
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
     * Converts array to Xml
     *
     * @param array $array
     * @param string|null $nodeName
     * @param string|null $itemTag
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

    /**
     * Converts filter collection object to Xml
     *
     * @param Mage_XmlConnect_Model_Filter_Collection $collection
     * @param string $rootName
     * @param bool $addOpenTag
     * @param bool $addCdata
     * @param bool $safeAdditionalEntities
     * @param string $additionalAtrributes
     * @return string
     */
    public function filterCollectionToXml(Mage_XmlConnect_Model_Filter_Collection $collection,
        $rootName = 'product', $addOpenTag = true, $addCdata=false, $safeAdditionalEntities = false,
        $additionalAtrributes = '')
    {
        $xml = $this->_getCollectionXmlStart($collection, $rootName, $addOpenTag, 'filters');

        foreach ($collection as $item) {
            if (count($item->getValues())) {
                $xml .= $this->_filterItemToXml($item);
            }
        }

        $xml .= $this->_getCollectionXmlEnd($collection, $rootName, $safeAdditionalEntities,
            $additionalAtrributes, 'filters');
        return $xml;
    }

    /**
     * Converts filter object to Xml
     *
     * @param Mage_Catalog_Model_Layer_Filter_Abstract $item
     * @return string
     */
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
                                <count>{$xmlModel->xmlentities(strip_tags($value->getProductsCount()))}</count>
                          </value>";
        }
        $xml .= "<values>$valuesXml</values>";
        $xml .= '</item>';
        return $xml;
    }

    /**
     * Converts category collection object to Xml
     *
     * @param Varien_Data_Collection_Db $collection
     * @param string $rootName
     * @param bool $addOpenTag
     * @param bool $addCdata
     * @param bool $safeAdditionalEntities
     * @param string $additionalAtrributes
     * @return string
     */
    public function categoryCollectionToXml(Varien_Data_Collection_Db $collection,
        $rootName = 'category', $addOpenTag = true, $addCdata=false, $safeAdditionalEntities = false,
        $additionalAtrributes = ''
    ) {
        $xml = $this->_getCollectionXmlStart($collection, $rootName, $addOpenTag);

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

        $xml .= $this->_getCollectionXmlEnd($collection, $rootName, $safeAdditionalEntities, $additionalAtrributes);
        return $xml;
    }

    /**
     * Converts review collection object to Xml
     *
     * @param Varien_Data_Collection_Db $collection
     * @param string $rootName
     * @param bool $addOpenTag
     * @param bool $addCdata
     * @param bool $safeAdditionalEntities
     * @param string $additionalAtrributes
     * @return string
     */
    public function reviewCollectionToXml(Varien_Data_Collection_Db $collection,
        $rootName = 'reviews', $addOpenTag = true, $addCdata=false, $safeAdditionalEntities = false,
        $additionalAtrributes = ''
    ) {
        $xml = $this->_getCollectionXmlStart($collection, $rootName, $addOpenTag);

        foreach ($collection as $item) {
            $remainder = '';
            $item->detail = Mage::helper('core/string')
                ->truncate($item->detail, self::REVIEW_DETAIL_TRUNCATE_LEN, '', $remainder, false);
            $xml .= $this->reviewToXml($item, $addCdata);
        }

        $xml .= $this->_getCollectionXmlEnd($collection, $rootName, $safeAdditionalEntities, $additionalAtrributes);
        return $xml;
    }

    /**
     * Converts review object to Xml
     *
     * @param Mage_Review_Model_Review $item
     * @param bool $addCdata
     * @return string Xml
     */
    public function reviewToXml($item, $addCdata = false)
    {
        $summary = Mage::getModel('rating/rating')->getReviewSummary($item->getId());
        $rating = 0;
        if ($summary->count > 0) {
            $rating = round($summary->sum / $summary->count / 10);
        }
        $item->setRatingVotes($rating);
        return $item->toXml($this->_reviewAttributes, 'item', false, $addCdata);
    }

    public function productOptionsCollectionToXml(Varien_Data_Collection_Db $collection,
        $rootName = 'product', $addOpenTag = true, $addCdata=false, $safeAdditionalEntities = false,
        $additionalAtrributes = ''
    ){
//        $xml = $this->_getCollectionXmlStart($collection, $rootName, $addOpenTag, 'options');

        $xml = '<?xml version="1.0" encoding="UTF-8"?>
            <product id="10">
                <options>
                    <!--Simple product options (Qty\'s are not editable)-->
                    <option code="option_code1" type="text" label="Text Option Is Not Required" is_qty_editable="0" price="$5.00"/>
                    <option code="option_code1" type="text" label="Text Option Is Required" is_qty_editable="0" is_required="1" price="$5.00"/>
                    <option code="option_code2" type="select" label="Select Option With Specific Prices Per Option And Is Required" is_qty_editable="0" is_required="1">
                        <value code="value_1" label="Value Labe1" price="$7.00"/>
                        <value code="value_2" label="Value Labe2" price="$5.00"/>
                        <value code="value_3" label="Value Labe3" price="-$1.00"/>
                    </option>
                    <option code="option_code2" type="select" label="Select Option With One Price For Any Options And Is Not Required" is_qty_editable="0" price="$10.00">
                        <value code="value_1" label="Value Labe1"/>
                        <value code="value_2" label="Value Labe2"/>
                        <value code="value_3" label="Value Labe3"/>
                    </option>
                    <option code="option_code3" type="checkbox" label="CheckBox Option With Specific Prices Per Option And Is Required" is_qty_editable="0" is_required="1">
                        <value code="value_1" label="Value Labe1" price="$7.00"/>
                        <value code="value_2" label="Value Labe2" price="-$5.00"/>
                        <value code="value_3" label="Value Labe3" price="$1.00"/>
                    </option>
                    <option code="option_code4" type="checkbox" label="CheckBox Option With One Price For Any Options And Is Not Required" is_qty_editable="0" price="-$5.00">
                        <value code="value_code1" label="Value Labe1l"/>
                        <value code="value_code2" label="Value Label2"/>
                        <value code="value_code3" label="Value Label3"/>
                    </option>
                    <option code="option_code41" type="radio" label="Radio Option With Specific Prices Per Option And Is Required" is_qty_editable="0" is_required="1">
                        <value code="value_code1" label="Value Labe1l" price="+$10.00"/>
                        <value code="value_code2" label="Value Label2" price="-$50.00"/>
                        <value code="value_code3" label="Value Label3" price="+$50.00"/>
                    </option>

                    <!--Configurable product options, can be "select" with relation on other options or can be any simple product options (Qty\'s are not editable)-->
                    <option code="option_code5_related" type="select" label="Related And Required Select Option (Color Price Related on Size)" is_qty_editable="0" is_requred="1">
                        <value code="value_red_code" label="Red" price="+$5.00">
                            <relation to="option_code6_relative">
                                <value code="small" label="Small"/>
                                <value code="medium" label="Medium"/>
                                <value code="large" label="Large"/>
                            </relation>
                        </value>
                        <value code="value_green_code" label="Green" price="+$5.00">
                            <relation to="option_code6_relative">
                                <value code="small" label="Small"/>
                                <value code="medium" label="Medium"/>
                            </relation>
                        </value>
                        <value code="value_black_code" label="Black" price="+$15.00">
                            <relation to="option_code6_relative">
                                <value code="medium" label="Medium"/>
                                <value code="large" label="Large"/>
                            </relation>
                        </value>
                    </option>
                    <option code="option_code6_relative" type="select" label="Relative On Color Size Option And Is Required" is_qty_editable="0" is_requred="1"/>
                    <option code="option_code7" type="text" label="Text Option Is Not Required" is_qty_editable="0" price="$5.00"/>
                    <option code="option_code8" type="select" label="Select Option With One Price For Any Options And Is Not Required" is_qty_editable="0" price="$10.00">
                        <value code="value_1" label="Value Labe1"/>
                        <value code="value_2" label="Value Labe2"/>
                        <value code="value_3" label="Value Labe3"/>
                    </option>
                    <option code="option_code9" type="checkbox" label="CheckBox Option With Specific Prices Per Option And Is Required" is_qty_editable="0" is_required="1">
                        <value code="value_1" label="Value Labe1" price="$7.00"/>
                        <value code="value_2" label="Value Labe2" price="$5.00"/>
                        <value code="value_3" label="Value Labe3" price="$1.00"/>
                    </option>
                    <option code="option_code91" type="radio" label="Radio Option With One Price For Any Options And Is Required" is_qty_editable="0" price="$14.00" is_required="1">
                        <value code="value_code1" label="Value Labe1l"/>
                        <value code="value_code2" label="Value Label2"/>
                        <value code="value_code3" label="Value Label3"/>
                    </option>

                    <!--Grouped product options (Qty\'s can be editable or not)-->
                    <option code="option_code10" type="product" label="Option With Editable Qty And Without Qty Preset" is_qty_editable="1" price="$10.00"/>
                    <option code="option_code11" type="product" label="Option With Not Editable Qty And With Qty Preset" is_qty_editable="0" qty="1" price="$1.00"/>
                    <option code="option_code12" type="product" label="Option With Editable Qty With Qty Preset" is_qty_editable="1" qty="6" price="$12.00"/>

                    <!--Bundle product options (Qty\'s can be editable or not)-->
                    <option code="option_code13" type="text" lable="Text Option" is_qty_editable="0" price="$5"/>

                    <option code="option_code14" type="select" lable="Select Option" is_qty_editable="0" qty="2">
                        <value code="value_code" label="Value Label" price="$7"/>
                    </option>
                    <option code="option_code15" type="radio" lable="Radio Option" is_qty_editable="1" qty="2" price="-$10">
                        <value code="value_code1" label="Value Labe1l"/>
                        <value code="value_code2" label="Value Label2"/>
                    </option>
                    <option code="option_code16" type="radio" lable="Radio Option 2" is_qty_editable="1" is_required="1">
                        <value code="value_code1" label="Value Labe1l" price="+$3.00"/>
                        <value code="value_code2" label="Value Label2" price="+$8.00"/>
                    </option>
                    <option code="option_code17" type="checkbox" label="CheckBox Option" is_qty_editable="1" is_required="1">
                        <value code="value_1" label="Value Labe1" price="+$7.00"/>
                        <value code="value_2" label="Value Labe2" price="+$5.00"/>
                        <value code="value_3" label="Value Labe3" price="+$1.00"/>
                    </option>

                    <!--Gift Card options (Qty\'s are not editable, prices are not set)-->
                    <option code="option_code19" type="text" lable="Text Option 1" is_qty_editable="0"/>
                    <option code="option_code20" type="text" lable="Text Option 2" is_qty_editable="0" is_required="1"/>
                    <option code="option_code21" type="select" label="Amount" is_qty_editable="0">
                        <value code="value_code1" label="$100.00"/>
                        <value code="value_code2" label="$200.00"/>
                        <value code="value_code3" label="$300.00"/>
                    </option>
                </options>
            </product>
        ';

//        $xml .= $this->_getCollectionXmlEnd($collection, $rootName, $safeAdditionalEntities, $additionalAtrributes);
        return $xml;
    }

    /**
     * Build product image gallery xml
     *
     * @param Varien_Data_Collection $collection
     * @param Mage_Catalog_Model_Product $product
     *
     * @return string
     */
    public function productImagesCollectionToXml(Varien_Data_Collection $collection, Mage_Catalog_Model_Product $product)
    {
        $xmlModel = new Varien_Simplexml_Element('<product></product>');
        $xmlModel->addAttribute('id', $product->getId());
        $imagesNode = $xmlModel->addChild('images');
        $helper = $this->helper('catalog/image');

        foreach ($collection as $item) {
            $imageNode = $imagesNode->addChild('image');
            $imageNode->addAttribute('position', $item->getPosition());

            /**
             * Big image
             */
            $bigImage = $helper->init($product, 'thumbnail', $item->getFile())
                ->resize(self::PRODUCT_GALLERY_BIG_IMAGE_SIZE_PARAM);

            $fileNode = $imageNode->addChild('file');
            $fileNode->addAttribute('type', 'big');
            $fileNode->addAttribute('url', $bigImage);

            /**
             * Small image
             */
            $smallImage = $helper->init($product, 'thumbnail', $item->getFile())
                ->resize(self::PRODUCT_GALLERY_SMALL_IMAGE_SIZE_PARAM);

            $fileNode = $imageNode->addChild('file');
            $fileNode->addAttribute('type', 'small');
            $fileNode->addAttribute('url', $smallImage);
        }
        return $xmlModel->asXML();
    }
}
