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
            ->setWebsiteId(Mage::registry('website')->getId())
            ->distinct(true)
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('price')
            ->addAttributeToSelect('description')
            ->setPageSize($pageSize);

        if (($categoryId = (int) $this->getRequest()->getParam('category')) && $categoryId != 1) {

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
        $form = $this->getLayout()->createBlock('admin_catalog_product_create_option', 'product_create_option');
        $this->getResponse()->setBody($form->toHtml());
    }
    
    public function deleteAction()
    {
        
    }

    /**
     * Product card structure (json)
     *
     */
    public function cardAction()
    {
        $card = $this->getLayout()->createBlock('admin_catalog_product_card', 'product_card');
        $this->getResponse()->setBody($card->toJson());
    }
    
    public function viewAction()
    {
        $product = Mage::getModel('catalog', 'product')->load($this->getRequest()->getParam('product'));
        $block = $this->getLayout()->createBlock('tpl', 'product.view')
            ->setTemplate('catalog/product/view.phtml')
            ->assign('product', $product);
        $this->getResponse()->setBody($block->toHtml());
    }

    /**
     * Attributes group form
     *
     */
    public function formAction()
    {
        $form = $this->getLayout()->createBlock('admin_catalog_product_form', 'product_form');
        $this->getResponse()->setBody($form->toHtml());
    }

    /**
     * Related products control panel
     *
     */
    public function relatedProductsAction()
    {
        $productId = $this->getRequest()->getParam('product');
        $block = $this->getLayout()->createBlock('tpl', 'related_products_panel')
            ->setTemplate('catalog/product/related_products.phtml')
            ->assign('postAction', Mage::getBaseUrl().'mage_catalog/product/save/product/'.$productId.'/');
        $this->getResponse()->setBody($block->toHtml());
    }
    
    public function imagesAction()
    {
        $id = $this->getRequest()->getParam('product', -1);
        $product = Mage::getModel('catalog', 'product')->load($id);
        $block = $this->getLayout()->createBlock('tpl', 'root');
        if ($this->getRequest()->getParam('iframe')) {
            $block->setTemplate('catalog/product/images/iframe.phtml');
            $block->assign('imagePreviewUrl', $product->getImageUrl());
            $block->assign('uploadAction', Mage::getBaseUrl()."mage_catalog/product/upload/product/$id/");
        } else {
            $block->setTemplate('catalog/product/images.phtml');
            $block->assign('iframeSrc', Mage::getBaseUrl()."mage_catalog/product/images/product/$id/iframe/true/");
        }
        $this->getResponse()->setBody($block->toHtml());
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
        $res = array('error' => 0);
        $product = Mage::getModel('catalog', 'product')
            ->setProductId($this->getRequest()->getParam('product'))
            ->setSetId($this->getRequest()->getParam('set_id', 1))
            ->setTypeId($this->getRequest()->getParam('type_id', 1))
            ->setAttributes($this->getRequest()->getParam('attribute'));
        
        if ($relatedProducts = $this->getRequest()->getParam('related_products')) {
            $product->setRelatedLinks(explode(',',$relatedProducts));
        }
            
        try {
            $product->save();
            $res['product_id'] = $product->getId();
        }
        catch (Exception $e){
            $res = array(
                'error' => 1,
                'errorMessage' => $e->getMessage()
            );
        }
        $this->getResponse()->setBody(Zend_Json::encode($res));
    }
//////////////////////////////////////////////////////////////////////////////////////////////
// Product links

    public function relatedListAction()
    {
        $data = array();
        $productId = $this->getRequest()->getParam('product');
        if ($productId) {
            $relatedLinks = Mage::getModel('catalog', 'product')
                ->load($productId)
                ->getRelatedProducts();
            
            foreach ($relatedLinks as $link) {
                $data[] = array(
                    'product_id' => $link->getProduct()->getId(),
                    'name'       => $link->getProduct()->getName(),
                    'price'      => $link->getProduct()->getPrice(),
                    'description'=> $link->getProduct()->getDescription(),
                );
            }
        }
        $this->getResponse()->setBody(Zend_Json::encode(array('totalRecords'=>$relatedLinks->getSize(), 'items'=>$data)));
    }
    
    public function bundleListAction()
    {
        $data = array();
        $productId = $this->getRequest()->getParam('product');
        if ($productId) {
            $linkedProducts = Mage::getModel('catalog', 'product')
                ->load($productId)
                ->getLinkedProducts('bundle');
            
            foreach ($linkedProducts as $product) {
                $data[] = array(
                    'product_id' => $product->getId()
                );
            }
        }
        $this->getResponse()->setBody(Zend_Json::encode($data));
    }
    
    public function superListAction()
    {
        $data = array();
        $productId = $this->getRequest()->getParam('product');
        if ($productId) {
            $linkedProducts = Mage::getModel('catalog', 'product')
                ->load($productId)
                ->getLinkedProducts('super');
            
            foreach ($linkedProducts as $product) {
                $data[] = array(
                    'product_id' => $product->getId()
                );
            }
        }
        $this->getResponse()->setBody(Zend_Json::encode($data));
    }

