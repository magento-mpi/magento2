<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_PageCache
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Enterprise_PageCache_Model_Resource_Crawler extends Magento_Core_Model_Resource_Db_Abstract
{
    /**
     * Internal constructor
     */
    protected function _construct()
    {
        $this->_init('core_url_rewrite', 'url_rewrite_id');
    }

    /**
     * Retrieve URLs paths that must be visited by crawler
     *
     * @param  $storeId
     * @return array
     */
    public function getUrlsPaths($storeId)
    {
        $adapter = $this->_getReadAdapter();
        $select = $adapter->select()
            ->from($this->getTable('core_url_rewrite'), array('request_path'))
            ->where('store_id=?', $storeId)
            ->where('is_system=1');
        return $adapter->fetchCol($select);
    }
}
