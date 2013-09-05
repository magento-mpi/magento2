<?php
/**
 * Cms block grid collection
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Cms_Model_Resource_Block_Grid_Collection extends Magento_Cms_Model_Resource_Block_Collection
{

    /**
     * @return Magento_Cms_Model_Resource_Block_Grid_Collection
     */
    protected function _afterLoad()
    {
        $this->walk('afterLoad');
        parent::_afterLoad();
    }

    /**
     * @param string $field
     * @param null $condition
     * @return Magento_Cms_Model_Resource_Block_Grid_Collection
     */
    public function addFieldToFilter($field, $condition = null)
    {
        if ($field == 'store_id'){
            return $this->addStoreFilter($field);
        }
    }
}
