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
 * API2 for category (customer role)
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Model_Api2_Category_Rest_Customer_V1 extends Mage_Catalog_Model_Api2_Category_Rest
{
    /**
     * Create tree root category based on request params, check if customer can access it
     *
     * @return Mage_Catalog_Model_Category
     */
    protected function _initTreeRootCategory()
    {
        $treeRootCategory = parent::_initTreeRootCategory();
        if (!$treeRootCategory->getIsActive() || !$treeRootCategory->getParentId()) {
            $this->_critical(self::RESOURCE_NOT_FOUND);
        }

        return $treeRootCategory;
    }
}
