<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

/**
 * RMA Adminhtml Block
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Rma\Block\Adminhtml\Rma\Item;

class Attribute extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Initialize rma item management page
     *
     * @return void
     */
    public function _construct()
    {
        $this->_controller = 'adminhtml_rma_item_attribute';
        $this->_blockGroup = 'Magento_Rma';
        $this->_headerText = __('Return Item Attribute');
        $this->_addButtonLabel = __('Add New Attribute');
        parent::_construct();
    }
}
