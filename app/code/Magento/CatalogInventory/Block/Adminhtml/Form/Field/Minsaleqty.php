<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CatalogInventory
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml catalog inventory "Minimum Qty Allowed in Shopping Cart" field
 *
 * @category   Magento
 * @package    Magento_CatalogInventory
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\CatalogInventory\Block\Adminhtml\Form\Field;

class Minsaleqty
    extends \Magento\Backend\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray
{
    /**
     * @var \Magento\CatalogInventory\Block\Adminhtml\Form\Field\Customergroup
     */
    protected $_groupRenderer;

    /**
     * Retrieve group column renderer
     *
     * @return \Magento\CatalogInventory\Block\Adminhtml\Form\Field\Customergroup
     */
    protected function _getGroupRenderer()
    {
        if (!$this->_groupRenderer) {
            $this->_groupRenderer = $this->getLayout()->createBlock(
                '\Magento\CatalogInventory\Block\Adminhtml\Form\Field\Customergroup', '',
                array('data' => array('is_render_to_js_template' => true))
            );
            $this->_groupRenderer->setClass('customer_group_select');
            $this->_groupRenderer->setExtraParams('style="width:120px"');
        }
        return $this->_groupRenderer;
    }

    /**
     * Prepare to render
     */
    protected function _prepareToRender()
    {
        $this->addColumn('customer_group_id', array(
            'label' => __('Customer Group'),
            'renderer' => $this->_getGroupRenderer(),
        ));
        $this->addColumn('min_sale_qty', array(
            'label' => __('Minimum Qty'),
            'style' => 'width:100px',
        ));
        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add Minimum Qty');
    }

    /**
     * Prepare existing row data object
     *
     * @param \Magento\Object
     */
    protected function _prepareArrayRow(\Magento\Object $row)
    {
        $row->setData(
            'option_extra_attr_' . $this->_getGroupRenderer()->calcOptionHash($row->getData('customer_group_id')),
            'selected="selected"'
        );
    }
}
