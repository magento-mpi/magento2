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
 * SalesRule Mass Coupon Generator
 *
 * @method Magento_SalesRule_Model_Resource_Coupon getResource()
 *
 * @category    Magento
 * @package     Magento_SalesRule
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_SalesRule_Model_Coupon_Massgenerator extends Magento_Core_Model_Abstract
    implements Magento_SalesRule_Model_Coupon_CodegeneratorInterface
{
    /**
     * Maximum probability of guessing the coupon on the first attempt
     */
    const MAX_PROBABILITY_OF_GUESSING = 0.25;
    const MAX_GENERATE_ATTEMPTS = 10;

    /**
     * Count of generated Coupons
     * @var int
     */
    protected $_generatedCount = 0;

    /**
     * Sales rule coupon
     *
     * @var Magento_SalesRule_Helper_Coupon
     */
    protected $_salesRuleCoupon = null;

    /**
     * @param Magento_SalesRule_Helper_Coupon $salesRuleCoupon
     * @param Magento_Core_Model_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_Core_Model_Resource_Abstract $resource
     * @param Magento_Data_Collection_Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        Magento_SalesRule_Helper_Coupon $salesRuleCoupon,
        Magento_Core_Model_Context $context,
        Magento_Core_Model_Registry $registry,
        Magento_Core_Model_Resource_Abstract $resource = null,
        Magento_Data_Collection_Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_salesRuleCoupon = $salesRuleCoupon;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Initialize resource
     */
    protected function _construct()
    {
        $this->_init('Magento_SalesRule_Model_Resource_Coupon');
    }

    /**
     * Generate coupon code
     *
     * @return string
     */
    public function generateCode()
    {
        $format  = $this->getFormat();
        if (!$format) {
            $format = Magento_SalesRule_Helper_Coupon::COUPON_FORMAT_ALPHANUMERIC;
        }
        $length  = max(1, (int) $this->getLength());
        $split   = max(0, (int) $this->getDash());
        $suffix  = $this->getSuffix();
        $prefix  = $this->getPrefix();

        $splitChar = $this->getDelimiter();
        $charset = $this->_salesRuleCoupon->getCharset($format);

        $code = '';
        $charsetSize = count($charset);
        for ($i=0; $i<$length; $i++) {
            $char = $charset[mt_rand(0, $charsetSize - 1)];
            if ($split > 0 && ($i % $split) == 0 && $i != 0) {
                $char = $splitChar . $char;
            }
            $code .= $char;
        }

        $code = $prefix . $code . $suffix;
        return $code;
    }

    /**
     * Retrieve delimiter
     *
     * @return string
     */
    public function getDelimiter()
    {
        if ($this->getData('delimiter')) {
            return $this->getData('delimiter');
        } else {
            return $this->_salesRuleCoupon->getCodeSeparator();
        }
    }

    /**
     * Generate Coupons Pool
     *
     * @return Magento_SalesRule_Model_Coupon_Massgenerator
     */
    public function generatePool()
    {
        $this->_generatedCount = 0;
        $size = $this->getQty();

        $maxProbability = $this->getMaxProbability() ? $this->getMaxProbability() : self::MAX_PROBABILITY_OF_GUESSING;
        $maxAttempts = $this->getMaxAttempts() ? $this->getMaxAttempts() : self::MAX_GENERATE_ATTEMPTS;

        /** @var $coupon Magento_SalesRule_Model_Coupon */
        $coupon = Mage::getModel('Magento_SalesRule_Model_Coupon');

        $chars = count($this->_salesRuleCoupon->getCharset($this->getFormat()));
        $length = (int) $this->getLength();
        $maxCodes = pow($chars, $length);
        $probability = $size / $maxCodes;
        //increase the length of Code if probability is low
        if ($probability > $maxProbability) {
            do {
                $length++;
                $maxCodes = pow($chars, $length);
                $probability = $size / $maxCodes;
            } while ($probability > $maxProbability);
            $this->setLength($length);
        }

        $now = $this->getResource()->formatDate(
            Mage::getSingleton('Magento_Core_Model_Date')->gmtTimestamp()
        );

        for ($i = 0; $i < $size; $i++) {
            $attempt = 0;
            do {
                if ($attempt >= $maxAttempts) {
                    Mage::throwException(__('We cannot create the requested Coupon Qty. Please check your settings and try again.'));
                }
                $code = $this->generateCode();
                $attempt++;
            } while ($this->getResource()->exists($code));

            $expirationDate = $this->getToDate();
            if ($expirationDate instanceof Zend_Date) {
                $expirationDate = $expirationDate->toString(Magento_Date::DATETIME_INTERNAL_FORMAT);
            }

            $coupon->setId(null)
                ->setRuleId($this->getRuleId())
                ->setUsageLimit($this->getUsesPerCoupon())
                ->setUsagePerCustomer($this->getUsesPerCustomer())
                ->setExpirationDate($expirationDate)
                ->setCreatedAt($now)
                ->setType(Magento_SalesRule_Helper_Coupon::COUPON_TYPE_SPECIFIC_AUTOGENERATED)
                ->setCode($code)
                ->save();

            $this->_generatedCount++;
        }
        return $this;
    }

    /**
     * Validate input
     *
     * @param array $data
     * @return bool
     */
    public function validateData($data)
    {
        return !empty($data) && !empty($data['qty']) && !empty($data['rule_id'])
            && !empty($data['length']) && !empty($data['format'])
            && (int)$data['qty'] > 0 && (int) $data['rule_id'] > 0
            && (int) $data['length'] > 0;
    }

    /**
     * Retrieve count of generated Coupons
     *
     * @return int
     */
    public function getGeneratedCount()
    {
        return $this->_generatedCount;
    }
}
