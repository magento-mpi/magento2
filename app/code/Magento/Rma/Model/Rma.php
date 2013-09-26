<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Rma
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * RMA model
 */
class Magento_Rma_Model_Rma extends Magento_Core_Model_Abstract
{
    /**
     * XML configuration paths
     */
    const XML_PATH_SECTION_RMA       = 'sales/magento_rma/';
    const XML_PATH_ENABLED           = 'sales/magento_rma/enabled';
    const XML_PATH_USE_STORE_ADDRESS = 'sales/magento_rma/use_store_address';
     /**
     * Rma Instance
     *
     * @var Magento_Rma_Model_Rma
     */
    protected $_rma;

    /**
     * Rma items collection
     *
     * @var null
     */
    protected $_items;

    /**
     * Rma order object
     *
     * @var Magento_Sales_Model_Order
     */
    protected $_order;

    /**
     * @var Magento_Rma_Model_Resource_Shipping_Collection
     */
    protected $_trackingNumbers;

    /**
     * @var Magento_Rma_Model_Shipping
     */
    protected $_shippingLabel;

    /**
     * @var Magento_Rma_Helper_Data
     */
    protected $_rmaData;

    /**
     * @var Magento_Core_Helper_Data
     */
    protected $_coreData;

    /**
     * @var Magento_Core_Model_Session
     */
    protected $_session;

    /**
     * @var Magento_Core_Model_Email_TemplateFactory
     */
    protected $_templateFactory;

    /**
     * @var Magento_Core_Model_Translate_Proxy
     */
    protected $_translate;

    /**
     * @var Magento_Core_Model_StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var Magento_Eav_Model_Config
     */
    protected $_eavConfig;

    /**
     * @var Magento_Rma_Model_Config
     */
    protected $_rmaConfig;

    /**
     * @var Magento_Rma_Model_ItemFactory
     */
    protected $_rmaItemFactory;

    /**
     * @var Magento_Rma_Model_Item_Attribute_Source_StatusFactory
     */
    protected $_attrSourceFactory;

    /**
     * @var Magento_Rma_Model_GridFactory
     */
    protected $_rmaGridFactory;

    /**
     * @var Magento_Rma_Model_Rma_Status_HistoryFactory
     */
    protected $_historyFactory;

    /**
     * @var Magento_Rma_Model_Rma_Source_StatusFactory
     */
    protected $_statusFactory;

    /**
     * @var Magento_Rma_Model_Resource_ItemFactory
     */
    protected $_itemFactory;

    /**
     * @var Magento_Rma_Model_Resource_Item_CollectionFactory
     */
    protected $_itemsFactory;

    /**
     * @var Magento_Rma_Model_Resource_Shipping_CollectionFactory
     */
    protected $_rmaShippingFactory;

    /**
     * @var Magento_Sales_Model_QuoteFactory
     */
    protected $_quoteFactory;

    /**
     * @var Magento_Sales_Model_Quote_Address_RateFactory
     */
    protected $_quoteRateFactory;

    /**
     * @var Magento_Sales_Model_Quote_ItemFactory
     */
    protected $_quoteItemFactory;

    /**
     * @var Magento_Sales_Model_OrderFactory
     */
    protected $_orderFactory;

    /**
     * @var Magento_Sales_Model_Resource_Order_Item_CollectionFactory
     */
    protected $_ordersFactory;

    /**
     * @var Magento_Shipping_Model_Rate_RequestFactory
     */
    protected $_rateRequestFactory;

    /**
     * @var Magento_Shipping_Model_ShippingFactory
     */
    protected $_shippingFactory;

