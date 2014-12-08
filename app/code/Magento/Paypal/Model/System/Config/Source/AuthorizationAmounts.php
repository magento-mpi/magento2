<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Paypal\Model\System\Config\Source;

/**
 * Source model for available Authorization Amounts for Account Verification
 *
 * @deprecated since 1.6.2.0
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class AuthorizationAmounts implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return [];
    }
}
