<?php
/**
 * Tax class grid
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Alexander Stadnitski <alexander@varien.com>
 */

class Mage_Adminhtml_Block_Tax_Class_Grid_Default extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setDefaultSort('class_name');
        $this->setDefaultDir('asc');
    }

    protected function _prepareCollection()
    {
        $classType = ( $this->getClassType() ) ? $this->getClassType() : 'CUSTOMER' ;

        $collection = Mage::getResourceModel('tax/class_collection')->setClassTypeFilter($classType);

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $classType = ( $this->getClassType() ) ? $this->getClassType() : 'CUSTOMER' ;

        $actionsUrl = Mage::getUrl('adminhtml/tax_class');

        $this->addColumn('class_name',
            array(
                'header'=>__('Class name'),
                'align' =>'left',
                'filter'    =>false,
                'index' => 'class_name'
            )
        );

       $this->addColumn('grid_actions',
            array(
                'header'=>__('Actions'),
                'width'=>10,
                'sortable'=>false,
                'filter'    =>false,
                'type' => 'action',
                'actions'   => array(
                                    array(
                                        'url' => $actionsUrl .'edit/classId/$class_id/classType/' . $classType,
                                        'caption' => __('Edit')
                                    ),

                                    array(
                                        'url' => $actionsUrl .'delete/classId/$class_id/classType/' . $classType,
                                        'caption' => __('Delete'),
                                        'confirm' => __('Are you sure you want to do this?')
                                    )
                                )
            )
        );
        $this->setFilterVisibility(false);
        return parent::_prepareColumns();
    }
}