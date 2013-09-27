<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Oauth
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * oAuth nonce resource model
 *
 * @category    Magento
 * @package     Magento_Oauth
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Oauth\Model\Resource;

class Nonce extends \Magento\Core\Model\Resource\Db\AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('oauth_nonce', null);
    }

    /**
     * Delete old entries
     *
     * @param int $minutes Delete entries older than
     * @return int
     */
    public function deleteOldEntries($minutes)
    {
        if ($minutes > 0) {
            $adapter = $this->_getWriteAdapter();

            return $adapter->delete(
                $this->getMainTable(), $adapter->quoteInto('timestamp <= ?', time() - $minutes * 60, \Zend_Db::INT_TYPE)
            );
        } else {
            return 0;
        }
    }

    /**
     * Select a unique nonce row using a composite primary key (i.e. $nonce and $consumerId)
     *
     * @param string $nonce - The nonce string
     * @param int $consumerId - The consumer id
     * @return array
     */
    public function selectByCompositeKey($nonce, $consumerId)
    {
        $adapter = $this->_getReadAdapter();
        $select = $adapter->select()
            ->from($this->getMainTable())
            ->where('nonce = ?', $nonce)->where('consumer_id = ?', $consumerId);
        return $adapter->fetchRow($select);
    }
}
