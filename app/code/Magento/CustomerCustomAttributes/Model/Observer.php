<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\CustomerCustomAttributes\Model;

/**
 * Customer observer
 */
class Observer
{
    const CONVERT_ALGORITM_SOURCE_TARGET_WITH_PREFIX = 1;

    const CONVERT_ALGORITM_SOURCE_WITHOUT_PREFIX = 2;

    const CONVERT_ALGORITM_TARGET_WITHOUT_PREFIX = 3;

    const CONVERT_TYPE_CUSTOMER = 'customer';

    const CONVERT_TYPE_CUSTOMER_ADDRESS = 'customer_address';

    /**
     * @var \Magento\CustomerCustomAttributes\Helper\Data
     */
    protected $_customerData;

    /**
     * @var \Magento\CustomerCustomAttributes\Model\Sales\OrderFactory
     */
    protected $_orderFactory;

    /**
     * @var \Magento\CustomerCustomAttributes\Model\Sales\Order\AddressFactory
     */
    protected $_orderAddressFactory;

    /**
     * @var \Magento\CustomerCustomAttributes\Model\Sales\QuoteFactory
     */
    protected $_quoteFactory;

    /**
     * @var \Magento\CustomerCustomAttributes\Model\Sales\Quote\AddressFactory
     */
    protected $_quoteAddressFactory;

    /**
     * @param \Magento\CustomerCustomAttributes\Helper\Data $customerData
     * @param \Magento\CustomerCustomAttributes\Model\Sales\OrderFactory $orderFactory
     * @param \Magento\CustomerCustomAttributes\Model\Sales\Order\AddressFactory $orderAddressFactory
     * @param \Magento\CustomerCustomAttributes\Model\Sales\QuoteFactory $quoteFactory
     * @param \Magento\CustomerCustomAttributes\Model\Sales\Quote\AddressFactory $quoteAddressFactory
     */
    public function __construct(
        \Magento\CustomerCustomAttributes\Helper\Data $customerData,
        \Magento\CustomerCustomAttributes\Model\Sales\OrderFactory $orderFactory,
        \Magento\CustomerCustomAttributes\Model\Sales\Order\AddressFactory $orderAddressFactory,
        \Magento\CustomerCustomAttributes\Model\Sales\QuoteFactory $quoteFactory,
        \Magento\CustomerCustomAttributes\Model\Sales\Quote\AddressFactory $quoteAddressFactory
    ) {
        $this->_orderFactory = $orderFactory;
        $this->_orderAddressFactory = $orderAddressFactory;
        $this->_quoteFactory = $quoteFactory;
        $this->_quoteAddressFactory = $quoteAddressFactory;
        $this->_customerData = $customerData;
    }

