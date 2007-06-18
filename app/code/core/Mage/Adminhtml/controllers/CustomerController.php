<?php
/**
 * Customer admin controller
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Adminhtml_CustomerController extends Mage_Core_Controller_Front_Action 
{
    public function indexAction()
    {
        $this->loadLayout('baseframe');
        
        $this->getLayout()->getBlock('left')->append($this->getLayout()->createBlock('adminhtml/customer_left'));
        
        $block = $this->getLayout()->createBlock('adminhtml/customers', 'customers');
        $this->getLayout()->getBlock('content')->append($block);
        
        $this->getLayout()->getBlock('breadcrumbs')
            ->addLink(__('customers'), __('customers title'));

        $this->renderLayout();
    }
}
