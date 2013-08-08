<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Oauth
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * OAuth token resource model
 *
 * @category    Mage
 * @package     Mage_Oauth
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Oauth_Model_Resource_Token extends Magento_Core_Model_Resource_Db_Abstract
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('oauth_token', 'entity_id');
    }

    /**
     * Clean up old authorized tokens for specified consumer-user pairs
     *
     * @param Mage_Oauth_Model_Token $exceptToken Token just created to exclude from delete
     * @return int The number of affected rows
     */
    public function cleanOldAuthorizedTokensExcept(Mage_Oauth_Model_Token $exceptToken)
    {
        if (!$exceptToken->getId() || !$exceptToken->getAuthorized()) {
            Mage::throwException('Invalid token to except');
        }
        $adapter = $this->_getWriteAdapter();
        $where   = $adapter->quoteInto(
            'authorized = 1 AND consumer_id = ?', $exceptToken->getConsumerId(), Zend_Db::INT_TYPE
        );
        $where .= $adapter->quoteInto(' AND entity_id <> ?', $exceptToken->getId(), Zend_Db::INT_TYPE);

        if ($exceptToken->getCustomerId()) {
            $where .= $adapter->quoteInto(' AND customer_id = ?', $exceptToken->getCustomerId(), Zend_Db::INT_TYPE);
        } elseif ($exceptToken->getAdminId()) {
            $where .= $adapter->quoteInto(' AND admin_id = ?', $exceptToken->getAdminId(), Zend_Db::INT_TYPE);
        } else {
            Mage::throwException('Invalid token to except');
        }
        return $adapter->delete($this->getMainTable(), $where);
    }

    /**
     * Delete old entries
     *
     * @param int $minutes
     * @return int
     */
    public function deleteOldEntries($minutes)
    {
        if ($minutes > 0) {
            $adapter = $this->_getWriteAdapter();

            return $adapter->delete(
                $this->getMainTable(),
                $adapter->quoteInto(
                    'type = "' . Mage_Oauth_Model_Token::TYPE_REQUEST . '" AND created_at <= ?',
                    Magento_Date::formatDate(time() - $minutes * 60)
                )
            );
        } else {
            return 0;
        }
    }
}