    /**
     * After load observer for quote
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function salesQuoteAfterLoad(\Magento\Framework\Event\Observer $observer)
    {
        $quote = $observer->getEvent()->getQuote();
        if ($quote instanceof \Magento\Framework\Model\AbstractModel) {
            /** @var $quoteModel \Magento\CustomerCustomAttributes\Model\Sales\Quote */
            $quoteModel = $this->_quoteFactory->create();
            $quoteModel->load($quote->getId());
            $quoteModel->attachAttributeData($quote);
        }
        return $this;
    }

    /**
     * After load observer for collection of quote address
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function salesQuoteAddressCollectionAfterLoad(\Magento\Framework\Event\Observer $observer)
    {
        $collection = $observer->getEvent()->getQuoteAddressCollection();
        if ($collection instanceof \Magento\Framework\Data\Collection\Db) {
            /** @var $quoteAddress \Magento\CustomerCustomAttributes\Model\Sales\Quote\Address */
            $quoteAddress = $this->_quoteAddressFactory->create();
            $quoteAddress->attachDataToEntities($collection->getItems());
        }
        return $this;
    }

    /**
     * After save observer for quote
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function salesQuoteAfterSave(\Magento\Framework\Event\Observer $observer)
    {
        $quote = $observer->getEvent()->getQuote();
        if ($quote instanceof \Magento\Framework\Model\AbstractModel) {
            /** @var $quoteModel \Magento\CustomerCustomAttributes\Model\Sales\Quote */
            $quoteModel = $this->_quoteFactory->create();
            $quoteModel->saveAttributeData($quote);
        }
        return $this;
    }

    /**
     * After save observer for quote address
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function salesQuoteAddressAfterSave(\Magento\Framework\Event\Observer $observer)
    {
        $quoteAddress = $observer->getEvent()->getQuoteAddress();
        if ($quoteAddress instanceof \Magento\Framework\Model\AbstractModel) {
            /** @var $quoteAddressModel \Magento\CustomerCustomAttributes\Model\Sales\Quote\Address */
            $quoteAddressModel = $this->_quoteAddressFactory->create();
            $quoteAddressModel->saveAttributeData($quoteAddress);
        }
        return $this;
    }

    /**
     * After load observer for order
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function salesOrderAfterLoad(\Magento\Framework\Event\Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();
        if ($order instanceof \Magento\Framework\Model\AbstractModel) {
            /** @var $orderModel \Magento\CustomerCustomAttributes\Model\Sales\Order */
            $orderModel = $this->_orderFactory->create();
            $orderModel->load($order->getId());
            $orderModel->attachAttributeData($order);
        }
        return $this;
    }

    /**
     * After load observer for collection of order address
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function salesOrderAddressCollectionAfterLoad(\Magento\Framework\Event\Observer $observer)
    {
        $collection = $observer->getEvent()->getOrderAddressCollection();
        if ($collection instanceof \Magento\Framework\Data\Collection\Db) {
            /** @var $orderAddress \Magento\CustomerCustomAttributes\Model\Sales\Order\Address */
            $orderAddress = $this->_orderAddressFactory->create();
            $orderAddress->attachDataToEntities($collection->getItems());
        }
        return $this;
    }

    /**
     * After save observer for order
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function salesOrderAfterSave(\Magento\Framework\Event\Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();
        if ($order instanceof \Magento\Framework\Model\AbstractModel) {
            /** @var $orderModel \Magento\CustomerCustomAttributes\Model\Sales\Order */
            $orderModel = $this->_orderFactory->create();
            $orderModel->saveAttributeData($order);
        }
        return $this;
    }

    /**
     * After load observer for order address
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function salesOrderAddressAfterLoad(\Magento\Framework\Event\Observer $observer)
    {
        $address = $observer->getEvent()->getAddress();
        if ($address instanceof \Magento\Framework\Model\AbstractModel) {
            /** @var $orderAddress \Magento\CustomerCustomAttributes\Model\Sales\Order\Address */
            $orderAddress = $this->_orderAddressFactory->create();
            $orderAddress->attachDataToEntities([$address]);
        }
        return $this;
    }

    /**
     * After save observer for order address
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function salesOrderAddressAfterSave(\Magento\Framework\Event\Observer $observer)
    {
        $orderAddress = $observer->getEvent()->getAddress();
        if ($orderAddress instanceof \Magento\Framework\Model\AbstractModel) {
            /** @var $orderAddressModel \Magento\CustomerCustomAttributes\Model\Sales\Order\Address */
            $orderAddressModel = $this->_orderAddressFactory->create();
            $orderAddressModel->saveAttributeData($orderAddress);
        }
        return $this;
    }

    /**
     * Before save observer for customer attribute
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     * @throws \Magento\Eav\Exception
     */
    public function enterpriseCustomerAttributeBeforeSave(\Magento\Framework\Event\Observer $observer)
    {
        $attribute = $observer->getEvent()->getAttribute();
        if ($attribute instanceof \Magento\Customer\Model\Attribute && $attribute->isObjectNew()) {
            /**
             * Check for maximum attribute_code length
             */
            $attributeCodeMaxLength = \Magento\Eav\Model\Entity\Attribute::ATTRIBUTE_CODE_MAX_LENGTH - 9;
            $validate = \Zend_Validate::is(
                $attribute->getAttributeCode(),
                'StringLength',
                ['max' => $attributeCodeMaxLength]
            );
            if (!$validate) {
                throw new \Magento\Eav\Exception(
                    __('Maximum length of attribute code must be less than %1 symbols', $attributeCodeMaxLength)
                );
            }
        }

        return $this;
    }

    /**
     * After save observer for customer attribute
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function enterpriseCustomerAttributeSave(\Magento\Framework\Event\Observer $observer)
    {
        $attribute = $observer->getEvent()->getAttribute();
        if ($attribute instanceof \Magento\Customer\Model\Attribute && $attribute->isObjectNew()) {
            /** @var $quoteModel \Magento\CustomerCustomAttributes\Model\Sales\Quote */
            $quoteModel = $this->_quoteFactory->create();
            $quoteModel->saveNewAttribute($attribute);
            /** @var $orderModel \Magento\CustomerCustomAttributes\Model\Sales\Order */
            $orderModel = $this->_orderFactory->create();
            $orderModel->saveNewAttribute($attribute);
        }
        return $this;
    }

    /**
     * After delete observer for customer attribute
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function enterpriseCustomerAttributeDelete(\Magento\Framework\Event\Observer $observer)
    {
        $attribute = $observer->getEvent()->getAttribute();
        if ($attribute instanceof \Magento\Customer\Model\Attribute && !$attribute->isObjectNew()) {
            /** @var $quoteModel \Magento\CustomerCustomAttributes\Model\Sales\Quote */
            $quoteModel = $this->_quoteFactory->create();
            $quoteModel->deleteAttribute($attribute);
            /** @var $orderModel \Magento\CustomerCustomAttributes\Model\Sales\Order */
            $orderModel = $this->_orderFactory->create();
            $orderModel->deleteAttribute($attribute);
        }
        return $this;
    }

    /**
     * After save observer for customer address attribute
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function enterpriseCustomerAddressAttributeSave(\Magento\Framework\Event\Observer $observer)
    {
        $attribute = $observer->getEvent()->getAttribute();
        if ($attribute instanceof \Magento\Customer\Model\Attribute && $attribute->isObjectNew()) {
            /** @var $quoteAddress \Magento\CustomerCustomAttributes\Model\Sales\Quote\Address */
            $quoteAddress = $this->_quoteAddressFactory->create();
            $quoteAddress->saveNewAttribute($attribute);
            /** @var $orderAddress \Magento\CustomerCustomAttributes\Model\Sales\Order\Address */
            $orderAddress = $this->_orderAddressFactory->create();
            $orderAddress->saveNewAttribute($attribute);
        }
        return $this;
    }

    /**
     * After delete observer for customer address attribute
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function enterpriseCustomerAddressAttributeDelete(\Magento\Framework\Event\Observer $observer)
    {
        $attribute = $observer->getEvent()->getAttribute();
        if ($attribute instanceof \Magento\Customer\Model\Attribute && !$attribute->isObjectNew()) {
            /** @var $quoteAddress \Magento\CustomerCustomAttributes\Model\Sales\Quote\Address */
            $quoteAddress = $this->_quoteAddressFactory->create();
            $quoteAddress->deleteAttribute($attribute);
            /** @var $orderAddress \Magento\CustomerCustomAttributes\Model\Sales\Order\Address */
            $orderAddress = $this->_orderAddressFactory->create();
            $orderAddress->deleteAttribute($attribute);
        }
        return $this;
    }

    /**
     * Observer for converting quote to order
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function coreCopyFieldsetSalesConvertQuoteToOrder(\Magento\Framework\Event\Observer $observer)
    {
        $this->_copyFieldset($observer, self::CONVERT_ALGORITM_SOURCE_TARGET_WITH_PREFIX, self::CONVERT_TYPE_CUSTOMER);

        return $this;
    }

    /**
     * Observer for converting quote address to order address
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function coreCopyFieldsetSalesConvertQuoteAddressToOrderAddress(\Magento\Framework\Event\Observer $observer)
    {
        $this->_copyFieldset(
            $observer,
            self::CONVERT_ALGORITM_SOURCE_TARGET_WITH_PREFIX,
            self::CONVERT_TYPE_CUSTOMER_ADDRESS
        );

        return $this;
    }

    /**
     * Observer for converting order to quote
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function coreCopyFieldsetSalesCopyOrderToEdit(\Magento\Framework\Event\Observer $observer)
    {
        $this->_copyFieldset($observer, self::CONVERT_ALGORITM_SOURCE_TARGET_WITH_PREFIX, self::CONVERT_TYPE_CUSTOMER);

        return $this;
    }

    /**
     * Observer for converting order billing address to quote billing address
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function coreCopyFieldsetSalesCopyOrderBillingAddressToOrder(\Magento\Framework\Event\Observer $observer)
    {
        $this->_copyFieldset(
            $observer,
            self::CONVERT_ALGORITM_SOURCE_TARGET_WITH_PREFIX,
            self::CONVERT_TYPE_CUSTOMER_ADDRESS
        );

        return $this;
    }

    /**
     * Observer for converting order shipping address to quote shipping address
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function coreCopyFieldsetSalesCopyOrderShippingAddressToOrder(\Magento\Framework\Event\Observer $observer)
    {
        $this->_copyFieldset(
            $observer,
            self::CONVERT_ALGORITM_SOURCE_TARGET_WITH_PREFIX,
            self::CONVERT_TYPE_CUSTOMER_ADDRESS
        );

        return $this;
    }

    /**
     * Observer for converting customer to quote
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function coreCopyFieldsetCustomerAccountToQuote(\Magento\Framework\Event\Observer $observer)
    {
        $this->_copyFieldset($observer, self::CONVERT_ALGORITM_SOURCE_WITHOUT_PREFIX, self::CONVERT_TYPE_CUSTOMER);

        return $this;
    }

    /**
     * Observer for converting customer address to quote address
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function coreCopyFieldsetCustomerAddressToQuoteAddress(\Magento\Framework\Event\Observer $observer)
    {
        $this->_copyFieldset(
            $observer,
            self::CONVERT_ALGORITM_SOURCE_WITHOUT_PREFIX,
            self::CONVERT_TYPE_CUSTOMER_ADDRESS
        );

        return $this;
    }

    /**
     * Observer for converting quote address to customer address
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function coreCopyFieldsetQuoteAddressToCustomerAddress(\Magento\Framework\Event\Observer $observer)
    {
        $this->_copyFieldset(
            $observer,
            self::CONVERT_ALGORITM_SOURCE_WITHOUT_PREFIX,
            self::CONVERT_TYPE_CUSTOMER_ADDRESS
        );

        return $this;
    }

    /**
     * Observer for converting quote to customer
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function coreCopyFieldsetCheckoutOnepageQuoteToCustomer(\Magento\Framework\Event\Observer $observer)
    {
        $this->_copyFieldset($observer, self::CONVERT_ALGORITM_TARGET_WITHOUT_PREFIX, self::CONVERT_TYPE_CUSTOMER);

        return $this;
    }

    /**
     * CopyFieldset converts customer attributes from source object to target object
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @param int $algoritm
     * @param int $convertType
     * @return $this
     */
    protected function _copyFieldset(
        \Magento\Framework\Event\Observer $observer,
        $algoritm = self::CONVERT_ALGORITM_TARGET_WITHOUT_PREFIX,
        $convertType = self::CONVERT_TYPE_CUSTOMER
    ) {
        $source = $observer->getEvent()->getSource();
        $target = $observer->getEvent()->getTarget();

        if ($source instanceof \Magento\Framework\Model\AbstractModel &&
            $target instanceof \Magento\Framework\Model\AbstractModel
        ) {
            if ($convertType == self::CONVERT_TYPE_CUSTOMER_ADDRESS) {
                $attributes = $this->_customerData->getCustomerAddressUserDefinedAttributeCodes();
                $prefix = '';
            } else {
                $attributes = $this->_customerData->getCustomerUserDefinedAttributeCodes();
                $prefix = 'customer_';
            }

            foreach ($attributes as $attribute) {
                switch ($algoritm) {
                    case self::CONVERT_ALGORITM_SOURCE_TARGET_WITH_PREFIX:
                        $sourceAttribute = $prefix . $attribute;
                        $targetAttribute = $prefix . $attribute;
                        break;
                    case self::CONVERT_ALGORITM_SOURCE_WITHOUT_PREFIX:
                        $sourceAttribute = $attribute;
                        $targetAttribute = $prefix . $attribute;
                        break;
                    case self::CONVERT_ALGORITM_TARGET_WITHOUT_PREFIX:
                    default:
                        $sourceAttribute = $prefix . $attribute;
                        $targetAttribute = $attribute;
                        break;
                }
                $target->setData($targetAttribute, $source->getData($sourceAttribute));
            }
        }

        return $this;
    }
}
