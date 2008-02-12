<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_LoadTest
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * LoadTest Renderer Catalog model
 *
 * @category   Mage
 * @package    Mage_LoadTest
 * @author     Victor Tihonchuk <victor@varien.com>
 */

class Mage_LoadTest_Model_Renderer_Catalog extends Mage_LoadTest_Model_Renderer_Abstract
{
    /**
     * Store collection
     *
     * @var Mage_Core_Model_Mysql4_Store_Collection
     */
    protected $_stores;

    /**
     * Category Ids array
     *
     * @var array
     */
    protected $_categoryIds;

    /**
     * Tax class collection
     *
     * @var Mage_Tax_Model_Mysql4_Class_Collection
     */
    protected $_tax_classes;

    /**
     * Processed (created/deleted) categories
     *
     * @var array
     */
    protected $_category;

    /**
     * Processed (created/deleted) products
     *
     * @var array
     */
    protected $_product;

    protected $_attribute;

    /**
     * Product attributes array
     *
     * @var array
     */
    protected $_productAttributes;

    protected $_attributeSet;

    protected $_attributeData;

    /**
     * Init model
     *
     */
    public function __construct()
    {
        parent::__construct();

        $this->setType('CATEGORY');
        $this->setSroreIds(null);
        $this->setNesting(2);
        $this->setMinCount(2);
        $this->setMaxCount(5);
        $this->setCountProducts(100);
        $this->setMinPrice(10);
        $this->setMaxPrice(300);
        $this->setMinWeight(10);
        $this->setMaxWeight(999);
        $this->setVisibility(4);
        $this->setQty(5);
        $this->setAttributeSetId(5);
        $this->setStartProductName(0);
    }

    /**
     * Render Products/Categories
     *
     * @return Mage_LoadTest_Model_Renderer_Catalog
     */
    public function render()
    {
        if ($this->getType() == 'CATEGORY') {
            $this->_profilerBegin();
            $this->_nestCategory(0, 1, null);
            $this->_updateCategories();
            $this->_profilerEnd();
        }
        elseif ($this->getType() == 'ATTRIBUTE_SET') {
            $this->_profilerBegin();
            $this->_createAttributeSet();
            $this->_profilerEnd();
        }
        elseif ($this->getType() == 'SIMPLE_PRODUCT')
        {
            $this->_profilerBegin();
            for ($i = 1; $i <= $this->getCountProducts(); $i ++) {
                $this->_createProduct($i + $this->getStartProductName());
            }
            $this->_profilerEnd();
        }
        return $this;
    }

    /**
     * Delete all Products/Categories
     *
     * @return Mage_LoadTest_Model_Renderer_Catalog
     */
    public function delete()
    {
        if ($this->getType() == 'PRODUCT')
        {
            $this->_profilerBegin();
            $collection = Mage::getModel('catalog/product')
                ->getCollection()
                ->addAttributeToSelect('name')
                ->load();
            foreach ($collection as $product) {
                $this->_profilerOperationStart();
                $this->_product = array(
                    'id'    => $product->getId(),
                    'name'  => $product->getName()
                );
                $product->delete();
                $this->_profilerOperationStop();
            }
            $this->_profilerEnd();
        }
        elseif ($this->getType() == 'CATEGORY') {
            $this->_profilerBegin();
            $collection = Mage::getModel('catalog/category')
                ->setStoreId(0)
                ->getCollection()
                ->addAttributeToSelect('name')
                ->load();
            $deleted  = array();
            $toDelete = array();
            foreach ($collection as $category) {
                if ($category->getId() == 2) {
                    continue;
                }
                if (!isset($deleted[$category->getParentId()])) {
                    $deleted[$category->getId()] = true;
                    $toDelete[] = $category->getId();
                }
                else {
                    $deleted[$category->getId()] = true;
                }
                $parentId = $category->getParentId();
                $parentId = $parentId == 2 ? 0 : $parentId;
            }
            foreach ($toDelete as $category) {
                $this->_profilerOperationStart();
                $this->_category = array(
                    'id'        => $category->getId(),
                    'parent_id' => $category->getParentId() == 2 ? 0 : $category->getParentId(),
                    'name'      => $category->getName()
                );
                $category
                    ->delete();
                $this->_profilerOperationStop();
            }
            unset($toDelete);
            $this->_profilerEnd();
        }

        return $this;
    }

