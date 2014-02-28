<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\RecurringProfile\Block\Adminhtml;

/**
 * Adminhtml sales orders block
 */
class Profile extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Instructions to create child grid
     *
     * @var string
     */
    protected $_blockGroup = 'Magento_RecurringProfile';

    /**
     * @var string
     */
    protected $_controller = 'adminhtml_profile';

    /**
     * Set header text and remove "add" btn
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_headerText = __('Recurring Billing Profiles (beta)');
        parent::_construct();
        $this->_removeButton('add');
    }
}
