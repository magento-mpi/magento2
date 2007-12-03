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
    protected $_innerCache = array();

    public function getButton($type, Varien_Object $entity)
    {
        if (!$this->isMessagesAviable($type, $entity)) {
            return '&nbsp;';
        }

        return Mage::getSingleton('core/layout')->createBlock('giftmessage/message_helper')
            ->setId('giftmessage_button_' . $this->_nextId++)
            ->setCanDisplayContainer(true)
            ->setEntity($entity)
            ->setType($type)->toHtml();
    }


    public function getInline($type, Varien_Object $entity)
    {
        if (!$this->isMessagesAviable($type, $entity)) {
            return '';
        }

        return Mage::getSingleton('core/layout')->createBlock('giftmessage/message_inline')
            ->setId('giftmessage_form_' . $this->_nextId++)
            ->setCanDisplayContainer(true)
            ->setEntity($entity)
            ->setType($type)->toHtml();
    }

    public function getAdminButton($type, Varien_Object $entity, $store=null)
    {
        if (!$this->isMessagesAviable($type, $entity, $store)) {
            return '&nbsp;';
        }

        return Mage::getSingleton('core/layout')->createBlock('adminhtml/giftmessage_helper')
            ->setId('giftmessage_button_' . $this->_nextId++)
            ->setCanDisplayContainer(true)
            ->setEntity($entity)
            ->setType($type)->toHtml();
    }

    public function isMessagesAviable($type, Varien_Object $entity, $store=null)
    {
        if(is_null($store)) {
             $result = Mage::getStoreConfig(self::XPATH_CONFIG_GIFT_MESSAGE_ALLOW);
        } else {
            if(is_object($store)) {
                $result = $store->getConfig(self::XPATH_CONFIG_GIFT_MESSAGE_ALLOW);
            } else {
                if(!$this->isCached('aviable_store_' . $store)) {
                    $this->setCached('aviable_store_' . $store, Mage::getModel('core/store')->load($store)->getConfig(self::XPATH_CONFIG_GIFT_MESSAGE_ALLOW));
                }
                $result = $this->getCached('aviable_store_' . $store);
            }
        }

        if ($result) {
            if ($type=='item') {
                return $entity->getProduct()->getGiftMessageAviable();
            } elseif ($type=='order_item') {
                return $entity->getGiftMessageAviable();
            }
            elseif ($type=='address_item') {
                if(!$this->isCached('address_item_' . $entity->getProductId())) {
                    $this->setCached('address_item_' . $entity->getProductId(), Mage::getModel('catalog/product')->load($entity->getProductId())->getGiftMessageAviable());
                }
                return $this->getCached('address_item_' . $entity->getProductId());
            } else {
                return true;
            }
        }

        return false;
    }

    public function getIsMessagesAviable($type, Varien_Object $entity, $store=null)
    {
        return $this->isMessagesAviable($type, $entity, $store);
    }

    public function getEscapedGiftMessage(Varien_Object $entity)
    {
        if($entity->getGiftMessageId()) {
            $message = $this->getGiftMessage($entity->getGiftMessageId());
            return $this->htmlEscape($message->getMessage());
        }
        return null;
    }

    public function getCached($key)
    {
        if($this->isCached($key)) {
            return $this->_innerCache[$key];
        }

        return null;
    }

    public function isCached($key)
    {
        return isset($this->_innerCache[$key]);
    }

    public function setCached($key, $value)
    {
        $this->_innerCache[$key] = $value;
        return $this;
    }

    public function getAviableForQuoteItems($quote, $store=null)
    {
        foreach($quote->getAllItems() as $item) {
            if($this->isMessagesAviable('item', $item, $store)) {
                return true;
            }
        }

        return false;
    }

    public function getAviableForAddressItems($items, $store=null)
    {
        foreach($items as $item) {
            if($this->isMessagesAviable('address_item', $item, $store)) {
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