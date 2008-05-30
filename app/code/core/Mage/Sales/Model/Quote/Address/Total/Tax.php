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
 * @package    Mage_Sales
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


class Mage_Sales_Model_Quote_Address_Total_Tax extends Mage_Sales_Model_Quote_Address_Total_Abstract
{
    protected $_appliedTaxes = array();

    public function collect(Mage_Sales_Model_Quote_Address $address)
    {
        $store = $address->getQuote()->getStore();
        $address->setTaxAmount(0);
        $address->setBaseTaxAmount(0);

        $items = $address->getAllItems();
        if (!count($items)) {
            return $this;
        }
        $custTaxClassId = $address->getQuote()->getCustomerTaxClassId();

        $taxCalculationModel = Mage::getModel('tax/calculation');
        /* @var $taxCalculationModel Mage_Tax_Model_Calculation */
        $request = $taxCalculationModel->getRateRequest($address, $address->getQuote()->getBillingAddress(), $custTaxClassId, $store);

        foreach ($items as $item) {
        	$rate = $taxCalculationModel->getRate($request->setProductClassId($item->getProduct()->getTaxClassId()));
        	$this->_appliedTaxes = $taxCalculationModel->getAppliedRates($request);
//        	$item->setTaxString($tax->getRateCalculationString());
            $item->setTaxPercent($rate);
            $item->calcTaxAmount();
            $address->setTaxAmount($address->getTaxAmount() + $item->getTaxAmount());
            $address->setBaseTaxAmount($address->getBaseTaxAmount() + $item->getBaseTaxAmount());
        }

        $shippingTaxClass = Mage::getStoreConfig(Mage_Tax_Model_Config::CONFIG_XML_PATH_SHIPPING_TAX_CLASS, $store);
        if ($shippingTaxClass) {
            if ($rate = $taxCalculationModel->getRate($request->setProductClassId($shippingTaxClass))) {
                $shippingTax    = $address->getShippingAmount() * $rate/100;
                $shippingBaseTax= $address->getBaseShippingAmount() * $rate/100;
                $shippingTax    = $store->roundPrice($shippingTax);
                $shippingBaseTax= $store->roundPrice($shippingBaseTax);

                $address->setShippingTaxAmount($shippingTax);
                $address->setBaseShippingTaxAmount($shippingBaseTax);

                $address->setTaxAmount($address->getTaxAmount() + $shippingTax);
                $address->setBaseTaxAmount($address->getBaseTaxAmount() + $shippingBaseTax);
            }
        }

        $address->setGrandTotal($address->getGrandTotal() + $address->getTaxAmount());
        $address->setBaseGrandTotal($address->getBaseGrandTotal() + $address->getBaseTaxAmount());
        return $this;
    }

    protected function _getAppliedTaxes()
    {
        return $this->_appliedTaxes;
    }

    public function fetch(Mage_Sales_Model_Quote_Address $address)
    {
        $store = $address->getQuote()->getStore();
        $amount = $address->getTaxAmount();
        if ($amount!=0) {
            if (Mage::helper('tax')->displayFullSummary($store)) {
                $calculationTotal = $address->getSubtotal();
                if (Mage::helper('tax')->applyTaxAfterDiscount($store)) {
                    $calculationTotal -= $address->getDiscountAmount();
                }
                foreach ($this->_getAppliedTaxes() as $tax) {
                    $address->addTotal(array(
                        'code'=>$tax->getCode(),
                        'title'=>Mage::helper('sales')->__('Tax: %s - %d%%', $tax->getCode(), $tax->getRate()*1),
                        'value'=>$calculationTotal*$tax->getRate()/100,
                    ));
                }
            } else {
                $address->addTotal(array(
                    'code'=>$this->getCode(),
                    'title'=>Mage::helper('sales')->__('Tax'),
                    'value'=>$amount
                ));
            }
        }
        return $this;
    }
}