    /**
     * Recursive call create categories
     *
     * @param int $parentId
     * @param int $nested
     * @param string $prefix
     *
     * @return Mage_LoadTest_Model_Renderer_Catalog
     */
    protected function _nestCategory($parentId, $nested = 1, $prefix = null)
    {
        for ($i = 0; $i < rand($this->getMinCount(), $this->getMaxCount()); $i++) {
            $thisPrefix = (!empty($prefix) ? $prefix.'.' : '') . ($i + 1);

            $categoryId = $this->_createCategory($parentId, $thisPrefix);

            if ($nested < $this->getNesting()) {
                $this->_nestCategory($categoryId, $nested + 1, $thisPrefix);
            }
        }

        return $this;
    }

    /**
     * Create category
     *
     * @param int $parentId
     * @param string $mask
     * @return int
     */
    protected function _createCategory($parentId, $mask)
    {
        if (is_null($this->_stores)) {
            $this->_stores = $this->getStores($this->getStoreIds());
        }

        $this->_profilerOperationStart();

        $categoryName = Mage::helper('loadtest')->__('Catalog %s', $mask);
        $category = Mage::getModel('catalog/category');
        foreach ($this->_stores as $store) {
            if (!$parentId) {
                $catalogParentId = $store->getRootCategoryId();
            }
            else {
                $catalogParentId = $parentId;
            }
            $category->setStoreId($store->getId());
            $category->setParentId($catalogParentId);
            $category->setName($categoryName);
            $category->setDisplayMode('PRODUCTS');
            $category->setAttributeSetId($category->getDefaultAttributeSetId());
            $category->setIsActive(1);
            $category->setNotUpdateDepends(true);
            $category->save();
        }

        /**
         * Save for All Stores
         */
        $category->setStore(0);
        $category->save();

        $categoryId = $category->getId();
        unset($category);
        $this->_category = array(
            'parent_id' => $parentId,
            'id'        => $categoryId,
            'name'      => $categoryName
        );

        $this->_profilerOperationStop();

        return $categoryId;
    }

    /**
     * Update categories tree and urls
     *
     * @return Mage_LoadTest_Model_Renderer_Catalog
     */
    protected function _updateCategories()
    {
        if (is_null($this->_stores)) {
            $this->_stores = $this->getStores($this->getStoreIds());
        }

        $this->_profilerOperationStart();

        $category = Mage::getModel('catalog/category');
        /* @var $category Mage_Catalog_Model_Category */
        $tree = $category->getTreeModel()
            ->load();
        $nodes = array();
        /* @var $tree Mage_Catalog_Model_Entity_Category_Tree */
        foreach ($tree->getNodes() as $nodeId => $node) {
            $nodes[$nodeId] = array(
                'path'          => array(),
                'children'      => array(),
                'children_all'  => array()
            );
            foreach ($node->getPath() as $path) {
                $nodes[$nodeId]['path'][] = $path->getId();
            }
            foreach ($node->getChildren() as $child) {
                $nodes[$nodeId]['children'][] = $child->getId();
            }

            foreach ($node->getAllChildNodes() as $child) {
                $nodes[$nodeId]['children_all'][] = $child->getId();
            }
        }

        $collection = $category->getCollection()
            ->load();
        foreach ($collection as $item) {
            $item->setData('path_in_store', join(',', $nodes[$item->getId()]['path']));
            $item->getResource()->saveAttribute($item, 'path_in_store');

            $item->setData('children', join(',', $nodes[$item->getId()]['children']));
            $item->getResource()->saveAttribute($item, 'children');

            $item->setData('all_children', join(',', $nodes[$item->getId()]['children_all']));
            $item->getResource()->saveAttribute($item, 'all_children');

            foreach ($this->_stores as $store) {
                $catalogParentId = $store->getRootCategoryId();
                $deep = true;
                $pathIds = array();
                foreach ($nodes[$item->getId()]['path'] as $path) {
                    if (!$deep) {
                        continue;
                    }
                    if ($path == $catalogParentId) {
                        $deep = false;
                        continue;
                    }
                    $pathIds[] = $path;
                }
                $item->setStore($store->getId());
                $item->setData('path_in_store', join(',', $pathIds));
                $item->getResource()->saveAttribute($item, 'path_in_store');
            }
        }

        unset($collection);
        unset($nodes);

        Mage::getSingleton('catalog/url')->refreshRewrites();

        $this->_profilerUpdateCateriesStop();

        return $this;
    }

