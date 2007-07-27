<?php
/**
 * description
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Alexander Stadnitski <alexander@varien.com>
 */

class Mage_Adminhtml_Catalog_Product_GroupController extends Mage_Adminhtml_Controller_Action
{
    public function saveAction()
    {
        $model = Mage::getModel('eav/entity_attribute_group');

        $model->setAttributeGroupName($this->getRequest()->getParam('attribute_group_name'))
              ->setAttributeSetId($this->getRequest()->getParam('attribute_set_id'));

        if( $model->itemExists() ) {
            Mage::getSingleton('adminhtml/session')->addError('Error while saving this group. Group with the same name already exists.');
        } else {
            try {
                $model->save();
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError('Error while saving this group. Please try again later.');
            }
        }
        $this->getResponse()->setRedirect((Mage::getUrl('*/catalog_product_set/edit', array('id' => $this->getRequest()->getParam('attribute_set_id')))));
    }
}