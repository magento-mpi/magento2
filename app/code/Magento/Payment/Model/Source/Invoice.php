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
namespace Magento\Payment\Model\Source;

class Invoice
{
    public function toOptionArray()
    {
        return array(
            array(
                'value' => \Magento\Payment\Model\Method\AbstractMethod::ACTION_AUTHORIZE_CAPTURE,
                'label' => __('Yes')
            ),
            array(
                'value' => '',
                'label' => __('No')
            ),
        );
    }
}
