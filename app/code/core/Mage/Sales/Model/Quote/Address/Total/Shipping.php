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
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


class Mage_Sales_Model_Quote_Address_Total_Shipping extends Mage_Sales_Model_Quote_Address_Total_Abstract
{
    public function collect(Mage_Sales_Model_Quote_Address $address)
    {
        $oldWeight = $address->getWeight();
        $address->setWeight(0);
        $address->setShippingAmount(0);
        $address->setFreeMethodWeight(0);

        $items = $address->getAllItems();
        if (!count($items)) {
            return $this;
        }

        $method = $address->getShippingMethod();
        $freeAddress = $address->getFreeShipping();

        $addressWeight      = $address->getWeight();
        $freeMethodWeight   = $address->getFreeMethodWeight();

        $addressQty = 0;

        foreach ($items as $item) {
            /**
             * Skip if this item is virtual
             */

            if ($item->getProduct()->getTypeInstance()->isVirtual()) {
                continue;
            }
            /**
             * Children weight we calculate for parent
             */
            if ($item->getParentItem()) {
                continue;
            }

            if ($item->getHasChildren() && $item->isShipSeparately()) {
                foreach ($item->getChildren() as $child) {
                    if ($child->getProduct()->getTypeInstance()->isVirtual()) {
                        continue;
                    }
                    $addressQty += $item->getQty()*$child->getQty();

                    if (!$item->getProduct()->getWeightType()) {
                        $itemWeight = $child->getWeight();
                        $itemQty    = $item->getQty()*$child->getQty();
                        $rowWeight  = $itemWeight*$itemQty;
                        $addressWeight += $rowWeight;
                        if ($freeAddress || $child->getFreeShipping()===true) {
                            $rowWeight = 0;
                        } elseif (is_numeric($child->getFreeShipping())) {
                            $freeQty = $child->getFreeShipping();
                            if ($itemQty>$freeQty) {
                                $rowWeight = $itemWeight*($itemQty-$freeQty);
                            }
                            else {
                                $rowWeight = 0;
                            }
                        }
                        $freeMethodWeight += $rowWeight;
                    }
                }
                if ($item->getProduct()->getWeightType()) {
                    $itemWeight = $item->getWeight();
                    $rowWeight  = $itemWeight*$item->getQty();
                    $addressWeight+= $rowWeight;
                    if ($freeAddress || $item->getFreeShipping()===true) {
                        $rowWeight = 0;
                    } elseif (is_numeric($item->getFreeShipping())) {
                        $freeQty = $item->getFreeShipping();
                        if ($item->getQty()>$freeQty) {
                            $rowWeight = $itemWeight*($item->getQty()-$freeQty);
                        }
                        else {
                            $rowWeight = 0;
                        }
                    }
                    $freeMethodWeight+= $rowWeight;
                }
            }
            else {
                if (!$item->getProduct()->getTypeInstance()->isVirtual()) {
                    $addressQty += $item->getQty();
                }
                $itemWeight = $item->getWeight();
                $rowWeight  = $itemWeight*$item->getQty();
                $addressWeight+= $rowWeight;
                if ($freeAddress || $item->getFreeShipping()===true) {
                    $rowWeight = 0;
                } elseif (is_numeric($item->getFreeShipping())) {
                    $freeQty = $item->getFreeShipping();
                    if ($item->getQty()>$freeQty) {
                        $rowWeight = $itemWeight*($item->getQty()-$freeQty);
                    }
                    else {
                        $rowWeight = 0;
                    }
                }
                $freeMethodWeight+= $rowWeight;
            }
        }

        if (isset($addressQty)) {
            $address->setItemQty($addressQty);
        }

        $address->setWeight($addressWeight);
        $address->setFreeMethodWeight($freeMethodWeight);

        $address->collectShippingRates();

        $address->setShippingAmount(0);
        $address->setBaseShippingAmount(0);

        $method = $address->getShippingMethod();

        if ($method) {
            foreach ($address->getAllShippingRates() as $rate) {
                if ($rate->getCode()==$method) {
                    $amountPrice = $address->getQuote()->getStore()->convertPrice($rate->getPrice(), false);
                    $address->setShippingAmount($amountPrice);
                    $address->setBaseShippingAmount($rate->getPrice());
                    $address->setShippingDescription($rate->getCarrierTitle().' - '.$rate->getMethodDescription());
                    break;
                }
            }
        }

        $address->setGrandTotal($address->getGrandTotal() + $address->getShippingAmount());
        $address->setBaseGrandTotal($address->getBaseGrandTotal() + $address->getBaseShippingAmount());
        return $this;
    }

    public function fetch(Mage_Sales_Model_Quote_Address $address)
    {
        $amount = $address->getShippingAmount();
        if ($amount!=0 || $address->getShippingDescription()) {
            $address->addTotal(array(
                'code'=>$this->getCode(),
                'title'=>Mage::helper('sales')->__('Shipping & Handling').' ('.$address->getShippingDescription().')',
                'value'=>$address->getShippingAmount()
            ));
        }
        return $this;
    }
}
