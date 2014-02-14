<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml sales orders block
 */
namespace Magento\RecurringProfile\Block\Adminhtml;

class Profile extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Instructions to create child grid
     *
     * @var string
     */
    protected $_blockGroup = 'Magento_RecurringProfile';
    protected $_controller = 'adminhtml_profile';

    /**
     * Set header text and remove "add" btn
     */
    protected function _construct()
    {
        $this->_headerText = __('Recurring Billing Profiles (beta)');
        parent::_construct();
        $this->_removeButton('add');
    }
}
