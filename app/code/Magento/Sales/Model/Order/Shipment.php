<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Sales order shipment model
 *
 * @method Magento_Sales_Model_Resource_Order_Shipment _getResource()
 * @method Magento_Sales_Model_Resource_Order_Shipment getResource()
 * @method int getStoreId()
 * @method Magento_Sales_Model_Order_Shipment setStoreId(int $value)
 * @method float getTotalWeight()
 * @method Magento_Sales_Model_Order_Shipment setTotalWeight(float $value)
 * @method float getTotalQty()
 * @method Magento_Sales_Model_Order_Shipment setTotalQty(float $value)
 * @method int getEmailSent()
 * @method Magento_Sales_Model_Order_Shipment setEmailSent(int $value)
 * @method int getOrderId()
 * @method Magento_Sales_Model_Order_Shipment setOrderId(int $value)
 * @method int getCustomerId()
 * @method Magento_Sales_Model_Order_Shipment setCustomerId(int $value)
 * @method int getShippingAddressId()
 * @method Magento_Sales_Model_Order_Shipment setShippingAddressId(int $value)
 * @method int getBillingAddressId()
 * @method Magento_Sales_Model_Order_Shipment setBillingAddressId(int $value)
 * @method int getShipmentStatus()
 * @method Magento_Sales_Model_Order_Shipment setShipmentStatus(int $value)
 * @method string getIncrementId()
 * @method Magento_Sales_Model_Order_Shipment setIncrementId(string $value)
 * @method string getCreatedAt()
 * @method Magento_Sales_Model_Order_Shipment setCreatedAt(string $value)
 * @method string getUpdatedAt()
 * @method Magento_Sales_Model_Order_Shipment setUpdatedAt(string $value)
 */
class Magento_Sales_Model_Order_Shipment extends Magento_Sales_Model_Abstract
{
    const STATUS_NEW    = 1;

    const XML_PATH_EMAIL_TEMPLATE               = 'sales_email/shipment/template';
    const XML_PATH_EMAIL_GUEST_TEMPLATE         = 'sales_email/shipment/guest_template';
    const XML_PATH_EMAIL_IDENTITY               = 'sales_email/shipment/identity';
    const XML_PATH_EMAIL_COPY_TO                = 'sales_email/shipment/copy_to';
    const XML_PATH_EMAIL_COPY_METHOD            = 'sales_email/shipment/copy_method';
    const XML_PATH_EMAIL_ENABLED                = 'sales_email/shipment/enabled';

    const XML_PATH_UPDATE_EMAIL_TEMPLATE        = 'sales_email/shipment_comment/template';
    const XML_PATH_UPDATE_EMAIL_GUEST_TEMPLATE  = 'sales_email/shipment_comment/guest_template';
    const XML_PATH_UPDATE_EMAIL_IDENTITY        = 'sales_email/shipment_comment/identity';
    const XML_PATH_UPDATE_EMAIL_COPY_TO         = 'sales_email/shipment_comment/copy_to';
    const XML_PATH_UPDATE_EMAIL_COPY_METHOD     = 'sales_email/shipment_comment/copy_method';
    const XML_PATH_UPDATE_EMAIL_ENABLED         = 'sales_email/shipment_comment/enabled';

    const REPORT_DATE_TYPE_ORDER_CREATED        = 'order_created';
    const REPORT_DATE_TYPE_SHIPMENT_CREATED     = 'shipment_created';

    /**
     * Identifier for order history item
     */
    const HISTORY_ENTITY_NAME = 'shipment';

    protected $_items;
    protected $_tracks;
    protected $_order;
    protected $_comments;

    protected $_eventPrefix = 'sales_order_shipment';
    protected $_eventObject = 'shipment';

    /**
     * Sales data
     *
     * @var Magento_Sales_Helper_Data
     */
    protected $_salesData;

    /**
     * Payment data
     *
     * @var Magento_Payment_Helper_Data
     */
    protected $_paymentData;

