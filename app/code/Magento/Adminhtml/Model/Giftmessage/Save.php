<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Adminhtml giftmessage save model
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Adminhtml_Model_Giftmessage_Save extends Magento_Object
{
    protected $_saved = false;

    /**
     * Gift message message
     *
     * @var Magento_GiftMessage_Helper_Message
     */
    protected $_giftMessageMessage = null;

    /**
     * @var Magento_Adminhtml_Model_Session_Quote
     */
    protected $_session;

    /**
     * @var Magento_GiftMessage_Model_MessageFactory
     */
    protected $_messageFactory;

    /**
     * @var Magento_Catalog_Model_ProductFactory
     */
    protected $_productFactory;

    /**
     * @param Magento_Catalog_Model_ProductFactory $productFactory
     * @param Magento_GiftMessage_Model_MessageFactory $messageFactory
     * @param Magento_Adminhtml_Model_Session_Quote $session
     * @param Magento_GiftMessage_Helper_Message $giftMessageMessage
     */
    public function __construct(
        Magento_Catalog_Model_ProductFactory $productFactory,
        Magento_GiftMessage_Model_MessageFactory $messageFactory,
        Magento_Adminhtml_Model_Session_Quote $session,
        Magento_GiftMessage_Helper_Message $giftMessageMessage
    ) {
        $this->_productFactory = $productFactory;
        $this->_messageFactory = $messageFactory;
        $this->_session = $session;
        $this->_giftMessageMessage = $giftMessageMessage;
    }

    /**
     * Save all seted giftmessages
     *
     * @return Magento_Adminhtml_Model_Giftmessage_Save
     */
    public function saveAllInQuote()
    {
        $giftmessages = $this->getGiftmessages();

        if (!is_array($giftmessages)) {
            return $this;
        }

        foreach ($giftmessages as $entityId=>$giftmessage) {
            $this->_saveOne($entityId, $giftmessage);
        }

        return $this;
    }

    public function getSaved()
    {
        return $this->_saved;
    }

    public function saveAllInOrder()
    {
        $giftmessages = $this->getGiftmessages();

        if (!is_array($giftmessages)) {
            return $this;
        }

        foreach ($giftmessages as $entityId=>$giftmessage) {
            $this->_saveOne($entityId, $giftmessage);
        }

        return $this;
    }

    /**
     * Save a single gift message
     *
     * @param integer $entityId
     * @param array $giftmessage
     * @return Magento_Adminhtml_Model_Giftmessage_Save
     */
    protected function _saveOne($entityId, $giftmessage)
    {
        /* @var $giftmessageModel Magento_GiftMessage_Model_Message */
        $giftmessageModel = $this->_messageFactory->create();
        $entityType = $this->_getMappedType($giftmessage['type']);

        switch($entityType) {
            case 'quote':
                $entityModel = $this->_getQuote();
                break;

            case 'quote_item':
                $entityModel = $this->_getQuote()->getItemById($entityId);
                break;

            default:
                $entityModel = $giftmessageModel->getEntityModelByType($entityType)
                    ->load($entityId);
                break;
        }

        if (!$entityModel) {
            return $this;
        }

        if ($entityModel->getGiftMessageId()) {
            $giftmessageModel->load($entityModel->getGiftMessageId());
        }

        $giftmessageModel->addData($giftmessage);

        if ($giftmessageModel->isMessageEmpty() && $giftmessageModel->getId()) {
            // remove empty giftmessage
            $this->_deleteOne($entityModel, $giftmessageModel);
            $this->_saved = false;
        } elseif (!$giftmessageModel->isMessageEmpty()) {
            $giftmessageModel->save();
            $entityModel->setGiftMessageId($giftmessageModel->getId());
            if($entityType != 'quote') {
                $entityModel->save();
            }
            $this->_saved = true;
        }

        return $this;
    }

    /**
     * Delete a single gift message from entity
     *
     * @param Magento_GiftMessage_Model_Message|null $giftmessageModel
     * @param Magento_Object $entityModel
     * @return Magento_Adminhtml_Model_Giftmessage_Save
     */
    protected function _deleteOne($entityModel, $giftmessageModel=null)
    {
        if (is_null($giftmessageModel)) {
            $giftmessageModel = $this->_messageFactory->create()
                ->load($entityModel->getGiftMessageId());
        }
        $giftmessageModel->delete();
        $entityModel->setGiftMessageId(0)
            ->save();
        return $this;
    }

    /**
     * Set allowed quote items for gift messages
     *
     * @param array $items
     * @return Magento_Adminhtml_Model_Giftmessage_Save
     */
    public function setAllowQuoteItems($items)
    {
        $this->_getSession()->setAllowQuoteItemsGiftMessage($items);
        return $this;
    }

    /**
     * Add allowed quote item for gift messages
     *
     * @param int $item
     * @return Magento_Adminhtml_Model_Giftmessage_Save
     */
    public function addAllowQuoteItem($item)
    {
        $items = $this->getAllowQuoteItems();
        if (!in_array($item, $items)) {
            $items[] = $item;
        }
        $this->setAllowQuoteItems($items);

        return $this;
    }

    /**
     * Retrive allowed quote items for gift messages
     *
     * @return array
     */
    public function getAllowQuoteItems()
    {
        if (!is_array($this->_getSession()->getAllowQuoteItemsGiftMessage())) {
            $this->setAllowQuoteItems(array());
        }

        return $this->_getSession()->getAllowQuoteItemsGiftMessage();
    }

    /**
     * Retrive allowed quote items products for gift messages
     *
     * @return array
     */
    public function getAllowQuoteItemsProducts()
    {
        $result = array();
        foreach ($this->getAllowQuoteItems() as $itemId) {
            $item = $this->_getQuote()->getItemById($itemId);
            if (!$item) {
                continue;
            }
            $result[] = $item->getProduct()->getId();
        }
        return $result;
    }

    /**
     * Checks allowed quote item for gift messages
     *
     * @param  Magento_Object $item
     * @return boolean
     */
    public function getIsAllowedQuoteItem($item)
    {
        if (!in_array($item->getId(), $this->getAllowQuoteItems())) {
            if ($item->getGiftMessageId() && $this->isGiftMessagesAvailable($item)) {
                $this->addAllowQuoteItem($item->getId());
                return true;
            }
            return false;
        }

        return true;
    }

    /**
     * Retrieve is gift message available for item (product)
     *
     * @param Magento_Object $item
     * @return bool
     */
    public function isGiftMessagesAvailable($item)
    {
        return $this->_giftMessageMessage->getIsMessagesAvailable('item', $item, $item->getStore());
    }

    /**
     * Imports quote items for gift messages from products data
     *
     * @param unknown_type $products
     * @return unknown
     */
    public function importAllowQuoteItemsFromProducts($products)
    {
        $allowedItems = $this->getAllowQuoteItems();
        $deleteAllowedItems = array();
        foreach ($products as $productId=>$data) {
            $product = $this->_productFactory->create()
                ->setStore($this->_getSession()->getStore())
                ->load($productId);
            $item = $this->_getQuote()->getItemByProduct($product);

            if (!$item) {
                continue;
            }

            if (in_array($item->getId(), $allowedItems)
                && !isset($data['giftmessage'])) {
                $deleteAllowedItems[] = $item->getId();
            } elseif (!in_array($item->getId(), $allowedItems)
                      && isset($data['giftmessage'])) {
                $allowedItems[] = $item->getId();
            }

        }

        $allowedItems = array_diff($allowedItems, $deleteAllowedItems);

        $this->setAllowQuoteItems($allowedItems);
        return $this;
    }

    public function importAllowQuoteItemsFromItems($items)
    {
        $allowedItems = $this->getAllowQuoteItems();
        $deleteAllowedItems = array();
        foreach ($items as $itemId=>$data) {

            $item = $this->_getQuote()->getItemById($itemId);

            if (!$item) {
                // Clean not exists items
                $deleteAllowedItems[] = $itemId;
                continue;
            }

            if (in_array($item->getId(), $allowedItems)
                && !isset($data['giftmessage'])) {
                $deleteAllowedItems[] = $item->getId();
            } elseif (!in_array($item->getId(), $allowedItems)
                      && isset($data['giftmessage'])) {
                $allowedItems[] = $item->getId();
            }

        }

        $allowedItems = array_diff($allowedItems, $deleteAllowedItems);
        $this->setAllowQuoteItems($allowedItems);
        return $this;
    }

    /**
     * Retrive mapped type for entity
     *
     * @param string $type
     * @return string
     */
    protected function _getMappedType($type)
    {
        $map = array(
            'main'          =>  'quote',
            'item'          =>  'quote_item',
            'order'         =>  'order',
            'order_item'    =>  'order_item'
        );

        if (isset($map[$type])) {
            return $map[$type];
        }

        return null;
    }

    /**
     * Retrieve quote object
     *
     * @return Magento_Sales_Model_Quote
     */
    protected function _getQuote()
    {
        return $this->_session->getQuote();
    }
}
