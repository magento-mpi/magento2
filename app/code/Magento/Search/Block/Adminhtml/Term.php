<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Search\Block\Adminhtml;

class Term extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'term';
        $this->_headerText = __('Search');
        $this->_addButtonLabel = __('Add New Search Term');
        parent::_construct();
    }
}