    /**
     * Core store config
     *
     * @var Magento_Core_Model_Store_ConfigInterface
     */
    protected $_coreStoreConfig;

    /**
     * @var Magento_Sales_Model_OrderFactory
     */
    protected $_orderFactory;

    /**
     * @var Magento_Sales_Model_Resource_Order_Shipment_Item_CollectionFactory
     */
    protected $_shipmentItemCollFactory;

    /**
     * @var Magento_Sales_Model_Resource_Order_Shipment_Track_CollectionFactory
     */
    protected $_trackCollFactory;

    /**
     * @var Magento_Sales_Model_Order_Shipment_CommentFactory
     */
    protected $_commentFactory;

    /**
     * @var Magento_Sales_Model_Resource_Order_Shipment_Comment_CollectionFactory
     */
    protected $_commentCollFactory;

    /**
     * @var Magento_Core_Model_Email_Template_MailerFactory
     */
    protected $_templateMailerFactory;

    /**
     * @var Magento_Core_Model_Email_InfoFactory
     */
    protected $_emailInfoFactory;

    /**
     * @param Magento_Payment_Helper_Data $paymentData
     * @param Magento_Sales_Helper_Data $salesData
     * @param Magento_Core_Model_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_Core_Model_Store_ConfigInterface $coreStoreConfig
     * @param Magento_Core_Model_LocaleInterface $coreLocale
     * @param Magento_Sales_Model_OrderFactory $orderFactory
     * @param Magento_Sales_Model_Resource_Order_Shipment_Item_CollectionFactory $shipmentItemCollFactory
     * @param Magento_Sales_Model_Resource_Order_Shipment_Track_CollectionFactory $trackCollFactory
     * @param Magento_Sales_Model_Order_Shipment_CommentFactory $commentFactory
     * @param Magento_Sales_Model_Resource_Order_Shipment_Comment_CollectionFactory $commentCollFactory
     * @param Magento_Core_Model_Email_Template_MailerFactory $templateMailerFactory
     * @param Magento_Core_Model_Email_InfoFactory $emailInfoFactory
     * @param Magento_Core_Model_Resource_Abstract $resource
     * @param Magento_Data_Collection_Db $resourceCollection
     * @param array $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        Magento_Payment_Helper_Data $paymentData,
        Magento_Sales_Helper_Data $salesData,
        Magento_Core_Model_Context $context,
        Magento_Core_Model_Registry $registry,
        Magento_Core_Model_Store_ConfigInterface $coreStoreConfig,
        Magento_Core_Model_LocaleInterface $coreLocale,
        Magento_Sales_Model_OrderFactory $orderFactory,
        Magento_Sales_Model_Resource_Order_Shipment_Item_CollectionFactory $shipmentItemCollFactory,
        Magento_Sales_Model_Resource_Order_Shipment_Track_CollectionFactory $trackCollFactory,
        Magento_Sales_Model_Order_Shipment_CommentFactory $commentFactory,
        Magento_Sales_Model_Resource_Order_Shipment_Comment_CollectionFactory $commentCollFactory,
        Magento_Core_Model_Email_Template_MailerFactory $templateMailerFactory,
        Magento_Core_Model_Email_InfoFactory $emailInfoFactory,
        Magento_Core_Model_Resource_Abstract $resource = null,
        Magento_Data_Collection_Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_paymentData = $paymentData;
        $this->_salesData = $salesData;
        $this->_coreStoreConfig = $coreStoreConfig;
        $this->_orderFactory = $orderFactory;
        $this->_shipmentItemCollFactory = $shipmentItemCollFactory;
        $this->_trackCollFactory = $trackCollFactory;
        $this->_commentFactory = $commentFactory;
        $this->_commentCollFactory = $commentCollFactory;
        $this->_templateMailerFactory = $templateMailerFactory;
        $this->_emailInfoFactory = $emailInfoFactory;
        parent::__construct($context, $registry, $coreLocale, $resource, $resourceCollection, $data);
    }

    /**
     * Initialize shipment resource model
     */
    protected function _construct()
    {
        $this->_init('Magento_Sales_Model_Resource_Order_Shipment');
    }

