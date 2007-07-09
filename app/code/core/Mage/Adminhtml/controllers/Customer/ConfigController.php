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
class Mage_Adminhtml_Customer_ConfigController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $this->loadLayout('baseframe');
        $this->_setActiveMenu('customer/config');
        $this->_addBreadcrumb(__('Customer'), __('customer title'));
        $this->_addBreadcrumb(__('Config'), __('config title'));

        $this->_addContent(
            $this->getLayout()->createBlock('adminhtml/customer_config')
        );
        $this->getLayout()->getBlock('left')
            ->append($this->getLayout()->createBlock('adminhtml/customer_config_tabs'));

        $this->renderLayout();
    }
}
