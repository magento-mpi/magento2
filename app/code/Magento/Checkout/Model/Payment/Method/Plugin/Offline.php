<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Checkout\Model\Payment\Method\Plugin;

class Offline
{
    public function afterGetPaymentMethods(\Magento\Payment\Helper\Data $subject, array $result)
    {
        $output = array();
        foreach ($result as $type => $value) {
            if (isset($value['group']) && $value['group'] == 'offline') {
                $output[$type] = $value;
            }
        }
        return $output;
    }
}
