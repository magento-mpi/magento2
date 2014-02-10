<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Authorizenet
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 *
 * Authorizenet Payment Action Dropdown source
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Authorizenet\Model\Authorizenet\Source;

class PaymentAction implements \Magento\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return array(
            array(
                'value' => \Magento\Authorizenet\Model\Authorizenet::ACTION_AUTHORIZE,
                'label' => __('Authorize Only')
            ),
            array(
                'value' => \Magento\Authorizenet\Model\Authorizenet::ACTION_AUTHORIZE_CAPTURE,
                'label' => __('Authorize and Capture')
            ),
        );
    }
}