    protected function _createAttributeSet()
    {
        $entityTypeId = Mage::getModel('eav/entity')->setType('catalog_product')
            ->getTypeId();
        $defaultSetId = Mage::getModel('catalog/product')->getResource()->getConfig()->getDefaultAttributeSetId();

        $setKey = '';
        $rand = array_merge(range('A', 'Z'), range('a', 'z'), range(0, 9));
        foreach (array_rand($rand, 4) as $v) {
            $setKey .= $rand[$v];
        }
        $setName = Mage::helper('loadtest')->__('Attribute Set %s', $setKey);

        $setModel = Mage::getModel('eav/entity_attribute_set')
            ->setAttributeSetName($setName)
            ->setEntityTypeId($entityTypeId)
            ->save();
        $setId = $setModel->getId();
        $this->_attributeSet = $setModel;

        $setModel->initFromSkeleton($defaultSetId)
            ->save();

        $this->_attributeData = array(
            'groups'            => array(),
            'attributes'        => array(),
            'not_attributes'    => array(),
            'removeGroups'      => array(),
            'attribute_set_name'=> $setName,
        );

        foreach ($setModel->getGroups() as $group) {
            $this->_attributeData['groups'][] = array(
                $group->getId(),
                $group->getAttributeGroupName(),
                $group->getSortOrder()
            );
            foreach ($group->getAttributes() as $attribute) {
                $this->_attributeData['attributes'][] = array(
                    $attribute->getId(),
                    $attribute->getAttributeGroupId(),
                    $attribute->getSortOrder(),
                );
            }
        }
        $this->_attributeData['groups'][] = array(
            'ynode-245',
            Mage::helper('loadtest')->__('Group %s', $setKey),
            count($this->_attributeData['groups']) + 1
        );

        if ($this->getText()) {
            $this->_createAttributes('text', $setKey);
        }
        if ($this->getTextarea()) {
            $this->_createAttributes('textarea', $setKey);
        }
        if ($this->getDate()) {
            $this->_createAttributes('date', $setKey);
        }
        if ($this->getBoolean()) {
            $this->_createAttributes('boolean', $setKey);
        }
        if ($this->getMultiselect() && $this->getMultiselect() != '0,0,0') {
            $this->_createAttributes('multiselect', $setKey);
        }
        if ($this->getSelect() && $this->getSelect() != '0,0,0') {
            $this->_createAttributes('select', $setKey);
        }
        if ($this->getPrice()) {
            $this->_createAttributes('price', $setKey);
        }
        if ($this->getImage()) {
            $this->_createAttributes('image', $setKey);
        }

        $this->_profilerAddChild('attribute_set_id', $setId);

        $setModel->organizeData($this->_attributeData);
        $setModel->save();

        unset($setModel);
        unset($this->_attributeSet);

        return $setId;
    }

