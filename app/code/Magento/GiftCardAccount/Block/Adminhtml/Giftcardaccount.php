<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GiftCardAccount\Block\Adminhtml;

class Giftcardaccount extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_giftcardaccount';
        $this->_blockGroup = 'Magento_GiftCardAccount';
        $this->_headerText = __('Gift Card Accounts');
        $this->_addButtonLabel = __('Add Gift Card Account');
        parent::_construct();
    }
}
