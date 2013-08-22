<?php
/**
 * {license_notice}
 *
 * @category    Magento
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
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array(
                'value' => Magento_Payment_Model_Method_Abstract::ACTION_AUTHORIZE,
                'label' => __('Authorize Only')
            ),
            array(
                'value' => Magento_Payment_Model_Method_Abstract::ACTION_AUTHORIZE_CAPTURE,
                'label' => __('Authorize and Capture')
            ),
        );
    }
}