    protected function _createAttributes($type, $key)
    {
        $backendTypes   = array(
            'text'          => 'text',
            'textarea'      => 'text',
            'date'          => 'datetime',
            'boolean'       => 'int',
            'multiselect'   => 'int',
            'select'        => 'int',
            'price'         => 'decimal',
            'image'         => 'varchar',
        );
        $backendModel   = '';

        if ($type == 'select' || $type == 'multiselect') {
            $split = split(',', $this->getData($type));
            if (isset($split[0]) && isset($split[1]) && isset($split[2])) {
                $count = $split[0];
                $minCount = $split[1];
                $maxCount = $split[2];
            }
            else {
                $count = 0;
            }
            if ($type == 'multiselect') {
                $backendModel = 'eav/entity_attribute_backend_array';
            }
        }
        else {
            $count = intval($this->getData($type));
        }

        $rand = array_merge(range('A', 'Z'), range('a', 'z'), range(0, 9));

        for ($i = 0; $i < $count; $i ++) {
            $this->_profilerOperationStart();

            $code = '';
            foreach (array_rand($rand, 8) as $v) {
                $code .= $rand[$v];
            }

            $attributeCode = $key . '_' . $type . '_' . $code;
            $attributeName = ucfirst($type) . ' ' . $code;

            $model = Mage::getModel('eav/entity_attribute')
                ->setEntityTypeId($this->_attributeSet->getEntityTypeId())
                ->setAttributeCode($attributeCode)
                ->setAttributeModel('')
                ->setBackendModel($backendModel)
                ->setBackendType($backendTypes[$type])
                ->setBackendTable('')
                ->setFrontendModel('')
                ->setFrontendInput($type)
                ->setFrontendLabel(array($attributeName))
                ->setFrontendClass('')
                ->setSourceModel('')
                ->setIsGlobal(1)
                ->setIsVisible(1)
                ->setIsRequired(0)
                ->setIsUserDefined(1)
                ->setDefaultValue('')
                ->setIsSearchable(1)
                ->setIsFilterable(0)
                ->setIsComparable(0)
                ->setIsVisibleOnFront(0)
                ->setIsUnique(0)
                ->setApplyTo(0)
                ->setUseInSuperProduct(1)
                ->setIsVisibleInAdvancedSearch(0);

            if ($type == 'select' || $type == 'multiselect') {
                $options = array();
                for ($j = 0; $j < rand($minCount, $maxCount); $j ++) {
                    $options['option_' . ($j + 1)] = array('Select ' . ($j + 1));
                }
                $model->setOption(array('value' => $options));
            }

            $model->save();

            $this->_attributeData['attributes'][] = array(
                $model->getId(),
                'ynode-245',
                $this->_operationCount + 1
            );

            $this->_attribute = array(
                'id'    => $model->getId(),
                'code'  => $attributeCode,
                'type'  => $type,
                'name'  => $attributeName
            );

            unset($model);

            $this->_profilerOperationStop();
        }
    }

