<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Oauth
 * @copyright  {copyright}
 * @license    {license_link}
 */
namespace Magento\Integration\Model\Resource\Oauth;

/**
 * OAuth token resource model
 */
class Token extends \Magento\Model\Resource\Db\AbstractDb
{
    /**
     * @var \Magento\Stdlib\DateTime
     */
    protected $_dateTime;

    /**
     * @param \Magento\App\Resource $resource
     * @param \Magento\Stdlib\DateTime $dateTime
     */
    public function __construct(\Magento\App\Resource $resource, \Magento\Stdlib\DateTime $dateTime)
    {
        $this->_dateTime = $dateTime;
        parent::__construct($resource);
    }

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
     * @param \Magento\Integration\Model\Oauth\Token $exceptToken Token just created to exclude from delete
     * @throws \Magento\Core\Exception
     * @return int The number of affected rows
     */
    public function cleanOldAuthorizedTokensExcept(\Magento\Integration\Model\Oauth\Token $exceptToken)
    {
        if (!$exceptToken->getId() || !$exceptToken->getAuthorized()) {
            throw new \Magento\Core\Exception('Invalid token to except');
        }
        $adapter = $this->_getWriteAdapter();
        $where   = $adapter->quoteInto(
            'authorized = 1 AND consumer_id = ?', $exceptToken->getConsumerId(), \Zend_Db::INT_TYPE
        );
        $where .= $adapter->quoteInto(' AND entity_id <> ?', $exceptToken->getId(), \Zend_Db::INT_TYPE);

        if ($exceptToken->getCustomerId()) {
            $where .= $adapter->quoteInto(' AND customer_id = ?', $exceptToken->getCustomerId(), \Zend_Db::INT_TYPE);
        } elseif ($exceptToken->getAdminId()) {
            $where .= $adapter->quoteInto(' AND admin_id = ?', $exceptToken->getAdminId(), \Zend_Db::INT_TYPE);
        } else {
            throw new \Magento\Core\Exception('Invalid token to except');
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
                    'type = "' . \Magento\Integration\Model\Oauth\Token::TYPE_REQUEST . '" AND created_at <= ?',
                    $this->_dateTime->formatDate(time() - $minutes * 60)
                )
            );
        } else {
            return 0;
        }
    }

    /**
     * Select a single token of the specified type for the specified consumer.
     *
     * @param int $consumerId - The consumer id
     * @param string $type - The token type (e.g. 'verifier')
     * @return array|boolean - Row data (array) or false if there is no corresponding row
     */
    public function selectTokenByType($consumerId, $type)
    {
        $adapter = $this->_getReadAdapter();
        $select = $adapter->select()
            ->from($this->getMainTable())
            ->where('consumer_id = ?', $consumerId)->where('type = ?', $type);
        return $adapter->fetchRow($select);
    }
}