    /**
     * Load shipment by increment id
     *
     * @param string $incrementId
     * @return Magento_Sales_Model_Order_Shipment
     */
    public function loadByIncrementId($incrementId)
    {
        $ids = $this->getCollection()
            ->addAttributeToFilter('increment_id', $incrementId)
            ->getAllIds();

        if (!empty($ids)) {
            reset($ids);
            $this->load(current($ids));
        }
        return $this;
    }


    /**
     * Declare order for shipment
     *
     * @param   Magento_Sales_Model_Order $order
     * @return  Magento_Sales_Model_Order_Shipment
     */
    public function setOrder(Magento_Sales_Model_Order $order)
    {
        $this->_order = $order;
        $this->setOrderId($order->getId())
            ->setStoreId($order->getStoreId());
        return $this;
    }


    /**
     * Retrieve hash code of current order
     *
     * @return string
     */
    public function getProtectCode()
    {
        return (string)$this->getOrder()->getProtectCode();
    }

    /**
     * Retrieve the order the shipment for created for
     *
     * @return Magento_Sales_Model_Order
     */
    public function getOrder()
    {
        if (!$this->_order instanceof Magento_Sales_Model_Order) {
            $this->_order = $this->_orderFactory->create()->load($this->getOrderId());
        }
        return $this->_order->setHistoryEntityName(self::HISTORY_ENTITY_NAME);
    }

    /**
     * Retrieve billing address
     *
     * @return Magento_Sales_Model_Order_Address
     */
    public function getBillingAddress()
    {
        return $this->getOrder()->getBillingAddress();
    }

    /**
     * Retrieve shipping address
     *
     * @return Magento_Sales_Model_Order_Address
     */
    public function getShippingAddress()
    {
        return $this->getOrder()->getShippingAddress();
    }

    /**
     * Register shipment
     *
     * Apply to order, order items etc.
     *
     * @return $this
     * @throws Magento_Core_Exception
     */
    public function register()
    {
        if ($this->getId()) {
            throw new Magento_Core_Exception(__('We cannot register an existing shipment'));
        }

        $totalQty = 0;
        foreach ($this->getAllItems() as $item) {
            if ($item->getQty() > 0) {
                $item->register();
                if (!$item->getOrderItem()->isDummy(true)) {
                    $totalQty += $item->getQty();
                }
            } else {
                $item->isDeleted(true);
            }
        }
        $this->setTotalQty($totalQty);

        return $this;
    }

    public function getItemsCollection()
    {
        if (empty($this->_items)) {
            $this->_items = $this->_shipmentItemCollFactory->create()->setShipmentFilter($this->getId());

            if ($this->getId()) {
                foreach ($this->_items as $item) {
                    $item->setShipment($this);
                }
            }
        }
        return $this->_items;
    }

    /**
     * @return array
     */
    public function getAllItems()
    {
        $items = array();
        foreach ($this->getItemsCollection() as $item) {
            if (!$item->isDeleted()) {
                $items[] =  $item;
            }
        }
        return $items;
    }

    /**
     * @param string|int $itemId
     * @return bool|Magento_Sales_Model_Order_Shipment_Item
     */
    public function getItemById($itemId)
    {
        foreach ($this->getItemsCollection() as $item) {
            if ($item->getId() == $itemId) {
                return $item;
            }
        }
        return false;
    }

    /**
     * @param Magento_Sales_Model_Order_Shipment_Item $item
     * @return $this
     */
    public function addItem(Magento_Sales_Model_Order_Shipment_Item $item)
    {
        $item->setShipment($this)
            ->setParentId($this->getId())
            ->setStoreId($this->getStoreId());
        if (!$item->getId()) {
            $this->getItemsCollection()->addItem($item);
        }
        return $this;
    }


