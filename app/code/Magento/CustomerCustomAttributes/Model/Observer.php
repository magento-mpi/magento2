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
 *
 */
class Magento_CustomerCustomAttributes_Model_Observer
{
    const CONVERT_ALGORITM_SOURCE_TARGET_WITH_PREFIX = 1;
    const CONVERT_ALGORITM_SOURCE_WITHOUT_PREFIX     = 2;
    const CONVERT_ALGORITM_TARGET_WITHOUT_PREFIX     = 3;

    const CONVERT_TYPE_CUSTOMER             = 'customer';
    const CONVERT_TYPE_CUSTOMER_ADDRESS     = 'customer_address';

    /**
     * Customer data
     *
     * @var Magento_CustomerCustomAttributes_Helper_Data
     */
    protected $_customerData = null;

    /**
     * @param Magento_CustomerCustomAttributes_Helper_Data $customerData
     */
    public function __construct(
        Magento_CustomerCustomAttributes_Helper_Data $customerData
    ) {
        $this->_customerData = $customerData;
    }

    /**
     * After load observer for quote
     *
     * @param Magento_Event_Observer $observer
     * @return Magento_CustomerCustomAttributes_Model_Observer
     */
    public function salesQuoteAfterLoad(Magento_Event_Observer $observer)
    {
        $quote = $observer->getEvent()->getQuote();
        if ($quote instanceof Magento_Core_Model_Abstract) {
            Mage::getModel('Magento_CustomerCustomAttributes_Model_Sales_Quote')
                ->load($quote->getId())
                ->attachAttributeData($quote);
        }

        return $this;
    }

    /**
     * After load observer for collection of quote address
     *
     * @param Magento_Event_Observer $observer
     * @return Magento_CustomerCustomAttributes_Model_Observer
     */
    public function salesQuoteAddressCollectionAfterLoad(Magento_Event_Observer $observer)
    {
        $collection = $observer->getEvent()->getQuoteAddressCollection();
        if ($collection instanceof Magento_Data_Collection_Db) {
            Mage::getModel('Magento_CustomerCustomAttributes_Model_Sales_Quote_Address')
                ->attachDataToEntities($collection->getItems());
        }

        return $this;
    }

    /**
     * After save observer for quote
     *
     * @param Magento_Event_Observer $observer
     * @return Magento_CustomerCustomAttributes_Model_Observer
     */
    public function salesQuoteAfterSave(Magento_Event_Observer $observer)
    {
        $quote = $observer->getEvent()->getQuote();
        if ($quote instanceof Magento_Core_Model_Abstract) {
            Mage::getModel('Magento_CustomerCustomAttributes_Model_Sales_Quote')
                ->saveAttributeData($quote);
        }

        return $this;
    }

    /**
     * After save observer for quote address
     *
     * @param Magento_Event_Observer $observer
     * @return Magento_CustomerCustomAttributes_Model_Observer
     */
    public function salesQuoteAddressAfterSave(Magento_Event_Observer $observer)
    {
        $quoteAddress = $observer->getEvent()->getQuoteAddress();
        if ($quoteAddress instanceof Magento_Core_Model_Abstract) {
            Mage::getModel('Magento_CustomerCustomAttributes_Model_Sales_Quote_Address')
                ->saveAttributeData($quoteAddress);
        }

        return $this;
    }

    /**
     * After load observer for order
     *
     * @param Magento_Event_Observer $observer
     * @return Magento_CustomerCustomAttributes_Model_Observer
     */
    public function salesOrderAfterLoad(Magento_Event_Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();
        if ($order instanceof Magento_Core_Model_Abstract) {
            Mage::getModel('Magento_CustomerCustomAttributes_Model_Sales_Order')
                ->load($order->getId())
                ->attachAttributeData($order);
        }

        return $this;
    }

    /**
     * After load observer for collection of order address
     *
     * @param Magento_Event_Observer $observer
     * @return Magento_CustomerCustomAttributes_Model_Observer
     */
    public function salesOrderAddressCollectionAfterLoad(Magento_Event_Observer $observer)
    {
        $collection = $observer->getEvent()->getOrderAddressCollection();
        if ($collection instanceof Magento_Data_Collection_Db) {
            Mage::getModel('Magento_CustomerCustomAttributes_Model_Sales_Order_Address')
                ->attachDataToEntities($collection->getItems());
        }

        return $this;
    }

