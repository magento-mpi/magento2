<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Config category field backend
 *
 * @category   Magento
 * @package    Magento_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Catalog_Model_Config_Backend_Category extends Magento_Core_Model_Config_Value
{
    /**
     * Catalog category
     *
     * @var Magento_Catalog_Model_Category
     */
    protected $_catalogCategory;

    /**
     * Construct
     *
     * @param Magento_Catalog_Model_Category $catalogCategory
     * @param Magento_Core_Model_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_Core_Model_StoreManager $storeManager
     * @param Magento_Core_Model_Config $config
     * @param Magento_Core_Model_Resource_Abstract $resource
     * @param Magento_Data_Collection_Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        Magento_Catalog_Model_Category $catalogCategory,
        Magento_Core_Model_Context $context,
        Magento_Core_Model_Registry $registry,
        Magento_Core_Model_StoreManager $storeManager,
        Magento_Core_Model_Config $config,
        Magento_Core_Model_Resource_Abstract $resource = null,
        Magento_Data_Collection_Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_catalogCategory = $catalogCategory;
        parent::__construct($context, $registry, $storeManager, $config, $resource, $resourceCollection, $data);
    }

    protected function _afterSave()
    {
        if ($this->getScope() == 'stores') {
            $rootId     = $this->getValue();
            $storeId    = $this->getScopeId();

            $tree       = $this->_catalogCategory->getTreeModel();

            // Create copy of categories attributes for choosed store
            $tree->load();
            $root = $tree->getNodeById($rootId);

            // Save root
            $this->_catalogCategory->setStoreId(0)
               ->load($root->getId());
            $this->_catalogCategory->setStoreId($storeId)
                ->save();

            foreach ($root->getAllChildNodes() as $node) {
                $this->_catalogCategory->setStoreId(0)
                   ->load($node->getId());
                $this->_catalogCategory->setStoreId($storeId)
                    ->save();
            }
        }
        return $this;
    }
}
