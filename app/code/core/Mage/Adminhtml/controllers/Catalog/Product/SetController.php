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

        try {
            $modelSet->save();
        } catch (Exception $e) {
            die($e);
        }
    }

    public function removeAction()
    {
        #
    }

    protected function _setTypeId()
    {
        Mage::register('entityType', 10);
    }
}