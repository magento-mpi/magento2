<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml catalog inventory "Minimum Qty Allowed in Shopping Cart" field
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\CatalogInventory\Block\Adminhtml\Form\Field;

class Minsaleqty extends \Magento\Backend\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray
{
    /**
     * @var Customergroup
     */
    protected $_groupRenderer;

    /**
     * Retrieve group column renderer
     *
     * @return Customergroup
     */
    protected function _getGroupRenderer()
    {
        if (!$this->_groupRenderer) {
            $this->_groupRenderer = $this->getLayout()->createBlock(
                'Magento\CatalogInventory\Block\Adminhtml\Form\Field\Customergroup',
                '',
                array('data' => array('is_render_to_js_template' => true))
            );
            $this->_groupRenderer->setClass('customer_group_select');
        }
        return $this->_groupRenderer;
    }

    /**
     * Prepare to render
     *
     * @return void
     */
    protected function _prepareToRender()
    {
        $this->addColumn(
            'customer_group_id',
            array('label' => __('Customer Group'), 'renderer' => $this->_getGroupRenderer())
        );
        $this->addColumn('min_sale_qty', array('label' => __('Minimum Qty')));
        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add Minimum Qty');
    }

    /**
     * Prepare existing row data object
     *
     * @param \Magento\Framework\Object $row
     * @return void
     */
    protected function _prepareArrayRow(\Magento\Framework\Object $row)
    {
        $row->setData(
            'option_extra_attr_' . $this->_getGroupRenderer()->calcOptionHash($row->getData('customer_group_id')),
            'selected="selected"'
        );
    }
}
