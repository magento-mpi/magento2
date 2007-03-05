<?php


#include_once 'Ecom/Core/Controller/Zend/Action.php';

class Ecom_Catalog_CategoryController extends Ecom_Core_Controller_Action {

    function indexAction() 
    {

    }

    function viewAction() 
    {
        // Valid category id
        if ($categoryId = $this->_getId()) {
            $category = Ecom::getModel('catalog', 'categories')->getNode($categoryId);

            $productInfoBlock = Ecom::createBlock('catalog_category_view', 'category.products', array('category'=>$category));
            $productInfoBlock->loadData($this->getRequest());

            Ecom::getBlock('content')->append($productInfoBlock);
        }
        else { // TODO: forvard to error action
            echo 'Category id is not defined';
        }


    }

    function fillAction()
    {
        set_time_limit(0);
        /**
         * @var $db Zend_Db_Adapter_Abstract
         */
        $db = Ecom_Core_Resource::getResource('catalog_write')->getConnection();

        for ($i=1;$i<10000;$i++) {
            $base = array();
            $base['category_id']   = rand(3,23);
            $base['weight']        = rand(1, 1000);
            $base['price']         = rand(10, 200);
            $base['base_prop1']    = rand(100, 500);
            $base['base_prop2']    = rand(500, 1000);

            $db->insert('catalog_product', $base);
            $product_id = $db->lastInsertId();

            for ($j=1;$j<=5;$j++) {
                $ext = array();
                $ext['product_id']     = $product_id;
                $ext['website_id']     = $j;
                $ext['name']           = 'Product ' . $j . '_' . $product_id;
                $ext['description']    = $j . '_' . $product_id . ' - Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aliquam fringilla. In hac habitasse platea dictumst. Pellentesque erat sapien, condimentum ac, dictum in, faucibus sit amet, augue. Pellentesque convallis. Duis in mauris id metus ornare mattis. Vestibulum placerat. Pellentesque quam risus, venenatis fermentum, molestie eu, feugiat sit amet, metus. Suspendisse eleifend nulla eu elit. Vivamus eu mauris. Pellentesque sed odio. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae;';
                $ext['ext_prop1']      = 'text of property 1 :' . $j . '_' . $product_id;
                $ext['ext_prop2']      = date('Y-m-d');
                $ext['ext_prop3']      = date('Y-m-d H:i:s');
                $ext['ext_prop4']      = 'text of property 4 :' . $j . '_' . $product_id;
                $ext['ext_prop5']      = 'text of property 5 :' . $j . '_' . $product_id;
                $ext['ext_prop6']      = rand(1, 10000);
                $ext['ext_prop7']      = 'text of property 7 :' . $j . '_' . $product_id;
                $ext['ext_prop8']      = rand(1, 10000);
                $db->insert('catalog_product_extension', $ext);
            }
        }
    }

    protected function _getId()
    {
        return $this->getRequest()->getParam('id');
    }
}
