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
        $this->setTypeId(10);

        $this->setUseAjax(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('eav/entity_attribute_collection')
            ->setEntityTypeFilter( $this->getTypeId() );
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('attribute_id',array(
                            'header'=>__('ID'),
                            'align'=>'center',
                            'sortable'=>true,
                            'index'=>'attribute_id'
                        )
            );

        $this->addColumn('attribute_name', array(
                            'header'=>__('Attribute Name'),
                            'align'=>'center',
                            'sortable'=>true,
                            'index'=>'attribute_name'
                        )
            );

        $this->addColumn('attribute_model', array(
                            'header'=>__('Attribute Model'),
                            'align'=>'center',
                            'sortable'=>true,
                            'index'=>'attribute_model'
                        )
            );

        $this->addColumn('backend_model', array(
                            'header'=>__('Backend Model'),
                            'align'=>'center',
                            'sortable'=>true,
                            'index'=>'backend_model'
                        )
            );

        $this->addColumn('backend_type', array(
                            'header'=>__('Backend Type'),
                            'align'=>'center',
                            'sortable'=>true,
                            'index'=>'backend_type'
                        )
            );

        $this->addColumn('backend_table', array(
                            'header'=>__('Backend Table'),
                            'align'=>'center',
                            'sortable'=>true,
                            'index'=>'backend_table'
                        )
            );

        $this->addColumn('frontend_model', array(
                            'header'=>__('Frontend Table'),
                            'align'=>'center',
                            'sortable'=>true,
                            'index'=>'frontend_model'
                        )
            );

        $this->addColumn('frontend_input', array(
                            'header'=>__('Frontend Input'),
                            'align'=>'center',
                            'sortable'=>true,
                            'index'=>'frontend_input'
                        )
            );

        $this->addColumn('frontend_label', array(
                            'header'=>__('Frontend Label'),
                            'align'=>'center',
                            'sortable'=>true,
                            'index'=>'frontend_label'
                        )
            );

        $this->addColumn('frontend_class', array(
                            'header'=>__('Frontend Class'),
                            'align'=>'center',
                            'sortable'=>true,
                            'index'=>'frontend_class'
                        )
            );

        $this->addColumn('source_model', array(
                            'header'=>__('Source Model'),
                            'align'=>'center',
                            'sortable'=>true,
                            'index'=>'source_model'
                        )
            );

        $this->addColumn('is_global', array(
                            'header'=>__('Global'),
                            'align'=>'center',
                            'sortable'=>true,
                            'index'=>'is_global',
                            'type' => 'boolean'
                        )
            );

        $this->addColumn('is_visible', array(
                            'header'=>__('Visible'),
                            'align'=>'center',
                            'sortable'=>true,
                            'index'=>'is_visible',
                            'type' => 'boolean'
                        )
            );

        $this->addColumn('is_required', array(
                            'header'=>__('Required'),
                            'align'=>'center',
                            'sortable'=>true,
                            'index'=>'is_required',
                            'type' => 'boolean'
                        )
            );

        $this->addColumn('is_user_defined', array(
                            'header'=>__('System'),
                            'align'=>'center',
                            'sortable'=>true,
                            'index'=>'is_user_defined',
                            'type' => 'boolean',
                            'values' => array(__('Yes'), __('No'))
                        )
            );

        $this->addColumn('default_value', array(
                            'header'=>__('Default Value'),
                            'align'=>'center',
                            'sortable'=>true,
                            'index'=>'default_value',
                            'default' => 'N/A'
                        )
            );

        $this->addColumn('is_searchable', array(
                            'header'=>__('Searchable'),
                            'align'=>'center',
                            'sortable'=>true,
                            'index'=>'is_searchable',
                            'type' => 'boolean',
                        )
            );

        $this->addColumn('is_filterable', array(
                            'header'=>__('Filterable'),
                            'align'=>'center',
                            'sortable'=>true,
                            'index'=>'is_filterable',
                            'type' => 'boolean',
                        )
            );

        $this->addColumn('is_comparable', array(
                            'header'=>__('Comparable'),
                            'align'=>'center',
                            'sortable'=>true,
                            'index'=>'is_comparable',
                            'type' => 'boolean',
                        )
            );

        $this->addColumn('actions', array(
                            'header'=>__('Actions'),
                            'align'=>'center',
                            'sortable'=>false,
                            'filter'=>false,
                            'type' => 'action',
                            'actions' => array(
                                array(
                                    'url' => Mage::getUrl('*/*/edit/attributeId/$attribute_id'),
                                    'caption' => __('Edit')
                                )
                            )
                        )
            );

        return parent::_prepareColumns();
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/attributeGrid', array('_current'=>true));
    }
}