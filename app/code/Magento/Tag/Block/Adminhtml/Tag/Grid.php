<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Tag
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml all tags grid
 *
 * @category   Magento
 * @package    Magento_Tag
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Tag_Block_Adminhtml_Tag_Grid extends Magento_Adminhtml_Block_Widget_Grid
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('tag_tag_grid')
             ->setDefaultSort('name')
             ->setDefaultDir('ASC')
             ->setUseAjax(true)
             ->setSaveParametersInSession(true);
    }

    protected function _addColumnFilterToCollection($column)
    {
        if($column->getIndex()=='stores') {
            $this->getCollection()->addStoreFilter($column->getFilter()->getCondition(), false);
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('Magento_Tag_Model_Resource_Tag_Collection')
            ->addSummary(Mage::app()->getStore()->getId())
            ->addStoresVisibility();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('name', array(
            'header'        => __('Tag'),
            'index'         => 'name',
        ));

        $this->addColumn('products', array(
            'header'        => __('Products'),
            'width'         => 140,
            'align'         => 'right',
            'index'         => 'products',
            'type'          => 'number',
        ));

        $this->addColumn('customers', array(
            'header'        => __('Customers'),
            'width'         => 140,
            'align'         => 'right',
            'index'         => 'customers',
            'type'          => 'number',
        ));

        $this->addColumn('status', array(
            'header'        => __('Status'),
            'width'         => 90,
            'index'         => 'status',
            'type'          => 'options',
            'options'       => $this->helper('Magento_Tag_Helper_Data')->getStatusesArray(),
        ));

        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('visible_in', array(
                'header'                => __('Store View'),
                'type'                  => 'store',
                'skipAllStoresLabel'    => true,
                'index'                 => 'stores',
                'sortable'              => false,
                'store_view'            => true
            ));
        }

        return parent::_prepareColumns();
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('tag_id');
        $this->getMassactionBlock()->setFormFieldName('tag');

        $this->getMassactionBlock()->addItem('delete', array(
             'label'    => __('Delete'),
             'url'      => $this->getUrl('*/*/massDelete'),
             'confirm'  => __('Are you sure?')
        ));

        $statuses = $this->helper('Magento_Tag_Helper_Data')->getStatusesOptionsArray();

        array_unshift($statuses, array('label'=>'', 'value'=>''));

        $this->getMassactionBlock()->addItem('status', array(
            'label'=> __('Change status'),
            'url'  => $this->getUrl('*/*/massStatus', array('_current'=>true)),
            'additional' => array(
                'visibility' => array(
                    'name'     => 'status',
                    'type'     => 'select',
                    'class'    => 'required-entry',
                    'label'    => __('Status'),
                    'values'   => $statuses
                )
             )
        ));

        return $this;
    }

    /*
     * Retrieves Grid Url
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/tag/ajaxGrid', array('_current' => true));
    }

    /**
     * Retrives row click URL
     *
     * @param  mixed $row
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('tag_id' => $row->getId()));
    }
}
