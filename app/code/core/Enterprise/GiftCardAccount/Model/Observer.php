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
 * @category   Enterprise
 * @package    Enterprise_GiftCardAccount
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Enterprise_GiftCardAccount_Model_Observer extends Mage_Core_Model_Abstract
{
    /**
     * Charge all gift cards applied to the order
     * used for event: sales_order_place_after
     *
     * @param Varien_Event_Observer $observer
     * @return Enterprise_GiftCardAccount_Model_Observer
     */
    public function processOrderPlace(Varien_Event_Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();
        $cards = Mage::helper('enterprise_giftcardaccount')->getCards($order);
        if (is_array($cards)) {
            foreach ($cards as $card) {
                $args = array(
                    'amount'=>$card['ba'],
                    'giftcardaccount_id'=>$card['i'],
                );

                Mage::dispatchEvent('enterprise_giftcardaccount_charge', $args);
            }
        }
        return $this;
    }

    /**
     * Charge specified Gift Card (using code)
     * used for event: enterprise_giftcardaccount_charge_by_code
     *
     * @param Varien_Event_Observer $observer
     * @return Enterprise_GiftCardAccount_Model_Observer
     */
    public function chargeByCode(Varien_Event_Observer $observer)
    {
        $id = $observer->getEvent()->getGiftcardaccountCode();
        $amount = $observer->getEvent()->getAmount();

        Mage::getModel('enterprise_giftcardaccount/giftcardaccount')
            ->loadByCode($id)
            ->charge($amount)
            ->save();

        return $this;
    }

    /**
     * Charge specified Gift Card (using id)
     * used for event: enterprise_giftcardaccount_charge
     *
     * @param Varien_Event_Observer $observer
     * @return Enterprise_GiftCardAccount_Model_Observer
     */
    public function chargeById(Varien_Event_Observer $observer)
    {
        $id = $observer->getEvent()->getGiftcardaccountId();
        $amount = $observer->getEvent()->getAmount();

        Mage::getModel('enterprise_giftcardaccount/giftcardaccount')
            ->load($id)
            ->charge($amount)
            ->save();

        return $this;
    }

    /**
     * Increase order giftcards_amount_invoiced attribute based on created invoice
     * used for event: sales_order_invoice_save_after
     *
     * @param Varien_Event_Observer $observer
     * @return Enterprise_GiftCardAccount_Model_Observer
     */
    public function increaseOrderInvoicedAmount(Varien_Event_Observer $observer)
    {
        $invoice = $observer->getEvent()->getInvoice();
        $order = $invoice->getOrder();

        if ($invoice->getBaseGiftCardsAmount()) {
            $order->setBaseGiftCardsInvoiced($order->getBaseGiftCardsInvoiced() + $invoice->getBaseGiftCardsAmount());
            $order->setGiftCardsInvoiced($order->getGiftCardsInvoiced() + $invoice->getGiftCardsAmount());
        }

        return $this;
    }

    /**
     * Create gift card account on event
     * used for event: enterprise_giftcardaccount_create
     *
     * @param Varien_Event_Observer $observer
     * @return Enterprise_GiftCardAccount_Model_Observer
     */
    public function create(Varien_Event_Observer $observer)
    {
        $data = $observer->getEvent()->getRequest();
        $code = $observer->getEvent()->getCode();

        $model = Mage::getModel('enterprise_giftcardaccount/giftcardaccount')
            ->setStatus(Enterprise_GiftCardAccount_Model_Giftcardaccount::STATUS_ENABLED)
            ->setWebsiteId($data->getWebsiteId())
            ->setBalance($data->getAmount())
            ->setDateExpires($data->getLifetime())
            ->setIsRedeemable($data->getIsRedeemable())
            ->save();

        $code->setCode($model->getCode());

        return $this;
    }
}