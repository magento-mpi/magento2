<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftMessage
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Gift message inline edit form
 *
 * @category   Magento
 * @package    Magento_GiftMessage
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_GiftMessage_Block_Message_Inline extends Magento_Core_Block_Template
{
    protected $_entity = null;
    protected $_type   = null;
    protected $_giftMessage = null;

    protected $_template = 'inline.phtml';

    /**
     * Gift message message
     *
     * @var Magento_GiftMessage_Helper_Message
     */
    protected $_giftMessageMessage = null;

    /**
     * @param Magento_GiftMessage_Helper_Message $giftMessageMessage
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param array $data
     */
    public function __construct(
        Magento_GiftMessage_Helper_Message $giftMessageMessage,
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        array $data = array()
    ) {
        $this->_giftMessageMessage = $giftMessageMessage;
        parent::__construct($coreData, $context, $data);
    }

    /**
     * Set entity
     *
     * @param $entity
     * @return Magento_GiftMessage_Block_Message_Inline
     */
    public function setEntity($entity)
    {
        $this->_entity = $entity;
        return $this;
    }

    /**
     * Get entity
     *
     * @return Magento_GiftMessage_Block_Message_Inline
     */
    public function getEntity()
    {
        return $this->_entity;
    }

    /**
     * Set type
     *
     * @param string $type
     * @return Magento_GiftMessage_Block_Message_Inline
     */
    public function setType($type)
    {
        $this->_type = $type;
        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->_type;
    }

    /**
     * Check if entity has gift message
     *
     * @return bool
     */
    public function hasGiftMessage()
    {
        return $this->getEntity()->getGiftMessageId() > 0;
    }

    /**
     * Init message
     *
     * @return Magento_GiftMessage_Block_Message_Inline
     */
    protected function _initMessage()
    {
        $this->_giftMessage = $this->helper('Magento_GiftMessage_Helper_Message')->getGiftMessage(
            $this->getEntity()->getGiftMessageId()
        );
        return $this;
    }

    /**
     * Get default value for From field
     *
     * @return string
     */
    public function getDefaultFrom()
    {
        if (Mage::getSingleton('Magento_Customer_Model_Session')->isLoggedIn()) {
            return Mage::getSingleton('Magento_Customer_Model_Session')->getCustomer()->getName();
        } else {
            return $this->getEntity()->getBillingAddress()->getName();
        }
    }

    /**
     * Get default value for To field
     *
     * @return string
     */
    public function getDefaultTo()
    {
        if ($this->getEntity()->getShippingAddress()) {
            return $this->getEntity()->getShippingAddress()->getName();
        } else {
            return $this->getEntity()->getName();
        }
    }

    /**
     * Retrieve message
     *
     * @param mixed $entity
     * @return string
     */
    public function getMessage($entity=null)
    {
        if (is_null($this->_giftMessage)) {
            $this->_initMessage();
        }

        if ($entity) {
            if (!$entity->getGiftMessage()) {
                $entity->setGiftMessage(
                    $this->helper('Magento_GiftMessage_Helper_Message')->getGiftMessage($entity->getGiftMessageId())
                );
            }
            return $entity->getGiftMessage();
        }

        return $this->_giftMessage;
    }

    /**
     * Retrieve items
     *
     * @return array
     */
    public function getItems()
    {
        if (!$this->getData('items')) {
            $items = array();

            $entityItems = $this->getEntity()->getAllItems();
            $this->_eventManager->dispatch('gift_options_prepare_items', array('items' => $entityItems));

            foreach ($entityItems as $item) {
                if ($item->getParentItem()) {
                    continue;
                }
                if ($this->isItemMessagesAvailable($item) || $item->getIsGiftOptionsAvailable()) {
                    $items[] = $item;
                }
            }
            $this->setData('items', $items);
        }
        return $this->getData('items');
    }

    /**
     * Retrieve additional url
     *
     * @return bool
     */
    public function getAdditionalUrl()
    {
        return $this->getUrl('*/*/getAdditional');
    }

    /**
     * Check if items are available
     *
     * @return bool
     */
    public function isItemsAvailable()
    {
        return count($this->getItems()) > 0;
    }

    /**
     * Return items count
     *
     * @return int
     */
    public function countItems()
    {
        return count($this->getItems());
    }

    /**
     * Check if items has messages
     *
     * @return bool
     */
    public function getItemsHasMesssages()
    {
        foreach ($this->getItems() as $item) {
            if ($item->getGiftMessageId()) {
                return true;
            }
        }
        return false;
    }

    /**
     * Check if entity has message
     *
     * @return bool
     */
    public function getEntityHasMessage()
    {
        return $this->getEntity()->getGiftMessageId() > 0;
    }

    /**
     * Return escaped value
     *
     * @param string $value
     * @param string $defaultValue
     * @return string
     */
    public function getEscaped($value, $defaultValue='')
    {
        return $this->escapeHtml(trim($value)!='' ? $value : $defaultValue);
    }

    /**
     * Check availability of giftmessages for specified entity
     *
     * @return bool
     */
    public function isMessagesAvailable()
    {
        return $this->_giftMessageMessage->isMessagesAvailable('quote', $this->getEntity());
    }

    /**
     * Check availability of giftmessages for specified entity item
     *
     * @param $item
     * @return bool
     */
    public function isItemMessagesAvailable($item)
    {
        $type = substr($this->getType(), 0, 5) == 'multi' ? 'address_item' : 'item';
        return $this->_giftMessageMessage->isMessagesAvailable($type, $item);
    }

    /**
     * Product thumbnail image url getter
     *
     * @param Magento_Catalog_Model_Product $product
     * @return string
     */
    public function getThumbnailUrl($product)
    {
        return (string)$this->helper('Magento_Catalog_Helper_Image')->init($product, 'thumbnail')
            ->resize($this->getThumbnailSize());
    }

    /**
     * Thumbnail image size getter
     *
     * @return int
     */
    public function getThumbnailSize()
    {
        return $this->getVar('product_thumbnail_image_size', 'Magento_Catalog');
    }
}
