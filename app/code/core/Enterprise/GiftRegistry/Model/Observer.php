<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_GiftRegistry
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Gift registry observer model
 */
class Enterprise_GiftRegistry_Model_Observer
{
    /**
     * Module enabled flag
     * @var bool
     */
    protected $_isEnabled;

    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->_isEnabled = Mage::helper('Enterprise_GiftRegistry_Helper_Data')->isEnabled();
    }

    /**
     * Check if giftregistry is enabled
     * @return bool
     */
    public function isGiftregistryEnabled()
    {
        return $this->_isEnabled;
    }

    /**
     * Retrieve customer session model object
     *
     * @return Mage_Customer_Model_Session
     */
    protected function _getSession()
    {
        return Mage::getSingleton('Mage_Customer_Model_Session');
    }

    /**
     * Customer address data object before load processing
     * Set gift registry item id flag
     *
     * @param Varien_Event_Observer $observer
     * @return Enterprise_GiftRegistry_Model_Observer
     */
    public function addressDataBeforeLoad($observer)
    {
        $addressId = $observer->getEvent()->getValue();

        if (!is_numeric($addressId)) {
            $prefix = Mage::helper('Enterprise_GiftRegistry_Helper_Data')->getAddressIdPrefix();
            $registryItemId = str_replace($prefix, '', $addressId);
            $object = $observer->getEvent()->getDataObject();
            $object->setGiftregistryItemId($registryItemId);
            $object->setCustomerAddressId($addressId);
        }
        return $this;
    }

    /**
     * Customer address data object after load
     * Check gift registry item id flag and set shipping address data to object
     *
     * @param Varien_Event_Observer $observer
     * @return Enterprise_GiftRegistry_Model_Observer
     */
    public function addressDataAfterLoad($observer)
    {
        $object = $observer->getEvent()->getDataObject();

        if ($registryItemId = $object->getGiftregistryItemId()) {
            $model = Mage::getModel('Enterprise_GiftRegistry_Model_Entity')
                ->loadByEntityItem($registryItemId);
            if ($model->getId()) {
                $object->setId(Mage::helper('Enterprise_GiftRegistry_Helper_Data')->getAddressIdPrefix() . $model->getId());
                $object->setCustomerId($this->_getSession()->getCustomer()->getId());
                $object->addData($model->exportAddress()->getData());
            }
        }
        return $this;
    }

    /**
     * Hide customer address on the frontend if it is gift registry shipping address
     *
     * @param Varien_Event_Observer $observer
     * @return Enterprise_GiftRegistry_Model_Observer
     */
    public function addressFormatFront($observer)
    {
        $this->_addressFormat($observer);
        return $this;
    }

    /**
     * Hide customer address in admin panel if it is gift registry shipping address
     *
     * @param Varien_Event_Observer $observer
     * @return Enterprise_GiftRegistry_Model_Observer
     */
    public function addressFormatAdmin($observer)
    {
        if (Mage::getDesign()->getArea() == 'frontend') {
            $this->_addressFormat($observer);
        }
        return $this;
    }

    /**
     * Hide customer address if it is gift registry shipping address
     *
     * @param Varien_Event_Observer $observer
     * @return Enterprise_GiftRegistry_Model_Observer
     */
    protected function _addressFormat($observer)
    {
        $type = $observer->getEvent()->getType();
        $address = $observer->getEvent()->getAddress();

        if ($address->getGiftregistryItemId()) {
            if (!$type->getPrevFormat()) {
                $type->setPrevFormat($type->getDefaultFormat());
            }
            $type->setDefaultFormat(Mage::helper('Enterprise_GiftRegistry_Helper_Data')->__("Ship to recipient's address."));
        } elseif ($type->getPrevFormat()) {
            $type->setDefaultFormat($type->getPrevFormat());
        }
        return $this;
    }

    /**
     * Copy gift registry item id flag from quote item to order item
     *
     * @param Varien_Event_Observer $observer
     * @return Enterprise_GiftRegistry_Model_Observer
     */
    public function convertItems($observer)
    {
        $orderItem = $observer->getEvent()->getOrderItem();
        $item = $observer->getEvent()->getItem();

        if ($item instanceof Mage_Sales_Model_Quote_Address_Item) {
            $registryItemId = $item->getQuoteItem()->getGiftregistryItemId();
        } else {
            $registryItemId = $item->getGiftregistryItemId();
        }

        if ($registryItemId) {
            $orderItem->setGiftregistryItemId($registryItemId);
        }
        return $this;
    }

    /**
     * After place order processing, update gift registry items fulfilled qty
     *
     * @param Varien_Event_Observer $observer
     * @return Enterprise_GiftRegistry_Model_Observer
     */
    public function orderPlaced($observer)
    {
        $order = $observer->getEvent()->getOrder();
        $item = Mage::getModel('Enterprise_GiftRegistry_Model_Item');
        $giftRegistries = array();
        $updatedQty = array();

        foreach ($order->getAllVisibleItems() as $orderItem) {
            if ($registryItemId = $orderItem->getGiftregistryItemId()) {
                $item->load($registryItemId);
                if ($item->getId()) {
                    $newQty = $item->getQtyFulfilled() + $orderItem->getQtyOrdered();
                    $item->setQtyFulfilled($newQty)->save();
                    $giftRegistries[] = $item->getEntityId();

                    $updatedQty[$registryItemId] = array(
                        'ordered' => $orderItem->getQtyOrdered(),
                        'fulfilled' => $newQty
                    );
                }
            }
        }

        $giftRegistries = array_unique($giftRegistries);
        if (count($giftRegistries)) {
            $entity = Mage::getModel('Enterprise_GiftRegistry_Model_Entity');
            foreach ($giftRegistries as $registryId) {
                $entity->load($registryId);
                $entity->sendUpdateRegistryEmail($updatedQty);
            }
        }
        return $this;
    }

    /**
     * Save page body to cache storage
     *
     * @param Varien_Event_Observer $observer
     * @return Enterprise_GiftRegistry_Model_Observer
     */
    public function addGiftRegistryQuoteFlag(Varien_Event_Observer $observer)
    {
        if (!$this->isGiftregistryEnabled()) {
            return $this;
        }
        $product = $observer->getEvent()->getProduct();
        $quoteItem = $observer->getEvent()->getQuoteItem();

        $giftregistryItemId = $product->getGiftregistryItemId();
        if ($giftregistryItemId) {
            $quoteItem->setGiftregistryItemId($giftregistryItemId);

            $parent = $quoteItem->getParentItem();
            if ($parent) {
                $parent->setGiftregistryItemId($giftregistryItemId);
            }
        }
        return $this;
    }
}
