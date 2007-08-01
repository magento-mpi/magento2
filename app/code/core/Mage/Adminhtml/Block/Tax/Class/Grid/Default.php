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
        $this->setSaveParametersInSession(true);
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
        $classType = ( $this->getClassType() ) ? $this->getClassType() : 'CUSTOMER';
        $this->setClassType($classType);

        $this->addColumn('class_name',
            array(
                'header'=>__('Class Name'),
                'align' =>'left',
                'index' => 'class_name'
            )
        );

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return Mage::getUrl('*/tax_class/edit', array('classId' => $row->getClassId(), 'classType' => $this->getClassType()));
    }
}