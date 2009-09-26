<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
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
 * @category    Enterprise
 * @package     Enterprise_GiftCard
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

class Enterprise_GiftCard_Block_Catalog_Product_Price extends Mage_Catalog_Block_Product_Price
{
    protected $_amountCache = array();
    protected $_minMaxCache = array();

    public function getMinAmount($product = null)
    {
        $minMax = $this->_calcMinMax($product);
        return $minMax['min'];
    }

    public function getMaxAmount($product = null)
    {
        $minMax = $this->_calcMinMax($product);
        return $minMax['max'];
    }

    protected function _calcMinMax($product = null)
    {
        if (is_null($product)) {
            $product = $this->getProduct();
        }
        if (!isset($this->_minMaxCache[$product->getId()])) {
            $min = $max = null;
            if ($product->getAllowOpenAmount()) {
                $openMin = $product->getOpenAmountMin();
                $openMax = $product->getOpenAmountMax();

                if ($openMin) {
                    $min = $openMin;
                } else {
                    $min = 0;
                }
                if ($openMax) {
                    $max = $openMax;
                } else {
                    $max = 0;
                }
            }

            foreach ($this->_getAmounts($product) as $amount) {
                if ($amount) {
                    if (is_null($min)) {
                        $min = $amount;
                    }
                    if (is_null($max)) {
                        $max = $amount;
                    }

                    $min = min($min, $amount);
                    if ($max != 0) {
                        $max = max($max, $amount);
                    }
                }
            }

            $this->_minMaxCache[$product->getId()] = array('min'=>$min, 'max'=>$max);
        }
        return $this->_minMaxCache[$product->getId()];
    }

    protected function _getAmounts($product = null)
    {
        if (is_null($product)) {
            $product = $this->getProduct();
        }
        if (!isset($this->_amountCache[$product->getId()])) {
            $result = array();

            $giftcardAmounts = $product->getPriceModel()->getAmounts($product);
            if (is_array($giftcardAmounts)) {
                foreach ($giftcardAmounts as $amount) {
                    $result[] = Mage::app()->getStore()->roundPrice($amount['website_value']);
                }
            }
            sort($result);
            $this->_amountCache[$product->getId()] = $result;
        }
        return $this->_amountCache[$product->getId()];
    }
}
