<?php
/**
 * Tax class customer grid
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Alexander Stadnitski <alexander@varien.com>
 */

class Mage_Adminhtml_Block_Tax_Class_Customer_Grid_Class extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setSaveParametersInSession(true);
        $this->setDefaultSort('class_customer_name');
        $this->setDefaultDir('asc');
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('tax/class_customer_collection');
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('class_customer_name',
            array(
                'header'=>__('Class Name'),
                'align' =>'left',
                'filter'    =>false,
                'index' =>'class_customer_name'
            )
        );

        $this->setFilterVisibility(false);
        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return Mage::getUrl('*/*/customer/editItem', array('classId' => $row->getClassId()));
    }
}
