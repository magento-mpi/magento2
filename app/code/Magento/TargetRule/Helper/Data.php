<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_TargetRule
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * TargetRule data helper
 *
 * @category   Magento
 * @package    Magento_TargetRule
 */
class Magento_TargetRule_Helper_Data extends Magento_Core_Helper_Abstract
{
    const XML_PATH_TARGETRULE_CONFIG    = 'catalog/magento_targetrule/';

    const MAX_PRODUCT_LIST_RESULT       = 20;

    /**
     * Core store config
     *
     * @var Magento_Core_Model_Store_Config
     */
    protected $_coreStoreConfig;

    /**
     * @param Magento_Core_Helper_Context $context
     * @param Magento_Core_Model_Store_Config $coreStoreConfig
     */
    public function __construct(
        Magento_Core_Helper_Context $context,
        Magento_Core_Model_Store_Config $coreStoreConfig
    ) {
        $this->_coreStoreConfig = $coreStoreConfig;
        parent::__construct($context);
    }

    /**
     * Retrieve Maximum Number of Products in Product List
     *
     * @param int $type product list type
     * @throws Magento_Core_Exception
     * @return int
     */
    public function getMaximumNumberOfProduct($type)
    {
        switch ($type) {
            case Magento_TargetRule_Model_Rule::RELATED_PRODUCTS:
                $number = $this->_coreStoreConfig->getConfig(self::XML_PATH_TARGETRULE_CONFIG . 'related_position_limit');
                break;
            case Magento_TargetRule_Model_Rule::UP_SELLS:
                $number = $this->_coreStoreConfig->getConfig(self::XML_PATH_TARGETRULE_CONFIG . 'upsell_position_limit');
                break;
            case Magento_TargetRule_Model_Rule::CROSS_SELLS:
                $number = $this->_coreStoreConfig->getConfig(self::XML_PATH_TARGETRULE_CONFIG . 'crosssell_position_limit');
                break;
            default:
                Mage::throwException(__('Invalid product list type'));
        }

        return $this->getMaxProductsListResult($number);
    }

    /**
     * Show Related/Upsell/Cross-Sell Products behavior
     *
     * @param int $type
     * @throws Magento_Core_Exception
     * @return int
     */
    public function getShowProducts($type)
    {
        switch ($type) {
            case Magento_TargetRule_Model_Rule::RELATED_PRODUCTS:
                $show = $this->_coreStoreConfig->getConfig(self::XML_PATH_TARGETRULE_CONFIG . 'related_position_behavior');
                break;
            case Magento_TargetRule_Model_Rule::UP_SELLS:
                $show = $this->_coreStoreConfig->getConfig(self::XML_PATH_TARGETRULE_CONFIG . 'upsell_position_behavior');
                break;
            case Magento_TargetRule_Model_Rule::CROSS_SELLS:
                $show = $this->_coreStoreConfig->getConfig(self::XML_PATH_TARGETRULE_CONFIG . 'crosssell_position_behavior');
                break;
            default:
                Mage::throwException(__('Invalid product list type'));
        }

        return $show;
    }

    /**
     * Retrieve maximum number of products can be displayed in product list
     *
     * if number is 0 (unlimited) or great global maximum return global maximum value
     *
     * @param int $number
     * @return int
     */
    public function getMaxProductsListResult($number = 0)
    {
        if ($number == 0 || $number > self::MAX_PRODUCT_LIST_RESULT) {
            $number = self::MAX_PRODUCT_LIST_RESULT;
        }

        return $number;
    }

    /**
     * Retrieve Rotation Mode in Product List
     *
     * @param int $type product list type
     * @throws Magento_Core_Exception
     * @return int
     */
    public function getRotationMode($type)
    {
        switch ($type) {
            case Magento_TargetRule_Model_Rule::RELATED_PRODUCTS:
                $mode = $this->_coreStoreConfig->getConfig(self::XML_PATH_TARGETRULE_CONFIG . 'related_rotation_mode');
                break;
            case Magento_TargetRule_Model_Rule::UP_SELLS:
                $mode = $this->_coreStoreConfig->getConfig(self::XML_PATH_TARGETRULE_CONFIG . 'upsell_rotation_mode');
                break;
            case Magento_TargetRule_Model_Rule::CROSS_SELLS:
                $mode = $this->_coreStoreConfig->getConfig(self::XML_PATH_TARGETRULE_CONFIG . 'crosssell_rotation_mode');
                break;
            default:
                Mage::throwException(__('Invalid rotation mode type'));
        }
        return $mode;
    }
}
