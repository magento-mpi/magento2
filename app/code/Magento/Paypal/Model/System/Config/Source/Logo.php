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
 * Source model for available logo types
 */
namespace Magento\Paypal\Model\System\Config\Source;

class Logo
{
    public function toOptionArray()
    {
        $result = array('' => __('No Logo'));
        $result += \Mage::getModel('Magento\Paypal\Model\Config')->getAdditionalOptionsLogoTypes();
        return $result;
    }
}
