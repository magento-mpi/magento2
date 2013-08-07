<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Invitation
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Invitation collection
 *
 * @category    Magento
 * @package     Magento_Invitation
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Invitation_Model_Resource_Invitation_Collection extends Magento_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Fields mapping 
     *
     * @var array
     */
    protected $_map    = array('fields' => array(
        'invitee_email'    => 'c.email',
        'website_id'       => 'w.website_id',
        'invitation_email' => 'main_table.email',
        'invitee_group_id' => 'main_table.group_id'
    ));

    /**
     * Intialize collection
     *
     */
    protected function _construct()
    {
        $this->_init('Magento_Invitation_Model_Invitation', 'Magento_Invitation_Model_Resource_Invitation');
    }

    /**
     * Instantiate select object
     *
     * @return Magento_Invitation_Model_Resource_Invitation_Collection
     */
    protected function _initSelect()
    {
        $this->getSelect()->from(
            array('main_table' => $this->getResource()->getMainTable()),
            array('*', 'invitation_email' => 'email', 'invitee_group_id' => 'group_id')
        );
        return $this;
    }

    /**
     * Load collection where customer id equals passed parameter
     *
     * @param int $id
     * @return Magento_Invitation_Model_Resource_Invitation_Collection
     */
    public function loadByCustomerId($id)
    {
        $this->getSelect()->where('main_table.customer_id = ?', $id);
        return $this->load();
    }

    /**
     * Filter by specified store ids
     *
     * @param array|int $storeIds
     * @return Magento_Invitation_Model_Resource_Invitation_Collection
     */
    public function addStoreFilter($storeIds)
    {
        $this->getSelect()->where('main_table.store_id IN (?)', $storeIds);
        return $this;
    }

    /**
     * Join website ID
     *
     * @return Magento_Invitation_Model_Resource_Invitation_Collection
     */
    public function addWebsiteInformation()
    {
        $this->getSelect()
            ->joinInner(
                array('w' => $this->getTable('core_store')),
                'main_table.store_id = w.store_id',
                'w.website_id'
            );
        return $this;
    }

    /**
     * Join referrals information (email)
     *
     * @return Magento_Invitation_Model_Resource_Invitation_Collection
     */
    public function addInviteeInformation()
    {
        $this->getSelect()->joinLeft(
            array('c' => $this->getTable('customer_entity')),
            'main_table.referral_id = c.entity_id', array('invitee_email' => 'c.email')
        );
        return $this;
    }

    /**
     * Filter collection by items that can be sent
     *
     * @return Magento_Invitation_Model_Resource_Invitation_Collection
     */
    public function addCanBeSentFilter()
    {
        return $this->addFieldToFilter('status', Magento_Invitation_Model_Invitation::STATUS_NEW);
    }

    /**
     * Filter collection by items that can be cancelled
     *
     * @return Magento_Invitation_Model_Resource_Invitation_Collection
     */
    public function addCanBeCanceledFilter()
    {
        return $this->addFieldToFilter('status', array('nin' => array(
            Magento_Invitation_Model_Invitation::STATUS_CANCELED,
            Magento_Invitation_Model_Invitation::STATUS_ACCEPTED
        )));
    }
}
