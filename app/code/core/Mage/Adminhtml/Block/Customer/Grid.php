<?php
/**
 * Adminhtml customer grid block
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Adminhtml_Block_Customer_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('customerGrid');
    }

    protected function _initCollection()
    {
        $collection = Mage::getResourceModel('customer/customer_collection')
            ->addAttributeToSelect('firstname')
            ->addAttributeToSelect('lastname')
            ->addAttributeToSelect('email');
        $this->setCollection($collection);
    }

    protected function _beforeToHtml()
    {
        $this->addColumn('id', array(
            'header'=>__('id'), 
            'width'=>5, 
            'align'=>'center', 
            'sortable'=>true, 
            'index'=>'entity_id'
        ));
        $this->addColumn('email', array(
            'header'=>__('email'), 
            'width'=>40, 
            'align'=>'center', 
            'index'=>'email'
        ));
        $this->addColumn('firstname', array(
            'header'=>__('firstname'), 
            'index'=>'firstname'
        ));
        $this->addColumn('lastname', array(
            'header'=>__('lastname'), 
            'index'=>'lastname'
        ));
        $this->addColumn('action', array(
            'header'=>__('action'),
            'align'=>'center',
            'format'=>'<a href="'.Mage::getUrl('*/*/edit/id/$entity_id').'">'.__('edit').'</a>',
            'index'=>'customer_id', 
            'sortable'=>false)
        );

        $this->_initCollection();
        return parent::_beforeToHtml();
    }
}