    /**
     * Retrieve tracks collection.
     *
     * @return Magento_Sales_Model_Resource_Order_Shipment_Track_Collection
     */
    public function getTracksCollection()
    {
        if (empty($this->_tracks)) {
            $this->_tracks = $this->_trackCollFactory->create()->setShipmentFilter($this->getId());

            if ($this->getId()) {
                foreach ($this->_tracks as $track) {
                    $track->setShipment($this);
                }
            }
        }
        return $this->_tracks;
    }

    /**
     * @return array
     */
    public function getAllTracks()
    {
        $tracks = array();
        foreach ($this->getTracksCollection() as $track) {
            if (!$track->isDeleted()) {
                $tracks[] =  $track;
            }
        }
        return $tracks;
    }

    /**
     * @param string|int $trackId
     * @return bool|Magento_Sales_Model_Order_Shipment_Track
     */
    public function getTrackById($trackId)
    {
        foreach ($this->getTracksCollection() as $track) {
            if ($track->getId() == $trackId) {
                return $track;
            }
        }
        return false;
    }

    /**
     * @param Magento_Sales_Model_Order_Shipment_Track $track
     * @return $this
     */
    public function addTrack(Magento_Sales_Model_Order_Shipment_Track $track)
    {
        $track->setShipment($this)
            ->setParentId($this->getId())
            ->setOrderId($this->getOrderId())
            ->setStoreId($this->getStoreId());
        if (!$track->getId()) {
            $this->getTracksCollection()->addItem($track);
        }

        /**
         * Track saving is implemented in _afterSave()
         * This enforces Magento_Core_Model_Abstract::save() not to skip _afterSave()
         */
        $this->_hasDataChanges = true;

        return $this;
    }

    /**
     * Adds comment to shipment with additional possibility to send it to customer via email
     * and show it in customer account
     *
     * @param Magento_Sales_Model_Order_Shipment_Comment $comment
     * @param bool $notify
     * @param bool $visibleOnFront
     * @return Magento_Sales_Model_Order_Shipment
     */
    public function addComment($comment, $notify = false, $visibleOnFront = false)
    {
        if (!($comment instanceof Magento_Sales_Model_Order_Shipment_Comment)) {
            $comment = $this->_commentFactory->create()
                ->setComment($comment)
                ->setIsCustomerNotified($notify)
                ->setIsVisibleOnFront($visibleOnFront);
        }
        $comment->setShipment($this)
            ->setParentId($this->getId())
            ->setStoreId($this->getStoreId());
        if (!$comment->getId()) {
            $this->getCommentsCollection()->addItem($comment);
        }
        $this->_hasDataChanges = true;
        return $this;
    }

    /**
     * Retrieve comments collection.
     *
     * @param bool $reload
     * @return Magento_Sales_Model_Resource_Order_Shipment_Comment_Collection
     */
    public function getCommentsCollection($reload=false)
    {
        if (is_null($this->_comments) || $reload) {
            $this->_comments = $this->_commentCollFactory->create()
                ->setShipmentFilter($this->getId())
                ->setCreatedAtOrder();

            /**
             * When shipment created with adding comment,
             * comments collection must be loaded before we added this comment.
             */
            $this->_comments->load();

            if ($this->getId()) {
                foreach ($this->_comments as $comment) {
                    $comment->setShipment($this);
                }
            }
        }
        return $this->_comments;
    }

