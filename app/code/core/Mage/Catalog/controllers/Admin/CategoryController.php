<?php
/**
 * Admin category controller
 *
 * @package    Ecom
 * @subpackage Catalog
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Catalog_CategoryController extends Mage_Core_Controller_Admin_Action
{
    /**
     * New category layout
     *
     */
    public function newAction()
    {
        Mage_Core_Block::loadJsonFile('Mage/Catalog/Admin/category/newCategoryLayout.json', 'mage_catalog');
    }
    
    public function saveAction()
    {
        echo 'C save';
    }

    function removeAction() {
        $obj = Mage::getModel('catalog', 'category_tree')->getObject();
        if (intval($_GET['id'])) {
            $obj->removeNode($_GET['id']);
        }
    }

    function moveAction() {
        $obj = Mage::getModel('catalog', 'category_tree')->getObject();
        if (intval($_POST['id']) && intval($_POST['pid'])) {
            $obj->moveNode($_POST['id'], $_POST['pid']);
        }
    }

    function deleteAction() {
        $id = $this->getRequest()->getParam('id', null);
        if (!empty($id)) {
            Mage::getModel('catalog', 'category_tree')->deleteNode($id);
        }
    }

}
