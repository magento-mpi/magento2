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
 * Mustishipping checkout base abstract block
 *
 * @category   Magento
 * @package    Magento_Checkout
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Checkout_Block_Multishipping_Abstract extends Magento_Core_Block_Template
{
    /**
     * @var Magento_Checkout_Model_Type_Multishipping
     */
    protected $_multishipping;

    /**
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param Magento_Checkout_Model_Type_Multishipping $multishipping
     * @param array $data
     */
    public function __construct(
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        Magento_Checkout_Model_Type_Multishipping $multishipping,
        array $data = array()
    ) {
        $this->_multishipping = $multishipping;
        parent::__construct($coreData, $context, $data);
    }

    /**
     * Retrieve multishipping checkout model
     *
     * @return Magento_Checkout_Model_Type_Multishipping
     */
    public function getCheckout()
    {
        return $this->_multishipping;
    }
}
