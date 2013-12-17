<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml edit admin user account
 *
 * @category   Magento
 * @package    Magento_Backend
 * @author      Magento Core Team <core@magentocommerce.com>
 */

namespace Magento\Backend\Block\System\Account;

class Edit extends \Magento\Backend\Block\Widget\Form\Container
{
    protected function _construct()
    {
        parent::_construct();

        $this->_blockGroup = 'Magento_Backend';
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
