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
 * Filter collection
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Model_Filter_Collection extends Varien_Data_Collection
{

    /**
     * @param array $additionalAtrributes Additional nodes for xml 
     * @return string
     */
    public function toXml(array $additionalAtrributes = array())
    {
        $xml = '<items>
                <filters>
           ';

        foreach ($this as $item)
        {
            $xml .= $this->itemToXml($item);
        }
        $xml .= '</filters>';
        $xml .= $this->_arrayToXml($additionalAtrributes);
        $xml .= '</items>';

        return $xml;
    }

    /**
     * @param array $array
     * @param string|null $nodeName
     * @param bool $useItems
     * @return string
     */
    protected function _arrayToXml(array $array, $nodeName = null, $useItems = false)
    {
        $xml = '';
        $xmlModel = new Varien_Simplexml_Element('<node></node>');
        if (!is_null($nodeName))
        {
            $xml = '<'.$nodeName.'>';
        }

        foreach ($array as $key => $value)
        {
            if (is_array($value))
            {
                $value = $this->_arrayToXml($value, null, true);
            }
            else
            {
                $value = $xmlModel->xmlentities($value);
            }
            if ($useItems)
            {
                $xml .= "<item><code>{$xmlModel->xmlentities($key)}</code><name>$value</name></item>";
            }
            else
            {
                $xml .= "<{$key}>$value</{$key}>";
            }
        }

        if (!is_null($nodeName))
        {
            $xml .= '</'.$nodeName.'>';
        }
        return $xml;
    }

    public function setCategoryId($categoryId)
    {
        if ((int)$categoryId > 0)
        {
            $this->addFilter('category_id', $categoryId);
        }
        return $this;
    }

    protected function itemToXml($item)
    {
        $xmlModel = new Varien_Simplexml_Element('<node></node>');
        $xml = '<item>';
        $xml .= "<name>{$xmlModel->xmlentities($item->getName())}</name>";
        $xml .= "<code>{$xmlModel->xmlentities($item->getCode())}</code>";
        $valuesXml = '';
        foreach ($item->getValues() as $value)
        {
            $valuesXml .= "<value>
                                <id>{$xmlModel->xmlentities($value->getValueString())}</id>
                                <label>{$xmlModel->xmlentities(strip_tags($value->getLabel()))}</label>
                          </value>";
        }
        $xml .= "<values>$valuesXml</values>";
        $xml .= '</item>';
        return $xml;
    }

    public function loadData($printQuery = false, $logQuery = false)
    {
        $layer = Mage::getSingleton('catalog/layer');
        foreach ($this->_filters as $filter)
        {
            if ('category_id' == $filter['field'])
            {
                $layer->setCurrentCategory((int)$filter['value']);
            }
        }
        foreach ($layer->getFilterableAttributes() as $attributeItem)
        {
            $filterModelName = 'catalog/layer_filter_attribute';
            switch ($attributeItem->getAttributeCode())
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

            $filterModel = Mage::getModel($filterModelName);
            $filterModel->setLayer($layer)->setAttributeModel($attributeItem);
            $filterValues = new Varien_Data_Collection;
            foreach ($filterModel->getItems() as $valueItem)
            {
                $valueObject = new Varien_Object();
                $valueObject->setLabel($valueItem->getLabel());
                $valueObject->setValueString($valueItem->getValueString());
                $filterValues->addItem($valueObject);
            }
            $item = new Varien_Object;
            $item->setCode($attributeItem->getAttributeCode());
            $item->setName($filterModel->getName());
            $item->setValues($filterValues);
            $this->addItem($item);
        }
        return $this;
    }


}