    /**
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Rma_Helper_Data $rmaData
     * @param Magento_Core_Model_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_Core_Model_Session $session
     * @param Magento_Core_Model_Email_TemplateFactory $templateFactory
     * @param Magento_Core_Model_Translate_Proxy $translate
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param Magento_Eav_Model_Config $eavConfig
     * @param Magento_Rma_Model_Config $rmaConfig
     * @param Magento_Rma_Model_ItemFactory $rmaItemFactory
     * @param Magento_Rma_Model_Item_Attribute_Source_StatusFactory $attrSourceFactory
     * @param Magento_Rma_Model_GridFactory $rmaGridFactory
     * @param Magento_Rma_Model_Rma_Status_HistoryFactory $historyFactory
     * @param Magento_Rma_Model_Rma_Source_StatusFactory $statusFactory
     * @param Magento_Rma_Model_Resource_ItemFactory $itemFactory
     * @param Magento_Rma_Model_Resource_Item_CollectionFactory $itemsFactory
     * @param Magento_Rma_Model_Resource_Shipping_CollectionFactory $rmaShippingFactory
     * @param Magento_Sales_Model_QuoteFactory $quoteFactory
     * @param Magento_Sales_Model_Quote_Address_RateFactory $quoteRateFactory
     * @param Magento_Sales_Model_Quote_ItemFactory $quoteItemFactory
     * @param Magento_Sales_Model_OrderFactory $orderFactory
     * @param Magento_Sales_Model_Resource_Order_Item_CollectionFactory $ordersFactory
     * @param Magento_Shipping_Model_Rate_RequestFactory $rateRequestFactory
     * @param Magento_Shipping_Model_ShippingFactory $shippingFactory
     * @param Magento_Rma_Model_Resource_Rma $resource
     * @param Magento_Data_Collection_Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        Magento_Core_Helper_Data $coreData,
        Magento_Rma_Helper_Data $rmaData,
        Magento_Core_Model_Context $context,
        Magento_Core_Model_Registry $registry,
        Magento_Core_Model_Session $session,
        Magento_Core_Model_Email_TemplateFactory $templateFactory,
        Magento_Core_Model_Translate_Proxy $translate,
        Magento_Core_Model_StoreManagerInterface $storeManager,
        Magento_Eav_Model_Config $eavConfig,
        Magento_Rma_Model_Config $rmaConfig,
        Magento_Rma_Model_ItemFactory $rmaItemFactory,
        Magento_Rma_Model_Item_Attribute_Source_StatusFactory $attrSourceFactory,
        Magento_Rma_Model_GridFactory $rmaGridFactory,
        Magento_Rma_Model_Rma_Status_HistoryFactory $historyFactory,
        Magento_Rma_Model_Rma_Source_StatusFactory $statusFactory,
        Magento_Rma_Model_Resource_ItemFactory $itemFactory,
        Magento_Rma_Model_Resource_Item_CollectionFactory $itemsFactory,
        Magento_Rma_Model_Resource_Shipping_CollectionFactory $rmaShippingFactory,
        Magento_Sales_Model_QuoteFactory $quoteFactory,
        Magento_Sales_Model_Quote_Address_RateFactory $quoteRateFactory,
        Magento_Sales_Model_Quote_ItemFactory $quoteItemFactory,
        Magento_Sales_Model_OrderFactory $orderFactory,
        Magento_Sales_Model_Resource_Order_Item_CollectionFactory $ordersFactory,
        Magento_Shipping_Model_Rate_RequestFactory $rateRequestFactory,
        Magento_Shipping_Model_ShippingFactory $shippingFactory,
        Magento_Rma_Model_Resource_Rma $resource,
        Magento_Data_Collection_Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_coreData = $coreData;
        $this->_rmaData = $rmaData;
        $this->_session = $session;
        $this->_templateFactory = $templateFactory;
        $this->_translate = $translate;
        $this->_storeManager = $storeManager;
        $this->_eavConfig = $eavConfig;
        $this->_rmaConfig = $rmaConfig;
        $this->_rmaItemFactory = $rmaItemFactory;
        $this->_attrSourceFactory = $attrSourceFactory;
        $this->_rmaGridFactory = $rmaGridFactory;
        $this->_historyFactory = $historyFactory;
        $this->_statusFactory = $statusFactory;
        $this->_itemFactory = $itemFactory;
        $this->_itemsFactory = $itemsFactory;
        $this->_rmaShippingFactory = $rmaShippingFactory;
        $this->_quoteFactory = $quoteFactory;
        $this->_quoteRateFactory = $quoteRateFactory;
        $this->_quoteItemFactory = $quoteItemFactory;
        $this->_orderFactory = $orderFactory;
        $this->_ordersFactory = $ordersFactory;
        $this->_rateRequestFactory = $rateRequestFactory;
        $this->_shippingFactory = $shippingFactory;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Init resource model
     */
    protected function _construct()
    {
        $this->_init('Magento_Rma_Model_Resource_Rma');
        parent::_construct();
    }

    /**
     * Processing object before save data
     *
     * @return Magento_Core_Model_Abstract
     */
    protected function _beforeSave()
    {
        parent::_beforeSave();

        if (!$this->getIncrementId()) {
            $incrementId = $this->_eavConfig->getEntityType('rma_item')->fetchNewIncrementId($this->getStoreId());
            $this->setIncrementId($incrementId);
        }
        if (!$this->getIsUpdate()) {
            $this->setData('protect_code', substr(md5(uniqid(mt_rand(), true) . ':' . microtime(true)), 5, 6));
        }
        return $this;
    }

    /**
     * Save related items
     *
     * @return Magento_Rma_Model_Rma
     */
    protected function _afterSave()
    {
        parent::_afterSave();

        /** @var $gridModel Magento_Rma_Model_Grid */
        $gridModel = $this->_rmaGridFactory->create();
        $gridModel->addData($this->getData());
        $gridModel->save();

        /** @var $statusHistory  Magento_Rma_Model_Rma_Status_History */
        $statusHistory = $this->_historyFactory->create();
        $statusHistory->setRma($this);
        $statusHistory->saveSystemComment();

        $itemsCollection = $this->getItemsCollection();
        if (is_array($itemsCollection)) {
            foreach ($itemsCollection as $item) {
                $item->save();
            }
        }
        return $this;
    }

    /**
     * Return Entity Type ID
     *
     * @return int
     */
    public function getEntityTypeId()
    {
        $entityTypeId = $this->getData('entity_type_id');
        if (!$entityTypeId) {
            $entityTypeId = $this->getEntityType()->getId();
            $this->setData('entity_type_id', $entityTypeId);
        }
        return $entityTypeId;
    }

