<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Factory for Adminhtml VAT validation block
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Adminhtml\Block\Customer\System\Config;

class ValidatevatFactory
{
    /**
     * Create new VAT validator
     *
     * @return \Magento\Adminhtml\Block\Customer\System\Config\Validatevat
     */
    public function createVatValidator()
    {
        return \Mage::getBlockSingleton('\Magento\Adminhtml\Block\Customer\System\Config\Validatevat');
    }
}
