<?php
/**
 * sales admin controller
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Adminhtml_SalesController extends Mage_Core_Controller_Front_Action 
{
    public function indexAction()
    {
        $this->loadLayout('baseframe');
        $this->getLayout()->getBlock('breadcrumbs')
            ->addLink(__('sales'), __('sales title'));
        $this->renderLayout();
    }
}