//////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////
    // Attributes

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
            
            $set = Mage::getModel('catalog', 'product_attribute_set')->load($setId);
            $groups = $set->getGroups();
            $attributes = $set->getAttributes();
                
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
    
    public function moveAttributeInSetAction()
    {
        if (!$this->getRequest()->isPost()) {
            return;
        }
        
        $p = $this->getRequest()->getPost();
        
        if (preg_match('#group:([0-9]+)/attr:([0-9]+)#', $p['id'], $match)) {
            $fromGroupId = $match[1];
            $attribute = Mage::getModel('catalog', 'product_attribute')->load($match[2]);
        } else {
            return;
        }
        
        if (preg_match('#set:([0-9]+)/group:([0-9]+)#', $p['pid'], $match)) {
            $setId = (int)$match[1];
            $toGroupId = (int)$match[2];
        } else {
            return;
        }
        
        if ($p['aid']=='0') {
            $position = 1;
        } elseif (preg_match('#attr:([0-9]+)#', $p['aid'], $match)) {
            $sibling = Mage::getModel('catalog', 'product_attribute')->load($match[1]);
            $position = $sibling->getPositionInGroup($toGroupId)+($p['point']=='above' ? -1 : 1);
        } else {
            return;
        }
        

        $set = Mage::getModel('catalog', 'product_attribute_set')->load($setId);
        $set->moveAttribute($attribute, $fromGroupId, $toGroupId, $position);
        
        $data = array('error'=>0);
        $this->getResponse()->setBody(Zend_Json::encode($data));
    }
    
    /**
     * Product attribute set JSON
     *
     */
    public function attributeListAction()
    {
        $order = $this->getRequest()->getPost('sort', 'attribute_id');
        $dir   = $this->getRequest()->getPost('dir', 'asc');
        $collection  = Mage::getModel('catalog_resource', 'product_attribute_collection')
            ->setOrder($order, $dir)
            ->load();
            
        foreach ($collection->getItems() as $item) {
            $item->setRequired((boolean)$item->getRequired());
            $item->setDeletable((boolean)$item->getDeletable());
            $item->setSearchable((boolean)$item->getSearchable());
            $item->setComparable((boolean)$item->getComparable());
            $item->setMultiple((boolean)$item->getMultiple());
        }

        $this->getResponse()->setBody(Zend_Json::encode($collection->__toArray()));
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
                        ->load($this->getRequest()->getPost('attributeId', false));
                    
                    if (!$attribute->isDeletable()) {
                        throw new Exception('Attribute is not deletable');
                    }
                            
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
            
            // Create new set
            if (!$setId) {
                // Create default "General" group
                $group = Mage::getModel('catalog', 'product_attribute_group')
                    ->setCode('General')
                    ->setSetId($set->getId())
                    ->save();
                
                // Add defaults group attributes
                // TODO: get arr atrributes id from config
                $arrAttributes = array(1,2,3,4,5,6,7,8,9,10,11,12,13);
                foreach ($arrAttributes as $attributeId) {
                    $attribute = Mage::getModel('catalog', 'product_attribute')->setAttributeId($attributeId);
                    $group->addAttribute($attribute);
                }
                
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
    
    public function attributeDeleteAction() {
        $res = array('error' => 0);
        $attributes = $this->getRequest()->getPost('data', array());
        $attributes = Zend_Json::decode($attributes);
        $attribute = Mage::getModel('catalog', 'product_attribute');
            

        try {
            foreach ($attributes as $attributeId) {
                $attribute->setAttributeId($attributeId);
                $attribute->delete();
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
    
    public function attributeCreateAction() {
        $res = array('error' => 0);
        $data = $this->getRequest()->getPost('attribute', array());
        
        $attribute = Mage::getModel('catalog', 'product_attribute')
            ->setData(Zend_Json::decode($data));
        
        $attribute->setAttributeId(null);
            
        try {
            $attribute->save();
            $res['attribute'] = $attribute->getData();
            $res['attributeId'] = $attribute->getId();
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
     * Save product attributes
     *
     */
    public function attributeSaveAction()
    {
        $res = array('error' => 0);
        
        $rowsJson = $this->getRequest()->getPost('attributes', false);
        if (!empty($rowsJson)) {
            $rowsData = Zend_Json::decode($rowsJson);
        }
        if (empty($rowsData)) {
            $res = array(
                'error' => 1,
                'errorMessage' => 'Invalid input data',
            );
        } else {
            try {
                foreach ($rowsData as $row) {
                    Mage::getModel('catalog', 'product_attribute')->addData($row)->save();
                }
            } catch (Exception $e){
                $res = array(
                    'error' => 1,
                    'errorMessage' => $e->getMessage()
                );
            }
        }
        $this->getResponse()->setBody(Zend_Json::encode($res));
    }
    
    public function attributePropListAction() {
        $res = array();
        $attribute = Mage::getModel('catalog', 'product_attribute');
        $type = $this->getRequest()->getParam('type');
        
        switch ($type) {
            case 'data_type':
                $data = $attribute->getAllowType();
                break;
            case 'data_source':
                $data = $attribute->getAllowSource();
                break;
            case 'data_input':
                $data = $attribute->getAllowInput();
                break;
            case 'data_saver':
                $data = $attribute->getAllowSaver();
                break;
            default:
                break;
        }

        if (!empty($data)) {
            $res['totalRecords'] = count($data);
            foreach ($data as $item) {
                $res['items'][] = array(
                    'id'    => $item['value'],
                    'text'  => $item['label']
                );
            }
        }
        
        $this->getResponse()->setBody(Zend_Json::encode($res));
    }
}
