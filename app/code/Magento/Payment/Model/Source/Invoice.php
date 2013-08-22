<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Payment
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Automatic invoice create source model
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Payment_Model_Source_Invoice
{
    public function toOptionArray()
    {
        return array(
            array(
                'value' => Magento_Payment_Model_Method_Abstract::ACTION_AUTHORIZE_CAPTURE,
                'label' => __('Yes')
            ),
            array(
                'value' => '',
                'label' => __('No')
            ),
        );
    }
}
