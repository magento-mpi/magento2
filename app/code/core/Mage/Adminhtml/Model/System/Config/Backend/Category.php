<?php
/**
 * Config category field backend
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Adminhtml_Model_System_Config_Backend_Category
{
    public function afterSave(Varien_Object $configData)
    {
        if ($configData->getScope() == 'stores') {
            $rootId     = $configData->getValue();
            $oldRootId  = $configData->getOldValue();
            $storeId    = $configData->getScopeId();
            
            $category   = Mage::getSingleton('catalog/category');
            $tree       = $category->getTreeModel()->getTree();
            
            // Create copy of categories attributes for choosed store
            $tree->load($rootId, 100);
            foreach ($tree->getNodes() as $node) {
            	$category->setStoreId(0)
            	   ->load($node->getId());
                $category->setStoreId($storeId)
                    ->save();
            }
        }
        return $configData;
    }
}
