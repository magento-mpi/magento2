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
        $attributes = $this->getProductWeeeAttributes($product, $shipping, $billing, $website);
        foreach ($attributes as $attribute) {
            $amount += $attribute->getAmount();
        }
        return $amount;
    }

    public function getWeeeAttributeCodes()
    {
        return $this->getWeeeTaxAttributeCodes();
    }

    public function getWeeeTaxAttributeCodes()
    {
        return Mage::getModel('eav/entity_attribute')->getAttributeCodesByFrontendType('weee');
    }

    public function getProductWeeeAttributes($product, $shipping = null, $billing = null, $website = null)
    {
        $result = array();

        $websiteId = Mage::app()->getStore()->getWebsiteId();
        $rateRequest = Mage::getModel('tax/calculation')->getRateRequest($shipping, $billing);

        $productAttributes = $product->getTypeInstance()->getSetAttributes();
        $allWeee = Mage::getModel('eav/entity_attribute')->getAttributeCodesByFrontendType('weee');
        foreach ($productAttributes as $code=>$attribute) {
            if (in_array($code, $allWeee)) {
                $attributeId = $attribute->getId();

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
                    $one->setName(Mage::helper('catalog')->__($attribute->getFrontend()->getLabel()))
                        ->setAmount($value)
                        ->setCode($attribute->getAttributeCode());

                    $result[] = $one;
                }
            }
        }
        return $result;
    }
}