    /**
     * Get available statuses for RMAs
     *
     * @return array
     */
    public function getAllStatuses()
    {
        /** @var $sourceStatus Magento_Rma_Model_Rma_Source_Status */
        $sourceStatus = $this->_statusFactory->create();
        return $sourceStatus->getAllOptionsForGrid();
    }

    /**
     * Get RMA's status label
     *
     * @return string
     */
    public function getStatusLabel()
    {
        if (is_null(parent::getStatusLabel())) {
            /** @var $sourceStatus Magento_Rma_Model_Rma_Source_Status */
            $sourceStatus = $this->_statusFactory->create();
            $this->setStatusLabel($sourceStatus->getItemLabel($this->getStatus()));
        }
        return parent::getStatusLabel();
    }

    /**
     * Gets Rma items collection
     *
     * @return Magento_Rma_Model_Resource_Item_Collection
     */
    public function getItemsCollection()
    {
        if ($this->getId() && !empty($this->_items)) {
            foreach ($this->_items as $item) {
                if (!$item->getRmaEntityId()) {
                    $item->setRmaEntityId($this->getId());
                }
            }
        }
        return $this->_items;
    }

    /**
     * Get rma order object
     *
     * @return Magento_Sales_Model_Order
     */
    public function getOrder()
    {
        if (!$this->_order) {
            $this->_order = $this->_orderFactory->create()->load($this->getOrderId());
        }
        return $this->_order;
    }

    /**
     * Retrieves rma close availability
     *
     * @return bool
     */
    public function canClose()
    {
        $status = $this->getStatus();
        if ($status === Magento_Rma_Model_Rma_Source_Status::STATE_CLOSED
            || $status === Magento_Rma_Model_Rma_Source_Status::STATE_PROCESSED_CLOSED) {
            return false;
        }

        return true;
    }

    /**
     * Close rma
     *
     * @return Magento_Rma_Model_Rma
     */
    public function close()
    {
        if ($this->canClose()) {
            $this->setStatus(Magento_Rma_Model_Rma_Source_Status::STATE_CLOSED);
        }
        return $this;
    }

    /**
     * Save Rma
     *
     * @param array $data
     * @return bool|Magento_Rma_Model_Rma
     */
    public function saveRma($data)
    {
        // TODO: move errors adding to controller
        $errors = 0;

        if ($this->getCustomerCustomEmail()) {
            $validateEmail = $this->_validateEmail($this->getCustomerCustomEmail());
            if (is_array($validateEmail)) {
                foreach ($validateEmail as $error) {
                    $this->_session->addError($error);
                }
                $this->_session->setRmaFormData($data);
                $errors = 1;
            }
        }

        $itemModels = $this->_createItemsCollection($data);
        if (!$itemModels || $errors) {
            return false;
        }

        $this->save();
        $this->_rma = $this;
        return $this;
    }

    /**
     * Sending email with RMA data
     *
     * @return Magento_Rma_Model_Rma
     */
    public function sendNewRmaEmail()
    {
        return $this->_sendRmaEmailWithItems($this->_rmaConfig->getRootRmaEmail());
    }

    /**
     * Sending authorizing email with RMA data
     *
     * @return Magento_Rma_Model_Rma
     */
    public function sendAuthorizeEmail()
    {
        if (!$this->getIsSendAuthEmail()) {
            return $this;
        }
        return $this->_sendRmaEmailWithItems($this->_rmaConfig->getRootAuthEmail());
    }

    /**
     * Sending authorizing email with RMA data
     *
     * @param string $rootConfig
     * @return Magento_Rma_Model_Rma
     */
    public function _sendRmaEmailWithItems($rootConfig)
    {
        $this->_rmaConfig->init($rootConfig, $this->getStoreId());
        if (!$this->_rmaConfig->isEnabled()) {
            return $this;
        }

        $this->_translate->setTranslateInline(false);
        $mailTemplate = $this->_templateFactory->create();
        /* @var $mailTemplate Magento_Core_Model_Email_Template */
        $copyTo = $this->_rmaConfig->getCopyTo();
        $copyMethod = $this->_rmaConfig->getCopyMethod();
        if ($copyTo && $copyMethod == 'bcc') {
            foreach ($copyTo as $email) {
                $mailTemplate->addBcc($email);
            }
        }

        if ($this->getOrder()->getCustomerIsGuest()) {
            $template = $this->_rmaConfig->getGuestTemplate();
            $customerName = $this->getOrder()->getBillingAddress()->getName();
        } else {
            $template = $this->_rmaConfig->getTemplate();
            $customerName = $this->getCustomerName();
        }

        $sendTo = array(
            array(
                'email' => $this->getOrder()->getCustomerEmail(),
                'name'  => $customerName
            )
        );
        if ($this->getCustomerCustomEmail()) {
            $sendTo[] = array(
                            'email' => $this->getCustomerCustomEmail(),
                            'name'  => $customerName
                        );
        }
        if ($copyTo && $copyMethod == 'copy') {
            foreach ($copyTo as $email) {
                $sendTo[] = array(
                    'email' => $email,
                    'name'  => null
                );
            }
        }

        $returnAddress = $this->_rmaData->getReturnAddress(
            'html', array(), $this->getStoreId()
        );

        foreach ($sendTo as $recipient) {
            $mailTemplate->setDesignConfig(array(
                'area' => Magento_Core_Model_App_Area::AREA_FRONTEND,
                'store' => $this->getStoreId()
            ))
                ->sendTransactional(
                    $template,
                    $this->_rmaConfig->getIdentity(),
                    $recipient['email'],
                    $recipient['name'],
                    array(
                        'rma'               => $this,
                        'order'             => $this->getOrder(),
                        'return_address'    => $returnAddress,
                        //We cannot use $this->_items as items collection, because some items might not be loaded now
                        'item_collection'   => $this->getItemsForDisplay(),
                    )
                );
        }
        $this->setEmailSent(true);
        $this->_translate->setTranslateInline(true);

        return $this;
    }

