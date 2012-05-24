<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Api2
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * API2 for category (admin role)
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Model_Api2_Category_Rest_Admin_V1 extends Mage_Catalog_Model_Api2_Category_Rest
{
    /**
     * Attributes that could use default value from config
     *
     * @var array
     */
    protected $_attributesWithUseConfigOption = array('default_sort_by', 'filter_price_range');

    /**
     * Create category
     *
     * @param array $data
     * @return string
     */
    protected function _create(array $data)
    {
        /** @var $category Mage_Catalog_Model_Category */
        $category = Mage::getModel('Mage_Catalog_Model_Category');
        $this->_setCategoryData($category, $data);
        $this->_setParentId($category, $data);
        $this->_saveCategory($category);
        $this->_multicall($category->getId());
        return $this->_getLocation($category);
    }

    /**
     * Update category by its ID
     *
     * @param array $data
     */
    protected function _update(array $data)
    {
        $category = $this->_getCategory();
        $this->_setCategoryData($category, $data);
        $this->_saveCategory($category);
        $this->_updateParentId($category, $data);
    }

    /**
     * Delete category by its ID
     */
    protected function _delete()
    {
        $category = $this->_getCategory();
        /* @var $validator Mage_Catalog_Model_Api2_Category_Validator_Category */
        $validator = Mage::getModel('Mage_Catalog_Model_Api2_Category_Validator_Category', array(
            'operation' => $this->getOperation()));
        if ($validator->isValidForDelete($category)) {
            try {
                $category->delete();
            } catch (Mage_Core_Exception $e) {
                $this->_critical($e->getMessage(), Mage_Api2_Model_Server::HTTP_INTERNAL_ERROR);
            } catch (Exception $e) {
                $this->_critical(self::RESOURCE_UNKNOWN_ERROR);
            }
        } else {
            $this->_processValidationErrors($validator);
        }
    }

    /**
     * Save category. Perform validation before save
     *
     * @param Mage_Catalog_Model_Category $category
     */
    protected function _saveCategory($category)
    {
        /* @var $validator Mage_Catalog_Model_Api2_Category_Validator_Category */
        $validator = Mage::getModel('Mage_Catalog_Model_Api2_Category_Validator_Category', array(
            'operation' => $this->getOperation()));
        if ($validator->isValidForSave($category)) {
            try {
                $category->save();
                $this->_saveImages($category);
            } catch (Mage_Api2_Exception $e) {
                $this->_critical($e->getMessage(), $e->getCode());
            } catch (Mage_Core_Exception $e) {
                $this->_critical($e->getMessage(), Mage_Api2_Model_Server::HTTP_INTERNAL_ERROR);
            } catch (Exception $e) {
                $this->_critical(self::RESOURCE_UNKNOWN_ERROR);
            }
        } else {
            $this->_processValidationErrors($validator);
        }
    }

    /**
     * Initialize parent category using request data
     *
     * @param Mage_Catalog_Model_Category $category
     * @param array $data
     */
    protected function _setParentId(Mage_Catalog_Model_Category $category, array $data)
    {
        $parentId = false;
        if (isset($data['parent_id'])) {
            $parentId = $data['parent_id'];
        } else {
            $this->_critical("'parent_id' attribute must be set in request.", Mage_Api2_Model_Server::HTTP_BAD_REQUEST);
        }
        $parentCategory = $this->_initCategory($parentId);
        if (!$parentCategory) {
            $this->_critical('Requested category does not exist.', Mage_Api2_Model_Server::HTTP_BAD_REQUEST);
        }
        // parent category is defined by path
        $category->setPath($parentCategory->getPath());
    }

    /**
     * Move category in the category tree
     *
     * @param Mage_Catalog_Model_Category $category
     * @param array $data
     */
    protected function _updateParentId(Mage_Catalog_Model_Category $category, array $data)
    {
        if (isset($data['parent_id']) && $data['parent_id'] != $category->getParentId()) {
            $parentCategory = $this->_initCategory($data['parent_id']);
            if (!$parentCategory) {
                $this->_critical('Requested category does not exist.', Mage_Api2_Model_Server::HTTP_BAD_REQUEST);
            }
            $parentChildren = explode(',', $parentCategory->getChildren());
            $insertAfterId = end($parentChildren) ? end($parentChildren) : null;
            try {
                $category->move($parentCategory->getId(), $insertAfterId);
            } catch (Mage_Core_Exception $e) {
                $this->_critical($e->getMessage(), Mage_Api2_Model_Server::HTTP_INTERNAL_ERROR);
            }
        }
    }

    /**
     * Save images specified in 'image' attributes of category and attach them to it
     *
     * @param Mage_Catalog_Model_Category $category
     */
    protected function _saveImages(Mage_Catalog_Model_Category $category)
    {
        $imageAttributes = array('image', 'thumbnail');
        foreach ($imageAttributes as $imageAttributeCode) {
            if ($imageData = $category->getData($imageAttributeCode)) {
                if (is_array($imageData)) {
                    if (isset($imageData['delete']) && $imageData['delete']) {
                        $category->setData($imageAttributeCode, null);
                    } else {
                        /** @var $imageUploader Mage_Api2_Model_Request_Uploader_Image */
                        $imageUploader = Mage::getModel('Mage_Api2_Model_Request_Uploader_Image');
                        $uploadDirectoryPath = Mage::getBaseDir('media') . DS . 'catalog' . DS . 'category' . DS;
                        $imageUploader->setUploadDirectory($uploadDirectoryPath);
                        $imageUploader->upload($imageData);
                        $category->setData($imageAttributeCode, $imageUploader->getUploadedFileName());
                        /** @var $categoryResource Mage_Catalog_Model_Resource_Category */
                        $categoryResource = Mage::getResourceModel('Mage_Catalog_Model_Resource_Category');
                        $categoryResource->saveAttribute($category, $imageAttributeCode);
                    }
                }
            }
        }
    }

    /**
     * Set data to category object. "use_config_..." fields are also processed
     *
     * @param Mage_Catalog_Model_Category $category
     * @param array $data
     */
    protected function _setCategoryData(Mage_Catalog_Model_Category $category, array $data)
    {
        $category->setStoreId($this->_getStore()->getId());
        $this->_setAttributes($category, $data);
        $category->setAttributeSetId($category->getDefaultAttributeSetId());
        // Create Permanent Redirect for old URL key
        if ($category->getId() && isset($data['url_key']) && isset($data['url_key_create_redirect'])) {
            $category->setData('save_rewrites_history', (bool)$data['url_key_create_redirect']);
        }
    }

    /**
     * Set category attributes with "use_config_..." fields processing
     *
     * @param Mage_Catalog_Model_Category $category
     * @param array $data
     */
    protected function _setAttributes(Mage_Catalog_Model_Category $category, array $data)
    {
        // filter input data and save only valid category attributes
        foreach ($category->getAttributes() as $attribute) {
            $attributeCode = $attribute->getAttributeCode();
            if ($this->_isConfigValueUsed($data, $attributeCode)) {
                $category->setData($attributeCode, null);
            } else if (isset($data[$attributeCode])) {
                $category->setData($attributeCode, $data[$attributeCode]);
            }
        }
        $this->_processUseConfigOptions($data, $category);
    }

    /**
     * Unset data from fields that have respective 'use_config_...' option set
     *
     * @param array $data
     * @param Mage_Catalog_Model_Category $category
     */
    protected function _processUseConfigOptions($data, $category)
    {
        foreach ($this->_attributesWithUseConfigOption as $attributeCode) {
            $useConfigOption = "use_config_$attributeCode";
            if (isset($data[$useConfigOption]) && $data[$useConfigOption]) {
                $category->setData($useConfigOption, true);
            }
        }
    }

    /**
     * Check if config value should be used for specified attribute
     *
     * @param array $data
     * @param string $attributeCode
     * @return bool
     */
    protected function _isConfigValueUsed($data, $attributeCode)
    {
        $isConfigValueUsed = false;
        $useConfigAttributeCode = "use_config_$attributeCode";
        if (in_array($attributeCode, $this->_attributesWithUseConfigOption)) {
            $isConfigValueUsed = isset($data[$useConfigAttributeCode]) && $data[$useConfigAttributeCode];
        }
        return $isConfigValueUsed;
    }

    /**
     * Get categories as tree
     *
     * @param Mage_Catalog_Model_Resource_Eav_Mysql4_Category_Tree $tree
     * @param Mage_Catalog_Model_Resource_Category_Collection $categoriesCollection
     * @return array
     */
    protected function _getTreeCategories($tree, $categoriesCollection)
    {
        $categoryIds = array();
        /** @var $categoryNode Varien_Data_Tree_Node */
        foreach ($tree->getNodes() as $categoryNode) {
            $categoryIds[] = $categoryNode->getId();
        }
        $categoriesCollection->addIdFilter($categoryIds);
        /** @var $category Mage_Catalog_Model_Category */
        foreach ($categoriesCollection as $category) {
            $this->_initAdditionalCategoryFields($category);
        }
        $tree->addCollectionData($categoriesCollection, true);
        $treeCategories = array();
        /** @var $categoryNode Varien_Data_Tree_Node */
        foreach ($tree->getNodes() as $categoryNode) {
            if (!$categoryNode->getParent()) {
                $treeCategories[$categoryNode->getId()] = $this->_treeNodeToArray($categoryNode);
            }
        }

        return $treeCategories;
    }
}