    /**
     * Send email with shipment data
     *
     * @param boolean $notifyCustomer
     * @param string $comment
     * @return Magento_Sales_Model_Order_Shipment
     */
    public function sendEmail($notifyCustomer = true, $comment = '')
    {
        $order = $this->getOrder();
        $storeId = $order->getStore()->getId();

        if (!$this->_salesData->canSendNewShipmentEmail($storeId)) {
            return $this;
        }
        // Get the destination email addresses to send copies to
        $copyTo = $this->_getEmails(self::XML_PATH_EMAIL_COPY_TO);
        $copyMethod = $this->_coreStoreConfig->getConfig(self::XML_PATH_EMAIL_COPY_METHOD, $storeId);
        // Check if at least one recepient is found
        if (!$notifyCustomer && !$copyTo) {
            return $this;
        }

        $paymentBlockHtml = $this->_paymentData->getInfoBlockHtml($order->getPayment(), $storeId);

        // Retrieve corresponding email template id and customer name
        if ($order->getCustomerIsGuest()) {
            $templateId = $this->_coreStoreConfig->getConfig(self::XML_PATH_EMAIL_GUEST_TEMPLATE, $storeId);
            $customerName = $order->getBillingAddress()->getName();
        } else {
            $templateId = $this->_coreStoreConfig->getConfig(self::XML_PATH_EMAIL_TEMPLATE, $storeId);
            $customerName = $order->getCustomerName();
        }

        /** @var $mailer Magento_Core_Model_Email_Template_Mailer */
        $mailer = $this->_templateMailerFactory->create();
        if ($notifyCustomer) {
            $emailInfo = $this->_emailInfoFactory->create();
            $emailInfo->addTo($order->getCustomerEmail(), $customerName);
            if ($copyTo && $copyMethod == 'bcc') {
                // Add bcc to customer email
                foreach ($copyTo as $email) {
                    $emailInfo->addBcc($email);
                }
            }
            $mailer->addEmailInfo($emailInfo);
        }

        // Email copies are sent as separated emails if their copy method is 'copy' or a customer should not be notified
        if ($copyTo && ($copyMethod == 'copy' || !$notifyCustomer)) {
            foreach ($copyTo as $email) {
                $emailInfo = $this->_emailInfoFactory->create();
                $emailInfo->addTo($email);
                $mailer->addEmailInfo($emailInfo);
            }
        }

        // Set all required params and send emails
        $mailer->setSender($this->_coreStoreConfig->getConfig(self::XML_PATH_EMAIL_IDENTITY, $storeId));
        $mailer->setStoreId($storeId);
        $mailer->setTemplateId($templateId);
        $mailer->setTemplateParams(array(
                'order'        => $order,
                'shipment'     => $this,
                'comment'      => $comment,
                'billing'      => $order->getBillingAddress(),
                'payment_html' => $paymentBlockHtml
            )
        );
        $mailer->send();

        $this->setEmailSent(true);
        $this->_getResource()->saveAttribute($this, 'email_sent');

        return $this;
    }

