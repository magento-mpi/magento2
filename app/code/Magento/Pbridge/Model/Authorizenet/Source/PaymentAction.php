<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Pbridge
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 *
 *  Authorizenet Payment Action Dropdown source
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Pbridge_Model_Authorizenet_Source_PaymentAction
{
    /**
     * @var Magento_Pbridge_Helper_Data
     */
    protected $_helper = null;

    /**
     * @param Magento_Pbridge_Helper_Data $helper
     */
    public function __construct(Magento_Pbridge_Helper_Data $helper)
    {
        $this->_helper = $helper;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array(
                'value' => Magento_Payment_Model_Method_Abstract::ACTION_AUTHORIZE,
                'label' => $this->_helper->__('Authorize Only')
            ),
            array(
                'value' => Magento_Payment_Model_Method_Abstract::ACTION_AUTHORIZE_CAPTURE,
                'label' => $this->_helper->__('Authorize and Capture')
            ),
        );
    }
}
