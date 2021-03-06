<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Banner\Block\Adminhtml;

class Banner extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Initialize banners manage page
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_banner';
        $this->_blockGroup = 'Magento_Banner';
        $this->_headerText = __('Banners');
        $this->_addButtonLabel = __('Add Banner');
        parent::_construct();
    }
}
