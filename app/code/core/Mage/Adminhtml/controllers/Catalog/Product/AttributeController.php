<?php
/**
 * Catalog product attribute controller
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 * @author      Alexander Stadnitski <alexander@varien.com>
 */
class Mage_Adminhtml_Catalog_Product_AttributeController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $this->loadLayout('baseframe');
        $this->_setActiveMenu('catalog/categories');
        $this->getLayout()->getBlock('root')->setCanLoadExtJs(true);

        $this->_addBreadcrumb(__('Catalog'), __('Catalog Title'));
        $this->_addBreadcrumb(__('Manage Product Attributes'), __('Manage Product Attributes Title'));

        $this->_addContent(
                $this->getLayout()->createBlock('adminhtml/catalog_product_attribute_grid')
            );

        $this->renderLayout();
    }

    public function editAction()
    {
        $this->loadLayout('baseframe');
        $this->_setActiveMenu('catalog/categories');
        $this->getLayout()->getBlock('root')->setCanLoadExtJs(true);

        $this->_addBreadcrumb(__('Catalog'), __('Catalog Title'));
        $this->_addBreadcrumb(__('Manage Product Attributes'), __('Manage Product Attributes Title'), Mage::getUrl('*/*/'));
        $this->_addBreadcrumb(__('Edit Product Attribute'), __('Edit Product Attribute Title'));

        $this->_addContent($this->getLayout()->createBlock('adminhtml/catalog_product_attribute_form'));

        $this->renderLayout();
    }

    public function saveAction()
    {
        try {
            Mage::getModel('eav/entity_attribute')
                ->setId($this->getRequest()->getParam('attribute_id'))
                ->setAttributeName($this->getRequest()->getParam('attribute_name'))
                ->setDefaultValue($this->getRequest()->getParam('default_value'))
                ->setAttributeModel($this->getRequest()->getParam('attribute_model'))
                ->setBackendModel($this->getRequest()->getParam('backend_model'))
                ->setBackendType($this->getRequest()->getParam('backend_type'))
                ->setBackendTable($this->getRequest()->getParam('backend_table'))
                ->setFrontendModel($this->getRequest()->getParam('frontend_model'))
                ->setFrontendInput($this->getRequest()->getParam('frontend_input'))
                ->setFrontendLabel($this->getRequest()->getParam('frontend_label'))
                ->setFrontendClass($this->getRequest()->getParam('frontend_class'))
                ->setSourceModel($this->getRequest()->getParam('source_model'))
                ->setIsGlobal($this->getRequest()->getParam('is_global'))
                ->setIsVisible($this->getRequest()->getParam('is_visible'))
                ->setIsRequired($this->getRequest()->getParam('is_required'))
                ->setIsSearchable($this->getRequest()->getParam('is_searchable'))
                ->setIsFilterable($this->getRequest()->getParam('is_filterable'))
                ->setIsComparable($this->getRequest()->getParam('is_comparable'))
                ->save();
            $this->_redirect('*/*/');
        } catch (Exception $e) {
            if ($referer = $this->getRequest()->getServer('HTTP_REFERER')) {
                $this->getResponse()->setRedirect($referer);
            }
            Mage::getSingleton('adminhtml/session')->addError('Error while saving this attribute. Please, try again later.');
            $this->_returnLocation();
        }
    }

    public function deleteAction()
    {

    }

    public function attributeGridAction()
    {
        $this->getResponse()->setBody($this->getLayout()->createBlock('adminhtml/catalog_product_attribute_grid')->toHtml());
    }

    protected function _returnLocation()
    {
        if ($referer = $this->getRequest()->getServer('HTTP_REFERER')) {
            $this->getResponse()->setRedirect($referer);
        }
    }
}
