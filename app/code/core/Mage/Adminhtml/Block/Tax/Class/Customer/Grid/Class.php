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

class Mage_Adminhtml_Block_Tax_Class_Customer_Grid_Class extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('tax/class_customer_collection');
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $baseUrl = Mage::getUrl();

        $actionsUrl = Mage::getUrl('adminhtml/tax_class_customer');

        $this->addColumn('class_customer_name',
            array(
                'header'=>__('Class name'),
                'align' =>'left',
                'filter'    =>false,
                'index' =>'class_customer_name'
            )
        );

       $this->addColumn('customer_actions',
            array(
                'header'=>__('actions'),
                'width'=>10,
                'sortable'=>false,
                'filter'    =>false,
                'format' => '<a href="' . $actionsUrl .'editItem/classId/$class_customer_id/">' . __('Edit') . '</a> <a href="' . $actionsUrl .'deleteItem/classId/$class_customer_id/">' . __('Delete') . '</a>'
            )
        );

        return parent::_prepareColumns();
    }
}