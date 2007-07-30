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

        $this->_addBreadcrumb(__('Catalog'), __('Catalog Title'));
        $this->_addBreadcrumb(__('Manage Product Sets'), __('Manage Product Sets Title'));

        $this->_addContent($this->getLayout()->createBlock('adminhtml/catalog_product_attribute_set_toolbar_main'));
        $this->_addContent($this->getLayout()->createBlock('adminhtml/catalog_product_attribute_set_grid'));

        $this->renderLayout();
    }

    public function editAction()
    {
        $this->_setTypeId();

        $this->loadLayout('baseframe');
        $this->_setActiveMenu('catalog/sets');
        $this->getLayout()->getBlock('root')->setCanLoadExtJs(true);

        $this->_addBreadcrumb(__('Catalog'), __('Catalog Title'));
        $this->_addBreadcrumb(__('Manage Product Sets'), __('Manage Product Sets Title'));

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
        if( $this->getRequest()->getParam('gotoEdit') != 1 ) {
            $data = Zend_Json_Decoder::decode($this->getRequest()->getPost('data'));

            $modelSet = Mage::getModel('eav/entity_attribute_set');
            $modelGroupArray = array();
            $modelAttributeArray = array();

            if( $data['groups'] ) {
                foreach( $data['groups'] as $group ) {
                    $modelGroup = Mage::getModel('eav/entity_attribute_group');
                    $modelGroup->setId($group[0])
                               ->setAttributeGroupName($group[1])
                               ->setAttributeSetId($this->getRequest()->getParam('id'));

                    if( $data['attributes'] ) {
                        foreach( $data['attributes'] as $key => $attribute ) {
                            if( $attribute[1] == $group[0] ) {
                                $modelAttribute = Mage::getModel('eav/entity_attribute');
                                $modelAttribute->setId($attribute[0])
                                               ->setAttributeGroupId($attribute[1])
                                               ->setAttributeSetId($this->getRequest()->getParam('id'))
                                               ->setEntityTypeId(Mage::registry('entityType'))
                                               ->setSortOrder($attribute[2]);
                                $modelAttributeArray[] = $modelAttribute;
                            }
                        }
                        $modelGroup->setAttributes($modelAttributeArray);
                        $modelAttributeArray = array();
                    }
                    $modelGroupArray[] = $modelGroup;
                }
                $modelSet->setGroups($modelGroupArray);
            }

            if( $data['not_attributes'] ) {
                $modelAttributeArray = array();
                foreach( $data['not_attributes'] as $key => $attributeId ) {
                    $modelAttribute = Mage::getModel('eav/entity_attribute');

                    $modelAttribute->setId($attributeId)
                                   ->setAttributeSetId($this->getRequest()->getParam('id'))
                                   ->setSortOrder($key)
                                   ->setEntityTypeId(Mage::registry('entityType'));
                    $modelAttributeArray[] = $modelAttribute;
                }
                $modelSet->setRemoveAttributes($modelAttributeArray);
            }

            if( $data['removeGroups'] ) {
                $modelGroupArray = array();
                foreach( $data['removeGroups'] as $key => $groupId ) {
                    $modelGroup = Mage::getModel('eav/entity_attribute_group');
                    $modelGroup->setId($groupId);

                    $modelGroupArray[] = $modelGroup;
                }
                $modelSet->setRemoveGroups($modelGroupArray);
            }

            $modelSet->setId($this->getRequest()->getParam('id'))
                ->setAttributeSetName($data['attribute_set_name'])
                ->setEntityTypeId(Mage::registry('entityType'));
        } else {
            $modelSet = Mage::getModel('eav/entity_attribute_set');
            $modelSet->setAttributeSetName($this->getRequest()->getParam('attribute_set_name'))
                ->setEntityTypeId(Mage::registry('entityType'));
        }

        try {
            $modelSet->save();
            Mage::getSingleton('adminhtml/session')->addSuccess('Attribute set successfully saved.');
            if( $this->getRequest()->getParam('gotoEdit') == 1 ) {
                $this->getResponse()->setRedirect(Mage::getUrl('*/*/edit', array('id' => $modelSet->getId())));
            }
        } catch (Exception $e) {
            if( $this->getRequest()->getParam('gotoEdit') == 1 ) {
                Mage::getSingleton('adminhtml/session')->addError('Error while saving this set. May be set with the same name already exists.');
                if ($referer = $this->getRequest()->getServer('HTTP_REFERER')) {
                    $this->getResponse()->setRedirect($referer);
                }
            } else {
                Mage::getSingleton('adminhtml/session')->addError('Error while saving this set.');
                die($e);
            }
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
        Mage::register('entityType', 10);
    }
}