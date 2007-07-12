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

class Mage_Adminhtml_Block_Tax_Class_Grid_Group extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
    }

    protected function _prepareCollection()
    {
        $classId = $this->getRequest()->getParam('classId', null);
        $classType = $this->getRequest()->getParam('classType', null);

        if( isset($classId) ) {
            switch( $classType ) {
                case "CUSTOMER":
                    $collection = Mage::getResourceModel('customer/group_collection');
                    break;

                /* FIXME!!! */
                case "PRODUCT":
                    $collection = Mage::getResourceModel('customer/group_collection');
                    break;
            }
        }
        $collection->setTaxGroupFilter($classId);
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $classId = $this->getRequest()->getParam('classId');
        $classType = $this->getRequest()->getParam('classType');

        $actionsUrl = Mage::getUrl('adminhtml/tax_class/deleteGroup', array('classId'=>$classId, 'classType'=>$classType));

        if( isset($classId) ) {
            switch( $classType ) {
                case "CUSTOMER":
                    $index = 'customer_group_code';
                    $this->setGridHeader('Included customer groups');
                    break;

                /* FIXME!!! */
                case "PRODUCT":
                    $index = 'customer_group_code';
                    $this->setGridHeader('Included product categories');
                    break;
            }
        }

        $this->addColumn('class_name',
            array(
                'header'=>__('Group name'),
                'align' =>'left',
                'filter'    =>false,
                'index' => $index
            )
        );

       $this->addColumn('grid_actions',
            array(
                'header'=>__('Actions'),
                'width'=>5,
                'sortable'=>false,
                'filter'    =>false,
                'format' => '<a href="' . $actionsUrl .'groupId/$group_id/">' . __('Delete') . '</a>'
            )
        );
        $this->setFilterVisibility(false);
        return parent::_prepareColumns();
    }
}