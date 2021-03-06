<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\TargetRule\Block\Adminhtml;

class Targetrule extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Initialize invitation manage page
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_targetrule';
        $this->_blockGroup = 'Magento_TargetRule';
        $this->_headerText = __('Related Products Rule');
        $this->_addButtonLabel = __('Add Rule');
        parent::_construct();
    }
}
