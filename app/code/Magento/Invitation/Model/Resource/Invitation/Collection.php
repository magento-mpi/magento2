<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Invitation
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Invitation\Model\Resource\Invitation;

/**
 * Invitation collection
 *
 * @category    Magento
 * @package     Magento_Invitation
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Collection extends \Magento\Core\Model\Resource\Db\Collection\AbstractCollection
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
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magento\Invitation\Model\Invitation', 'Magento\Invitation\Model\Resource\Invitation');
    }

    /**
     * Instantiate select object
     *
     * @return $this
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
     * @return $this
     */
    public function loadByCustomerId($id)
    {
        $this->getSelect()->where('main_table.customer_id = ?', $id);
        return $this->load();
    }

    /**
     * Filter by specified store ids
     *
     * @param int[]|int $storeIds
     * @return $this
     */
    public function addStoreFilter($storeIds)
    {
        $this->getSelect()->where('main_table.store_id IN (?)', $storeIds);
        return $this;
    }

    /**
     * Join website ID
     *
     * @return $this
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
     * @return $this
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
     * @return $this
     */
    public function addCanBeSentFilter()
    {
        return $this->addFieldToFilter('status', \Magento\Invitation\Model\Invitation::STATUS_NEW);
    }

    /**
     * Filter collection by items that can be cancelled
     *
     * @return $this
     */
    public function addCanBeCanceledFilter()
    {
        return $this->addFieldToFilter('status', array('nin' => array(
            \Magento\Invitation\Model\Invitation::STATUS_CANCELED,
            \Magento\Invitation\Model\Invitation::STATUS_ACCEPTED
        )));
    }
}
