<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GiftCard\Model;

class Observer extends \Magento\Framework\Model\AbstractModel
{
    const ATTRIBUTE_CODE = 'giftcard_amounts';

    /**
     * Gift card data
     *
     * @var \Magento\GiftCard\Helper\Data
     */
    protected $_giftCardData = null;

    /**
     * Scope config
     *
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * Url model
     *
     * @var \Magento\Framework\UrlInterface
     */
    protected $_urlModel;

    /**
     * Invoice factory
     *
     * @var \Magento\Sales\Model\Order\InvoiceFactory
     */
    protected $_invoiceFactory;

    /**
     * @var \Magento\Framework\Mail\Template\TransportBuilder
     */
    protected $_transportBuilder;

    /**
     * Invoice items collection factory
     *
     * @var \Magento\Sales\Model\Resource\Order\Invoice\Item\CollectionFactory
     */
    protected $_itemsFactory;

    /**
     * Store manager
     *
     * @var \Magento\Framework\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Layout
     *
     * @var \Magento\Framework\View\LayoutInterface
     */
    protected $_layout;

    /**
     * @var \Magento\Framework\Locale\CurrencyInterface
     */
    protected $_localeCurrency;

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\StoreManagerInterface $storeManager
     * @param \Magento\Framework\View\LayoutInterface $layout
     * @param \Magento\Framework\Locale\CurrencyInterface $localeCurrency
     * @param \Magento\Sales\Model\Resource\Order\Invoice\Item\CollectionFactory $itemsFactory
     * @param \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder ,
     * @param \Magento\Sales\Model\Order\InvoiceFactory $invoiceFactory
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Magento\Framework\UrlInterface $urlModel
     * @param \Magento\GiftCard\Helper\Data $giftCardData
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\Model\Resource\AbstractResource $resource
     * @param \Magento\Framework\Model\Resource\Db\Collection\AbstractCollection $resourceCollection
     * @param array $data
     *
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\StoreManagerInterface $storeManager,
        \Magento\Framework\View\LayoutInterface $layout,
        \Magento\Framework\Locale\CurrencyInterface $localeCurrency,
        \Magento\Sales\Model\Resource\Order\Invoice\Item\CollectionFactory $itemsFactory,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Sales\Model\Order\InvoiceFactory $invoiceFactory,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\UrlInterface $urlModel,
        \Magento\GiftCard\Helper\Data $giftCardData,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Model\Resource\AbstractResource $resource = null,
        \Magento\Framework\Model\Resource\Db\Collection\AbstractCollection $resourceCollection = null,
        array $data = array()
    ) {
        $this->_storeManager = $storeManager;
        $this->_layout = $layout;
        $this->_localeCurrency = $localeCurrency;
        $this->_itemsFactory = $itemsFactory;
        $this->_transportBuilder = $transportBuilder;
        $this->_invoiceFactory = $invoiceFactory;
        $this->messageManager = $messageManager;
        $this->_urlModel = $urlModel;
        $this->_giftCardData = $giftCardData;
        $this->_scopeConfig = $scopeConfig;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Set attribute renderer on catalog product edit page
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function setAmountsRendererInForm(\Magento\Framework\Event\Observer $observer)
    {
        //adminhtml_catalog_product_edit_prepare_form
        $form = $observer->getEvent()->getForm();
        $elem = $form->getElement(self::ATTRIBUTE_CODE);

        if ($elem) {
            $elem->setRenderer($this->_layout->createBlock('Magento\GiftCard\Block\Adminhtml\Renderer\Amount'));
        }
    }

    /**
     * Set giftcard amounts field as not used in mass update
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function updateExcludedFieldList(\Magento\Framework\Event\Observer $observer)
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
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function generateGiftCardAccounts(\Magento\Framework\Event\Observer $observer)
    {
        // sales_order_save_after

        $order = $observer->getEvent()->getOrder();
        $requiredStatus = $this->_scopeConfig->getValue(
            \Magento\GiftCard\Model\Giftcard::XML_PATH_ORDER_ITEM_STATUS,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $order->getStore()
        );
        $loadedInvoices = array();

        foreach ($order->getAllItems() as $item) {
            if ($item->getProductType() == \Magento\GiftCard\Model\Catalog\Product\Type\Giftcard::TYPE_GIFTCARD) {
                $qty = 0;
                $options = $item->getProductOptions();

                switch ($requiredStatus) {
                    case \Magento\Sales\Model\Order\Item::STATUS_INVOICED:
                        $paidInvoiceItems = isset(
                            $options['giftcard_paid_invoice_items']
                        ) ? $options['giftcard_paid_invoice_items'] : array();
                        // find invoice for this order item
                        $invoiceItemCollection = $this->_itemsFactory->create()->addFieldToFilter(
                            'order_item_id',
                            $item->getId()
                        );

                        foreach ($invoiceItemCollection as $invoiceItem) {
                            $invoiceId = $invoiceItem->getParentId();
                            if (isset($loadedInvoices[$invoiceId])) {
                                $invoice = $loadedInvoices[$invoiceId];
                            } else {
                                $invoice = $this->_invoiceFactory->create()->load($invoiceId);
                                $loadedInvoices[$invoiceId] = $invoice;
                            }
                            // check, if this order item has been paid
                            if ($invoice->getState() == \Magento\Sales\Model\Order\Invoice::STATE_PAID && !in_array(
                                $invoiceItem->getId(),
                                $paidInvoiceItems
                            )
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

                    $data = new \Magento\Framework\Object();
                    $data->setWebsiteId(
                        $websiteId
                    )->setAmount(
                        $amount
                    )->setLifetime(
                        $lifetime
                    )->setIsRedeemable(
                        $isRedeemable
                    )->setOrderItem(
                        $item
                    );

                    $codes = isset($options['giftcard_created_codes']) ? $options['giftcard_created_codes'] : array();
                    $goodCodes = 0;
                    for ($i = 0; $i < $qty; $i++) {
                        try {
                            $code = new \Magento\Framework\Object();
                            $this->_eventManager->dispatch(
                                'magento_giftcardaccount_create',
                                array('request' => $data, 'code' => $code)
                            );
                            $codes[] = $code->getCode();
                            $goodCodes++;
                        } catch (\Magento\Framework\Model\Exception $e) {
                            $hasFailedCodes = true;
                            $codes[] = null;
                        }
                    }
                    if ($goodCodes && $item->getProductOptionByCode('giftcard_recipient_email')) {
                        $sender = $item->getProductOptionByCode('giftcard_sender_name');
                        $senderName = $item->getProductOptionByCode('giftcard_sender_name');
                        $senderEmail = $item->getProductOptionByCode('giftcard_sender_email');
                        if ($senderEmail) {
                            $sender = "{$sender} <{$senderEmail}>";
                        }

                        $codeList = $this->_giftCardData->getEmailGeneratedItemsBlock()->setCodes(
                            $codes
                        )->setIsRedeemable(
                            $isRedeemable
                        )->setStore(
                            $this->_storeManager->getStore($order->getStoreId())
                        );
                        $balance = $this->_localeCurrency->getCurrency(
                            $this->_storeManager->getStore($order->getStoreId())->getBaseCurrencyCode()
                        )->toCurrency(
                            $amount
                        );

                        $templateData = array(
                            'name' => $item->getProductOptionByCode('giftcard_recipient_name'),
                            'email' => $item->getProductOptionByCode('giftcard_recipient_email'),
                            'sender_name_with_email' => $sender,
                            'sender_name' => $senderName,
                            'gift_message' => $item->getProductOptionByCode('giftcard_message'),
                            'giftcards' => $codeList->toHtml(),
                            'balance' => $balance,
                            'is_multiple_codes' => 1 < $goodCodes,
                            'store' => $order->getStore(),
                            'store_name' => $order->getStore()->getName(),
                            'is_redeemable' => $isRedeemable
                        );

                        $transport = $this->_transportBuilder->setTemplateIdentifier(
                            $item->getProductOptionByCode('giftcard_email_template')
                        )->setTemplateOptions(
                            array(
                                'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                                'store' => $item->getOrder()->getStoreId()
                            )
                        )->setTemplateVars(
                            $templateData
                        )->setFrom(
                            $this->_scopeConfig->getValue(
                                \Magento\GiftCard\Model\Giftcard::XML_PATH_EMAIL_IDENTITY,
                                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                                $item->getOrder()->getStoreId()
                            )
                        )->addTo(
                            $item->getProductOptionByCode('giftcard_recipient_email'),
                            $item->getProductOptionByCode('giftcard_recipient_name')
                        )->getTransport();

                        $transport->sendMessage();
                        $options['email_sent'] = 1;
                    }
                    $options['giftcard_created_codes'] = $codes;
                    $item->setProductOptions($options);
                    $item->save();
                }
                if ($hasFailedCodes) {
                    $url = $this->_urlModel->getUrl('adminhtml/giftcardaccount');
                    $message = __(
                        'Some gift card accounts were not created properly. You can create gift card accounts manually <a href="%1">here</a>.',
                        $url
                    );

                    $this->messageManager->addError($message);
                }
            }
        }

        return $this;
    }

    /**
     * Process `giftcard_amounts` attribute afterLoad logic on loading by collection
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function loadAttributesAfterCollectionLoad(\Magento\Framework\Event\Observer $observer)
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
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function initOptionRenderer(\Magento\Framework\Event\Observer $observer)
    {
        $block = $observer->getBlock();
        $block->addOptionsRenderCfg('giftcard', 'Magento\GiftCard\Helper\Catalog\Product\Configuration');
        return $this;
    }
}
