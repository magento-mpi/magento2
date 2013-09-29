<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Paypal
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Source model for available logo types
 */
class Magento_Paypal_Model_System_Config_Source_Logo implements Magento_Core_Model_Option_ArrayInterface
{
    /**
     * @var Magento_Paypal_Model_ConfigFactory
     */
    protected $_configFactory;

    /**
     * @param Magento_Paypal_Model_ConfigFactory $configFactory
     */
    public function __construct(Magento_Paypal_Model_ConfigFactory $configFactory)
    {
        $this->_configFactory = $configFactory;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $result = array('' => __('No Logo'));
        $result += $this->_configFactory->create()->getAdditionalOptionsLogoTypes();
        return $result;
    }
}
