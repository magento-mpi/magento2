<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Downloadable
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Downloadable Products Observer
 *
 * @category    Magento
 * @package     Magento_Downloadable
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Downloadable_Model_Observer
{
    const XML_PATH_DISABLE_GUEST_CHECKOUT   = 'catalog/downloadable/disable_guest_checkout';

    /**
     * @var Magento_Core_Helper_Data
     */
    protected $_helper;

    /**
     * Core store config
     *
     * @var Magento_Core_Model_Store_Config
     */
    protected $_coreStoreConfig;

    /**
     * @var Magento_Downloadable_Model_Link_PurchasedFactory
     */
    protected $_purchasedFactory;

    /**
     * @var Magento_Catalog_Model_ProductFactory
     */
    protected $_productFactory;

    /**
     * @var Magento_Downloadable_Model_Link_Purchased_ItemFactory
     */
    protected $_itemFactory;

    /**
     * @var Magento_Checkout_Model_Session
     */
    protected $_checkoutSession;

    /**
     * @var Magento_Downloadable_Model_Resource_Link_Purchased_Item_Collection
     */
    protected $_itemsFactory;

    /**
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Model_Store_Config $coreStoreConfig
     * @param Magento_Downloadable_Model_Link_PurchasedFactory $purchasedFactory
     * @param Magento_Catalog_Model_ProductFactory $productFactory
     * @param Magento_Downloadable_Model_Link_Purchased_ItemFactory $itemFactory
     * @param Magento_Checkout_Model_Session $checkoutSession
     * @param Magento_Downloadable_Model_Resource_Link_Purchased_Item_Collection $itemsFactory
     */
    public function __construct(
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Model_Store_Config $coreStoreConfig,
        Magento_Downloadable_Model_Link_PurchasedFactory $purchasedFactory,
        Magento_Catalog_Model_ProductFactory $productFactory,
        Magento_Downloadable_Model_Link_Purchased_ItemFactory $itemFactory,
        Magento_Checkout_Model_Session $checkoutSession,
        Magento_Downloadable_Model_Resource_Link_Purchased_Item_Collection $itemsFactory
    ) {
        $this->_helper = $coreData;
        $this->_coreStoreConfig = $coreStoreConfig;
        $this->_purchasedFactory = $purchasedFactory;
        $this->_productFactory = $productFactory;
        $this->_itemFactory = $itemFactory;
        $this->_checkoutSession = $checkoutSession;
        $this->_itemsFactory = $itemsFactory;
    }

    /**
     * Prepare product to save
     *
     * @param   Magento_Object $observer
     * @return  Magento_Downloadable_Model_Observer
     */
    public function prepareProductSave($observer)
    {
        $request = $observer->getEvent()->getRequest();
        $product = $observer->getEvent()->getProduct();

        if ($downloadable = $request->getPost('downloadable')) {
            $product->setDownloadableData($downloadable);
        }

        return $this;
    }
    /**
     * Change product type on the fly depending on selected options
     *
     * @param  Magento_Event_Observer $observer
     * @return Magento_Downloadable_Model_Observer
     */
    public function transitionProductType(Magento_Event_Observer $observer)
    {
        $request = $observer->getEvent()->getRequest();
        $product = $observer->getEvent()->getProduct();
        $downloadable = $request->getPost('downloadable');
        $isTransitionalType = $product->getTypeId() === Magento_Catalog_Model_Product_Type::TYPE_SIMPLE
            || $product->getTypeId() === Magento_Catalog_Model_Product_Type::TYPE_VIRTUAL
            || $product->getTypeId() === Magento_Downloadable_Model_Product_Type::TYPE_DOWNLOADABLE;

        if ($isTransitionalType) {
            if ($product->hasIsVirtual()) {
                if ($downloadable) {
                    $product->setTypeId(Magento_Downloadable_Model_Product_Type::TYPE_DOWNLOADABLE);
                } else {
                    $product->setTypeId(Magento_Catalog_Model_Product_Type::TYPE_VIRTUAL);
                }
            } else {
                $product->setTypeId(Magento_Catalog_Model_Product_Type::TYPE_SIMPLE);
            }
        }
        return $this;
    }

    /**
     * Save data from order to purchased links
     *
     * @param Magento_Object $observer
     * @return Magento_Downloadable_Model_Observer
     */
    public function saveDownloadableOrderItem($observer)
    {
        $orderItem = $observer->getEvent()->getItem();
        if (!$orderItem->getId()) {
            //order not saved in the database
            return $this;
        }
        $product = $orderItem->getProduct();
        if ($product && $product->getTypeId() != Magento_Downloadable_Model_Product_Type::TYPE_DOWNLOADABLE) {
            return $this;
        }
        $purchasedLink = $this->_createPurchasedModel()->load($orderItem->getId(), 'order_item_id');
        if ($purchasedLink->getId()) {
            return $this;
        }
        if (!$product) {
            $product = $this->_createProductModel()
                ->setStoreId($orderItem->getOrder()->getStoreId())
                ->load($orderItem->getProductId());
        }
        if ($product->getTypeId() == Magento_Downloadable_Model_Product_Type::TYPE_DOWNLOADABLE) {
            $links = $product->getTypeInstance()->getLinks($product);
            if ($linkIds = $orderItem->getProductOptionByCode('links')) {
                $linkPurchased = $this->_createPurchasedModel();
                $this->_helper->copyFieldsetToTarget(
                    'downloadable_sales_copy_order',
                    'to_downloadable',
                    $orderItem->getOrder(),
                    $linkPurchased
                );
                $this->_helper->copyFieldsetToTarget(
                    'downloadable_sales_copy_order_item',
                    'to_downloadable',
                    $orderItem,
                    $linkPurchased
                );
                $linkSectionTitle = (
                    $product->getLinksTitle()
                        ? $product->getLinksTitle()
                        : $this->_coreStoreConfig->getConfig(Magento_Downloadable_Model_Link::XML_PATH_LINKS_TITLE)
                );
                $linkPurchased->setLinkSectionTitle($linkSectionTitle)
                    ->save();
                foreach ($linkIds as $linkId) {
                    if (isset($links[$linkId])) {
                        $linkPurchasedItem = $this->_createPurchasedItemModel()
                            ->setPurchasedId($linkPurchased->getId())
                            ->setOrderItemId($orderItem->getId());

                        $this->_helper->copyFieldsetToTarget(
                            'downloadable_sales_copy_link',
                            'to_purchased',
                            $links[$linkId],
                            $linkPurchasedItem
                        );
                        $linkHash = strtr(base64_encode(microtime() . $linkPurchased->getId() . $orderItem->getId()
                            . $product->getId()), '+/=', '-_,');
                        $numberOfDownloads = $links[$linkId]->getNumberOfDownloads()*$orderItem->getQtyOrdered();
                        $linkPurchasedItem->setLinkHash($linkHash)
                            ->setNumberOfDownloadsBought($numberOfDownloads)
                            ->setStatus(Magento_Downloadable_Model_Link_Purchased_Item::LINK_STATUS_PENDING)
                            ->setCreatedAt($orderItem->getCreatedAt())
                            ->setUpdatedAt($orderItem->getUpdatedAt())
                            ->save();
                    }
                }
            }
        }

        return $this;
    }

    /**
     * Set checkout session flag if order has downloadable product(s)
     *
     * @param Magento_Object $observer
     * @return Magento_Downloadable_Model_Observer
     */
    public function setHasDownloadableProducts($observer)
    {
        if (!$this->_checkoutSession->getHasDownloadableProducts()) {
            $order = $observer->getEvent()->getOrder();
            foreach ($order->getAllItems() as $item) {
                /* @var $item Magento_Sales_Model_Order_Item */
                if ($item->getProductType() == Magento_Downloadable_Model_Product_Type::TYPE_DOWNLOADABLE
                    || $item->getRealProductType() == Magento_Downloadable_Model_Product_Type::TYPE_DOWNLOADABLE
                    || $item->getProductOptionByCode('is_downloadable')
                ) {
                    $this->_checkoutSession->setHasDownloadableProducts(true);
                    break;
                }
            }
        }
        return $this;
    }

    /**
     * Set status of link
     *
     * @param Magento_Object $observer
     * @return Magento_Downloadable_Model_Observer
     */
    public function setLinkStatus($observer)
    {
        $order = $observer->getEvent()->getOrder();

        if (!$order->getId()) {
            //order not saved in the database
            return $this;
        }

        /* @var $order Magento_Sales_Model_Order */
        $status = '';
        $linkStatuses = array(
            'pending'         => Magento_Downloadable_Model_Link_Purchased_Item::LINK_STATUS_PENDING,
            'expired'         => Magento_Downloadable_Model_Link_Purchased_Item::LINK_STATUS_EXPIRED,
            'avail'           => Magento_Downloadable_Model_Link_Purchased_Item::LINK_STATUS_AVAILABLE,
            'payment_pending' => Magento_Downloadable_Model_Link_Purchased_Item::LINK_STATUS_PENDING_PAYMENT,
            'payment_review'  => Magento_Downloadable_Model_Link_Purchased_Item::LINK_STATUS_PAYMENT_REVIEW
        );

        $downloadableItemsStatuses = array();
        $orderItemStatusToEnable = $this->_coreStoreConfig->getConfig(
            Magento_Downloadable_Model_Link_Purchased_Item::XML_PATH_ORDER_ITEM_STATUS, $order->getStoreId()
        );

        if ($order->getState() == Magento_Sales_Model_Order::STATE_HOLDED) {
            $status = $linkStatuses['pending'];
        } elseif ($order->isCanceled()
                  || $order->getState() == Magento_Sales_Model_Order::STATE_CLOSED
                  || $order->getState() == Magento_Sales_Model_Order::STATE_COMPLETE
        ) {
            $expiredStatuses = array(
                Magento_Sales_Model_Order_Item::STATUS_CANCELED,
                Magento_Sales_Model_Order_Item::STATUS_REFUNDED,
            );
            foreach ($order->getAllItems() as $item) {
                if ($item->getProductType() == Magento_Downloadable_Model_Product_Type::TYPE_DOWNLOADABLE
                    || $item->getRealProductType() == Magento_Downloadable_Model_Product_Type::TYPE_DOWNLOADABLE
                ) {
                    if (in_array($item->getStatusId(), $expiredStatuses)) {
                        $downloadableItemsStatuses[$item->getId()] = $linkStatuses['expired'];
                    } else {
                        $downloadableItemsStatuses[$item->getId()] = $linkStatuses['avail'];
                    }
                }
            }
        } elseif ($order->getState() == Magento_Sales_Model_Order::STATE_PENDING_PAYMENT) {
            $status = $linkStatuses['payment_pending'];
        } elseif ($order->getState() == Magento_Sales_Model_Order::STATE_PAYMENT_REVIEW) {
            $status = $linkStatuses['payment_review'];
        } else {
            $availableStatuses = array($orderItemStatusToEnable, Magento_Sales_Model_Order_Item::STATUS_INVOICED);
            foreach ($order->getAllItems() as $item) {
                if ($item->getProductType() == Magento_Downloadable_Model_Product_Type::TYPE_DOWNLOADABLE
                    || $item->getRealProductType() == Magento_Downloadable_Model_Product_Type::TYPE_DOWNLOADABLE
                ) {
                    if ($item->getStatusId() == Magento_Sales_Model_Order_Item::STATUS_BACKORDERED &&
                        $orderItemStatusToEnable == Magento_Sales_Model_Order_Item::STATUS_PENDING &&
                        !in_array(Magento_Sales_Model_Order_Item::STATUS_BACKORDERED, $availableStatuses, true) ) {
                        $availableStatuses[] = Magento_Sales_Model_Order_Item::STATUS_BACKORDERED;
                    }

                    if (in_array($item->getStatusId(), $availableStatuses)) {
                        $downloadableItemsStatuses[$item->getId()] = $linkStatuses['avail'];
                    }
                }
            }
        }
        if (!$downloadableItemsStatuses && $status) {
            foreach ($order->getAllItems() as $item) {
                if ($item->getProductType() == Magento_Downloadable_Model_Product_Type::TYPE_DOWNLOADABLE
                    || $item->getRealProductType() == Magento_Downloadable_Model_Product_Type::TYPE_DOWNLOADABLE
                ) {
                    $downloadableItemsStatuses[$item->getId()] = $status;
                }
            }
        }

        if ($downloadableItemsStatuses) {
            $linkPurchased = $this->_createItemsCollection()->addFieldToFilter(
                'order_item_id',
                array('in' => array_keys($downloadableItemsStatuses))
            );
            foreach ($linkPurchased as $link) {
                if ($link->getStatus() != $linkStatuses['expired']
                    && !empty($downloadableItemsStatuses[$link->getOrderItemId()])
                ) {
                    $link->setStatus($downloadableItemsStatuses[$link->getOrderItemId()])
                    ->save();
                }
            }
        }
        return $this;
    }

    /**
     * Check is allowed guest checkout if quote contain downloadable product(s)
     *
     * @param Magento_Event_Observer $observer
     * @return Magento_Downloadable_Model_Observer
     */
    public function isAllowedGuestCheckout(Magento_Event_Observer $observer)
    {
        $quote  = $observer->getEvent()->getQuote();
        /* @var $quote Magento_Sales_Model_Quote */
        $store  = $observer->getEvent()->getStore();
        $result = $observer->getEvent()->getResult();

        $isContain = false;

        foreach ($quote->getAllItems() as $item) {
            if (($product = $item->getProduct()) &&
            $product->getTypeId() == Magento_Downloadable_Model_Product_Type::TYPE_DOWNLOADABLE) {
                $isContain = true;
            }
        }

        if ($isContain && $this->_coreStoreConfig->getConfigFlag(self::XML_PATH_DISABLE_GUEST_CHECKOUT, $store)) {
            $result->setIsAllowed(false);
        }

        return $this;
    }

    /**
     * Initialize product options renderer with downloadable specific params
     *
     * @param Magento_Event_Observer $observer
     * @return Magento_Downloadable_Model_Observer
     */
    public function initOptionRenderer(Magento_Event_Observer $observer)
    {
        $block = $observer->getBlock();
        $block->addOptionsRenderCfg('downloadable', 'Magento_Downloadable_Helper_Catalog_Product_Configuration');
        return $this;
    }

    /**
     * Duplicating downloadable product data
     *
     * @param Magento_Event_Observer $observer
     * @return Magento_Downloadable_Model_Observer
     */
    public function duplicateProduct($observer)
    {
        $currentProduct = $observer->getCurrentProduct();
        $newProduct = $observer->getNewProduct();
        if ($currentProduct->getTypeId() !== Magento_Downloadable_Model_Product_Type::TYPE_DOWNLOADABLE) {
            //do nothing if not downloadable
            return $this;
        }
        $downloadableData = array();
        $type = $currentProduct->getTypeInstance();
        foreach ($type->getLinks($currentProduct) as $link) {
            $linkData = $link->getData();
            $downloadableData['link'][] = array(
                'is_delete'           => false,
                'link_id'             => null,
                'title'               => $linkData['title'],
                'is_shareable'        => $linkData['is_shareable'],
                'sample'              => array(
                    'type'       => $linkData['sample_type'],
                    'url'        => $linkData['sample_url'],
                    'file'       => $this->_helper->jsonEncode(array(array(
                        'file'   => $linkData['sample_file'],
                        'name'   => $linkData['sample_file'],
                        'size'   => 0,
                        'status' => null,
                    )))
                ),
                'file'       => $this->_helper->jsonEncode(array(array(
                    'file'   => $linkData['link_file'],
                    'name'   => $linkData['link_file'],
                    'size'   => 0,
                    'status' => null,
                ))),
                'type'                => $linkData['link_type'],
                'link_url'            => $linkData['link_url'],
                'sort_order'          => $linkData['sort_order'],
                'number_of_downloads' => $linkData['number_of_downloads'],
                'price'               => $linkData['price'],
            );
        }
        foreach ($type->getSamples($currentProduct) as $sample) {
            $sampleData = $sample->getData();
            $downloadableData['sample'][] = array(
                'is_delete'  => false,
                'sample_id'  => null,
                'title'      => $sampleData['title'],
                'type'       => $sampleData['sample_type'],
                'file'       => $this->_helper->jsonEncode(array(array(
                    'file'   => $sampleData['sample_file'],
                    'name'   => $sampleData['sample_file'],
                    'size'   => 0,
                    'status' => null,
                ))),
                'sample_url' => $sampleData['sample_url'],
                'sort_order' => $sampleData['sort_order'],
            );
        }
        $newProduct->setDownloadableData($downloadableData);
        return $this;
    }

    /**
     * @return Magento_Downloadable_Model_Link_Purchased
     */
    protected function _createPurchasedModel()
    {
        return $this->_purchasedFactory->create();
    }

    /**
     * @return Magento_Catalog_Model_Product
     */
    protected function _createProductModel()
    {
        return $this->_productFactory->create();
    }

    /**
     * @return Magento_Downloadable_Model_Link_Purchased_Item
     */
    protected function _createPurchasedItemModel()
    {
        return $this->_itemFactory->create();
    }

    /**
     * @return Magento_Downloadable_Model_Resource_Link_Purchased_Item_Collection
     */
    protected function _createItemsCollection()
    {
        return $this->_itemsFactory->create();
    }
}
