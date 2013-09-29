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
 * Adminhtml edit admin user account
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

namespace Magento\Adminhtml\Block\System\Account;

class Edit extends \Magento\Adminhtml\Block\Widget\Form\Container
{
    protected function _construct()
    {
        parent::_construct();

        $this->_controller = 'system_account';
        $this->_updateButton('save', 'label', __('Save Account'));
        $this->_removeButton('delete');
        $this->_removeButton('back');
    }

    public function getHeaderText()
    {
        return __('My Account');
    }
}
