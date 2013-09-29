<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftCard
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftCard\Model;

class Observer extends \Magento\Core\Model\AbstractModel
{
    const ATTRIBUTE_CODE = 'giftcard_amounts';

    /**
     * Email template model instance
     *
     * @var \Magento\Core\Model\Email\Template
     */
    protected $_emailTemplateModel;

    /**
     * Gift card data
     *
     * @var \Magento\GiftCard\Helper\Data
     */
    protected $_giftCardData = null;

    /**
     * Core store config
     *
     * @var \Magento\Core\Model\Store\Config
     */
    protected $_coreStoreConfig;

    /**
     * Session
     *
     * @var \Magento\Core\Model\Session\AbstractSession
     */
    protected $_session;

    /**
     * Url model
     *
     * @var \Magento\Core\Model\UrlInterface
     */
    protected $_urlModel;

    /**
     * Invoice factory
     *
     * @var \Magento\Sales\Model\Order\InvoiceFactory
     */
    protected $_invoiceFactory;

    /**
     * Template factory
     *
     * @var \Magento\Core\Model\Email\TemplateFactory
     */
    protected $_templateFactory;

    /**
     * Invoice items collection factory
     *
     * @var \Magento\Sales\Model\Resource\Order\Invoice\Item\CollectionFactory
     */
    protected $_itemsFactory;

    /**
     * Store manager
     *
     * @var \Magento\Core\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Layout
     *
     * @var \Magento\Core\Model\Layout
     */
    protected $_layout;

    /**
     * Locale
     *
     * @var \Magento\Core\Model\LocaleInterface
     */
    protected $_locale;

    /**
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param \Magento\Core\Model\Layout $layout
     * @param \Magento\Core\Model\LocaleInterface $locale
     * @param \Magento\Sales\Model\Resource\Order\Invoice\Item\CollectionFactory $itemsFactory
     * @param \Magento\Core\Model\Email\TemplateFactory $templateFactory
     * @param \Magento\Sales\Model\Order\InvoiceFactory $invoiceFactory
     * @param \Magento\Core\Model\Session\AbstractSession $session
     * @param \Magento\Core\Model\UrlInterface $urlModel
     * @param \Magento\GiftCard\Helper\Data $giftCardData
     * @param \Magento\Core\Model\Context $context
     * @param \Magento\Core\Model\Registry $registry
     * @param \Magento\Core\Model\Store\Config $coreStoreConfig
     * @param \Magento\Core\Model\Resource\AbstractResource $resource
     * @param \Magento\Core\Model\Resource\Db\Collection\AbstractCollection $resourceCollection
     * @param array $data
     * @throws \InvalidArgumentException
     */
    public function __construct(
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        \Magento\Core\Model\Layout $layout,
        \Magento\Core\Model\LocaleInterface $locale,
        \Magento\Sales\Model\Resource\Order\Invoice\Item\CollectionFactory $itemsFactory,
        \Magento\Core\Model\Email\TemplateFactory $templateFactory,
        \Magento\Sales\Model\Order\InvoiceFactory $invoiceFactory,
        \Magento\Core\Model\Session\AbstractSession $session,
        \Magento\Core\Model\UrlInterface $urlModel,
        \Magento\GiftCard\Helper\Data $giftCardData,
        \Magento\Core\Model\Context $context,
        \Magento\Core\Model\Registry $registry,
        \Magento\Core\Model\Store\Config $coreStoreConfig,
        \Magento\Core\Model\Resource\AbstractResource $resource = null,
        \Magento\Core\Model\Resource\Db\Collection\AbstractCollection $resourceCollection = null,
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
            if (!$data['email_template_model'] instanceof \Magento\Core\Model\Email\Template) {
                throw new \InvalidArgumentException(
                    'Argument "email_template_model" is expected to be an'
                        . ' instance of "Magento\Core\Model\Email\Template".'
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
     * @param \Magento\Event\Observer $observer
     */
    public function setAmountsRendererInForm(\Magento\Event\Observer $observer)
    {
        //adminhtml_catalog_product_edit_prepare_form
        $form = $observer->getEvent()->getForm();
        $elem = $form->getElement(self::ATTRIBUTE_CODE);

        if ($elem) {
            $elem->setRenderer(
                $this->_layout->createBlock('Magento\GiftCard\Block\Adminhtml\Renderer\Amount')
            );
        }
    }

    /**
     * Set giftcard amounts field as not used in mass update
     *
     * @param \Magento\Event\Observer $observer
     */
    public function updateExcludedFieldList(\Magento\Event\Observer $observer)
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
     * @param \Magento\Event\Observer $observer
     * @return \Magento\GiftCard\Model\Observer
     */
    public function generateGiftCardAccounts(\Magento\Event\Observer $observer)
    {
        // sales_order_save_after

        $order = $observer->getEvent()->getOrder();
        $requiredStatus = $this->_coreStoreConfig->getConfig(
            \Magento\GiftCard\Model\Giftcard::XML_PATH_ORDER_ITEM_STATUS,
            $order->getStore()
        );
        $loadedInvoices = array();

        foreach ($order->getAllItems() as $item) {
            if ($item->getProductType() == \Magento\GiftCard\Model\Catalog\Product\Type\Giftcard::TYPE_GIFTCARD) {
                $qty = 0;
                $options = $item->getProductOptions();

                switch ($requiredStatus) {
                    case \Magento\Sales\Model\Order\Item::STATUS_INVOICED:
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
                            if ($invoice->getState() == \Magento\Sales\Model\Order\Invoice::STATE_PAID &&
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

                    $data = new \Magento\Object();
                    $data->setWebsiteId($websiteId)
                        ->setAmount($amount)
                        ->setLifetime($lifetime)
                        ->setIsRedeemable($isRedeemable)
                        ->setOrderItem($item);

                    $codes = isset($options['giftcard_created_codes']) ? $options['giftcard_created_codes'] : array();
                    $goodCodes = 0;
                    for ($i = 0; $i < $qty; $i++) {
                        try {
                            $code = new \Magento\Object();
                            $this->_eventDispatcher->dispatch('magento_giftcardaccount_create', array(
                                'request' => $data, 'code' => $code
                            ));
                            $codes[] = $code->getCode();
                            $goodCodes++;
                        } catch (\Magento\Core\Exception $e) {
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
                            'area' => \Magento\Core\Model\App\Area::AREA_FRONTEND,
                            'store' => $item->getOrder()->getStoreId(),
                        ));
                        $email->sendTransactional(
                            $item->getProductOptionByCode('giftcard_email_template'),
                            $this->_coreStoreConfig->getConfig(
                                \Magento\GiftCard\Model\Giftcard::XML_PATH_EMAIL_IDENTITY,
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
     * @param \Magento\Event\Observer $observer
     * @return \Magento\GiftCard\Model\Observer
     */
    public function loadAttributesAfterCollectionLoad(\Magento\Event\Observer $observer)
    {
        $collection = $observer->getEvent()->getCollection();

        foreach ($collection as $item) {
            if (\Magento\GiftCard\Model\Catalog\Product\Type\Giftcard::TYPE_GIFTCARD == $item->getTypeId()) {
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
     * @param \Magento\Event\Observer $observer
     * @return \Magento\GiftCard\Model\Observer
     */
    public function initOptionRenderer(\Magento\Event\Observer $observer)
    {
        $block = $observer->getBlock();
        $block->addOptionsRenderCfg('giftcard', 'Magento\GiftCard\Helper\Catalog\Product\Configuration');
        return $this;
    }
}
