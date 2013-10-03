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
 * Custom Varieble Block
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */

namespace Magento\Adminhtml\Block\System;

class Variable extends \Magento\Adminhtml\Block\Widget\Grid\Container
{
    /**
     * Block constructor
     */
    protected function _construct()
    {
        $this->_controller = 'system_variable';
        $this->_headerText = __('Custom Variables');
        parent::_construct();
        $this->_updateButton('add', 'label', __('Add New Variable'));
    }
}
