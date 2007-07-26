<?php
/**
 * Adminhtml customers page content block
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Adminhtml_Block_Customers extends Mage_Core_Block_Template 
{
    public function __construct() 
    {
        parent::__construct();
        $this->setTemplate('customer/index.phtml');
    }
    
    protected function _initChildren()
    {
        $this->setChild('addNewButton', 
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => __('Add Customer'),
                    'onclick'   => 'location.href=\''.Mage::getUrl('adminhtml/customer/new').'\'',
                    'class'   => 'add'
                ))
        );
        $this->setChild('grid', $this->getLayout()->createBlock('adminhtml/customer_grid', 'customer.grid'));
    }
    
    public function getAddNewButtonHtml()
    {
        return $this->getChildHtml('addNewButton');
    }
    
    public function getGridHtml()
    {
        return $this->getChildHtml('grid');
    }
}
