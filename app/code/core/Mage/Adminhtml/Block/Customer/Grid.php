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
    }

    protected function _initCollection()
    {
        $collection = Mage::getModel('customer_resource/customer_collection');

        $order = $this->getRequest()->getPost('sort', 'customer_id');
        $dir   = $this->getRequest()->getPost('dir', 'desc');
        $collection->setOrder($order, $dir);
        $this->setCollection($collection);
    }

    protected function _beforeToHtml()
    {
        $this->addColumn('id', array('header'=>__('id'), 'width'=>40, 'align'=>'center', 'index'=>'customer_id'));
        $this->addColumn('http_host', array('header'=>__('email'), 'align'=>'center', 'index'=>'email'));
        $this->addColumn('firstname', array('header'=>__('firstname'), 'index'=>'firstname'));
        $this->addColumn('lastname', array('header'=>__('lastname'), 'index'=>'lastname'));

        $this->_initCollection();
        return parent::_beforeToHtml();
    }
}