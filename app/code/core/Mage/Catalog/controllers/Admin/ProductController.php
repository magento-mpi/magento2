<?php


class Mage_Catalog_ProductController extends Mage_Core_Controller_Admin_Action
{
    /**
     * Product collection JSON
     */
    public function gridDataAction()
    {
        $pageSize = $this->getRequest()->getPost('limit', 30);
        $websiteId = $this->getRequest()->getParam('website');
        $prodCollection = Mage::getModel('catalog_resource','product_collection')
            ->setWebsiteId($websiteId)
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('price')
            ->addAttributeToSelect('description')
            ->setPageSize($pageSize);

        if ($categoryId = $this->getRequest()->getParam('category')) {

            $nodes = Mage::getModel('catalog_resource','category_tree')
                        ->load($categoryId, 10)
                        ->getNodes();

            if ($nodes->count()) {
                
                $arrCategories = array();
                $prodCollection->distinct(true);
                foreach ($nodes as $node) {
                    $arrCategories[] = $node->getId();
                }
            }
            else {
                $arrCategories = array($categoryId);
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
     * List allowed websites for category
     *
     */
    public function allowWebsitesAction()
    {
        $categoryId = (int) $this->getRequest()->getParam('category', false);

        $category = Mage::getModel('catalog', 'category')->setCategoryId($categoryId);
        $websites = $category->getWebsites();
        
        $data = array();
        foreach ($websites as $website) {
            $data[] = array(
                'value' => $website->getWebsiteId(),
                'text'  => $website->getWebsiteCode()
            );
        }
        
        $this->getResponse()->setBody(Zend_Json::encode($data));
    }
        
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
        $block->setTemplate('catalog/product/related_products.phtml');
        $this->getResponse()->setBody($block->toString());
    }
    
    public function imagesAction()
    {
        $id = $this->getRequest()->getParam('product', -1);
        $product = Mage::getModel('catalog', 'product')->load($id);
        $block = Mage::createBlock('tpl', 'root');
        if ($this->getRequest()->getParam('iframe')) {
            $block->setTemplate('catalog/product/images/iframe.phtml');
            $block->assign('imagePreviewUrl', $product->getImageUrl());
            $block->assign('uploadAction', Mage::getBaseUrl()."mage_catalog/product/upload/product/$id/");
        } else {
            $block->setTemplate('catalog/product/images.phtml');
            $block->assign('iframeSrc', Mage::getBaseUrl()."mage_catalog/product/images/product/$id/iframe/true/");
        }
        $this->getResponse()->setBody($block->toString());
    }
    
    public function uploadAction()
    {
        if (isset($_FILES['image'])) {
            $id = $this->getRequest()->getParam('product', -1);
            $fileDir = Mage::getBaseDir('media').DS.'catalog'.DS.'product'.DS.($id%997);
            if (!file_exists($fileDir)) {
                mkdir($fileDir, 0777, true);
                chmod($fileDir, 0777);
            }
            $fileName = $id.'.orig.'.$_FILES['image']['name'];
            move_uploaded_file($_FILES['image']['tmp_name'], $fileDir.DS.$fileName);
            chmod($fileDir.DS.$fileName, 0777);
            
            Mage::getModel('catalog', 'product')->load($id)->setImage($_FILES['image']['name'])->save();
        }
        $this->getResponse()->setHeader('Location', Mage::getBaseUrl()."mage_catalog/product/images/product/$id/iframe/true/");
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
            $setCollection  = Mage::getModel('catalog_resource', 'product_attribute_set_collection')
                ->load();
                
            foreach($setCollection as $set) {
                $data[] = array(
                    'text'      => $set->getCode(),
                    'id'        => 'set:' . $set->getSetId(),
                    'iconCls'   => 'set',
                    'cls'       => 'set',
                    'draggable' => false, 
                    'allowDrop' => true,
                    'type'      => 'set',
                    'allowDelete' => true,
                    'expanded'  => false,
                    'allowEdit' => true,
                    'setId'     => $set->getSetId(),
                );
            }
        } elseif (preg_match('/^set:([0-9]+)$/', $rootNode, $matches)) {
            $setId = $matches[1];
            
            $groups = Mage::getModel('catalog', 'product_attribute_set')
                ->load($setId)
                ->getGroups();
                
            $attributes = Mage::getModel('catalog_resource', 'product_attribute_collection')
                ->addSetFilter($setId)
                ->loadData();
                
            foreach ($attributes as $attribute) {
                $attrs[$attribute->getGroupId()][] = array(
                    'text'      => $attribute->getCode(),
                    'id'        => $rootNode.'/group:'.$attribute->getGroupId().'/attr:'.$attribute->getId(),
                    'iconCls'   => 'attr',
                    'cls'       => 'attr',
                    'leaf'      => true,
                    'allowDrop' => false,
                    'allowChildren' => false,
                    'type'      => 'attribute',                        
                    'allowDelete' => true,
                    'expanded'  => false,                       
                    'allowEdit' => false,
                    'setId'     => $setId,
                    'groupId'   => $attribute->getGroupId(),
                    'attributeId' => $attribute->getId(),
                );
            }
            
            foreach ($groups as $group) {
                $isGeneral = strtolower($group->getCode())==='general';
                $data[] = array(
                    'text'      => $group->getCode(),
                    'id'        => $rootNode.'/group:'.$group->getId(),
                    'iconCls'   => 'group',
                    'cls'       => 'group',
                    'allowDrop' => true,
                    'type'      => 'group',
                    'isGeneral' => $isGeneral,
                    'allowDelete' => !$isGeneral,
                    'expanded'  => true,
                    'allowEdit' => !$isGeneral,
                    'allowDrag' => !$isGeneral,
                    'groupId'   => $group->getId(),
                    'setId'     => $setId,
                    'children'  => isset($attrs[$group->getId()]) ? $attrs[$group->getId()] : null,
                );
            }
        }
        $this->getResponse()->setBody(Zend_Json::encode($data));
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

        //$arrGridFields = array('attribute_id', 'attribute_code', 'data_input', 'data_type', 'required');
        $data = $collection->__toArray();
        
        $data['elements'] = array(
            'input_type' => array(
                'type'      => 'combobox',
                'default'   => 'text',
                'values'=> array(
                    array(
                        'value' => 'text',
                        'text'  => 'Text'
                    ),
                    array(
                        'value' => 'select',
                        'text'  => 'Combobox'
                    ),
                    array(
                        'value' => 'image',
                        'text'  => 'Image'
                    ),
                )
            ),
            'attribute_code' => array(
                'type'      => 'textfield',
                'default'   => 'uniq code'
            ),
            'searchable' => array(
                'type'      => 'checkbox',
                'default'   => 0
            )
        );
        
        $this->getResponse()->setBody(Zend_Json::encode($data));
    }
    
    /**
     * Save product attributes
     *
     */
    public function saveAttributesAction()
    {

    }

    public function addGroupAttributesAction() {
        $res = array(
            'error' => 0,
            'errorNodes' => array()
        );
        $groupId = $this->getRequest()->getPost('groupId', false);
        $arrAttributes = Zend_Json::decode($this->getRequest()->getPost('attributes','[]'));
        
        $group = Mage::getModel('catalog', 'product_attribute_group')->load($groupId);
        foreach ($arrAttributes as $attributeId) {
            $attribute = Mage::getModel('catalog', 'product_attribute')->setAttributeId($attributeId);
            try {
                $group->addAttribute($attribute);
            }
            catch (Exception $e){
                $res['error'] = 1;
                $res['errorMessage'] = $e->getMessage();
                $res['errorNodes'][] = $attributeId;
            }
        }
        
        $this->getResponse()->setBody(Zend_Json::encode($res));
    }
    
    /**
     * Remove atribute set/group tree element
     *
     */
    public function removElementAction() {
        $element = $this->getRequest()->getPost('element');
        $res = array('error' => 0);
        try {
            switch ($element) {
                case 'set':
                    Mage::getModel('catalog', 'product_attribute_set')
                       ->setSetId($this->getRequest()->getPost('setId', false))
                       ->delete();
                    break;
                case 'group':
                    Mage::getModel('catalog', 'product_attribute_group')
                       ->setGroupId($this->getRequest()->getPost('groupId', false))
                       ->delete();
                    break;
                case 'attribute':
                    $attribute = Mage::getModel('catalog', 'product_attribute')
                        ->setAttributeId($this->getRequest()->getPost('attributeId', false));
                        
                    Mage::getModel('catalog', 'product_attribute_group')
                       ->load($this->getRequest()->getPost('groupId', false))
                       ->removeAttribute($attribute);
                    break;
                default:
                    break;
            }
        }
        catch (Exception $e){
            $res = array(
                'error' => 1,
                'errorMessage' => $e->getMessage()
            );
        }
        
        $this->getResponse()->setBody(Zend_Json::encode($res));
    }
    
    /**
     * Save attribute group
     *
     */
    public function saveGroupAction()
    {
        $res = array('error' => 0);
        $groupId    = (int) $this->getRequest()->getPost('id', false);
        $setId      = (int) $this->getRequest()->getPost('setId', false);
        $groupCode  = $this->getRequest()->getPost('code', false);
        
        $group = Mage::getModel('catalog', 'product_attribute_group')
            ->setGroupId($groupId)
            ->setCode($groupCode)
            ->setSetId($setId);
            
        try {
            $group->save();
            $res['groupId'] = $group->getId();
        }
        catch (Exception $e){
            $res = array(
                'error' => 1,
                'errorMessage' => $e->getMessage()
            );
        }
        $this->getResponse()->setBody(Zend_Json::encode($res));
    }
    
    /**
     * Save product attribute set
     *
     */
    public function saveSetAction() {
        $res = array('error' => 0);
        $setId      = (int) $this->getRequest()->getPost('id', false);
        $setCode    = $this->getRequest()->getPost('code', false);
        $groupCode  = $this->getRequest()->getPost('groupCode', false);
        
        $set = Mage::getModel('catalog', 'product_attribute_set')
            ->setSetId($setId)
            ->setCode($setCode);
            
        try {
            $set->save();
            $res['setId'] = $set->getId();
            if (!$setId) {
                $group = Mage::getModel('catalog', 'product_attribute_group')
                    ->setCode('General')
                    ->setSetId($set->getId())
                    ->save();
                $res['groupId'] = $group->getId();
            }
        }
        catch (Exception $e){
            $res = array(
                'error' => 1,
                'errorMessage' => $e->getMessage()
            );
        }
        
        $this->getResponse()->setBody(Zend_Json::encode($res));
    }
    
    public function attributedeleteAction() {
            $res = array(
                'error' => 0
            );
       $this->getResponse()->setBody(Zend_Json::encode($res));
    }
}
