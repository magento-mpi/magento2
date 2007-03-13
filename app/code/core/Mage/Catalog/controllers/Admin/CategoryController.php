<?php


class Mage_Catalog_CategoryController extends Mage_Core_Controller_Admin_Action
{

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

}
