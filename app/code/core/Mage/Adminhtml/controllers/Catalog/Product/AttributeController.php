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
        $this->_setTypeId();
        $this->loadLayout('baseframe');
        $this->_setActiveMenu('catalog/categories');
        $this->getLayout()->getBlock('root')->setCanLoadExtJs(true);

        $this->_addBreadcrumb(__('Catalog'), __('Catalog Title'));
        $this->_addBreadcrumb(__('Manage Product Attributes'), __('Manage Product Attributes Title'));

        $this->_addContent($this->getLayout()->createBlock('adminhtml/catalog_product_attribute_toolbar_add'));
        $this->_addContent($this->getLayout()->createBlock('adminhtml/catalog_product_attribute_grid'));

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
        $this->_setTypeId();

        $data = $this->getRequest()->getPost();
        foreach( $data as $key => $value ) {
            if( !$value ) {
                unset($data[$key]);
            }
        }

        $model = Mage::getModel('eav/entity_attribute')
            ->setData($data)
            ->setEntityTypeId(Mage::registry('entityType'));

        if( $this->getRequest()->getParam('attribute_id') > 0 ) {
            $model->setId($this->getRequest()->getParam('attribute_id') );
        }

        if( $model->itemExists() === false ) {
            try {
                $model->save();

                Mage::getSingleton('adminhtml/session')->addSuccess('Product attribute successfully saved.');
                $this->_redirect('*/*/');
            } catch (Exception $e) {
                if ($referer = $this->getRequest()->getServer('HTTP_REFERER')) {
                    $this->getResponse()->setRedirect($referer);
                }
                Mage::getSingleton('adminhtml/session')->addError('Error while saving this attribute. Please, try again later.');
                $this->_returnLocation();
            }
        } else {
            Mage::getSingleton('adminhtml/session')->addError('Error while saving this attribute. Attribute with the same Name already exists.');
            $this->_returnLocation();
        }
    }

    public function deleteAction()
    {
        $attributeId = $this->getRequest()->getParam('attributeId');
        try {
            Mage::getModel('eav/entity_attribute')
                ->setId($attributeId)
                ->delete();
            $this->_redirect('*/*/');
        } catch (Exception $e) {
            if ($referer = $this->getRequest()->getServer('HTTP_REFERER')) {
                $this->getResponse()->setRedirect($referer);
            }
            Mage::getSingleton('adminhtml/session')->addError('Error while deleting this attribute. Please, try again later.');
            $this->_returnLocation();
        }
    }

    public function attributeGridAction()
    {
        $this->_setTypeId();
        $this->getResponse()->setBody($this->getLayout()->createBlock('adminhtml/catalog_product_attribute_grid')->toHtml());
    }

    protected function _returnLocation()
    {
        if ($referer = $this->getRequest()->getServer('HTTP_REFERER')) {
            $this->getResponse()->setRedirect($referer);
        }
    }

    protected function _setTypeId()
    {
        Mage::register('entityType', 10);
    }
}