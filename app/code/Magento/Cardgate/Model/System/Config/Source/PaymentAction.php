<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Cardgate
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @category   Magento
 * @package    Magento_Cardgate
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Cardgate_Model_System_Config_Source_PaymentAction
{
    /**
     * Returns payment actions available for CardGate
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array(
                'value' => 0,
                'label' => __('Authorize Only')
            ),
            array(
                'value' => 1,
                'label' => __('Authorize and Capture')
            ),
        );
    }
}
