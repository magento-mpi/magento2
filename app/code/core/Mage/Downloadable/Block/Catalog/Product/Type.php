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
 * @category   Mage
 * @package    Mage_Downloadable
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Catalog downloadable product part block
 *
 * @category   Mage
 * @package    Mage_Downloadable
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Downloadable_Block_Catalog_Product_Type extends Mage_Catalog_Block_Product_View_Type_Virtual
{

    public function getJsonConfig()
    {
        return '{}';

        $attributes = array();
        $options = array();
        $store = Mage::app()->getStore();
        foreach ($this->getAllowProducts() as $product) {
            $productId  = $product->getId();

            foreach ($this->getAllowAttributes() as $attribute) {
                $productAttribute = $attribute->getProductAttribute();
                $attributeValue = $product->getData($productAttribute->getAttributeCode());
                if (!isset($options[$productAttribute->getId()])) {
                    $options[$productAttribute->getId()] = array();
                }

                if (!isset($options[$productAttribute->getId()][$attributeValue])) {
                    $options[$productAttribute->getId()][$attributeValue] = array();
                }
                $options[$productAttribute->getId()][$attributeValue][] = $productId;
            }
        }

        $this->_resPrices = array(
            $this->_preparePrice($this->getProduct()->getFinalPrice())
        );

        foreach ($this->getAllowAttributes() as $attribute) {
            $productAttribute = $attribute->getProductAttribute();
            $attributeId = $productAttribute->getId();
            $info = array(
               'id'        => $productAttribute->getId(),
               'code'      => $productAttribute->getAttributeCode(),
               'label'     => $attribute->getLabel(),
               'options'   => array()
            );

            $optionPrices = array();
            $prices = $attribute->getPrices();
            if (is_array($prices)) {
                foreach ($prices as $value) {
                    if(!$this->_validateAttributeValue($attributeId, $value, $options)) {
                        continue;
                    }

                    $info['options'][] = array(
                        'id'    => $value['value_index'],
                        'label' => $value['label'],
                        'price' => $this->_preparePrice($value['pricing_value'], $value['is_percent']),
                        'products'   => isset($options[$attributeId][$value['value_index']]) ? $options[$attributeId][$value['value_index']] : array(),
                    );
                    $optionPrices[] = $this->_preparePrice($value['pricing_value'], $value['is_percent']);
                    //$this->_registerAdditionalJsPrice($value['pricing_value'], $value['is_percent']);
                }
            }
            /**
             * Prepare formated values for options choose
             */
            foreach ($optionPrices as $optionPrice) {
                foreach ($optionPrices as $additional) {
                    $this->_preparePrice(abs($additional-$optionPrice));
                }
            }
            if($this->_validateAttributeInfo($info)) {
               $attributes[$attributeId] = $info;
            }
        }

        $config = array(
            'attributes'=> $attributes,
            'template'  => str_replace('%s', '#{price}', $store->getCurrentCurrency()->getOutputFormat()),
            'basePrice' => $this->_registerJsPrice($this->_convertPrice($this->getProduct()->getFinalPrice())),
            'oldPrice'  => $this->_registerJsPrice($this->_convertPrice($this->getProduct()->getPrice())),
            'productId' => $this->getProduct()->getId(),
            'chooseText'=> Mage::helper('catalog')->__('Choose option...'),
        );

        return Zend_Json::encode($config);
    }

    protected function _preparePrice($price, $isPercent=false)
    {
        if ($isPercent && !empty($price)) {
            $price = $this->getProduct()->getFinalPrice()*$price/100;
        }

        return $this->_registerJsPrice($this->_convertPrice($price, true));
    }

    protected function _registerJsPrice($price)
    {
        $jsPrice = str_replace(',', '.', $price);
        return $jsPrice;
    }

    protected function _convertPrice($price, $round=false)
    {
        if (empty($price)) {
            return 0;
        }

        $price = Mage::app()->getStore()->convertPrice($price);
        if ($round) {
            $price = Mage::app()->getStore()->roundPrice($price);
        }

        return $price;
    }
}