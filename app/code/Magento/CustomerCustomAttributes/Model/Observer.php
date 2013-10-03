<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CustomerCustomAttributes
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Customer observer
 */
namespace Magento\CustomerCustomAttributes\Model;

class Observer
{
    const CONVERT_ALGORITM_SOURCE_TARGET_WITH_PREFIX = 1;
    const CONVERT_ALGORITM_SOURCE_WITHOUT_PREFIX     = 2;
    const CONVERT_ALGORITM_TARGET_WITHOUT_PREFIX     = 3;

    const CONVERT_TYPE_CUSTOMER             = 'customer';
    const CONVERT_TYPE_CUSTOMER_ADDRESS     = 'customer_address';

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
     * @param \Magento\Event\Observer $observer
     * @return \Magento\CustomerCustomAttributes\Model\Observer
     */
    public function salesQuoteAfterLoad(\Magento\Event\Observer $observer)
    {
        $quote = $observer->getEvent()->getQuote();
        if ($quote instanceof \Magento\Core\Model\AbstractModel) {
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
     * @param \Magento\Event\Observer $observer
     * @return \Magento\CustomerCustomAttributes\Model\Observer
     */
    public function salesQuoteAddressCollectionAfterLoad(\Magento\Event\Observer $observer)
    {
        $collection = $observer->getEvent()->getQuoteAddressCollection();
        if ($collection instanceof \Magento\Data\Collection\Db) {
            /** @var $quoteAddress \Magento\CustomerCustomAttributes\Model\Sales\Quote\Address */
            $quoteAddress = $this->_quoteAddressFactory->create();
            $quoteAddress->attachDataToEntities($collection->getItems());
        }
        return $this;
    }

    /**
     * After save observer for quote
     *
     * @param \Magento\Event\Observer $observer
     * @return \Magento\CustomerCustomAttributes\Model\Observer
     */
    public function salesQuoteAfterSave(\Magento\Event\Observer $observer)
    {
        $quote = $observer->getEvent()->getQuote();
        if ($quote instanceof \Magento\Core\Model\AbstractModel) {
            /** @var $quoteModel \Magento\CustomerCustomAttributes\Model\Sales\Quote */
            $quoteModel = $this->_quoteFactory->create();
            $quoteModel->saveAttributeData($quote);
        }
        return $this;
    }

    /**
     * After save observer for quote address
     *
     * @param \Magento\Event\Observer $observer
     * @return \Magento\CustomerCustomAttributes\Model\Observer
     */
    public function salesQuoteAddressAfterSave(\Magento\Event\Observer $observer)
    {
        $quoteAddress = $observer->getEvent()->getQuoteAddress();
        if ($quoteAddress instanceof \Magento\Core\Model\AbstractModel) {
            /** @var $quoteAddressModel \Magento\CustomerCustomAttributes\Model\Sales\Quote\Address */
            $quoteAddressModel = $this->_quoteAddressFactory->create();
            $quoteAddressModel->saveAttributeData($quoteAddress);
        }
        return $this;
    }

    /**
     * After load observer for order
     *
     * @param \Magento\Event\Observer $observer
     * @return \Magento\CustomerCustomAttributes\Model\Observer
     */
    public function salesOrderAfterLoad(\Magento\Event\Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();
        if ($order instanceof \Magento\Core\Model\AbstractModel) {
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
     * @param \Magento\Event\Observer $observer
     * @return \Magento\CustomerCustomAttributes\Model\Observer
     */
    public function salesOrderAddressCollectionAfterLoad(\Magento\Event\Observer $observer)
    {
        $collection = $observer->getEvent()->getOrderAddressCollection();
        if ($collection instanceof \Magento\Data\Collection\Db) {
            /** @var $orderAddress \Magento\CustomerCustomAttributes\Model\Sales\Order\Address */
            $orderAddress = $this->_orderAddressFactory->create();
            $orderAddress->attachDataToEntities($collection->getItems());
        }
        return $this;
    }

    /**
     * After save observer for order
     *
     * @param \Magento\Event\Observer $observer
     * @return \Magento\CustomerCustomAttributes\Model\Observer
     */
    public function salesOrderAfterSave(\Magento\Event\Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();
        if ($order instanceof \Magento\Core\Model\AbstractModel) {
            /** @var $orderModel \Magento\CustomerCustomAttributes\Model\Sales\Order */
            $orderModel = $this->_orderFactory->create();
            $orderModel->saveAttributeData($order);
        }
        return $this;
    }

    /**
     * After load observer for order address
     *
     * @param \Magento\Event\Observer $observer
     * @return \Magento\CustomerCustomAttributes\Model\Observer
     */
    public function salesOrderAddressAfterLoad(\Magento\Event\Observer $observer)
    {
        $address = $observer->getEvent()->getAddress();
        if ($address instanceof \Magento\Core\Model\AbstractModel) {
            /** @var $orderAddress \Magento\CustomerCustomAttributes\Model\Sales\Order\Address */
            $orderAddress = $this->_orderAddressFactory->create();
            $orderAddress->attachDataToEntities(array($address));
        }
        return $this;
    }

    /**
     * After save observer for order address
     *
     * @param \Magento\Event\Observer $observer
     * @return \Magento\CustomerCustomAttributes\Model\Observer
     */
    public function salesOrderAddressAfterSave(\Magento\Event\Observer $observer)
    {
        $orderAddress = $observer->getEvent()->getAddress();
        if ($orderAddress instanceof \Magento\Core\Model\AbstractModel) {
            /** @var $orderAddressModel \Magento\CustomerCustomAttributes\Model\Sales\Order\Address */
            $orderAddressModel = $this->_orderAddressFactory->create();
            $orderAddressModel->saveAttributeData($orderAddress);
        }
        return $this;
    }

    /**
     * Before save observer for customer attribute
     *
     * @param \Magento\Event\Observer $observer
     * @return \Magento\CustomerCustomAttributes\Model\Observer
     * @throws \Magento\Eav\Exception
     */
    public function enterpriseCustomerAttributeBeforeSave(\Magento\Event\Observer $observer)
    {
        $attribute = $observer->getEvent()->getAttribute();
        if ($attribute instanceof \Magento\Customer\Model\Attribute && $attribute->isObjectNew()) {
            /**
             * Check for maximum attribute_code length
             */
            $attributeCodeMaxLength = \Magento\Eav\Model\Entity\Attribute::ATTRIBUTE_CODE_MAX_LENGTH - 9;
            $validate = \Zend_Validate::is($attribute->getAttributeCode(), 'StringLength', array(
                'max' => $attributeCodeMaxLength
            ));
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
     * @param \Magento\Event\Observer $observer
     * @return \Magento\CustomerCustomAttributes\Model\Observer
     */
    public function enterpriseCustomerAttributeSave(\Magento\Event\Observer $observer)
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
     * @param \Magento\Event\Observer $observer
     * @return \Magento\CustomerCustomAttributes\Model\Observer
     */
    public function enterpriseCustomerAttributeDelete(\Magento\Event\Observer $observer)
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
     * @param \Magento\Event\Observer $observer
     * @return \Magento\CustomerCustomAttributes\Model\Observer
     */
    public function enterpriseCustomerAddressAttributeSave(\Magento\Event\Observer $observer)
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
     * @param \Magento\Event\Observer $observer
     * @return \Magento\CustomerCustomAttributes\Model\Observer
     */
    public function enterpriseCustomerAddressAttributeDelete(\Magento\Event\Observer $observer)
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
     * @param \Magento\Event\Observer $observer
     * @return \Magento\CustomerCustomAttributes\Model\Observer
     */
    public function coreCopyFieldsetSalesConvertQuoteToOrder(\Magento\Event\Observer $observer)
    {
        $this->_copyFieldset(
            $observer,
            self::CONVERT_ALGORITM_SOURCE_TARGET_WITH_PREFIX,
            self::CONVERT_TYPE_CUSTOMER
        );

        return $this;
    }

    /**
     * Observer for converting quote address to order address
     *
     * @param \Magento\Event\Observer $observer
     * @return \Magento\CustomerCustomAttributes\Model\Observer
     */
    public function coreCopyFieldsetSalesConvertQuoteAddressToOrderAddress(\Magento\Event\Observer $observer)
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
     * @param \Magento\Event\Observer $observer
     * @return \Magento\CustomerCustomAttributes\Model\Observer
     */
    public function coreCopyFieldsetSalesCopyOrderToEdit(\Magento\Event\Observer $observer)
    {
        $this->_copyFieldset(
            $observer,
            self::CONVERT_ALGORITM_SOURCE_TARGET_WITH_PREFIX,
            self::CONVERT_TYPE_CUSTOMER
        );

        return $this;
    }

    /**
     * Observer for converting order billing address to quote billing address
     *
     * @param \Magento\Event\Observer $observer
     * @return \Magento\CustomerCustomAttributes\Model\Observer
     */
    public function coreCopyFieldsetSalesCopyOrderBillingAddressToOrder(\Magento\Event\Observer $observer)
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
     * @param \Magento\Event\Observer $observer
     * @return \Magento\CustomerCustomAttributes\Model\Observer
     */
    public function coreCopyFieldsetSalesCopyOrderShippingAddressToOrder(\Magento\Event\Observer $observer)
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
     * @param \Magento\Event\Observer $observer
     * @return \Magento\CustomerCustomAttributes\Model\Observer
     */
    public function coreCopyFieldsetCustomerAccountToQuote(\Magento\Event\Observer $observer)
    {
        $this->_copyFieldset(
            $observer,
            self::CONVERT_ALGORITM_SOURCE_WITHOUT_PREFIX,
            self::CONVERT_TYPE_CUSTOMER
        );

        return $this;
    }

    /**
     * Observer for converting customer address to quote address
     *
     * @param \Magento\Event\Observer $observer
     * @return \Magento\CustomerCustomAttributes\Model\Observer
     */
    public function coreCopyFieldsetCustomerAddressToQuoteAddress(\Magento\Event\Observer $observer)
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
     * @param \Magento\Event\Observer $observer
     * @return \Magento\CustomerCustomAttributes\Model\Observer
     */
    public function coreCopyFieldsetQuoteAddressToCustomerAddress(\Magento\Event\Observer $observer)
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
     * @param \Magento\Event\Observer $observer
     * @return \Magento\CustomerCustomAttributes\Model\Observer
     */
    public function coreCopyFieldsetCheckoutOnepageQuoteToCustomer(\Magento\Event\Observer $observer)
    {
        $this->_copyFieldset(
            $observer,
            self::CONVERT_ALGORITM_TARGET_WITHOUT_PREFIX,
            self::CONVERT_TYPE_CUSTOMER
        );

        return $this;
    }

    /**
     * CopyFieldset converts customer attributes from source object to target object
     *
     * @param \Magento\Event\Observer $observer
     * @param int $algoritm
     * @param int $convertType
     * @return \Magento\CustomerCustomAttributes\Model\Observer
     */
    protected function _copyFieldset(\Magento\Event\Observer $observer, $algoritm, $convertType)
    {
        $source = $observer->getEvent()->getSource();
        $target = $observer->getEvent()->getTarget();

        if ($source instanceof \Magento\Core\Model\AbstractModel && $target instanceof \Magento\Core\Model\AbstractModel) {
            if ($convertType == self::CONVERT_TYPE_CUSTOMER) {
                $attributes = $this->_customerData->getCustomerUserDefinedAttributeCodes();
                $prefix     = 'customer_';
            } else if ($convertType == self::CONVERT_TYPE_CUSTOMER_ADDRESS) {
                $attributes = $this->_customerData->getCustomerAddressUserDefinedAttributeCodes();
                $prefix     = '';
            } else {
                return $this;
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
                        $sourceAttribute = $prefix . $attribute;
                        $targetAttribute = $attribute;
                        break;
                    default:
                        return $this;
                }
                $target->setData($targetAttribute, $source->getData($sourceAttribute));
            }
        }

        return $this;
    }
}