    /**
     * After save observer for order
     *
     * @param Magento_Event_Observer $observer
     * @return Magento_CustomerCustomAttributes_Model_Observer
     */
    public function salesOrderAfterSave(Magento_Event_Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();
        if ($order instanceof Magento_Core_Model_Abstract) {
            Mage::getModel('Magento_CustomerCustomAttributes_Model_Sales_Order')
                ->saveAttributeData($order);
        }

        return $this;
    }

    /**
     * After load observer for order address
     *
     * @param Magento_Event_Observer $observer
     * @return Magento_CustomerCustomAttributes_Model_Observer
     */
    public function salesOrderAddressAfterLoad(Magento_Event_Observer $observer)
    {
        $address = $observer->getEvent()->getAddress();
        if ($address instanceof Magento_Core_Model_Abstract) {
            Mage::getModel('Magento_CustomerCustomAttributes_Model_Sales_Order_Address')
                ->attachDataToEntities(array($address));
        }

        return $this;
    }

    /**
     * After save observer for order address
     *
     * @param Magento_Event_Observer $observer
     * @return Magento_CustomerCustomAttributes_Model_Observer
     */
    public function salesOrderAddressAfterSave(Magento_Event_Observer $observer)
    {
        $orderAddress = $observer->getEvent()->getAddress();
        if ($orderAddress instanceof Magento_Core_Model_Abstract) {
            Mage::getModel('Magento_CustomerCustomAttributes_Model_Sales_Order_Address')
                ->saveAttributeData($orderAddress);
        }

        return $this;
    }

    /**
     * Before save observer for customer attribute
     *
     * @param Magento_Event_Observer $observer
     * @return Magento_CustomerCustomAttributes_Model_Observer
     */
    public function enterpriseCustomerAttributeBeforeSave(Magento_Event_Observer $observer)
    {
        $attribute = $observer->getEvent()->getAttribute();
        if ($attribute instanceof Magento_Customer_Model_Attribute && $attribute->isObjectNew()) {
            /**
             * Check for maximum attribute_code length
             */
            $attributeCodeMaxLength = Magento_Eav_Model_Entity_Attribute::ATTRIBUTE_CODE_MAX_LENGTH - 9;
            $validate = Zend_Validate::is($attribute->getAttributeCode(), 'StringLength', array(
                'max' => $attributeCodeMaxLength
            ));
            if (!$validate) {
                throw Mage::exception('Magento_Eav',
                    __('Maximum length of attribute code must be less than %1 symbols', $attributeCodeMaxLength)
                );
            }
        }

        return $this;
    }

    /**
     * After save observer for customer attribute
     *
     * @param Magento_Event_Observer $observer
     * @return Magento_CustomerCustomAttributes_Model_Observer
     */
    public function enterpriseCustomerAttributeSave(Magento_Event_Observer $observer)
    {
        $attribute = $observer->getEvent()->getAttribute();
        if ($attribute instanceof Magento_Customer_Model_Attribute && $attribute->isObjectNew()) {
            Mage::getModel('Magento_CustomerCustomAttributes_Model_Sales_Quote')
                ->saveNewAttribute($attribute);
            Mage::getModel('Magento_CustomerCustomAttributes_Model_Sales_Order')
                ->saveNewAttribute($attribute);
        }

        return $this;
    }

    /**
     * After delete observer for customer attribute
     *
     * @param Magento_Event_Observer $observer
     * @return Magento_CustomerCustomAttributes_Model_Observer
     */
    public function enterpriseCustomerAttributeDelete(Magento_Event_Observer $observer)
    {
        $attribute = $observer->getEvent()->getAttribute();
        if ($attribute instanceof Magento_Customer_Model_Attribute && !$attribute->isObjectNew()) {
            Mage::getModel('Magento_CustomerCustomAttributes_Model_Sales_Quote')
                ->deleteAttribute($attribute);
            Mage::getModel('Magento_CustomerCustomAttributes_Model_Sales_Order')
                ->deleteAttribute($attribute);
        }

        return $this;
    }

