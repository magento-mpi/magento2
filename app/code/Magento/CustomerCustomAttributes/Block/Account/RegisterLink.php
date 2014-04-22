<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CustomerCustomAttributes\Block\Account;

/**
 * Class \Magento\CustomerCustomAttributes\Block\Account\RegisterLink
 */
class RegisterLink extends \Magento\Customer\Block\Account\RegisterLink
{
    /**
     * {@inheritdoc}
     */
    protected function _toHtml()
    {
        $key = 'magento_invitation/general/registration_required_invitation';
        if ($this->_scopeConfig->isSetFlag($key, \Magento\Store\Model\ScopeInterface::SCOPE_STORE)) {
            return '';
        }
        return parent::_toHtml();
    }
}
