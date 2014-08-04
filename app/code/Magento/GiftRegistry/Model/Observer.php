<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GiftRegistry\Model;

/**
 * Gift registry observer model
 */
class Observer
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Magento\GiftRegistry\Model\EntityFactory
     */
    protected $entityFactory;

    /**
     * @var \Magento\GiftRegistry\Model\ItemFactory
     */
    protected $itemFactory;

    /**
     * @var \Magento\GiftRegistry\Model\Item\OptionFactory
     */
    protected $optionFactory;

    /**
     * Module enabled flag
     * @var bool
     */
    protected $_isEnabled;

    /**
     * Gift registry data
     *
     * @var \Magento\GiftRegistry\Helper\Data
     */
    protected $_giftRegistryData = null;

    /**
     * Design package instance
     *
     * @var \Magento\Framework\View\DesignInterface
     */
    protected $_design = null;

    /**
     * @param \Magento\GiftRegistry\Helper\Data $giftRegistryData
     * @param \Magento\Framework\View\DesignInterface $design
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\GiftRegistry\Model\EntityFactory $entityFactory
     * @param \Magento\GiftRegistry\Model\ItemFactory $itemFactory
     * @param \Magento\GiftRegistry\Model\Item\OptionFactory $optionFactory
     */
    public function __construct(
        \Magento\GiftRegistry\Helper\Data $giftRegistryData,
        \Magento\Framework\View\DesignInterface $design,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\GiftRegistry\Model\EntityFactory $entityFactory,
        \Magento\GiftRegistry\Model\ItemFactory $itemFactory,
        \Magento\GiftRegistry\Model\Item\OptionFactory $optionFactory
    ) {
        $this->_giftRegistryData = $giftRegistryData;
        $this->_design = $design;
        $this->_isEnabled = $this->_giftRegistryData->isEnabled();
        $this->customerSession = $customerSession;
        $this->entityFactory = $entityFactory;
        $this->itemFactory = $itemFactory;
        $this->optionFactory = $optionFactory;
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
        return $this->customerSession;
    }

    /**
     * Customer address data object before load processing
     * Set gift registry item id flag
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function addressDataBeforeLoad($observer)
    {
        $addressId = $observer->getEvent()->getValue();

        if (!is_numeric($addressId)) {
            $prefix = $this->_giftRegistryData->getAddressIdPrefix();
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
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function addressDataAfterLoad($observer)
    {
        $object = $observer->getEvent()->getDataObject();

        if ($registryItemId = $object->getGiftregistryItemId()) {
            $model = $this->entityFactory->create()->loadByEntityItem($registryItemId);
            if ($model->getId()) {
                $object->setId($this->_giftRegistryData->getAddressIdPrefix() . $registryItemId);
                $object->setCustomerId($this->_getSession()->getCustomer()->getId());
                $object->addData($model->exportAddress()->getData());
            }
        }
        return $this;
    }

    /**
     * Check if gift registry prefix is set for customer address id
     * and set giftRegistryItemId
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function addressDataBeforeSave($observer)
    {
        $object = $observer->getEvent()->getDataObject();
        $addressId = $object->getCustomerAddressId();
        $prefix = $this->_giftRegistryData->getAddressIdPrefix();

        if (!is_numeric($addressId) && preg_match('/^' . $prefix . '([0-9]+)$/', $addressId)) {
            $object->setGiftregistryItemId(str_replace($prefix, '', $addressId));
        }
        return $this;
    }

    /**
     * Hide customer address on the frontend if it is gift registry shipping address
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function addressFormatFront($observer)
    {
        $this->_addressFormat($observer);
        return $this;
    }

    /**
     * Hide customer address in admin panel if it is gift registry shipping address
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function addressFormatAdmin($observer)
    {
        if ($this->_design->getArea() == \Magento\Framework\App\Area::AREA_FRONTEND) {
            $this->_addressFormat($observer);
        }
        return $this;
    }

    /**
     * Hide customer address if it is gift registry shipping address
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
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
     * After place order processing, update gift registry items fulfilled qty
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function orderPlaced($observer)
    {
        $order = $observer->getEvent()->getOrder();
        $item = $this->itemFactory->create();
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
            $entity = $this->entityFactory->create();
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
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function addGiftRegistryQuoteFlag(\Magento\Framework\Event\Observer $observer)
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
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function deleteProduct(\Magento\Framework\Event\Observer $observer)
    {
        /** @var $product \Magento\Catalog\Model\Product */
        $product = $observer->getEvent()->getProduct();

        if ($product->getParentId()) {
            $productId = $product->getParentId();
        } else {
            $productId = $product->getId();
        }

        /** @var $grItem Item */
        $grItem = $this->itemFactory->create();
        /** @var $collection \Magento\GiftRegistry\Model\Resource\Item\Collection */
        $collection = $grItem->getCollection()->addProductFilter($productId);

        foreach ($collection->getItems() as $item) {
            $item->delete();
        }

        /** @var $options \Magento\GiftRegistry\Model\Item\Option*/
        $options = $this->optionFactory->create();
        $optionCollection = $options->getCollection()->addProductFilter($productId);

        $itemsArray = array();
        foreach ($optionCollection->getItems() as $optionItem) {
            $itemsArray[$optionItem->getItemId()] = $optionItem->getItemId();
        }

        $collection = $grItem->getCollection()->addItemFilter(array_keys($itemsArray));

        foreach ($collection->getItems() as $item) {
            $item->delete();
        }

        return $this;
    }

    /**
     * Assign a flag to HTML head block signaling whether GiftRegistry is enabled or not
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function assignHtmlHeadRenderingFlag(\Magento\Framework\Event\Observer $observer)
    {
        /** @var $layout \Magento\Framework\View\LayoutInterface */
        $layout = $observer->getEvent()->getLayout();
        /** @var $blockHead \Magento\Theme\Block\Html\Head */
        $blockHead = $layout->getBlock('head');
        if ($blockHead && $this->isGiftregistryEnabled()) {
            $blockHead->setData('giftregistry_enabled', true);
        }
    }
}
