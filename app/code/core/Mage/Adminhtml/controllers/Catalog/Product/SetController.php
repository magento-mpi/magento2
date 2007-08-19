<?php
/**
 * Adminhtml entity sets controller
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Alexander Stadnitski <alexander@varien.com>
 */

class Mage_Adminhtml_Catalog_Product_SetController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $this->_setTypeId();

        $this->loadLayout('baseframe');
        $this->_setActiveMenu('catalog/sets');

        $this->_addBreadcrumb(__('Catalog'), __('Catalog'));
        $this->_addBreadcrumb(__('Manage Product Sets'), __('Manage Product Sets'));

        $this->_addContent($this->getLayout()->createBlock('adminhtml/catalog_product_attribute_set_toolbar_main'));
        $this->_addContent($this->getLayout()->createBlock('adminhtml/catalog_product_attribute_set_grid'));

        $this->renderLayout();
    }

    public function editAction()
    {
        $this->_setTypeId();
        $attributeSet = Mage::getModel('eav/entity_attribute_set')
            ->load($this->getRequest()->getParam('id'));
        
        if (!$attributeSet->getId()) {
            $this->_redirect('*/*/index');
            return;
        }
        
        Mage::register('current_attribute_set', $attributeSet);
        
        $this->loadLayout('baseframe');
        $this->_setActiveMenu('catalog/sets');
        $this->getLayout()->getBlock('root')->setCanLoadExtJs(true);

        $this->_addBreadcrumb(__('Catalog'), __('Catalog'));
        $this->_addBreadcrumb(__('Manage Product Sets'), __('Manage Product Sets'));

        $this->_addContent($this->getLayout()->createBlock('adminhtml/catalog_product_attribute_set_main'));

        $this->renderLayout();
    }

    public function setGridAction()
    {
        $this->_setTypeId();
        $this->getResponse()->setBody($this->getLayout()->createBlock('adminhtml/catalog_product_attribute_set_grid')->toHtml());
    }

    public function saveAction()
    {
        $this->_setTypeId();
        $response = new Varien_Object();
        $response->setError(0);

        $modelSet = Mage::getModel('eav/entity_attribute_set')
            ->setId($this->getRequest()->getParam('id'));

        if( $this->getRequest()->getParam('gotoEdit') ) {
            $modelSet = Mage::getModel('eav/entity_attribute_set');
            $modelSet->setAttributeSetName($this->getRequest()->getParam('attribute_set_name'))
                ->setEntityTypeId(Mage::registry('entityType'));
        } else {
            $data = Zend_Json_Decoder::decode($this->getRequest()->getPost('data'));
            $modelSet->organizeData($data);
        }

        try {
            $modelSet->save();
            if( $this->getRequest()->getParam('gotoEdit') == 1 ) {
                $modelSet->initFromSkeleton($this->getRequest()->getParam('skeleton_set'))
                    ->save();

                $this->getResponse()->setRedirect(Mage::getUrl('*/*/edit', array('id' => $modelSet->getId())));
                Mage::getSingleton('adminhtml/session')->addSuccess(__('Attribute set successfully saved.'));
            } else {
                Mage::getSingleton('adminhtml/session')->addSuccess(__('Attribute set successfully saved.'));
                $response->setMessage(__('Attribute set successfully saved.'));
                $response->setUrl(Mage::getUrl('*/*/'));
            }
        } catch (Exception $e) {
            if( $this->getRequest()->getParam('gotoEdit') == 1 ) {
                if ($referer = $this->getRequest()->getServer('HTTP_REFERER')) {
                    $this->getResponse()->setRedirect($referer);
                }
                //Mage::getSingleton('adminhtml/session')->addError(__('Error while saving this set. May be set with the same name already exists.'));
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            } else {
                $response->setMessage(__('Error while saving this set.'));
                $response->setError(1);
            }
        }
        if( $this->getRequest()->getParam('gotoEdit') != 1 ) {
            $this->getResponse()->setBody($response->toJson());
        }
    }

    public function addAction()
    {
         $this->_setTypeId();

        $this->loadLayout('baseframe');
        $this->_setActiveMenu('catalog/sets');

        $this->_addContent($this->getLayout()->createBlock('adminhtml/catalog_product_attribute_set_toolbar_add'));

        $this->renderLayout();
    }

    public function deleteAction()
    {
        $setId = $this->getRequest()->getParam('id');
        try {
            Mage::getModel('eav/entity_attribute_set')
                ->setId($setId)
                ->delete();

            Mage::getSingleton('adminhtml/session')->addSuccess('Attribute Set Successfully Removed.');
            $this->getResponse()->setRedirect(Mage::getUrl('*/*/'));
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError('Error while deleting this set.');
            if ($referer = $this->getRequest()->getServer('HTTP_REFERER')) {
                $this->getResponse()->setRedirect($referer);
            }
        }
    }

    protected function _setTypeId()
    {
        
        Mage::register('entityType', 
            Mage::getModel('catalog/product')->getResource()->getConfig()->getId());
    }
}