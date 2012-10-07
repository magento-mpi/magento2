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
     * Default constructor
     * @param array $arguments
     */
    public function __construct(array $arguments = array())
    {
        if (isset($arguments['helper'])) {
            $this->_helper = $arguments['helper'];
        }
    }

    /**
     * Get helper
     * @return Enterprise_Pbridge_Helper_Data|null
     */
    protected function _getHelper()
    {
        if (is_null($this->_helper)) {
            $this->_helper = Mage::helper('Enterprise_Pbridge_Helper_Data');
        }
        return $this->_helper;
    }

    public function toOptionArray()
    {
        return array(
            array(
                'value' => Mage_Payment_Model_Method_Abstract::ACTION_AUTHORIZE,
                'label' => $this->_getHelper()->__('Authorize Only')
            ),
            array(
                'value' => Mage_Payment_Model_Method_Abstract::ACTION_AUTHORIZE_CAPTURE,
                'label' => $this->_getHelper()->__('Authorize and Capture')
            ),
        );
    }
}