    /**
     * Prepares Item's data
     *
     * @param  $item
     * @return array
     */
    protected function _preparePost($item)
    {
        $errors         = false;
        $preparePost    = array();
        $qtyKeys        = array('qty_authorized', 'qty_returned', 'qty_approved');

        ksort($item);
        foreach ($item as $key=>$value) {
            if ($key == 'order_item_id') {
                $preparePost['order_item_id'] = (int)$value;
            } elseif ($key == 'qty_requested') {
                $preparePost['qty_requested'] = is_numeric($value) ? $value : 0;
            } elseif (in_array($key, $qtyKeys)) {
                if (is_numeric($value)) {
                    $preparePost[$key] = (float)$value;
                } else {
                    $preparePost[$key] = '';
                }
            } elseif ($key == 'resolution') {
                $preparePost['resolution'] = (int)$value;
            } elseif ($key == 'condition') {
                $preparePost['condition'] = (int)$value;
            } elseif ($key == 'reason') {
                $preparePost['reason'] = (int)$value;
            } elseif ($key == 'reason_other' && !empty($value)) {
                $preparePost['reason_other'] = $value;
            } else {
                $preparePost[$key] = $value;
            }
        }

        $order      = $this->getOrder();
        $realItem   = $order->getItemById($preparePost['order_item_id']);

        $stat = Magento_Rma_Model_Item_Attribute_Source_Status::STATE_PENDING;
        if (!empty($preparePost['status'])) {
            /** @var $status Magento_Rma_Model_Item_Attribute_Source_Status */
            $status = $this->_attrSourceFactory->create();
            if ($status->checkStatus($preparePost['status'])) {
                $stat = $preparePost['status'];
            }
        }

        $preparePost['status']             = $stat;

        $preparePost['product_name']       = $realItem->getName();
        $preparePost['product_sku']        = $realItem->getSku();
        $preparePost['product_admin_name'] = $this->_rmaData->getAdminProductName($realItem);
        $preparePost['product_admin_sku']  = $this->_rmaData->getAdminProductSku($realItem);
        $preparePost['product_options']    = serialize($realItem->getProductOptions());
        $preparePost['is_qty_decimal']     = $realItem->getIsQtyDecimal();

        if ($preparePost['is_qty_decimal']) {
            $preparePost['qty_requested']  = (float)$preparePost['qty_requested'];
        } else {
            $preparePost['qty_requested']  = (int)$preparePost['qty_requested'];

            foreach ($qtyKeys as $key) {
                if (!empty($preparePost[$key])) {
                    $preparePost[$key] = (int)$preparePost[$key];
                }
            }
        }

        if (isset($preparePost['qty_requested'])
            && $preparePost['qty_requested'] <= 0
        ) {
            $errors = true;
        }

        foreach ($qtyKeys as $key) {
            if (isset($preparePost[$key])
                && !is_string($preparePost[$key])
                && $preparePost[$key] <= 0
            ) {
                $errors = true;
            }
        }

        if ($errors) {
            $this->_session->addError(
                __('There is an error in quantities for item %1.', $preparePost['product_name'])
            );
        }

        return $preparePost;
    }

