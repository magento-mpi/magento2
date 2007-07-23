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
        $this->setDefaultSort('attribute_code');
        $this->setDefaultDir('ASC');
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
                            'align'=>'right',
                            'sortable'=>true,
                            'index'=>'attribute_id'
                        )
            );

        $this->addColumn('attribute_code', array(
                            'header'=>__('Attribute Code'),
                            'sortable'=>true,
                            'index'=>'attribute_code'
                        )
            );

        $this->addColumn('attribute_name', array(
                            'header'=>__('Attribute Name'),
                            'sortable'=>true,
                            'index'=>'attribute_name'
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
                            'type' => 'boolean',
                            'align' => 'center',
                        )
            );

        $this->addColumn('is_visible', array(
                            'header'=>__('Visible'),
                            'sortable'=>true,
                            'index'=>'is_visible',
                            'type' => 'boolean'
                        )
            );

        $this->addColumn('is_required', array(
                            'header'=>__('Required'),
                            'sortable'=>true,
                            'index'=>'is_required',
                            'type' => 'boolean',
                            'align' => 'center',
                        )
            );

        $this->addColumn('is_user_defined', array(
                            'header'=>__('System'),
                            'sortable'=>true,
                            'index'=>'is_user_defined',
                            'type' => 'boolean',
                            'align' => 'center',
                            'values' => array(__('Yes'), __('No'))
                        )
            );

        $this->addColumn('is_searchable', array(
                            'header'=>__('Searchable'),
                            'sortable'=>true,
                            'index'=>'is_searchable',
                            'type' => 'boolean',
                            'align' => 'center',
                        )
            );

        $this->addColumn('is_filterable', array(
                            'header'=>__('Filterable'),
                            'sortable'=>true,
                            'index'=>'is_filterable',
                            'type' => 'boolean',
                            'align' => 'center',
                        )
            );

        $this->addColumn('is_comparable', array(
                            'header'=>__('Comparable'),
                            'sortable'=>true,
                            'index'=>'is_comparable',
                            'type' => 'boolean',
                            'align' => 'center',
                        )
            );

        $this->addColumn('actions', array(
                            'header'=>__('Actions'),
                            'sortable'=>false,
                            'filter'=>false,
                            'type' => 'action',
                            'align' => 'center',
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