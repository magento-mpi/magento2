<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftCard
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_GiftCard_Model_Observer extends Magento_Core_Model_Abstract
{
    const ATTRIBUTE_CODE = 'giftcard_amounts';

    /**
     * Email template model instance
     *
     * @var Magento_Core_Model_Email_Template
     */
    protected $_emailTemplateModel;

    /**
     * Gift card data
     *
     * @var Magento_GiftCard_Helper_Data
     */
    protected $_giftCardData = null;

    /**
     * Core store config
     *
     * @var Magento_Core_Model_Store_Config
     */
    protected $_coreStoreConfig;

    /**
     * Session
     *
     * @var Magento_Core_Model_Session_Abstract
     */
    protected $_session;

    /**
     * Url model
     *
     * @var Magento_Core_Model_UrlInterface
     */
    protected $_urlModel;

    /**
     * Invoice factory
     *
     * @var Magento_Sales_Model_Order_InvoiceFactory
     */
    protected $_invoiceFactory;

    /**
     * Template factory
     *
     * @var Magento_Core_Model_Email_TemplateFactory
     */
    protected $_templateFactory;

    /**
     * Invoice items collection factory
     *
     * @var Magento_Sales_Model_Resource_Order_Invoice_Item_CollectionFactory
     */
    protected $_itemsFactory;

    /**
     * Store manager
     *
     * @var Magento_Core_Model_StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Layout
     *
     * @var Magento_Core_Model_Layout
     */
    protected $_layout;

    /**
     * Locale
     *
     * @var Magento_Core_Model_LocaleInterface
     */
    protected $_locale;

    /**
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param Magento_Core_Model_Layout $layout
     * @param Magento_Core_Model_LocaleInterface $locale
     * @param Magento_Sales_Model_Resource_Order_Invoice_Item_CollectionFactory $itemsFactory
     * @param Magento_Core_Model_Email_TemplateFactory $templateFactory
     * @param Magento_Sales_Model_Order_InvoiceFactory $invoiceFactory
     * @param Magento_Core_Model_Session_Abstract $session
     * @param Magento_Core_Model_UrlInterface $urlModel
     * @param Magento_GiftCard_Helper_Data $giftCardData
     * @param Magento_Core_Model_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_Core_Model_Store_Config $coreStoreConfig
     * @param Magento_Core_Model_Resource_Abstract $resource
     * @param Magento_Core_Model_Resource_Db_Collection_Abstract $resourceCollection
     * @param array $data
     * @throws InvalidArgumentException
     */
    public function __construct(
        Magento_Core_Model_StoreManagerInterface $storeManager,
        Magento_Core_Model_Layout $layout,
        Magento_Core_Model_LocaleInterface $locale,
        Magento_Sales_Model_Resource_Order_Invoice_Item_CollectionFactory $itemsFactory,
        Magento_Core_Model_Email_TemplateFactory $templateFactory,
        Magento_Sales_Model_Order_InvoiceFactory $invoiceFactory,
        Magento_Core_Model_Session_Abstract $session,
        Magento_Core_Model_UrlInterface $urlModel,
        Magento_GiftCard_Helper_Data $giftCardData,
        Magento_Core_Model_Context $context,
        Magento_Core_Model_Registry $registry,
        Magento_Core_Model_Store_Config $coreStoreConfig,
        Magento_Core_Model_Resource_Abstract $resource = null,
        Magento_Core_Model_Resource_Db_Collection_Abstract $resourceCollection = null,
        array $data = array()
    ) {
        $this->_storeManager = $storeManager;
        $this->_layout = $layout;
        $this->_locale = $locale;
        $this->_itemsFactory = $itemsFactory;
        $this->_templateFactory = $templateFactory;
        $this->_invoiceFactory = $invoiceFactory;
        $this->_session = $session;
        $this->_urlModel = $urlModel;
        $this->_giftCardData = $giftCardData;
        $this->_coreStoreConfig = $coreStoreConfig;
        if (isset($data['email_template_model'])) {
            if (!$data['email_template_model'] instanceof Magento_Core_Model_Email_Template) {
                throw new InvalidArgumentException(
                    'Argument "email_template_model" is expected to be an'
                        . ' instance of "Magento_Core_Model_Email_Template".'
                );
            }
            $this->_emailTemplateModel = $data['email_template_model'];
            unset($data['email_template_model']);
        }
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Set attribute renderer on catalog product edit page
     *
     * @param Magento_Event_Observer $observer
     */
    public function setAmountsRendererInForm(Magento_Event_Observer $observer)
    {
        //adminhtml_catalog_product_edit_prepare_form
        $form = $observer->getEvent()->getForm();
        $elem = $form->getElement(self::ATTRIBUTE_CODE);

        if ($elem) {
            $elem->setRenderer(
                $this->_layout->createBlock('Magento_GiftCard_Block_Adminhtml_Renderer_Amount')
            );
        }
    }

    /**
     * Set giftcard amounts field as not used in mass update
     *
     * @param Magento_Event_Observer $observer
     */
    public function updateExcludedFieldList(Magento_Event_Observer $observer)
    {
        //adminhtml_catalog_product_form_prepare_excluded_field_list

        $block = $observer->getEvent()->getObject();
        $list = $block->getFormExcludedFieldList();
        $list[] = self::ATTRIBUTE_CODE;
        $block->setFormExcludedFieldList($list);
    }

    /**
     * Generate gift card accounts after order save
     *
     * @param Magento_Event_Observer $observer
     * @return Magento_GiftCard_Model_Observer
     */
    public function generateGiftCardAccounts(Magento_Event_Observer $observer)
    {
        // sales_order_save_after

        $order = $observer->getEvent()->getOrder();
        $requiredStatus = $this->_coreStoreConfig->getConfig(
            Magento_GiftCard_Model_Giftcard::XML_PATH_ORDER_ITEM_STATUS,
            $order->getStore()
        );
        $loadedInvoices = array();

        foreach ($order->getAllItems() as $item) {
            if ($item->getProductType() == Magento_GiftCard_Model_Catalog_Product_Type_Giftcard::TYPE_GIFTCARD) {
                $qty = 0;
                $options = $item->getProductOptions();

                switch ($requiredStatus) {
                    case Magento_Sales_Model_Order_Item::STATUS_INVOICED:
                        $paidInvoiceItems = isset($options['giftcard_paid_invoice_items'])
                            ? $options['giftcard_paid_invoice_items']
                            : array();
                        // find invoice for this order item
                        $invoiceItemCollection = $this->_itemsFactory->create()
                            ->addFieldToFilter('order_item_id', $item->getId());

                        foreach ($invoiceItemCollection as $invoiceItem) {
                            $invoiceId = $invoiceItem->getParentId();
                            if (isset($loadedInvoices[$invoiceId])) {
                                $invoice = $loadedInvoices[$invoiceId];
                            } else {
                                $invoice = $this->_invoiceFactory->create()->load($invoiceId);
                                $loadedInvoices[$invoiceId] = $invoice;
                            }
                            // check, if this order item has been paid
                            if ($invoice->getState() == Magento_Sales_Model_Order_Invoice::STATE_PAID &&
                                !in_array($invoiceItem->getId(), $paidInvoiceItems)
                            ) {
                                $qty += $invoiceItem->getQty();
                                $paidInvoiceItems[] = $invoiceItem->getId();
                            }
                        }
                        $options['giftcard_paid_invoice_items'] = $paidInvoiceItems;
                        break;
                    default:
                        $qty = $item->getQtyOrdered();
                        if (isset($options['giftcard_created_codes'])) {
                            $qty -= count($options['giftcard_created_codes']);
                        }
                        break;
                }

                $hasFailedCodes = false;
                if ($qty > 0) {
                    $isRedeemable = 0;
                    $option = $item->getProductOptionByCode('giftcard_is_redeemable');
                    if ($option) {
                        $isRedeemable = $option;
                    }

                    $lifetime = 0;
                    $option = $item->getProductOptionByCode('giftcard_lifetime');
                    if ($option) {
                        $lifetime = $option;
                    }

                    $amount = $item->getBasePrice();
                    $websiteId = $this->_storeManager->getStore($order->getStoreId())->getWebsiteId();

                    $data = new Magento_Object();
                    $data->setWebsiteId($websiteId)
                        ->setAmount($amount)
                        ->setLifetime($lifetime)
                        ->setIsRedeemable($isRedeemable)
                        ->setOrderItem($item);

                    $codes = isset($options['giftcard_created_codes']) ? $options['giftcard_created_codes'] : array();
                    $goodCodes = 0;
                    for ($i = 0; $i < $qty; $i++) {
                        try {
                            $code = new Magento_Object();
                            $this->_eventDispatcher->dispatch('magento_giftcardaccount_create', array(
                                'request' => $data, 'code' => $code
                            ));
                            $codes[] = $code->getCode();
                            $goodCodes++;
                        } catch (Magento_Core_Exception $e) {
                            $hasFailedCodes = true;
                            $codes[] = null;
                        }
                    }
                    if ($goodCodes && $item->getProductOptionByCode('giftcard_recipient_email')) {
                        $sender = $item->getProductOptionByCode('giftcard_sender_name');
                        $senderName = $item->getProductOptionByCode('giftcard_sender_name');
                        $senderEmail = $item->getProductOptionByCode('giftcard_sender_email');
                        if ($senderEmail) {
                            $sender = "$sender <$senderEmail>";
                        }

                        $codeList = $this->_giftCardData->getEmailGeneratedItemsBlock()
                            ->setCodes($codes)
                            ->setIsRedeemable($isRedeemable)
                            ->setStore($this->_storeManager->getStore($order->getStoreId()));
                        $balance = $this->_locale->currency(
                            $this->_storeManager->getStore($order->getStoreId())->getBaseCurrencyCode()
                        )->toCurrency($amount);

                        $templateData = array(
                            'name'                   => $item->getProductOptionByCode('giftcard_recipient_name'),
                            'email'                  => $item->getProductOptionByCode('giftcard_recipient_email'),
                            'sender_name_with_email' => $sender,
                            'sender_name'            => $senderName,
                            'gift_message'           => $item->getProductOptionByCode('giftcard_message'),
                            'giftcards'              => $codeList->toHtml(),
                            'balance'                => $balance,
                            'is_multiple_codes'      => 1 < $goodCodes,
                            'store'                  => $order->getStore(),
                            'store_name'             => $order->getStore()->getName(),
                            'is_redeemable'          => $isRedeemable,
                        );

                        $email = $this->_emailTemplateModel ?: $this->_templateFactory->create();
                        $email->setDesignConfig(array(
                            'area' => Magento_Core_Model_App_Area::AREA_FRONTEND,
                            'store' => $item->getOrder()->getStoreId(),
                        ));
                        $email->sendTransactional(
                            $item->getProductOptionByCode('giftcard_email_template'),
                            $this->_coreStoreConfig->getConfig(
                                Magento_GiftCard_Model_Giftcard::XML_PATH_EMAIL_IDENTITY,
                                $item->getOrder()->getStoreId()
                            ),
                            $item->getProductOptionByCode('giftcard_recipient_email'),
                            $item->getProductOptionByCode('giftcard_recipient_name'),
                            $templateData
                        );

                        if ($email->getSentSuccess()) {
                            $options['email_sent'] = 1;
                        }
                    }
                    $options['giftcard_created_codes'] = $codes;
                    $item->setProductOptions($options);
                    $item->save();
                }
                if ($hasFailedCodes) {
                    $url = $this->_urlModel->getUrl('adminhtml/giftcardaccount');
                    $message = __('Some gift card accounts were not created properly. You can create gift card accounts manually <a href="%1">here</a>.', $url);

                    $this->_session->addError($message);
                }
            }
        }

        return $this;
    }

    /**
     * Process `giftcard_amounts` attribute afterLoad logic on loading by collection
     *
     * @param Magento_Event_Observer $observer
     * @return Magento_GiftCard_Model_Observer
     */
    public function loadAttributesAfterCollectionLoad(Magento_Event_Observer $observer)
    {
        $collection = $observer->getEvent()->getCollection();

        foreach ($collection as $item) {
            if (Magento_GiftCard_Model_Catalog_Product_Type_Giftcard::TYPE_GIFTCARD == $item->getTypeId()) {
                $attribute = $item->getResource()->getAttribute('giftcard_amounts');
                if ($attribute->getId()) {
                    $attribute->getBackend()->afterLoad($item);
                }
            }
        }
        return $this;
    }

    /**
     * Initialize product options renderer with giftcard specific params
     *
     * @param Magento_Event_Observer $observer
     * @return Magento_GiftCard_Model_Observer
     */
    public function initOptionRenderer(Magento_Event_Observer $observer)
    {
        $block = $observer->getBlock();
        $block->addOptionsRenderCfg('giftcard', 'Magento_GiftCard_Helper_Catalog_Product_Configuration');
        return $this;
    }
}