    /**
     * Checks Items Quantity in Return
     *
     * @param  Magento_Rma_Model_Item $itemModels
     * @param  $orderId
     * @return array|bool
     */
    protected function _checkPost($itemModels, $orderId)
    {
        $errors     = array();
        $errorKeys  = array();
        if (!$this->getIsUpdate()) {
            $availableItems = $this->_rmaData->getOrderItems($orderId);
        } else {
            /** @var $itemResource Magento_Rma_Model_Resource_Item */
            $itemResource = $this->_itemFactory->create();
            $availableItems = $itemResource->getOrderItemsCollection($orderId);
        }

        $itemsArray = array();
        foreach ($itemModels as $item) {
            if (!isset($itemsArray[$item->getOrderItemId()])) {
                $itemsArray[$item->getOrderItemId()] = $item->getQtyRequested();
            } else {
                $itemsArray[$item->getOrderItemId()] += $item->getQtyRequested();
            }

            if ($this->getIsUpdate()) {
                $validation = array();
                foreach (array('qty_requested', 'qty_authorized', 'qty_returned', 'qty_approved') as $tempQty) {
                    if (is_null($item->getData($tempQty))) {
                        if (!is_null($item->getOrigData($tempQty))) {
                            $validation[$tempQty] = (float)$item->getOrigData($tempQty);
                        }
                    } else {
                        $validation[$tempQty] = (float)$item->getData($tempQty);
                    }
                }
                $validation['dummy'] = -1;
                $previousValue = null;
                $escapedProductName = $this->_rmaData->escapeHtml($item->getProductName());
                foreach ($validation as $key => $value) {
                    if (isset($previousValue) && $value > $previousValue) {
                        $errors[] = __('There is an error in quantities for item %1.', $escapedProductName);
                        $errorKeys[$item->getId()] = $key;
                        $errorKeys['tabs'] = 'items_section';
                        break;
                    }
                    $previousValue = $value;
                }

                //if we change item status i.e. to authorized, then qty_authorized must be non-empty and so on.
                $qtyToStatus = array(
                    'qty_authorized' => array(
                            'name' => __('Authorized Qty'),
                            'status' => Magento_Rma_Model_Rma_Source_Status::STATE_AUTHORIZED
                        ),
                    'qty_returned' => array(
                            'name' => __('Returned Qty'),
                            'status' => Magento_Rma_Model_Rma_Source_Status::STATE_RECEIVED
                        ),
                    'qty_approved' => array(
                            'name' => __('Approved Qty'),
                            'status' => Magento_Rma_Model_Rma_Source_Status::STATE_APPROVED
                        ),

                );
                foreach ($qtyToStatus as $qtyKey => $qtyValue) {
                    if ($item->getStatus() === $qtyValue['status']
                        && $item->getOrigData('status') !== $qtyValue['status']
                        && !$item->getData($qtyKey)
                    ) {
                        $errors[] = __('%1 for item %2 cannot be empty.', $qtyValue['name'], $escapedProductName);
                        $errorKeys[$item->getId()] = $qtyKey;
                        $errorKeys['tabs'] = 'items_section';
                    }
                }
            }
        }
        ksort($itemsArray);

        $availableItemsArray = array();
        foreach ($availableItems as $item) {
            $availableItemsArray[$item->getId()] = array(
                'name'  => $item->getName(),
                'qty'   => $item->getAvailableQty()
            );
        }

        foreach ($itemsArray as $key=>$qty) {
            $escapedProductName = $this->_rmaData->escapeHtml(
                $availableItemsArray[$key]['name']
            );
            if (!array_key_exists($key, $availableItemsArray)) {
                $errors[] = __('You cannot return %1.', $escapedProductName);
            }
            if (isset($availableItemsArray[$key]) && $availableItemsArray[$key]['qty'] < $qty) {
                $errors[] = __('Quantity of %1 is greater than you can return.', $escapedProductName);
                $errorKeys[$key] = 'qty_requested';
                $errorKeys['tabs'] = 'items_section';
            }
        }

        if (!empty($errors)) {
            return array($errors, $errorKeys);
        }
        return true;
    }

