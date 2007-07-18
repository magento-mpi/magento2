<?php
/**
 * Customer group edit block
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Ivan Chepurnyi <mitch@varien.com>
 */
class Mage_Adminhtml_Block_Customer_Group_Edit extends Mage_Adminhtml_Block_Widget 
{
    /**
     * Edit mode flag
     *
     * @var boolean
     */
    protected $_editMode = false;
    
    public function __construct() 
    {
        parent::__construct();
        $this->setTemplate('adminhtml/customer/group/edit.phtml');
    }
    
    /**
     * Return action url for form
     *
     * @return string
     */
    public function getSaveUrl()
    {
        return Mage::getUrl('adminhtml', array('controller'=>'customer_group', 'action'=>'save'));
    }
    
    /**
     * Return delete url for customer group
     *
     * @return string
     */
    public function getDeleteUrl()
    {
        return Mage::getUrl('adminhtml', array('controller' => 'customer_group',
                                               'action'     => 'delete',
                                               'id'         => $this->_request->getParam('id')));
    }
    
    /**
     * Set edit flag for block
     * 
     * @param boolean $value
     * @return Mage_Adminhtml_Block_Customer_Group_Edit
     */
    public function setEditMode($value=true)
    {
        $this->_editMode = $value;
        return $this;
    }
    
    /**
     * Return edit flag for block
     *
     * @return boolean
     */
    public function getEditMode()
    {
        return $this->_editMode;
    }
    
    /**
     * Prepares block for rendering
     * 
     * @return Mage_Adminhtml_Block_Customer_Group_Edit
     */
    protected function _beforeToHtml()
    {
        if($this->getEditMode()) {
            $this->assign('header', __('Edit Customer Group'));
        } else {
            $this->assign('header', __('Add New Customer Group'));
        }
        
        $this->assign('form', $this->getLayout()->createBlock('adminhtml/customer_group_edit_form')->toHtml());
        return $this;
    }
}
