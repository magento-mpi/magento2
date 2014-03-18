<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_FullPageCache
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\FullPageCache\Model\Resource;

class Crawler extends \Magento\Model\Resource\Db\AbstractDb
{
    /**
     * Internal constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('core_url_rewrite', 'url_rewrite_id');
    }

    /**
     * Retrieve URLs paths that must be visited by crawler
     *
     * @param  int $storeId
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
