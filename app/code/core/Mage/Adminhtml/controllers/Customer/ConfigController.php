<?php
/**
 * config controller
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Adminhtml_Customer_ConfigController extends Mage_Core_Controller_Front_Action 
{
    public function indexAction()
    {
        $this->loadLayout('baseframe');
        $this->getLayout()->getBlock('menu')->setActive('customer/config');
        $this->getLayout()->getBlock('breadcrumbs')
            ->addLink(__('customer'), __('customer title'))
            ->addLink(__('config'), __('config title'));
        
        $this->getLayout()->getBlock('content')->append(
            $this->getLayout()->createBlock('adminhtml/customer_config')
        );
        $this->getLayout()->getBlock('left')
            ->append($this->getLayout()->createBlock('adminhtml/customer_config_tabs'));
            
        $this->renderLayout();
    }
}
