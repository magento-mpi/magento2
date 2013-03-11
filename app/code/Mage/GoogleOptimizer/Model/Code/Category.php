<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_GoogleOptimizer
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Google Optimizer Category model
 *
 * @category    Mage
 * @package     Mage_GoogleOptimizer
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_GoogleOptimizer_Model_Code_Category extends Mage_GoogleOptimizer_Model_Code
{

    protected $_entityType = 'category';

    /**
     * Removing scripts assigned to entity
     *
     * @param integer $storeId
     * @return Mage_GoogleOptimizer_Model_Code
     */
    public function deleteScripts($storeId)
    {
        $category = $this->getEntity();
        if ($category) {
            /**
             * We need check category children ids
             */
            $ids = $category->getDeletedChildrenIds();
            if (is_array($ids)) {
                $ids[] = $category->getId();
            } else {
                $ids = array($category->getId());
            }
            $this->setEntityIds($ids);
        }
        return parent::deleteScripts($storeId);
    }

}
