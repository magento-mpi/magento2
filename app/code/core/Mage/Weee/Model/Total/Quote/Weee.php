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
 * @package    Mage_Weee
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


class Mage_Weee_Model_Total_Quote_Weee extends Mage_Sales_Model_Quote_Address_Total_Tax
{
    public function __construct(){
        $this->setCode('weee');
    }

    public function collect(Mage_Sales_Model_Quote_Address $address)
    {
        $totalWeeeTax = 0;
        $baseTotalWeeeTax = 0;

        $items = $address->getAllItems();
        if (!count($items)) {
            return $this;
        }

        foreach ($items as $item) {
            if ($item->getParentItemId()) {
                continue;
            }

            if ($item->getHasChildren() && $item->isChildrenCalculated()) {
                foreach ($item->getChildren() as $child) {
                    $this->_processItem($address, $child);

                    $totalWeeeTax += $child->getWeeeTaxAppliedRowAmount();
                    $baseTotalWeeeTax += $child->getBaseWeeeTaxAppliedRowAmount();
                }
            } else {
                $this->_processItem($address, $item);

                $totalWeeeTax += $item->getWeeeTaxAppliedRowAmount();
                $baseTotalWeeeTax += $item->getBaseWeeeTaxAppliedRowAmount();
            }
        }

        $address->setTaxAmount($address->getTaxAmount() + $totalWeeeTax);
        $address->setBaseTaxAmount($address->getBaseTaxAmount() + $baseTotalWeeeTax);

        $address->setGrandTotal($address->getGrandTotal() + $totalWeeeTax);
        $address->setBaseGrandTotal($address->getBaseGrandTotal() + $baseTotalWeeeTax);
        return $this;
    }

    protected function _processItem(Mage_Sales_Model_Quote_Address $address, $item)
    {
        $item->setBaseWeeeTaxAppliedAmount(0);
        $item->setBaseWeeeTaxAppliedRowAmount(0);

        $item->setWeeeTaxAppliedAmount(0);
        $item->setWeeeTaxAppliedRowAmount(0);

        $store = $address->getQuote()->getStore();

        $attributes = Mage::helper('weee')->getProductWeeeAttributes(
            $item->getProduct(),
            $address,
            $address->getQuote()->getBillingAddress(),
            $store->getWebsiteId()
        );

        $applied = array();
        $productTaxes = array();

        foreach ($attributes as $k=>$attribute) {
            $baseValue = $attribute->getAmount();
            $value = $store->convertPrice($baseValue);

            $rowValue = $value*$item->getQty();
            $baseRowValue = $baseValue*$item->getQty();

            $title = $attribute->getName();

            if ($item->getDiscountPercent() && Mage::helper('weee')->isDiscounted()) {
                $valueDiscount = $value/100*$item->getDiscountPercent();
                $baseValueDiscount = $baseValue/100*$item->getDiscountPercent();

                $rowValueDiscount = $rowValue/100*$item->getDiscountPercent();
                $baseRowValueDiscount = $baseRowValue/100*$item->getDiscountPercent();


//                $value        = $store->roundPrice($value-$valueDiscount);
//                $baseValue    = $store->roundPrice($baseValue-$baseValueDiscount);
//                $rowValue     = $store->roundPrice($rowValue-$rowValueDiscount);
//                $baseRowValue = $store->roundPrice($baseRowValue-$baseRowValueDiscount);


                $address->setDiscountAmount($address->getDiscountAmount()+$rowValueDiscount);
                $address->setBaseDiscountAmount($address->getBaseDiscountAmount()+$baseRowValueDiscount);
                
                $address->setGrandTotal($address->getGrandTotal() - $rowValueDiscount);
                $address->setBaseGrandTotal($address->getBaseGrandTotal() - $baseRowValueDiscount);
            }

            $productTaxes[] = array(
                'title'=>$title,
                'base_amount'=>$baseValue,
                'amount'=>$value,
                'row_amount'=>$rowValue,
                'base_row_amount'=>$baseRowValue
            );

            $applied[] = array(
                'id'=>$attribute->getCode(),
                'percent'=>null,
                'rates' => array(array(
                    'amount'=>$rowValue,
                    'base_amount'=>$baseRowValue,
                    'base_real_amount'=>$baseRowValue,
                    'code'=>$attribute->getCode(),
                    'title'=>$title,
                    'percent'=>null,
                    'position'=>1,
                    'priority'=>-1000+$k,
                ))
            );


            $item->setBaseWeeeTaxAppliedAmount($item->getBaseWeeeTaxAppliedAmount() + $baseValue);
            $item->setBaseWeeeTaxAppliedRowAmount($item->getBaseWeeeTaxAppliedRowAmount() + $baseRowValue);

            $item->setWeeeTaxAppliedAmount($item->getWeeeTaxAppliedAmount() + $value);
            $item->setWeeeTaxAppliedRowAmount($item->getWeeeTaxAppliedRowAmount() + $rowValue);
        }

        Mage::helper('weee')->setApplied($item, $productTaxes);

        if ($applied) {
            $this->_saveAppliedTaxes(
               $address,
               $applied,
               $item->getWeeeTaxAppliedAmount(),
               $item->getBaseWeeeTaxAppliedAmount(),
               null
            );
        }
    }

    public function fetch(Mage_Sales_Model_Quote_Address $address)
    {
        return $this;
    }
}