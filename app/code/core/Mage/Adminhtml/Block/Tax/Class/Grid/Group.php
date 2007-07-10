<?php
/**
 * Tax cluss customer grid
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

        $classId = $this->getRequest()->getParam('classId', null);
        $classType = $this->getRequest()->getParam('classType', null);

        if( isset($classId) ) {
            switch( $classType ) {
                case "CUSTOMER":
                    $collection = Mage::getResourceModel('customer/group_collection');
                    break;

                case "PRODUCT":
                    $collection = Mage::getResourceModel('customer/group_collection');
                    break;
            }
        }
        $collection->setTaxGroupFilter($classId);
        $this->setCollection($collection);
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
                    break;

                /* FIXME!!! */
                case "PRODUCT":
                    $index = 'customer_group_code';
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

        return parent::_prepareColumns();
    }
}