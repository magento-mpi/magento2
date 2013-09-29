<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Rma
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * RMA Adminhtml Block
 *
 * @category    Magento
 * @package     Magento_Rma
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Rma\Block\Adminhtml\Rma\Item;

class Attribute extends \Magento\Adminhtml\Block\Widget\Grid\Container
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
