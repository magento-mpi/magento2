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
     * @return Magento_Customer_Model_Session
     */
    protected function _getSession()
    {
        return Mage::getSingleton('Magento_Customer_Model_Session');
    }

    /**
     * Customer address data object before load processing
     * Set gift registry item id flag
     *
     * @param Magento_Event_Observer $observer
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
     * @param Magento_Event_Observer $observer
     * @return Enterprise_GiftRegistry_Model_Observer
     */
    public function addressDataAfterLoad($observer)
    {
        $object = $observer->getEvent()->getDataObject();

        if ($registryItemId = $object->getGiftregistryItemId()) {
            $model = Mage::getModel('Enterprise_GiftRegistry_Model_Entity')
                ->loadByEntityItem($registryItemId);
            if ($model->getId()) {
                $object->setId(
                    Mage::helper('Enterprise_GiftRegistry_Helper_Data')->getAddressIdPrefix() . $model->getId()
                );
                $object->setCustomerId($this->_getSession()->getCustomer()->getId());
                $object->addData($model->exportAddress()->getData());
            }
        }
        return $this;
    }

    /**
     * Hide customer address on the frontend if it is gift registry shipping address
     *
     * @param Magento_Event_Observer $observer
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
     * @param Magento_Event_Observer $observer
     * @return Enterprise_GiftRegistry_Model_Observer
     */
    public function addressFormatAdmin($observer)
    {
        if (Mage::getDesign()->getArea() == Magento_Core_Model_App_Area::AREA_FRONTEND) {
            $this->_addressFormat($observer);
        }
        return $this;
    }

    /**
     * Hide customer address if it is gift registry shipping address
     *
     * @param Magento_Event_Observer $observer
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
            $type->setDefaultFormat(__("Ship to the recipient's address."));
        } elseif ($type->getPrevFormat()) {
            $type->setDefaultFormat($type->getPrevFormat());
        }
        return $this;
    }

    /**
     * Copy gift registry item id flag from quote item to order item
     *
     * @param Magento_Event_Observer $observer
     * @return Enterprise_GiftRegistry_Model_Observer
     */
    public function convertItems($observer)
    {
        $orderItem = $observer->getEvent()->getOrderItem();
        $item = $observer->getEvent()->getItem();

        if ($item instanceof Magento_Sales_Model_Quote_Address_Item) {
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
     * @param Magento_Event_Observer $observer
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
     * @param Magento_Event_Observer $observer
     * @return Enterprise_GiftRegistry_Model_Observer
     */
    public function addGiftRegistryQuoteFlag(Magento_Event_Observer $observer)
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

    /**
     * Clean up gift registry items that belongs to the product.
     *
     * @param Magento_Event_Observer $observer
     * @return Enterprise_Cms_Model_Observer
     */
    public function deleteProduct(Magento_Event_Observer $observer)
    {
        /** @var $product Magento_Catalog_Model_Product */
        $product = $observer->getEvent()->getProduct();

        if ($product->getParentId()) {
            $productId = $product->getParentId();
        } else {
            $productId = $product->getId();
        }

        /** @var $grItem Enterprise_GiftRegistry_Model_Item */
        $grItem = Mage::getModel('Enterprise_GiftRegistry_Model_Item');
        /** @var $collection Enterprise_GiftRegistry_Model_Resource_Item_Collection */
        $collection = $grItem->getCollection()->addProductFilter($productId);

        foreach($collection->getItems() as $item) {
            $item->delete();
        }

        /** @var $options Enterprise_GiftRegistry_Model_Item_Option*/
        $options = Mage::getModel('Enterprise_GiftRegistry_Model_Item_Option');
        $optionCollection = $options->getCollection()->addProductFilter($productId);

        $itemsArray = array();
        foreach($optionCollection->getItems() as $optionItem) {
            $itemsArray[$optionItem->getItemId()]  = $optionItem->getItemId();
        }

        $collection = $grItem->getCollection()->addItemFilter(array_keys($itemsArray));

        foreach($collection->getItems() as $item) {
            $item->delete();
        }

        return $this;
    }

    /**
     * Assign a flag to HTML head block signaling whether GiftRegistry is enabled or not
     *
     * @param Magento_Event_Observer $observer
     */
    public function assignHtmlHeadRenderingFlag(Magento_Event_Observer $observer)
    {
        /** @var $layout Magento_Core_Model_Layout */
        $layout = $observer->getEvent()->getLayout();
        /** @var $blockHead Magento_Page_Block_Html_Head */
        $blockHead = $layout->getBlock('head');
        if ($blockHead && $this->isGiftregistryEnabled()) {
            $blockHead->setData('giftregistry_enabled', true);
        }
    }
}