    /**
     * Send email with shipment update information
     *
     * @param boolean $notifyCustomer
     * @param string $comment
     * @return Magento_Sales_Model_Order_Shipment
     */
    public function sendUpdateEmail($notifyCustomer = true, $comment = '')
    {
        $order = $this->getOrder();
        $storeId = $order->getStore()->getId();

        if (!$this->_salesData->canSendShipmentCommentEmail($storeId)) {
            return $this;
        }
        // Get the destination email addresses to send copies to
        $copyTo = $this->_getEmails(self::XML_PATH_UPDATE_EMAIL_COPY_TO);
        $copyMethod = $this->_coreStoreConfig->getConfig(self::XML_PATH_UPDATE_EMAIL_COPY_METHOD, $storeId);
        // Check if at least one recepient is found
        if (!$notifyCustomer && !$copyTo) {
            return $this;
        }

        // Retrieve corresponding email template id and customer name
        if ($order->getCustomerIsGuest()) {
            $templateId = $this->_coreStoreConfig->getConfig(self::XML_PATH_UPDATE_EMAIL_GUEST_TEMPLATE, $storeId);
            $customerName = $order->getBillingAddress()->getName();
        } else {
            $templateId = $this->_coreStoreConfig->getConfig(self::XML_PATH_UPDATE_EMAIL_TEMPLATE, $storeId);
            $customerName = $order->getCustomerName();
        }

        $mailer = $this->_templateMailerFactory->create();
        if ($notifyCustomer) {
            $emailInfo = $this->_emailInfoFactory->create();
            $emailInfo->addTo($order->getCustomerEmail(), $customerName);
            if ($copyTo && $copyMethod == 'bcc') {
                // Add bcc to customer email
                foreach ($copyTo as $email) {
                    $emailInfo->addBcc($email);
                }
            }
            $mailer->addEmailInfo($emailInfo);
        }

        // Email copies are sent as separated emails if their copy method is 'copy' or a customer should not be notified
        if ($copyTo && ($copyMethod == 'copy' || !$notifyCustomer)) {
            foreach ($copyTo as $email) {
                $emailInfo = $this->_emailInfoFactory->create();
                $emailInfo->addTo($email);
                $mailer->addEmailInfo($emailInfo);
            }
        }

        // Set all required params and send emails
        $mailer->setSender($this->_coreStoreConfig->getConfig(self::XML_PATH_UPDATE_EMAIL_IDENTITY, $storeId));
        $mailer->setStoreId($storeId);
        $mailer->setTemplateId($templateId);
        $mailer->setTemplateParams(array(
                'order'    => $order,
                'shipment' => $this,
                'comment'  => $comment,
                'billing'  => $order->getBillingAddress()
            )
        );
        $mailer->send();

        return $this;
    }

    /**
     * @param string $configPath
     * @return array|bool
     */
    protected function _getEmails($configPath)
    {
        $data = $this->_coreStoreConfig->getConfig($configPath, $this->getStoreId());
        if (!empty($data)) {
            return explode(',', $data);
        }
        return false;
    }

    /**
     * Before object save
     *
     * @return Magento_Sales_Model_Order_Shipment
     * @throws Magento_Core_Exception
     */
    protected function _beforeSave()
    {
        if ((!$this->getId() || null !== $this->_items) && !count($this->getAllItems())) {
            throw new Magento_Core_Exception(__('We cannot create an empty shipment.'));
        }

        if (!$this->getOrderId() && $this->getOrder()) {
            $this->setOrderId($this->getOrder()->getId());
            $this->setShippingAddressId($this->getOrder()->getShippingAddress()->getId());
        }
        if ($this->getPackages()) {
            $this->setPackages(serialize($this->getPackages()));
        }

        return parent::_beforeSave();
    }

    /**
     * @return Magento_Core_Model_Abstract
     */
    protected function _beforeDelete()
    {
        $this->_protectFromNonAdmin();
        return parent::_beforeDelete();
    }

    /**
     * After object save manipulations
     *
     * @return Magento_Sales_Model_Order_Shipment
     */
    protected function _afterSave()
    {
        if (null !== $this->_items) {
            foreach ($this->_items as $item) {
                $item->save();
            }
        }

        if (null !== $this->_tracks) {
            foreach($this->_tracks as $track) {
                $track->save();
            }
        }

        if (null !== $this->_comments) {
            foreach($this->_comments as $comment) {
                $comment->save();
            }
        }

        return parent::_afterSave();
    }

    /**
     * Retrieve store model instance
     *
     * @return Magento_Core_Model_Store
     */
    public function getStore()
    {
        return $this->getOrder()->getStore();
    }

    /**
     * Set shipping label
     *
     * @param string $label   label representation (image or pdf file)
     * @return Magento_Sales_Model_Order_Shipment
     */
    public function setShippingLabel($label)
    {
        $this->setData('shipping_label', $label);
        return $this;
    }

    /**
     * Get shipping label and decode by db adapter
     *
     * @return void
     */
    public function getShippingLabel()
    {
        $label = $this->getData('shipping_label');
        if ($label) {
            return $this->getResource()->getReadConnection()->decodeVarbinary($label);
        }
        return $label;
    }
}
