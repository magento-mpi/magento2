<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftRegistry
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Gift registry observer model
 */
namespace Magento\GiftRegistry\Model;

class Observer
{
    /**
     * Module enabled flag
     * @var bool
     */
    protected $_isEnabled;

    /**
     * Design package instance
     *
     * @var \Magento\Core\Model\View\DesignInterface
     */
    protected $_design = null;

    /**
     * Class constructor
     *
     * @param \Magento\Core\Model\View\DesignInterface $design
     */
    public function __construct(
        \Magento\Core\Model\View\DesignInterface $design
    ) {
        $this->_design = $design;
        $this->_isEnabled = \Mage::helper('Magento\GiftRegistry\Helper\Data')->isEnabled();
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
     * @return \Magento\Customer\Model\Session
     */
    protected function _getSession()
    {
        return \Mage::getSingleton('Magento\Customer\Model\Session');
    }

    /**
     * Customer address data object before load processing
     * Set gift registry item id flag
     *
     * @param \Magento\Event\Observer $observer
     * @return \Magento\GiftRegistry\Model\Observer
     */
    public function addressDataBeforeLoad($observer)
    {
        $addressId = $observer->getEvent()->getValue();

        if (!is_numeric($addressId)) {
            $prefix = \Mage::helper('Magento\GiftRegistry\Helper\Data')->getAddressIdPrefix();
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
     * @param \Magento\Event\Observer $observer
     * @return \Magento\GiftRegistry\Model\Observer
     */
    public function addressDataAfterLoad($observer)
    {
        $object = $observer->getEvent()->getDataObject();

        if ($registryItemId = $object->getGiftregistryItemId()) {
            $model = \Mage::getModel('\Magento\GiftRegistry\Model\Entity')
                ->loadByEntityItem($registryItemId);
            if ($model->getId()) {
                $object->setId(
                    \Mage::helper('Magento\GiftRegistry\Helper\Data')->getAddressIdPrefix() . $model->getId()
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
     * @param \Magento\Event\Observer $observer
     * @return \Magento\GiftRegistry\Model\Observer
     */
    public function addressFormatFront($observer)
    {
        $this->_addressFormat($observer);
        return $this;
    }

    /**
     * Hide customer address in admin panel if it is gift registry shipping address
     *
     * @param \Magento\Event\Observer $observer
     * @return \Magento\GiftRegistry\Model\Observer
     */
    public function addressFormatAdmin($observer)
    {
        if ($this->_design->getArea() == \Magento\Core\Model\App\Area::AREA_FRONTEND) {
            $this->_addressFormat($observer);
        }
        return $this;
    }

    /**
     * Hide customer address if it is gift registry shipping address
     *
     * @param \Magento\Event\Observer $observer
     * @return \Magento\GiftRegistry\Model\Observer
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
     * @param \Magento\Event\Observer $observer
     * @return \Magento\GiftRegistry\Model\Observer
     */
    public function convertItems($observer)
    {
        $orderItem = $observer->getEvent()->getOrderItem();
        $item = $observer->getEvent()->getItem();

        if ($item instanceof \Magento\Sales\Model\Quote\Address\Item) {
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
     * @param \Magento\Event\Observer $observer
     * @return \Magento\GiftRegistry\Model\Observer
     */
    public function orderPlaced($observer)
    {
        $order = $observer->getEvent()->getOrder();
        $item = \Mage::getModel('\Magento\GiftRegistry\Model\Item');
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
            $entity = \Mage::getModel('\Magento\GiftRegistry\Model\Entity');
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
     * @param \Magento\Event\Observer $observer
     * @return \Magento\GiftRegistry\Model\Observer
     */
    public function addGiftRegistryQuoteFlag(\Magento\Event\Observer $observer)
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
     * @param \Magento\Event\Observer $observer
     * @return \Magento\Cms\Model\Observer
     */
    public function deleteProduct(\Magento\Event\Observer $observer)
    {
        /** @var $product \Magento\Catalog\Model\Product */
        $product = $observer->getEvent()->getProduct();

        if ($product->getParentId()) {
            $productId = $product->getParentId();
        } else {
            $productId = $product->getId();
        }

        /** @var $grItem \Magento\GiftRegistry\Model\Item */
        $grItem = \Mage::getModel('\Magento\GiftRegistry\Model\Item');
        /** @var $collection \Magento\GiftRegistry\Model\Resource\Item\Collection */
        $collection = $grItem->getCollection()->addProductFilter($productId);

        foreach($collection->getItems() as $item) {
            $item->delete();
        }

        /** @var $options \Magento\GiftRegistry\Model\Item\Option*/
        $options = \Mage::getModel('\Magento\GiftRegistry\Model\Item\Option');
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
     * @param \Magento\Event\Observer $observer
     */
    public function assignHtmlHeadRenderingFlag(\Magento\Event\Observer $observer)
    {
        /** @var $layout \Magento\Core\Model\Layout */
        $layout = $observer->getEvent()->getLayout();
        /** @var $blockHead \Magento\Page\Block\Html\Head */
        $blockHead = $layout->getBlock('head');
        if ($blockHead && $this->isGiftregistryEnabled()) {
            $blockHead->setData('giftregistry_enabled', true);
        }
    }
}
