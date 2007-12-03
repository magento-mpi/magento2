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
 * @package    Mage_GiftMessage
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Gift Message Observer Model
 *
 * @category   Mage
 * @package    Mage_GiftMessage
 * @author      Ivan Chepurnyi <mitch@varien.com>
 */
class Mage_GiftMessage_Model_Observer extends Varien_Object
{
    public function checkoutEventSetShippingItems($observer)
    {
        foreach($observer->getEvent()->getQuote()->getAllShippingAddresses() as $address) {
            foreach ($address->getItemsCollection() as $item) {
                if($item->getGiftMessageId()) {
                    $message = Mage::getModel('giftmessage/message')->load($item->getGiftMessageId());
                    $message->setId(null);
                    $message->save();
                    $item->setGiftMessageId($message->getId());
                    $item->save();
                }
            }
        }
        return $this;
    }

    public function checkoutEventMultishippingCreateOrder($observer)
    {
        $observer->getEvent()->getOrder()->setGiftMessageId($observer->getEvent()->getAddress()->getGiftMessageId());
        return $this;
    }

    public function checkoutEventCreateOrder($observer)
    {
        $observer->getEvent()->getOrder()->setGiftMessageId($observer->getEvent()->getQuote()->getGiftMessageId());
        return $this;
    }

    public function salesEventImportAddressItem($observer)
    {
        $observer->getEvent()->getOrderItem()
            ->setGiftMessageId($observer->getEvent()->getAddressItem()->getGiftMessageId())
            ->setGiftMessageAviable($this->_getAviable($observer->getEvent()->getAddressItem()->getProductId()));
        return $this;
    }

    protected function _getAviable($product)
    {
        if(is_object($product)) {
            return $product->getGiftMessageAviable();
        }
        return Mage::getModel('catalog/product')->load($product)->getGiftMessageAviable();
    }

    public function salesEventImportItem($observer)
    {
        $observer->getEvent()->getOrderItem()
            ->setGiftMessageAviable($this->_getAviable($observer->getEvent()->getQuoteItem()->getProduct()))
            ->setGiftMessageId($observer->getEvent()->getQuoteItem()->getGiftMessageId());
        return $this;
    }

    public function checkoutEventCreateGiftMessage($observer)
    {
        $giftMessages = $observer->getEvent()->getRequest()->getParam('giftmessage');
        if(is_array($giftMessages)) {
            foreach ($giftMessages as $entityId=>$message) {

                $giftMessage = Mage::getModel('giftmessage/message');
                $entity = $giftMessage->getEntityModelByType($message['type'])->load($entityId);


                if($entity->getGiftMessageId()) {
                    $giftMessage->load($entity->getGiftMessageId());
                }

                if(trim($message['message'])=='') {
                    if($giftMessage->getId()) {
                        try{
                            $giftMessage->delete();
                            $entity->setGiftMessageId(0)
                                ->save();
                        }
                        catch (Exception $e) { }
                    }
                    continue;
                }

                try {
                    $giftMessage->setSender($message['from'])
                        ->setRecipient($message['to'])
                        ->setMessage($message['message'])
                        ->save();

                    $entity->setGiftMessageId($giftMessage->getId())
                        ->save();
                }
                catch (Exception $e) { }
            }
        }
        return $this;
    }
} // Class Mage_GiftMessage_Model_Observer End