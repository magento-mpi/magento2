<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Checkout
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Multishipping checkout state
 *
 * @category   Magento
 * @package    Magento_Checkout
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Checkout_Block_Multishipping_State extends Magento_Core_Block_Template
{
    /**
     * @var Magento_Checkout_Model_Type_Multishipping_State
     */
    protected $_multishippingState;

    /**
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param Magento_Checkout_Model_Type_Multishipping_State $multishippingState
     * @param array $data
     */
    public function __construct(
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        Magento_Checkout_Model_Type_Multishipping_State $multishippingState,
        array $data = array()
    ) {
        $this->_multishippingState = $multishippingState;
        parent::__construct($coreData, $context, $data);
    }

    /**
     * @return array
     */
    public function getSteps()
    {
        return $this->_multishippingState->getSteps();
    }
}
