<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Rma
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Rma\Block\Adminhtml\Rma\Item\Attribute\Edit;

/**
 * RMA items attributes edit page tabs
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Tabs
    extends \Magento\Backend\Block\Widget\Tabs
{
    /**
     * Initialize edit tabs
     *
     * @return void
     */
    public function _construct()
    {
        parent::_construct();

        $this->setId('rma_item_attribute_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Attribute Information'));
    }
}
