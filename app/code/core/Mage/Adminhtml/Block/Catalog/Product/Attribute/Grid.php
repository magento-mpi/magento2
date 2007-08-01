<?php
/**
 * Product attributes grid
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Alexander Stadnitski <alexander@varien.com>
 */

class Mage_Adminhtml_Block_Catalog_Product_Attribute_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('attributeGrid');
        $this->setTypeId(Mage::registry('entityType'));
        $this->setDefaultSort('attribute_code');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('eav/entity_attribute_collection')
            ->setEntityTypeFilter( $this->getTypeId() )
            ->addVisibleFilter();
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('attribute_id',array(
                            'header'=>__('ID'),
                            'align'=>'right',
                            'sortable'=>true,
                            'width' => '50px',
                            'index'=>'attribute_id'
                        )
            );

        $this->addColumn('attribute_code', array(
                            'header'=>__('Attribute Code'),
                            'sortable'=>true,
                            'index'=>'attribute_code'
                        )
            );

        $this->addColumn('frontend_label', array(
                            'header'=>__('Frontend Label'),
                            'sortable'=>true,
                            'index'=>'frontend_label'
                        )
            );

        $this->addColumn('is_global', array(
                            'header'=>__('Global'),
                            'sortable'=>true,
                            'index'=>'is_global',
                            'type' => 'options',
                            'options' => array(__('No'), __('Yes')),
                            'filter' => 'adminhtml/widget_grid_column_filter_select',
                            'align' => 'center',
                        )
            );

        $this->addColumn('is_required', array(
                            'header'=>__('Required'),
                            'sortable'=>true,
                            'index'=>'is_required',
                            'type' => 'options',
                            'options' => array(__('No'), __('Yes')),
                            'filter' => 'adminhtml/widget_grid_column_filter_select',
                            'align' => 'center',
                        )
            );

        $this->addColumn('is_user_defined', array(
                            'header'=>__('System'),
                            'sortable'=>true,
                            'index'=>'is_user_defined',
                            'type' => 'options',
                            'options' => array(__('Yes'), __('No')),
                            'filter' => 'adminhtml/widget_grid_column_filter_select',
                            'values' => array(__('Yes'), __('No')),
                            'align' => 'center',
                        )
            );

        $this->addColumn('is_searchable', array(
                            'header'=>__('Searchable'),
                            'sortable'=>true,
                            'index'=>'is_searchable',
                            'type' => 'options',
                            'options' => array(__('No'), __('Yes')),
                            'filter' => 'adminhtml/widget_grid_column_filter_select',
                            'align' => 'center',
                        )
            );

        $this->addColumn('is_filterable', array(
                            'header'=>__('Filterable'),
                            'sortable'=>true,
                            'index'=>'is_filterable',
                            'type' => 'options',
                            'options' => array(__('No'), __('Fiterable (with results)'), __('Fiterable (no results)')),
                            'filter' => 'adminhtml/widget_grid_column_filter_select',
                            'align' => 'center',
                        )
            );

        $this->addColumn('is_comparable', array(
                            'header'=>__('Comparable'),
                            'sortable'=>true,
                            'index'=>'is_comparable',
                            'type' => 'options',
                            'options' => array(__('No'), __('Yes')),
                            'filter' => 'adminhtml/widget_grid_column_filter_select',
                            'align' => 'center',
                        )
            );

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return Mage::getUrl('*/*/edit', array('attributeId' => $row->getAttributeId()));
    }
}