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
        echo "debug: <pre>";
        print_r($data);
        echo "</pre>";
        if( $data['attribute_set_name'] ) {
            $model = Mage::getModel('eav/entity_attribute_set');
            $model->setId($this->getRequest()->getParam('id'))
                  ->setAttributeSetName($data['attribute_set_name'])
                  ->setEntityTypeId(Mage::registry('entityType'))
                  ->save();
        }

        if( $data['attributes'] || $data['not_attributes'] ) {
            $model = Mage::getModel('eav/entity_attribute');
            $model->setAttributesArray( ($data['attributes']) ? $data['attributes'] : false )
              ->setNotAttributesArray( ($data['not_attributes']) ? $data['not_attributes'] : false )
              ->setEntityTypeId(Mage::registry('entityType'))
              ->setSetId($this->getRequest()->getParam('id'))
              ->saveAttributes();
        }

        /*
        if( $data['removeGroups'] ) {
            $model = Mage::getModel('eav/entity_attribute_group');
            $model->setGroupsArray($data['attribute_set_name'])
                  ->deleteGroups();
        }
        */
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