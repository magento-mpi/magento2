<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml sales orders grid
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Sales_Order_Grid extends Mage_Backend_Block_Widget_Grid
{
    /**
     * Initialize grid.
     * Add optional columns
     */
    protected function _prepareGrid()
    {
        if (!Mage::app()->isSingleStoreMode()) {
            $orderBlockName = $this->getColumnSet()->getChildBlock('real_order_id')->getNameInLayout();
            $this->getColumnSet()->insert(
                $this->getLayout()
                    ->createBlock('Mage_Backend_Block_Widget_Grid_Column', 'store_id', array(
                        'header'          => Mage::helper('Mage_Sales_Helper_Data')->__('Purchased From (Store)'),
                        'index'           => 'store_id',
                        'type'            => 'store',
                        'store_view'      => true,
                        'display_deleted' => true
                    )), $orderBlockName, true, 'store_id');
        }

        if (Mage::getSingleton('Mage_Core_Model_Authorization')->isAllowed('Mage_Sales::actions_view')) {
            $statusBlockName = $this->getColumnSet()->getChildBlock('status')->getNameInLayout();
            $this->getColumnSet()->insert($this->getLayout()
                ->createBlock('Mage_Backend_Block_Widget_Grid_Column', 'action', array(
                    'header'    => Mage::helper('Mage_Sales_Helper_Data')->__('Action'),
                    'width'     => '50px',
                    'type'      => 'action',
                    'getter'     => 'getId',
                    'actions'   => array(
                        array(
                            'caption' => Mage::helper('Mage_Sales_Helper_Data')->__('View'),
                            'url'     => array('base'=>'*/sales_order/view'),
                            'field'   => 'order_id'
                        )
                    ),
                    'filter'    => false,
                    'sortable'  => false,
                    'index'     => 'stores',
                    'is_system' => true
                )), $statusBlockName, true, 'action');
        }

        return parent::_prepareGrid();
    }
}
