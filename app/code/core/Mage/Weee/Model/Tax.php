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
 * @category   Mage
 * @package    Mage_Weee
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Mage_Weee_Model_Tax extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('weee/tax', 'weee/tax');
    }

    public function getWeeeAmount($product, $shipping = null, $billing = null, $website = null)
    {
        $amount = 0;
        if (is_null($website)) {
            $websiteId = Mage::app()->getStore()->getWebsiteId();
        } else {
            $websiteId = $website;
        }

        $rateRequest = Mage::getModel('tax/calculation')->getRateRequest($shipping, $billing);
        $attributes = $this->getWeeeAttributeCodes();

        foreach ($attributes as $attribute) {
            $attributeId = Mage::getSingleton('eav/entity_attribute')->getIdByCode('catalog_product', $attribute);
            $tableAlias = "weee_{$attribute}_table";
            $attributeSelect = $this->getResource()->getReadConnection()->select();
            $attributeSelect->from(array($tableAlias=>$this->getResource()->getTable('weee/tax')), 'value');
    
            $on = array();
            $on[] = "{$tableAlias}.attribute_id = '{$attributeId}'";
            $on[] = "({$tableAlias}.website_id in ('{$websiteId}', 0))";

            $country = $rateRequest->getCountryId();
            $on[] = "({$tableAlias}.country = '{$country}')";

            $region = $rateRequest->getRegionId();
            $on[] = "({$tableAlias}.state in ('{$region}', '*'))";

            foreach ($on as $one) {
                $attributeSelect->where($one);
            }
            $attributeSelect->where('entity_id = ?', $product->getId());
            $attributeSelect->limit(1);

            $order = array($tableAlias.'.state DESC', $tableAlias.'.website_id DESC');

            $attributeSelect->order($order);
        
            $value = $this->getResource()->fetchOne($attributeSelect);
            if ($value) {
                $amount += $value;
            }
        }

        return $amount;
    }

    public function mergeAppliedRates($applied, $item, $product, $shipping = null, $billing = null, $website = null)
    {
        if (is_null($website)) {
            $websiteId = Mage::app()->getStore()->getWebsiteId();
        } else {
            $websiteId = $website;
        }
        $productTaxes = array();
        $total = 0;

        $productAttributes = $product->getTypeInstance()->getSetAttributes();
        $attributes = $this->getWeeeAttributeCodes();
        foreach ($attributes as $k=>$attribute) {
            $attributeId = Mage::getSingleton('eav/entity_attribute')->getIdByCode('catalog_product', $attribute);
            $tableAlias = $attribute;
            $rateRequest = Mage::getModel('tax/calculation')->getRateRequest($shipping, $billing);
            $attributeSelect = $this->getResource()->getReadConnection()->select();
            $attributeSelect->from(array($tableAlias=>$this->getResource()->getTable('weee/tax')), 'value');
    
            $on = array();
            $on[] = "{$tableAlias}.attribute_id = '{$attributeId}'";
            $on[] = "({$tableAlias}.website_id in ('{$websiteId}', 0))";
    
            $country = $rateRequest->getCountryId();
            $on[] = "({$tableAlias}.country = '{$country}')";
    
            $region = $rateRequest->getRegionId();
            $on[] = "({$tableAlias}.state in ('{$region}', '*'))";
    
            foreach ($on as $one) {
                $attributeSelect->where($one);
            }
            $attributeSelect->where('entity_id = ?', $product->getId());
            $attributeSelect->limit(1);
    
            $order = array($tableAlias.'.state DESC', $tableAlias.'.website_id DESC');
    
            $attributeSelect->order($order);
            $value = $this->getResource()->getReadConnection()->fetchOne($attributeSelect);
            if ($value) {
                $title = $productAttributes[$attribute]->getFrontend()->getLabel();
                $productTaxes[] = array('title'=>$title, 'amount'=>$value, 'row_amount'=>$value*$item->getQty());
                $applied[] = array(
                    'id'=>$attribute,
                    'percent'=>null,
                    'rates' => array(array(
                        'amount'=>$value*$item->getQty(),
                        'base_amount'=>$value*$item->getQty(),
                        'base_real_amount'=>$value*$item->getQty(),
                        'code'=>$attribute,
                        'title'=>$title,
                        'percent'=>null,
                        'position'=>1,
                        'priority'=>-1000+$k,
                    ))
                );
                $total += $value;
            }
        }
        $item->setWeeeTaxAppliedAmount($total);
        $item->setWeeeTaxAppliedRowAmount($total*$item->getQty());
        Mage::helper('weee')->setApplied($item, $productTaxes);

        return $applied;
    }

    public function getWeeeAttributeCodes()
    {
        return $this->getWeeeTaxAttributeCodes();
    }

    public function getWeeeTaxAttributeCodes()
    {
        return Mage::getModel('eav/entity_attribute')->getAttributeCodesByFrontendType('weee');
    }

    public function getProductWeeeAttributes($product)
    {
        $result = array();
        $productAttributes = $product->getTypeInstance()->getSetAttributes();
        $allWeee = Mage::getModel('eav/entity_attribute')->getAttributeCodesByFrontendType('weee');
        foreach ($productAttributes as $code=>$attribute) {
            if (in_array($code, $allWeee)) {


                $attributeId = $attribute->getId();
                $websiteId = Mage::app()->getStore()->getWebsiteId();
                $rateRequest = Mage::getModel('tax/calculation')->getRateRequest();

                $attributeSelect = $this->getResource()->getReadConnection()->select();
                $attributeSelect->from($this->getResource()->getTable('weee/tax'), 'value');
    
                $on = array();
                $on[] = "attribute_id = '{$attributeId}'";
                $on[] = "(website_id in ('{$websiteId}', 0))";
    
                $country = $rateRequest->getCountryId();
                $on[] = "(country = '{$country}')";
    
                $region = $rateRequest->getRegionId();
                $on[] = "(state in ('{$region}', '*'))";
    
                foreach ($on as $one) {
                    $attributeSelect->where($one);
                }
                $attributeSelect->where('entity_id = ?', $product->getId());
                $attributeSelect->limit(1);
    
                $order = array('state DESC', 'website_id DESC');
    
                $attributeSelect->order($order);
                $value = $this->getResource()->getReadConnection()->fetchOne($attributeSelect);
                if ($value) {
                    $one = new Varien_Object();
                    $one->setName($attribute->getFrontend()->getLabel())->setAmount($value);
                    $result[] = $one;
                }
            }
        }
        return $result;
    }
}
