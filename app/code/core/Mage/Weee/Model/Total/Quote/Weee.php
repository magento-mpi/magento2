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

        $store = $address->getQuote()->getStore();

        $items = $address->getAllItems();
        if (!count($items)) {
            return $this;
        }

        $request = Mage::getSingleton('tax/calculation')->getRateRequest($address, $address->getQuote()->getBillingAddress(), null, $store);

        foreach ($items as $item) {
            /**
             * Child item's tax we calculate for parent
             */
            if ($item->getParentItemId()) {
                continue;
            }
            /**
             * We calculate parent tax amount as sum of children's tax amounts
             */

            if ($item->getHasChildren() && $item->isChildrenCalculated()) {
                foreach ($item->getChildren() as $child) {
                    $weeeTax = Mage::helper('weee')->getAmount(
                        $child->getProduct(),
                        $address,
                        $address->getQuote()->getBillingAddress(),
                        $store->getWebsiteId()
                    );

                    $applied = Mage::helper('weee')->getAppliedRates(
                        $child,
                        $address,
                        $address->getQuote()->getBillingAddress(),
                        $store->getWebsiteId()
                    );

                    $this->_saveAppliedTaxes(
                       $address,
                       $applied,
                       $child->getWeeeTaxAppliedAmount(),
                       $child->getBaseWeeeTaxAppliedAmount(),
                       null
                    );

                    $totalWeeeTax += $child->getWeeeTaxAppliedRowAmount();
                    $baseTotalWeeeTax += $child->getBaseWeeeTaxAppliedRowAmount();

                    $address->setTaxAmount($address->getTaxAmount() + $child->getWeeeTaxAppliedRowAmount());
                    $address->setBaseTaxAmount($address->getBaseTaxAmount() + $child->getWeeeTaxAppliedRowAmount());
                }
            }
            else {
                $weeeTax = Mage::helper('weee')->getAmount(
                    $item->getProduct(),
                    $address,
                    $address->getQuote()->getBillingAddress(),
                    $store->getWebsiteId()
                );

                $applied = Mage::helper('weee')->getAppliedRates(
                    $item,
                    $address,
                    $address->getQuote()->getBillingAddress(),
                    $store->getWebsiteId()
                );

                $this->_saveAppliedTaxes(
                   $address,
                   $applied,
                   $item->getWeeeTaxAppliedAmount(),
                   $item->getBaseWeeeTaxAppliedAmount(),
                   null
                );


                $totalWeeeTax += $item->getWeeeTaxAppliedRowAmount();
                $baseTotalWeeeTax += $item->getBaseWeeeTaxAppliedRowAmount();

                $address->setTaxAmount($address->getTaxAmount() + $item->getWeeeTaxAppliedRowAmount());
                $address->setBaseTaxAmount($address->getBaseTaxAmount() + $item->getBaseWeeeTaxAppliedRowAmount());
            }
        }

        $address->setGrandTotal($address->getGrandTotal() + $totalWeeeTax);
        $address->setBaseGrandTotal($address->getBaseGrandTotal() + $baseTotalWeeeTax);
        return $this;
    }

    public function fetch(Mage_Sales_Model_Quote_Address $address)
    {
        return $this;
    }
}
