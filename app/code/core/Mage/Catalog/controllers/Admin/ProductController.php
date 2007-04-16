<?php


class Mage_Catalog_ProductController extends Mage_Core_Controller_Admin_Action
{
    /**
     * Create new product dialog form
     *
     */
    public function newoptionAction()
    {
        $form = Mage::createBlock('admin_catalog_product_create_option', 'product_create_option');
        $this->getResponse()->setBody($form->toString());
        //echo '<form id="test" action="'.Mage::getBaseUrl().'/mage_catalog/product/card/">test form<input type="text" name="set" value="1"></form>';
    }

    /**
     * Product card structure (json)
     *
     */
    public function cardAction()
    {
        $card = Mage::createBlock('admin_catalog_product_card', 'product_card');
        $this->getResponse()->setBody($card->toJson());
    }

    /**
     * Attributes group form
     *
     */
    public function formAction()
    {
        $form = Mage::createBlock('admin_catalog_product_form', 'product_form');
        $this->getResponse()->setBody($form->toString());
    }

    /**
     * Related products control panel
     *
     */
    public function relatedProductsAction()
    {
        $block = Mage::createBlock('tpl', 'related_products_panel');
        $block->setViewName('Mage_Catalog', 'Admin/product/related_products.phtml');
        $this->getResponse()->setBody($block->toString());
    }

    /**
     * GRid
     *
     */
    public function gridAction()
    {
        #Mage_Core_Block::loadJsonFile('Mage/Catalog/Admin/product/initGridLayout.json', 'mage_catalog');
    }

    /**
     * Product collection JSON
     *
     */
    public function gridDataAction()
    {
        $pageSize = isset($_POST['limit']) ? $_POST['limit'] : 30;
        $prodCollection = Mage::getModel('catalog_resource','product_collection');

        $prodCollection->addAttributeToSelect('name', 'varchar');
        $prodCollection->addAttributeToSelect('price', 'decimal');
        $prodCollection->addAttributeToSelect('description', 'text');

        $prodCollection->setPageSize($pageSize);

        if ($categoryId = $this->getRequest()->getParam('category')) {

            $tree = Mage::getModel('catalog','category_tree');
            $data = $tree->getLevel($categoryId, 0);

            if (empty($data)) {
                $arrCategories = array($categoryId);
            }
            else {
                $arrCategories = array();
                $prodCollection->distinct(true);
                foreach ($data as $node) {
                    $arrCategories[] = $node->getId();
                }
            }
            $prodCollection->addCategoryFilter($arrCategories);
        }



        $page = isset($_POST['start']) ? $_POST['start']/$pageSize+1 : 1;

        $order = isset($_POST['sort']) ? $_POST['sort'] : 'product_id';
        $dir   = isset($_POST['dir']) ? $_POST['dir'] : 'desc';
        $prodCollection->setOrder($order, $dir);
        $prodCollection->setCurPage($page);
        $prodCollection->load();

        $arrGridFields = array('product_id', 'name', 'price', 'description');
        $data = $prodCollection->__toArray($arrGridFields);
        $this->getResponse()->setBody(Zend_Json::encode($data));
    }

    /**
     * Save product attributes
     *
     */
    public function saveAttributesAction()
    {

    }

    /**
     * Save product
     *
     */
    public function saveAction()
    {
        $validator = new Mage_Catalog_Validate_Product($_POST);
        if ($validator->isValid()) {
            Mage::log('begin product saving');
            $productModel = Mage::getModel('catalog_resource', 'product');

            if ($productId = $validator->getProductId()) {
                if ($productModel->update($validator->getData(), $productId)) {

                }
                else {

                }
            }
            else {
                if ($productId = $productModel->insert($validator->getData())) {

                }
                else {

                }
            }
            Mage::log('end product saving');
        }
        else {
            Mage::log($validator->getMessage());
            $this->getResponse()->setBody($validator->getMessage('json'));
        }
    }

    /////////////////////////////////
    // Attributes

    /**
     * Product attribute set JSON
     *
     */
    public function attributeSetListAction()
    {
        $setCollection  = Mage::getModel('catalog_resource', 'product_attribute_set_collection');
        $setCollection->load();
        $arrSets = $setCollection->__toArray();

        $this->getResponse()->setBody(Zend_Json::encode($arrSets));
    }

    public function attributeSetPropertiesAction() {
        $arrSets = array ("totalRecords"=> 2,
            "items" => array(
                array(
                    "id" => 0,
                    "name" => "Name",
                    "value" => "Simple"
                ),
                array(
                    "id" => 1,
                    "name" => "Active",
                    "value" => false
                ),
                array(
                    "id" => 2,
                    "name" => "Type",
                    "value" => "1"
                )
            )
        );
        $this->getResponse()->setBody(Zend_Json::encode($arrSets));
    }

    /**
     * Product attribute set JSON
     *
     */
    public function attributeListAction()
    {
        $collection  = Mage::getModel('catalog_resource', 'product_attribute_collection');
        $order = isset($_POST['sort']) ? $_POST['sort'] : 'attribute_code';
        $dir   = isset($_POST['dir']) ? $_POST['dir'] : 'desc';
        $collection->setOrder($order, $dir);
        $collection->load();

        $arrGridFields = array('attribute_id', 'attribute_code', 'data_input', 'data_type', 'required');
        $this->getResponse()->setBody(Zend_Json::encode($collection->__toArray($arrGridFields)));
    }
    
    public function filtersettingsAction()
    {
        $data = array(
        'totalRecords' => 2,
        'filters' => array(
                0 => array(
                    'filter_id' => '0',
                    'filter_field' => 'name',
                    'filter_name' => 'Name',
                    'filter_type' => 'text',
                    'filter_comp' => array(
                        0 => array(
                            'v' => 'eq',
                            'n' => 'Equal' 
                        ),
                        1 => array(
                            'v' => 'neq',
                            'n' => 'Not Equal' 
                        ),                            
                            2 => array(
                            'v' => 'like',
                            'n' => 'Like'
                        )                            
                    )
                ),
                1 => array(
                    'filter_id' => '1',
                    'filter_field' => 'create_date',
                    'filter_name'  => 'Added Date',
                    'filter_type' => 'date',
                    'filter_comp' => array (
                         0 => array(
                            'v' => 'gt',
                            'n' => 'Greater Than' 
                        ),
                        1 => array(
                            'v' => 'lt',
                            'n' => 'Lower Than' 
                        )
                   )
               )                            
           )      
       );
       $this->getResponse()->setBody(Zend_Json::encode($data));
    }

}
