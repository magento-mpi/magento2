<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Indexer\Block\Backend;

class Container extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Initialize object state with incoming parameters
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'indexer';
        $this->_blockGroup = 'Magento_Indexer';
        $this->_headerText = __('New Indexer Management');
        parent::_construct();
    }

    /**
     * Prepare layout, remove button
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $this->_removeButton('add');
        return $this;
    }
}
