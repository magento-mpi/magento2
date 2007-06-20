<?php
/**
 * dashboard admin controller
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Adminhtml_DashboardController extends Mage_Core_Controller_Front_Action 
{
    public function indexAction()
    {
        $this->loadLayout('baseframe');
        $this->getLayout()->getBlock('menu')->setActive('dashboard');
        $this->getLayout()->getBlock('breadcrumbs')
            ->addLink(__('dashboard'), __('dashboard title'));
        $this->getLayout()->getBlock('left')->append($this->getLayout()->createBlock('core/template', 'dashboard.menu')->setTemplate('adminhtml/dashboard/left.phtml'));
        $this->getLayout()->getBlock('content')->append($this->getLayout()->createBlock('adminhtml/dashboard', 'dashboard'));
        $this->renderLayout();
    }
}
