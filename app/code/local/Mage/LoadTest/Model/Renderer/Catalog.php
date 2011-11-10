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
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Mage
 * @package    Mage_LoadTest
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * LoadTest Renderer Catalog model
 *
 * @category   Mage
 * @package    Mage_LoadTest
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_LoadTest_Model_Renderer_Catalog extends Mage_LoadTest_Model_Renderer_Abstract
{
    /**
     * Store collection
     *
     * @var Mage_Core_Model_Resource_Store_Collection
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
     * @var Mage_Tax_Model_Resource_Class_Collection
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
        $this->setParentId(0);
        $this->setNested(1);
        $this->setPrefix(null);
        $this->setIncrement(0);
        $this->setCurrentCount(0);
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
            $this->_nestCategory($this->getParentId(), $this->getNested(), $this->getPrefix());
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
                if (!$this->_checkMemorySuffice()) {
                    $urlParams = array(
                        'count_products='.($this->getCountProducts() - $i + 1),
                        'min_count='.$this->getMinCount(),
                        'max_count='.$this->getMaxCount(),
                        'min_price='.$this->getMinPrice(),
                        'max_price='.$this->getMaxPrice(),
                        'min_weight='.$this->getMinWeight(),
                        'max_weight='.$this->getMaxWeight(),
                        'visibility='.$this->getVisibility(),
                        'qty='.$this->getQty(),
                        'start_product_name='.($this->getStartProductName() + $i - 1),
                        'attribute_set_id='.$this->getAttributeSetId(),
                        'fill_attribute='.$this->getFillAttribute(),
                        'detail_log='.$this->getDetailLog()
                    );
                    $this->_urls[] = Mage::getUrl('*/*/*/') . ' GET:"'.join(';', $urlParams).'"';
                    break;
                }
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
            $collection = Mage::getModel('Mage_Catalog_Model_Product')
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
            $collection = Mage::getModel('Mage_Catalog_Model_Category')
                ->setStoreId(0)
                ->getCollection()
                ->addAttributeToSelect('name')
                ->load();
            $deleted  = array();
            $toDelete = array();
            foreach ($collection as $category) {
                if ($category->getId() < 3) {
                    continue;
                }
                if (!isset($deleted[$category->getParentId()])) {
                    $deleted[$category->getId()] = true;
                    $toDelete[] = $category;
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
        $rand = rand($this->getMinCount(), $this->getMaxCount());
        for ($i = 0; $i < $rand; $i++) {
            if ($this->getCurrentCount()) {
                $rand = $this->getCurrentCount();
                $this->setCurrentCount(0);
            }
            if ($this->getIncrement()) {
                $i += $this->getIncrement();
                $rand += $this->getIncrement();
                $this->setIncrement(0);
            }
            if (!$this->_checkMemorySuffice()) {
                $urlParams = array(
                    'nesting='.($this->getNesting() - $nested + 1),
                    'min_count='.$this->getMinCount(),
                    'max_count='.$this->getMaxCount(),
                    'current_count='.($rand - $i),
                    'parent_id='.rawurlencode($parentId),
                    'increment='.$i,
                    'detail_log='.$this->getDetailLog(),
                    'prefix='.rawurlencode($prefix)
                );
                $this->_urls[] = Mage::getUrl('*/*/*/') . ' GET:"'.join(';', $urlParams).'"';
                break;
            }
            else {
                $thisPrefix = (!empty($prefix) ? $prefix.'.' : '') . ($i + 1);

                $categoryId = $this->_createCategory($parentId, $thisPrefix);

                if ($nested < $this->getNesting()) {
                    $this->_nestCategory($categoryId, $nested + 1, $thisPrefix);
                }
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

        $categoryName = Mage::helper('Mage_LoadTest_Helper_Data')->__('Catalog %s', $mask);
        $category = Mage::getModel('Mage_Catalog_Model_Category');

        if (!$parentId) {
            foreach ($this->_stores as $store) {
                $parentId = Mage::getModel('Mage_Catalog_Model_Category')->load($store->getRootCategoryId())->getPath();
                break;
            }
        }
        $category->setPath($parentId);
        $category->setName($categoryName);
        $category->setDisplayMode('PRODUCTS');
        $category->setAttributeSetId($category->getDefaultAttributeSetId());
        $category->setIsActive(1);
        $category->save();

        $categoryId = $category->getPath();
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
     * Create product attribute set
     *
     * @return int
     */
    protected function _createAttributeSet()
    {
        $entityTypeId = Mage::getModel('Mage_Eav_Model_Entity')->setType('catalog_product')
            ->getTypeId();
        $defaultSetId = Mage::getModel('Mage_Catalog_Model_Product')->getResource()->getEntityType()->getDefaultAttributeSetId();

        $setKey = '';
        $rand = array_merge(range('A', 'Z'), range('a', 'z'), range(0, 9));
        foreach (array_rand($rand, 4) as $v) {
            $setKey .= $rand[$v];
        }
        $setName = Mage::helper('Mage_LoadTest_Helper_Data')->__('Attribute Set %s', $setKey);

        $setModel = Mage::getModel('Mage_Eav_Model_Entity_Attribute_Set')
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
            Mage::helper('Mage_LoadTest_Helper_Data')->__('Group %s', $setKey),
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

    /**
     * Create product attributes
     *
     * @param string $type
     * @param string $key
     */
    protected function _createAttributes($type, $key)
    {
        $backendTypes   = array(
            'text'          => 'varchar',
            'gallery'       => 'varchar',
            'media_image'   => 'varchar',
            'multiselect'   => 'varchar',
            'image'         => 'text',
            'textarea'      => 'text',
            'date'          => 'datetime',
            'boolean'       => 'int',
            'select'        => 'int',
            'price'         => 'decimal',
        );
        $backendModel   = '';

        if ($type == 'select' || $type == 'multiselect') {
            $split = explode(',', $this->getData($type));
            if (isset($split[0]) && isset($split[1]) && isset($split[2])) {
                $count = $split[0];
                $minCount = $split[1];
                $maxCount = $split[2];
            }
            else {
                $count = 0;
            }
            if ($type == 'multiselect') {
                $backendModel = 'Mage_Eav_Model_Entity_Attribute_Backend_Array';
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

            $model = Mage::getModel('Mage_Eav_Model_Entity_Attribute')
                ->setEntityTypeId($this->_attributeSet->getEntityTypeId())
                ->setAttributeCode($attributeCode)
                ->setBackendModel($backendModel)
                ->setBackendType($backendTypes[$type])
                ->setBackendTable('')
                ->setFrontendInput($type)
                ->setFrontendLabel(array($attributeName))
                ->setFrontendClass('')
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
                ->setApplyTo('simple,grouped,configurable')
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
            $collection = Mage::getModel('Mage_Catalog_Model_Category')
                ->getCollection()
                ->load();
            $this->_categoryIds = array();

            foreach ($collection as $category) {
                $this->_categoryIds[$category->getId()] = $category->getId();
            }

            if (count($this->_categoryIds) == 0) {
                Mage::throwException(Mage::helper('Mage_LoadTest_Helper_Data')->__('Categories not found, please create category(ies) first'));
            }
        }

        if (is_null($this->_stores)) {
            $this->_stores = $this->getStores($this->getStoreIds());
        }
        if (is_null($this->_tax_classes)) {
            $this->_tax_classes = Mage::getModel('Mage_Tax_Model_Class')
                ->getCollection()
                ->setClassTypeFilter('PRODUCT');
        }

        $this->_profilerOperationStart();

        $productName = Mage::helper('Mage_LoadTest_Helper_Data')->__('Product #%s', $mask);
        $productDescription = Mage::helper('Mage_LoadTest_Helper_Data')->__('Description for Product #%s', $mask);
        $productShortDescription = Mage::helper('Mage_LoadTest_Helper_Data')->__('Short description for Product #%s', $mask);
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
        $websites = array();
        foreach ($this->_stores as $store) {
            $websites[$store->getWebsiteId()] = $store->getWebsiteId();
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

        $product = Mage::getModel('Mage_Catalog_Model_Product')
            ->setTypeId('simple')
            ->setStoreId(0)
            ->setName($productName)
            ->setDescription($productDescription)
            ->setShortDescription($productShortDescription)
            ->setSku($productSku)
            ->setWeight(rand($this->getMinWeight(), $this->getMaxWeight()))
            ->setStatus(1)
            ->setVisibility($this->getVisibility())
            ->setGiftMessageAvailable(1)
            ->setTierPrice(array())
            ->setPrice($productPrice)
            ->setSpecialPrice($productPrice)
            ->setSpecialFromDate(now(true))
            ->setSpecialToDate(now(true))
            ->setStockData($stockData)
            ->setTaxClassId($taxClass)
            ->setWebsiteIds($websites)
            ->setCategoryIds($categories)
            ;
        if (Mage::app()->isSingleStoreMode()) {
            $product->setWebsiteIds(array(Mage::app()->getStore(true)->getWebsiteId()));
        }
        else {
            $websiteIds = array();
            foreach (Mage::app()->getWebsites() as $website) {
                $websiteIds[] = $website->getId();
            }
            $product->setWebsiteIds($websiteIds);
        }

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
            $entityType = Mage::getSingleton('Mage_Eav_Model_Entity_Type')
                ->loadByCode('catalog_product');
            $entityTypeId = $entityType->getId();

            $attributeSet = Mage::getSingleton('Mage_Eav_Model_Entity_Attribute_Set')
                ->load($this->getAttributeSetId());
            /* @var $attributeSet Mage_Eav_Model_Entity_Attribute_Set */
            if (!$attributeSet->getId() || $attributeSet->getEntityTypeId() != $entityTypeId) {
                $this->setAttributeSetId($product->getResource()->getEntityType()->getDefaultAttributeSetId());
                $attributeSet = Mage::getSingleton('Mage_Eav_Model_Entity_Attribute_Set')
                    ->load($this->getAttributeSetId());
            }
            $attributeSetId = $attributeSet->getId();

            $collection = Mage::getModel('Mage_Eav_Model_Entity_Attribute')
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
                'tier_price',

                'image',
                'small_image',
                'thumbnail',
                'gallery',

                'custom_design',
                'custom_design_from',
                'custom_design_to',
                'custom_layout_update',

                'cost',
                'category_ids'
            );

            foreach ($collection as $attribute) {
                $attributeCode = $attribute->getAttributeCode();
                if (in_array($attributeCode, $skipAttribute)) {
                    continue;
                }

                $attributeType = $attribute->getFrontendInput();
                if ($attributeType == 'multiselect' || $attributeType == 'select') {
                    $optionCollection = Mage::getModel('Mage_Eav_Model_Entity_Attribute_Option')
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

                for ($i = 0; $i < $length; $i++) {
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
                        $length--;
                    }
                }
                $attributeValue = trim($attributeValue);
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

        if ($this->getDebug()) {
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
}
