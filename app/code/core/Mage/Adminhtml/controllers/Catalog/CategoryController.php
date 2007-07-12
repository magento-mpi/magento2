<?php
/**
 * Catalog category controller
 *
 * @package     MAge
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Adminhtml_Catalog_CategoryController extends Mage_Adminhtml_Controller_Action
{
    public function testAction()
    {
        $this->loadLayout('baseframe');
        $this->_setActiveMenu('catalog');
        $this->_addBreadcrumb(__('Catalog'), __('catalog title'));
        
        $this->getLayout()->getBlock('root')->setCanLoadExtJs(true);
        $this->_addContent(
            $this->getLayout()->createBlock('core/template')
                ->setTemplate('adminhtml/catalog/test.phtml')
        );
        $this->renderLayout();
    }
}
