<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\GiftRegistry\Block\Adminhtml;

/**
 * Gift Registry Adminhtml Block
 */
class Giftregistry extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Initialize gift registry manage page
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_giftregistry';
        $this->_blockGroup = 'Magento_GiftRegistry';
        $this->_headerText = __('Gift Registry Types');
        $this->_addButtonLabel = __('Add Gift Registry Type');
        parent::_construct();
    }
}
