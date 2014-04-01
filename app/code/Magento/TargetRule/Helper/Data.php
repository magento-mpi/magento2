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
namespace Magento\TargetRule\Helper;

class Data extends \Magento\App\Helper\AbstractHelper
{
    const XML_PATH_TARGETRULE_CONFIG = 'catalog/magento_targetrule/';

    const MAX_PRODUCT_LIST_RESULT = 20;

    /**
     * Core store config
     *
     * @var \Magento\App\Config\ScopeConfigInterface
     */
    protected $_storeConfig;

    /**
     * @param \Magento\App\Helper\Context $context
     * @param \Magento\App\Config\ScopeConfigInterface $coreStoreConfig
     */
    public function __construct(
        \Magento\App\Helper\Context $context,
        \Magento\App\Config\ScopeConfigInterface $coreStoreConfig
    ) {
        $this->_storeConfig = $coreStoreConfig;
        parent::__construct($context);
    }

    /**
     * Retrieve Maximum Number of Products in Product List
     *
     * @param int $type product list type
     * @throws \Magento\Model\Exception
     * @return int
     */
    public function getMaximumNumberOfProduct($type)
    {
        switch ($type) {
            case \Magento\TargetRule\Model\Rule::RELATED_PRODUCTS:
                $number = $this->_storeConfig->getValue(self::XML_PATH_TARGETRULE_CONFIG . 'related_position_limit', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
                break;
            case \Magento\TargetRule\Model\Rule::UP_SELLS:
                $number = $this->_storeConfig->getValue(self::XML_PATH_TARGETRULE_CONFIG . 'upsell_position_limit', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
                break;
            case \Magento\TargetRule\Model\Rule::CROSS_SELLS:
                $number = $this->_storeConfig->getValue(self::XML_PATH_TARGETRULE_CONFIG . 'crosssell_position_limit', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
                break;
            default:
                throw new \Magento\Model\Exception(__('Invalid product list type'));
        }

        return $this->getMaxProductsListResult($number);
    }

    /**
     * Show Related/Upsell/Cross-Sell Products behavior
     *
     * @param int $type
     * @throws \Magento\Model\Exception
     * @return int
     */
    public function getShowProducts($type)
    {
        switch ($type) {
            case \Magento\TargetRule\Model\Rule::RELATED_PRODUCTS:
                $show = $this->_storeConfig->getValue(self::XML_PATH_TARGETRULE_CONFIG . 'related_position_behavior', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
                break;
            case \Magento\TargetRule\Model\Rule::UP_SELLS:
                $show = $this->_storeConfig->getValue(self::XML_PATH_TARGETRULE_CONFIG . 'upsell_position_behavior', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
                break;
            case \Magento\TargetRule\Model\Rule::CROSS_SELLS:
                $show = $this->_storeConfig->getValue(self::XML_PATH_TARGETRULE_CONFIG . 'crosssell_position_behavior', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
                break;
            default:
                throw new \Magento\Model\Exception(__('Invalid product list type'));
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
     * @throws \Magento\Model\Exception
     * @return int
     */
    public function getRotationMode($type)
    {
        switch ($type) {
            case \Magento\TargetRule\Model\Rule::RELATED_PRODUCTS:
                $mode = $this->_storeConfig->getValue(self::XML_PATH_TARGETRULE_CONFIG . 'related_rotation_mode', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
                break;
            case \Magento\TargetRule\Model\Rule::UP_SELLS:
                $mode = $this->_storeConfig->getValue(self::XML_PATH_TARGETRULE_CONFIG . 'upsell_rotation_mode', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
                break;
            case \Magento\TargetRule\Model\Rule::CROSS_SELLS:
                $mode = $this->_storeConfig->getValue(self::XML_PATH_TARGETRULE_CONFIG . 'crosssell_rotation_mode', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
                break;
            default:
                throw new \Magento\Model\Exception(__('Invalid rotation mode type'));
        }
        return $mode;
    }
}
