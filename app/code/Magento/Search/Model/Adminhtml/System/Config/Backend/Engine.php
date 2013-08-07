<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Search
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Catalog search backend model
 *
 * @category    Magento
 * @package     Magento_Search
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Search_Model_Adminhtml_System_Config_Backend_Engine extends Magento_Core_Model_Config_Data
{
    /**
     * After save call
     * Invalidate catalog search index if engine was changed
     *
     * @return Magento_Search_Model_Adminhtml_System_Config_Backend_Engine
     */
    protected function _afterSave()
    {
        parent::_afterSave();

        if ($this->isValueChanged()) {
            Mage::getSingleton('Magento_Index_Model_Indexer')->getProcessByCode('catalogsearch_fulltext')
                ->changeStatus(Magento_Index_Model_Process::STATUS_REQUIRE_REINDEX);
        }

        return $this;
    }
}
