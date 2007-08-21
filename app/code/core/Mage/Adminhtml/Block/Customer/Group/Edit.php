<?php
/**
 * Customer group edit block
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Alexander Stadnitski <alexander@varien.com>
 */
class Mage_Adminhtml_Block_Customer_Group_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{

    public function __construct()
    {
        parent::__construct();

        $this->_objectId = 'id';
        $this->_controller = 'customer_group';

        $this->_updateButton('save', 'label', __('Save Customer Group'));
        $this->_updateButton('delete', 'label', __('Delete Customer Group'));

        if( !$this->getRequest()->getParam($this->_objectId) ) {
            $this->_removeButton('delete');
        }
    }

    public function getHeaderText()
    {
        if( $this->getRequest()->getParam($this->_objectId) ) {
            $groupData = Mage::getModel('customer/group')
                ->load($this->getRequest()->getParam($this->_objectId));
            return __('Edit Customer Group') . " '" . $groupData->getCustomerGroupCode() . "'";
        } else {
            return __('New Customer Group');
        }
    }
}