    /**
     * Creates rma items collection by passed data
     *
     * @param array $data
     * @return array
     */
    protected function _createItemsCollection($data)
    {
        if (!is_array($data)) {
            $data = (array) $data;
        }
        $order      = $this->getOrder();
        $itemModels = array();
        $errors     = array();
        $errorKeys  = array();

        foreach ($data['items'] as $key=>$item) {
            if (isset($item['items'])) {
                $itemModel  = $firstModel   = false;
                $files      = $f            =array();
                foreach ($item['items'] as $id=>$qty) {
                    if ($itemModel) {
                        $firstModel = $itemModel;
                    }
                    /** @var $itemModel Magento_Rma_Model_Item */
                    $itemModel                  = $this->_rmaItemFactory->create();
                    $subItem                    = $item;
                    unset($subItem['items']);
                    $subItem['order_item_id']   = $id;
                    $subItem['qty_requested']   = $qty;

                    $itemPost                   = $this->_preparePost($subItem);

                    $f = $itemModel->setData($itemPost)
                        ->prepareAttributes($itemPost, $key);

                    /* Copy image(s) to another bundle items */
                    if (!empty($f)) {
                        $files = $f;
                    }
                    if (!empty($files) && $firstModel) {
                        foreach ($files as $code) {
                            $itemModel->setData($code, $firstModel->getData($code));
                        }
                    }
                    $errors = array_merge($itemModel->getErrors(), $errors);

                    $itemModels[] = $itemModel;
                }
            } else {
                /** @var $itemModel Magento_Rma_Model_Item */
                $itemModel = $this->_rmaItemFactory->create();
                if (isset($item['entity_id']) && $item['entity_id']) {
                    $itemModel->load($item['entity_id']);
                    if ($itemModel->getId()) {
                        if (empty($item['reason'])) {
                            $item['reason'] = $itemModel->getReason();
                        }

                        if (empty($item['reason_other'])) {
                            $item['reason_other'] = $itemModel->getReasonOther() === null ? ''
                                : $itemModel->getReasonOther();
                        }

                        if (empty($item['condition'])) {
                            $item['condition'] = $itemModel->getCondition();
                        }

                        if (empty($item['qty_requested'])) {
                            $item['qty_requested'] = $itemModel->getQtyRequested();
                        }
                    }

                }

                $itemPost = $this->_preparePost($item);

                $itemModel->setData($itemPost)
                    ->prepareAttributes($itemPost, $key);
                $errors = array_merge($itemModel->getErrors(), $errors);
                if ($errors) {
                    $errorKeys['tabs'] = 'items_section';
                }

                $itemModels[] = $itemModel;

                if (($itemModel->getStatus() === Magento_Rma_Model_Item_Attribute_Source_Status::STATE_AUTHORIZED)
                    && ($itemModel->getOrigData('status') !== $itemModel->getStatus())) {
                    $this->setIsSendAuthEmail(1);
                }
            }
        }

        $result = $this->_checkPost($itemModels, $order->getId());

        if ($result !== true) {
            list($result, $errorKey) = $result;
            $errors     = array_merge($result, $errors);
            $errorKeys  = array_merge($errorKey, $errorKeys);
        }

        $eMessages  = $this->_session->getMessages()->getErrors();
        if (!empty($errors) || !empty($eMessages)) {
            $this->_session->setRmaFormData($data);
            if (!empty($errorKeys)) {
                $this->_session->setRmaErrorKeys($errorKeys);
            }
            if (!empty($errors)) {
                foreach ($errors as $message) {
                    $this->_session->addError($message);
                }
            }
            return false;
        }
        $this->_items = $itemModels;
        return $itemModels;
    }


    /**
     * Validate email
     *
     * @param string $value
     * @return string
     */
    protected function _validateEmail($value)
    {
        $label = $this->_rmaData->getContactEmailLabel();

        $validator = new Zend_Validate_EmailAddress();
        $validator->setMessage(
            __('You entered an invalid type: "%1".', $label),
            Zend_Validate_EmailAddress::INVALID
        );
        $validator->setMessage(
            __('You entered an invalid email address: "%1".', $label),
            Zend_Validate_EmailAddress::INVALID_FORMAT
        );
        $validator->setMessage(
            __('You entered an invalid hostname: "%1"', $label),
            Zend_Validate_EmailAddress::INVALID_HOSTNAME
        );
        $validator->setMessage(
            __('You entered an invalid hostname: "%1"', $label),
            Zend_Validate_EmailAddress::INVALID_MX_RECORD
        );
        $validator->setMessage(
            __('You entered an invalid hostname: "%1"', $label),
            Zend_Validate_EmailAddress::INVALID_MX_RECORD
        );
        $validator->setMessage(
            __('You entered an invalid email address: "%1".', $label),
            Zend_Validate_EmailAddress::DOT_ATOM
        );
        $validator->setMessage(
            __('You entered an invalid email address: "%1".', $label),
            Zend_Validate_EmailAddress::QUOTED_STRING
        );
        $validator->setMessage(
            __('You entered an invalid email address: "%1".', $label),
            Zend_Validate_EmailAddress::INVALID_LOCAL_PART
        );
        $validator->setMessage(
            __('"%1" is longer than allowed.', $label),
            Zend_Validate_EmailAddress::LENGTH_EXCEEDED
        );
        if (!$validator->isValid($value)) {
            return array_unique($validator->getMessages());
        }

        return true;
    }

    /**
     * Get formated RMA created date in store timezone
     *
     * @param   string $format date format type (short|medium|long|full)
     * @return  string
     */
    public function getCreatedAtFormated($format)
    {
        return $this->_coreData->formatDate($this->getCreatedAtStoreDate(), $format, true);
    }

