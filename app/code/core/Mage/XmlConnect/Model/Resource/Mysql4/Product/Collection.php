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
 * Product resource collection
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Model_Resource_Mysql4_Product_Collection extends Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection
{

    const IMAGE_RESIZE_PARAM = 80;

    protected function _beforeLoad()
    {
        $this->joinField('rating_summary',
                         'review_entity_summary',
                         'rating_summary',
                         'entity_pk_value=entity_id',
                         array('entity_type'=>1, 'store_id'=> Mage::app()->getStore()->getId()),
                         'left')
             ->joinField('reviews_count',
                         'review_entity_summary',
                         'reviews_count',
                         'entity_pk_value=entity_id',
                         array('entity_type'=>1, 'store_id'=> Mage::app()->getStore()->getId()),
                         'left')
             ->addAttributeToSelect(array('image'));

        return parent::_beforeLoad();
    }

    /**
     * @param array $additionalAtrributes Additional nodes for xml
     * @param bool $safeAdditionalEntities
     * @return string
     */
    public function toXml(array $additionalAtrributes = array(), $safeAdditionalEntities = true)
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>
        <product>
           ';
        if (count($this)>0)
        {
            $xml .= '<items>';
        }

        $optinalPriceAttributes = array('price', 'final_price', 'minimal_price', 'min_price',
                                        'max_price', 'tier_price', 'special_price');
        foreach ($this as $item)
        {
            $attributes = array('entity_id', 'name', 'in_stock', 'rating_summary', 'reviews_count', 'icon', 'price');
            $item->icon = Mage::helper('catalog/image')->init($item, 'image')->resize(self::IMAGE_RESIZE_PARAM);
            $this->_formPrice($item, $attributes);
            $item->in_stock = (int)$item->isInStock();
            $xml .= $item->toXml($attributes, 'item', false, false);
        }
        
        if (count($this)>0)
        {
            $xml .= '</items>';
        }

        $xmlModel = new Varien_Simplexml_Element('<node></node>');
        foreach ($additionalAtrributes as $attrKey => $value)
        {
            $value = $safeAdditionalEntities ? $xmlModel->xmlentities($value) : $value;
            $xml .= "<{$attrKey}>$value</{$attrKey}>";
        }

        $xml .= '</product>';

        return $xml;
    }

    protected function _formPrice(Mage_Catalog_Model_Product $product, &$attributes)
    {
        if (strlen($product->special_price))
        {
            $attributes[] = 'old_price';
            $product->old_price = strip_tags(Mage::app()->getStore()->formatPrice($product->price));
            $product->price = strip_tags(Mage::app()->getStore()->formatPrice($product->special_price));
        }
        else if (strlen($product->min_price) && strlen($product->max_price)
                 && $product->min_price !== $product->max_price && strlen($product->price) )
        {
            $product->min_price = strip_tags(Mage::app()->getStore()->formatPrice($product->min_price));
            $product->max_price = strip_tags(Mage::app()->getStore()->formatPrice($product->max_price));
            $product->price = Mage::helper('catalog/product')->__('From:').' '. $product->min_price. ' '. Mage::helper('catalog/product')->__('To:').' '. $product->max_price;
        }
        else if (strlen($product->min_price) && 0 == strlen($product->price))
        {
            $product->min_price = strip_tags(Mage::app()->getStore()->formatPrice($product->min_price));
            $product->price = Mage::helper('catalog/product')->__('Starting at:').' '. $product->min_price;
        }
        else if (strlen($product->tier_price))
        {
            $attributes[] = 'aslowas_price';
            $product->tier_price = strip_tags(Mage::app()->getStore()->formatPrice($product->tier_price));
            $product->aslowas_price = Mage::helper('catalog/product')->__('As low as:').' '. $product->tier_price;
            $product->price = strip_tags(Mage::app()->getStore()->formatPrice($product->price));
        }
        else
        {
            $product->price = strip_tags(Mage::app()->getStore()->formatPrice($product->price));
        }

    }

    /**
     * @param int $offset
     * @param int $count
     * @return Mage_XmlConnect_Model_Resource_Mysql4_Product_Collection
     */
    public function addLimit($offset, $count)
    {
        $this->getSelect()->limit($count, $offset);
        return $this;
    }

    /**
     * @param Zend_Controller_Request_Abstract $reguest
     * @param string $prefix
     * @return Mage_XmlConnect_Model_Resource_Mysql4_Product_Collection
     */
    public function addFiltersFromRequest(Zend_Controller_Request_Abstract $reguest, $category, $prefix = 'filter_')
    {
        $layer = Mage::getSingleton('catalog/layer');
        $layer->setData('current_category', $category);
        $attributes = array();
        foreach ($layer->getFilterableAttributes() as $attributeModel )
        {
            $attributes[$attributeModel->getAttributeCode()] = $attributeModel;
        }

        foreach ($reguest->getParams() as $key => $value)
        {
            if (0 === strpos($key, $prefix))
            {
                $key = str_replace($prefix, '', $key);
                if (isset($attributes[$key]))
                {
                    $filter = $this->_getFilterByKey($key)
                                   ->setAttributeModel($attributes[$key])
                                   ->setRequestVar($prefix.$key)
                                   ->apply($reguest, null);
                }
            }
        }
        return $this;
    }

    /**
     * @param Zend_Controller_Request_Abstract $reguest
     * @param string $prefix
     * @return Mage_XmlConnect_Model_Resource_Mysql4_Product_Collection
     */
    public function addOrdersFromRequest(Zend_Controller_Request_Abstract $reguest, $prefix = 'order_')
    {
        foreach ($reguest->getParams() as $key => $value)
        {
            if (0 === strpos($key, $prefix))
            {
                $key = str_replace($prefix, '', $key);
                if ($value != 'desc')
                {
                    $value = 'asc';
                }
                $this->addAttributeToSort($key, $value);
            }
        }
        return $this;
    }

    protected function _getFilterByKey($key)
    {
        $filterModelName = 'catalog/layer_filter_attribute';
        switch ($key)
        {
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

}