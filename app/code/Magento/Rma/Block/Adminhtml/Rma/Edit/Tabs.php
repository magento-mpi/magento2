<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Rma\Block\Adminhtml\Rma\Edit;

class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    /**
     * Initialize RMA edit page tabs
     *
     * @return void
     */
    public function _construct()
    {
        parent::_construct();
        $this->setId('rma_info_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Return Information'));
    }
}
