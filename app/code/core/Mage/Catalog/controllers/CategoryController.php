<?php




class Mage_Catalog_CategoryController extends Mage_Core_Controller_Front_Action {

    function indexAction() 
    {

    }

    function viewAction() 
    {
        // Valid category id
        if ($categoryId = $this->_getId()) {
            $category = Mage::getModel('catalog', 'categories')->getNode($categoryId);

            $productInfoBlock = Mage::createBlock('catalog_category_view', 'category.products', array('category'=>$category));
            $productInfoBlock->loadData($this->getRequest());

            Mage::getBlock('content')->append($productInfoBlock);
        }
        else { // TODO: forvard to error action
            echo 'Category id is not defined';
        }


    }

    function fillAction()
    {
        set_time_limit(0);
        return false;
        /**
         * @var $db Zend_Db_Adapter_Abstract
         */
        $db = Mage_Core_Resource::getResource('catalog_write')->getConnection();

        for ($i=1;$i<10000;$i++) {
            $base = array();
            $base['category_id']   = rand(3,23);

            $db->insert('catalog_product', $base);
            $product_id = $db->lastInsertId();

            for ($website=1;$website<=2;$website++) {
                $attr = array();
                $attr['product_id']     = $product_id;
                $attr['attribute_id']   = 1;
                $attr['website_id']     = $website;
                $attr['attribute_value']= 'Product #' . $product_id;
                $db->insert('catalog_product_attribute_varchar', $attr);

                $attr = array();
                $attr['product_id']     = $product_id;
                $attr['attribute_id']   = 2;
                $attr['website_id']     = $website;
                $attr['attribute_value']= 'Product #' . $product_id . ' description';
                $db->insert('catalog_product_attribute_text', $attr);

                $attr = array();
                $attr['product_id']     = $product_id;
                $attr['attribute_id']   = 3;
                $attr['website_id']     = $website;
                $attr['attribute_value']= rand(1,10000);
                $db->insert('catalog_product_attribute_decimal', $attr);

                $attr = array();
                $attr['product_id']     = $product_id;
                $attr['attribute_id']   = 4;
                $attr['website_id']     = $website;
                $attr['attribute_value']= rand(1,10000);
                $db->insert('catalog_product_attribute_decimal', $attr);

                $attr = array();
                $attr['product_id']     = $product_id;
                $attr['attribute_id']   = 5;
                $attr['website_id']     = $website;
                $attr['attribute_value']= rand(1,100);
                $db->insert('catalog_product_attribute_int', $attr);

                $attr = array();
                $attr['product_id']     = $product_id;
                $attr['attribute_id']   = 6;
                $attr['website_id']     = $website;
                $attr['attribute_value']= 'Product #' . $product_id;
                $db->insert('catalog_product_attribute_varchar', $attr);

                $attr = array();
                $attr['product_id']     = $product_id;
                $attr['attribute_id']   = 7;
                $attr['website_id']     = $website;
                $attr['attribute_value']= 200;
                $db->insert('catalog_product_attribute_int', $attr);

                $attr = array();
                $attr['product_id']     = $product_id;
                $attr['attribute_id']   = 8;
                $attr['website_id']     = $website;
                $attr['attribute_value']= 300;
                $db->insert('catalog_product_attribute_int', $attr);

            }
        }
    }

    protected function _getId()
    {
        return $this->getRequest()->getParam('id');
    }
}
