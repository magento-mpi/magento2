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
 * OAuth token resource collection model
 *
 * @category    Mage
 * @package     Mage_Oauth
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Oauth_Model_Resource_Token_Collection extends Magento_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Initialize collection model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Mage_Oauth_Model_Token', 'Mage_Oauth_Model_Resource_Token');
    }

    /**
     * Load collection with consumer data
     *
     * Method use for show applications list (token-consumer)
     *
     * @return Mage_Oauth_Model_Resource_Token_Collection
     */
    public function joinConsumerAsApplication()
    {
        $select = $this->getSelect();
        $select->joinLeft(
                    array('c' => $this->getTable('oauth_consumer')),
                    'c.entity_id = main_table.consumer_id',
                    'name'
                );

        return $this;
    }

    /**
     * Add filter by admin ID
     *
     * @param int $adminId
     * @return Mage_Oauth_Model_Resource_Token_Collection
     */
    public function addFilterByAdminId($adminId)
    {
        $this->addFilter('main_table.admin_id', $adminId);
        return $this;
    }

    /**
     * Add filter by customer ID
     *
     * @param int $customerId
     * @return Mage_Oauth_Model_Resource_Token_Collection
     */
    public function addFilterByCustomerId($customerId)
    {
        $this->addFilter('main_table.customer_id', $customerId);
        return $this;
    }

    /**
     * Add filter by consumer ID
     *
     * @param int $consumerId
     * @return Mage_Oauth_Model_Resource_Token_Collection
     */
    public function addFilterByConsumerId($consumerId)
    {
        $this->addFilter('main_table.consumer_id', $consumerId);
        return $this;
    }

    /**
     * Add filter by type
     *
     * @param string $type
     * @return Mage_Oauth_Model_Resource_Token_Collection
     */
    public function addFilterByType($type)
    {
        $this->addFilter('main_table.type', $type);
        return $this;
    }

    /**
     * Add filter by ID
     *
     * @param array|int $id
     * @return Mage_Oauth_Model_Resource_Token_Collection
     */
    public function addFilterById($id)
    {
        $this->addFilter('main_table.entity_id', array('in' => $id), 'public');
        return $this;
    }

    /**
     * Add filter by "Is Revoked" status
     *
     * @param bool|int $flag
     * @return Mage_Oauth_Model_Resource_Token_Collection
     */
    public function addFilterByRevoked($flag)
    {
        $this->addFilter('main_table.revoked', (int) $flag, 'public');
        return $this;
    }
}