    /**
     * Gets Shipping Methods
     *
     * @param bool $returnItems Flag if needs to return Items
     * @return array|bool
     */
    public function getShippingMethods($returnItems = false)
    {
        $found = false;
        $address = false;
        /** @var $itemResource Magento_Rma_Model_Resource_Item */
        $itemResource = $this->_itemFactory->create();
        $rmaItems = $itemResource->getAuthorizedItems($this->getId());

        if (!empty($rmaItems)) {
            /** @var $quoteItemsCollection Magento_Sales_Model_Resource_Order_Item_Collection */
            $quoteItemsCollection = $this->_ordersFactory->create();
            $quoteItemsCollection->addFieldToFilter('item_id', array('in' => array_keys($rmaItems)))->getData();

            $quoteItems = array();
            $subtotal   = $weight = $qty = $storeId = 0;
            foreach ($quoteItemsCollection as $item) {
                /** @var $itemModel Magento_Sales_Model_Quote_Item */
                $itemModel = $this->_quoteItemFactory->create();

                $item['qty']                    = $rmaItems[$item['item_id']]['qty'];
                $item['name']                   = $rmaItems[$item['item_id']]['product_name'];
                $item['row_total']              = $item['price'] * $item['qty'];
                $item['base_row_total']         = $item['base_price'] * $item['qty'];
                $item['row_total_with_discount']= 0;
                $item['row_weight']             = $item['weight'] * $item['qty'];
                $item['price_incl_tax']         = $item['price'];
                $item['base_price_incl_tax']    = $item['base_price'];
                $item['row_total_incl_tax']     = $item['row_total'];
                $item['base_row_total_incl_tax']= $item['base_row_total'];

                $quoteItems[] = $itemModel->setData($item);

                $subtotal   += $item['base_row_total'];
                $weight     += $item['row_weight'];
                $qty        += $item['qty'];

                if (!$storeId) {
                    $storeId = $item['store_id'];
                    /** @var $order Magento_Sales_Model_Order */
                    $order = $this->_orderFactory->create()->load($item['order_id']);
                    /** @var $address Magento_Sales_Model_Order_Address */
                    $address = $order->getShippingAddress();
                }
                /** @var $quote Magento_Sales_Model_Quote */
                $quote = $this->_quoteFactory->create();
                $quote->setStoreId($storeId);
                $itemModel->setQuote($quote);
            }

            if ($returnItems) {
                return $quoteItems;
            }

            $store = $this->_storeManager->getStore($storeId);
            $this->setStore($store);

            $found = $this->_requestShippingRates($quoteItems, $address, $store, $subtotal, $weight, $qty);
        }

        return $found;
    }

    /**
     * Returns Shipping Rates
     *
     * @param array $items
     * @param Magento_Sales_Model_Order_Address|bool $address Shop address
     * @param Magento_Core_Model_Store $store
     * @param int $subtotal
     * @param int $weight
     * @param int $qty
     *
     * @return array|bool
     */
    protected function _requestShippingRates($items, $address, $store, $subtotal, $weight, $qty)
    {
        /** @var Magento_Sales_Model_Quote_Address $shippingDestinationInfo */
        $shippingDestinationInfo = $this->_rmaData->getReturnAddressModel(
            $this->getStoreId()
        );

        /** @var $request Magento_Shipping_Model_Rate_Request */
        $request = $this->_rateRequestFactory->create();
        $request->setAllItems($items);
        $request->setDestCountryId($shippingDestinationInfo->getCountryId());
        $request->setDestRegionId($shippingDestinationInfo->getRegionId());
        $request->setDestRegionCode($shippingDestinationInfo->getRegionId());
        $request->setDestStreet($shippingDestinationInfo->getStreetFull());
        $request->setDestCity($shippingDestinationInfo->getCity());
        $request->setDestPostcode($shippingDestinationInfo->getPostcode());
        $request->setDestCompanyName($shippingDestinationInfo->getCompany());

        $request->setPackageValue($subtotal);
        $request->setPackageValueWithDiscount($subtotal);
        $request->setPackageWeight($weight);
        $request->setPackageQty($qty);

        //shop destination address data
        //different carriers use different variables. So we duplicate them
        $request
            ->setOrigCountryId($address->getCountryId())
            ->setOrigCountry($address->getCountryId())
            ->setOrigState($address->getRegionId())
            ->setOrigRegionCode($address->getRegionId())
            ->setOrigCity($address->getCity())
            ->setOrigPostcode($address->getPostcode())
            ->setOrigPostal($address->getPostcode())
            ->setOrigCompanyName($address->getCompany() ? $address->getCompany() : 'NA')
            ->setOrig(true);

        /**
         * Need for shipping methods that use insurance based on price of physical products
         */
        $request->setPackagePhysicalValue($subtotal);

        $request->setFreeMethodWeight(0);

        /**
         * Store and website identifiers need specify from quote
         */
        $request->setStoreId($store->getId());
        $request->setWebsiteId($store->getWebsiteId());
        /**
         * Currencies need to convert in free shipping
         */
        $request->setBaseCurrency($store->getBaseCurrency());
        $request->setPackageCurrency($store->getCurrentCurrency());

        /*
         * For international shipments we must set customs value larger than zero
         * This number is being taken from items' prices
         * But for the case when we try to return bundle items from fixed-price bundle,
         * we have no items' prices. We should add this customs value manually
         */
        if (($request->getOrigCountryId() !== $request->getDestCountryId()) && ($request->getPackageValue() < 1)) {
            $request->setPackageCustomsValue(1);
        }

        $request->setIsReturn(true);

        /** @var $shipping Magento_Shipping_Model_Shipping */
        $shipping = $this->_shippingFactory->create();
        $result = $shipping->setCarrierAvailabilityConfigField('active_rma')
            ->collectRates($request)
            ->getResult();

        $found = false;
        if ($result) {
            $shippingRates = $result->getAllRates();

            foreach ($shippingRates as $shippingRate) {
                if (
                    in_array(
                        $shippingRate->getCarrier(),
                        array_keys($this->_rmaData->getShippingCarriers())
                    )
                ) {
                    /** @var $addressRate Magento_Sales_Model_Quote_Address_Rate */
                    $addressRate = $this->_quoteRateFactory->create();
                    $found[] = $addressRate->importShippingRate($shippingRate);
                }
            }
        }
        return $found;
    }

