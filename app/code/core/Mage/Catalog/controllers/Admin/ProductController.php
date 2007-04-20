<?php


class Mage_Catalog_ProductController extends Mage_Core_Controller_Admin_Action
{
    /**
     * Create new product dialog form
     *
     */
    public function createAction()
    {
        $form = Mage::createBlock('admin_catalog_product_create_option', 'product_create_option');
        $this->getResponse()->setBody($form->toString());
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

    public function filtersettingsAction()
    {
        $data = array(
        'totalRecords' => 2,
        'filters' => array(
                array(
                    'filter_id' => '0',
                    'filter_field' => 'name',
                    'filter_name' => 'Name',
                    'filter_type' => 'text',
                    'filter_comp' => array(
                        array(
                            'v' => 'eq',
                            'n' => 'Equal' 
                        ),
                        array(
                            'v' => 'neq',
                            'n' => 'Not Equal' 
                        ),                            
                        array(
                            'v' => 'like',
                            'n' => 'Like'
                        ),                            
                    )
                ),
                array(
                    'filter_id' => '1',
                    'filter_field' => 'price',
                    'filter_name'  => 'Price',
                    'filter_type' => 'number',
                    'filter_comp' => array (
                        array(
                            'v' => 'gt',
                            'n' => 'Greater Than' 
                        ),
                        array(
                            'v' => 'lt',
                            'n' => 'Lower Than' 
                        ),
                   )
               ),                            
           )      
       );
       $this->getResponse()->setBody(Zend_Json::encode($data));
    }

    /**
     * Product collection JSON
     *
     */
    public function gridDataAction()
    {
        $pageSize = $this->getRequest()->getPost('limit', 30);
        $prodCollection = Mage::getModel('catalog_resource','product_collection')
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('price')
            ->addAttributeToSelect('description')
            ->setPageSize($pageSize);

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
        
        $filters = $this->getRequest()->getPost('filters', false);
        if ($filters) {
            $prodCollection->addAdminFilters(Zend_Json::decode($filters));
        }

        $page = $this->getRequest()->getPost('start', 1);
        if ($page>1) {
            $page = $page/$pageSize+1;
        }

        $order = $this->getRequest()->getPost('sort', 'product_id');
        $dir   = $this->getRequest()->getPost('dir', 'desc');
        $prodCollection->setOrder($order, $dir);
        $prodCollection->setCurPage($page);
        $prodCollection->load();

        $arrGridFields = array('product_id', 'name', 'price', 'description');
        $data = $prodCollection->__toArray($arrGridFields);
        $this->getResponse()->setBody(Zend_Json::encode($data));
    }

    /**
     * Save product
     *
     */
    public function saveAction()
    {
        
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
    
    public function attributeSetTreeAction() {
        $rootNode = $this->getRequest()->getPost('node', false);
        $data  = array();
                
        if ($rootNode == 'croot') {
            $setCollection  = Mage::getModel('catalog_resource', 'product_attribute_set_collection');
            $setCollection->load();
            $arrSets = $setCollection->__toArray();
            foreach($arrSets['items'] as $item) {
                $data[] = array(
                    'text' => $item['code'],
                    'id' => 'set:' . $item['set_id'],
                    'iconCls' => 'set',
                    'cls' => 'set',
                    'draggable' => 'false', 
                    'allowDrop' => 'true',
                    'type' => 'typeSet',
                    'setId' => 'set:' . $item['set_id'],
                    'allowDelete' => 'true',
                    'expanded' => 'true',
                    'allowEdit' => 'true'
            	);
            }
        } elseif (preg_match('/^set:\d?$/', $rootNode)) {
            $setInfo = explode(':', $rootNode, 2);
            for($i = 0; $i < 3; $i++) {
                $data[] = array(
                    'text' => 'Group' . $i,
                    'id' => $rootNode.'/group:'.$i,
                    'iconCls' => 'group',
                    'cls' => 'group',
                    'allowDrop' => 'true',
                    'type' => 'typeGroup',
                    'setId' => 'set:' . $setInfo[1],
                    'allowDelete' => 'true',
                    'expanded' => 'true',
                    'allowEdit' => 'true'
            	);
            }
        } elseif (preg_match('/^set:\d?\/group:\d?$/', $rootNode)) {
            $tmpInfo = explode('/', $rootNode, 2);
            $setInfo = explode(':',$tmpInfo[0]);
            $groupInfo = explode(':',$tmpInfo[1]);
            for($i = 0; $i < 50; $i++) {
                $data[] = array(
                    'text' => 'Attribute' . $i,
                    'id' => $rootNode.'/attr:'.$i,
                    'iconCls' => 'attr',
                    'cls' => 'attr',
                    'leaf' => 'false',
                    'allowDrop' => 'false',
                    'allowChildren' => 'false',
                    'type' => 'typeAttr',                        
                    'setId' => 'set:' . $setInfo[1],
                    'allowDelete' => 'true',
                    'expanded' => 'true',                       
                    'allowEdit' => 'false'                
            	);
            }
        }
        $this->getResponse()->setBody(Zend_Json::encode($data));
    }
    
    public function attributeGroupListAction()
    {
        
    }

    /**
     * Product attribute set JSON
     *
     */
    public function attributeListAction()
    {
        $collection  = Mage::getModel('catalog_resource', 'product_attribute_collection');
        $order = $this->getRequest()->getPost('sort','attribute_code');
        $dir   = $this->getRequest()->getPost('dir','desc');
        $collection->setOrder($order, $dir);
        $collection->load();

        $arrGridFields = array('attribute_id', 'attribute_code', 'data_input', 'data_type', 'required');
        $this->getResponse()->setBody(Zend_Json::encode($collection->__toArray($arrGridFields)));
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
     * Save product attributes
     *
     */
    public function saveAttributesAction()
    {

    }


}
