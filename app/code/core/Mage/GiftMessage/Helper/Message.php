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
 * Gift Message helper
 *
 * @category   Mage
 * @package    Mage_GiftMessage
 * @author      Ivan Chepurnyi <mitch@varien.com>
 */
class Mage_GiftMessage_Helper_Message extends Mage_Core_Helper_Data
{
    const XPATH_CONFIG_GIFT_MESSAGE_ALLOW = 'sales/gift_messages/allow';
    protected $_nextId = 0;
    public function getButton($type, Varien_Object $entity)
    {
        if (!$this->isMessagesAviable($type, $entity)) {
            return '';
        }

        return Mage::getSingleton('core/layout')->createBlock('giftmessage/message_helper')
            ->setId('giftmessage_button_' . $this->_nextId++)
            ->setCanDisplayContainer(true)
            ->setEntity($entity)
            ->setType($type)->toHtml();
    }

    public function isMessagesAviable($type, Varien_Object $entity)
    {
        if (Mage::getStoreConfig(self::XPATH_CONFIG_GIFT_MESSAGE_ALLOW)) {
            if ($type=='item') {
                return $entity->getProduct()->getGiftMessageAviable();
            } elseif ($type=='adress_item') {
                return Mage::getModel('catalog/product')->load($entity->getProductId())->getGiftMessageAviable();
            } else {
                return true;
            }
        }

        return false;
    }

    public function getAviableForQuoteItems($quote)
    {
        foreach($quote->getAllItems() as $item) {
            if($item->getProduct()->getGiftMessageAviable()) {
                return true;
            }
        }
        return false;
    }

    public function getGiftMessage($messageId=null)
    {
        $message = Mage::getModel('giftmessage/message');
        if(!is_null($messageId)) {
            $message->load($messageId);
        }

        return $message;
    }
} // Class Mage_GiftMessage_Helper_Message End