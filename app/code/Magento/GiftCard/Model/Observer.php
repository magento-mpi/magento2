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
     * @param Magento_Core_Model_Context $context
     * @param Magento_Core_Model_Resource_Abstract $resource
     * @param Magento_Core_Model_Resource_Db_Collection_Abstract $resourceCollection
     * @param array $data
     * @throws InvalidArgumentException
     */
    public function __construct(
        Magento_Core_Model_Context $context,
        Magento_Core_Model_Resource_Abstract $resource = null,
        Magento_Core_Model_Resource_Db_Collection_Abstract $resourceCollection = null,
        array $data = array()
    ) {
        if (isset($data['email_template_model'])) {
            if (!$data['email_template_model'] instanceof Magento_Core_Model_Email_Template) {
                throw new InvalidArgumentException(
                    'Argument "email_template_model" is expected to be an instance of "Magento_Core_Model_Email_Template".'
                );
            }
            $this->_emailTemplateModel = $data['email_template_model'];
            unset($data['email_template_model']);
        }
        parent::__construct($context, $resource, $resourceCollection, $data);
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
                Mage::app()->getLayout()->createBlock('Magento_GiftCard_Block_Adminhtml_Renderer_Amount')
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
     * Append gift card additional data to order item options
     *
     * @param Magento_Event_Observer $observer
     * @return Magento_GiftCard_Model_Observer
     */
    public function appendGiftcardAdditionalData(Magento_Event_Observer $observer)
    {
        //sales_convert_quote_item_to_order_item

        $orderItem = $observer->getEvent()->getOrderItem();
        $quoteItem = $observer->getEvent()->getItem();
        $keys = array(
            'giftcard_sender_name',
            'giftcard_sender_email',
            'giftcard_recipient_name',
            'giftcard_recipient_email',
            'giftcard_message',
        );
        $productOptions = $orderItem->getProductOptions();
        foreach ($keys as $key) {
            if ($option = $quoteItem->getProduct()->getCustomOption($key)) {
                $productOptions[$key] = $option->getValue();
            }
        }

        $product = $quoteItem->getProduct();
        // set lifetime
        $lifetime = 0;
        if ($product->getUseConfigLifetime()) {
            $lifetime = Mage::getStoreConfig(
                Magento_GiftCard_Model_Giftcard::XML_PATH_LIFETIME,
                $orderItem->getStore()
            );
        } else {
            $lifetime = $product->getLifetime();
        }
        $productOptions['giftcard_lifetime'] = $lifetime;

        // set is_redeemable
        $isRedeemable = 0;
        if ($product->getUseConfigIsRedeemable()) {
            $isRedeemable = Mage::getStoreConfigFlag(
                Magento_GiftCard_Model_Giftcard::XML_PATH_IS_REDEEMABLE,
                $orderItem->getStore()
            );
        } else {
            $isRedeemable = (int) $product->getIsRedeemable();
        }
        $productOptions['giftcard_is_redeemable'] = $isRedeemable;

        // set email_template
        $emailTemplate = 0;
        if ($product->getUseConfigEmailTemplate()) {
            $emailTemplate = Mage::getStoreConfig(
                Magento_GiftCard_Model_Giftcard::XML_PATH_EMAIL_TEMPLATE,
                $orderItem->getStore()
            );
        } else {
            $emailTemplate = $product->getEmailTemplate();
        }
        $productOptions['giftcard_email_template'] = $emailTemplate;
        $productOptions['giftcard_type'] = $product->getGiftcardType();

        $orderItem->setProductOptions($productOptions);

        return $this;
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
        $requiredStatus = Mage::getStoreConfig(
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
                        $invoiceItemCollection = Mage::getResourceModel(
                            'Magento_Sales_Model_Resource_Order_Invoice_Item_Collection'
                        )->addFieldToFilter('order_item_id', $item->getId());

                        foreach ($invoiceItemCollection as $invoiceItem) {
                            $invoiceId = $invoiceItem->getParentId();
                            if(isset($loadedInvoices[$invoiceId])) {
                                $invoice = $loadedInvoices[$invoiceId];
                            } else {
                                $invoice = Mage::getModel('Magento_Sales_Model_Order_Invoice')->load($invoiceId);
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
                    if ($option = $item->getProductOptionByCode('giftcard_is_redeemable')) {
                        $isRedeemable = $option;
                    }

                    $lifetime = 0;
                    if ($option = $item->getProductOptionByCode('giftcard_lifetime')) {
                        $lifetime = $option;
                    }

                    $amount = $item->getBasePrice();
                    $websiteId = Mage::app()->getStore($order->getStoreId())->getWebsiteId();

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
                            Mage::dispatchEvent('magento_giftcardaccount_create', array(
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
                        if ($senderEmail = $item->getProductOptionByCode('giftcard_sender_email')) {
                            $sender = "$sender <$senderEmail>";
                        }

                        $codeList = Mage::helper('Magento_GiftCard_Helper_Data')->getEmailGeneratedItemsBlock()
                            ->setCodes($codes)
                            ->setIsRedeemable($isRedeemable)
                            ->setStore(Mage::app()->getStore($order->getStoreId()));
                        $balance = Mage::app()->getLocale()->currency(
                            Mage::app()->getStore($order->getStoreId())->getBaseCurrencyCode()
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

                        $email = $this->_emailTemplateModel ?: Mage::getModel('Magento_Core_Model_Email_Template');
                        $email->setDesignConfig(array(
                            'area' => Magento_Core_Model_App_Area::AREA_FRONTEND,
                            'store' => $item->getOrder()->getStoreId(),
                        ));
                        $email->sendTransactional(
                            $item->getProductOptionByCode('giftcard_email_template'),
                            Mage::getStoreConfig(
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
                    $url = Mage::getSingleton('Magento_Backend_Model_Url')->getUrl('adminhtml/giftcardaccount');
                    $message = __('Some gift card accounts were not created properly. You can create gift card accounts manually <a href="%1">here</a>.', $url);

                    Mage::getSingleton('Magento_Adminhtml_Model_Session')->addError($message);
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
