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

    /**
     * Assing gift message identifier from address item to order
     *
     * @param unknown_type $observer
     * @return unknown
     */
    public function checkoutEventMultishippingCreateOrder($observer)
    {
        $observer->getEvent()->getOrder()->setGiftMessageId($observer->getEvent()->getAddress()->getGiftMessageId());
        return $this;
    }

    /**
     * Assing gift message identifier from quote to order
     *
     * @param unknown_type $observer
     * @return unknown
     */
    public function checkoutEventCreateOrder($observer)
    {
        $observer->getEvent()->getOrder()->setGiftMessageId($observer->getEvent()->getQuote()->getGiftMessageId());
        return $this;
    }

    /**
     * Set gift messages to order item on import address item
     * in multiple shipping
     *
     * @param unknown_type $observer
     * @return unknown
     */
    public function salesEventImportAddressItem($observer)
    {
        $observer->getEvent()->getOrderItem()
            ->setGiftMessageId($observer->getEvent()->getAddressItem()->getGiftMessageId())
            ->setGiftMessageAviable($this->_getAviable($observer->getEvent()->getAddressItem()->getProductId()));
        return $this;
    }

    /**
     * Geter for aviable gift messages value from product
     *
     * @param Mage_Catalog_Model_Product|integer $product
     * @return integer|null
     */
    protected function _getAviable($product)
    {
        if(is_object($product)) {
            return $product->getGiftMessageAviable();
        }
        return Mage::getModel('catalog/product')->load($product)->getGiftMessageAviable();
    }

    /**
     * Set gift messages to order item on import
     *
     * @param Varien_Object $observer
     * @return Mage_GiftMessage_Model_Observer
     */
    public function salesEventImportItem($observer)
    {
        $observer->getEvent()->getOrderItem()
            ->setGiftMessageAviable($this->_getAviable($observer->getEvent()->getQuoteItem()->getProduct()))
            ->setGiftMessageId($observer->getEvent()->getQuoteItem()->getGiftMessageId());
        return $this;
    }

    /**
     * Operete with gift messages on checkout proccess
     *
     * @param Varieb_Object $observer
     * @return Mage_GiftMessage_Model_Observer
     */
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

    /**
     * Set giftmessage aviable default value to product
     * on catalog products collection load
     *
     * @param Varien_Object $observer
     * @return Mage_GiftMessage_Model_Observer
     */
    public function catalogEventProductCollectionAfterLoad($observer)
    {
        $collection = $observer->getEvent()->getCollection();
        foreach ($collection as $item) {
            if($item->getGiftMessageAviable()===null) {
                $item->setGiftMessageAviable(2);
            }
        }
        return $this;
    }
} // Class Mage_GiftMessage_Model_Observer End