    /**
     * Create product
     *
     * @param int $mask
     * @return int
     */
    protected function _createProduct($mask)
    {
        if (is_null($this->_categoryIds)) {
            $collection = Mage::getModel('catalog/category')
                ->getCollection()
                ->load();
            $this->_categoryIds = array();

            foreach ($collection as $category) {
                $this->_categoryIds[$category->getId()] = $category->getId();
            }

            if (count($this->_categoryIds) == 0) {
                Mage::throwException(Mage::helper('loadtest')->__('Categories not found, please create category(ies) first'));
            }
        }

        if (is_null($this->_stores)) {
            $this->_stores = $this->getStores($this->getStoreIds());
        }
        if (is_null($this->_tax_classes)) {
            $this->_tax_classes = Mage::getModel('tax/class')
                ->getCollection()
                ->setClassTypeFilter('PRODUCT');
        }

        $this->_profilerOperationStart();

        $productName = Mage::helper('loadtest')->__('Product #%s', $mask);
        $productDescription = Mage::helper('loadtest')->__('Description for Product #%s', $mask);
        $productShortDescription = Mage::helper('loadtest')->__('Short description for Product #%s', $mask);
        $productSku = $this->_getSku($mask);
        $productPrice = rand($this->getMinPrice(), $this->getMaxPrice());
        $stockData = array(
            'qty'               => $this->getQty(),
            'min_qty'           => 0,
            'min_sale_qty'      => 0,
            'max_sale_qty'      => $this->getQty(),
            'is_qty_decimal'    => 0,
            'backorders'        => 0,
            'is_in_stock'       => 1
        );
        $stores = array();
        foreach ($this->_stores as $store) {
            $stores[$store->getId()] = 0;
        }
        $categories = array_rand($this->_categoryIds, rand($this->getMinCount(), $this->getMaxCount()));
        $taxClass = 0;

        foreach ($this->_tax_classes as $class) {
            if (!$taxClass) {
                $taxClass = $class->getId();
            }
            else {
                if (rand(1,0) == 1) {
                    $taxClass = $class->getId();
                }
            }
        }

        $product = Mage::getModel('catalog/product')
            ->setTypeId(1)
            ->setStoreId(0)
            ->setName($productName)
            ->setDescription($productDescription)
            ->setShortDescription($productShortDescription)
            ->setSku($productSku)
            ->setWeight(rand($this->getMinWeight(), $this->getMaxWeight()))
            ->setStatus(1)
            ->setVisibility($this->getVisibility())
            ->setGiftMessageAvailable(1)
            ->setPrice($productPrice)
            ->setSpecialPrice($productPrice)
            ->setSpecialFromDate(now(true))
            ->setSpecialToDate(now(true))
            ->setStockData($stockData)
            ->setTaxClassId($taxClass)
            ->setPostedStores($stores)
            ->setPostedCategories($categories);

        $this->_fillAttribute($product);

        try {
            $product->save();
        }
        catch (Exception $e) {
            Mage::throwException($e->getMessage() . "\n\n" . print_r($product->getData(), true));
        }

        $productId = $product->getId();

        if ($product->getStoresChangedFlag()) {
             Mage::dispatchEvent('catalog_controller_product_save_visibility_changed', array('product'=>$product));
        }

        $this->_product = array(
            'id'    => $productId,
            'name'  => $product->getName()
        );

        unset($product);

        $this->_profilerOperationStop();

        return $productId;
    }

    protected function _fillAttribute(Mage_Catalog_Model_Product $product)
    {
        if (is_null($this->_productAttributes)) {
            $attributeSet = Mage::getModel('eav/entity_attribute_set')
                ->load($this->getAttributeSetId());
            if (!$attributeSet) {
                $this->setAttributeSetId($product->getResource()->getConfig()->getDefaultAttributeSetId());
                $attributeSet = Mage::getModel('eav/entity_attribute_set')
                    ->load($this->getAttributeSetId());
            }
            $attributeSetId = $attributeSet->getId();

            $collection = Mage::getModel('eav/entity_attribute')
                ->getCollection()
                ->setAttributeSetFilter($attributeSetId)
                ->load();

            $skipAttribute = array(
                'old_id',
                'name',
                'description',
                'short_description',
                'sku',
                'weight',
                'status',
                'tax_class_id',
                'url_key',
                'url_path',
                'visibility',
                'gift_message_available',

                'price',
                'special_price',
                'special_from_date',
                'special_to_date',

                'image',
                'small_image',
                'thumbnail',
                'gallery',

                'custom_design',
                'custom_design_from',
                'custom_design_to',
                'custom_layout_update',

                'cost',
            );

            foreach ($collection as $attribute) {
                $attributeCode = $attribute->getAttributeCode();
                if (in_array($attributeCode, $skipAttribute)) {
                    continue;
                }

                $attributeType = $attribute->getFrontendInput();
                if ($attributeType == 'multiselect' || $attributeType == 'select') {
                    $optionCollection = Mage::getModel('eav/entity_attribute_option')
                        ->getCollection()
                        ->setAttributeFilter($attribute->getId())
                        ->setPositionOrder('desc')
                        ->load();
                    $options = array();
                    foreach ($optionCollection as $option) {
                        $options[$option->getId()] = $option->getId();
                    }

                    $attribute->setOptionValues($options);
                }
                $this->_productAttributes[$attribute->getId()] = $attribute;
            }
        }

        $product->setAttributeSetId($this->getAttributeSetId());

        foreach ($this->_productAttributes as $attribute) {
            if ($this->getFillAttribute() == 0 && !$attribute->getFillAttribute()) {
                continue;
            }

            $attributeCode = $attribute->getAttributeCode();
            $attributeType = $attribute->getFrontendInput();

            if ($attributeType == 'date') {
                $attributeValue = now(true);
            }
            elseif ($attributeType == 'boolean') {
                $attributeValue = rand(0,1);
            }
            elseif ($attributeType == 'price') {
                $attributeValue = rand(0, 1000);
            }
            elseif ($attributeType == 'select') {
                $attributeValue = array_rand($attribute->getOptionValues());
            }
            elseif ($attributeType == 'multiselect') {
                $attributeValue = array_rand($attribute->getOptionValues(), rand(1, count($attribute->getOptionValues())));
            }
            elseif ($attributeCode == 'image') {
                $attributeValue = '/default_image.jpg';
            }
            else {
                $attributeValue = '';
                $length = rand(1, 255);
                $rnd    = array_merge(range('A', 'Z'), range('a', 'z'), range(0, 9), array(' '));
                $len    = count($rnd) - 1;
                $space  = 0;

                for ($i = 0; $i < $length; $i ++) {
                    $letter = $rnd[rand(0, $len)];
                    $attributeValue .= $rnd[rand(0, $len)];
                    if ($letter == ' ') {
                        $space = 0;
                    }
                    else {
                        $space ++;
                    }

                    if ($space > 12) {
                        $attributeValue .= ' ';
                        $space = 0;
                    }
                }
            }
            if (is_null($attributeValue)) {
                $attributeValue = '';
            }

            $product->setData($attributeCode, $attributeValue);
        }
    }