    /**
     * Get collection of tracking on this RMA
     *
     * @return Magento_Rma_Model_Resource_Shipping_Collection
     */
    public function getTrackingNumbers()
    {
        if (is_null($this->_trackingNumbers)) {
            $this->_trackingNumbers = $this->_rmaShippingFactory->create();
            $this->_trackingNumbers->addFieldToFilter('rma_entity_id', $this->getEntityId());
            $this->_trackingNumbers->addFieldToFilter('is_admin', array(
                'neq' => Magento_Rma_Model_Shipping::IS_ADMIN_STATUS_ADMIN_LABEL
            ));
        }
        return $this->_trackingNumbers;
    }

    /**
     * Get shipping label RMA
     *
     * @return Magento_Rma_Model_Shipping
     */
    public function getShippingLabel()
    {
        if (is_null($this->_shippingLabel)) {
            /** @var $shippingCollection Magento_Rma_Model_Resource_Shipping_Collection */
            $shippingCollection = $this->_rmaShippingFactory->create();
            $this->_shippingLabel = $shippingCollection->addFieldToFilter('rma_entity_id', $this->getEntityId())
                ->addFieldToFilter('is_admin', Magento_Rma_Model_Shipping::IS_ADMIN_STATUS_ADMIN_LABEL)
                ->getFirstItem();
        }
        return $this->_shippingLabel;
    }

    /**
     * Defines whether RMA status and RMA Items statuses allow to create shipping label
     *
     * @return bool
     */
    public function isAvailableForPrintLabel()
    {
        return (bool)($this->_isRmaAvailableForPrintLabel() && $this->_isItemsAvailableForPrintLabel());
    }

    /**
     * Defines whether RMA status allow to create shipping label
     *
     * @return bool
     */
    protected function _isRmaAvailableForPrintLabel()
    {
        return ($this->getStatus() !== Magento_Rma_Model_Rma_Source_Status::STATE_CLOSED)
            && ($this->getStatus() !== Magento_Rma_Model_Rma_Source_Status::STATE_PROCESSED_CLOSED)
            && ($this->getStatus() !== Magento_Rma_Model_Rma_Source_Status::STATE_PENDING);
    }

    /**
     * Defines whether RMA items' statuses allow to create shipping label
     *
     * @return bool
     */
    protected function _isItemsAvailableForPrintLabel()
    {
        /** @var $collection Magento_Rma_Model_Resource_Item_Collection */
        $collection = $this->_itemsFactory->create();
        $collection->addFieldToFilter('rma_entity_id', $this->getEntityId());

        $return = false;
        foreach ($collection as $item) {
            if (!in_array($item->getStatus(),
                array(
                    Magento_Rma_Model_Item_Attribute_Source_Status::STATE_AUTHORIZED,
                    Magento_Rma_Model_Item_Attribute_Source_Status::STATE_DENIED,
                ), true)
            ) {
                return false;
            }
            if (($item->getStatus() === Magento_Rma_Model_Item_Attribute_Source_Status::STATE_AUTHORIZED)
                && is_numeric($item->getQtyAuthorized())
                && $item->getQtyAuthorized() > 0
            ) {
                $return = true;
            }
        }
        return $return;
    }

    /**
     * Get collection of RMA Items with common order rules to be displayed in different lists
     *
     * @param bool $withoutAttributes - sets whether add EAV attributes into select
     * @return Magento_Rma_Model_Resource_Item_Collection
     */
    public function getItemsForDisplay($withoutAttributes = false)
    {
        /** @var $collection Magento_Rma_Model_Resource_Item_Collection */
        $collection = $this->_itemsFactory->create();
        $collection->addFieldToFilter('rma_entity_id', $this->getEntityId())
            ->setOrder('order_item_id')
            ->setOrder('entity_id');

        if (!$withoutAttributes) {
            $collection->addAttributeToSelect('*');
        }
        return $collection;
    }

    /**
     * Get button disabled status
     *
     * @return bool
     */
    public function getButtonDisabledStatus()
    {
        /** @var $sourceStatus Magento_Rma_Model_Rma_Source_Status */
        $sourceStatus = $this->_statusFactory->create();
        return $sourceStatus->getButtonDisabledStatus($this->getStatus()) && $this->_isItemsNotInPendingStatus();
    }

    /**
     * Defines whether RMA items' not in pending status
     *
     * @return bool
     */
    public function _isItemsNotInPendingStatus()
    {
        /** @var $collection Magento_Rma_Model_Resource_Item_Collection */
        $collection = $this->_itemsFactory->create();
        $collection->addFieldToFilter('rma_entity_id', $this->getEntityId());

        foreach ($collection as $item) {
            if ($item->getStatus() == Magento_Rma_Model_Item_Attribute_Source_Status::STATE_PENDING) {
                return false;
            }
        }
        return true;
    }
}
