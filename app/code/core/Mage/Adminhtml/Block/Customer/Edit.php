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
class Mage_Adminhtml_Block_Customer_Edit extends Mage_Adminhtml_Block_Widget 
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('adminhtml/customer/edit.phtml');
    }

    public function getSaveUrl()
    {
        return $this->getUrl('*/*/save', array('_current'=>true));
    }

    public function getCustomerId()
    {
        return $this->getRequest()->getParam('id');
    }
    
    public function getDeleteUrl()
    {
        return $this->getUrl('*/*/delete', array('_current'=>true));
    }
    
    public function getHeader()
    {
        if ($this->getCustomerId()) {
            return __('edit customer');
        }
        else {
            return __('new customer');
        }
    }
}