    /**
     * Get Unique generated SKU
     *
     * @param int $number
     * @return string
     */
    protected function _getSku($number)
    {
        $length = 8;
        $str    = '';
        $rnd    = array_merge(range('A', 'Z'), range('a', 'z'), range(0, 9));
        $len    = count($rnd) - 1;

        for ($i = 0; $i < $length; $i ++) {
            $str .= $rnd[rand(0, $len)];
        }

        return $str . '-' . $number;
    }

    protected function _profilerOperationStop()
    {
        parent::_profilerOperationStop();

        if ($this->debug) {
            if ($this->getType() == 'CATEGORY') {
                if (!$this->_xmlFieldSet) {
                    $this->_xmlFieldSet = $this->_xmlResponse->addChild('categories');
                }

                $category = $this->_xmlFieldSet->addChild('category');
                $category->addAttribute('id', $this->_category['id']);
                $category->addAttribute('parent_id', $this->_category['parent_id']);
                $category->addChild('name', $this->_category['name']);
                $this->_profilerOperationAddDebugInfo($category);
            }
            elseif ($this->getType() == 'ATTRIBUTE_SET') {
                if (!$this->_xmlFieldSet) {
                    $this->_xmlFieldSet = $this->_xmlResponse->addChild('attributes');
                }

                $attribute = $this->_xmlFieldSet->addChild('attribute');
                $attribute->addAttribute('id', $this->_attribute['id']);
                $attribute->addChild('code', $this->_attribute['code']);
                $attribute->addChild('type', $this->_attribute['type']);
                $attribute->addChild('name', $this->_attribute['name']);
                $this->_profilerOperationAddDebugInfo($attribute);
            }
            elseif ($this->getType() == 'SIMPLE_PRODUCT' || $this->getType() == 'PRODUCT') {
                if (!$this->_xmlFieldSet) {
                    $this->_xmlFieldSet = $this->_xmlResponse->addChild('products');
                }

                $product = $this->_xmlFieldSet->addChild('product');
                $product->addAttribute('id', $this->_product['id']);
                $product->addChild('name', $this->_product['name']);
                $this->_profilerOperationAddDebugInfo($product);
            }
        }
    }

    protected function _profilerUpdateCateriesStop()
    {
        parent::_profilerOperationStop();
        $this->_operationCount --;
        if ($this->debug) {
            $update = $this->_xmlResponse->addChild('update_categories');
            $this->_profilerOperationAddDebugInfo($update);
        }
    }
}