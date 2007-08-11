<?php
/**
 * Customer edit block
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Adminhtml_Block_Customer_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        $this->_objectId = 'customer_id';
        $this->_controller = 'customer';

        parent::__construct();

        $this->_updateButton('save', 'label', __('Save Customer'));
        $this->_updateButton('delete', 'label', __('Delete Customer'));

        if ($this->getCustomerId()) {
            $this->_addButton('order', array(
                'label' => __('Create Order'),
                'onclick' => 'window.location.href=\'' . $this->getCreateOrderUrl() . '\'',
                'class' => 'add',
            ), 1);
        }
    }

    public function getCreateOrderUrl()
    {
        return Mage::getUrl('*/sales_order_create', array('customer_id' => $this->getRequest()->getParam('customer_id')));
    }

    public function getCustomerId()
    {
        return Mage::registry('customer')->getId();
    }

    public function getHeaderText()
    {
        if (Mage::registry('customer')->getId()) {
            return Mage::registry('customer')->getName();
        }
        else {
            return __('New Customer');
        }
    }

}
