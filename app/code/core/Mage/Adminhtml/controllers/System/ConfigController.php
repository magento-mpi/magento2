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
class Mage_Adminhtml_System_ConfigController extends Mage_Adminhtml_Controller_Action
{
    protected function _construct()
    {
        $this->setFlag('index', 'no-preDispatch', true);
        return parent::_construct();
    }

    public function indexAction()
    {
        $this->_forward('edit');
    }

    public function editAction()
    {
        $this->loadLayout('baseframe');
        $this->_setActiveMenu('system/config');

        $this->_addBreadcrumb(__('System'), __('system title'), Mage::getUrl('adminhtml/system'));

        $breadcrumbs = $this->getLayout()->getBlock('breadcrumbs');

        $this->getLayout()->getBlock('left')
            ->append(
                $this->getLayout()->createBlock('adminhtml/system_config_tabs')
                    ->bindBreadcrumbs($breadcrumbs)
            );
        $this->_addContent(
            $this->getLayout()->createBlock('adminhtml/system_config_edit')
                    ->bindBreadcrumbs($breadcrumbs)
        );

        $this->renderLayout();
    }

    public function saveAction()
    {

    }
}
