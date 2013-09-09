<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_SalesRule
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Shopping Cart Rule data model
 *
 * @method Magento_SalesRule_Model_Resource_Rule _getResource()
 * @method Magento_SalesRule_Model_Resource_Rule getResource()
 * @method string getName()
 * @method Magento_SalesRule_Model_Rule setName(string $value)
 * @method string getDescription()
 * @method Magento_SalesRule_Model_Rule setDescription(string $value)
 * @method string getFromDate()
 * @method Magento_SalesRule_Model_Rule setFromDate(string $value)
 * @method string getToDate()
 * @method Magento_SalesRule_Model_Rule setToDate(string $value)
 * @method int getUsesPerCustomer()
 * @method Magento_SalesRule_Model_Rule setUsesPerCustomer(int $value)
 * @method int getUsesPerCoupon()
 * @method Magento_SalesRule_Model_Rule setUsesPerCoupon(int $value)
 * @method Magento_SalesRule_Model_Rule setCustomerGroupIds(string $value)
 * @method int getIsActive()
 * @method Magento_SalesRule_Model_Rule setIsActive(int $value)
 * @method string getConditionsSerialized()
 * @method Magento_SalesRule_Model_Rule setConditionsSerialized(string $value)
 * @method string getActionsSerialized()
 * @method Magento_SalesRule_Model_Rule setActionsSerialized(string $value)
 * @method int getStopRulesProcessing()
 * @method Magento_SalesRule_Model_Rule setStopRulesProcessing(int $value)
 * @method int getIsAdvanced()
 * @method Magento_SalesRule_Model_Rule setIsAdvanced(int $value)
 * @method string getProductIds()
 * @method Magento_SalesRule_Model_Rule setProductIds(string $value)
 * @method int getSortOrder()
 * @method Magento_SalesRule_Model_Rule setSortOrder(int $value)
 * @method string getSimpleAction()
 * @method Magento_SalesRule_Model_Rule setSimpleAction(string $value)
 * @method float getDiscountAmount()
 * @method Magento_SalesRule_Model_Rule setDiscountAmount(float $value)
 * @method float getDiscountQty()
 * @method Magento_SalesRule_Model_Rule setDiscountQty(float $value)
 * @method int getDiscountStep()
 * @method Magento_SalesRule_Model_Rule setDiscountStep(int $value)
 * @method int getSimpleFreeShipping()
 * @method Magento_SalesRule_Model_Rule setSimpleFreeShipping(int $value)
 * @method int getApplyToShipping()
 * @method Magento_SalesRule_Model_Rule setApplyToShipping(int $value)
 * @method int getTimesUsed()
 * @method Magento_SalesRule_Model_Rule setTimesUsed(int $value)
 * @method int getIsRss()
 * @method Magento_SalesRule_Model_Rule setIsRss(int $value)
 * @method string getWebsiteIds()
 * @method Magento_SalesRule_Model_Rule setWebsiteIds(string $value)
 * @method int getCouponType()
 * @method Magento_SalesRule_Model_Rule setCouponType(int $value)
 * @method int getUseAutoGeneration()
 * @method Magento_SalesRule_Model_Rule setUseAutoGeneration(int $value)
 * @method string getCouponCode()
 * @method Magento_SalesRule_Model_Rule setCouponCode(string $value)
 *
 * @category    Magento
 * @package     Magento_SalesRule
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_SalesRule_Model_Rule extends Magento_Rule_Model_Abstract
{
    /**
     * Free Shipping option "For matching items only"
     */
    const FREE_SHIPPING_ITEM    = 1;

    /**
     * Free Shipping option "For shipment with matching items"
     */
    const FREE_SHIPPING_ADDRESS = 2;

    /**
     * Coupon types
     */
    const COUPON_TYPE_NO_COUPON = 1;
    const COUPON_TYPE_SPECIFIC  = 2;
    const COUPON_TYPE_AUTO      = 3;

    /**
     * Rule type actions
     */
    const TO_PERCENT_ACTION = 'to_percent';
    const BY_PERCENT_ACTION = 'by_percent';
    const TO_FIXED_ACTION   = 'to_fixed';
    const BY_FIXED_ACTION   = 'by_fixed';
    const CART_FIXED_ACTION = 'cart_fixed';
    const BUY_X_GET_Y_ACTION = 'buy_x_get_y';

    /**
     * Store coupon code generator instance
     *
     * @var Magento_SalesRule_Model_Coupon_CodegeneratorInterface
     */
    protected static $_couponCodeGenerator;

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'salesrule_rule';

    /**
     * Parameter name in event
     *
     * In observe method you can use $observer->getEvent()->getRule() in this case
     *
     * @var string
     */
    protected $_eventObject = 'rule';

    /**
     * Contain sores labels
     *
     * @deprecated after 1.6.2.0
     *
     * @var array
     */
    protected $_labels = array();

    /**
     * Rule's primary coupon
     *
     * @var Magento_SalesRule_Model_Coupon
     */
    protected $_primaryCoupon;

    /**
     * Rule's subordinate coupons
     *
     * @var array of Magento_SalesRule_Model_Coupon
     */
    protected $_coupons;

    /**
     * Coupon types cache for lazy getter
     *
     * @var array
     */
    protected $_couponTypes;

    /**
     * Store already validated addresses and validation results
     *
     * @var array
     */
    protected $_validatedAddresses = array();

    /**
     * Core event manager proxy
     *
     * @var Magento_Core_Model_Event_Manager
     */
    protected $_eventManager = null;

    /**
     * @param Magento_Core_Model_Event_Manager $eventManager
     * @param Magento_Data_Form_Factory $formFactory
     * @param Magento_Core_Model_Context $context
     * @param Magento_Core_Model_Resource_Abstract $resource
     * @param Magento_Data_Collection_Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_Event_Manager $eventManager,
        Magento_Data_Form_Factory $formFactory,
        Magento_Core_Model_Context $context,
        Magento_Core_Model_Resource_Abstract $resource = null,
        Magento_Data_Collection_Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_eventManager = $eventManager;
        parent::__construct($formFactory, $context, $resource, $resourceCollection, $data);
    }

    /**
     * Set resource model and Id field name
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('Magento_SalesRule_Model_Resource_Rule');
        $this->setIdFieldName('rule_id');
    }

    /**
     * Returns code mass generator instance for auto generated specific coupons
     *
     * @return Magento_SalesRule_Model_Coupon_MassgneratorInterface
     */
    public static function getCouponMassGenerator()
    {
        return Mage::getSingleton('Magento_SalesRule_Model_Coupon_Massgenerator');
    }

    /**
     * Set coupon code and uses per coupon
     *
     * @return Magento_SalesRule_Model_Rule
     */
    protected function _afterLoad()
    {
        $this->setCouponCode($this->getPrimaryCoupon()->getCode());
        if ($this->getUsesPerCoupon() !== null && !$this->getUseAutoGeneration()) {
            $this->setUsesPerCoupon($this->getPrimaryCoupon()->getUsageLimit());
        }
        return parent::_afterLoad();
    }

    /**
     * Save/delete coupon
     *
     * @return Magento_SalesRule_Model_Rule
     */
    protected function _afterSave()
    {
        $couponCode = trim($this->getCouponCode());
        if (strlen($couponCode)
            && $this->getCouponType() == self::COUPON_TYPE_SPECIFIC
            && !$this->getUseAutoGeneration()
        ) {
            $this->getPrimaryCoupon()
                ->setCode($couponCode)
                ->setUsageLimit($this->getUsesPerCoupon() ? $this->getUsesPerCoupon() : null)
                ->setUsagePerCustomer($this->getUsesPerCustomer() ? $this->getUsesPerCustomer() : null)
                ->setExpirationDate($this->getToDate())
                ->save();
        } else {
            $this->getPrimaryCoupon()->delete();
        }

        parent::_afterSave();
        return $this;
    }

    /**
     * Initialize rule model data from array.
     * Set store labels if applicable.
     *
     * @param array $data
     *
     * @return Magento_SalesRule_Model_Rule
     */
    public function loadPost(array $data)
    {
        parent::loadPost($data);

        if (isset($data['store_labels'])) {
            $this->setStoreLabels($data['store_labels']);
        }

        return $this;
    }

    /**
     * Get rule condition combine model instance
     *
     * @return Magento_SalesRule_Model_Rule_Condition_Combine
     */
    public function getConditionsInstance()
    {
        return Mage::getModel('Magento_SalesRule_Model_Rule_Condition_Combine');
    }

    /**
     * Get rule condition product combine model instance
     *
     * @return Magento_SalesRule_Model_Rule_Condition_Product_Combine
     */
    public function getActionsInstance()
    {
        return Mage::getModel('Magento_SalesRule_Model_Rule_Condition_Product_Combine');
    }

    /**
     * Returns code generator instance for auto generated coupons
     *
     * @return Magento_SalesRule_Model_Coupon_CodegeneratorInterface
     */
    public static function getCouponCodeGenerator()
    {
        if (!self::$_couponCodeGenerator) {
            return Mage::getModel('Magento_SalesRule_Model_Coupon_Codegenerator',
                array('data' => array('length' => 16)));
        }
        return self::$_couponCodeGenerator;
    }

    /**
     * Set code generator instance for auto generated coupons
     *
     * @param Magento_SalesRule_Model_Coupon_CodegeneratorInterface
     */
    public static function setCouponCodeGenerator(Magento_SalesRule_Model_Coupon_CodegeneratorInterface $codeGenerator)
    {
        self::$_couponCodeGenerator = $codeGenerator;
    }

    /**
     * Retrieve rule's primary coupon
     *
     * @return Magento_SalesRule_Model_Coupon
     */
    public function getPrimaryCoupon()
    {
        if ($this->_primaryCoupon === null) {
            $this->_primaryCoupon = Mage::getModel('Magento_SalesRule_Model_Coupon');
            $this->_primaryCoupon->loadPrimaryByRule($this->getId());
            $this->_primaryCoupon->setRule($this)->setIsPrimary(true);
        }
        return $this->_primaryCoupon;
    }

    /**
     * Get sales rule customer group Ids
     *
     * @return array
     */
    public function getCustomerGroupIds()
    {
        if (!$this->hasCustomerGroupIds()) {
            $customerGroupIds = $this->_getResource()->getCustomerGroupIds($this->getId());
            $this->setData('customer_group_ids', (array)$customerGroupIds);
        }
        return $this->_getData('customer_group_ids');
    }

    /**
     * Get Rule label by specified store
     *
     * @param Magento_Core_Model_Store|int|bool|null $store
     *
     * @return string|bool
     */
    public function getStoreLabel($store = null)
    {
        $storeId = Mage::app()->getStore($store)->getId();
        $labels = (array)$this->getStoreLabels();

        if (isset($labels[$storeId])) {
            return $labels[$storeId];
        } elseif (isset($labels[0]) && $labels[0]) {
            return $labels[0];
        }

        return false;
    }

    /**
     * Set if not yet and retrieve rule store labels
     *
     * @return array
     */
    public function getStoreLabels()
    {
        if (!$this->hasStoreLabels()) {
            $labels = $this->_getResource()->getStoreLabels($this->getId());
            $this->setStoreLabels($labels);
        }

        return $this->_getData('store_labels');
    }

    /**
     * Retrieve subordinate coupons
     *
     * @return array of Magento_SalesRule_Model_Coupon
     */
    public function getCoupons()
    {
        if ($this->_coupons === null) {
            $collection = Mage::getResourceModel('Magento_SalesRule_Model_Resource_Coupon_Collection');
            /** @var Magento_SalesRule_Model_Resource_Coupon_Collection */
            $collection->addRuleToFilter($this);
            $this->_coupons = $collection->getItems();
        }
        return $this->_coupons;
    }

    /**
     * Retrieve coupon types
     *
     * @return array
     */
    public function getCouponTypes()
    {
        if ($this->_couponTypes === null) {
            $this->_couponTypes = array(
                Magento_SalesRule_Model_Rule::COUPON_TYPE_NO_COUPON => __('No Coupon'),
                Magento_SalesRule_Model_Rule::COUPON_TYPE_SPECIFIC  => __('Specific Coupon'),
            );
            $transport = new Magento_Object(array(
                'coupon_types'                => $this->_couponTypes,
                'is_coupon_type_auto_visible' => false
            ));
            $this->_eventManager->dispatch('salesrule_rule_get_coupon_types', array('transport' => $transport));
            $this->_couponTypes = $transport->getCouponTypes();
            if ($transport->getIsCouponTypeAutoVisible()) {
                $this->_couponTypes[Magento_SalesRule_Model_Rule::COUPON_TYPE_AUTO] = __('Auto');
            }
        }
        return $this->_couponTypes;
    }

    /**
     * Acquire coupon instance
     *
     * @param bool $saveNewlyCreated Whether or not to save newly created coupon
     * @param int $saveAttemptCount Number of attempts to save newly created coupon
     *
     * @return Magento_SalesRule_Model_Coupon|null
     */
    public function acquireCoupon($saveNewlyCreated = true, $saveAttemptCount = 10)
    {
        if ($this->getCouponType() == self::COUPON_TYPE_NO_COUPON) {
            return null;
        }
        if ($this->getCouponType() == self::COUPON_TYPE_SPECIFIC) {
            return $this->getPrimaryCoupon();
        }
        /** @var Magento_SalesRule_Model_Coupon $coupon */
        $coupon = Mage::getModel('Magento_SalesRule_Model_Coupon');
        $coupon->setRule($this)
            ->setIsPrimary(false)
            ->setUsageLimit($this->getUsesPerCoupon() ? $this->getUsesPerCoupon() : null)
            ->setUsagePerCustomer($this->getUsesPerCustomer() ? $this->getUsesPerCustomer() : null)
            ->setExpirationDate($this->getToDate());

        $couponCode = self::getCouponCodeGenerator()->generateCode();
        $coupon->setCode($couponCode);

        $ok = false;
        if (!$saveNewlyCreated) {
            $ok = true;
        } else if ($this->getId()) {
            for ($attemptNum = 0; $attemptNum < $saveAttemptCount; $attemptNum++) {
                try {
                    $coupon->save();
                } catch (Exception $e) {
                    if ($e instanceof Magento_Core_Exception || $coupon->getId()) {
                        throw $e;
                    }
                    $coupon->setCode(
                        $couponCode .
                        self::getCouponCodeGenerator()->getDelimiter() .
                        sprintf('%04u', rand(0, 9999))
                    );
                    continue;
                }
                $ok = true;
                break;
            }
        }
        if (!$ok) {
            Mage::throwException(__('Can\'t acquire coupon.'));
        }

        return $coupon;
    }

    /**
     * Check cached validation result for specific address
     *
     * @param   Magento_Sales_Model_Quote_Address $address
     * @return  bool
     */
    public function hasIsValidForAddress($address)
    {
        $addressId = $this->_getAddressId($address);
        return isset($this->_validatedAddresses[$addressId]) ? true : false;
    }

    /**
     * Set validation result for specific address to results cache
     *
     * @param   Magento_Sales_Model_Quote_Address $address
     * @param   bool $validationResult
     * @return  Magento_SalesRule_Model_Rule
     */
    public function setIsValidForAddress($address, $validationResult)
    {
        $addressId = $this->_getAddressId($address);
        $this->_validatedAddresses[$addressId] = $validationResult;
        return $this;
    }

    /**
     * Get cached validation result for specific address
     *
     * @param   Magento_Sales_Model_Quote_Address $address
     * @return  bool
     */
    public function getIsValidForAddress($address)
    {
        $addressId = $this->_getAddressId($address);
        return isset($this->_validatedAddresses[$addressId]) ? $this->_validatedAddresses[$addressId] : false;
    }

    /**
     * Return id for address
     *
     * @param   Magento_Sales_Model_Quote_Address $address
     * @return  string
     */
    private function _getAddressId($address) {
        if($address instanceof Magento_Sales_Model_Quote_Address) {
            return $address->getId();
        }
        return $address;
    }





    /**
     * Collect all product attributes used in serialized rule's action or condition
     *
     * @deprecated after 1.6.2.0 use Magento_SalesRule_Model_Resource_Rule::getProductAttributes() instead
     *
     * @param string $serializedString
     *
     * @return array
     */
    protected function _getUsedAttributes($serializedString)
    {
        return $this->_getResource()->getProductAttributes($serializedString);
    }

    /**
     * @deprecated after 1.6.2.0
     *
     * @param string $format
     *
     * @return string
     */
    public function toString($format='')
    {
        return '';
    }

    /**
     * Returns rule as an array for admin interface
     *
     * @deprecated after 1.6.2.0
     *
     * @param array $arrAttributes
     *
     * Output example:
     * array(
     *   'name'=>'Example rule',
     *   'conditions'=>{condition_combine::toArray}
     *   'actions'=>{action_collection::toArray}
     * )
     *
     * @return array
     */
    public function toArray(array $arrAttributes = array())
    {
        return parent::toArray($arrAttributes);
    }
}
