<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_CatalogRule
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Enter description here ...
 *
 * @category    Mage
 * @package     Mage_CatalogRule
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_CatalogRule_Model_Resource_Rule_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Enter description here ...
     *
     */
    protected function _construct()
    {
        $this->_init('Mage_CatalogRule_Model_Rule', 'Mage_CatalogRule_Model_Resource_Rule');
    }

    /**
     * Enter description here ...
     *
     */
    protected function _afterLoad()
    {
        $this->walk('afterLoad');
    }

    /**
     * Filter collection by specified website IDs
     *
     * @param int|array $websiteIds
     * @return Mage_CatalogRule_Model_Resource_Rule_Collection
     */
    public function addWebsiteFilter($websiteIds)
    {
        if (!is_array($websiteIds)) {
            $websiteIds = array($websiteIds);
        }
        $parts = array();
        foreach ($websiteIds as $websiteId) {
          $parts[] = $this->getConnection()->prepareSqlCondition('main_table.website_ids', array('finset' => $websiteId));
        }
        if ($parts) {
            $this->getSelect()->where(new Zend_Db_Expr(implode(' OR ', $parts)));
        }
        return $this;
    }

    /**
     * Find product attribute in conditions or actions
     *
     * @param string $attributeCode
     * @return Mage_CatalogRule_Model_Resource_Rule_Collection
     */
    public function addAttributeInConditionFilter($attributeCode)
    {
        $match = sprintf('%%%s%%', substr(serialize(array('attribute' => $attributeCode)), 5, -1));
        $this->addFieldToFilter('conditions_serialized', array('like' => $match));

        return $this;
    }
}
