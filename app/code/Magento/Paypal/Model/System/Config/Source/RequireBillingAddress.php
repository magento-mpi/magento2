<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Paypal
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Source model for Require Billing Address
 */
namespace Magento\Paypal\Model\System\Config\Source;

class RequireBillingAddress implements \Magento\Core\Model\Option\ArrayInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        /** @var $configModel \Magento\Paypal\Model\Config */
        $configModel = \Mage::getModel('Magento\Paypal\Model\Config');
        return $configModel->getRequireBillingAddressOptions();
    }
}
