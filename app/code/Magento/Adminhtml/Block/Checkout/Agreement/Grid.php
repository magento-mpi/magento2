<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Adminhtml_Block_Checkout_Agreement_Grid extends Magento_Adminhtml_Block_Widget_Grid
{

    protected function _construct()
    {
        parent::_construct();
        $this->setDefaultSort('agreement_id');
        $this->setId('agreementGrid');
        $this->setDefaultDir('asc');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('Magento_Checkout_Model_Agreement')
            ->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('agreement_id',
            array(
                'header'=>__('ID'),
                'index' => 'agreement_id',
                'header_css_class'  => 'col-id',
                'column_css_class'  => 'col-id'
            )
        );

        $this->addColumn('name',
            array(
                'header'=>__('Condition'),
                'index' => 'name',
                'header_css_class'  => 'col-name',
                'column_css_class'  => 'col-name'
            )
        );

        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('store_id', array(
                'header'        => __('Store View'),
                'index'         => 'store_id',
                'type'          => 'store',
                'store_all'     => true,
                'store_view'    => true,
                'sortable'      => false,
                'filter_condition_callback'
                                => array($this, '_filterStoreCondition'),
                'header_css_class'  => 'col-store-view',
                'column_css_class'  => 'col-store-view'
            ));
        }

        $this->addColumn('is_active', array(
            'header'    => __('Status'),
            'index'     => 'is_active',
            'type'      => 'options',
            'options'   => array(
                0 => __('Disabled'),
                1 => __('Enabled')
            ),
            'header_css_class'  => 'col-status',
            'column_css_class'  => 'col-status'
        ));

        return parent::_prepareColumns();
    }

    protected function _afterLoadCollection()
    {
        $this->getCollection()->walk('afterLoad');
        parent::_afterLoadCollection();
    }

    protected function _filterStoreCondition($collection, $column)
    {
        if (!$value = $column->getFilter()->getValue()) {
            return;
        }

        $this->getCollection()->addStoreFilter($value);
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }

}
