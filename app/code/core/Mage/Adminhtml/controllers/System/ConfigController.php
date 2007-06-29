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
class Mage_Adminhtml_System_ConfigController extends Mage_Core_Controller_Front_Action 
{
    public function indexAction()
    {
        $this->_forward('edit');
    }
    
    public function editAction()
    {
        $this->loadLayout('baseframe');
        $this->getLayout()->getBlock('menu')->setActive('system/config');
        
        $breadcrumbs = $this->getLayout()->getBlock('breadcrumbs')
            ->addLink(__('system'), __('system title'), Mage::getUrl('adminhtml/system'));
        
        $this->getLayout()->getBlock('content')->append(
            $this->getLayout()->createBlock('adminhtml/system_config_edit')
        );
        $this->getLayout()->getBlock('left')
            ->append(
                $this->getLayout()->createBlock('adminhtml/system_config_left')
                    ->bindBreadcrumbs($breadcrumbs)
            );
            
        $this->renderLayout();
    }
    
    public function saveAction()
    {
        
    }
}
