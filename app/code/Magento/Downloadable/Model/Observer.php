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
     * @param array $data
     */
    public function __construct(array $data = array())
    {
        $this->_helper = isset($data['helper']) ? $data['helper'] : Mage::helper('Magento_Core_Helper_Data');
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
        $purchasedLink = Mage::getModel('Magento_Downloadable_Model_Link_Purchased')
            ->load($orderItem->getId(), 'order_item_id');
        if ($purchasedLink->getId()) {
            return $this;
        }
        if (!$product) {
            $product = Mage::getModel('Magento_Catalog_Model_Product')
                ->setStoreId($orderItem->getOrder()->getStoreId())
                ->load($orderItem->getProductId());
        }
        if ($product->getTypeId() == Magento_Downloadable_Model_Product_Type::TYPE_DOWNLOADABLE) {
            $links = $product->getTypeInstance()->getLinks($product);
            if ($linkIds = $orderItem->getProductOptionByCode('links')) {
                $linkPurchased = Mage::getModel('Magento_Downloadable_Model_Link_Purchased');
                Mage::helper('Magento_Core_Helper_Data')->copyFieldsetToTarget(
                    'downloadable_sales_copy_order',
                    'to_downloadable',
                    $orderItem->getOrder(),
                    $linkPurchased
                );
                Mage::helper('Magento_Core_Helper_Data')->copyFieldsetToTarget(
                    'downloadable_sales_copy_order_item',
                    'to_downloadable',
                    $orderItem,
                    $linkPurchased
                );
                $linkSectionTitle = (
                    $product->getLinksTitle()?
                    $product->getLinksTitle():Mage::getStoreConfig(Magento_Downloadable_Model_Link::XML_PATH_LINKS_TITLE)
                );
                $linkPurchased->setLinkSectionTitle($linkSectionTitle)
                    ->save();
                foreach ($linkIds as $linkId) {
                    if (isset($links[$linkId])) {
                        $linkPurchasedItem = Mage::getModel('Magento_Downloadable_Model_Link_Purchased_Item')
                            ->setPurchasedId($linkPurchased->getId())
                            ->setOrderItemId($orderItem->getId());

                        Mage::helper('Magento_Core_Helper_Data')->copyFieldsetToTarget(
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
        $session = Mage::getSingleton('Magento_Checkout_Model_Session');
        if (!$session->getHasDownloadableProducts()) {
            $order = $observer->getEvent()->getOrder();
            foreach ($order->getAllItems() as $item) {
                /* @var $item Magento_Sales_Model_Order_Item */
                if ($item->getProductType() == Magento_Downloadable_Model_Product_Type::TYPE_DOWNLOADABLE
                || $item->getRealProductType() == Magento_Downloadable_Model_Product_Type::TYPE_DOWNLOADABLE
                || $item->getProductOptionByCode('is_downloadable'))
                {
                    $session->setHasDownloadableProducts(true);
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
        $orderItemStatusToEnable = Mage::getStoreConfig(
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
            $linkPurchased = Mage::getResourceModel('Magento_Downloadable_Model_Resource_Link_Purchased_Item_Collection')
            ->addFieldToFilter('order_item_id', array('in' => array_keys($downloadableItemsStatuses)));
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

        if ($isContain && Mage::getStoreConfigFlag(self::XML_PATH_DISABLE_GUEST_CHECKOUT, $store)) {
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
}
