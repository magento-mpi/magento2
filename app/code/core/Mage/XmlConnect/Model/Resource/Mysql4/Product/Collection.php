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
class Mage_XmlConnect_Model_Resource_Mysql4_Product_Collection
    extends Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection
{

    protected $_itemObjectClass = 'Mage_Xmlconnect_Model_Product';

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
     * @param array|string $additionalAtrributes Additional nodes for xml
     * @param bool $safeAdditionalEntities
     * @return string
     */
    public function toXml($additionalAtrributes = array(),
        $safeAdditionalEntities = true,
        $addOpenTag = true,
        $parentNodeTitle = 'product',
        Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection $collection = null
    ) {
        if (is_null($collection)) {
            $collection = $this;
        }
        $xml = '';
        if ($addOpenTag) {
            $xml .= '<?xml version="1.0" encoding="UTF-8"?>';
        }
        $xml .= '<'.$parentNodeTitle.'>';
        if (count($collection) > 0) {
            $xml .= '<items>';
        }

        foreach ($collection as $item) {
            if ('Mage_XmlConnect_Model_Product' != get_class($item)) {
                $xmlModelItem = Mage::getModel('xmlconnect/product');
                $xmlModelItem->fromArray($item->toArray());
                $item = $xmlModelItem;
            }
            $xml .= $item->toXml(array(), 'item', false, false);
        }
        
        if (count($collection) > 0) {
            $xml .= '</items>';
        }

        $xmlModel = new Varien_Simplexml_Element('<node></node>');

        if (is_array($additionalAtrributes)) {
            foreach ($additionalAtrributes as $attrKey => $value) {
                $value = $safeAdditionalEntities ? $xmlModel->xmlentities($value) : $value;
                $xml .= "<{$attrKey}>$value</{$attrKey}>";
            }
        } else {
            $xml .= $additionalAtrributes;
        }


        $xml .= '</'.$parentNodeTitle.'>';
        return $xml;
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
        return $this;
    }

    /**
     * @param Zend_Controller_Request_Abstract $reguest
     * @param string $prefix
     * @return Mage_XmlConnect_Model_Resource_Mysql4_Product_Collection
     */
    public function addOrdersFromRequest(Zend_Controller_Request_Abstract $reguest, $prefix = 'order_')
    {
        foreach ($reguest->getParams() as $key => $value) {
            if (0 === strpos($key, $prefix)) {
                $key = str_replace($prefix, '', $key);
                if ($value != 'desc') {
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

}