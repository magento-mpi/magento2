<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Enterprise_Pbridge
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 *
 *  Authorizenet Payment Action Dropdown source
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Pbridge_Model_Authorizenet_Source_PaymentAction
{
    /**
     * @var Enterprise_Pbridge_Helper_Data
     */
    protected $_helper = null;

    /**
     * @param Enterprise_Pbridge_Helper_Data $helper
     */
    public function __construct(Enterprise_Pbridge_Helper_Data $helper)
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
