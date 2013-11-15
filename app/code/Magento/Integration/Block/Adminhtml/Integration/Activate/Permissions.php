<?php
/**
 * Permissions dialog container.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Integration\Block\Adminhtml\Integration\Activate;

class Permissions extends \Magento\Backend\Block\Widget\Form\Container
{
    /**
     * {@inheritDoc}
     */
    protected function _construct()
    {
        $this->_controller = 'integration';
        parent::_construct();
        $this->_removeButton('back');
        $this->_removeButton('delete');
        $this->_removeButton('save');
        $this->_removeButton('reset');
    }

    /**
     * {@inheritDoc}
     */
    public function getFormActionUrl()
    {
        return '#';
    }
}