    /**
     * After save observer for customer address attribute
     *
     * @param Magento_Event_Observer $observer
     * @return Magento_CustomerCustomAttributes_Model_Observer
     */
    public function enterpriseCustomerAddressAttributeSave(Magento_Event_Observer $observer)
    {
        $attribute = $observer->getEvent()->getAttribute();
        if ($attribute instanceof Magento_Customer_Model_Attribute && $attribute->isObjectNew()) {
            Mage::getModel('Magento_CustomerCustomAttributes_Model_Sales_Quote_Address')
                ->saveNewAttribute($attribute);
            Mage::getModel('Magento_CustomerCustomAttributes_Model_Sales_Order_Address')
                ->saveNewAttribute($attribute);
        }

        return $this;
    }

    /**
     * After delete observer for customer address attribute
     *
     * @param Magento_Event_Observer $observer
     * @return Magento_CustomerCustomAttributes_Model_Observer
     */
    public function enterpriseCustomerAddressAttributeDelete(Magento_Event_Observer $observer)
    {
        $attribute = $observer->getEvent()->getAttribute();
        if ($attribute instanceof Magento_Customer_Model_Attribute && !$attribute->isObjectNew()) {
            Mage::getModel('Magento_CustomerCustomAttributes_Model_Sales_Quote_Address')
                ->deleteAttribute($attribute);
            Mage::getModel('Magento_CustomerCustomAttributes_Model_Sales_Order_Address')
                ->deleteAttribute($attribute);
        }

        return $this;
    }

    /**
     * Observer for converting quote to order
     *
     * @param Magento_Event_Observer $observer
     * @return Magento_CustomerCustomAttributes_Model_Observer
     */
    public function coreCopyFieldsetSalesConvertQuoteToOrder(Magento_Event_Observer $observer)
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
     * @param Magento_Event_Observer $observer
     * @return Magento_CustomerCustomAttributes_Model_Observer
     */
    public function coreCopyFieldsetSalesConvertQuoteAddressToOrderAddress(Magento_Event_Observer $observer)
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
     * @param Magento_Event_Observer $observer
     * @return Magento_CustomerCustomAttributes_Model_Observer
     */
    public function coreCopyFieldsetSalesCopyOrderToEdit(Magento_Event_Observer $observer)
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
     * @param Magento_Event_Observer $observer
     * @return Magento_CustomerCustomAttributes_Model_Observer
     */
    public function coreCopyFieldsetSalesCopyOrderBillingAddressToOrder(Magento_Event_Observer $observer)
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
     * @param Magento_Event_Observer $observer
     * @return Magento_CustomerCustomAttributes_Model_Observer
     */
    public function coreCopyFieldsetSalesCopyOrderShippingAddressToOrder(Magento_Event_Observer $observer)
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
     * @param Magento_Event_Observer $observer
     * @return Magento_CustomerCustomAttributes_Model_Observer
     */
    public function coreCopyFieldsetCustomerAccountToQuote(Magento_Event_Observer $observer)
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
     * @param Magento_Event_Observer $observer
     * @return Magento_CustomerCustomAttributes_Model_Observer
     */
    public function coreCopyFieldsetCustomerAddressToQuoteAddress(Magento_Event_Observer $observer)
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
     * @param Magento_Event_Observer $observer
     * @return Magento_CustomerCustomAttributes_Model_Observer
     */
    public function coreCopyFieldsetQuoteAddressToCustomerAddress(Magento_Event_Observer $observer)
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
     * @param Magento_Event_Observer $observer
     * @return Magento_CustomerCustomAttributes_Model_Observer
     */
    public function coreCopyFieldsetCheckoutOnepageQuoteToCustomer(Magento_Event_Observer $observer)
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
     * @param Magento_Event_Observer $observer
     * @param int $algoritm
     * @param int $convertType
     * @return Magento_CustomerCustomAttributes_Model_Observer
     */
    protected function _copyFieldset(Magento_Event_Observer $observer, $algoritm, $convertType)
    {
        $source = $observer->getEvent()->getSource();
        $target = $observer->getEvent()->getTarget();

        if ($source instanceof Magento_Core_Model_Abstract && $target instanceof Magento_Core_Model_Abstract) {
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
