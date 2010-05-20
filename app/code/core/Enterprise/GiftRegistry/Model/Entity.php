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
 * @package     Enterprise_GiftRegistry
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

/**
 * Entity data model
 */
class Enterprise_GiftRegistry_Model_Entity extends Enterprise_Enterprise_Model_Core_Abstract
{

    const XML_PATH_SHARE_EMAIL_IDENTITY = 'enterprise_giftregistry/sharing_email/identity';
    const XML_PATH_SHARE_EMAIL_TEMPLATE = 'enterprise_giftregistry/sharing_email/template';

   /**
     * Init resource model
     */
    function _construct() {
        $this->_init('enterprise_giftregistry/entity');
    }

    /**
     * Add items to registry
     *
     * @param array $itemsIds
     * @return Enterprise_GiftRegistry_Model_Entity
     */
    public function addQuoteItems($itemsIds)
    {
        if (is_array($itemsIds)) {
            foreach($itemsIds as $itemId) {
                $quoteItem = Mage::getModel('sales/quote_item')->load($itemId);
                if ($quoteItem && $quoteItem->getId()) {
                    $this->addItem($quoteItem);
                }
            }
        }
        return $this;
    }

    /**
     * Add new product to registry
     *
     * @param int|Mage_Sales_Model_Quote_Item $itemToAdd
     * @return Enterprise_GiftRegistry_Model_Item
     */
    public function addItem($itemToAdd)
    {
        if ($itemToAdd instanceof Mage_Sales_Model_Quote_Item) {
            $productId = $itemToAdd->getProductId();
            $qty = $itemToAdd->getQty();
        } else {
            $productId = $itemToAdd;
            $qty = 1;
        }

        $item = Mage::getModel('enterprise_giftregistry/item');
        $item->loadByProductRegistry($this->getId(), $productId);

        if ($item->getId()) {
            $item->setQty($item->getQty() + $qty)
                ->save();
        } else {
            $item->setEntityId($this->getId())
                ->setProductId($productId)
                ->setQty($qty)
                ->save();
        }
        return $item;
    }

    /**
     * Send share email
     *
     * @param string $email
     * @param int $storeId
     * @param string $message
     * @return bool
     */
    public function sendShareEmail($recipient, $storeId, $message, $sender = null)
    {
        $translate = Mage::getSingleton('core/translate');
        /* @var $translate Mage_Core_Model_Translate */
        $translate->setTranslateInline(false);

        $store = Mage::app()->getStore($this->getStoreId());
        $mail  = Mage::getModel('core/email_template');
        $template = $store->getConfig(self::XML_PATH_SHARE_EMAIL_TEMPLATE);

        if (is_array($recipient)) {
            $email = $recipient['email'];
        } else {
            $email = $recipient;
        }

        if (is_array($sender)) {
            $identity = $sender;
        } else {
            $identity = $store->getConfig(self::XML_PATH_SHARE_EMAIL_IDENTITY);
        }

        $templateVars = array(
            'store'   => $store,
            'message' => $message,
            'registry_link' => $this->getRegistryLink()
        );

        $mail->setDesignConfig(array('area' => 'frontend', 'store' => $storeId));
        $mail->sendTransactional($template, $identity, $email, null, $templateVars);

        $translate->setTranslateInline(true);

        if ($mail->getSentSuccess()) {
            return true;
        }
        return false;
    }

    /**
     * Return frontend registry link
     *
     * @return string
     */
    public function getRegistryLink()
    {
        return $this->getUrl('*/*/*');
    }

    /**
     * Return comma-separated list of entitity registrants
     *
     * @return string
     */
    public function getRegistrants()
    {
        $collection = $this->getRegistrantsCollection();
        if ($collection->getSize()) {
            $registrants = array();
            foreach($collection as $item) {
                $registrants[] =  $item->getFirstname().' '.$item->getLastname();
            }
            return implode(', ', $registrants);
        }
        return '';
    }

    /**
     * Return entity registrants collection
     *
     * @return Enterprise_GiftRegistry_Model_Mysql4_Person_Collection
     */
    public function getRegistrantsCollection()
    {
        $collection = Mage::getModel('enterprise_giftregistry/person')->getCollection()
            ->addRegistryFilter($this->getId());

        return $collection;
    }

    /**
     * Return entity items collection
     *
     * @return Enterprise_GiftRegistry_Model_Mysql4_Item_Collection
     */
    public function getItemsCollection()
    {
        $collection = Mage::getModel('enterprise_giftregistry/item')->getCollection()
            ->addRegistryFilter($this->getId());

        return $collection;
    }

    /**
     * Get active entity
     *
     * @param int $customerId
     * @return false|Enterprise_GiftRegistry_Model_Entity
     */
    public function getActiveEntity($customerId)
    {
        $collection = $this->getCollection()
            ->filterByCustomerId($customerId)
            ->filterByActive();

        foreach ($collection as $entity) {
            return $entity;
        }
        return false;
    }

    /**
     * Set active entity
     *
     * @param int $customerId
     * @param int $entityId
     * @return Enterprise_GiftRegistry_Model_Entity
     */
    public function setActiveEntity($customerId, $entityId)
    {
        $this->_getResource()->setActiveEntity($customerId, $entityId);
        return $this;
    }

    /**
     * Search
     *
     * @param array $params
     * @return array
     */
    public function search($params)
    {
        $params = $this->_prepareSearchParams($params);
        return $this->_getResource()->quickSearch($params);
    }

    /**
     * Prepare search params
     *
     * @param array $params
     * @return array
     */
    protected function _prepareSearchParams($params)
    {
        $params['store_id'] = Mage::app()->getStore()->getId();
        $params['website_id'] = Mage::app()->getStore()->getWebsiteId();

        if (isset($params['firstname'])) {
            $params['firstname'] = substr(trim($params['firstname']), 0 , 2);
        }

        if (isset($params['lastname'])) {
            $params['lastname'] = substr(trim($params['lastname']), 0 , 2);
        }

        return $params;
    }
}