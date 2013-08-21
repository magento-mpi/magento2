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
class Magento_Catalog_Model_Config_Backend_Category extends Magento_Core_Model_Config_Data
{
    protected function _afterSave()
    {
        if ($this->getScope() == 'stores') {
            $rootId     = $this->getValue();
            $storeId    = $this->getScopeId();

            $category   = Mage::getSingleton('Magento_Catalog_Model_Category');
            $tree       = $category->getTreeModel();

            // Create copy of categories attributes for choosed store
            $tree->load();
            $root = $tree->getNodeById($rootId);

            // Save root
            $category->setStoreId(0)
               ->load($root->getId());
            $category->setStoreId($storeId)
                ->save();

            foreach ($root->getAllChildNodes() as $node) {
                $category->setStoreId(0)
                   ->load($node->getId());
                $category->setStoreId($storeId)
                    ->save();
            }
        }
        return $this;
    }
}
